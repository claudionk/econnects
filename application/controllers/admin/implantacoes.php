<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Parceiros_Contatos
 *
 * @property Produto_Ramo_Model $current_model
 *
 */
class Implantacoes extends Admin_Controller
{
    private $parceiro_id;

    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Implantações");
        $this->template->set_breadcrumb("Implantações", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('produto_parceiro_model', 'current_model');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('parceiro_relacionamento_produto_model', 'parceiro_relacionamento_produto');
        $this->load->model('implantacao_status_model', 'implantacao_status');
        $this->load->model('produto_parceiro_implantacao_model', 'produto_parceiro_implantacao');

        $this->parceiro_id = $this->session->userdata('parceiro_id');
    }

    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Implantações");
        $this->template->set_breadcrumb("Implantações", base_url("{$this->controller_uri}/index"));

        $produtos = $this->current_model
        ->with_implantacao_staus();

        if ( isset($_GET['filter']) )
        {
            if (!empty($_GET['filter']['produto']))
                $produtos = $produtos->filter_produto_texto($_GET['filter']['produto']);

            if (!empty($_GET['filter']['implantacao_status_id']))
                $produtos = $produtos->filter_implantacao_status_id($_GET['filter']['implantacao_status_id']);
        }

        //relacionamentos
        $produtos = $produtos->get_produtos_venda_admin_parceiros($this->parceiro_id);
        $parceiros = [];
        $parceiros_ids = [];

        // echo "<pre>";print_r($this->db->last_query());die();

        //Carrega dados para a página
        $data = array();
        $data["rows"] = $produtos;
        $data['primary_key'] = $this->current_model->primary_key();
        $data['implantacao_status'] = $this->implantacao_status->get_all();

        if ( isset($_GET['filter']) )
        {
            if (!empty($_GET['filter']['nome_fantasia']))
                $parceiros = $this->parceiro->filterFromInput()->get_all();
        }

        foreach ($parceiros as $key => $value) {
            array_push($parceiros_ids, $value['parceiro_id']);
        }
        // echo "<pre>";print_r($parceiros_ids);die();

