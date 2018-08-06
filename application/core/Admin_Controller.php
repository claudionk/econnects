<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends MY_Controller
{
    protected $noLogin = false;
    protected $_theme = 'theme-1';
    protected $_theme_logo = '';
    protected $_theme_nome = 'Connects Insurance';
    protected $layout = "base";

    protected $controller_name;
    protected $controller_uri;

    const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
    const FORMA_PAGAMENTO_FATURADO = 9;
    const FORMA_PAGAMENTO_CARTAO_DEBITO = 7;
    const FORMA_PAGAMENTO_BOLETO = 8;
    const FORMA_PAGAMENTO_TRANSF_BRADESCO = 5;
    const FORMA_PAGAMENTO_TRANSF_BB = 6;

    function __construct()
    {
        parent::__construct();

        $this->load->model("usuario_model", "usuario");

        $this->output->set_header('Expires: Sat, 01 Jan 2000 00:00:01 GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0, max-age=0');
        $this->output->set_header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        $this->output->set_header('Pragma: no-cache');

        $this->_theme_logo = app_assets_url('template/img/logo-connects.png', 'admin');
        $this->controller_name = strtolower(get_class($this) );
        $this->controller_uri = "admin/{$this->controller_name}";

        $userdata = $this->session->all_userdata();

        if(isset($userdata['parceiro_id']))
        {
            $this->_setTheme($userdata['parceiro_id']);
        }

        $this->template->set('theme', $this->_theme);
        $this->template->set('theme_logo', $this->_theme_logo);
        $this->template->set('title', $this->_theme_nome);
        $this->template->set_breadcrumb('Home', base_url('admin/home'));
        $this->template->set('current_controller_name', $this->controller_name );
        $this->template->set('current_controller_uri', $this->controller_uri);
        $this->template->set('userdata', $userdata);

        //Seta layout
        $layout = ($this->session->userdata("layout")) ? $this->session->userdata("layout") : 'base';
        if($this->input->get('layout'))
        {
            $this->session->set_userdata("layout", $this->input->get('layout'));
            $layout = $this->input->get('layout');
        }
        //Seta layout
        if($this->input->get('context'))
        {
            $this->session->set_userdata("context", $this->input->get('context'));
        }

        $this->template->set('context', $this->session->userdata("context"));

        $this->template->set('layout', $layout);
        $urls_pode_acessar = $this->session->userdata("urls_pode_acessar");

        if(($this->router->fetch_class() !== 'login') && (!$this->auth->is_admin())  &&  ($this->noLogin === false) )
        {
            $url_redirect = '';
            $this->load->library('user_agent');
            if ($this->agent->is_referral())
            {
               $url_redirect = urlencode($this->agent->referrer());
            }

            $redirect = "admin/login/index/?redirect={$url_redirect}";
            $this->load->helper('cookie');
            $login_parceiro_id = get_cookie('login_parceiro_id');


            if($login_parceiro_id)
            {
                $this->load->model('parceiro_model', 'parceiro');
                $parceiro = $this->parceiro->get($login_parceiro_id);
                if($parceiro) {
                    $redirect = "parceiro/{$parceiro['slug']}?redirect={$url_redirect}";
                }
            }

            $token = $this->input->get("token");


            if(!$token)
            {
                redirect($redirect);
            }
            else
            {


                if(!$this->usuario->login_token($token))
                {
                    echo "Token inválido.";
                    exit;
                }
                else
                {
                    if(!empty($urls_pode_acessar))
                    {
                        $this->session->set_userdata("urls_pode_acessar", $urls_pode_acessar);
                    }


                    redirect(current_url() . '?' . $_SERVER['QUERY_STRING']);
                }
            }
        }
        else if ($this->router->fetch_class() !== 'login')
        {

           if(isset($userdata['termo_aceite']) && $userdata['termo_aceite'] == 0)
           {
               $this->template->js(app_assets_url('core/js/termo.js', 'admin'));
           }
           if(isset($userdata['termo_aceite']) && $userdata['termo_aceite'] == 0){
               $this->template->js(app_assets_url('core/js/termo.js', 'admin'));
           }

           if(($urls_pode_acessar) && (!empty($urls_pode_acessar)) && (is_array($urls_pode_acessar)) &&  ($this->noLogin === false)){
                $pode_acessar = false;
                foreach($urls_pode_acessar as $url)
                {
                    $url_relativa = explode("#", $url);
                    $url_relativa = $url_relativa[0];

                    if(strpos(current_url(), $url_relativa) !== false)
                    {

                        $pode_acessar = true;
                    }
                }

                if(!$pode_acessar && !in_array(current_url(), $urls_pode_acessar))
                {
                    echo "Você não possui autorização para ver esta página.";
                    exit;
                }
            }

            if(isset($userdata['termo_aceite']) && $userdata['termo_aceite'] == 0)
            {
                $this->template->js(app_assets_url('core/js/termo.js', 'admin'));
            }
        }

        //Verifica permissão

        /*
        if(!$this->auth->checar_permissoes_pagina_atual() && !$this->noLogin)
        {

            $this->session->set_flashdata('fail_msg', 'Você não têm permissão para acessar esta página.');

            redirect("admin/home");

        }*/


    }

    public function _setTheme($parceiro_id){

        $this->load->model('parceiro_model', 'parceiro');
        $parceiro = $this->parceiro->get($parceiro_id);
        $this->_theme = (!empty($parceiro['theme'])) ? $parceiro['theme'] : 'theme-1';
        $this->_theme_logo =  (!empty($parceiro['logo'])) ? app_assets_url("upload/parceiros/{$parceiro['logo']}", 'admin') : app_assets_url('template/img/logo-connects.png', 'admin');
        $this->_theme_nome =  (!empty($parceiro['apelido'])) ? $parceiro['apelido']  :  $this->_theme_nome;
        $this->template->set('theme', $this->_theme);
        $this->template->set('theme_logo', $this->_theme_logo);
        $this->template->set('title', $this->_theme_nome);
    }


    public function venda_pagamento( $produto_parceiro_id, $cotacao_id, $pedido_id = 0 ) {
        $pedido_id = (int)$pedido_id;

        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('forma_pagamento_tipo_model', 'forma_pagamento_tipo');
        $this->load->model('forma_pagamento_bandeira_model', 'forma_pagamento_bandeira');
        $this->load->model('forma_pagamento_model', 'forma_pagamento');
        $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        $this->load->model('pedido_model', 'pedido');

      
        //Carrega templates
        $this->template->js(app_assets_url('core/js/jquery.card.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/pagamento/js/pagamento.js', 'admin'));

        //Retorna cotação
        $cotacao = $this->cotacao->get_cotacao_produto($cotacao_id);
        error_log( "Produto: " . print_r( $cotacao['produto_slug'], true ) . "\n", 3, "/var/log/nginx/php_errors.log" );
        switch ($cotacao['produto_slug']) {
            case "seguro_viagem":
                $valor_total = $this->cotacao_seguro_viagem->getValorTotal($cotacao_id);
                break;
            case "equipamento":
                $valor_total = $this->cotacao_equipamento->getValorTotal($cotacao_id);
                break;
            case "generico":
                $valor_total = $this->cotacao_generico->getValorTotal($cotacao_id);
                break;
            case "seguro_saude":
                $valor_total = $this->cotacao_generico->getValorTotal($cotacao_id);
                break;
            
        }
        error_log( "Valor Total: " . print_r( $valor_total, true ) . "\n", 3, "/var/log/nginx/php_errors.log" );

        //formas de pagamento
        $forma_pagamento = array();
        $tipo_pagamento = $this->forma_pagamento_tipo->get_all();

        //Para cada tipo de pagamento
        foreach ($tipo_pagamento as $index => $tipo) {
            $forma = $this->produto_pagamento->with_forma_pagamento()
                ->filter_by_produto_parceiro($produto_parceiro_id)
                ->filter_by_forma_pagamento_tipo($tipo['forma_pagamento_tipo_id'])
                ->filter_by_ativo()
                ->get_all();

            $bandeiras = $this->forma_pagamento_bandeira->get_many_by( array( "forma_pagamento_tipo_id" =>  $tipo["forma_pagamento_tipo_id"] ) );


            if( count($forma) > 0 ) {
                foreach ($forma as $index => $item) {
                    $parcelamento = array();
                    for($i = 1; $i <= $item['parcelamento_maximo'];$i++){
                        if($i <= $item['parcelamento_maximo_sem_juros']) {
                            $parcelamento[$i] = "{$i} X ". app_format_currency($valor_total/$i) . " sem juros";
                        }else{
                            //$valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
                            $valor = ($valor_total/(1-($item['juros_parcela']/100)))/$i;
                            $parcelamento[$i] = "{$i} X ". app_format_currency($valor) . " com juros (". app_format_currency($item['juros_parcela']) ."%)";
                        }
                    }
                    $forma[$index]['parcelamento'] = $parcelamento;
                }

                $forma_pagamento[] = array('tipo' => $tipo, 'pagamento' => $forma, 'bandeiras' => $bandeiras);
            }

        }


        $data = array();
        $data['cotacao_id'] = $cotacao_id;
        $data['pedido_id'] = $pedido_id;
        $data['forma_pagamento'] = $forma_pagamento;
        $data['produto_slug'] = $cotacao['produto_slug'];
        $data['cotacao'] = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $data['carrossel'] = $this->session->userdata("carrossel_{$produto_parceiro_id}");
        $data['dados_segurado'] = $this->session->userdata("dados_segurado_{$produto_parceiro_id}");
        $data['produto_parceiro_configuracao'] = $this->produto_parceiro_configuracao->get_by(array(
            'produto_parceiro_id' => $produto_parceiro_id
        ));
        $data['url_pagamento_confirmado'] = "{$this->controller_uri}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/5/";
        $data['produto_parceiro_id'] = $produto_parceiro_id;

        $data['url_acesso_externo'] = $this->auth->generate_page_token('', array(
            current_url(),
            base_url("{$this->controller_uri}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/5/#"),
            base_url("admin/gateway/consulta"),
        ),
            'front',
            'pagamento'
        );

//        print_r($data['carrossel']);
//        print_r($data['dados_segurado']);
//        print_r($data['cotacao']);exit;


//        print_r($data);exit;
//,

        /*
        if(isset($data['dados_segurado']) && isset($data['cotacao'])){
            $arrDados = array_merge($data['dados_segurado'], $data['cotacao'] );
        }elseif(isset($data['dados_segurado'])){
            $arrDados = isset($data['dados_segurado']);

        }elseif(isset($data['dados_segurado'])){
            $arrDados = isset($data['dados_segurado']);

        }elseif(isset($data['cotacao'])){
            $arrDados = $data['cotacao'];
        }else{
            $arrDados = array();
        }

        $data['campos'] = $this->getDataCamposBoleto($arrDados,  $data['carrossel']['plano']); */

        if($_POST){

          error_log( print_r( $_POST, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
          
            $tipo_forma_pagamento_id = $this->input->post('forma_pagamento_tipo_id');


            $validacoes = array();


            /**
             * Cartão de crédito
             */
            if($tipo_forma_pagamento_id == self::FORMA_PAGAMENTO_CARTAO_CREDITO){
                //cartão de crédito
                $validacao[] = array(
                    'field' => "numero",
                    'label' => "Número do Cartão",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "nome_cartao",
                    'label' => "Nome",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "validade",
                    'label' => "Validade",
                    'rules' => "trim|required|valid_vencimento_cartao" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "codigo",
                    'label' => "Código",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
                $validacao[] = array(
                    'field' => "bandeira_cartao",
                    'label' => "Bandeira",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                if(($data['produto_parceiro_configuracao']['pagamento_tipo'] == "RECORRENTE") && ($data['produto_parceiro_configuracao']['pagmaneto_cobranca'] == 'VENCIMENTO_CARTAO '))
                {
                    $validacao[] = array(
                        'field' => "dia_vencimento",
                        'label' => "Dia do vencimento",
                        'rules' => "trim|numeric|required" ,
                        'groups' => 'pagamento'
                    );
                }
            }else if ($tipo_forma_pagamento_id == self::FORMA_PAGAMENTO_CARTAO_DEBITO){
                //cartão de débito

                $validacao[] = array(
                    'field' => "numero_debito",
                    'label' => "Número do Cartão",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "nome_cartao_debito",
                    'label' => "Nome",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "validade_debito",
                    'label' => "Validade",
                    'rules' => "trim|required|valid_vencimento_cartao" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "codigo_debito",
                    'label' => "Código",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
                $validacao[] = array(
                    'field' => "bandeira_cartao_debito",
                    'label' => "Bandeira",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

            }else if ($tipo_forma_pagamento_id == Self::FORMA_PAGAMENTO_FATURADO){
                //faturado
                $validacao[] = array();
            }else if ($tipo_forma_pagamento_id == Self::FORMA_PAGAMENTO_BOLETO){
                //faturado
                $validacao[] = array(
                    'field' => "sacado_nome",
                    'label' => "Nome",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
                $validacao[] = array(
                    'field' => "sacado_documento",
                    'label' => "CPF",
                    'rules' => "trim|required|validate_cpf" ,
                    'groups' => 'pagamento'
                );
                $validacao[] = array(
                    'field' => "sacado_endereco",
                    'label' => "Endereço",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
                $validacao[] = array(
                    'field' => "sacado_endereco_num",
                    'label' => "Número",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
                $validacao[] = array(
                    'field' => "sacado_endereco_cep",
                    'label' => "CEP",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
                $validacao[] = array(
                    'field' => "sacado_endereco_bairro",
                    'label' => "Bairro",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
                $validacao[] = array(
                    'field' => "sacado_endereco_cidade",
                    'label' => "Cidade",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
                $validacao[] = array(
                    'field' => "sacado_endereco_uf",
                    'label' => "Estado",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
            }

            $this->cotacao->setValidate($validacao);

            if ($this->cotacao->validate_form('pagamento'))
            {

                if($pedido_id == 0)
                {
                    $pedido_id = $this->pedido->insertPedido($_POST);
                }
                else
                {
                    $this->pedido->updatePedido($pedido_id, $_POST);
                    $this->pedido->insDadosPagamento($_POST, $pedido_id);
                }

                //Se for faturamento, muda status para aguardando faturamento
                if($pedido_id && $tipo_forma_pagamento_id == self::FORMA_PAGAMENTO_FATURADO)
                {
                    $status = $this->pedido->mudaStatus($pedido_id, "aguardando_faturamento");
                }

                $this->session->set_flashdata('succ_msg', 'Pedido incluido com sucesso!'); //Mensagem de sucesso
                if(($this->input->post('forma_pagamento_tipo_id') == self::FORMA_PAGAMENTO_CARTAO_CREDITO) || ($this->input->post('forma_pagamento_tipo_id') == self::FORMA_PAGAMENTO_CARTAO_DEBITO)){
                    $this->session->set_flashdata('succ_msg', 'Aguardando confirmação do pagamento'); //Mensagem de sucesso
                    redirect("{$this->controller_uri}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/5/{$pedido_id}");
                }elseif ($this->input->post('forma_pagamento_tipo_id') == self::FORMA_PAGAMENTO_BOLETO){
                    $this->session->set_flashdata('succ_msg', 'Aguardando confirmação do pagamento'); //Mensagem de sucesso
                    redirect("{$this->controller_uri}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/5/{$pedido_id}");
                }else{
                    redirect("{$this->controller_uri}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/6/{$pedido_id}");
                }

            }


        }



        $this->template->load("admin/layouts/{$this->layout}", "admin/venda/pagamento", $data );

    }

    public function venda_aguardando_pagamento($produto_parceiro_id, $pedido_id = 0){


        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');


        $this->template->js(app_assets_url('modulos/venda/pagamento/js/aguardando_pagamento.js', 'admin'));


        $pedido = $this->pedido->with_pedido_status()->filter_by_pedido($pedido_id)->get_all();
        $pedido = $pedido[0];

        //Retorna cotação
        $cotacao = $this->cotacao->get_cotacao_produto($pedido['cotacao_id']);


        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['pedido_id'] = $pedido_id;
        $data['produto_slug'] = $cotacao['produto_slug'];
        $data['pedido'] = $pedido;

        if($this->input->get('retorno') == 'pagmax'){
            $this->consulta_pagmax($pedido_id);
        }


        $this->template->load("admin/layouts/{$this->layout}", "admin/venda/aguardando_pagamento", $data );

    }


    public function pagamento_carrinho($novo = 0){

        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('forma_pagamento_tipo_model', 'forma_pagamento_tipo');
        $this->load->model('forma_pagamento_model', 'forma_pagamento');
        $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        $this->load->model('forma_pagamento_bandeira_model', 'forma_pagamento_bandeira');
        $this->load->model('pedido_model', 'pedido');

        $this->load->library('form_validation');



        $pedidos = ($novo == 0) ? $this->pedido->getPedidoCarrinho($this->session->userdata('usuario_id')) : $this->session->userdata('pedido_carrinho');

        if(!$pedidos){
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Carrinho esta vazio.');

            //Redireciona para index
            redirect("$this->controller_uri/index");
        }




        //valor total
        $valor_total = 0;
        foreach ($pedidos as $index => $pedido) {
            $valor_total += $pedido['valor_total'];
        }



        //Carrega templates
        $this->template->js(app_assets_url('core/js/jquery.card.js', 'admin'));
        $this->template->js(app_assets_url('modulos/venda/pagamento_carrinho/js/pagamento.js', 'admin'));


        //formas de pagamento
        $forma_pagamento = array();
        $tipo_pagamento = $this->forma_pagamento_tipo->get_all();

        //Para cada tipo de pagamento
        foreach ($tipo_pagamento as $index => $tipo)
        {
            $forma = $this->produto_pagamento->with_forma_pagamento()
                ->filter_by_produto_parceiro($pedidos[0]['produto_parceiro_id'])
                ->filter_by_forma_pagamento_tipo($tipo['forma_pagamento_tipo_id'])
                ->filter_by_ativo()
                ->get_all();

            $bandeiras = $this->forma_pagamento_bandeira
                ->get_many_by(array(
                    'forma_pagamento_tipo_id' =>  $tipo['forma_pagamento_tipo_id']
                ));


            if(count($forma) > 0){

                foreach ($forma as $index => $item) {
                    $parcelamento = array();
                    for($i = 1; $i <= $item['parcelamento_maximo'];$i++){
                        if($i <= $item['parcelamento_maximo_sem_juros']) {
                            $parcelamento[$i] = "{$i} X ". app_format_currency($valor_total/$i) . " sem juros";
                        }else{
                            //$valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
                            $valor = ($valor_total/(1-($item['juros_parcela']/100)))/$i;
                            $parcelamento[$i] = "{$i} X ". app_format_currency($valor) . " com juros (". app_format_currency($item['juros_parcela']) ."%)";
                        }
                    }
                    $forma[$index]['parcelamento'] = $parcelamento;
                }

                $forma_pagamento[] = array('tipo' => $tipo, 'pagamento' => $forma, 'bandeiras' => $bandeiras);
            }

        }


        $data = array();
        $data['pedidos'] = $pedidos;
        $data['forma_pagamento'] = $forma_pagamento;

        if($_POST){


            $tipo_forma_pagamento_id = $this->input->post('forma_pagamento_tipo_id');


            $validacoes = array();
            if($tipo_forma_pagamento_id == 1){
                //cartão de crédito
                $validacao[] = array(
                    'field' => "numero",
                    'label' => "Número do Cartão",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "nome_cartao",
                    'label' => "Nome",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "validade",
                    'label' => "Validade",
                    'rules' => "trim|required|valid_vencimento_cartao" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "codigo",
                    'label' => "Código",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "bandeira_cartao",
                    'label' => "Bandeira",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

            }elseif ($tipo_forma_pagamento_id == 6){
                //cartão de débito

                $validacao[] = array(
                    'field' => "numero_debito",
                    'label' => "Número do Cartão",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "nome_cartao_debito",
                    'label' => "Nome",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "validade_debito",
                    'label' => "Validade",
                    'rules' => "trim|required|valid_vencimento_cartao" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "codigo_debito",
                    'label' => "Código",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );

                $validacao[] = array(
                    'field' => "bandeira_cartao_debito",
                    'label' => "Bandeira",
                    'rules' => "trim|required" ,
                    'groups' => 'pagamento'
                );
            }

            $this->cotacao->setValidate($validacao);
            if ($this->cotacao->validate_form('pagamento')){
                //print_r($_POST);exit;


                foreach ($pedidos as $index => $pedido) {
                    $dados = $_POST;
                    $dados['cotacao_id'] = $pedido['cotacao_id'];
                    $this->pedido->updatePedido($pedido['pedido_id'], $dados);
                    $this->pedido->insDadosPagamento($dados, $pedido['pedido_id']);
                }

                $this->session->set_userdata("pedido_carrinho", $pedidos);
                if(($this->input->post('forma_pagamento_tipo_id') == 1) || ($this->input->post('forma_pagamento_tipo_id') == 6)){
                    $this->session->set_flashdata('succ_msg', 'Aguardando confirmação do pagamento'); //Mensagem de sucesso

                    redirect("{$this->controller_uri}/pagamento_carrinho_aguardando/");


                }else{
                    $this->session->set_flashdata('succ_msg', 'Pedido efetuado com sucesso!'); //Mensagem de sucesso
                    redirect("{$this->controller_uri}/index");
                }

                /*
                $this->session->set_flashdata('succ_msg', 'Pedido incluido com sucesso!'); //Mensagem de sucesso
                if(($this->input->post('forma_pagamento_tipo_id') == 1) || ($this->input->post('forma_pagamento_tipo_id') == 6)){
                    $this->session->set_flashdata('succ_msg', 'Aguardando confirmação do pagamento'); //Mensagem de sucesso
                    redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/5/{$pedido_id}");
                }else{
                    redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/6/{$pedido_id}");
                }

                */

            }


        }


            $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/pagamento_carrinho/pagamento", $data );








    }

    public function getDataCamposBoleto($data_campo, $plano_id = 0){

        $data = array();
        if(isset($data_campo['nome'])){
            $data['sacado_nome'] = $data_campo['nome'];
        }elseif (isset($data_campo["plano_{$plano_id}_nome"])){
            $data['sacado_nome'] = $data_campo["plano_{$plano_id}_nome"];
        }

        if(isset($data_campo['cnpj_cpf'])){
            $data['sacado_documento'] = $data_campo['cnpj_cpf'];
        }elseif (isset($data_campo["plano_{$plano_id}_cpf"])){
            $data['sacado_cpf'] = $data_campo["plano_{$plano_id}_cnpj_cpf"];
        }

        if(isset($data_campo['endereco_cep'])){
            $data['sacado_endereco_cep'] = $data_campo['endereco_cep'];
        }elseif (isset($data_campo["plano_{$plano_id}_endereco_cep"])){
            $data['sacado_endereco_cep'] = $data_campo["plano_{$plano_id}_endereco_cep"];
        }
        if(isset($data_campo['endereco'])){
            $data['sacado_endereco'] = $data_campo['endereco'];
        }elseif (isset($data_campo["plano_{$plano_id}_endereco"])){
            $data['sacado_endereco'] = $data_campo["plano_{$plano_id}_endereco"];
        }

        if(isset($data_campo['endereco_numero'])){
            $data['sacado_endereco_num'] = $data_campo['endereco_numero'];
        }elseif (isset($data_campo["plano_{$plano_id}_endereco_numero"])){
            $data['sacado_endereco_num'] = $data_campo["plano_{$plano_id}_endereco_numero"];
        }
        if(isset($data_campo['endereco_complemento'])){
            $data['sacado_endereco_comp'] = $data_campo['endereco_complemento'];
        }elseif (isset($data_campo["plano_{$plano_id}_endereco_complemento"])){
            $data['sacado_endereco_comp'] = $data_campo["plano_{$plano_id}_endereco_complemento"];
        }
        if(isset($data_campo['endereco_bairro'])){
            $data['sacado_endereco_bairro'] = $data_campo['endereco_bairro'];
        }elseif (isset($data_campo["plano_{$plano_id}_endereco_bairro"])){
            $data['sacado_endereco_bairro'] = $data_campo["plano_{$plano_id}_endereco_bairro"];
        }
        if(isset($data_campo['endereco_cidade'])){
            $data['sacado_endereco_cidade'] = $data_campo['endereco_cidade'];
        }elseif (isset($data_campo["plano_{$plano_id}_endereco_cidade"])){
            $data['sacado_endereco_cidade'] = $data_campo["plano_{$plano_id}_endereco_cidade"];
        }
        if(isset($data_campo['endereco_estado'])){
            $data['sacado_endereco_uf'] = $data_campo['endereco_estado'];
        }elseif (isset($data_campo["plano_{$plano_id}_endereco_estado"])){
            $data['sacado_endereco_uf'] = $data_campo["plano_{$plano_id}_endereco_estado"];
        }

        return $data;
    }

    public function pagamento_carrinho_aguardando()
    {

        $this->load->model('pedido_model', 'pedido');
        $this->load->library('form_validation');

        $this->template->js(app_assets_url('modulos/venda/pagamento_carrinho/js/aguardando_pagamento.js', 'admin'));

        $data = array();


        $pedidos = $this->session->userdata('pedido_carrinho');
        $ids = array();
        foreach ($pedidos as $pedido) {
            $ids[] = $pedido['pedido_id'];
        }

        $data['pedidos'] = $this->pedido->getPedidosByID($ids);


        //print_r($ids);exit;
        //print_r($data['pedidos']);exit;


        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/pagamento_carrinho/aguardando_pagamento", $data );
    }



}


