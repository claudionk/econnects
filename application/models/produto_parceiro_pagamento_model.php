<?php
Class Produto_Parceiro_Pagamento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_pagamento';
    protected $primary_key = 'produto_parceiro_pagamento_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome_fatura');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'forma_pagamento_id',
            'label' => 'Forma de Pagamento',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo_operadora',
            'label' => 'Código Operadora',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome_fatura',
            'label' => 'Nome na Fatura',
            'rules' => 'required|max_length[13]',
            'groups' => 'default'
        ),
        array(
            'field' => 'parcelamento_maximo',
            'label' => 'Parcelamento máximo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parcelamento_maximo_sem_juros',
            'label' => 'Parcelamento máximo sem juros',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'juros_parcela',
            'label' => 'Juros Parcela',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'ativo',
            'label' => 'Ativo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'boleto_banco',
            'label' => 'Código Banco',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'boleto_cedente_endereco',
            'label' => 'Cedente Endereço',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'boleto_cedente_nome',
            'label' => 'Cedente Nome',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'boleto_cedente_cnpj',
            'label' => 'Cedente CNPJ',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'boleto_instrucoes',
            'label' => 'Instruções',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'boleto_nosso_numero',
            'label' => 'Nosso Número',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'boleto_vencimento',
            'label' => 'Boleto Vencimento',
            'rules' => '',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
            'forma_pagamento_id' => $this->input->post('forma_pagamento_id'),
            'codigo_operadora' => $this->input->post('codigo_operadora'),
            'nome_fatura' => $this->input->post('nome_fatura'),
            'parcelamento_maximo' => $this->input->post('parcelamento_maximo'),
            'parcelamento_maximo_sem_juros' => $this->input->post('parcelamento_maximo_sem_juros'),
            'juros_parcela' => app_unformat_currency($this->input->post('juros_parcela')),
            'ativo' => $this->input->post('ativo'),
            'boleto_banco' => $this->input->post('boleto_banco'),
            'boleto_cedente_endereco' => $this->input->post('boleto_cedente_endereco'),
            'boleto_cedente_nome' => $this->input->post('boleto_cedente_nome'),
            'boleto_cedente_cnpj' => $this->input->post('boleto_cedente_cnpj'),
            'boleto_instrucoes' => $this->input->post('boleto_instrucoes'),
            'boleto_nosso_numero' => $this->input->post('boleto_nosso_numero'),
            'boleto_vencimento' => $this->input->post('boleto_vencimento'),

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_forma_pagamento(){
        $this->with_simple_relation('forma_pagamento', 'forma_pagamento_', 'forma_pagamento_id', array('nome'));
        return $this;
    }

    function with_forma_pagamento_tipo(){
        $this->_database->select('forma_pagamento_tipo.forma_pagamento_integracao_id');
        $this->_database->select('forma_pagamento_tipo.nome as forma_pagamento_tipo_nome');
        $this->_database->select('forma_pagamento_tipo.slug as forma_pagamento_tipo_slug');
        $this->_database->join('forma_pagamento_tipo', 'forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id');
        return $this;
    }

    function  filter_by_forma_pagamento_tipo($forma_pagamento_tipo_id){

        $this->_database->where('forma_pagamento.forma_pagamento_tipo_id', $forma_pagamento_tipo_id);

        return $this;
    }

    function  filter_by_ativo(){

        $this->_database->where('produto_parceiro_pagamento.ativo', 1);

        return $this;
    }


    function  filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);

        return $this;
    }
}