        if ( !empty($data["rows"]) )
        {
            foreach ($data["rows"] as $key => $value) {
                $prods = $this->current_model->getProdutosHabilitados($parceiros_ids, $value['produto_parceiro_id']);
                if (!empty($prods))
                {
                    foreach ($prods as $k => $v) {
                        if ( empty($data["rows"][$key]['representante']) )
                        {
                            $data["rows"][$key]['representante'] = $v['nome'];
                        }
                    }
                } else {
                    unset( $data["rows"][$key] );
                }
            }
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function core($id) //Função que edita registro
    {
        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Visualizar Implantações");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        $this->load->model('produto_ramo_model', 'produto_ramo');
        $this->load->model('produto_parceiro_pagamento_model', 'produto_parceiro_pagamento');
        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        $this->load->model('produto_parceiro_servico_model', 'produto_parceiro_servico');
        $this->load->model('produto_parceiro_cancelamento_model', 'produto_parceiro_cancelamento');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('produto_parceiro_capitalizacao_model', 'produto_parceiro_capitalizacao');
        $this->load->model('produto_parceiro_regra_preco_model', 'produto_parceiro_regra_preco');
        $this->load->model('cobertura_plano_model', 'cobertura_plano');
        $this->load->model('apolice_model', 'apolice');

        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->with_produto()->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/view/{$id}");

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

        $data['row']['forma_pagamentos'] = '';
        $data['row']['ramo'] = $this->produto_ramo->get($data['row']['produto_produto_ramo_id']);
        $data['row']['venda_agrupada'] = yes_no($data['row']['venda_agrupada']);
        $data['row']['configuracao'] = $this->produto_parceiro_configuracao->filter_by_produto_parceiro($id)->get_all();
        $data['row']['pagamento'] = $this->produto_parceiro_pagamento->with_forma_pagamento()->filter_by_produto_parceiro($id)->get_all();
        $data['row']['servicos'] = $this->produto_parceiro_servico->with_servico()->with_servico_tipo()->filter_by_produto_parceiro($id)->get_all();
        $data['row']['cancelamento'] = $this->produto_parceiro_cancelamento->filter_by_produto_parceiro($id)->get_all();
        $data['row']['planos'] = $this->produto_parceiro_plano->with_precificacao_tipo()->with_moeda()->filter_by_produto_parceiro($id)->get_all();
        $data['row']['acrescimo_premio'] = $this->produto_parceiro_regra_preco->with_regra_preco()->filter_by_produto_parceiro($id)->get_all();
        $data['row']['capitalizacao'] = $this->produto_parceiro_capitalizacao->with_capitalizacao()->with_capitalizacao_tipo()->with_capitalizacao_sorteio()->filter_by_produto_parceiro($id)->get_all();
        $data['row']['apolice'] = $this->apolice->search_apolice_produto_parceiro_id($id)->get_all();

        $data['row']['data_configuracao'] = app_date_mysql_to_mask($data['row']['criacao'], 'd/m/Y');

        if( $data['row']['apolice'] )
        {
            $data['row']['apolice'] = $data['row']['apolice'][0];
            $data['row']['data_primeira_emissao'] = app_date_mysql_to_mask($data['row']['apolice']['criacao'], 'd/m/Y');
        }

        $data['row']['data_aprovacao'] = '-';
        $data['row']['data_producao'] = '-';
        $data['row']['implantacao_aprovado'] = $this->produto_parceiro_implantacao->filter_by_produto_parceiro_id($id)->filter_by_implantacao_slug('aprovado')->filter_by_last()->get_all();
        $data['row']['implantacao_producao'] = $this->produto_parceiro_implantacao->filter_by_produto_parceiro_id($id)->filter_by_implantacao_slug('producao')->filter_by_last()->get_all();

        if ( !empty($data['row']['implantacao_aprovado']) )
        {
            $data['row']['implantacao_aprovado'] = $data['row']['implantacao_aprovado'][0];
            $data['row']['data_aprovacao'] = app_date_mysql_to_mask($data['row']['implantacao_aprovado']['criacao'], 'd/m/Y');
            $data['row']['user_aprovacao'] = $data['row']['implantacao_aprovado']['user'];
        }

        if ( !empty($data['row']['implantacao_producao']) )
        {
            $data['row']['implantacao_producao'] = $data['row']['implantacao_producao'][0];
            $data['row']['data_producao'] = app_date_mysql_to_mask($data['row']['implantacao_producao']['criacao'], 'd/m/Y');
        }

        if( $data['row']['configuracao'] )
        {
            foreach ($data['row']['configuracao'] as $key => $value) {
                $data['row']['configuracoes'] = $data['row']['configuracao'][$key];
                $data['row']['configuracoes']['calculo_tipo'] = ($data['row']['configuracao'][$key]['calculo_tipo_id'] == 1) ? 'NET' : 'BRUTO'; 
                $data['row']['configuracoes']['apolice_sequencia'] = ($data['row']['configuracao'][$key]['apolice_sequencia'] == 1) ? 'INTERNO' : 'EXTERNO'; 
                $data['row']['configuracoes']['apolice_vigencia_regra'] = ($data['row']['configuracao'][$key]['apolice_vigencia_regra'] == 'N') ? 'NÃO POSSUI' : 'A PARTIR DA MEIA NOITE'; 
                $data['row']['configuracoes']['arrecadacao'] = ($data['row']['configuracao'][$key]['endosso_controle_cliente'] == 0) ? 'ÚNICO' : (($data['row']['configuracao'][$key]['endosso_controle_cliente'] == 1) ? 'RECORRENTE' : 'PARCELADO'); 
            }
        }

        if( $data['row']['pagamento'] )
        {
            $separator='';
            foreach ($data['row']['pagamento'] as $key => $value) {
                $data['row']['forma_pagamentos'] .= $separator.$value['forma_pagamento_nome'];
                $separator=' / ';
            }
        }

        if( $data['row']['servicos'] )
        {
            $data['row']['servico']['cpf'] = 'NÃO';
            $data['row']['servico']['email_comp'] = 'NÃO';
            $data['row']['servico']['sms_comp'] = 'NÃO';
            foreach ($data['row']['servicos'] as $key => $value) {
                if ( in_array( $data['row']['servicos'][$key]['slug'], ['ifaro_pf', 'unitfour_pf'])  )
                {
                    $data['row']['servico']['cpf'] = 'SIM';
                }
            }
        }

        if( $data['row']['cancelamento'] )
        {
            $data['row']['cancelamento'] = $data['row']['cancelamento'][0];
            $data['row']['cancelamento']['seg_antes_hab'] = yes_no($data['row']['cancelamento']['seg_antes_hab']);
            $data['row']['cancelamento']['seg_depois_hab'] = yes_no($data['row']['cancelamento']['seg_depois_hab']);
            $data['row']['cancelamento']['inad_hab'] = yes_no($data['row']['cancelamento']['inad_hab']);
            $data['row']['cancelamento']['inad_reativacao_hab'] = yes_no($data['row']['cancelamento']['inad_reativacao_hab']);
            $data['row']['cancelamento']['calculo_tipo'] = ($data['row']['cancelamento']['calculo_tipo'] == 'T') ? 'TABELA PRAZO CURTO' : (($data['row']['cancelamento']['calculo_tipo'] == 'P') ? 'PRO-RATA' : 'ESPECIFICO');
        }

        $data['iof'] = 0;
        if( $data['row']['acrescimo_premio'] )
        {
            foreach ($data['row']['acrescimo_premio'] as $key => $value) {
                if ( $data['row']['acrescimo_premio'][$key]['regra_preco_slug'] == 'iof' )
                {
                    $data['iof'] = $data['row']['acrescimo_premio'][$key]['parametros'];
                }
            }
        }

        if( $data['row']['capitalizacao'] )
        {
            $data['row']['capitalizacao'] = $data['row']['capitalizacao'][0];
            $data['row']['capitalizacao']['qnt_sorteio'] = !empty($data['row']['capitalizacao']['capitalizacao_tipo_qnt_sorteio']) ? 'MESES DE VIGÊNCIA' : $data['row']['capitalizacao']['capitalizacao_qnt_sorteio'];
            $data['row']['capitalizacao']['serie'] = !empty($data['row']['capitalizacao']['capitalizacao_serie']) ? 'FECHADA' : 'ABERTA';
            $data['row']['capitalizacao']['titulo_randomico'] = !empty($data['row']['capitalizacao']['capitalizacao_titulo_randomico']) ? 'RANDÔMICO' : 'SEQUENCIAL';
            $data['row']['capitalizacao']['responsavel_num_sorte'] = empty($data['row']['capitalizacao']['capitalizacao_responsavel_num_sorte']) ? 'INTEGRAÇÃO' : ($data['row']['capitalizacao']['capitalizacao_responsavel_num_sorte'] == 1 ? 'PARCEIRO' : 'MANUAL');
        }

        if( $data['row']['planos'] )
        {
            foreach ($data['row']['planos'] as $key => $value) {
                $data['row']['planos'][$key]['coberturas'] = $this->cobertura_plano
                    ->with_cobertura()
                    ->with_cobertura_tipo()
                    ->with_parceiro()
                    ->filter_by_produto_parceiro_plano($value['produto_parceiro_plano_id'])
                    ->get_all();
            }
        }

        // $data['produtos'] = $this->current_model->with_produto()->with_parceiro()->get_all();
        $data['parceiros'] = $this->parceiro_relacionamento_produto
            ->filter_by_produto_parceiro($id)
            ->with_parceiro()
            ->with_parceiro_tipo()
            ->with_parceiro_endereco()
            ->get_all();

        if ( !empty($data['parceiros']) )
        {
            foreach ($data['parceiros'] as $key => $value) 
            {
                switch ($data['parceiros'][$key]['codigo_interno']) {
                    case 'corretora':
                        $tipo_comissao = 'COMISSÃO';
                        break;
                    case 'representante':
                        $tipo_comissao = 'PRÓ LABORE';
                        break;
                    
                    default:
                        $tipo_comissao = '-';
                        break;
                }
                $data['parceiros'][$key]['tipo_comissao'] = $tipo_comissao;
                $data['parceiros'][$key]['comissao_tipo'] = ($data['parceiros'][$key]['comissao_tipo'] == 0) ? 'FIXO' : 'VARIÁVEL';
            }
        }

        $data['parceiro'] = array();
        $data['implantacao_status'] = $this->implantacao_status->get_all();

        $prod_parc = $this->current_model->getProdutosByParceiro($this->parceiro_id, $id, false);
        foreach ($prod_parc as $key => $value) {
            $parc_prod = $this->current_model->getProdutosHabilitados(null, $id);
            
            // nenhum parceiro tem permissao para o produto
            if ( !empty($parc_prod) )
            {
                foreach ($parc_prod as $k => $v) {
                    // se o parceiro nao seja a seguradora logada
                    if ( $parc_prod[$k]['parceiro_id'] == $this->parceiro_id )
                    {
                        unset($parc_prod[$k]);
                    } else{
                        $data['parceiro'] = $this->parceiro->get($parc_prod[$k]['parceiro_id']);
                    }
                }
            }
        }

        //Carrega template
        return $data;
    }

    public function view($id)
    {
        $data = $this->core($id);
        $this->template->load("admin/layouts/base", "$this->controller_uri/view", $data );
    }

    public function printer($id, $export = 'pdf')
    {
        $data = $this->core($id);
        $data['print'] = true;
        $this->template->load("admin/layouts/empty", "$this->controller_uri/view", $data);
    }

    public function status($id, $slug)
    {
        // validar se o produto já está no status
        $implantacao_status = $this->implantacao_status->filter_by_slug($slug)->get_all();
        if ( empty($implantacao_status) )
        {
            return false;
        }

        $implantacao_status_id = $implantacao_status[0]['implantacao_status_id'];

        $dados['produto_parceiro_id'] = $id;
        $dados['implantacao_status_id'] = $implantacao_status_id;
        $dados['criacao'] = date('Y-m-d H:i:s');
        $dados['alteracao_usuario_id'] = $this->parceiro_id;
        $this->produto_parceiro_implantacao->insert($dados, true);

        $this->current_model->update($id, ['implantacao_status_id' => $implantacao_status_id], true);

        redirect("$this->controller_uri/index");
    }

}
