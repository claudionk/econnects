<?php
Class Produto_Parceiro_Apolice_Multiplo_Range_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_apolice_multiplo_range';
    protected $primary_key = 'produto_parceiro_apolice_multiplo_range_id';

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
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'numero_inicio',
            'label' => 'Número inicial',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'quantidade',
            'label' => 'Quantidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'habilitado',
            'label' => 'Habilitado',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $len = strlen($this->input->post('numero_inicio'));
        $data =  array(
            'parceiro_id' => $this->input->post('parceiro_id'),
            'nome' => $this->input->post('nome'),
            'numero_inicio' => $this->input->post('numero_inicio'),
            'sequencia' => $this->input->post('numero_inicio'),
            'numero_fim' => str_pad((int)$this->input->post('numero_inicio') + (int)$this->input->post('quantidade'), $len, "0", STR_PAD_LEFT ) ,
            'quantidade' => app_retorna_numeros($this->input->post('quantidade')),
            'habilitado' => $this->input->post('habilitado'),
        );

        // echo "<pre>";print_r($data);die();
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


    public function get_proximo_codigo($produto_parceiro_apolice_multiplo_range_id){

        $this->_database->select("{$this->_table}.*");
        $this->_database->where("{$this->_table}.produto_parceiro_apolice_multiplo_range_id", $produto_parceiro_apolice_multiplo_range_id);
        $this->_database->where("{$this->_table}.deletado", 0);
        $this->_database->where("{$this->_table}.habilitado", 1);
        $this->_database->where("{$this->_table}.sequencia < ", "{$this->_table}.numero_fim");
        $this->_database->from($this->_table);
        $this->_database->limit(1);
        $query = $this->_database->get();
        if($query->num_rows() > 0) {
            $row = $query->result_array();
            $row = $row[0];
            $row['sequencia'] = (int)$row['sequencia'] + 1;

            $arr_update = array();
            $len = strlen($row['numero_inicio']);
            $arr_update['sequencia'] = str_pad($row['sequencia'], $len, "0", STR_PAD_LEFT );
            $this->update($row['produto_parceiro_apolice_multiplo_range_id'], $arr_update, TRUE);

            return $arr_update['sequencia'];
        }else{
            return 'NNNNNNNNNN';
        }
    }

    function  filter_by_parceiro($produto_parceiro_id){

        $this->_database->select("(SELECT COUNT(1) FROM produto_parceiro_apolice_multiplo WHERE produto_parceiro_apolice_multiplo_range_id = produto_parceiro_apolice_multiplo_range.produto_parceiro_apolice_multiplo_range_id) AS qtdeParc ");
        $this->_database->where("{$this->_table}.parceiro_id", $produto_parceiro_id);

        return $this;
    }

}
