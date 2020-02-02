<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Characterize_Phrases
*
* @property Produto_Parceiro_Plano_Model $current_model
*
*/
class Venda_Seguro_Saude extends Admin_Controller 
{
    const PRECO_TIPO_TABELA = 1;
    const PRECO_TIPO_COBERTURA = 2;
    const PRECO_TIPO_VALOR_SEGURADO = 3;
    const PRECO_TIPO_FIXO_SERVICO = 4;

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
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
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

    /**
     * Página Inicial
     */
    public function index()
    {
        redirect("admin/venda/index");

    }

    public function continuar( $cotacao_id ){

        $this->load->model('cotacao_generico_model', 'cotacao_generico');

        //Carrega dados para a página
        $cotacao_generico = $this->cotacao->with_cotacao_generico()->filterByID($cotacao_id)->get_all();

        //Verifica se registro existe
        if(!$cotacao_generico) 
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("{$this->controller_uri}/index");
        }else{
            $cotacao_generico = $cotacao_generico[0];
        }


        redirect("admin/venda_seguro_saude/seguro_saude/{$cotacao_generico['produto_parceiro_id']}/{$cotacao_generico['step']}/$cotacao_id");

    }

    /**
     * Seguro Saude
     * @param $produto_parceiro_id
     * @param int $step
     * @param int $cotacao_id
     * @param int $pedido_id
     */
    public function seguro_saude($produto_parceiro_id, $step = 1, $cotacao_id = 0, $pedido_id = 0, $status = '') {
        //Carrega models
        $this->load->model("cotacao_generico", "cotacao_generico");
        $this->load->model("pedido_model", "pedido_model");
        $this->load->library('form_validation');

        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Venda');
        $this->template->set_breadcrumb('Venda', base_url("$this->controller_uri/index"));


        if($step == 1)
        {
            $this->seguro_saude_formulario($produto_parceiro_id, $cotacao_id);
        }
        elseif ($step == 2)
        {

            $this->seguro_saude_carrossel($produto_parceiro_id, $cotacao_id);
        }
        elseif ($step == 3)
        {

            //Verifica se possui desconto (vai para passo específico)
            if($this->cotacao_generico->verifica_possui_desconto($cotacao_id) && $status != "desconto_aprovado")
            {
                //Verifica se desconto foi aprovado
                if($this->cotacao_generico->verifica_desconto_aprovado($cotacao_id))
                {
                    //Carrega função para visualizar desconto
                    $this->seguro_saude_verificar_desconto($produto_parceiro_id, $cotacao_id);
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
                $this->seguro_saude_finalizar( $produto_parceiro_id, $cotacao_id, $status );
            }

        } elseif ($step == 4)
        {
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
                redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/5/{$pedido['pedido_id']}");
            }
            else
            {

                $this->set_carrossel_session($cotacao_id, $produto_parceiro_id);
                $this->venda_pagamento($produto_parceiro_id, $cotacao_id, $pedido_id);
            }

        } elseif ($step == 5) {
            $this->venda_aguardando_pagamento($produto_parceiro_id, $cotacao_id);
        } elseif ($step == 6) {
            $this->seguro_saude_certificado($produto_parceiro_id, $cotacao_id);
        } elseif ($step == 7) {
            $this->seguro_saude_add_carrinho($produto_parceiro_id, $cotacao_id, $pedido_id);
        } elseif ($step == 8) {
            $this->seguro_saude_salvar_cotacao( $produto_parceiro_id, $cotacao_id, $pedido_id );
        }

    }

    /**
     * Página que verifica desconto
     * @param $produto_parceiro_id
     * @param $cotacao_id
     */
    public function seguro_saude_verificar_desconto($produto_parceiro_id, $cotacao_id)
    {
        //Carrega models necessários
        $this->load->model('cotacao_model', 'cotacao');

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->cotacao->get($cotacao_id);
        $data['cotacao_id'] = $cotacao_id;
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['primary_key'] = $this->cotacao->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/view/{$cotacao_id}");

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');

            //Redireciona para index
            redirect("{$this->controller_uri}/index");
        }

        if($_POST)
        {
            //Se for setado para finalizar com desconto aprovado
            if($this->input->post("finalizar_desconto_aprovado"))
            {
                redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/3/{$cotacao_id}/0/desconto_aprovado");
            }

        }

        //Carrega template
        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/seguro_saude/verificar_desconto", $data );
    }

    /**
     * Formulário seguro Saude
     * @param $produto_parceiro_id
     * @param int $cotacao_id
     */
    public function seguro_saude_formulario($produto_parceiro_id, $cotacao_id = 0)
    {
        //Carrega models necessários
        $this->load->model('produto_parceiro_campo_model', 'campo');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('localidade_estado_model', 'localidade_estado');
        $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'plano_preco');

        //Adiciona bibliotecas necessárias
        $this->template->css(app_assets_url('modulos/venda/seguro_saude/css/select2.css', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_saude/js/base.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_saude/js/formulario.js', 'admin'));

        //Dados para template
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data["slug"] = "cotacao";

        //Verifica cotação
        if($cotacao_id > 0)
        {
            if($this->cotacao->isCotacaoValida($cotacao_id) == FALSE)
            {
                $this->session->set_flashdata('fail_msg', 'Essa Cotação não é válida');
                redirect("{$this->controller_uri}/index");
            }else{
                $this->set_cotacao_session($cotacao_id, $produto_parceiro_id);
            }
        }

        //Carrega dados
        $campos_session = $this->session->userdata("cotacao_{$produto_parceiro_id}");

        if(isset($campos_session) && is_array($campos_session)){
            $data['row'] = $campos_session;
        }else{
            $data['row'] = array();
        }

        //Campos para formulário
        $Campos = $this->campo->getCamposProduto($data['produto_parceiro_id'], $data['slug']);
        $this->token = $Campos['token'];
        $data["campos"] = $Campos['campos'];
        $data['cotacao_id'] = $cotacao_id;
        $data['list'] = array();
        $data['list']['rg_uf'] = $this->localidade_estado->order_by('nome')->get_all();
        $data['list']['faixa_etaria'] = $this->plano_preco->get_all_faixa_etaria_by_produto($produto_parceiro_id, $cotacao_id)->get_all(0, 0, false);

        if($_POST)
        {

            $validacao = $this->campo->setValidacoesCampos($produto_parceiro_id, 'cotacao');
            $this->cotacao->setValidate($validacao);

            //Verifica válido form
            if ($this->cotacao->validate_form('cotacao'))
            {
                $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $_POST);
                $cotacao_id = $this->input->post('cotacao_id');

                $cotacao_id = $this->cotacao_generico->insert_update($produto_parceiro_id, $cotacao_id);
                redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/2/{$cotacao_id}");
            }
        }


        $this->template->load("admin/layouts/{$this->layout}", "admin/venda/seguro_saude/formulario", $data );

    }

    /**
     * Passo 7
     * @param $produto_parceiro_id
     * @param int $cotacao_id
     */
    public function seguro_saude_salvar_cotacao( $produto_parceiro_id, $cotacao_id = 0 )
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
        $this->load->model('contato_tipo_model', 'contato_tipo');

        //Carrega JS
        $this->template->js(app_assets_url('modulos/venda/seguro_saude/js/base.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_saude/js/salvar_cotacao.js', 'admin'));
        $this->template->css(app_assets_url('modulos/venda/seguro_saude/css/base.css', 'admin'));

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
        if($_POST) {

            $validacao[] = array(
                'field' => "cliente_terceiro[0]",
                'label' => "Cliente / terceiro",
                'rules' => 'trim|required',
                'groups' => 'salvar_cotacao'
            );

            $contato_tipo = $this->input->post("cliente_terceiro");
            if(isset($contato_tipo[0]) && $contato_tipo[0] == 1)
            {
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
            $qnt_contato = $this->input->post('quantidade_contatos');

            for ($i = 0; $i < $qnt_contato; $i++) {
                if($this->input->post('contato_tipo_id')[$i]){
                    $validacao[] = array(
                        'field' => "contato[$i]",
                        'label' => "Tipo de Contato",
                        'rules' => "callback_validate_contato_salvar_cotacao[$i]",
                        'groups' => 'salvar_cotacao'
                    );
                }
            }

            $this->cotacao->setValidate($validacao);

            //Verifica válido form
            if ($this->cotacao->validate_form('salvar_cotacao'))
            {
                if($cotacao_id > 0){
                    $this->cotacao_generico->insert_update($produto_parceiro_id, $cotacao_id);
                }else{
                    $cotacao_id = $this->cotacao_generico->insert_update($produto_parceiro_id);
                }

                $this->salvar_cotacao_campos_adicionais($cotacao_id);
                $cotacao = $this->cotacao->get($cotacao_id);
                $this->session->set_flashdata('succ_msg', 'Cotação salva com sucesso, código: '. $cotacao['codigo']); //Mensagem de sucesso
                $this->limpa_cotacao($produto_parceiro_id);
                redirect("{$this->controller_uri}/index");
            }

        }

        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['cotacao'] = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $data['carrossel'] = $this->session->userdata("carrossel_{$produto_parceiro_id}");
        $this->template->load("admin/layouts/{$this->layout}", "admin/venda/seguro_saude/salvar_cotacao", $data );

    }

    public function seguro_saude_finalizar( $produto_parceiro_id, $cotacao_id = 0, $status = "" )
    {
        //Carrega models necessários
        $this->load->model('produto_parceiro_campo_model', 'campo');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_cobertura_model', 'cotacao_cobertura');
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('localidade_estado_model', 'localidade_estado');
        $this->load->model('capitalizacao_model', 'capitalizacao');
        $this->load->model('cotacao_saude_faixa_etaria_model', 'faixa_etaria');

        //Carrega JS para template
        $this->template->css(app_assets_url('modulos/venda/seguro_saude/css/select2.css', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_saude/js/dados_busca_cep.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_saude/js/dados_segurado.js', 'admin'));

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
                $this->cotacao_generico->insert_update($produto_parceiro_id, $cotacao_id, 3);
            }else{
                redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/2/{$cotacao_id}");
            }
        }else{
            if($valido) {
                $cotacao_id = $this->cotacao_generico->insert_update($produto_parceiro_id, $cotacao_id, 3);
            }else{
                redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/2/{$cotacao_id}");
            }
        }

        $cotacao_salva = $this->cotacao->with_cotacao_generico()
            ->filterByID($cotacao_id)
            ->get_all();

        $cotacao_salva = $cotacao_salva[0];

        if($cotacao_salva['desconto_condicional'] > 0 && $status != "desconto_aprovado")
        {
            $data_cotacao = array();
            $data_cotacao['desconto_cond_enviar'] = 1;
            $this->cotacao_generico->update($data_cotacao['cotacao_generico_id'],  $data_cotacao, TRUE);
            $this->cotacao->update($data_cotacao['cotacao_id'],array('cotacao_status_id' => 4), TRUE);
            $this->session->set_flashdata('succ_msg', 'Você optou pelo desconto condicional, esse desconto precisa de autorização, salvamos sua cotação com o código: '. $cotacao_salva['codigo'] . ' Aguarde a aprovação'); //Mensagem de sucesso
            $this->limpa_cotacao($produto_parceiro_id);
            redirect("{$this->controller_uri}/index");
        }

        // valida Capitalização
        $capitalizacao = $this->capitalizacao->validaNumeroSorte($cotacao_id);
        if ( empty($capitalizacao['status']) ) {
            $this->session->set_flashdata('fail_msg', $capitalizacao["message"]);
            redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/3/{$cotacao_id}");
        }

        $data = array();
        $data['cotacao_id'] = $cotacao_id;
        $data['campos'] = $this->campo->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug('dados_segurado')
            ->order_by("ordem", "asc")
            ->get_all();

        // Campos para formulário - Dependentes
        $data["campos_dependente"] = $this->campo->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug('dados_dependente')
            ->order_by("ordem", "asc")
            ->get_all();

        $data['cotacao'] = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $data['carrossel'] = $this->session->userdata("carrossel_{$produto_parceiro_id}");
        $data['carrossel']['num_dependente'] = $this->faixa_etaria->get_qtde_beneficiarios($cotacao_id);

        if(isset($cotacao_salva['cotacao_upgrade_id']) && (int)$cotacao_salva['cotacao_upgrade_id'] > 0){
            $this->set_cotacao_session($cotacao_id, $produto_parceiro_id);
        }
        $dados_segurado =  $this->session->userdata("dados_segurado_{$produto_parceiro_id}");
        $data['row'] = (isset($dados_segurado) && is_array($dados_segurado) && count($dados_segurado) > 0) ? $dados_segurado : array();
        $data['list'] = array();
        $data['list']['rg_uf'] = $this->localidade_estado->order_by('nome')->get_all();

        if($_POST)
        {
            $planos = explode(';', $data['carrossel']['plano']);
            $planos_nome = explode(';', $data['carrossel']['plano_nome']);
            $plano_dependente = explode(';', $data['carrossel']['num_dependente']);
            $validacao = $this->campo->setValidacoesCamposPlano($produto_parceiro_id, 'dados_segurado', $data['carrossel']['plano']);
            $validacao_dep = $this->campo->setValidacoesCamposAdicionais($produto_parceiro_id, 'dados_dependente', $data['carrossel']['plano'], $plano_dependente, 'Beneficiário' );
            $validacao = array_merge($validacao, $validacao_dep);

            $this->cotacao->setValidate($validacao);
            if ($this->cotacao->validate_form('dados_segurado'))
            {

                foreach ($planos as $index => $plano)
                {

                    //busca cotação do cotacao_seguro_viagem
                    $cotacao_salva = $this->cotacao->with_cotacao_generico()
                        ->filterByID($cotacao_id)
                        ->get_all();

                    $cotacao_salva = $cotacao_salva[0];
                    $dados_cotacao = array();
                    $dados_cotacao['step'] = 4;

                    $this->campo->setDadosCampos($produto_parceiro_id, 'generico', 'dados_segurado', $plano, $dados_cotacao);
                    $this->cotacao_generico->update($cotacao_salva['cotacao_generico_id'], $dados_cotacao, TRUE);

                    // Se tem alguma beneficiário
                    if( !empty($plano_dependente[0]) && $plano_dependente[0] > 1)
                    {
                        $dadosTitular = $this->cotacao_generico->filterByCotacao($cotacao_id)->filterByTipoSegurado('T')->get_all();
                        $dadosTitular = emptyor($dadosTitular[0], []);

                        $this->cotacao_generico->remove_beneficiarios($cotacao_id);

                        // Remove campos que não precisam enviar para os demais
                        unset($dadosTitular['cotacao_generico_id']);
                        unset($dadosTitular['criacao']);
                        unset($dadosTitular['alteracao']);
                        unset($dadosTitular['alteracao_usuario_id']);
                        unset($dadosTitular['deletado']);

                        // echo "DADOS TITULAR<br>";
                        // print_pre($dadosTitular, false);

                        for ($cont = 2; $cont <= $plano_dependente[0]; $cont++ )
                        {
                            $dadosTitular['tipo_segurado'] = 'D';
                            $this->campo->setDadosCampos($produto_parceiro_id, 'generico', 'dados_dependente', $plano, $dadosTitular, $cont);
                            $this->cotacao_generico->insert($dadosTitular, TRUE);
                            // echo "DADOS<br>";
                            // print_pre($dadosTitular, true);
                        }
                    }

                    $coberturas = $this->cotacao_cobertura->geraCotacaoCobertura($cotacao_id, $produto_parceiro_id, $cotacao_salva['produto_parceiro_plano_id'], $cotacao_salva["nota_fiscal_valor"], $cotacao_salva['premio_liquido']);

                    if($this->input->post("plano_{$plano}_password")){
                        $dados_cotacao['password'] = $this->input->post("plano_{$plano}_password");
                    }
                    $this->cliente->atualizar($cotacao_salva['cliente_id'], $dados_cotacao);

                }

                $this->session->set_userdata("dados_segurado_{$produto_parceiro_id}", $_POST);
                redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/4/{$cotacao_id}");

            }
        }

        $this->template->load("admin/layouts/{$this->layout}", "admin/venda/seguro_saude/dados_segurado", $data );

    }

    public function seguro_saude_add_carrinho($produto_parceiro_id, $cotacao_id, $pedido_id = 0)
    {
        if($pedido_id == 0) {
            $pedido_id = $this->insertPedidoCarrinho($cotacao_id);
        }else{
            $this->updatePedidoCarrinho($pedido_id, $cotacao_id);
        }

        $this->session->set_flashdata('succ_msg', 'Pedido Adicionado no carrinho com sucesso'); //Mensagem de sucesso
        redirect("{$this->controller_uri}/index");
    }

    /**
     * Mostra o Certificado gerado
     * @param $apolice_id
     * @param string $export
     */
    public function certificado($apolice_id, $export = ''){

        $this->load->model('apolice_model', 'apolice');

        $result = $this->apolice->certificado($apolice_id, $export);
        if($result !== FALSE){
            exit($result);
        }

    }

    /**
     * Passo 2
     * @param $produto_parceiro_id
     * @param int $cotacao_id
     */
    public function seguro_saude_carrossel($produto_parceiro_id, $cotacao_id = 0)
    {
        //Carrega models necessários
        $this->load->model('produto_parceiro_campo_model', 'campo');
        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('cobertura_plano_model', 'plano_cobertura');
        $this->load->model('cobertura_model', 'cobertura');
        $this->load->model('cotacao_generico_cobertura_model', 'cotacao_generico_cobertura');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_cobertura_model', 'cotacao_cobertura');
        $this->load->model('produto_parceiro_desconto_model', 'desconto');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
        $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
        $this->load->model('contato_tipo_model', 'contato_tipo');
        $this->load->model('comunicacao_track_model', 'comunicacao_track');

        //Carrega JS
        $this->template->js(app_assets_url('modulos/venda/seguro_saude/js/base.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_saude/js/carrossel.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/seguro_saude/js/calculo.js', 'admin'));
        $this->template->css(app_assets_url('modulos/venda/seguro_saude/css/carrossel.css', 'admin'));
        $this->template->css(app_assets_url('modulos/venda/seguro_saude/css/base.css', 'admin'));

        if($cotacao_id > 0){
            $this->comunicacao_track->insert_or_update($cotacao_id);
        }

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

        $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if(count($configuracao) > 0){
            $data['configuracao'] = $configuracao[0];
        }else{
            $data['configuracao'] = array();
        }

        if($data['row']['parceiro_id'] != $this->session->userdata('parceiro_id')){

            $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $this->session->userdata('parceiro_id'));

            if( isset( $rel['repasse_comissao'] ) && isset( $rel['comissao'] ) && isset($rel['repasse_maximo']) ) {
                $data['configuracao']['repasse_comissao'] = $rel['repasse_comissao'];
                $data['configuracao']['repasse_maximo'] = $rel['repasse_maximo'];
                $data['configuracao']['comissao'] = $rel['comissao'];
            }

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
        $cotacao_antiga = $this->cotacao->with_cotacao_generico()->get($cotacao_atual['cotacao_upgrade_id']);

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

            $cotacao_salva = $this->cotacao->with_cotacao_generico()->filterByID($cotacao_id)->get_all();

            if($cotacao_salva){

                $cotacao_salva = $cotacao_salva[0];

                $data['cotacao_codigo'] = $cotacao_salva['codigo'];
                $data['carrossel']['repasse_comissao'] = $cotacao_salva['repasse_comissao'];
                $data['carrossel']['desconto_condicional'] = $cotacao_salva['desconto_condicional'];
                $data['carrossel']['desconto_condicional_valor'] = $cotacao_salva['desconto_condicional_valor'];
                $data['carrossel']['quantidade'] = $cotacao_salva['quantidade'];

                $data['carrinho'] = array();
                $data['carrinho_hidden']['plano'] = array();
                $data['carrinho_hidden']['plano_nome'] = array();
                $data['carrinho_hidden']['quantidade'] = array();
                $data['carrinho_hidden']['valor'] = array();
                $data['carrinho_hidden']['comissao_repasse'] = array();
                $data['carrinho_hidden']['desconto_condicional'] = array();
                $data['carrinho_hidden']['desconto_condicional_valor'] = array();
                $data['carrinho_hidden']['valor_total'] = array();
                $data['carrinho_hidden']['num_passageiro'] = array();
                $data['carrinho_hidden']['cobertura_adicional'] = "";
                $data['carrinho_hidden']['cobertura_adicional_valor'] = "";
                $data['carrinho_hidden']['cobertura_adicional_valor_total'] = "";


                if((int)$cotacao_salva['produto_parceiro_plano_id'] > 0) {

                    $plano_salvo = $this->plano->get($cotacao_salva['produto_parceiro_plano_id']);

                    if (($cotacao_antiga && isset($cotacao_antiga['produto_parceiro_plano_id']) && $cotacao_antiga['produto_parceiro_plano_id'] != $cotacao_salva['produto_parceiro_plano_id']) || (!$cotacao_antiga)) 
                    {
                        $data['carrinho_hidden']['plano'][] = $plano_salvo['produto_parceiro_plano_id'];
                        $data['carrinho_hidden']['plano_nome'][] = $plano_salvo['nome'];
                        $data['carrinho_hidden']['quantidade'][] = $cotacao_salva['quantidade'];
                        $data['carrinho_hidden']['valor'][] = app_format_currency($cotacao_salva['premio_liquido'], false, 3);
                        $data['carrinho_hidden']['comissao_repasse'][] = app_format_currency($cotacao_salva['repasse_comissao'], false, 3);
                        $data['carrinho_hidden']['desconto_condicional'][] = app_format_currency($cotacao_salva['desconto_condicional'], false, 3);
                        $data['carrinho_hidden']['desconto_condicional_valor'][] = $cotacao_salva['desconto_condicional_valor'];
                        $data['carrinho_hidden']['valor_total'][] = app_format_currency($cotacao_salva['premio_liquido_total'], false, 3);

                        $cotacao_adicional = $this->cotacao_generico_cobertura->get_many_by(array(
                            'cotacao_generico_id' => $cotacao_salva['cotacao_generico_id'],
                            'deletado' => 0,
                        ));

                        foreach ($cotacao_adicional as $ca) {
                            $data['carrinho_hidden']['cobertura_adicional'] .= $ca['cobertura_plano_id'].";";
                            $data['carrinho_hidden']['cobertura_adicional_valor'] .= $ca['valor'].";";
                            $data['carrinho_hidden']['cobertura_adicional_valor_total'] += $ca['valor'];
                        }

                        $data['carrinho'][] = array(
                            'item' => 1,
                            'quantidade' => $cotacao_salva['quantidade'],
                            'plano_id' => $plano_salvo['produto_parceiro_plano_id'],
                            'plano' => $plano_salvo['nome'],
                            'valor' => app_format_currency($cotacao_salva['premio_liquido_total'], FALSE, 2)
                        );
                    }
                }

                $data['carrinho_hidden']['plano'] = implode(';', $data['carrinho_hidden']['plano']);
                $data['carrinho_hidden']['quantidade'] = implode(';', $data['carrinho_hidden']['quantidade']);
                $data['carrinho_hidden']['plano_nome'] = implode(';', $data['carrinho_hidden']['plano_nome']);
                $data['carrinho_hidden']['valor'] = implode(';', $data['carrinho_hidden']['valor']);
                $data['carrinho_hidden']['comissao_repasse'] = implode(';', $data['carrinho_hidden']['comissao_repasse']);
                $data['carrinho_hidden']['desconto_condicional'] = implode(';', $data['carrinho_hidden']['desconto_condicional']);
                $data['carrinho_hidden']['desconto_condicional_valor'] = implode(';', $data['carrinho_hidden']['desconto_condicional_valor']);
                $data['carrinho_hidden']['valor_total'] = implode(';', $data['carrinho_hidden']['valor_total']);
                $data['carrinho_hidden']['num_passageiro'] = implode(';', $data['carrinho_hidden']['num_passageiro']);

                $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");
            } else {

                $cotacao_salva = $this->cotacao->get($cotacao_id);
                $data['cotacao_codigo'] = $cotacao_salva['codigo'];

                $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");

                $data['carrinho'] = array();
            }
        } else {
            $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");
            $data['carrinho'] = array();
        }

        $data['regra_preco'] = $this->regra_preco->with_regra_preco()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();


        /**
         * Busca Planos e coberturas
         */
        $data['coberturas'] = $this->cobertura->getCoberturasProdutoParceiroPlano($produto_parceiro_id);

        if(isset($cotacao_antiga) && $cotacao_antiga)
        {
            $cotacao_antiga = $this->cotacao->with_cotacao_generico()->get($cotacao_antiga['cotacao_id']);

            if($data['row']['venda_agrupada']) {
                $arrPlanos = $this->plano
                    ->distinct()
                    ->order_by('produto_parceiro_plano.ordem', 'asc')
                    ->with_produto_parceiro()
                    ->with_produto()
                    ->where("produto_parceiro_plano.produto_parceiro_plano_id", "!=", $cotacao_antiga['produto_parceiro_plano_id']);

                if((isset($cotacao['faixa_salarial_id'])) && ($cotacao['faixa_salarial_id'])){
                    $arrPlanos->with_faixa_salarial($cotacao['faixa_salarial_id']);
                }

                $arrPlanos = $arrPlanos
                    ->get_many_by(array(
                        'produto_parceiro.venda_agrupada' => 1,
                    ));
            }
            else
            {
                $arrPlanos = $this->plano
                ->order_by('produto_parceiro_plano.ordem', 'asc')
                ->distinct()
                ->where("produto_parceiro_plano.produto_parceiro_plano_id", "!=", $cotacao_antiga['produto_parceiro_plano_id']);

                if((isset($cotacao['faixa_salarial_id'])) && ($cotacao['faixa_salarial_id'])){
                    $arrPlanos->with_faixa_salarial($cotacao['faixa_salarial_id']);
                }

                $arrPlanos = $arrPlanos
                ->get_many_by(array(
                    'produto_parceiro_id' => $produto_parceiro_id
                ));
            }
        }
        else
        {
            if($data['row']['venda_agrupada'])
            {
                $arrPlanos = $this->plano
                ->distinct()
                ->order_by('produto_parceiro_plano.ordem', 'asc')
                ->with_produto_parceiro()
                ->with_produto();

                if((isset($cotacao['faixa_salarial_id'])) && ($cotacao['faixa_salarial_id'])){
                    $arrPlanos->with_faixa_salarial($cotacao['faixa_salarial_id']);
                }

                $arrPlanos = $arrPlanos
                ->get_many_by(array(
                    'produto_parceiro.venda_agrupada' => 1,
                    'produto_parceiro.deletado' => 0
                ));

            }else{
                $arrPlanos = $this->plano
                    ->order_by('produto_parceiro_plano.ordem', 'asc')
                    ->distinct();

                if((isset($cotacao['faixa_salarial_id'])) && ($cotacao['faixa_salarial_id'])){
                    $arrPlanos->with_faixa_salarial($cotacao['faixa_salarial_id']);
                }

                $arrPlanos = $arrPlanos
                ->get_many_by(array(
                    'produto_parceiro_id' => $produto_parceiro_id
                ));
            }
        }

        $fail_msg = '';

        if(!$arrPlanos)
        {
            $this->session->set_flashdata('fail_msg', 'Não existem planos Configurados.');

            redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/1");
        }

        $data['planos'] = array();
        foreach ($arrPlanos as $plano)
        {
            $arrCoberturas = $this->plano_cobertura->filter_by_produto_parceiro_plano($plano['produto_parceiro_plano_id'])->get_all();
            foreach ($arrCoberturas as $idx => $cob) {
                $arrCoberturas[$idx]['preco'] = app_format_currency($arrCoberturas[$idx]['preco'], true, 3);
                $arrCoberturas[$idx]['porcentagem'] = app_format_currency($arrCoberturas[$idx]['porcentagem'], false, 5) . ' %';
            }
            $plano['cobertura'] = $arrCoberturas;
            $data['planos'][] = $plano;
        }

        $data['list'] = array();

        if($_POST)
        {
            $post_plano = $this->input->post('plano');
            if(empty($post_plano)){
                $this->session->set_flashdata('fail_msg', 'O Carrinho esta vazio, adicione um plano');
                if ($cotacao_id > 0) {
                    redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/2/{$cotacao_id}");
                } else {
                    redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/2");
                }
            }else{

                if ($this->cotacao->validate_form('carrossel'))
                {
                    $this->session->set_userdata("carrossel_{$produto_parceiro_id}", $_POST);
                    $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $cotacao);

                    if($this->input->post('salvar_cotacao') == 1){

                        $this->cotacao_generico->insert_update($produto_parceiro_id, $cotacao_id, 2);

                        //Merge dos dados para comunicação evento
                        $dados_comunicacao = array_merge($_POST, $cotacao);

                        //Carrega biblioteca e dispara evento para salvar cotação
                        $this->load->library("Comunicacao");
                        $comunicacao = new Comunicacao();
                        $comunicacao->setDestinatario($cotacao['email']);
                        $comunicacao->setNomeDestinatario($cotacao['nome']);
                        $comunicacao->setMensagemParametros($dados_comunicacao);
                        $comunicacao->disparaEvento("cotacao_salva", $cotacao['produto_parceiro_id']);

                        if($data['configuracao']['salvar_cotacao_formulario'] == 1){
                            redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/8/{$cotacao_id}");
                        }else{
                            $cotacao = $this->cotacao->get($cotacao_id);
                            $this->session->set_flashdata('succ_msg', 'Cotação salva com sucesso, código: '. $cotacao['codigo']); //Mensagem de sucesso
                            $this->limpa_cotacao($produto_parceiro_id);
                            redirect("{$this->controller_uri}/index");

                        }
                    }else{
                        if ($cotacao_id > 0)
                        {
                            $this->cotacao_generico->insert_update($produto_parceiro_id, $cotacao_id, 2);
                            $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");

                            $cotacao["nota_fiscal_valor"] = app_unformat_currency($cotacao["nota_fiscal_valor"]);
                            $_POST['valor'] = app_unformat_currency($_POST['valor']);
                            $coberturas = $this->cotacao_cobertura->geraCotacaoCobertura($cotacao_id, $produto_parceiro_id, $_POST['produto_parceiro_plano_id'], $cotacao["nota_fiscal_valor"], $_POST['valor']);

                            if ($this->cotacao_generico->verifica_possui_desconto($cotacao_id)) {
                                $this->cotacao_generico->insert_update($produto_parceiro_id, $cotacao_id, 2);
                                $update_cotacao = array();
                                $update_cotacao['cotacao_status_id'] = 4;
                                $this->cotacao->update($cotacao_id, $update_cotacao, TRUE);
                                $this->session->set_flashdata('succ_msg', 'Você optou pelo desconto condicional, esse desconto precisa de autorização, salvamos sua cotação com o código: ' . $cotacao_salva[0]['codigo'] . ' Aguarde a aprovação'); //Mensagem de sucesso
                                $this->limpa_cotacao($produto_parceiro_id);
                                redirect("{$this->controller_uri}/index");
                            }

                            redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/3/{$cotacao_id}");
                        } else {
                            redirect("{$this->controller_uri}/seguro_saude/{$produto_parceiro_id}/3");
                        }
                    }

                }

            }

        }

        $view = "admin/venda/seguro_saude/{$this->layout}/carrossel";
        if(!view_exists($view))
            $view = "admin/venda/seguro_saude/carrossel";

        $this->template->load("admin/layouts/{$this->layout}", $view, $data );

    }

    private function insertPedidoCarrinho($cotacao_id)
    {
        //Carrega models
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('pedido_codigo_model', 'pedido_codigo');
        $this->load->model('pedido_cartao_model', 'pedido_cartao');
        $this->load->model('pedido_transacao_model', 'pedido_transacao');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_model', 'cotacao');

        $cotacao = $this->cotacao->get($cotacao_id);
        $valor_total = $this->cotacao_generico->getValorTotal($cotacao_id);

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
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_model', 'cotacao');

        $valor_total = $this->cotacao_generico->getValorTotal($cotacao_id);

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
    public function calculo() {

        $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');

        $_POST['data_base'] = date('Y-m-d'); 
        $result = $this->regra_preco->calculo_plano();

        ob_clean();

        //Output
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($result));
    }

    private function salvar_cotacao_campos_adicionais($cotacao_id)
    {

        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cliente_contato_model', 'cliente_contato');

        $cotacao = $this->cotacao->get($cotacao_id);

        //insere os contatos
        $qnt_contato = $this->input->post('quantidade_contatos');

        for ($i = 0; $i < $qnt_contato; $i++) {

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
                $this->cliente_contato->insert_not_exist_contato($data_contato);
            }
        }

        $data_cotacao = array();
        $data_cotacao['motivo'] = $this->input->post('salvar_motivo');
        $data_cotacao['motivo_ativo'] = $this->input->post('motivo_ativo');
        $data_cotacao['motivo_obs'] = $this->input->post('motivo_obs');

        $this->cotacao->update($cotacao_id,  $data_cotacao, TRUE);
    }

    public function limpa_cotacao($produto_parceiro_id)
    {
        $this->session->unset_userdata("cotacao_{$produto_parceiro_id}");
        $this->session->unset_userdata("carrossel_{$produto_parceiro_id}");
        $this->session->unset_userdata("dados_segurado_{$produto_parceiro_id}");
    }

    /**
     * Seta sessão cotação
     * @param $cotacao_id
     * @param $produto_parceiro_id
     */
    private function set_cotacao_session($cotacao_id, $produto_parceiro_id, $status = '')
    {
        $this->load->model("Produto_Parceiro_Plano_Model","produto_parceiro_plano" );
        $this->load->model('cotacao_generico_model', 'cotacao_generico_model');

        $cotacao_salva = $this->cotacao
            ->with_cotacao_generico()
            ->filterByID($cotacao_id)->get_all();

        if($cotacao_salva)
        {
            $cotacao_salva = $cotacao_salva[0];

            $cotacao = array();
            $cotacao['produto_parceiro_id'] = $produto_parceiro_id;
            $cotacao['cnpj_cpf'] = (app_verifica_cpf_cnpj($cotacao_salva['cnpj_cpf']) == 'CPF') ? app_cpf_to_mask($cotacao_salva['cnpj_cpf']) : app_cnpj_to_mask($cotacao_salva['cnpj_cpf']);
            $cotacao['rg'] = $cotacao_salva['rg'];
            $cotacao['nome'] = $cotacao_salva['nome'];
            $cotacao['nome_mae'] = $cotacao_salva['nome_mae'];
            $cotacao['email'] = $cotacao_salva['email'];
            $cotacao['telefone'] = $cotacao_salva['telefone'];
            $cotacao['data_nascimento'] = app_date_mysql_to_mask($cotacao_salva['data_nascimento'], 'd/m/Y');
            $cotacao['endereco_cep'] = $cotacao_salva['endereco_cep'];
            $cotacao['endereco_logradouro'] = $cotacao_salva['endereco_logradouro'];
            $cotacao['endereco_numero'] = $cotacao_salva['endereco_numero'];
            $cotacao['endereco_complemento'] = $cotacao_salva['endereco_complemento'];
            $cotacao['endereco_bairro'] = $cotacao_salva['endereco_bairro'];
            $cotacao['endereco_cidade'] = $cotacao_salva['endereco_cidade'];
            $cotacao['endereco_estado'] = $cotacao_salva['endereco_estado'];
            $cotacao['repasse_comissao'] = app_format_currency($cotacao_salva['repasse_comissao']);
            $cotacao['desconto_condicional'] = app_format_currency($cotacao_salva['desconto_condicional']);
            $cotacao['desconto_condicional_valor'] = app_format_currency($cotacao_salva['desconto_condicional_valor']);
            $cotacao['premio_liquido'] = app_format_currency($cotacao_salva['premio_liquido']);
            $cotacao['iof'] = app_format_currency($cotacao_salva['iof']);
            $cotacao['premio_liquido_total'] = app_format_currency($cotacao_salva['premio_liquido_total']);
            $cotacao['faixa_salarial_id'] = $cotacao_salva['faixa_salarial_id'];
            $cotacao['quantidade'] = $cotacao_salva['quantidade'];
            $cotacao['sexo'] = $cotacao_salva['sexo'];
            $cotacao['estado_civil'] = $cotacao_salva['estado_civil'];
            $cotacao['rg_orgao_expedidor'] = $cotacao_salva['rg_orgao_expedidor'];
            $cotacao['rg_uf'] = $cotacao_salva['rg_uf'];
            $cotacao['rg_data_expedicao'] = app_date_mysql_to_mask($cotacao_salva['rg_data_expedicao']);
            $cotacao['aux_01'] = $cotacao_salva['aux_01'];
            $cotacao['aux_02'] = $cotacao_salva['aux_02'];
            $cotacao['aux_03'] = $cotacao_salva['aux_03'];
            $cotacao['aux_04'] = $cotacao_salva['aux_04'];
            $cotacao['aux_05'] = $cotacao_salva['aux_05'];
            $cotacao['aux_06'] = $cotacao_salva['aux_06'];
            $cotacao['aux_07'] = $cotacao_salva['aux_07'];
            $cotacao['aux_08'] = $cotacao_salva['aux_08'];
            $cotacao['aux_09'] = $cotacao_salva['aux_09'];
            $cotacao['aux_10'] = $cotacao_salva['aux_10'];
            $cotacao['data_inicio_vigencia'] = app_date_mysql_to_mask(issetor($cotacao_salva['data_inicio_vigencia'], null));
            $cotacao['data_fim_vigencia'] = app_date_mysql_to_mask(issetor($cotacao_salva['data_fim_vigencia'], null));

            $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $cotacao);
        }
    }

    public function consulta_pagmax($pedido_id = 0)
    {
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('pedido_cartao_model', 'pedido_cartao');
        $pedidos = $this->pedido->getPedidoPagamentoPendenteDebito($pedido_id);

        foreach ($pedidos as $index => $pedido)
        {
            //verifica se exite cartão não processado
            try {
                $cartao = $this->pedido_cartao->get_cartao_debito_pendente($pedido['pedido_id']);

                if (count($cartao) > 0) {
                    $cartao = $cartao[0];
                    $this->pagmax_consultar_cartao_debito($cartao);
                }

            }
            catch(Exception $e){ }
        }
    }

    private function pagmax_consultar_cartao_debito($dados)
    {

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
                    $this->apolice->insertSeguroGenerico($pedido['pedido_id']);
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
            }
            else
            {
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

    /**
     * Exibe o certificado após a compra
     * @param $produto_parceiro_id
     * @param int $pedido_id
     */
    public function seguro_saude_certificado($produto_parceiro_id, $pedido_id = 0)
    {
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
        $this->template->load("admin/layouts/{$this->layout}", "admin/venda/seguro_saude/certificado", $data );

    }

}
