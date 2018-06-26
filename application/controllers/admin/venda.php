<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Produto_Parceiro_Plano_Model $current_model
 *
 */
class Venda extends Admin_Controller
{
    const PRECO_TIPO_TABELA = 1;
    const PRECO_TIPO_COBERTURA = 2;
    const PRECO_TIPO_VALOR_SEGURADO = 3;
    const PRECO_POR_EQUIPAMENTO = 5;

    const TIPO_CALCULO_NET = 1;
    const TIPO_CALCULO_BRUTO = 2;

    protected $layout = "base";

    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Venda');
        $this->template->set_breadcrumb('Venda', base_url("$this->controller_uri/index"));

        //Carrega modelos necessários
        $this->load->model('produto_parceiro_model', 'current_model');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cliente_model', 'cliente');

        //Seta layout
        $layout = $this->session->userdata("layout");
        $this->layout = isset($layout) && !empty($layout) ? $layout : 'base';

        $this->template->js(app_assets_url("template/js/libs/cycle2/cycle2.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/cycle2/jquery.cycle2.carousel.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/toastr/toastr.js", "admin"));
        $this->template->css(app_assets_url("template/css/{$this->_theme}/libs/toastr/toastr.css", "admin"));
        $this->template->css(app_assets_url("template/css/{$this->_theme}/libs/wizard/wizard.css", "admin"));
    }

    public function envia_comunicacao_url_pagamento()
    {
        //Carrega biblioteca e dispara evento para salvar cotação
        $this->load->library("Comunicacao");
        $this->load->library("Response");
        $this->load->library("Short_url");

        $response = new Response();

        $contato = $this->input->post("contato");
        $nome = $this->input->post("nome");
        $url_acesso_externo = $this->input->post("url_acesso_externo");
        $produto_parceiro_id = $this->input->post("produto_parceiro_id");
        $tipo = $this->input->post("tipo");

        $short_url = new Short_url();
        $url_acesso_externo = $short_url::shorter($url_acesso_externo);

        if($url_acesso_externo === FALSE){
            $response->setMensagem("Não foi possivel fazer o short URL");
        }else {


            if ($contato && $url_acesso_externo && $produto_parceiro_id && $tipo) {
                $comunicacao = new Comunicacao();
                $comunicacao->setDestinatario($contato);
                $comunicacao->setNomeDestinatario($nome);
                $comunicacao->setMensagemParametros(array(
                    'url' => $url_acesso_externo,
                    'nome' => $nome,
                ));

                if ($comunicacao->disparaEvento("url_efetuar_pagamento_{$tipo}", $produto_parceiro_id)) {
                    $response->setStatus(true);
                } else {
                    $response->setMensagem("Não foi possível efetuar o disparo. Verifique se esta comunicação está ativada no sistema.");
                }

            } else if (!$contato) {
                if ($tipo == "email")
                    $response->setMensagem("Insira o e-mail que deverá ser enviado.");
                else
                    $response->setMensagem("Insira o telefone que deverá ser enviado.");
            }
        }



        echo $response->getJSON();
    }


    /**
     * Inicia uma venda
     * @param $produto_parceiro_id
     */
    public function iniciar_venda($produto_parceiro_id)
    {
        $produto = $this->current_model->with_produto()->get($produto_parceiro_id);

        if(!$produto){
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Produto não Encontrato');

            //Redireciona para index
            redirect("$this->controller_uri/index");
        }


        $this->limpa_cotacao($produto_parceiro_id);
        if($produto['produto_slug'] == 'seguro_viagem'){
            redirect("$this->controller_uri/{$produto['produto_slug']}/{$produto_parceiro_id}");
        }elseif($produto['produto_slug'] == 'equipamento'){
            redirect("admin/venda_equipamento/{$produto['produto_slug']}/{$produto_parceiro_id}");
        }elseif($produto['produto_slug'] == 'generico'){
            redirect("admin/venda_generico/{$produto['produto_slug']}/{$produto_parceiro_id}");
        }else{
            redirect("$this->controller_uri/{$produto['produto_slug']}/{$produto_parceiro_id}");
        }
        //if($produto['produto_slug'] == 'seguro_viagem')

    }



    /**
     * Index
     */
    public function index()
    {
        //Carrega models necessários
        $this->load->model('pedido_model', 'pedido');

        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Venda');
        $this->template->set_breadcrumb('Venda', base_url("$this->controller_uri/index"));


        $parceiro_id = $this->session->userdata('parceiro_id');

        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index");

        //Carrega dados
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();


        $produtos = $this->current_model->get_produtos_venda_admin($parceiro_id);
        $relacionamento = $this->current_model->get_produtos_venda_admin_parceiros($parceiro_id);


        $data['rows'] = array_merge($produtos, $relacionamento);

        //busca pedidos para o carrinho
        $data['carrinho'] = $this->pedido->getPedidoCarrinho($this->session->userdata('usuario_id'));


        //Carrega dados do template
        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/list", $data );
    }


    public function continuar($cotacao_id){

        $this->load->model('cotacao_model', 'cotacao');

        //Carrega dados para a página
        $row = $this->cotacao->get_cotacao_produto($cotacao_id);
        //$seguro_viagem = $this->seguro_viagem->getCotacaoLead($cotacao_id);



        //Verifica se registro existe
        if(!$row) {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }
    //    print_r($seguro_viagem);exit;
        switch ($row['produto_slug']) {
            case 'seguro_viagem':
                redirect("admin/venda/seguro_viagem/{$row['produto_parceiro_id']}/{$row['step']}/$cotacao_id");
                break;
            case 'equipamento':
                redirect("admin/venda_equipamento/equipamento/{$row['produto_parceiro_id']}/{$row['step']}/$cotacao_id");
                break;
            case 'generico':
                redirect("admin/venda_generico/generico/{$row['produto_parceiro_id']}/{$row['step']}/$cotacao_id");
                break;
        }


    }

    /**
     * Seguro viagem
     * @param $produto_parceiro_id
     * @param int $step
     * @param int $cotacao_id
     * @param int $pedido_id
     */
    public function seguro_viagem($produto_parceiro_id, $step = 1, $cotacao_id = 0, $pedido_id = 0, $status = '')
    {
        //Carrega models
        $this->load->model("cotacao_seguro_viagem_model", "seguro_viagem");
        $this->load->model("pedido_model");

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Venda');
        $this->template->set_breadcrumb('Venda', base_url("$this->controller_uri/index"));

        $this->load->library('form_validation');

        if($step == 1) {
            $this->seguro_viagem_formulario($produto_parceiro_id, $cotacao_id);
        }
        elseif ($step == 2)
        {
            $this->seguro_viagem_carrossel($produto_parceiro_id, $cotacao_id);

        }
        elseif ($step == 3)
        {

            //Verifica se possui desconto (vai para passo específico)
            if($this->seguro_viagem->verifica_possui_desconto($cotacao_id) && $status != "desconto_aprovado")
            {
                //Verifica se desconto foi aprovado
                if($this->seguro_viagem->verifica_desconto_aprovado($cotacao_id))
                {
                    //Carrega função para visualizar desconto
                    $this->seguro_viagem_verificar_desconto($produto_parceiro_id, $cotacao_id);
                }
                else
                {
                    //Avisa o usuário que desconto ainda não foi aprovado, portanto não consegue finalizar
                    $this->session->set_flashdata('fail_msg', 'O desconto ainda não foi aprovado.');
                    redirect("{$this->controller_uri}/index");
                }
            }
            else
            {
                //Carrega função para finalizar
                $this->seguro_viagem_finalizar($produto_parceiro_id, $cotacao_id, $status);
            }

        } elseif ($step == 4) {

            /**
             * Verifica se pedido já foi feito (se sim encaminha para página de pagamento)
             */
            $pedido = $this->pedido_model
                ->with_foreign()
                ->get_by(array(
                    'pedido.cotacao_id' => $cotacao_id
                ));

            $status = array('pagamento_negado', 'cancelado', 'cancelado_stornado', 'aprovacao_cancelamento', 'cancelamento_aprovado');

            if($pedido && !in_array($pedido['pedido_status_slug'], $status) && $this->layout == 'front')
            {
                //$this->venda_aguardando_pagamento($produto_parceiro_id, $cotacao_id);
                redirect("{$this->controller_uri}/venda/{$produto_parceiro_id}/5/{$pedido['pedido_id']}");
            }
            else
            {
                $this->venda_pagamento($produto_parceiro_id, $cotacao_id, $pedido_id);
            }

        } elseif ($step == 5) {
            $this->venda_aguardando_pagamento($produto_parceiro_id, $cotacao_id);
        } elseif ($step == 6) {
            $this->seguro_viagem_certificado($produto_parceiro_id, $cotacao_id);
        } elseif ($step == 7) {
            $this->seguro_viagem_add_carrinho($produto_parceiro_id, $cotacao_id, $pedido_id);
        } elseif ($step == 8) {
            $this->seguro_viagem_salvar_cotacao($produto_parceiro_id, $cotacao_id, $pedido_id);
        }


    }

    /**
     * Página que verifica desconto
     * @param $produto_parceiro_id
     * @param $cotacao_id
     */
    public function seguro_viagem_verificar_desconto($produto_parceiro_id, $cotacao_id)
    {
        //Carrega models necessários
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_seguro_viagem_model', 'seguro_viagem');

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->cotacao->get($cotacao_id);
        $data['cotacao_id'] = $cotacao_id;
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['seguro_viagem'] = $this->seguro_viagem->getCotacaoAprovacao($cotacao_id);
        $data['primary_key'] = $this->cotacao->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/view/{$cotacao_id}");

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');

            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

        if($_POST)
        {
            //Se for setado para finalizar com desconto aprovado
            if($this->input->post("finalizar_desconto_aprovado"))
            {
                redirect("$this->controller_uri/seguro_viagem/$produto_parceiro_id/3/$cotacao_id/0/desconto_aprovado");
            }

        }


        //Carrega template
        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/seguro_viagem/verificar_desconto", $data );
    }

    /**
     * Formulário seguro viagem
     * @param $produto_parceiro_id
     * @param int $cotacao_id
     */
    public function seguro_viagem_formulario($produto_parceiro_id, $cotacao_id = 0)
    {
        //Carrega models necessários
        $this->load->model('produto_parceiro_campo_model', 'campo');
        $this->load->model('localidade_pais_model', 'pais');
        $this->load->model('localidade_estado_model', 'estado');
        $this->load->model('seguro_viagem_motivo_model', 'motivo');
        $this->load->model('cotacao_model', 'cotacao');

        //Adiciona bibliotecas necessárias
        $this->template->js(app_assets_url('modulos/venda/seguro_viagem/js/base.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_viagem/js/formulario.js', 'admin'));

        //Carrega dados
        $campos_session = $this->session->userdata("cotacao_{$produto_parceiro_id}");

        //Dados para template
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['produto_parceiro_id'] = $produto_parceiro_id;

        //Verifica cotação
        if($cotacao_id > 0)
        {
            if($this->cotacao->isCotacaoValida($cotacao_id) == FALSE)
            {
                $this->session->set_flashdata('fail_msg', 'Essa Cotação não é válida');
                redirect("{$this->controller_uri}/index");
            }
        }

        //Campos para formulário
        $data['campos'] = $this->campo->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug('cotacao')
            ->order_by("ordem", "asc")
            ->get_all();

        if(isset($campos_session) && is_array($campos_session)){
            $data['row'] = $campos_session;
        }else{
            $data['row'] = array();
        }

        $data['cotacao_id'] = $cotacao_id;
        $data['list'] = array();
        $data['list']['destino_id'] = $this->localidade->get_by_parceiro($produto_parceiro_id, 'destino');
        $data['list']['origem_id'] = $this->localidade->get_by_parceiro($produto_parceiro_id, 'origem');
        $data['list']['seguro_viagem_motivo_id'] = $this->motivo->get_all();


        if($_POST)
        {

            $validacao = array();
            foreach ($data['campos'] as $campo) {

                $validacao[] = array(
                    'field' => "{$campo['campo_nome_banco']}",
                    'label' => "{$campo['campo_nome']}",
                    'rules' => $campo['validacoes'],
                    'groups' => 'cotacao'
                );
            }

            $this->cotacao->setValidate($validacao);

            //Verifica válido form
            if ($this->cotacao->validate_form('cotacao'))
            {
                $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $_POST);
                $cotacao_id = (int)$this->input->post('cotacao_id');

                if($cotacao_id > 0)
                {
                    $this->update_cotacao_formulario($produto_parceiro_id, $cotacao_id);
                    redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2/{$cotacao_id}");
                }else{
                    //adiciona cotação
                    $cotacao_id = $this->insert_cotacao_formulario($produto_parceiro_id);
                    $this->update_cotacao_formulario($produto_parceiro_id, $cotacao_id);
                    redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2/{$cotacao_id}");
                }
            }
        }


        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/seguro_viagem/formulario", $data );

    }

    /**
     * Passo 7
     * @param $produto_parceiro_id
     * @param int $cotacao_id
     */
    public function seguro_viagem_salvar_cotacao($produto_parceiro_id, $cotacao_id = 0)
    {
        //Carrega models necessários
        $this->load->model('produto_parceiro_campo_model', 'campo');
        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('cobertura_plano_model', 'plano_cobertura');
        $this->load->model('cobertura_model', 'cobertura');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('produto_parceiro_desconto_model', 'desconto');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
        $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
        $this->load->model('cotacao_seguro_viagem_cobertura_model', 'cotacao_seguro_viagem_cobertura');
        $this->load->model('contato_tipo_model', 'contato_tipo');

        //Carrega JS
        $this->template->js(app_assets_url('modulos/venda/seguro_viagem/js/base.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_viagem/js/salvar_cotacao.js', 'admin'));
        $this->template->css(app_assets_url('modulos/venda/seguro_viagem/css/base.css', 'admin'));

        //Dados para template
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['row'] =  $this->current_model->get_by_id($produto_parceiro_id);



        $data['cotacao_id'] = $cotacao_id;

        $data['campos_salvar_cotacao'] = $this->campo->with_campo()
            ->with_campo_tipo()
            ->with_campo_classe()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug('salvar_cotacao')
            ->order_by("ordem", "asc")
            ->get_all();

        $data['data_salvar_cotacao']['contato_tipo'] = $this->contato_tipo->get_all();

        $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");

        $data['nome_segurado'] = $cotacao['nome'];

        /**
         * Busca Planos e coberturas
         */

        if($_POST){
            //print_r($_POST);exit;
            $validacao = array();
            foreach ($data['campos_salvar_cotacao'] as $campo) {


                $validacao[] = array(
                    'field' => "{$campo['campo_nome_banco']}",
                    'label' => "{$campo['campo_nome']}",
                    'rules' => $campo['validacoes'],
                    'groups' => 'salvar_cotacao'
                );
            }

            $validacao[] = array(
                'field' => "cliente_terceiro[0]",
                'label' => "Cliente / terceiro",
                'rules' => 'trim|required',
                'groups' => 'salvar_cotacao'
            );

            $contato_tipo = $this->input->post("cliente_terceiro");
            if(isset($contato_tipo[0]) && $contato_tipo[0] == 1) {

                $validacao[] = array(
                    'field' => "contato_nome[0]",
                    'label' => "Nome",
                    'rules' => 'trim|required',
                    'groups' => 'salvar_cotacao'
                );
            }
            $validacao[] = array(
                'field' => "contato_tipo_id[0]",
                'label' => "Tipo de Contato",
                'rules' => 'trim|required',
                'groups' => 'salvar_cotacao'
            );
            $validacao[] = array(
                'field' => "contato[0]",
                'label' => "Contato",
                'rules' => 'trim|required',
                'groups' => 'salvar_cotacao'
            );
            $validacao[] = array(
                'field' => "melhor_horario[0]",
                'label' => "Melhor Horario",
                'rules' => 'trim|required',
                'groups' => 'salvar_cotacao'
            );

            //validate_contato_salvar_cotacao

            $qnt_cotnato = $this->input->post('quantidade_contatos');


            for ($i = 0; $i < $qnt_cotnato; $i++) {
                if($this->input->post('contato_tipo_id')[$i]){
                    $validacao[] = array(
                        'field' => "contato[$i]",
                        'label' => "Tipo de Contato",
                        'rules' => "callback_validate_contato_salvar_cotacao[$i]",
                        'groups' => 'salvar_cotacao'
                    );
                }


            }

            //print_r($data['campos_salvar_cotacao']);exit;
            $this->cotacao->setValidate($validacao);

            //Verifica válido form
            if ($this->cotacao->validate_form('salvar_cotacao')){
                if($cotacao_id > 0){
                    $this->update_cotacao_carrossel($produto_parceiro_id, $cotacao_id);
                }else{
                    $cotacao_id = $this->salvar_cotacao($produto_parceiro_id);
                }

                $this->salvar_cotacao_campos_adicionais($cotacao_id);
                $cotacao = $this->cotacao->get($cotacao_id);
                $this->session->set_flashdata('succ_msg', 'Cotação salva com sucesso, código: '. $cotacao['codigo']); //Mensagem de sucesso
                $this->limpa_cotacao($produto_parceiro_id);
                redirect("$this->controller_uri/index");
            }

        }

        $data['cotacao'] = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $data['carrossel'] = $this->session->userdata("carrossel_{$produto_parceiro_id}");

        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/seguro_viagem/salvar_cotacao", $data );

    }


    public function seguro_viagem_finalizar($produto_parceiro_id, $cotacao_id = 0, $status = '')
    {
        //Carrega models necessários
        $this->load->model('produto_parceiro_campo_model', 'campo');
        $this->load->model('cotacao_seguro_viagem_model', 'seguro_viagem');
        $this->load->model('cotacao_seguro_viagem_pessoa_model', 'pessoa');
        $this->load->model('cotacao_model', 'cotacao');

        //Carrega JS para template
        $this->template->js(app_assets_url('modulos/venda/seguro_viagem/js/dados_busca_cep.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_viagem/js/dados_segurado.js', 'admin'));


        $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $carrossel = $this->session->userdata("carrossel_{$produto_parceiro_id}");

        $valido = isset($cotacao) && is_array($cotacao) && count($cotacao) > 0 && isset($carrossel) && is_array($carrossel) && count($carrossel) > 0;

        $cotacao_id = ((int)$this->input->post('cotacao_id') > 0) ? (int)$this->input->post('cotacao_id') : $cotacao_id;


        if($cotacao_id > 0)
        {
            if($this->cotacao->isCotacaoValida($cotacao_id) == FALSE){
                $this->session->set_flashdata('fail_msg', 'Essa Cotação não é válida');
                redirect("{$this->controller_uri}/index");
            }


            //Verifica se cotação e carrossel foram setados na session
            if ($status == 'desconto_aprovado'){
                //Seta session
                $this->set_cotacao_session($cotacao_id, $produto_parceiro_id, $status);
            }elseif($valido){
                //Move para cotação carrossel
                $this->update_cotacao_carrossel($produto_parceiro_id, $cotacao_id, $status);
            }else{
                redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2/{$cotacao_id}");
            }
        }else{
            if($valido) {
                $cotacao_id = $this->salvar_cotacao($produto_parceiro_id);
            }else{
                redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2/{$cotacao_id}");
            }
        }




        $cotacao_salva = $this->cotacao->with_cotacao_seguro_viagem()
            ->filterByID($cotacao_id)
            ->get_all();

        //cotacao_upgrade_id

        $desconto_ver = false;
        foreach ($cotacao_salva as $index => $item)
        {
            if($item['desconto_condicional'] > 0 && $status != "desconto_aprovado")
            {
                $data_cotacao = array();
                $data_cotacao['desconto_cond_enviar'] = 1;
                $this->seguro_viagem->update($item['cotacao_seguro_viagem_id'],  $data_cotacao, TRUE);
                $this->cotacao->update($item['cotacao_id'],array('cotacao_status_id' => 4), TRUE);
                $desconto_ver = true;
            }

        }

        if($desconto_ver == true && $cotacao_salva[0]['desconto_cond_aprovado'] == 0){
            $this->session->set_flashdata('succ_msg', 'Você optou pelo desconto condicional, esse desconto precisa de autorização, salvamos sua cotação com o código: '. $cotacao_salva[0]['codigo'] . ' Aguarde a aprovação'); //Mensagem de sucesso
            $this->limpa_cotacao($produto_parceiro_id);
            redirect("$this->controller_uri/index");
        }



        $data = array();

        $data['cotacao_id'] = $cotacao_id;
        $data['campos'] = $this->campo->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug('dados_segurado')
            ->order_by("ordem", "asc")
            ->get_all();

        $data['campos_passageiro'] = $this->campo->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug('dados_passageiro')
            ->order_by("ordem", "asc")
            ->get_all();

        $data['cotacao'] = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $data['carrossel'] = $this->session->userdata("carrossel_{$produto_parceiro_id}");


        if(isset($cotacao_salva[0]['cotacao_upgrade_id']) && (int)$cotacao_salva[0]['cotacao_upgrade_id'] > 0){
           $this->set_cotacao_session($cotacao_id, $produto_parceiro_id);
        }
        $dados_segurado =  $this->session->userdata("dados_segurado_{$produto_parceiro_id}");
        $data['row'] = (isset($dados_segurado) && is_array($dados_segurado) && count($dados_segurado) > 0) ? $dados_segurado : array();

        if($_POST)
        {
            $validacao = array();


            $planos = explode(';', $data['carrossel']['plano']);

            $planos_nome = explode(';', $data['carrossel']['plano_nome']);
            $plano_passageiro = explode(';', $carrossel['num_passageiro']);

            foreach ($planos as $index => $plano) {
                foreach ($data['campos'] as $campo){


                    $validacao[] = array(
                            'field' => "plano_{$plano}_{$campo['campo_nome_banco']}",
                            'label' => "{$planos_nome[$index]} - {$campo['campo_nome']}",
                            'rules' => $campo['validacoes'] ,
                            'groups' => 'dados_segurado'
                        );
                }
            }

            foreach ($planos as $index => $plano) {
                for ($passageiro = 2; $passageiro <= $plano_passageiro[$index]; $passageiro++ ) {

                    foreach ($data['campos_passageiro'] as $campo) {


                        $validacao[] = array(
                            'field' => "plano_{$plano}_{$passageiro}_{$campo['campo_nome_banco']}",
                            'label' => "{$planos_nome[$index]} - Passageiro {$passageiro} - {$campo['campo_nome']}",
                            'rules' => $campo['validacoes'],
                            'groups' => 'dados_segurado'
                        );
                    }
                }
            }



            $this->cotacao->setValidate($validacao);
           if ($this->cotacao->validate_form('dados_segurado')){



               foreach ($planos as $index => $plano) {

                   //busca cotação do cotacao_seguro_viagem
                   $cotacao_seguro_viagem = $this->seguro_viagem->filterByCotacaoPlano($cotacao_id, $plano)
                                                                ->get_all();

                   $cotacao_seguro_viagem = $cotacao_seguro_viagem[0];
                   $dados_cotacao = array();
                   $dados_cotacao['step'] = 4;
                   $this->seguro_viagem->update($cotacao_seguro_viagem['cotacao_seguro_viagem_id'],  $dados_cotacao, TRUE);


                   $data_pessoa = array();
                   $data_pessoa['cotacao_seguro_viagem_id'] = $cotacao_seguro_viagem['cotacao_seguro_viagem_id'];
                   $data_pessoa['email'] = $cotacao_seguro_viagem['email'];
                   $data_pessoa['alteracao_usuario_id'] = $this->session->userdata('usuario_id');
                   $data_pessoa['contratante_passageiro'] = 'CONTRATANTE';
                   $data_pessoa['nome'] = $cotacao['nome'];
                   $data_pessoa['cnpj_cpf'] = app_retorna_numeros($cotacao['cnpj_cpf']);
                   $data_pessoa['data_nascimento'] = app_dateonly_mask_to_mysql($cotacao['data_nascimento']);

                   $data_pessoa['rg'] = $this->input->post("plano_{$plano}_rg");
                   $data_pessoa['sexo'] = $this->input->post("plano_{$plano}_sexo");
                   $data_pessoa['contato_telefone'] = $this->input->post("plano_{$plano}_contato_telefone");
                   $data_pessoa['endereco_cep'] = $this->input->post("plano_{$plano}_endereco_cep");
                   $data_pessoa['endereco'] = $this->input->post("plano_{$plano}_endereco");
                   $data_pessoa['endereco_numero'] = $this->input->post("plano_{$plano}_endereco_numero");
                   $data_pessoa['endereco_complemento'] = $this->input->post("plano_{$plano}_endereco_complemento");
                   $data_pessoa['endereco_bairro'] = $this->input->post("plano_{$plano}_endereco_bairro");
                   $data_pessoa['endereco_cidade'] = $this->input->post("plano_{$plano}_endereco_cidade");
                   $data_pessoa['endereco_uf'] = $this->input->post("plano_{$plano}_endereco_uf");

                   $this->pessoa->insert($data_pessoa, TRUE);

               }

               foreach ($planos as $index => $plano) {
                   for ($passageiro = 2; $passageiro <= $plano_passageiro[$index]; $passageiro++ ) {

                       //busca cotação do cotacao_seguro_viagem
                       $cotacao_seguro_viagem = $this->seguro_viagem->filterByCotacaoPlano($cotacao_id, $plano)
                           ->get_all();

                       $cotacao_seguro_viagem = $cotacao_seguro_viagem[0];


                       $data_pessoa = array();
                       $data_pessoa['cotacao_seguro_viagem_id'] = $cotacao_seguro_viagem['cotacao_seguro_viagem_id'];
                       $data_pessoa['alteracao_usuario_id'] = $this->session->userdata('usuario_id');
                       $data_pessoa['contratante_passageiro'] = 'PASSAGEIRO';
                       $data_pessoa['nome'] = app_dateonly_mask_to_mysql($this->input->post("plano_{$plano}_{$passageiro}_passageiro_nome"));
                       $data_pessoa['data_nascimento'] = app_dateonly_mask_to_mysql($this->input->post("plano_{$plano}_{$passageiro}_passageiro_data_nascimento"));
                       $data_pessoa['cnpj_cpf'] = app_retorna_numeros($this->input->post("plano_{$plano}_{$passageiro}_cpf"));
                       $data_pessoa['sexo'] = $this->input->post("plano_{$plano}_{$passageiro}_sexo");

                       $data_pessoa['contato_telefone'] = $this->input->post("plano_{$plano}_contato_telefone");
                       $data_pessoa['endereco_cep'] = $this->input->post("plano_{$plano}_endereco_cep");
                       $data_pessoa['endereco'] = $this->input->post("plano_{$plano}_endereco");
                       $data_pessoa['endereco_numero'] = $this->input->post("plano_{$plano}_endereco_numero");
                       $data_pessoa['endereco_complemento'] = $this->input->post("plano_{$plano}_endereco_complemento");
                       $data_pessoa['endereco_bairro'] = $this->input->post("plano_{$plano}_endereco_bairro");
                       $data_pessoa['endereco_cidade'] = $this->input->post("plano_{$plano}_endereco_cidade");
                       $data_pessoa['endereco_uf'] = $this->input->post("plano_{$plano}_endereco_uf");
                       $data_pessoa['email'] = $cotacao_seguro_viagem['email'];

                       $this->pessoa->insert($data_pessoa, TRUE);


                   }
               }



               //redirecionar
               $this->session->set_userdata("dados_segurado_{$produto_parceiro_id}", $_POST);

               redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/4/{$cotacao_id}");

           }
        }



        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/seguro_viagem/dados_segurado", $data );

    }


    public function seguro_viagem_add_carrinho($produto_parceiro_id, $cotacao_id, $pedido_id = 0){

        if($pedido_id == 0) {

            $pedido_id = $this->insertPedidoCarrinho($cotacao_id);
        }else{
            $this->updatePedidoCarrinho($pedido_id, $cotacao_id);
        }

        $this->session->set_flashdata('succ_msg', 'Pedido Adicionado no carrinho com sucesso'); //Mensagem de sucesso
        redirect("{$this->controller_uri}/index");

    }

    public function certificado($apolice_id, $export = ''){

        $this->load->model('apolice_model', 'apolice');

        $result = $this->apolice->certificado($apolice_id, $export);
        if($result !== FALSE){
            exit($result);
        }

    }

    public function seguro_viagem_certificado($produto_parceiro_id, $pedido_id = 0){


        $this->load->model('pedido_model', 'pedido');
        $this->load->model('apolice_model', 'apolice');



        $apolice = $this->apolice->getApolicePedido($pedido_id);
        $pedido = $this->pedido->get($pedido_id);

        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['pedido_id'] = $pedido_id;
        $data['cotacao_id'] = $pedido['cotacao_id'];
        $data['apolice'] = $apolice;
        $data['pedido'] = $pedido;


        $this->limpa_cotacao($produto_parceiro_id);

        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/seguro_viagem/certificado", $data );

    }

    /**
     * Passo 2
     * @param $produto_parceiro_id
     * @param int $cotacao_id
     */
    public function seguro_viagem_carrossel($produto_parceiro_id, $cotacao_id = 0)
    {
        //Carrega models necessários
        $this->load->model('produto_parceiro_campo_model', 'campo');
        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('cobertura_plano_model', 'plano_cobertura');
        $this->load->model('cobertura_model', 'cobertura');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('produto_parceiro_desconto_model', 'desconto');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
        $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
        $this->load->model('cotacao_seguro_viagem_cobertura_model', 'cotacao_seguro_viagem_cobertura');
        $this->load->model('contato_tipo_model', 'contato_tipo');

        //Carrega JS
        $this->template->js(app_assets_url('modulos/venda/seguro_viagem/js/base.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_viagem/js/carrossel.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_viagem/js/calculo.js', 'admin'));
        $this->template->css(app_assets_url('modulos/venda/seguro_viagem/css/carrossel.css', 'admin'));
        $this->template->css(app_assets_url('modulos/venda/seguro_viagem/css/base.css', 'admin'));

        //Dados para template
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['row'] =  $this->current_model->get_by_id($produto_parceiro_id);


        $desconto = $this->desconto->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if(count($desconto) > 0){
            $data['desconto'] = $desconto[0];
        }else{
            $data['desconto'] = array('habilitado' => 0);
        }

        //print_r($data['desconto']);exit;

        $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();


        if(count($configuracao) > 0){
            $data['configuracao'] = $configuracao[0];
        }else{
            $data['configuracao'] = array();
        }

        if($data['row']['parceiro_id'] != $this->session->userdata('parceiro_id')){

            $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $this->session->userdata('parceiro_id'));



            $data['configuracao']['repasse_comissao'] = $rel['repasse_comissao'];
            $data['configuracao']['repasse_maximo'] = $rel['repasse_maximo'];
            $data['configuracao']['comissao'] = $rel['comissao'];


            $rel_desconto = $this->relacionamento->get_desconto($produto_parceiro_id, $this->session->userdata('parceiro_id'));
            if(count($rel_desconto) > 0){
                $data['desconto']['data_ini'] = $rel_desconto['desconto_data_ini'];
                $data['desconto']['data_fim'] = $rel_desconto['desconto_data_fim'];
                $data['desconto']['habilitado'] = $rel_desconto['desconto_habilitado'];
            }else{
                $data['desconto'] = array('habilitado' => 0);
            }



        }


        $data['parceiro_id'] = $this->session->userdata('parceiro_id');

        switch ((int)$data['configuracao']['calculo_tipo_id'])
        {
            case self::TIPO_CALCULO_NET:
                $data['label_valor_bruto'] = 'PRÊMIO NET';
                break;
            case self::TIPO_CALCULO_BRUTO:
                $data['label_valor_bruto'] = 'PRÊMIO BRUTO';
                break;
            default:
                $data['label_valor_bruto'] = 'PRÊMIO BRUTO';
                break;


        }

        $cotacao_atual = $this->cotacao->get($cotacao_id);
        $cotacao_antiga = $this->cotacao->with_cotacao_seguro_viagem()->get($cotacao_atual['cotacao_upgrade_id']);

        if((int)$cotacao_atual['cotacao_upgrade_id'] > 0){
            $data['desconto_upgrade'] = 1;
        }else{
            $data['desconto_upgrade'] = 0;
        }

        $data['cotacao_id'] = $cotacao_id;
        if($cotacao_id > 0){

            if($this->cotacao->isCotacaoValida($cotacao_id) == FALSE)
            {
                $this->session->set_flashdata('fail_msg', 'Essa Cotação não é válida');
                redirect("{$this->controller_uri}/index");
            }
            //Seta session
            $this->set_cotacao_session($cotacao_id, $produto_parceiro_id);

            $cotacao_salva = $this->cotacao->with_cotacao_seguro_viagem()
                ->filterByID($cotacao_id)
                ->get_all();


            if($cotacao_salva)
            {
                $data['cotacao_codigo'] = $cotacao_salva[0]['codigo'];
                $data['carrossel']['num_passageiro'] = $cotacao_salva[0]['num_passageiro'];
                $data['carrossel']['repasse_comissao'] = $cotacao_salva[0]['repasse_comissao'];
                $data['carrossel']['desconto_condicional'] = $cotacao_salva[0]['desconto_condicional'];
                $data['carrossel']['desconto_condicional_valor'] = $cotacao_salva[0]['desconto_condicional_valor'];

                $data['carrinho'] = array();
                $data['carrinho_hidden']['plano'] = array();
                $data['carrinho_hidden']['plano_nome'] = array();
                $data['carrinho_hidden']['valor'] = array();
                $data['carrinho_hidden']['comissao_repasse'] = array();
                $data['carrinho_hidden']['desconto_condicional'] = array();
                $data['carrinho_hidden']['desconto_condicional_valor'] = array();
                $data['carrinho_hidden']['valor_total'] = array();
                $data['carrinho_hidden']['num_passageiro'] = array();
                $data['carrinho_hidden']['cobertura_adicional'] = "";
                $data['carrinho_hidden']['cobertura_adicional_valor'] = "";
                $data['carrinho_hidden']['cobertura_adicional_valor_total'] = "";

                foreach ($cotacao_salva as $index => $item)
                {
                    if((int)$item['produto_parceiro_plano_id'] > 0) {
                        $plano_salvo = $this->plano->get($item['produto_parceiro_plano_id']);

                        //exit($plano_salvo['produto_parceiro_plano_id']);

                        if (($cotacao_antiga && isset($cotacao_antiga['produto_parceiro_plano_id']) && $cotacao_antiga['produto_parceiro_plano_id'] != $item['produto_parceiro_plano_id']) || (!$cotacao_antiga)) {
                            $data['carrinho_hidden']['plano'][] = $plano_salvo['produto_parceiro_plano_id'];
                            $data['carrinho_hidden']['plano_nome'][] = $plano_salvo['nome'];
                            $data['carrinho_hidden']['valor'][] = app_format_currency($item['premio_liquido'], false, 3);
                            $data['carrinho_hidden']['comissao_repasse'][] = app_format_currency($item['repasse_comissao'], false, 3);
                            $data['carrinho_hidden']['desconto_condicional'][] = app_format_currency($item['desconto_condicional'], false, 3);
                            $data['carrinho_hidden']['desconto_condicional_valor'][] = $item['desconto_condicional_valor'];
                            $data['carrinho_hidden']['valor_total'][] = app_format_currency($item['premio_liquido_total'], false, 3);
                            $data['carrinho_hidden']['num_passageiro'][] = $item['num_passageiro'];

                            $cotacao_adicional = $this->cotacao_seguro_viagem_cobertura->get_many_by(array(
                                'cotacao_seguro_viagem_id' => $item['cotacao_seguro_viagem_id'],
                            ));

                            foreach ($cotacao_adicional as $ca) {
                                $data['carrinho_hidden']['cobertura_adicional'] .= $ca['cobertura_plano_id'].";";
                                $data['carrinho_hidden']['cobertura_adicional_valor'] .= $ca['valor'].";";
                                $data['carrinho_hidden']['cobertura_adicional_valor_total'] += $ca['valor'];
                            }

                            $data['carrinho'][] = array(
                                'item' => $index + 1,
                                'plano_id' => $plano_salvo['produto_parceiro_plano_id'],
                                'plano' => $plano_salvo['nome'],
                                'passageiro' => $item['num_passageiro'],
                                'valor' => app_format_currency($item['premio_liquido_total'], FALSE, 2)
                            );
                        }
                    }

                }

                $data['carrinho_hidden']['plano'] = implode(';', $data['carrinho_hidden']['plano']);
                $data['carrinho_hidden']['plano_nome'] = implode(';', $data['carrinho_hidden']['plano_nome']);
                $data['carrinho_hidden']['valor'] = implode(';', $data['carrinho_hidden']['valor']);
                $data['carrinho_hidden']['comissao_repasse'] = implode(';', $data['carrinho_hidden']['comissao_repasse']);
                $data['carrinho_hidden']['desconto_condicional'] = implode(';', $data['carrinho_hidden']['desconto_condicional']);
                $data['carrinho_hidden']['desconto_condicional_valor'] = implode(';', $data['carrinho_hidden']['desconto_condicional_valor']);
                $data['carrinho_hidden']['valor_total'] = implode(';', $data['carrinho_hidden']['valor_total']);
                $data['carrinho_hidden']['num_passageiro'] = implode(';', $data['carrinho_hidden']['num_passageiro']);




                $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");
            }
            else
            {

                $cotacao_salva = $this->cotacao->get($cotacao_id);
                $data['cotacao_codigo'] = $cotacao_salva['codigo'];

                $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");

                $data['carrinho'] = array();
            }
        }
        else
        {
            $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");
            $data['carrinho'] = array();
        }

        $dias = app_date_get_diff_dias($cotacao['data_saida'], $cotacao['data_retorno'], 'D') + 1;
        $data['cotacao'] = $cotacao;
        $data['quantidade_dias'] = $dias;
        $data['regra_preco'] = $this->regra_preco->with_regra_preco()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();


        /**
         * Busca Planos e coberturas
         */
        //$data['coberturas'] = $this->cobertura->filter_by_produto($data['row']['produto_id'])->get_all();
         $data['coberturas'] = $this->cobertura->getCoberturasProdutoParceiroPlano($produto_parceiro_id);

        if(isset($cotacao_antiga) && $cotacao_antiga)
        {
            $cotacao_antiga = $this->cotacao->with_cotacao_seguro_viagem()->get($cotacao_antiga['cotacao_id']);

            if($data['row']['venda_agrupada']) {
                $arrPlanos = $this->plano
                    ->distinct()
                    ->order_by('produto_parceiro_plano.ordem', 'asc')
                    ->with_origem($cotacao['origem_id'])
                    ->with_destino($cotacao['destino_id'])
                    ->with_produto_parceiro()
                    ->with_produto()
                    ->where("produto_parceiro_plano.produto_parceiro_plano_id", "!=", $cotacao_antiga['produto_parceiro_plano_id'])
                    ->get_many_by(array(
                        'produto_parceiro.venda_agrupada' => 1,
                    ));
            }else{
                $arrPlanos = $this->plano
                    ->distinct()
                    ->order_by('produto_parceiro_plano.ordem', 'asc')
                    ->with_origem($cotacao['origem_id'])
                    ->with_destino($cotacao['destino_id'])
                    ->where("produto_parceiro_plano.produto_parceiro_plano_id", "!=", $cotacao_antiga['produto_parceiro_plano_id'])
                    ->get_many_by(array(
                        'produto_parceiro_id' => $produto_parceiro_id
                    ));
            }
        }
        else
        {
            if($data['row']['venda_agrupada']) {
                $arrPlanos = $this->plano
                    ->distinct()
                    ->order_by('produto_parceiro_plano.ordem', 'asc')
                    ->with_origem($cotacao['origem_id'])
                    ->with_destino($cotacao['destino_id'])
                    ->with_produto_parceiro()
                    ->with_produto()
                    ->get_many_by(array(
                        'produto_parceiro.venda_agrupada' => 1,
                        'produto_parceiro.deletado' => 0
                    ));
            }else{
                $arrPlanos = $this->plano
                    ->distinct()
                    ->order_by('produto_parceiro_plano.ordem', 'asc')
                    ->with_origem($cotacao['origem_id'])
                    ->with_destino($cotacao['destino_id'])
                    ->get_many_by(array(
                        'produto_parceiro_id' => $produto_parceiro_id
                    ));
            }
        }






        $fail_msg = '';


        foreach ($arrPlanos as $index => $arrPlano) {
            if(($arrPlano['unidade_tempo'] == 'MES')) {
                $plano_limite_vigencia = ($arrPlano['limite_vigencia'] * 30);
            }elseif($arrPlano['unidade_tempo'] == 'ANO'){
                $plano_limite_vigencia = ($arrPlano['limite_vigencia'] * 365);
            }else{
                $plano_limite_vigencia = $arrPlano['limite_vigencia'];
            }
            if($plano_limite_vigencia < $data['quantidade_dias'] ){
                $fail_msg .= "O Limite da vigencia do plano {$arrPlano['nome']} é de {$arrPlano['limite_vigencia']} {$arrPlano['unidade_tempo']}S <br />";
                unset($arrPlanos[$index]);
            }
        }


        if(!$arrPlanos)
        {
            if(!empty($fail_msg)){
                $fail_msg = "O prazo para sua viagem é de {$data['quantidade_dias']} Dias <br/>" .$fail_msg;
                $this->session->set_flashdata('fail_msg', $fail_msg);
            }else{
                $this->session->set_flashdata('fail_msg', 'Não existem planos para esta origem e destino.');
            }

            redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/1");
        }

        $data['planos'] = array();
        foreach ($arrPlanos as $plano)
        {
            $arrCoberturas = $this->plano_cobertura->filter_by_produto_parceiro_plano($plano['produto_parceiro_plano_id'])->get_all();
            foreach ($arrCoberturas as $idx => $cob) {
                $arrCoberturas[$idx]['preco'] = app_format_currency($arrCoberturas[$idx]['preco'], true, 3);
                $arrCoberturas[$idx]['porcentagem'] = app_format_currency($arrCoberturas[$idx]['porcentagem'], false, 3) . ' %';
            }
            $plano['cobertura'] = $arrCoberturas;
            $data['planos'][] = $plano;
        }

        // print_r($data['planos']);exit;
        $data['list'] = array();

        if($_POST)
        {
            $post_plano = $this->input->post('plano');
            if(empty($post_plano)){
                $this->session->set_flashdata('fail_msg', 'O Carrinho esta vazio, adicione um plano');
                if ($cotacao_id > 0) {
                    redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2/{$cotacao_id}");
                } else {
                    redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2");
                }


            }else{

                if ($this->cotacao->validate_form('carrossel'))
                {
                    $this->session->set_userdata("carrossel_{$produto_parceiro_id}", $_POST);
                    $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $cotacao);

                    if($this->input->post('salvar_cotacao') == 1)
                    {
                        if($cotacao_id > 0)
                        {
                            $this->update_cotacao_carrossel($produto_parceiro_id, $cotacao_id);
                        }
                        else
                        {
                            $cotacao_id = $this->salvar_cotacao($produto_parceiro_id);
                        }

                        //Merge dos dados para comunicação evento
                        $dados_comunicacao = array_merge($_POST, $cotacao);

                        //Carrega biblioteca e dispara evento para salvar cotação
                        $this->load->library("Comunicacao");
                        $comunicacao = new Comunicacao();
                        $comunicacao->setDestinatario($cotacao['email']);
                        $comunicacao->setNomeDestinatario($cotacao['nome']);
                        $comunicacao->setMensagemParametros($dados_comunicacao);
                        $comunicacao->disparaEvento("cotacao_salva", $cotacao['produto_parceiro_id']);

                        if($data['configuracao']['salvar_cotacao_formulario'] == 1)
                        {
                            redirect("$this->controller_uri/seguro_viagem/41/8/$cotacao_id");
                        }
                        else
                        {
                            $cotacao = $this->cotacao->get($cotacao_id);
                            $this->session->set_flashdata('succ_msg', 'Cotação salva com sucesso, código: '. $cotacao['codigo']); //Mensagem de sucesso
                            $this->limpa_cotacao($produto_parceiro_id);
                            redirect("$this->controller_uri/index");

                        }
                    }
                    else
                    {
                        if ($cotacao_id > 0) {


                            if ($this->seguro_viagem->verifica_possui_desconto($cotacao_id)) {
                                $this->update_cotacao_carrossel($produto_parceiro_id, $cotacao_id);
                                $update_cotacao = array();
                                $update_cotacao['cotacao_status_id'] = 4;
                                $this->cotacao->update($cotacao_id, $update_cotacao, TRUE);
                                $this->session->set_flashdata('succ_msg', 'Você optou pelo desconto condicional, esse desconto precisa de autorização, salvamos sua cotação com o código: ' . $cotacao_salva[0]['codigo'] . ' Aguarde a aprovação'); //Mensagem de sucesso
                                $this->limpa_cotacao($produto_parceiro_id);
                                redirect("$this->controller_uri/index");
                            }

                            redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/3/{$cotacao_id}");
                        } else {
                            redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/3");
                        }
                    }

                }


            }

        }


        $view = "admin/venda/seguro_viagem/{$this->layout}/carrossel";
        if(!view_exists($view))
            $view = "admin/venda/seguro_viagem/carrossel";

        $this->template->load("admin/layouts/{$this->layout}", $view, $data );

    }


    private function insertPedidoCarrinho($cotacao_id)
    {
        //Carrega models

        $this->load->model('pedido_model', 'pedido');
        $this->load->model('pedido_codigo_model', 'pedido_codigo');
        $this->load->model('pedido_cartao_model', 'pedido_cartao');
        $this->load->model('pedido_transacao_model', 'pedido_transacao');
        $this->load->model('cotacao_seguro_viagem_model', 'seguro_viagem');
        $this->load->model('cotacao_model', 'cotacao');


        $cotacao = $this->cotacao->get($cotacao_id);
        $valor_total = $this->seguro_viagem->getValorTotal($cotacao_id);




        $dados_pedido = array();
        $dados_pedido['cotacao_id'] = $cotacao_id;
        $dados_pedido['produto_parceiro_pagamento_id'] = 0;
        $dados_pedido['pedido_status_id'] = 1;
        $dados_pedido['codigo'] = $this->pedido_codigo->get_codigo_pedido_formatado('BE');
        $dados_pedido['status_data'] = date('Y-m-d H:i:s');
        $dados_pedido['valor_total'] = $valor_total;
        $dados_pedido['num_parcela'] = 0;
        $dados_pedido['valor_parcela'] = 0;
        $dados_pedido['alteracao_usuario_id'] = $this->session->userdata('usuario_id');

        $pedido_id = $this->pedido->insert($dados_pedido, TRUE);
        $this->pedido_transacao->insStatus($pedido_id, 'criado');
        $this->pedido_transacao->insStatus($pedido_id, 'carrinho');


        //altera o status da cotação
        $this->cotacao->update($cotacao_id, array('cotacao_status_id' => 2), TRUE);


        return $pedido_id;

    }

    private function updatePedidoCarrinho($pedido_id, $cotacao_id){



        $this->load->model('pedido_model', 'pedido');
        $this->load->model('pedido_codigo_model', 'pedido_codigo');
        $this->load->model('pedido_cartao_model', 'pedido_cartao');
        $this->load->model('pedido_transacao_model', 'pedido_transacao');
        $this->load->model('cotacao_seguro_viagem_model', 'seguro_viagem');
        $this->load->model('cotacao_model', 'cotacao');

        $this->load->model('produto_parceiro_capitalizacao_model', 'parceiro_capitalizacao');
        $this->load->model('capitalizacao_model', 'capitalizacao');
        $this->load->model('capitalizacao_serie_titulo_model', 'titulo');




        $valor_total = $this->seguro_viagem->getValorTotal($cotacao_id);




        $dados_pedido = array();
        $dados_pedido['produto_parceiro_pagamento_id'] = 0;
        $dados_pedido['valor_total'] = $valor_total;
        $dados_pedido['num_parcela'] = 0;
        $dados_pedido['valor_parcela'] = 0;
        $dados_pedido['alteracao_usuario_id'] = $this->session->userdata('usuario_id');

        $this->pedido->update($pedido_id,  $dados_pedido, TRUE);
        $this->pedido_transacao->insStatus($pedido_id, 'alteracao');
        $this->pedido_transacao->insStatus($pedido_id, 'carrinho');


        //altera o status da cotação
        $this->cotacao->update($cotacao_id, array('cotacao_status_id' => 2), TRUE);



        return $pedido_id;

    }

    /**
     * Efetua calculo, retorna JSON
     */
    public function calculo()
    {
        $this->load->model('produto_parceiro_campo_model', 'campo');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('cobertura_plano_model', 'plano_cobertura');
        $this->load->model('cobertura_model', 'cobertura');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('produto_parceiro_desconto_model', 'desconto');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
        $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');


        $sucess = TRUE;
        $messagem = '';
        $campo = 'data_retorno';

        $produto_parceiro_id = $this->input->post('produto_parceiro_id');
        $parceiro_id = $this->input->post('parceiro_id');
        $num_passageiro = $this->input->post('num_passageiro');

        $coberturas_adicionais = ($this->input->post('coberturas')) ? $this->input->post('coberturas') : array();

        $repasse_comissao = app_unformat_percent($this->input->post('repasse_comissao'));
        $desconto_condicional= app_unformat_percent($this->input->post('desconto_condicional'));
        $desconto = $this->desconto->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        $data_saida = $this->input->post("data_saida");
        $data_retorno = $this->input->post("data_retorno");


        $desconto_upgrade = 0;
        $cotacao_id = $this->input->post("cotacao_id");

        if($cotacao_id) {
            $cotacao_upgrade = $this->cotacao->get($cotacao_id);

            if(($cotacao_upgrade) && ((int)$cotacao_upgrade['cotacao_upgrade_id']) > 0){

                $pedido_antigo = $this->pedido->get_by(array('cotacao_id' => $cotacao_upgrade['cotacao_upgrade_id']));
                $desconto_upgrade = $pedido_antigo['valor_total'];

            }
        }

        //Retorna diferença de dias
        $quantidade_dias = app_date_get_diff_dias($data_saida, $data_retorno, 'D') + 1;



        if(!app_validate_data($data_saida)){
            $sucess = FALSE;
            $messagem .= "<strong>A DATA DE SAÍDA NÃO NÃO É UMA DATA VÁLIDA</strong><br />";
            $quantidade_dias = 1;
        }

        if(!app_validate_data($data_retorno)){
            $sucess = FALSE;
            $messagem .= "<strong>A DATA DE RETORNO NÃO NÃO É UMA DATA VÁLIDA</strong><br />";
            $quantidade_dias = 1;
        }

        if((app_date_get_diff_dias(date('d/m/Y'), $data_saida, 'D') + 1) < 1){
            $sucess = FALSE;
            $messagem .= "<strong>A DATA DE SAÍDA NÃO PODE SER MENOR QUE A DATA DE HOJE</strong><br />";
            $quantidade_dias = 1;
        }

        if($quantidade_dias < 1){
            $sucess = FALSE;
            $messagem .= "<strong>A DATA DE SAÍDA NÃO PODE SER MENOR QUE A DATA DE RETORNO</strong><br />";
            $quantidade_dias = 1;
        }


        $row =  $this->current_model->get_by_id($produto_parceiro_id);

        if(count($desconto) > 0){
            $desconto = $desconto[0];
        }else{
            $desconto = array('habilitado' => 0);
        }

        $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if(count($configuracao) > 0){
            $configuracao = $configuracao[0];
        }else{
            $configuracao = array();
        }


        $markup = 0;
        if($row['parceiro_id'] != $parceiro_id){


            $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $parceiro_id);

            $configuracao['repasse_comissao'] =  $rel['repasse_comissao'];
            $configuracao['repasse_maximo'] = $rel['repasse_maximo'];
            $configuracao['comissao'] = $rel['comissao'];


            //buscar o markup
            $markup = $this->relacionamento->get_comissao_markup($produto_parceiro_id, $parceiro_id);



            $rel_desconto = $this->relacionamento->get_desconto($produto_parceiro_id, $parceiro_id);
            if(count($rel_desconto) > 0){
                $desconto['data_ini'] = $rel_desconto['desconto_data_ini'];
                $desconto['data_fim'] = $rel_desconto['desconto_data_fim'];
                $desconto['habilitado'] = $rel_desconto['desconto_habilitado'];
            }else{
                $desconto = array('habilitado' => 0);
            }
            


        }

        if($repasse_comissao > $configuracao['repasse_maximo'] ){
            $repasse_comissao = $configuracao['repasse_maximo'];
        }

        $repasse_comissao = str_pad(number_format((double)$repasse_comissao, 2, '.', ''), 5, "0", STR_PAD_LEFT);

        $comissao_corretor = ($configuracao['comissao'] - $repasse_comissao);

        /**

        if($data['row']['venda_agrupada']) {
        $arrPlanos = $this->plano
        ->distinct()
        ->with_origem($cotacao['origem_id'])
        ->with_destino($cotacao['destino_id'])
        ->with_produto_parceiro()
        ->with_produto()
        ->get_many_by(array(
        'produto_parceiro.venda_agrupada' => 1
        ));
        }else{
        $arrPlanos = $this->plano
        ->distinct()
        ->with_origem($cotacao['origem_id'])
        ->with_destino($cotacao['destino_id'])
        ->get_many_by(array(
        'produto_parceiro_id' => $produto_parceiro_id
        ));
        }
         */

        $valores_bruto = $this->getValoresPlano($produto_parceiro_id, $num_passageiro, $quantidade_dias);

        $valores_cobertura_adicional_total = array();
        $valores_cobertura_adicional = array();
        if($coberturas_adicionais){

            foreach ($coberturas_adicionais as $coberturas_adicional) {
                $cobertura = explode(';', $coberturas_adicional);
                $valor = $this->getValorCoberturaAdicional($cobertura[0], $cobertura[1], $quantidade_dias);
                $valores_cobertura_adicional_total[$cobertura[0]] = (isset($valores_cobertura_adicional_total[$cobertura[0]])) ? ($valores_cobertura_adicional_total[$cobertura[0]] + $valor) : $valor;
                $valores_cobertura_adicional[$cobertura[0]][] = $valor;

            }

        }



        if(!$valores_bruto)
        {
            return null;
        }


        if($row['venda_agrupada']){
            $arrPlanos = $this->plano->distinct()
                ->order_by('produto_parceiro_plano.ordem', 'asc')
                ->with_produto_parceiro()
                ->with_produto()
                ->get_many_by(array(
                    'produto_parceiro.venda_agrupada' => 1
                ));
        }else {
            $arrPlanos = $this->plano->order_by('produto_parceiro_plano.ordem', 'asc')->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        }
        $valores_liquido = array();

        //verifica o limite da vigencia dos planos
        $fail_msg = '';
        foreach ($arrPlanos as $index => $arrPlano) {
            if(($arrPlano['unidade_tempo'] == 'MES')) {
                $plano_limite_vigencia = ($arrPlano['limite_vigencia'] * 30);
            }elseif($arrPlano['unidade_tempo'] == 'ANO'){
                $plano_limite_vigencia = ($arrPlano['limite_vigencia'] * 365);
            }else{
                $plano_limite_vigencia = $arrPlano['limite_vigencia'];
            }
            if($plano_limite_vigencia < $quantidade_dias ){
                $fail_msg .= "O Limite da vigencia do plano {$arrPlano['nome']} é de {$arrPlano['limite_vigencia']} {$arrPlano['unidade_tempo']}S <br />";
            }
        }

        if(!empty($fail_msg)){
            $sucess = FALSE;
            $messagem .= "<strong>ALTERE A DATA DE RETORNO DA SUA VIAGEM</strong><br />";
            $messagem .= $fail_msg;
        }


        /**
         * FAZ O CÁLCULO DO PLANO
         */
        $desconto_condicional_valor = 0;
        foreach ($arrPlanos as $plano){
            //precificacao_tipo_id

            switch ((int)$configuracao['calculo_tipo_id'])
            {
                case self::TIPO_CALCULO_NET:
                    $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
                    $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
                    $valor = ($valor/(1-(($markup + $comissao_corretor)/100)));
                    $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
                    $valor -= $desconto_condicional_valor;
                    $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
                    break;
                case self::TIPO_CALCULO_BRUTO:
                    $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
                    $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
                        $valor = ($valor) - (($valor) * (($markup + $comissao_corretor)/100));
                    $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
                    $valor -= $desconto_condicional_valor;
                    $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
                    break;
                default:
                    break;


            }

        }


        $regra_preco = $this->regra_preco->with_regra_preco()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        $valores_liquido_total = array();
        foreach ($valores_liquido as $key => $value) {
            $valores_liquido_total[$key] = $value;
            foreach ($regra_preco as $regra) {
                $valores_liquido_total[$key] += (($regra['parametros']/100) * $value);
                $valores_liquido_total[$key] -= $desconto_upgrade;
            }
        }

        //Resultado
        $result  = array(
            'sucess' => $sucess,
            'produto_parceiro_id' => $produto_parceiro_id,
            'num_passageiro' => $num_passageiro,
            'repasse_comissao' => $repasse_comissao,
            'comissao' => $comissao_corretor,
            'desconto_upgrade' => $desconto_upgrade,
            'desconto_condicional_valor' => $desconto_condicional_valor,
            'valores_bruto' => $valores_bruto,
            'valores_cobertura_adicional' => $valores_cobertura_adicional,
            'valores_totais_cobertura_adicional' => $valores_cobertura_adicional_total,
            'valores_liquido' => $valores_liquido,
            'valores_liquido_total' => $valores_liquido_total,
            'quantidade_dias' => $quantidade_dias,
            'mensagem' => $messagem,
            'campo' => $campo,
        );

        //Salva cotação

        if($cotacao_id) {
            $cotacao_salva = $this->cotacao->with_cotacao_seguro_viagem()
                ->filterByID($cotacao_id)
                ->get_all();

            foreach ($cotacao_salva as $index => $item)
            {

                $cotacao_sv = array();
                $cotacao_sv['data_saida'] = app_dateonly_mask_to_mysql($data_saida);
                $cotacao_sv['data_retorno'] = app_dateonly_mask_to_mysql($data_retorno);
                $cotacao_sv['qnt_dias'] = $quantidade_dias;
                $cotacao_sv['num_passageiro'] = $num_passageiro;
                $cotacao_sv['repasse_comissao'] = $repasse_comissao;
                $cotacao_sv['comissao_corretor'] = $comissao_corretor;
                $cotacao_sv['desconto_condicional'] = $desconto_condicional;
                $cotacao_sv['desconto_condicional_valor'] = $desconto_condicional_valor;
                $this->cotacao_seguro_viagem->update($item['cotacao_seguro_viagem_id'], $cotacao_sv, TRUE);
            }

        }

        //Seta sessão
       // $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $cotacao);

        //Output
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }


    /**
     * Retorna valores do plano
     * @param $produto_parceiro_id
     * @param int $num_passageiro
     * @return array
     */
    private function getValoresPlano($produto_parceiro_id, $num_passageiro = 1, $quantidade_dias){

        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('moeda_model', 'moeda');
        $this->load->model('moeda_cambio_model', 'moeda_cambio');

        $num_passageiro = (int)$num_passageiro;

        $moeda_padrao = $this->moeda->filter_by_moeda_padrao()->get_all();
        $moeda_padrao = $moeda_padrao[0];

        $produto_parceiro =  $this->current_model->get_by_id($produto_parceiro_id);
        if($produto_parceiro['venda_agrupada']) {
            $arrPlanos = $this->plano->distinct()
                ->order_by('produto_parceiro_plano.ordem', 'asc')
                ->with_produto_parceiro()
                ->with_produto()
                ->get_many_by(array(
                    'produto_parceiro.venda_agrupada' => 1
                ));
        }else{
            $arrPlanos = $this->plano->order_by('produto_parceiro_plano.ordem', 'asc')->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        }
        $valores = array();
        foreach ($arrPlanos as $plano){
            switch ((int)$plano['precificacao_tipo_id']) {
              case self::PRECO_TIPO_TABELA:

                $calculo = ($this->getValorTabelaFixa($plano['produto_parceiro_plano_id'], $quantidade_dias)*$num_passageiro);
                if($calculo)
                  $valores[$plano['produto_parceiro_plano_id']] = $calculo;
                else
                  return null;

                if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
                  $valores[$plano['produto_parceiro_plano_id']] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$plano['produto_parceiro_plano_id']]);
                }
                break;
              case self::PRECO_TIPO_COBERTURA:
                break;
              case self::PRECO_TIPO_VALOR_SEGURADO:
                break;
              default:
                break;
            }
        }

        return $valores;


    }

    private function getValorCoberturaAdicional($produto_parceiro_plano_id, $cobertura_plano_id, $qntDias){
        $this->load->model('cobertura_plano_model', 'cobertura_plano');

        $cobertura = $this->cobertura_plano->get_by(array(
            'produto_parceiro_plano_id' => $produto_parceiro_plano_id,
            'cobertura_plano_id' => $cobertura_plano_id,

        ));

        if($cobertura){
            return (app_calculo_porcentagem($cobertura['porcentagem'],$cobertura['preco'])*$qntDias);
        }else{
            return 0;
        }

    }

    private function getValorTabelaFixa($produto_parceiro_plano_id, $qntDias){
        $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'itens');

        $valor = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                    ->filter_by_intevalo_dias($qntDias)
                    ->filter_by_tipo('RANGE')
                    ->get_all();

        if(count($valor) > 0){
           return $valor[0]['valor'];
        }else{
            //não achou

            //Verifica se Busca por mês
            if(($qntDias >= 30) && ($qntDias % 30) == 0){
                $valor = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                    ->filter_by_intevalo_dias(floor($qntDias/30), 'MES')
                    ->filter_by_tipo('RANGE')
                    ->get_all();
                if(count($valor) > 0){
                    return $valor[0]['valor'];
                }
            }


            //Verifica se Busca por ANO
            if(($qntDias >= 365) && ($qntDias % 365) == 0){
                $valor = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                    ->filter_by_intevalo_dias(floor($qntDias/365), 'ANO')
                    ->filter_by_tipo('RANGE')
                    ->get_all();
                if(count($valor) > 0){
                    return $valor[0]['valor'];
                }
            }


            $ultimo = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                ->filter_by_intevalo_menor($qntDias, 'DIA')
                ->order_by('produto_parceiro_plano_precificacao_itens.final', 'DESC')
                ->filter_by_tipo('RANGE')
                ->limit(1)
                ->get_all();

            if(is_array($ultimo) && sizeof($ultimo) > 0)
                $ultimo = $ultimo[0];


            $valor_adicional = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                ->filter_by_tipo('ADICIONAL')
                ->limit(1)
                ->get_all();

            if(is_array($valor_adicional) && sizeof($valor_adicional) > 0)
            {
                $valor_adicional = $valor_adicional[0];

                $valor = $ultimo['valor'];
                for ($i = $ultimo['final']; $i < $qntDias; $i++){
                    $valor += $valor_adicional['valor'];
                }
                return $valor;
            }
            return null;


        }



    }

    private function salvar_cotacao_campos_adicionais($cotacao_id){


        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cliente_contato_model', 'cliente_contato');

        $cotacao = $this->cotacao->get($cotacao_id);


        //insere os contatos

        $qnt_cotnato = $this->input->post('quantidade_contatos');


        for ($i = 0; $i < $qnt_cotnato; $i++) {

            $cliente_terceiro = isset($this->input->post('cliente_terceiro')[$i]) ? $this->input->post('cliente_terceiro')[$i] : 0;
            $nome_segurado = (isset($this->input->post('contato_nome')[$i]) && $cliente_terceiro == 1) ? $this->input->post('contato_nome')[$i] : $this->input->post('nome_segurado');
            $contato = (isset($this->input->post('contato')[$i])) ? $this->input->post('contato')[$i] : '';
            $contato_tipo_id = (isset($this->input->post('contato_tipo_id')[$i])) ? $this->input->post('contato_tipo_id')[$i] : '';
            $melhor_horario = (isset($this->input->post('melhor_horario')[$i])) ? $this->input->post('melhor_horario')[$i] : 'Q';

            if($contato && $contato_tipo_id) {
                $data_contato = array();
                $data_contato['cliente_id'] = $cotacao['cliente_id'];
                $data_contato['cliente_contato_nivel_relacionamento_id'] = 3;
                $data_contato['decisor'] = 1;
                $data_contato['nome'] = $nome_segurado;
                $data_contato['contato'] = $contato;
                $data_contato['contato_tipo_id'] = $contato_tipo_id;
                $data_contato['cliente_terceiro'] = $cliente_terceiro;
                $data_contato['melhor_horario'] = $melhor_horario;
//                $data_contato['data_nascimento'] = app_dateonly_mask_to_mysql($cotacao['data_nascimento']);
                $this->cliente_contato->insert_not_exist_contato($data_contato);
              //  print_r($data_contato);
            }
        }


        $data_cotacao = array();
        $data_cotacao['motivo'] = $this->input->post('salvar_motivo');
        $data_cotacao['motivo_ativo'] = $this->input->post('motivo_ativo');
        $data_cotacao['motivo_obs'] = $this->input->post('motivo_obs');

        $this->cotacao->update($cotacao_id,  $data_cotacao, TRUE);

    }

    private function salvar_cotacao($produto_parceiro_id)
    {
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_contato_model', 'cliente_contato');
        $this->load->model('cliente_codigo_model', 'cliente_codigo');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_codigo_model', 'cotacao_codigo');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('cotacao_seguro_viagem_cobertura_model', 'cotacao_seguro_viagem_cobertura');
        $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');

        $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $carrossel = $this->session->userdata("carrossel_{$produto_parceiro_id}");

        //verifica se o cliente existe
        $cliente = $this->cliente->filterByCPFCNPJ(app_retorna_numeros($cotacao['cnpj_cpf']))
                                 ->get_all();

        $row =  $this->current_model->get_by_id($produto_parceiro_id);


        $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if(count($configuracao) > 0){
            $configuracao = $configuracao[0];
        }else{
            $configuracao = array();
        }

        if($row['parceiro_id'] != $this->session->userdata('parceiro_id')){


            $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $this->session->userdata('parceiro_id'));

            $data['configuracao']['repasse_comissao'] = $rel['repasse_comissao'];
            $data['configuracao']['repasse_maximo'] = $rel['repasse_maximo'];
            $data['configuracao']['comissao'] = $rel['comissao'];

        }


        $regra_preco = $this->regra_preco->with_regra_preco()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        if(count($cliente) == 0){
            //insere novo cliente
            $data_cliente = array();
            $data_cliente['tipo_cliente'] = (app_verifica_cpf_cnpj(app_retorna_numeros($cotacao['cnpj_cpf'])) == 'CNPJ') ? 'CO' : 'CF';
            $data_cliente['cnpj_cpf'] = app_retorna_numeros($cotacao['cnpj_cpf']);
            $data_cliente['codigo'] = $this->cliente_codigo->get_codigo_cliente_formatado($data_cliente['tipo_cliente']);
            $data_cliente['colaborador_id'] = 1;
            $data_cliente['colaborador_comercial_id'] = 1;
            $data_cliente['titular'] = 1;
            $data_cliente['razao_nome'] = $cotacao['nome'];
            $data_cliente['data_nascimento'] = app_dateonly_mask_to_mysql($cotacao['data_nascimento']);
            $data_cliente['cliente_evolucao_status_id'] = 6; //Salva como prospect
            $data_cliente['grupo_empresarial_id'] = 0;

            $cliente_id = $this->cliente->insert($data_cliente, TRUE);
            $cliente = $this->cliente->get($cliente_id);

            //Inseri Contato de cliente E-mail
            $data_contato = array();
            $data_contato['cliente_id'] = $cliente_id;
            $data_contato['cliente_contato_nivel_relacionamento_id'] = 3;
            $data_contato['decisor'] = 1;
            $data_contato['nome'] = $cotacao['nome'];
            $data_contato['contato'] = $cotacao['email'];
            $data_contato['contato_tipo_id'] = 1;
            $data_contato['data_nascimento'] =  app_dateonly_mask_to_mysql($cotacao['data_nascimento']);
            $this->cliente_contato->insert_contato($data_contato);

            //Celular
            $data_contato['contato'] = app_retorna_numeros($cotacao['telefone']);
            $data_contato['contato_tipo_id'] = 2;
            $this->cliente_contato->insert_contato($data_contato);

        }else{
            //
            $cliente = $cliente[0];
        }

        //salva cotacão
        $data_cotacao = array();
        $data_cotacao['cliente_id'] = $cliente['cliente_id'];
        $data_cotacao['codigo'] = $this->cotacao_codigo->get_codigo_cotacao_formatado('BE');
        $data_cotacao['cotacao_tipo'] = 'ONLINE';
        $data_cotacao['parceiro_id'] = $this->session->userdata('parceiro_id');
        $data_cotacao['usuario_cotacao_id'] = $this->session->userdata('usuario_id');
        $data_cotacao['usuario_venda_id'] = 0;
        $data_cotacao['cotacao_status_id'] = 1;
        $data_cotacao['alteracao_usuario_id'] = 1;
        $data_cotacao['produto_parceiro_id'] = $produto_parceiro_id;

        $cotacao_id = $this->cotacao->insert($data_cotacao, TRUE);

        $planos = explode(';', $carrossel['plano']);
        $valores = explode(';', $carrossel['valor']);
        $comissao_repasse = explode(';', $carrossel['comissao_repasse']);
        $desconto_condicional = explode(';', $carrossel['desconto_condicional']);
        $desconto_condicional_valor = explode(';', $carrossel['desconto_condicional_valor']);
        $valores_totais = explode(';', $carrossel['valor_total']);
        $num_passageiros = explode(';', $carrossel['num_passageiro']);

        foreach ($planos as $index => $plano)
        {
            $data_cotacao_sv = array();
            $data_cotacao_sv['produto_parceiro_id'] = $produto_parceiro_id;
            $data_cotacao_sv['produto_parceiro_plano_id'] = $plano;
            $data_cotacao_sv['cotacao_id'] = $cotacao_id;
            $data_cotacao_sv['seguro_viagem_motivo_id'] = $cotacao['seguro_viagem_motivo_id'];
            $data_cotacao_sv['email'] = $cotacao['email'];
            $data_cotacao_sv['telefone'] = $cotacao['telefone'];
            $data_cotacao_sv['origem_id'] = $cotacao['origem_id'];
            $data_cotacao_sv['destino_id'] = $cotacao['destino_id'];
            $data_cotacao_sv['data_saida'] = app_dateonly_mask_to_mysql($cotacao['data_saida']);
            $data_cotacao_sv['data_retorno'] = app_dateonly_mask_to_mysql($cotacao['data_retorno']);
            $data_cotacao_sv['qnt_dias'] = app_date_get_diff_dias($cotacao['data_saida'], $cotacao['data_retorno'], 'D') + 1;
            $data_cotacao_sv['num_passageiro'] = $num_passageiros[$index];
            $data_cotacao_sv['repasse_comissao'] = app_unformat_percent($comissao_repasse[$index]);
            $data_cotacao_sv['comissao_corretor'] = $configuracao['comissao'] - app_unformat_percent($comissao_repasse[$index]);
            $data_cotacao_sv['desconto_condicional'] = app_unformat_percent($desconto_condicional[$index]);
            $data_cotacao_sv['desconto_condicional_valor'] = $desconto_condicional_valor[$index];
            $data_cotacao_sv['premio_liquido'] = app_unformat_currency($valores[$index]);
            $data_cotacao_sv['premio_liquido_total'] = app_unformat_currency($valores_totais[$index]);
            $data_cotacao_sv['iof'] = app_unformat_percent($regra_preco[0]['parametros']);
            $cotacao_seguro_viagem_id = $this->cotacao_seguro_viagem->insert($data_cotacao_sv, TRUE);

            $cobertura_adicional = (!empty($carrossel['cobertura_adicional'])) ? explode(';', $carrossel['cobertura_adicional']) : array();
            $cobertura_adicional_valor = (!empty($carrossel['cobertura_adicional_valor'])) ? explode(';', $carrossel['cobertura_adicional_valor']) : array();

            if($cobertura_adicional){
                foreach ($cobertura_adicional as $index => $item) {

                    if(empty($item)) continue;

                    $dados_cobertura_adicional = array();
                    $dados_cobertura_adicional['cotacao_seguro_viagem_id'] = $cotacao_seguro_viagem_id;
                    $dados_cobertura_adicional['cobertura_plano_id'] = $item;
                    $dados_cobertura_adicional['valor'] = $cobertura_adicional_valor[$index];
                    $this->cotacao_seguro_viagem_cobertura->insert($dados_cobertura_adicional, TRUE);


                }
            }

        }

        return $cotacao_id;


    }

    /**
     * Realiza update do carrossel
     * @param $produto_parceiro_id
     * @param $cotacao_id
     * @param string $status
     * @return mixed
     */
    private function update_cotacao_carrossel($produto_parceiro_id, $cotacao_id, $status = '')
    {
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_codigo_model', 'cliente_codigo');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_codigo_model', 'cotacao_codigo');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('cotacao_seguro_viagem_cobertura_model', 'cotacao_seguro_viagem_cobertura');
        $this->load->model('cotacao_seguro_viagem_pessoa_model', 'cotacao_seguro_viagem_pessoa');
        $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('moeda_cambio_model', 'moeda_cambio');
        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');


        $produto_parceiro =  $this->current_model->get_by_id($produto_parceiro_id);
        $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $carrossel = $this->session->userdata("carrossel_{$produto_parceiro_id}");


        $cotacao_salva = $this->cotacao->with_cotacao_seguro_viagem()
            ->filterByID($cotacao_id)
            ->get_all();


        $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if(count($configuracao) > 0){
            $configuracao = $configuracao[0];
        }else{
            $configuracao = array();
        }

        if($produto_parceiro['parceiro_id'] != $this->session->userdata('parceiro_id')){

            $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $this->session->userdata('parceiro_id'));



            $configuracao['repasse_comissao'] = $rel['repasse_comissao'];
            $configuracao['repasse_maximo'] = $rel['repasse_maximo'];
            $configuracao['comissao'] = $rel['comissao'];

    
        }

        $regra_preco = $this->regra_preco->with_regra_preco()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        //salva cotacão
        $data_cotacao = array();
        $data_cotacao['usuario_cotacao_id'] = $this->session->userdata('usuario_id');
        $data_cotacao['parceiro_id'] = $this->session->userdata('parceiro_id');
        $data_cotacao['usuario_venda_id'] = 0;
        $data_cotacao['cotacao_status_id'] = 1;
        $data_cotacao['alteracao_usuario_id'] = 1;

        $this->cotacao->update($cotacao_id,  $data_cotacao, TRUE);

        foreach ($cotacao_salva as $index => $item)
        {
            $this->cotacao_seguro_viagem->delete($item['cotacao_seguro_viagem_id']);
            $this->cotacao_seguro_viagem_pessoa->delete_by('cotacao_seguro_viagem_id', $item['cotacao_seguro_viagem_id']);
            $this->cotacao_seguro_viagem_cobertura->delete_by('cotacao_seguro_viagem_id', $item['cotacao_seguro_viagem_id']);
        }



        $planos = explode(';', $carrossel['plano']);

        $valores = explode(';', $carrossel['valor']);
        $comissao_repasse = explode(';', $carrossel['comissao_repasse']);
        $desconto_condicional = explode(';', $carrossel['desconto_condicional']);
        $desconto_condicional_valor = explode(';', $carrossel['desconto_condicional_valor']);
        $valores_totais = explode(';', $carrossel['valor_total']);
        $num_passageiros = explode(';', $carrossel['num_passageiro']);



        foreach ($planos as $index => $plano)
        {
            if(isset($plano) && $plano > 0)
            {

                $plano_row = $this->produto_parceiro_plano->get($plano);
                $moeda_cambio = $this->moeda_cambio->getCotacaoDia($plano_row['moeda_id']);

                $data_cotacao_sv = array();
                $data_cotacao_sv['produto_parceiro_id'] = $produto_parceiro_id;
                $data_cotacao_sv['produto_parceiro_plano_id'] = $plano;
                $data_cotacao_sv['moeda_cambio_id'] = $moeda_cambio['moeda_cambio_id'];
                $data_cotacao_sv['step'] = 3;
                $data_cotacao_sv['cotacao_id'] = $cotacao_id;
                $data_cotacao_sv['seguro_viagem_motivo_id'] = $cotacao['seguro_viagem_motivo_id'];
                $data_cotacao_sv['email'] = $cotacao['email'];
                $data_cotacao_sv['origem_id'] = $cotacao['origem_id'];
                $data_cotacao_sv['destino_id'] = $cotacao['destino_id'];
                $data_cotacao_sv['telefone'] = $cotacao['telefone'];
                $data_cotacao_sv['data_saida'] = app_dateonly_mask_to_mysql($cotacao['data_saida']);
                $data_cotacao_sv['data_retorno'] = app_dateonly_mask_to_mysql($cotacao['data_retorno']);
                $data_cotacao_sv['qnt_dias'] = app_date_get_diff_dias($cotacao['data_saida'], $cotacao['data_retorno'], 'D') + 1;
                $data_cotacao_sv['num_passageiro'] = $num_passageiros[$index];
                $data_cotacao_sv['repasse_comissao'] = app_unformat_percent($comissao_repasse[$index]);
                $data_cotacao_sv['comissao_corretor'] = $configuracao['comissao'] - app_unformat_percent($comissao_repasse[$index]);
                $data_cotacao_sv['desconto_condicional'] = app_unformat_percent($desconto_condicional[$index]);
                $data_cotacao_sv['desconto_condicional_valor'] = app_unformat_percent($desconto_condicional_valor[$index]);
                $data_cotacao_sv['premio_liquido'] = app_unformat_currency($valores[$index]);
                $data_cotacao_sv['premio_liquido_total'] = app_unformat_currency($valores_totais[$index]);

                if(isset($regra_preco[0]))
                    $data_cotacao_sv['iof'] = app_unformat_percent($regra_preco[0]['parametros']);

                if($status == 'desconto_aprovado')
                    $data_cotacao_sv['desconto_cond_aprovado'] = 1;

                $cotacao_seguro_viagem_id  = $this->cotacao_seguro_viagem->insert($data_cotacao_sv, TRUE);

                $cobertura_adicional = (!empty($carrossel['cobertura_adicional'])) ? explode(';', $carrossel['cobertura_adicional']) : array();
                $cobertura_adicional_valor = (!empty($carrossel['cobertura_adicional_valor'])) ? explode(';', $carrossel['cobertura_adicional_valor']) : array();

                if($cobertura_adicional){
                    foreach ($cobertura_adicional as $index => $item) {

                        if(empty($item)) continue;

                        $dados_cobertura_adicional = array();
                        $dados_cobertura_adicional['cotacao_seguro_viagem_id'] = $cotacao_seguro_viagem_id;
                        $dados_cobertura_adicional['cobertura_plano_id'] = $item;
                        $dados_cobertura_adicional['valor'] = $cobertura_adicional_valor[$index];
                        $this->cotacao_seguro_viagem_cobertura->insert($dados_cobertura_adicional, TRUE);

                        
                    }
                }


            }

        }

        return $cotacao_id;

    }

    private function update_cotacao_formulario($produto_parceiro_id, $cotacao_id){


        $this->load->model('cliente_codigo_model', 'cliente_codigo');
        $this->load->model('cotacao_codigo_model', 'cotacao_codigo');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');

        $cotacao_salva = $this->cotacao->with_cotacao_seguro_viagem()
            ->filterByID($cotacao_id)
            ->get_all();

        $cliente_salva = $this->cliente->get($cotacao_salva[0]['cliente_id']);

        $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");


        $data_cliente = array();
        $data_cliente['razao_nome'] = $cotacao['nome'];
        $data_cliente['data_nascimento'] = app_dateonly_mask_to_mysql($cotacao['data_nascimento']);

        $this->cliente->update($cliente_salva['cliente_id'], $data_cliente, TRUE);


        if($cotacao_salva) {
            foreach ($cotacao_salva as $index => $item) {
                $data_cotacao = array();
                $data_cotacao['step'] = 2;
                $data_cotacao['origem_id'] = $cotacao['origem_id'];
                $data_cotacao['destino_id'] = $cotacao['destino_id'];
                $data_cotacao['seguro_viagem_motivo_id'] = $cotacao['seguro_viagem_motivo_id'];
                $data_cotacao['data_saida'] = app_dateonly_mask_to_mysql($cotacao['data_saida']);
                $data_cotacao['data_retorno'] = app_dateonly_mask_to_mysql($cotacao['data_retorno']);
                $data_cotacao['qnt_dias'] = app_date_get_diff_dias($cotacao['data_saida'], $cotacao['data_retorno'], 'D') + 1;


                if(isset($cotacao['estado_civil'])){
                    $data_cotacao['estado_civil'] = $cotacao['estado_civil'];
                }

                if(isset($cotacao['rg_orgao_expedidor'])){
                    $data_cotacao['rg_orgao_expedidor'] = $cotacao['rg_orgao_expedidor'];
                }

                if(isset($cotacao['rg_uf'])){
                    $data_cotacao['rg_uf'] = $cotacao['rg_uf'];
                }

                if(isset($cotacao['rg_data_expedicao'])){
                    $data_cotacao['rg_data_expedicao'] = app_dateonly_mask_to_mysql($cotacao['rg_data_expedicao']);
                }


                if(isset($cotacao['aux_01'])){
                    $data_cotacao['aux_01'] = $cotacao['aux_01'];
                }
                if(isset($cotacao['aux_02'])){
                    $data_cotacao['aux_02'] = $cotacao['aux_02'];
                }

                if(isset($cotacao['aux_03'])){
                    $data_cotacao['aux_03'] = $cotacao['aux_03'];
                }

                if(isset($cotacao['aux_04'])){
                    $data_cotacao['aux_04'] = $cotacao['aux_04'];
                }

                if(isset($cotacao['aux_05'])){
                    $data_cotacao['aux_05'] = $cotacao['aux_05'];
                }

                if(isset($cotacao['aux_06'])){
                    $data_cotacao['aux_06'] = $cotacao['aux_06'];
                }

                if(isset($cotacao['aux_07'])){
                    $data_cotacao['aux_07'] = $cotacao['aux_07'];
                }

                if(isset($cotacao['aux_08'])){
                    $data_cotacao['aux_08'] = $cotacao['aux_08'];
                }

                if(isset($cotacao['aux_09'])){
                    $data_cotacao['aux_09'] = $cotacao['aux_09'];
                }

                if(isset($cotacao['aux_10'])){
                    $data_cotacao['aux_10'] = $cotacao['aux_10'];
                }


                $this->cotacao_seguro_viagem->update($item['cotacao_seguro_viagem_id'], $data_cotacao, TRUE);

            }
        }else{
            $data_cotacao = array();
            $data_cotacao['step'] = 2;
            $data_cotacao['produto_parceiro_id'] = $produto_parceiro_id;
            $data_cotacao['cotacao_id'] = $cotacao_id;
            $data_cotacao['seguro_viagem_motivo_id'] = $cotacao['seguro_viagem_motivo_id'];
            $data_cotacao['produto_parceiro_plano_id'] = 0;
            $data_cotacao['email'] = $cotacao['email'];
            $data_cotacao['telefone'] = $cotacao['telefone'];
            $data_cotacao['origem_id'] = $cotacao['origem_id'];
            $data_cotacao['destino_id'] = $cotacao['destino_id'];
            $data_cotacao['data_saida'] = app_dateonly_mask_to_mysql($cotacao['data_saida']);
            $data_cotacao['data_retorno'] = app_dateonly_mask_to_mysql($cotacao['data_retorno']);
            $data_cotacao['qnt_dias'] = app_date_get_diff_dias($cotacao['data_saida'], $cotacao['data_retorno'], 'D') + 1;


            if(isset($cotacao['estado_civil'])){
                $data_cotacao['estado_civil'] = $cotacao['estado_civil'];
            }

            if(isset($cotacao['rg_orgao_expedidor'])){
                $data_cotacao['rg_orgao_expedidor'] = $cotacao['rg_orgao_expedidor'];
            }

            if(isset($cotacao['rg_uf'])){
                $data_cotacao['rg_uf'] = $cotacao['rg_uf'];
            }

            if(isset($cotacao['rg_data_expedicao'])){
                $data_cotacao['rg_data_expedicao'] = app_dateonly_mask_to_mysql($cotacao['rg_data_expedicao']);
            }


            if(isset($cotacao['aux_01'])){
                $data_cotacao['aux_01'] = $cotacao['aux_01'];
            }
            if(isset($cotacao['aux_02'])){
                $data_cotacao['aux_02'] = $cotacao['aux_02'];
            }

            if(isset($cotacao['aux_03'])){
                $data_cotacao['aux_03'] = $cotacao['aux_03'];
            }

            if(isset($cotacao['aux_04'])){
                $data_cotacao['aux_04'] = $cotacao['aux_04'];
            }

            if(isset($cotacao['aux_05'])){
                $data_cotacao['aux_05'] = $cotacao['aux_05'];
            }

            if(isset($cotacao['aux_06'])){
                $data_cotacao['aux_06'] = $cotacao['aux_06'];
            }

            if(isset($cotacao['aux_07'])){
                $data_cotacao['aux_07'] = $cotacao['aux_07'];
            }

            if(isset($cotacao['aux_08'])){
                $data_cotacao['aux_08'] = $cotacao['aux_08'];
            }

            if(isset($cotacao['aux_09'])){
                $data_cotacao['aux_09'] = $cotacao['aux_09'];
            }

            if(isset($cotacao['aux_10'])){
                $data_cotacao['aux_10'] = $cotacao['aux_10'];
            }


            $this->cotacao_seguro_viagem->insert($data_cotacao, TRUE);

        }


    }

    private function insert_cotacao_formulario($produto_parceiro_id){

        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_contato_model', 'cliente_contato');
        $this->load->model('cliente_codigo_model', 'cliente_codigo');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_codigo_model', 'cotacao_codigo');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');

        $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");

        //verifica se o cliente existe
        $cliente = $this->cliente->filterByCPFCNPJ(app_retorna_numeros($cotacao['cnpj_cpf']))
            ->get_all();


        $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        $configuracao = $configuracao[0];

        $regra_preco = $this->regra_preco->with_regra_preco()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        if(count($cliente) == 0){
            //insere novo cliente
            $data_cliente = array();
            $data_cliente['tipo_cliente'] = (app_verifica_cpf_cnpj(app_retorna_numeros($cotacao['cnpj_cpf'])) == 'CNPJ') ? 'CO' : 'CF';
            $data_cliente['cnpj_cpf'] = app_retorna_numeros($cotacao['cnpj_cpf']);
            $data_cliente['codigo'] = $this->cliente_codigo->get_codigo_cliente_formatado($data_cliente['tipo_cliente']);
            $data_cliente['colaborador_id'] = 1;
            $data_cliente['colaborador_comercial_id'] = 1;
            $data_cliente['titular'] = 1;
            $data_cliente['razao_nome'] = $cotacao['nome'];
            $data_cliente['data_nascimento'] = app_dateonly_mask_to_mysql($cotacao['data_nascimento']);
            $data_cliente['cliente_evolucao_status_id'] = 6; //salva como prospect
            $data_cliente['grupo_empresarial_id'] = 0;

            $cliente_id = $this->cliente->insert($data_cliente, TRUE);
            $cliente = $this->cliente->get($cliente_id);



            //Inseri Contato de cliente E-mail
            $data_contato = array();
            $data_contato['cliente_id'] = $cliente_id;
            $data_contato['cliente_contato_nivel_relacionamento_id'] = 3;
            $data_contato['decisor'] = 1;
            $data_contato['nome'] = $cotacao['nome'];
            $data_contato['contato'] = $cotacao['email'];
            $data_contato['contato_tipo_id'] = 1;
            $data_contato['data_nascimento'] =  app_dateonly_mask_to_mysql($cotacao['data_nascimento']);
            $this->cliente_contato->insert_contato($data_contato);

            //Celular
            $data_contato['contato'] = app_retorna_numeros($cotacao['telefone']);
            $data_contato['contato_tipo_id'] = 2;
            $this->cliente_contato->insert_contato($data_contato);


        }else{
            //
            $cliente = $cliente[0];
        }

        //salva cotacão
        $data_cotacao = array();
        $data_cotacao['cliente_id'] = $cliente['cliente_id'];
        $data_cotacao['codigo'] = $this->cotacao_codigo->get_codigo_cotacao_formatado('BE');
        $data_cotacao['cotacao_tipo'] = 'ONLINE';
        $data_cotacao['parceiro_id'] = $this->session->userdata('parceiro_id');
        $data_cotacao['usuario_cotacao_id'] = $this->session->userdata('usuario_id');
        $data_cotacao['usuario_venda_id'] = 0;
        $data_cotacao['cotacao_status_id'] = 5;
        $data_cotacao['produto_parceiro_id'] = $produto_parceiro_id;

        $cotacao_id = $this->cotacao->insert($data_cotacao, TRUE);

        return $cotacao_id;


    }

    public function limpa_cotacao($produto_parceiro_id){

        $this->session->unset_userdata("cotacao_{$produto_parceiro_id}");
        $this->session->unset_userdata("carrossel_{$produto_parceiro_id}");
        $this->session->unset_userdata("dados_segurado_{$produto_parceiro_id}");

    }

    public function validate_data_retorno_menor($date)
    {

        $date2 = $this->input->post('data_saida');

        $date_atual = mktime(0, 0, 0, substr($date2, 3, 2), substr($date2, 0, 2), substr($date2, 6, 4));
        $date= mktime(0, 0, 0, substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4));
        $this->form_validation->set_message('validate_data_retorno_menor', 'O Campo %s não não pode ser menor que campo Data Saída');
        if($date < $date_atual)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Seta sessão cotação
     * @param $cotacao_id
     * @param $produto_parceiro_id
     */
    private function set_cotacao_session($cotacao_id, $produto_parceiro_id, $status = '')
    {
        $this->load->model("Produto_Parceiro_Plano_Model","produto_parceiro_plano" );
        $this->load->model('cotacao_seguro_viagem_pessoa_model', 'pessoa');
        $this->load->model('cotacao_seguro_viagem_cobertura_model', 'cotacao_seguro_viagem_cobertura');

        $cotacao_salva = $this->cotacao
            ->with_cotacao_seguro_viagem()
            ->get($cotacao_id);



        if($cotacao_salva)
        {
            $cliente = $this->cliente->get($cotacao_salva['cliente_id']);


            $cotacao = array();
            $cotacao['produto_parceiro_id'] = $produto_parceiro_id;
            $cotacao['cnpj_cpf'] = (app_verifica_cpf_cnpj($cliente['cnpj_cpf']) == 'CPF') ? app_cpf_to_mask($cliente['cnpj_cpf']) : app_cnpj_to_mask($cliente['cnpj_cpf']);
            $cotacao['nome'] = $cliente['razao_nome'];
            $cotacao['email'] = $cotacao_salva['email'];
            $cotacao['telefone'] = $cotacao_salva['telefone'];
            $cotacao['data_nascimento'] = app_date_mysql_to_mask($cliente['data_nascimento'], 'd/m/Y');
            $cotacao['seguro_viagem_motivo_id'] = $cotacao_salva['seguro_viagem_motivo_id'];
            $cotacao['origem_id'] = $cotacao_salva['origem_id'];
            $cotacao['destino_id'] = $cotacao_salva['destino_id'];
            $cotacao['data_saida'] =  app_date_mysql_to_mask($cotacao_salva['data_saida'], 'd/m/Y');
            $cotacao['data_retorno'] = app_date_mysql_to_mask($cotacao_salva['data_retorno'], 'd/m/Y');

            $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $cotacao);
        }

