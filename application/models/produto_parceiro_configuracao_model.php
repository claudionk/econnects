<?php
Class Produto_Parceiro_Configuracao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_configuracao';
    protected $primary_key = 'produto_parceiro_configuracao_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();
    
    //Dados
    public $validate = array(
        array(
            'field' => 'venda_habilitada_admin',
            'label' => 'Painel de vendas Admin',
            'rules' => 'required',
            'groups' => 'geral'
        ),
        array(
            'field' => 'salvar_cotacao_formulario',
            'label' => 'Formulário Salvar Cotação',
            'rules' => 'required',
            'groups' => 'geral'
        ),
        array(
            'field' => 'venda_habilitada_web',
            'label' => 'Painel de vendas WEB',
            'rules' => 'required',
            'groups' => 'geral'
        ),
        array(
            'field' => 'venda_carrinho_compras',
            'label' => 'Venda Carrinho de compras',
            'rules' => 'required',
            'groups' => 'geral'
        ),/*
        array(
            'field' => 'venda_multiplo_cartao',
            'label' => 'Venda com múltiplos cartões',
            'rules' => 'required',
            'groups' => 'default'
        ),*/

        array(
            'field' => 'calculo_tipo_id',
            'label' => 'Tipo de cálculo',
            'rules' => 'required|callback_check_tipo_calculo',
            'groups' => 'geral'
        ),
        array(
            'field' => 'markup',
            'label' => 'Markup do Produto',
            'rules' => 'required|callback_check_markup_relacionamento',
            'groups' => 'comissao'
        ),
        array(
            'field' => 'comissao',
            'label' => 'Comissão',
            'rules' => 'required',
            'groups' => 'comissao'
        ),/*
        array(
            'field' => 'repasse_comissao',
            'label' => 'Repasse de comissão',
            'rules' => 'required',
            'groups' => 'default'
        ),*/
        array(
            'field' => 'repasse_maximo',
            'label' => 'Repasse maximo',
            'rules' => 'required|callback_check_repasse_maximo',
            'groups' => 'comissao'
        ),
        array(
            'field' => 'padrao_repasse_comissao',
            'label' => 'Repasse de comissão (Padrão)',
            'rules' => 'required',
            'groups' => 'comissao'
        ),/*
        array(
            'field' => 'padrao_repasse_maximo',
            'label' => 'Repasse maximo (Padrão)',
            'rules' => 'required',
            'groups' => 'default'
        ),*/
        array(
            'field' => 'padrao_comissao',
            'label' => 'Comissão (Padrão)',
            'rules' => 'required',
            'groups' => 'comissao'
        ),
        array(
            'field' => 'comissao_indicacao',
            'label' => 'Comissão Indicação',
            'rules' => 'required',
            'groups' => 'comissao'
        ), /*
        array(
            'field' => 'valor',
            'label' => 'Valor',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'apolice_sequencia',
            'label' => 'Valor',
            'rules' => 'required',
            'groups' => 'default'
        ),*/
        array(
            'field' => 'pagamento_tipo',
            'label' => 'Tipo de pagamento',
            'rules' => 'required',
            'groups' => 'pagamento'
        ),array(
            'field' => 'quantidade_cobertura',
            'label' => 'Exibição de coberturas',
            'rules' => 'required',
            'groups' => 'geral'
        ),array(
            'field' => 'quantidade_cobertura_front',
            'label' => 'Exibição de coberturas (Mobile)',
            'rules' => 'required',
            'groups' => 'geral'
        ),array(
            'field' => 'conclui_em_tempo_real',
            'label' => 'Conclui a venda em Tempo Real',
            'rules' => 'required',
            'groups' => 'geral'
        )
    );

    //Get dados
    public function get_form_data($tipo = 'geral')
    {
        //Dados

        if($tipo == 'geral') {
            $data = array(
                'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
                'venda_habilitada_admin' => $this->input->post('venda_habilitada_admin'),
                'venda_habilitada_web' => $this->input->post('venda_habilitada_web'),
                'venda_carrinho_compras' => $this->input->post('venda_carrinho_compras'),
                'venda_multiplo_cartao' => $this->input->post('venda_multiplo_cartao'),
                'calculo_tipo_id' => $this->input->post('calculo_tipo_id'),
                'apolice_sequencia' => $this->input->post('apolice_sequencia'),
              	'apolice_vigencia' => $this->input->post('apolice_vigencia'),
                'apolice_vigencia_regra' => $this->input->post('apolice_vigencia_regra'),
                'salvar_cotacao_formulario' => $this->input->post('salvar_cotacao_formulario'),
                'quantidade_cobertura' => $this->input->post('quantidade_cobertura'),
                'quantidade_cobertura_front' => $this->input->post('quantidade_cobertura_front'),
                'conclui_em_tempo_real' => $this->input->post('conclui_em_tempo_real'),
                'endosso_controle_cliente' => $this->input->post('endosso_controle_cliente'),
            );
        }elseif ($tipo == 'comissao'){
            $data = array(
                'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
                'repasse_comissao' => $this->input->post('repasse_comissao'),
                'repasse_maximo' => app_unformat_currency($this->input->post('repasse_maximo')),
                'markup' => app_unformat_currency($this->input->post('markup')),
                'comissao' => app_unformat_currency($this->input->post('comissao')),
                'comissao_indicacao' => app_unformat_currency($this->input->post('comissao_indicacao')),
                'padrao_comissao' => app_unformat_currency($this->input->post('padrao_comissao')),
                'padrao_comissao_indicacao' => app_unformat_currency($this->input->post('padrao_comissao_indicacao')),
                'padrao_repasse_comissao' => $this->input->post('padrao_repasse_comissao'),
                'padrao_repasse_maximo' => app_unformat_currency($this->input->post('padrao_repasse_maximo')),
            );
        }elseif ($tipo == 'pagamento'){
            $data = array(
                'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
                'pagamento_tipo' => $this->input->post('pagamento_tipo'),
                'pagamento_periodicidade_unidade' => $this->input->post('pagamento_periodicidade_unidade'),
                'pagamento_periodicidade' => $this->input->post('pagamento_periodicidade'),
                'pagmaneto_cobranca' => $this->input->post('pagmaneto_cobranca'),
                'pagmaneto_cobranca_dia' => $this->input->post('pagmaneto_cobranca_dia'),
                'pagamento_teimosinha' => $this->input->post('pagamento_teimosinha'),
            );
        }else{
            $data = array(
                'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
                'venda_habilitada_admin' => $this->input->post('venda_habilitada_admin'),
                'venda_habilitada_web' => $this->input->post('venda_habilitada_web'),
                'venda_carrinho_compras' => $this->input->post('venda_carrinho_compras'),
                'venda_multiplo_cartao' => $this->input->post('venda_multiplo_cartao'),
                'calculo_tipo_id' => $this->input->post('calculo_tipo_id'),
                'repasse_comissao' => $this->input->post('repasse_comissao'),
                'repasse_maximo' => app_unformat_currency($this->input->post('repasse_maximo')),
                'markup' => app_unformat_currency($this->input->post('markup')),
                'comissao' => app_unformat_currency($this->input->post('comissao')),
                'comissao_indicacao' => app_unformat_currency($this->input->post('comissao_indicacao')),
                'padrao_comissao' => app_unformat_currency($this->input->post('padrao_comissao')),
                'padrao_comissao_indicacao' => app_unformat_currency($this->input->post('padrao_comissao_indicacao')),
                'padrao_repasse_comissao' => $this->input->post('padrao_repasse_comissao'),
                'padrao_repasse_maximo' => app_unformat_currency($this->input->post('padrao_repasse_maximo')),
                'apolice_sequencia' => $this->input->post('apolice_sequencia'),
              	'apolice_vigencia' => $this->input->post('apolice_vigencia'),
                'apolice_vigencia_regra' => $this->input->post('apolice_vigencia_regra'),
                'salvar_cotacao_formulario' => $this->input->post('salvar_cotacao_formulario'),
                'pagamento_tipo' => $this->input->post('pagamento_tipo'),
                'pagamento_periodicidade_unidade' => $this->input->post('pagamento_periodicidade_unidade'),
                'pagamento_periodicidade' => $this->input->post('pagamento_periodicidade'),
                'pagmaneto_cobranca' => $this->input->post('pagmaneto_cobranca'),
                'pagmaneto_cobranca_dia' => $this->input->post('pagmaneto_cobranca_dia'),
                'pagamento_teimosinha' => $this->input->post('pagamento_teimosinha'),

            );
        }

        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function insert_config($tipo){

        $data = $this->get_form_data($tipo);
        $this->insert($data, TRUE);
    }

    function update_config($tipo){

        $id = $this->input->post('produto_parceiro_configuracao_id');
        $data = $this->get_form_data($tipo);
        $this->update($id, $data, TRUE);

        if ($tipo=='geral')
        {
            $this->load->model('produto_parceiro_canal_model', 'produto_parceiro_canal');
            $this->produto_parceiro_canal->remove_produto_parceiro($this->input->post('produto_parceiro_id'));

            // Emissão
            if ( $this->input->post('canal_emissao') )
            {
                foreach ($this->input->post('canal_emissao') as $key => $value) {
                    $dt = [
                        'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
                        'canal_id'            => $value,
                        'tipo'                => 0, // emissão
                    ];
                    $this->produto_parceiro_canal->insert($dt, TRUE);
                }
            }

            // Cancelamento
            if ( $this->input->post('canal_cancelamento') )
            {
                foreach ($this->input->post('canal_cancelamento') as $key => $value) {
                    $dt = [
                        'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
                        'canal_id'            => $value,
                        'tipo'                => 1, // emissão
                    ];
                    $this->produto_parceiro_canal->insert($dt, TRUE);
                }
            }
        }
    }

    function filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);

        return $this;
    }

    public function item_config($produto_parceiro_id, $item){

        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        $config = $this->get_all();

        return empty($config[0]) || !empty($config[0][$item]);
    }


}

