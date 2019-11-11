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
        // $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        // $this->load->model('parceiro_tipo_model', 'parceiro_tipo');

        $this->parceiro_id = $this->session->userdata('parceiro_id');
    }

    public function index($offset = 0) //Função padrão (load)
    {

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Implantações");
        $this->template->set_breadcrumb("Implantações", base_url("{$this->controller_uri}/index"));

        // $this->template->js(app_assets_url('template/js/libs/jquery.orgchart/jquery.orgchart.js', 'admin'));
        // $this->template->js(app_assets_url('modulos/parceiro_relacionamento_produto/base.js', 'admin'));
        // $this->template->css(app_assets_url("template/js/libs/jquery.orgchart/jquery.orgchart.css", "admin"));

        //relacionamentos
        $produtos = $this->current_model->get_produtos_venda_admin_parceiros($this->parceiro_id);

        //Carrega dados para a página
        $data = array();
        $data["rows"] = $produtos;
        $data['primary_key'] = $this->current_model->primary_key();
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
        $data = $this->template->load("admin/layouts/base", "$this->controller_uri/view", $data, true );

        // $this->load->library('parser');
        $this->custom_loader->library('pdf');
        $this->pdf->setPageOrientation('P');

        $this->pdf->AddPage();

        //$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $destino_dir = FCPATH . "assets/files/implantacoes/";
        if (!file_exists($destino_dir)) {
            mkdir($destino_dir, 0777, true);
        }
        $this->pdf->SetMargins(5, 5, 5);
        $this->pdf->writeHTML($data, true, false, true, false, '');
        $destino = ($export == 'pdf') ? 'D' : 'F';
        $file    = ($export == 'pdf') ? "{$id}.pdf" : "{$destino_dir}{$id}.pdf";
        ob_end_clean();
        $this->pdf->Output($file, $destino);
        $this->custom_loader->unload_library('pdf');
        if ($export == 'pdf_file') {
            return "{$destino_dir}{$id}.pdf";
        } else {
            exit;
        }
    }

}
