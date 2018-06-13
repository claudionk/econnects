<?php
Class Qualificacao_Questao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'qualificacao_questao';
    protected $primary_key = 'qualificacao_questao_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('pergunta', 'objetivo');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'pergunta',
            'label' => 'Pergunta',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'objetivo',
            'label' => 'Objetivo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'qualificacao_categoria_id',
            'label' => 'Categoria',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'qualificacao_criterio_id',
            'label' => 'Critério',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'peso',
            'label' => 'Peso',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'qualificacao_id',
            'label' => 'Qualificação',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'regua_logica',
            'label' => 'Regua',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'pergunta' => $this->input->post('pergunta'),
            'objetivo' => $this->input->post('objetivo'),
            'qualificacao_categoria_id' => $this->input->post('qualificacao_categoria_id'),
            'qualificacao_criterio_id' => $this->input->post('qualificacao_criterio_id'),
            'peso' => $this->input->post('peso'),
            'qualificacao_id' => $this->input->post('qualificacao_id'),
            'regua_logica' => $this->input->post('regua_logica')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_qualificacao($qualificacao_id){

        $this->_database->where('qualificacao_id', $qualificacao_id );

        return $this;
    }

    function with_categoria(){
        $this->with_simple_relation('qualificacao_categoria', 'qualificacao_categoria_', 'qualificacao_categoria_id', array('nome'));
        return $this;
    }

    function with_criterio(){
        $this->with_simple_relation('qualificacao_criterio', 'qualificacao_criterio_', 'qualificacao_criterio_id', array('nome'));
        return $this;
    }

    /**
     * Filtra do input
     * @param null $filter
     * @param null $data
     * @param bool $thisTable
     * @param bool $or
     * @return $this
     */
    public function filterFromInput($filter = null, $data = null, $thisTable = true, $or = true){


        if($this->input->get('filter')) {

            $filters = $this->input->get('filter');



            $field = 'pergunta';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = $filters[$field];
                $this->db->like("{$this->_table}.{$field}", $query );
            }

            $field = 'objetivo';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = $filters[$field];
                $this->db->like("{$this->_table}.{$field}", $query );
            }
        }

        return $this;
    }

}
