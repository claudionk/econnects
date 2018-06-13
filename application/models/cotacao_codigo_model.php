<?php
Class Cotacao_Codigo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cotacao_codigo_seq';
    protected $primary_key = 'id';

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

    );

    /**
     * Obtem o proximo id
     * @return mixed
     */
    public function get_next_id(){

        /**
         * Incrementa a id
         */
        $this->update_id();

        /**
         * Retorna o id ja incrementado
         */
        return $this->get_last_id();


    }


    //Retorna ultimo id
    public function get_last_id()
    {


        $this->_database->select($this->_table.'.'.$this->primary_key);
        $this->_database->from($this->_table);
        $this->_database->order_by($this->_table.'.'.$this->primary_key, 'DESC');
        $this->_database->limit(1);
        $query = $this->_database->get();

        if($query->num_rows() > 0)
        {

            $row = $query->result_array();
            $id = $row[0][$this->primary_key];
            if($id >= 99999)
                $this->zeraContador();

            return $id;
        }
        else
        {
            $this->zeraContador();
        }

    }

    public function zeraContador()
    {
        $this->_database->empty_table($this->_table);
        $this->_database->insert($this->_table, array('id' => '00000'));
        return $this->get_last_id();
    }

    public function update_id()
    {
        $idToUpdate = $this->get_last_id();
        $data = array('id' => $idToUpdate + 1);
        $this->_database->where('id', $idToUpdate);
        $this->_database->update($this->_table, $data);
    }

    public function get_codigo(){

        return str_pad($this->get_next_id() , 5, '0', STR_PAD_LEFT);
    }

    public function get_codigo_cotacao_formatado($tipo){

        return strtoupper($tipo).date('ym'). $this->get_codigo() ;
    }

}
