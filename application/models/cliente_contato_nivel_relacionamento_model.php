<?php
Class Cliente_Contato_Nivel_Relacionamento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cliente_contato_nivel_relacionamento';
    protected $primary_key = 'cliente_contato_nivel_relacionamento_id';

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
    public function get_form_data($just_check = false){

        //Dados
        $data =  array(
            'descricao' => $this->input->post('descricao'),
        );
        return $data;
    }

    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
