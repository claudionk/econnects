<?php
Class Produto_Parceiro_Plano_Precificacao_Itens_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_plano_precificacao_itens';
    protected $primary_key = 'produto_parceiro_plano_precificacao_itens_id';

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
            'field' => 'produto_parceiro_plano_id',
            'label' => 'Produto parceiro',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'produto_parceiro_plano',
        ),
        array(
            'field' => 'tipo',
            'label' => 'Tipo',
            'rules' => 'required|enum[RANGE,ADICIONAL]',
            'groups' => 'default'
        ),
        array(
            'field' => 'unidade_tempo',
            'label' => 'Unidade',
            'rules' => 'required|enum[DIA,MES,ANO,VALOR,IDADE]',
            'groups' => 'default'
        ),
        array(
            'field' => 'inicial',
            'label' => 'Inicial',
            'rules' => 'required|numeric',
            'groups' => 'default'
        ),
        array(
            'field' => 'final',
            'label' => 'Final',
            'rules' => 'required|numeric',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor',
            'label' => 'Valor',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'equipamento',
            'label' => 'equipamento',
            'groups' => 'default'
        ),
        array(
            'field' => 'cobranca',
            'label' => 'cobranca',
            'groups' => 'default'
        ),
        array(
            'field' => 'tipo_equipamento',
            'label' => 'tipo_equipamento',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_plano_id' => $this->input->post('produto_parceiro_plano_id'),
            'tipo' => $this->input->post('tipo'),
            'unidade_tempo' => $this->input->post('unidade_tempo'),
            'inicial' => $this->input->post('inicial'),
            'final' => $this->input->post('final'),
            'valor' => app_unformat_currency($this->input->post('valor')),
            'equipamento' => $this->input->post('equipamento'),
        );
        if( $data["equipamento"] != "" ) {
          $data["cobranca"] = "PORCENTAGEM";
          $data["tipo_equipamento"] = "CATEGORIA";
        }
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


    function  filter_by_produto_parceiro_plano($produto_parceiro_plano_id){

        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);

        return $this;
    }

    function  filter_by_tipo($tipo){

        $this->_database->where("{$this->_table}.tipo", $tipo);

        return $this;
    }

    function  filter_by_tipo_equipamento($tipo){

        //$this->_database->where( "({$this->_table}.tipo_equipamento='$tipo' OR {$this->_table}.tipo_equipamento='TODOS')" );
        $this->_database->where("{$this->_table}.tipo_equipamento", $tipo);
        //$this->_database->or_where("{$this->_table}.tipo_equipamento", "TODOS");

        return $this;
    }
    function  filter_by_equipamento($equipamento){

        $this->_database->like("{$this->_table}.equipamento", "'{$equipamento}'");

        return $this;
    }

    function  filter_by_intevalo_dias($qnt, $unidade_tempo = 'DIA'){

        $this->_database->where("$qnt >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$qnt <=", "{$this->_table}.final", FALSE);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

    function  filter_by_faixa_etaria( $qnt, $unidade_tempo = "IDADE" ){

        $this->_database->where("$qnt >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$qnt <=", "{$this->_table}.final", FALSE);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

    function  filter_by_faixa($qnt){
        $this->_database->where("$qnt >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$qnt <=", "{$this->_table}.final", FALSE);
        return $this;
    }



    function  filter_by_intevalo_menor($qnt, $unidade_tempo = 'DIA'){

        $this->_database->where("{$this->_table}.final <", $qnt);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

}



