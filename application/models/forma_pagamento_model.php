<?php

Class Forma_Pagamento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'forma_pagamento';
    protected $primary_key = 'forma_pagamento_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;
    
    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'forma_pagamento_tipo_id',
            'label' => 'Tipo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
        array(
            'field' => 'aceita_parcelamento',
            'label' => 'Aceita Parcelamento',
            'rules' => 'trim|required',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'forma_pagamento_tipo_id' => $this->input->post('forma_pagamento_tipo_id'),
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'icon' => $this->input->post('icon'),
            'aceita_parcelamento' => $this->input->post('aceita_parcelamento'),
        );

        return $data;
    }


    //Agrega tipo de cobertura
    function with_forma_pagamento_tipo($fields = array('nome'))
    {
        $this->with_simple_relation('forma_pagamento_tipo', 'forma_pagamento_tipo_', 'forma_pagamento_tipo_id', $fields );
        return $this;
    }


    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
