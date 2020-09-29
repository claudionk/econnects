<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin_Controller extends MY_Controller
{
    protected $noLogin      = false;
    protected $_theme       = 'theme-1';
    protected $_theme_logo  = '';
    protected $_theme_nome  = 'Connects Insurance';
    protected $layout       = "base";
    protected $color        = 'default';
    protected $whatsapp     = '';
    protected $whatsapp_msg = '';
    public $name            = '';
    public $email           = '';

    protected $controller_name;
    protected $controller_uri;
    protected $parceiro_id;
    protected $parceiro_pai_id;

    protected $isConfirmaEmail  = true;
    protected $hasApp           = false;
    protected $token;
    protected $getUrl = '';

    const FORMA_PAGAMENTO_CARTAO_CREDITO  = 1;
    const FORMA_PAGAMENTO_TRANSF_BRADESCO = 2;
    const FORMA_PAGAMENTO_TRANSF_BB       = 7;
    const FORMA_PAGAMENTO_CARTAO_DEBITO   = 8;
    const FORMA_PAGAMENTO_BOLETO          = 9;
    const FORMA_PAGAMENTO_FATURADO        = 10;
    const FORMA_PAGAMENTO_CHECKOUT_PAGMAX = 11;
    const FORMA_PAGAMENTO_TERCEIROS       = 12;

    public function __construct()
    {
        parent::__construct();

        $this->load->model("usuario_model", "usuario");

        $this->output->set_header('Expires: Sat, 01 Jan 2000 00:00:01 GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0, max-age=0');
        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $this->output->set_header('Pragma: no-cache');

        $this->_theme_logo     = app_assets_url('template/img/logo-connects.png', 'admin');
        $this->controller_name = strtolower(get_class($this));
        $this->controller_uri  = "admin/{$this->controller_name}";

        $userdata = $this->session->all_userdata();

        if (isset($userdata['email'])) {
            $this->email = $userdata['email'];
        }

        if (isset($userdata['parceiro_id'])) {
            $this->_setTheme($userdata['parceiro_id']);
        }

        if (!empty($this->session->userdata('parceiro_id'))) {
            $this->parceiro_id = $this->session->userdata('parceiro_id');
        }

        if (!empty($this->session->userdata('parceiro_pai_id'))) {
            $this->parceiro_pai_id = $this->session->userdata('parceiro_pai_id');
        }

        $this->template->set('theme', $this->_theme);
        $this->template->set('theme_logo', $this->_theme_logo);
        $this->template->set('title', $this->_theme_nome);
        $this->template->set_breadcrumb('Home', base_url('admin/home'));
        $this->template->set('current_controller_name', $this->controller_name);
        $this->template->set('current_controller_uri', $this->controller_uri);
        $this->template->set('userdata', $userdata);

        //Seta layout
        $layout = ($this->session->userdata("layout")) ? $this->session->userdata("layout") : 'base';
        $this->layout = isset($layout) && !empty($layout) ? $layout : 'base';

        if (!empty($this->input->get("token"))) {
            $this->token = $this->input->get("token");
            $this->getUrl = '?token=' . $this->token;
        }
        if ($this->input->get('layout')) {
            $this->session->set_userdata("layout", $this->input->get('layout'));
            $layout = $this->input->get('layout');
            $this->layout = $layout;
            $this->getUrl .= '&layout=' . $this->layout;
        }
        if (!empty($this->input->get("color"))) {
            $this->color  = $this->input->get("color");
            $this->getUrl .= '&color=' . $this->color;
        }
        if ($this->input->get('context')) {
            $this->session->set_userdata("context", $this->input->get('context'));
        }

        $this->template->set('context', $this->session->userdata("context"));
        $this->template->set('layout', $layout);
        $urls_pode_acessar = $this->session->userdata("urls_pode_acessar");

        if (($this->router->fetch_class() !== 'login') && (!$this->auth->is_admin()) && ($this->noLogin === false)) {
            $url_redirect = '';
            $this->load->library('user_agent');
            if ($this->agent->is_referral()) {
                $url_redirect = urlencode($this->agent->referrer());
            }

            $redirect = "admin/login/index/?redirect={$url_redirect}";
            $this->load->helper('cookie');
            $login_parceiro_id = get_cookie('login_parceiro_id');

            if ($login_parceiro_id) {
                $this->load->model('parceiro_model', 'parceiro');
                $parceiro = $this->parceiro->get($login_parceiro_id);
                if ($parceiro) {
                    $redirect = "parceiro/{$parceiro['slug']}?redirect={$url_redirect}";
                }
            }

            $token = $this->input->get("token");

            if (!$token) {
                redirect($redirect);
            } else {

                if (!$this->usuario->login_token($token)) {
                    echo "Token inválido.";
                    exit;
                } else {
                    if (!empty($urls_pode_acessar)) {
                        $this->session->set_userdata("urls_pode_acessar", $urls_pode_acessar);
                    }

                    redirect(current_url() . '?' . $_SERVER['QUERY_STRING']);
                }
            }
        } else if ($this->router->fetch_class() !== 'login') {

            if (isset($userdata['termo_aceite']) && $userdata['termo_aceite'] == 0) {
                $this->template->js(app_assets_url('core/js/termo.js', 'admin'));
            }

            if (($urls_pode_acessar) && (!empty($urls_pode_acessar)) && (is_array($urls_pode_acessar)) && ($this->noLogin === false)) {
                $pode_acessar = false;
                foreach ($urls_pode_acessar as $url) {
                    $url_relativa = explode("#", $url);
                    $url_relativa = $url_relativa[0];

                    if (strpos(current_url(), $url_relativa) !== false) {

                        $pode_acessar = true;
                    }
                }

                if (!$pode_acessar && !in_array(current_url(), $urls_pode_acessar)) {
                    echo "Você não possui autorização para ver esta página.";
                    exit;
                }
            }

            if (isset($userdata['termo_aceite']) && $userdata['termo_aceite'] == 0) {
                $this->template->js(app_assets_url('core/js/termo.js', 'admin'));
            }
        }
    }

    public function _setTheme($parceiro_id)
    {

        $this->load->model('parceiro_model', 'parceiro');
        $parceiro          = $this->parceiro->get($parceiro_id);
        $this->_theme      = (!empty($parceiro['theme'])) ? $parceiro['theme'] : 'theme-1';
        $this->_theme_logo = (!empty($parceiro['logo'])) ? app_assets_url("upload/parceiros/{$parceiro['logo']}", 'admin') : app_assets_url('template/img/logo-connects.png', 'admin');
        $this->_theme_nome = (!empty($parceiro['apelido'])) ? $parceiro['apelido'] : $this->_theme_nome;
        $this->whatsapp    = (!empty($parceiro['whatsapp_num'])) ? $parceiro['whatsapp_num'] : '';
        $this->whatsapp_msg = (!empty($parceiro['whatsapp_msg'])) ? $parceiro['whatsapp_msg'] : '';
        $this->template->set('theme', $this->_theme);
        $this->template->set('theme_logo', $this->_theme_logo);
        $this->template->set('title', $this->_theme_nome);
        $this->template->set('whatsapp', $this->whatsapp);
        $this->template->set('whatsapp_msg', $this->whatsapp_msg);
    }

    public function venda_pagamento($produto_parceiro_id, $cotacao_id, $pedido_id = 0, $conclui_em_tempo_real = true, $getUrl = '')
    {
        $pedido_id = (int) $pedido_id;

        $this->load->model('apolice_model', 'apolice');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('forma_pagamento_tipo_model', 'forma_pagamento_tipo');
        $this->load->model('forma_pagamento_bandeira_model', 'forma_pagamento_bandeira');
        $this->load->model('forma_pagamento_model', 'forma_pagamento');
        $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cliente_contato_model', 'cliente_contato');
        $this->load->model('produto_parceiro_autorizacao_cobranca_model', 'autorizacao_cobranca');

        //Carrega templates
        $this->template->js(app_assets_url('core/js/jquery.card.js', 'admin'));

        if ($this->layout == 'front') {
            $this->template->js(app_assets_url('modulos/venda/equipamento/front/js/pagamento.js', 'admin'));
        } else {
            $this->template->js(app_assets_url('modulos/venda/pagamento/js/pagamento.js', 'admin'));
        }

        //Retorna cotação
        $cotacao = $this->cotacao->get_cotacao_produto($cotacao_id);

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

        //formas de pagamento
        $forma_pagamento = array();
        $tipo_pagamento  = $this->forma_pagamento_tipo->get_all();
        $exibe_url_acesso_externo = false;

        //Para cada tipo de pagamento
        foreach ($tipo_pagamento as $index => $tipo) {

            $forma = $this->produto_pagamento->with_forma_pagamento()
                ->filter_by_produto_parceiro($produto_parceiro_id)
                ->filter_by_forma_pagamento_tipo($tipo['forma_pagamento_tipo_id'])
                ->filter_by_ativo()
                ->get_all();

            $bandeiras = $this->forma_pagamento_bandeira->get_many_by(array("forma_pagamento_tipo_id" => $tipo["forma_pagamento_tipo_id"]));

            if (count($forma) > 0) {

                // exibe o link para acesso externo se houver configurado os tipos de cartão
                if (!$exibe_url_acesso_externo && in_array($tipo['forma_pagamento_tipo_id'], [$this->config->item('FORMA_PAGAMENTO_CARTAO_CREDITO'), $this->config->item('FORMA_PAGAMENTO_CARTAO_DEBITO')])) {
                    $exibe_url_acesso_externo = true;
                }

                foreach ($forma as $index => $item) {
                    $parcelamento = array();
                    for ($i = 1; $i <= $item['parcelamento_maximo']; $i++) {
                        if ($i <= $item['parcelamento_maximo_sem_juros']) {
                            $parcelamento[$i] = array("Parcelas" => $i, "Valor" => round($valor_total / $i, 2), "Descricao" => "{$i} X " . app_format_currency(round($valor_total / $i, 2)) . " sem juros");
                            //$parcelamento[$i] = "{$i} X ". app_format_currency($valor_total/$i) . " sem juros";
                        } else {
                            //$valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
                            $valor            = ($valor_total / (1 - ($item['juros_parcela'] / 100))) / $i;
                            $parcelamento[$i] = array("Parcelas" => $i, "Valor" => $valor, "Descricao" => "{$i} X " . app_format_currency($valor) . " com juros (" . app_format_currency($item['juros_parcela']) . "%)");
                            //$parcelamento[$i] = "{$i} X ". app_format_currency($valor) . " com juros (". app_format_currency($item['juros_parcela']) ."%)";
                        }
                    }
                    $forma[$index]['parcelamento'] = $parcelamento;
                }

                $forma_pagamento[] = array('tipo' => $tipo, 'pagamento' => $forma, 'bandeiras' => $bandeiras);
            }
        }

        $data                                  = array();
        $data['cotacao_id']                    = $cotacao_id;
        $data['pedido_id']                     = $pedido_id;
        $data['forma_pagamento']               = $forma_pagamento;
        $data['produto_slug']                  = $cotacao['produto_slug'];
        $data['cotacao_dados']                 = $cotacao;
        $data["isConfirmaEmail"]               = $this->isConfirmaEmail;
        $data["valor_total"]                   = $valor_total;
        $data["produtos_nome"]                 = $cotacao['equipamento_nome'];
        $data['cotacao']                       = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $data['carrossel']                     = $this->session->userdata("carrossel_{$produto_parceiro_id}");
        $data['dados_segurado']                = $this->session->userdata("dados_segurado_{$produto_parceiro_id}");
        $data['produto_parceiro_configuracao'] = $this->produto_parceiro_configuracao->get_by(array(
            'produto_parceiro_id' => $produto_parceiro_id,
        ));
        $data['url_pagamento_confirmado'] = "admin/venda_{$cotacao['produto_slug']}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/5/";
        $data['produto_parceiro_id']      = $produto_parceiro_id;
        $data['exibe_url_acesso_externo'] = $exibe_url_acesso_externo;
        $data['exibe_url_acesso_externo_tipo'] = 'pagamento';

        if ($exibe_url_acesso_externo) {
            $data['url_acesso_externo'] = $this->auth->generate_page_token(
                '',
                $this->urlAcessoExternoAutorizados($cotacao['produto_slug'], $produto_parceiro_id),
                'front',
                'pagamento'
            );
        }

        if ($_POST) {
            $tipo_forma_pagamento_id = $this->input->post('forma_pagamento_tipo_id');
            $validacao               = array();
            switch ($tipo_forma_pagamento_id) {
                case self::FORMA_PAGAMENTO_CARTAO_CREDITO: //cartão de crédito
                    $validacao[] = array(
                        'field'  => "numero",
                        'label'  => "Número do Cartão",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );

                    $validacao[] = array(
                        'field'  => "nome_cartao",
                        'label'  => "Nome",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );

                    $validacao[] = array(
                        'field'  => "validade",
                        'label'  => "Validade",
                        'rules'  => "trim|required|valid_vencimento_cartao",
                        'groups' => 'pagamento',
                    );

                    $validacao[] = array(
                        'field'  => "codigo",
                        'label'  => "Código",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );

                    $validacao[] = array(
                        'field'  => "bandeira_cartao",
                        'label'  => "Bandeira",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );

                    if (
                        ($data['produto_parceiro_configuracao']['pagamento_tipo'] == "RECORRENTE") &&
                        ($data['produto_parceiro_configuracao']['pagmaneto_cobranca'] == 'VENCIMENTO_CARTAO')
                    ) {
                        $validacao[] = array(
                            'field'  => "dia_vencimento",
                            'label'  => "Dia do vencimento",
                            'rules'  => "trim|numeric|required",
                            'groups' => 'pagamento',
                        );
                    }
                    break;
                case self::FORMA_PAGAMENTO_CARTAO_DEBITO: //cartão de débito
                    $validacao[] = array(
                        'field'  => "numero_debito",
                        'label'  => "Número do Cartão",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );

                    $validacao[] = array(
                        'field'  => "nome_cartao_debito",
                        'label'  => "Nome",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );

                    $validacao[] = array(
                        'field'  => "validade_debito",
                        'label'  => "Validade",
                        'rules'  => "trim|required|valid_vencimento_cartao",
                        'groups' => 'pagamento',
                    );

                    $validacao[] = array(
                        'field'  => "codigo_debito",
                        'label'  => "Código",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );

                    $validacao[] = array(
                        'field'  => "bandeira_cartao_debito",
                        'label'  => "Bandeira",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );
                    break;
                case self::FORMA_PAGAMENTO_BOLETO: //BOLETO
                    $validacao[] = array(
                        'field'  => "sacado_nome",
                        'label'  => "Nome",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );
                    $validacao[] = array(
                        'field'  => "sacado_documento",
                        'label'  => "CPF",
                        'rules'  => "trim|required|validate_cpf",
                        'groups' => 'pagamento',
                    );
                    $validacao[] = array(
                        'field'  => "sacado_endereco",
                        'label'  => "Endereço",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );
                    $validacao[] = array(
                        'field'  => "sacado_endereco_num",
                        'label'  => "Número",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );
                    $validacao[] = array(
                        'field'  => "sacado_endereco_cep",
                        'label'  => "CEP",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );
                    $validacao[] = array(
                        'field'  => "sacado_endereco_bairro",
                        'label'  => "Bairro",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );
                    $validacao[] = array(
                        'field'  => "sacado_endereco_cidade",
                        'label'  => "Cidade",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );
                    $validacao[] = array(
                        'field'  => "sacado_endereco_uf",
                        'label'  => "Estado",
                        'rules'  => "trim|required",
                        'groups' => 'pagamento',
                    );
                    break;
                case self::FORMA_PAGAMENTO_FATURADO: //faturado
                    break;
            }

            $this->cotacao->setValidate($validacao);

            //No primeiro envio não entra nesse if
            if ($this->cotacao->validate_form('pagamento')) {
                $pedido_id = $this->finishedPedido($pedido_id, $tipo_forma_pagamento_id, $_POST, $cotacao);

                switch ($this->input->post('forma_pagamento_tipo_id')) {
                    case self::FORMA_PAGAMENTO_CARTAO_CREDITO:
                    case self::FORMA_PAGAMENTO_CARTAO_DEBITO:
                    case self::FORMA_PAGAMENTO_BOLETO:

                        if ($getUrl == '') {
                            $this->session->set_flashdata('succ_msg', 'Aguardando confirmação do pagamento'); //Mensagem de sucesso
                            redirect("{$this->controller_uri}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/5/{$pedido_id}");
                        } else {

                            $this->session->set_flashdata('succ_msg', 'Pedido incluido com sucesso!'); //Mensagem de sucesso
                            redirect("admin/venda_{$cotacao['produto_slug']}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/6/{$pedido_id}{$getUrl}");
                        }

                        break;
                    default:
                        $this->session->set_flashdata('succ_msg', 'Pedido incluido com sucesso!'); //Mensagem de sucesso
                        redirect("admin/venda_{$cotacao['produto_slug']}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/6/{$pedido_id}{$getUrl}");
                        break;
                }
            }
        }

        $data['step'] = ($conclui_em_tempo_real) ? 3 : 2;

        $view = "admin/venda/pagamento";
        if ($this->layout == 'front') {
            $view = "admin/venda/equipamento/front/steps/step-three-pagamento";
        }

        $vigencia = $this->produto_parceiro_plano->getInicioFimVigencia($cotacao["produto_parceiro_plano_id"]);

        $data['pedidos'] = array(
            array(
                "codigo" => $cotacao["codigo"],
                "nome" => $cotacao["produto_nome"],
                "valor_total" => $data["valor_total"],
                "inicio_vigencia" => $vigencia["inicio_vigencia"],
                "fim_vigencia" => $vigencia["fim_vigencia"],
                "produto_parceiro_id" => $produto_parceiro_id
            )
        );

        $data["autorizacao_cobranca"] = null;
        $autorizacaoCobranca = $this->autorizacao_cobranca->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        if (!empty($autorizacaoCobranca)) {

            $html = $autorizacaoCobranca[0]["autorizacao_cobranca"];

            if (!empty($html)) {
                $data["autorizacao_cobranca"]["autorizacao_cobranca"] = $this->createAutorizacaoCobrancaHTML($html, [
                    "segurado_cnpj_cpf" => $cotacao['cnpj_cpf'],
                    "segurado_nome" => $cotacao['nome'],
                    "data_ini_vigencia" => $vigencia["inicio_vigencia"],
                    "data_fim_vigencia" => $vigencia["fim_vigencia"],
                    "premio_total" => $data["valor_total"],
                    "produto_nome" => $cotacao["produto_nome"]
                ]);
            }
        }

        // print_pre($data);
        $this->template->load("admin/layouts/{$this->layout}", $view, $data);
    }

    public function venda_aguardando_pagamento($produto_parceiro_id, $pedido_id = 0)
    {
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');

        $this->template->js(app_assets_url('modulos/venda/pagamento/js/aguardando_pagamento.js', 'admin'));

        $pedido = $this->pedido->with_pedido_status()->filter_by_pedido($pedido_id)->get_all();
        $pedido = $pedido[0];

        //Retorna cotação
        $cotacao = $this->cotacao->get_cotacao_produto($pedido['cotacao_id']);

        $data                        = array();
        $data['primary_key']         = $this->current_model->primary_key();
        $data['produto_parceiro_id'] = $produto_parceiro_id;
        $data['pedido_id']           = $pedido_id;
        $data['produto_slug']        = $cotacao['produto_slug'];
        $data['pedido']              = $pedido;
        if ($this->input->get('retorno') == 'pagmax') {
            $this->consulta_pagmax($pedido_id);
        }

        $this->template->load("admin/layouts/{$this->layout}", "admin/venda/aguardando_pagamento", $data);
    }

    public function pagamento_carrinho($novo = 0, $conclui_em_tempo_real = true, $getUrl = '')
    {
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('forma_pagamento_tipo_model', 'forma_pagamento_tipo');
        $this->load->model('forma_pagamento_model', 'forma_pagamento');
        $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        $this->load->model('forma_pagamento_bandeira_model', 'forma_pagamento_bandeira');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('produto_parceiro_autorizacao_cobranca_model', 'autorizacao_cobranca');
        $this->load->library('form_validation');

        $data = array();

        // Front a consulta deve ser por 
        if (empty($this->session->userdata('logado')) && $this->template->get('layout') == 'front') {
            $this->step_login();
        } else {
            $loginDoc = '';
            $loginIdUser = 0;

            if (!empty($this->session->userdata('cnpj_cpf'))) {
                $loginDoc = $this->session->userdata('cnpj_cpf');
            } else {
                $loginIdUser = $this->session->userdata('usuario_id');
            }

            $pedidos = ($novo == 0) ? $this->pedido->getPedidoCarrinho($loginIdUser, $loginDoc) : $this->session->userdata('pedido_carrinho');
            if (!$pedidos) {
                //Mensagem de erro caso registro não exista
                $this->session->set_flashdata('fail_msg', 'Carrinho esta vazio.');

                //Redireciona para index
                if ($this->layout != 'front') {
                    redirect("$this->controller_uri/index");
                } else {
                    $data['carrinho_vazio'] = true;
                }
            } else {

                //valor total
                $valor_total = 0;
                $produtos_nome = $virg = '';
                foreach ($pedidos as $index => $pedido) {
                    $valor_total += $pedido['valor_total'];
                    $produtos_nome .= $virg . $pedido['nome'];

                    $_cotacao = $this->cotacao->get_cotacao_produto($pedido['cotacao_id']);

                    $vigencia = $this->produto_parceiro_plano->getInicioFimVigencia($_cotacao["produto_parceiro_plano_id"]);

                    $pedidos[$index]['cotacao'] = $_cotacao;
                    $pedidos[$index]["inicio_vigencia"] = $vigencia["inicio_vigencia"];
                    $pedidos[$index]["fim_vigencia"] = $vigencia["fim_vigencia"];
                    $virg = ', ';
                }

                //Carrega templates
                $this->template->js(app_assets_url('core/js/jquery.card.js', 'admin'));
                if ($this->layout == 'front') {
                    $this->template->js(app_assets_url('modulos/venda/equipamento/front/js/pagamento.js', 'admin'));
                } else {
                    $this->template->js(app_assets_url('modulos/venda/pagamento/js/pagamento.js', 'admin'));
                }

                $produto_parceiro_id = $pedidos[0]['produto_parceiro_id'];
                $cotacao_id = $pedidos[0]['cotacao_id'];
                $pedido_id = $pedidos[0]['pedido_id'];
                $cotacao = $pedidos[0]['cotacao'];

                //formas de pagamento
                $forma_pagamento = array();
                $tipo_pagamento  = $this->forma_pagamento_tipo->get_all();
                $exibe_url_acesso_externo = false;

                //Para cada tipo de pagamento
                foreach ($tipo_pagamento as $index => $tipo) {

                    $forma = $this->produto_pagamento->with_forma_pagamento()
                        ->filter_by_produto_parceiro($produto_parceiro_id)
                        ->filter_by_forma_pagamento_tipo($tipo['forma_pagamento_tipo_id'])
                        ->filter_by_ativo()
                        ->get_all();

                    $bandeiras = $this->forma_pagamento_bandeira
                        ->get_many_by(array(
                            'forma_pagamento_tipo_id' => $tipo['forma_pagamento_tipo_id'],
                        ));

                    if (count($forma) > 0) {

                        // exibe o link para acesso externo se houver configurado os tipos de cartão
                        if (!$exibe_url_acesso_externo && in_array($tipo['forma_pagamento_tipo_id'], [$this->config->item('FORMA_PAGAMENTO_CARTAO_CREDITO'), $this->config->item('FORMA_PAGAMENTO_CARTAO_DEBITO')])) {
                            $exibe_url_acesso_externo = true;
                        }

                        foreach ($forma as $index => $item) {
                            $parcelamento = array();
                            for ($i = 1; $i <= $item['parcelamento_maximo']; $i++) {
                                if ($i <= $item['parcelamento_maximo_sem_juros']) {
                                    $parcelamento[$i] = array("Parcelas" => $i, "Valor" => round($valor_total / $i, 2), "Descricao" => "{$i} X " . app_format_currency(round($valor_total / $i, 2)) . " sem juros");
                                    //$parcelamento[$i] = "{$i} X ". app_format_currency($valor_total/$i) . " sem juros";
                                } else {
                                    //$valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
                                    $valor            = ($valor_total / (1 - ($item['juros_parcela'] / 100))) / $i;
                                    $parcelamento[$i] = array("Parcelas" => $i, "Valor" => $valor, "Descricao" => "{$i} X " . app_format_currency($valor) . " com juros (" . app_format_currency($item['juros_parcela']) . "%)");
                                    //$parcelamento[$i] = "{$i} X ". app_format_currency($valor) . " com juros (" . app_format_currency($item['juros_parcela']) ."%)";
                                }
                            }
                            $forma[$index]['parcelamento'] = $parcelamento;
                        }

                        $forma_pagamento[] = array('tipo' => $tipo, 'pagamento' => $forma, 'bandeiras' => $bandeiras);
                    }
                }

                $data['pedidos']                       = $pedidos;
                $data['cotacao_id']                    = $cotacao_id;
                $data['pedido_id']                     = $pedido_id;
                $data['forma_pagamento']               = $forma_pagamento;
                $data['produto_slug']                  = $cotacao['produto_slug'];
                $data['cotacao_dados']                 = $cotacao;
                $data["isConfirmaEmail"]               = $this->isConfirmaEmail;
                $data["valor_total"]                   = $valor_total;
                $data["produtos_nome"]                 = emptyor($produtos_nome, $cotacao['equipamento_nome']);
                $data['produto_parceiro_configuracao'] = $this->produto_parceiro_configuracao->get_by(array(
                    'produto_parceiro_id' => $produto_parceiro_id,
                ));
                $data['url_pagamento_confirmado'] = "admin/venda_{$cotacao['produto_slug']}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/5/";
                $data['produto_parceiro_id']      = $produto_parceiro_id;
                $data['exibe_url_acesso_externo'] = $exibe_url_acesso_externo;
                $data['exibe_url_acesso_externo_tipo'] = 'pagamento';

                if ($exibe_url_acesso_externo) {
                    $data['url_acesso_externo'] = $this->auth->generate_page_token(
                        '',
                        $this->urlAcessoExternoAutorizados($cotacao['produto_slug'], $produto_parceiro_id),
                        'front',
                        'pagamento'
                    );
                }

                if ($_POST) {
                    $tipo_forma_pagamento_id = $this->input->post('forma_pagamento_tipo_id');
                    $validacoes = array();
                    switch ($tipo_forma_pagamento_id) {
                        case self::FORMA_PAGAMENTO_CARTAO_CREDITO: //cartão de crédito
                            $validacao[] = array(
                                'field'  => "numero",
                                'label'  => "Número do Cartão",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );

                            $validacao[] = array(
                                'field'  => "nome_cartao",
                                'label'  => "Nome",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );

                            $validacao[] = array(
                                'field'  => "validade",
                                'label'  => "Validade",
                                'rules'  => "trim|required|valid_vencimento_cartao",
                                'groups' => 'pagamento',
                            );

                            $validacao[] = array(
                                'field'  => "codigo",
                                'label'  => "Código",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );

                            $validacao[] = array(
                                'field'  => "bandeira_cartao",
                                'label'  => "Bandeira",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );

                            if (
                                ($data['produto_parceiro_configuracao']['pagamento_tipo'] == "RECORRENTE") &&
                                ($data['produto_parceiro_configuracao']['pagmaneto_cobranca'] == 'VENCIMENTO_CARTAO')
                            ) {
                                $validacao[] = array(
                                    'field'  => "dia_vencimento",
                                    'label'  => "Dia do vencimento",
                                    'rules'  => "trim|numeric|required",
                                    'groups' => 'pagamento',
                                );
                            }
                            break;
                        case self::FORMA_PAGAMENTO_CARTAO_DEBITO: //cartão de débito
                            $validacao[] = array(
                                'field'  => "numero_debito",
                                'label'  => "Número do Cartão",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );

                            $validacao[] = array(
                                'field'  => "nome_cartao_debito",
                                'label'  => "Nome",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );

                            $validacao[] = array(
                                'field'  => "validade_debito",
                                'label'  => "Validade",
                                'rules'  => "trim|required|valid_vencimento_cartao",
                                'groups' => 'pagamento',
                            );

                            $validacao[] = array(
                                'field'  => "codigo_debito",
                                'label'  => "Código",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );

                            $validacao[] = array(
                                'field'  => "bandeira_cartao_debito",
                                'label'  => "Bandeira",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );
                            break;
                        case self::FORMA_PAGAMENTO_BOLETO: //BOLETO
                            $validacao[] = array(
                                'field'  => "sacado_nome",
                                'label'  => "Nome",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );
                            $validacao[] = array(
                                'field'  => "sacado_documento",
                                'label'  => "CPF",
                                'rules'  => "trim|required|validate_cpf",
                                'groups' => 'pagamento',
                            );
                            $validacao[] = array(
                                'field'  => "sacado_endereco",
                                'label'  => "Endereço",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );
                            $validacao[] = array(
                                'field'  => "sacado_endereco_num",
                                'label'  => "Número",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );
                            $validacao[] = array(
                                'field'  => "sacado_endereco_cep",
                                'label'  => "CEP",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );
                            $validacao[] = array(
                                'field'  => "sacado_endereco_bairro",
                                'label'  => "Bairro",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );
                            $validacao[] = array(
                                'field'  => "sacado_endereco_cidade",
                                'label'  => "Cidade",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );
                            $validacao[] = array(
                                'field'  => "sacado_endereco_uf",
                                'label'  => "Estado",
                                'rules'  => "trim|required",
                                'groups' => 'pagamento',
                            );
                            break;
                        case self::FORMA_PAGAMENTO_FATURADO: //faturado
                            break;
                    }

                    $this->cotacao->setValidate($validacao);

                    if ($this->cotacao->validate_form('pagamento')) {
                        foreach ($pedidos as $index => $pedido) {
                            $dados               = $_POST;
                            $dados['cotacao_id'] = $pedido['cotacao_id'];
                            $this->finishedPedido($pedido['pedido_id'], $tipo_forma_pagamento_id, $dados, $pedido['cotacao']);
                        }

                        $this->session->set_userdata("pedido_carrinho", $pedidos);

                        switch ($this->input->post('forma_pagamento_tipo_id')) {
                            case self::FORMA_PAGAMENTO_CARTAO_CREDITO:
                            case self::FORMA_PAGAMENTO_CARTAO_DEBITO:
                            case self::FORMA_PAGAMENTO_BOLETO:

                                if ($getUrl == '') {
                                    $this->session->set_flashdata('succ_msg', 'Aguardando confirmação do pagamento'); //Mensagem de sucesso
                                    // redirect("{$this->controller_uri}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/5/{$pedido_id}");
                                    redirect("{$this->controller_uri}/pagamento_carrinho_aguardando/");
                                } else {

                                    $this->session->set_flashdata('succ_msg', 'Pedido incluido com sucesso!'); //Mensagem de sucesso
                                    redirect("admin/venda_{$cotacao['produto_slug']}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/6/{$pedido_id}{$getUrl}");
                                }

                                break;
                            default:
                                $this->session->set_flashdata('succ_msg', 'Pedido incluido com sucesso!'); //Mensagem de sucesso
                                redirect("admin/venda_{$cotacao['produto_slug']}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/6/{$pedido_id}{$getUrl}");
                                break;
                        }
                    }
                }
            }

            $data['step'] = ($conclui_em_tempo_real) ? 3 : 2;
            $autorizacaoCobranca = $this->autorizacao_cobranca->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
            if (!empty($autorizacaoCobranca)) {
                $data["autorizacao_cobranca"] = $autorizacaoCobranca[0];
                $html = $data["autorizacao_cobranca"]["autorizacao_cobranca"];

                $data["autorizacao_cobranca"]["autorizacao_cobranca"] = $this->createAutorizacaoCobrancaHTML($html, [
                    "segurado_cnpj_cpf" => $cotacao['cnpj_cpf'],
                    "segurado_nome" => $cotacao['nome'],
                    "data_ini_vigencia" => $pedidos[0]["inicio_vigencia"],
                    "data_fim_vigencia" => $pedidos[0]["fim_vigencia"],
                    "premio_total" => $data["valor_total"],
                    "produto_nome" => $produtos_nome
                ]);
            } else {
                $data["autorizacao_cobranca"] = null;
            }
            $view = "$this->controller_uri/pagamento_carrinho/pagamento";
            if ($this->layout == 'front') {
                $view = "admin/venda/equipamento/front/steps/step-three-pagamento";
            }

            $this->template->load("admin/layouts/{$this->layout}", $view, $data);
        }
    }

    private function createAutorizacaoCobrancaHTML($html, $data)
    {
        $this->load->library('parser');

        if (empty($html))
            return '';

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);
        $remover = $dom->getElementById("remover");
        $remover->parentNode->removeChild($remover);
        $html = $dom->saveHTML(($dom->documentElement));

        $html = $this->parser->parse_string($html, [
            "segurado_cnpj_cpf" => app_cpf_to_mask(issetor($data['segurado_cnpj_cpf'], '')),
            "segurado_nome" => issetor($data['segurado_nome'], ''),
            "data_ini_vigencia" => date("d/m/Y", strtotime($data["data_ini_vigencia"])),
            "data_fim_vigencia" => date("d/m/Y", strtotime($data["data_fim_vigencia"])),
            "premio_total" => app_format_currency(issetor($data["premio_total"], 0), true),
            "produto_nome" => $data["produto_nome"]
        ], true);

        return $html;
    }

    public function getDataCamposBoleto($data_campo, $plano_id = 0)
    {

        $data = array();
        if (isset($data_campo['nome'])) {
            $data['sacado_nome'] = $data_campo['nome'];
        } elseif (isset($data_campo["plano_{$plano_id}_nome"])) {
            $data['sacado_nome'] = $data_campo["plano_{$plano_id}_nome"];
        }

        if (isset($data_campo['cnpj_cpf'])) {
            $data['sacado_documento'] = $data_campo['cnpj_cpf'];
        } elseif (isset($data_campo["plano_{$plano_id}_cpf"])) {
            $data['sacado_cpf'] = $data_campo["plano_{$plano_id}_cnpj_cpf"];
        }

        if (isset($data_campo['endereco_cep'])) {
            $data['sacado_endereco_cep'] = $data_campo['endereco_cep'];
        } elseif (isset($data_campo["plano_{$plano_id}_endereco_cep"])) {
            $data['sacado_endereco_cep'] = $data_campo["plano_{$plano_id}_endereco_cep"];
        }
        if (isset($data_campo['endereco'])) {
            $data['sacado_endereco'] = $data_campo['endereco'];
        } elseif (isset($data_campo["plano_{$plano_id}_endereco"])) {
            $data['sacado_endereco'] = $data_campo["plano_{$plano_id}_endereco"];
        }

        if (isset($data_campo['endereco_numero'])) {
            $data['sacado_endereco_num'] = $data_campo['endereco_numero'];
        } elseif (isset($data_campo["plano_{$plano_id}_endereco_numero"])) {
            $data['sacado_endereco_num'] = $data_campo["plano_{$plano_id}_endereco_numero"];
        }
        if (isset($data_campo['endereco_complemento'])) {
            $data['sacado_endereco_comp'] = $data_campo['endereco_complemento'];
        } elseif (isset($data_campo["plano_{$plano_id}_endereco_complemento"])) {
            $data['sacado_endereco_comp'] = $data_campo["plano_{$plano_id}_endereco_complemento"];
        }
        if (isset($data_campo['endereco_bairro'])) {
            $data['sacado_endereco_bairro'] = $data_campo['endereco_bairro'];
        } elseif (isset($data_campo["plano_{$plano_id}_endereco_bairro"])) {
            $data['sacado_endereco_bairro'] = $data_campo["plano_{$plano_id}_endereco_bairro"];
        }
        if (isset($data_campo['endereco_cidade'])) {
            $data['sacado_endereco_cidade'] = $data_campo['endereco_cidade'];
        } elseif (isset($data_campo["plano_{$plano_id}_endereco_cidade"])) {
            $data['sacado_endereco_cidade'] = $data_campo["plano_{$plano_id}_endereco_cidade"];
        }
        if (isset($data_campo['endereco_estado'])) {
            $data['sacado_endereco_uf'] = $data_campo['endereco_estado'];
        } elseif (isset($data_campo["plano_{$plano_id}_endereco_estado"])) {
            $data['sacado_endereco_uf'] = $data_campo["plano_{$plano_id}_endereco_estado"];
        }

        return $data;
    }

    public function pagamento_carrinho_aguardando()
    {
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->library('form_validation');

        $this->template->js(app_assets_url('modulos/venda/pagamento_carrinho/js/aguardando_pagamento.js', 'admin'));

        $pedidos = $this->session->userdata('pedido_carrinho');
        $ids     = array();
        $produto_parceiro_id = null;
        $status = array('pagamento_negado', 'cancelado', 'cancelado_stornado', 'aprovacao_cancelamento', 'cancelamento_aprovado');

        foreach ($pedidos as $pedido) {
            $ids[] = $pedido['pedido_id'];
            $produto_parceiro_id = $pedido['produto_parceiro_id'];

            if (!in_array($pedido['pedido_status_slug'], $status)) {
                $cotacao = $this->cotacao->get_cotacao_produto($pedido['cotacao_id']);

                if ($this->layout == 'front') {
                    redirect("admin/venda_{$cotacao['produto_slug']}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/6/{$pedido['pedido_id']}?token={$this->token}&layout={$this->layout}&color={$this->color}");
                }

                redirect("admin/venda_{$cotacao['produto_slug']}/{$cotacao['produto_slug']}/{$produto_parceiro_id}/5/{$pedido['pedido_id']}");
            }
        }

        $data = array();
        $data['pedidos'] = $this->pedido->getPedidosByID($ids);
        $data['produto_parceiro_id'] = $produto_parceiro_id;

        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/pagamento_carrinho/aguardando_pagamento", $data);
    }

    public function urlAcessoExternoAutorizados($produto_slug, $produto_parceiro_id)
    {
        return array(
            current_url(),
            base_url("admin/venda_{$produto_slug}/{$produto_slug}/{$produto_parceiro_id}/5/#"),
            base_url("admin/venda_{$produto_slug}/{$produto_slug}/{$produto_parceiro_id}/6/#"),
            base_url("admin/gateway/consulta"),
            base_url("admin/venda/pagamento_carrinho"),
            base_url("admin/pedido/cancelamento_link"),
            base_url("admin/venda/termo"),
            base_url("admin/venda_equipamento/termo"),
        );
    }

    public function step_login($data = [], $cotacao_id = 0)
    {
        $data = $this->step_login_core($data, $cotacao_id);

        $this->template->load("admin/layouts/{$this->layout}", "admin/venda/equipamento/{$this->layout}/login", $data);
    }

    public function step_login_cancel($data = [], $pedido_id = 0)
    {
        $data = $this->step_login_core($data, 0, $pedido_id);

        $this->template->load("admin/layouts/{$this->layout}", "admin/pedido/{$this->layout}/login", $data);
    }

    public function step_login_core($data = [], $cotacao_id = 0, $pedido_id = 0)
    {
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('apolice_model', 'apolice');

        $this->template->js(app_assets_url("modulos/venda/equipamento/js/login.js", "admin"));
        $this->template->js(app_assets_url("core/js/SenhaForte.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/popper.min.js", "admin"));

        if ($_POST) {
            $documento  = $_POST['cnpj_cpf'];
            // $senha      = $_POST['password'];
            // $confSenha  = $_POST['password_confirm'];
            $sucesso    = true;

            if (empty($documento)) {
                $this->session->set_flashdata('fail_msg', 'Informe o Documento (CPF / CNPJ).');
                $sucesso = false;
            }

            if (!app_validate_cpf_cnpj($documento)) {
                $this->session->set_flashdata('fail_msg', 'O documento informado é inválido.');
                $sucesso = false;
            }

            // if ( empty($senha) )
            // {
            //     $this->session->set_flashdata('fail_msg', 'A senha é obrigatória.');
            //     $sucesso = false;
            // }

            // if ( $senha != $confSenha )
            // {
            //     $this->session->set_flashdata('fail_msg', 'A senha não confere');
            //     $sucesso = false;
            // }

            $documento = app_clear_number($documento);

            if (!empty($cotacao_id)) {
                $registro = $this->cotacao->get_cotacao_produto($cotacao_id);

                if (empty($registro)) {
                    $this->session->set_flashdata('fail_msg', 'Cotação não identificada');
                    $sucesso = false;
                } elseif (app_clear_number($registro['cnpj_cpf']) != $documento) {
                    $this->session->set_flashdata('fail_msg', 'O documento informado é diferente do documento da Cotação.');
                    $sucesso = false;
                }
            } elseif (!empty($pedido_id)) {
                $registro = $this->apolice->getApolicePedido($pedido_id);

                if (empty($registro)) {
                    $this->session->set_flashdata('fail_msg', 'Pedido não identificado');
                    $sucesso = false;
                } else {
                    foreach ($registro as $key => $value) {
                        if (app_clear_number($value['cnpj_cpf']) != $documento) {
                            $this->session->set_flashdata('fail_msg', 'O documento informado é diferente do documento do Pedido.');
                            $sucesso = false;
                            break;
                        }
                    }
                }
            }

            // TODO: Fazer o login considerando o produto
            // TODO: Quando externo validar se o ID da cotação/pedido pertence ao CPF acessado
            if ($sucesso) {
                // $this->cliente->atualizar($this->input->post('cliente_id'), $_POST);
                $this->name = ''; // TODO: pegar corretamente o nome do segurado
                $this->session->set_userdata('logado', true);
                $this->session->set_userdata('cnpj_cpf', $documento);
                header("Refresh: 0;");
                return;
            }
        }

        return $data;
    }

    public function finishedPedido($pedido_id, $tipo_forma_pagamento_id, $dados, $cotacao)
    {
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('apolice_model', 'apolice');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('cliente_contato_model', 'cliente_contato');

        if ($pedido_id == 0) {
            $pedido_id = $this->pedido->insertPedido($dados);
        } else {
            $this->pedido->updatePedido($pedido_id, $dados);
            $this->pedido->insDadosPagamento($dados, $pedido_id);
        }

        switch ($cotacao['produto_slug']) {
            case "seguro_viagem":
                $cot_aux = $this->cotacao_seguro_viagem;
                break;
            case "equipamento":
                $cot_aux = $this->cotacao_equipamento;
                break;
            case "generico":
            case "seguro_saude":
                $cot_aux = $this->cotacao_generico;
                break;
        }

        if (isset($dados["email"])) {
            $cot_aux->updateEmailByCotacaoId($cotacao['cotacao_id'], $dados["email"]);
            $aClienteContato = $this->cliente_contato->with_contato()->get_by_cliente($cotacao["cliente_id"]);
            foreach ($aClienteContato as $clienteContato) {
                if ($clienteContato["contato"] == $cotacao["email"]) {
                    $clienteContato["contato"] = $dados["email"];
                    $this->cliente_contato->update_contato($clienteContato);
                    break;
                }
            }
        }

        //Se for faturamento, muda status para aguardando faturamento
        if ($pedido_id && ($tipo_forma_pagamento_id == self::FORMA_PAGAMENTO_FATURADO || $tipo_forma_pagamento_id == self::FORMA_PAGAMENTO_TERCEIROS)) {
            $status = $this->pedido->mudaStatus($pedido_id, ($tipo_forma_pagamento_id == self::FORMA_PAGAMENTO_FATURADO) ? "aguardando_faturamento" : "pagamento_confirmado");

            $this->apolice->insertApolice($pedido_id);
        }

        return $pedido_id;
    }

    public function termo($produto_parceiro_id, $export = '')
    {

        $this->load->model('apolice_model', 'apolice');

        $result = $this->apolice->termo(null, $export, $produto_parceiro_id);
        if ($result !== FALSE) {
            exit($result);
        }
    }
}