/*
        if(!$this->session->userdata("carrossel_{$produto_parceiro_id}"))
        {*/
            $data = $this->seguro_viagem->get_many_by(array(
                'cotacao_id' => $cotacao_id,
            ));

            $carrossel = array();
            $carrossel['plano'] = "";
            $carrossel['plano_nome'] = "";
            $carrossel['valor'] = "";
            $carrossel['comissao_repasse'] = "";
            $carrossel['desconto_condicional'] = "";
            $carrossel['desconto_condicional_valor'] = "";
            $carrossel['valor_total'] = "";
            $carrossel['num_passageiro'] = "";
            $carrossel['cobertura_adicional'] = "";
            $carrossel['cobertura_adicional_valor'] = "";
            $carrossel['cobertura_adicional_valor_total'] = "";

            $carrossel['produto_parceiro_id'] = $produto_parceiro_id;

            foreach ($data as $d)
            {
                if((int)$d['produto_parceiro_plano_id'] > 0) {
                    $plano = $this->produto_parceiro_plano->get($d['produto_parceiro_plano_id']);

                    $carrossel['plano'] .= $d['produto_parceiro_plano_id'] . ';';
                    $carrossel['plano_nome'] .= $plano['nome'] . ';';
                    $carrossel['valor'] .= app_format_currency($d['premio_liquido_total'], false, 3) . ';';
                    $carrossel['comissao_repasse'] .= app_format_currency($d['repasse_comissao'], false, 2) . ';';
                    $carrossel['desconto_condicional'] .= app_format_currency($d['desconto_condicional'], false, 2) . ';';
                    $carrossel['desconto_condicional_valor'] .= $d['desconto_condicional_valor'] . ';';
                    $carrossel['valor_total'] .= app_format_currency($d['premio_liquido_total'], false, 3) . ';';
                    $carrossel['num_passageiro'] .= $d['num_passageiro'] . ';';

                    $cotacao_adicional = $this->cotacao_seguro_viagem_cobertura->get_many_by(array(
                        'cotacao_seguro_viagem_id' => $d['cotacao_seguro_viagem_id'],
                    ));

                    foreach ($cotacao_adicional as $ca) {
                        $carrossel['cobertura_adicional'] .= $ca['cobertura_plano_id'].";";
                        $carrossel['cobertura_adicional_valor'] .= $ca['valor'].";";
                        $carrossel['cobertura_adicional_valor_total'] += $ca['valor'];
                    }

                }
            }

            $keys = array_keys($carrossel);
            $i = 0;
            foreach($carrossel as $data)
            {
                $carrossel[$keys[$i]] = substr($data, 0, strlen($data) - 1);
                $i++;
            }

            if($carrossel)
            {
                $this->session->set_userdata("carrossel_{$produto_parceiro_id}", $carrossel);
            }




            if((int)$cotacao_salva['cotacao_upgrade_id'] > 0) {
                $cotacao_antiga = $this->cotacao
                    ->with_cotacao_seguro_viagem()
                    ->get($cotacao_salva['cotacao_upgrade_id']);

                $rows = $this->pessoa->get_many_by(array(
                    'cotacao_seguro_viagem_id' => $cotacao_antiga['cotacao_seguro_viagem_id'],
                ));
                $data_dados_segurado = array();
                $data_dados_segurado['produto_parceiro_id'] = $produto_parceiro_id;
                $data_dados_segurado['cotacao_id'] = $cotacao_id;
                $num_passageiro = 2;
                foreach ($rows as $row) {
                       if($row['contratante_passageiro'] == 'CONTRATANTE'){
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_nome"] = $row['nome'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_cpf"] = app_cpf_to_mask($row['cnpj_cpf']);
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_sexo"] = $row['sexo'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_data_nascimento"] = app_dateonly_mysql_to_mask($row['data_nascimento']);
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_contato_telefone"] = $row['contato_telefone'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_endereco_cep"] = $row['endereco_cep'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_endereco"] = $row['endereco'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_endereco_numero"] = $row['endereco_numero'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_endereco_complemento"] = $row['endereco_complemento'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_endereco_bairro"] = $row['endereco_bairro'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_endereco_cidade"] = $row['endereco_cidade'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_endereco_uf"] = $row['endereco_uf'];
                       }else{
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_{$num_passageiro}_passageiro_nome"] = $row['nome'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_{$num_passageiro}_sexo"] = $row['sexo'];
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_{$num_passageiro}_cpf"] = app_cpf_to_mask($row['cnpj_cpf']);
                           $data_dados_segurado["plano_{$cotacao_salva['produto_parceiro_plano_id']}_{$num_passageiro}_passageiro_data_nascimento"] = app_dateonly_mysql_to_mask($row['data_nascimento']);
                           $num_passageiro++;
                       }

                }
                $this->session->set_userdata("dados_segurado_{$produto_parceiro_id}", $data_dados_segurado);
            }


        //pega dados da pessoa
