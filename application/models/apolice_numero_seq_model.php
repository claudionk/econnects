<?php
Class Apolice_Numero_Seq_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'apolice_numero_seq';
    protected $primary_key = 'apolice_numero_seq_id';

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
     * Obtem o proximo número
     * @return mixed
     */
    public function get_proximo_codigo($produto_parceiro_id)
    {
        $this->_database->select("{$this->_table}.*");
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->where("{$this->_table}.deletado", 0);
        $this->_database->from($this->_table);
        $this->_database->limit(1);
        $query = $this->_database->get();
        if($query->num_rows() > 0){
            $row = $query->result_array();
            $row = $row[0];
            $row['sequencia']++;
        }else{

            $data_seq = array();
            $data_seq['produto_parceiro_id'] = $produto_parceiro_id;
            $data_seq['sequencia'] = 1;
            $id = $this->insert($data_seq, true);
            $row = $this->get($id);
        }

        $arr_update = array();
        $arr_update['sequencia'] = $row['sequencia'];
        $this->update($row['apolice_numero_seq_id'], $arr_update, TRUE);

        return str_pad($arr_update['sequencia'] , 10, '0', STR_PAD_LEFT);
    }

}
