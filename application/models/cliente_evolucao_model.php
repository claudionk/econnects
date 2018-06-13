<?php
Class Cliente_Evolucao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cliente_evolucao';
    protected $primary_key = 'cliente_evolucao_id';

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
            'field' => 'cliente_id',
            'label' => 'Cliente',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'colaborador_comercial_id',
            'label' => 'Comercial Responsável',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'cliente_evolucao_status_id',
            'label' => 'Status',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'data',
            'label' => 'Data',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'cliente_id' => $this->input->post('cliente_id'),
            'colaborador_comercial_id' => $this->input->post('colaborador_comercial_id'),
            'cliente_evolucao_status_id' => $this->input->post('cliente_evolucao_status_id'),
            'data' => date('Y-m-d H:i:s'),
        );
        return $data;
    }
    //Insere evolução caso mudado status ou responsável
    public function insere_data($cliente_id)
    {
        $data = $this->get_form_data();
        $data['cliente_id'] = $cliente_id;
        $this->_database->insert($this->_table, $data);
    }
    public function checa_update($cliente_id)
    {
        $data = $this->get_form_data();
        $this->_database->select($this->_table.'.*');
        $this->_database->from($this->_table);
        $this->_database->where($this->_table.'.cliente_id', $cliente_id);
        $this->_database->where($this->_table.'.colaborador_comercial_id', $data['colaborador_comercial_id']);
        $this->_database->where($this->_table.'.cliente_evolucao_status_id', $data['cliente_evolucao_status_id']);
        $query = $this->_database->get();

        if($query->num_rows() == 0)
            $this->insere_data($cliente_id);
    }


    //Retorna por ID
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
    //Retorna por Cliente
    function get_by_cliente($cliente_id)
    {
        $this->_database->select($this->_table.'.*');
        $this->_database->where($this->_table. '.cliente_id', $cliente_id);
        $this->_database->where($this->_table. '.deletado', 0);
        $this->_database->order_by($this->_table. '.data', 'DESC');
        $this->_database->from($this->_table);

        $query = $this->_database->get();

        if ($query->num_rows() > 0) 
        {
            $data = $query->result_array();
            return $data;
        }
        else
        {
            return array();
        }
    }
    //Agrega relação simples com colaborador
    function with_colaborador($fields = array('nome'))
    {
        $this->with_simple_relation_foreign('colaborador', 'colaborador_', 'colaborador_comercial_id', 'colaborador_id', $fields );
        return $this;
    }
    //Agrega relação simples com evolução status
    function with_cliente_evolucao_status($fields = array('descricao'))
    {
        $this->with_simple_relation('cliente_evolucao_status', 'cliente_evolucao_status_', 'cliente_evolucao_status_id', $fields );
        return $this;
    }
}