//            print_r($cotacao_salva);exit;
        /*
         *

Array
(
    [produto_parceiro_id] => 41
    [cotacao_id] => 248
    [plano_9_nome] => CARLOS DANILO QUINELATO
    [plano_9_cpf] => 305.566.468-00
    [plano_9_sexo] => M
    [plano_9_data_nascimento] => 14/09/1981
    [plano_9_contato_telefone] => (11)1111-11111
    [plano_9_endereco_cep] => 09403-150
    [plano_9_endereco] => RUA ATÁLAIA
    [plano_9_endereco_numero] => 9
    [plano_9_endereco_complemento] =>
    [plano_9_endereco_bairro] => VILA GOMES
    [plano_9_endereco_cidade] => RIBEIRÃO PIRES
    [plano_9_endereco_uf] => SP
    [plano_9_2_cpf] => 305.566.468-00
    [plano_9_2_passageiro_nome] => jonas
    [plano_9_2_sexo] => M
    [plano_9_2_passageiro_data_nascimento] => 15/06/2016
)

         *
            $data = $this->seguro_viagem->get_many_by(array(
                'cotacao_id' => $cotacao_id,
            ));*/
       // }


    }

    public function consulta_pagmax($pedido_id = 0){

        $this->load->model('pedido_model', 'pedido');
        $this->load->model('pedido_cartao_model', 'pedido_cartao');
        $pedidos = $this->pedido->getPedidoPagamentoPendenteDebito($pedido_id);


        foreach ($pedidos as $index => $pedido) {
            //verifica se exite cartão não processado
            try {


                $cartao = $this->pedido_cartao->get_cartao_debito_pendente($pedido['pedido_id']);

                if (count($cartao) > 0) {
                    $cartao = $cartao[0];

                    $this->pagmax_consultar_cartao_debito($cartao);
                }

            }catch(Exception $e){
            }
        }





    }


    private function pagmax_consultar_cartao_debito($dados){


        try {
            $this->load->model('fatura_model', 'fatura');
            $this->load->model('apolice_model', 'apolice');
            $this->load->model('pedido_model', 'pedido');
            $this->load->model('forma_pagamento_integracao_model', 'forma_pagamento_integracao');
            $this->load->model('pedido_transacao_model', 'pedido_transacao');
            $this->load->model('cotacao_model', 'cotacao');
            $this->load->model('pedido_cartao_transacao_model', 'pedido_cartao_transacao');

            $this->load->library("Nusoap_lib");


            $pedido = $this->pedido->get($dados['pedido_id']);



            $integracao = $this->forma_pagamento_integracao->get_by_slug('pagmax');



            $dados_transacao = array();
            $dados_transacao['pedido_cartao_id'] = $dados['pedido_cartao_id'];
            $dados_transacao['processado'] = 1;
            $dados_transacao['result'] = '';
            $dados_transacao['message'] = '';
            $dados_transacao['tid'] = '';
            $dados_transacao['status'] = '';


            $usesandbox = ($integracao['producao'] == 1) ? FALSE : TRUE;
            $tid = $dados['tid'];

            //Monta array com dados
            $param = array(
                'TID' => $tid,
                'MerchantAcquirerHashID' => $integracao['chave_acesso'],
                'UseSandbox' => $usesandbox,
            );
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }


        try{
            $client = new nusoap_client($integracao['url']);
            $client->setUseCurl(true);
            $response = $client->call('TIDinfo', $param);
            $response = json_decode($response);
            $dados_transacao['result'] = $response->result;
        }
        catch(Exception $e){
            $dados_transacao['result'] = 'ERRO';
            $dados_transacao['message'] = 'Erro Acessando modulo de pagamento';
            $dados_transacao['status'] = $e->getMessage();

        }



        $erro = false;
        try{

            if($dados_transacao['result'] == "OK"){
                $dados_transacao['result'] = 'OK';
                $dados_transacao['message'] = 'Transação Efetuada com Sucesso';
                $dados_transacao['tid'] = isset($response->TID) ? $response->TID : '';
                $dados_transacao['status'] = isset($response->Status) ? $response->Status : '';


                if($dados_transacao['status'] == 6)
                {

                    $this->fatura->pagamentoCompletoEfetuado($pedido['pedido_id']);
                    $this->apolice->insertAplice($pedido['pedido_id']);
                    $this->pedido_transacao->insStatus($pedido['pedido_id'], 'pagamento_confirmado', "Transação Efetuada com Sucesso [{$response->TID}]");

                    //Retorna pedido e cotação
                    $pedido = $this->pedido->get($pedido['pedido_id']);
                    $cotacao = $this->cotacao->get($pedido['cotacao_id']);

                    //Verifica se a cotação é um upgrade
                    if((int)$cotacao['cotacao_upgrade_id'] > 0)
                    {
                        //Seta cancelada e realiza update
                        $cotacao_antiga = $this->cotacao->get($cotacao['cotacao_upgrade_id']);
                        $pedido_antigo = $this->pedido->get_by(array('cotacao_id' => $cotacao['cotacao_upgrade_id']));

                        //Se achar
                        if($pedido_antigo)
                        {
                            //Muda status para cancelado
                            $pedido_antigo['pedido_status_id'] = 5;

                            //Realiza update
                            $this->pedido->update($pedido_antigo['pedido_id'], $pedido_antigo);
                            $this->pedido_transacao->insStatus($pedido_antigo['pedido_id'], 'cancelado', "PEDIDO CANCELADO PARA UPGRADE");




                            $faturas_pedido_antigo = $this->fatura->get_many_by(array("pedido_id" => $pedido_antigo['pedido_id']));

                            foreach($faturas_pedido_antigo as $fatura)
                            {
                                unset($fatura['fatura_id']);
                                $fatura['pedido_id'] = $pedido['pedido_id'];

                                $this->fatura->insert($fatura);
                            }

                            $this->pedido->executa_extorno_upgrade($pedido_antigo['pedido_id']);

                        }
                    }
                }



            }else{
                //log_message('error', 'pagmax: ' . print_r($response, true));


                $dados_transacao['result'] = isset($response->result) ? $response->result : '';
                $dados_transacao['message'] = isset($response->message) ? $response->message : '';
                $dados_transacao['status'] = (isset($response->Status)) ? $response->Status : '';

                $this->pedido_transacao->insStatus($pedido['pedido_id'], 'pagamento_negado', "Transação não Efetuada");
                log_message('debug', ' INSERE STATUS DO PEDIDO NEGADO');

                $erro = true;


            }

            if($erro)
            {
                $this->apolice->disparaEventoErroApolice($pedido['pedido_id']);
            }

            $update_transacao = array('processado' => 1);
            $this->pedido_cartao_transacao->update($dados['pedido_cartao_transacao_id'],  $update_transacao, TRUE);
            $this->pedido_cartao_transacao->insert($dados_transacao, TRUE);

        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }

    }


    public function validate_contato_salvar_cotacao($data, $j){

        $tipo = $this->input->post("contato_tipo_id")[$j];

        if($tipo){
            switch ((int)$tipo) {
                case 1:
                    $this->form_validation->set_message('validate_contato_salvar_cotacao', 'E-mail do campo Contato é inválido');
                    return $this->form_validation->valid_email($data);
                    break;
                case 2:
                    $this->form_validation->set_message('validate_contato_salvar_cotacao', 'Celular do campo Contato é inválido ');
                    $result = $this->form_validation->min_length(app_retorna_numeros($data), 10);
                    if($result){
                        return $this->form_validation->max_length(app_retorna_numeros($data), 11);
                    }else{
                        return false;
                    }
                    break;
                case 3:
                case 4:
                    $this->form_validation->set_message('validate_contato_salvar_cotacao', 'Telefone do campo Contato é inválido ');
                    return $this->form_validation->min_length(app_retorna_numeros($data), 10);
                    break;
                    break;
            }

        }else{
            $this->form_validation->set_message('validate_contato', 'O Campo tipo de contato é obrigátório');
        }

    }

}

