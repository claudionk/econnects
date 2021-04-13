<?php
Class Cliente_Evolucao_Status_Grupo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cliente_evolucao_status_grupo';
    protected $primary_key = 'cliente_evolucao_status_grupo_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('descricao_grupo');


    //Dados
    public $validate = array(
   
        array(
            'field' => 'descricao_grupo',
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
            'descricao_grupo' => $this->input->post('descricao_grupo'),
        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    public function getDescricao($id)
    {
        $this->db->select($this->_table. '.descricao_grupo');
        $this->db->from($this->_table);
        $this->db->where($this->_table. '.' .$this->primary_key, $id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) 
        {
            $data = $query->result_array();
            return $data[0]['descricao_grupo'];
        }
        else
        {
            return;
        }
    }

    //Retorna por CPF/CNPJ
    public function filter_by_descricao($descricao)
    {
        $this->_database->where($this->_table . '.descricao_grupo', $descricao);
        $this->_database->where($this->_table . '.deletado', 0);
        return $this;
    }

    //Retorna por CPF/CNPJ
    public function filter_by_uso_interno($uso_interno)
    {
        $this->_database->where($this->_table . '.uso_interno', $uso_interno);
        $this->_database->where($this->_table . '.deletado', 0);
        return $this;
    }

}
