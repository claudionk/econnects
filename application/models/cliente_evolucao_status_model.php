<?php
Class Cliente_Evolucao_Status_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cliente_evolucao_status';
    protected $primary_key = 'cliente_evolucao_status_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('descricao');


    //Dados
    public $validate = array(
   
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'descricao' => $this->input->post('descricao'),
        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
    public function getDescricao($id)
    {
        $this->db->select($this->_table. '.descricao');
        $this->db->from($this->_table);
        $this->db->where($this->_table. '.' .$this->primary_key, $id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) 
        {
            $data = $query->result_array();
            return $data[0]['descricao'];
        }
        else
        {
            return;
        }
    }
}
