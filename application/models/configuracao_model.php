<?php
Class Configuracao_Model extends MY_Model
{

    protected $_table = 'configuracao';
    protected $primary_key = 'configuracao_id';

    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    protected $soft_delete_key = 'deletado';

    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    protected $skip_validation = TRUE;

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();


    public $validate = array(
        array(
            'field' => 'chave',
            'label' => 'Chave',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor',
            'label' => 'Valor',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'label',
            'label' => 'Label',
            'rules' => 'required',
            'groups' => 'default'
        )

    );

    public function get_form_data($just_check = false){

        $data =  array(
            'chave' => $this->input->post('chave'),
            'valor' => $this->input->post('valor'),
            'label' => $this->input->post('label')
        );


        return $data;
    }


    public function get_config_value_by_chave($chave){

        $row = $this->get_by('chave', $chave);
        if($row){

            return $row['valor'];
        }
        return FALSE;
    }

    function get_config_data_from_input(){

        $data =  $this->input->post('config');


        return $data;

    }
    function update_config_from_input()
    {
        $config = $this->get_config_data_from_input();

        foreach($config as $key => $value){


            $this->update_by_chave($key, $value );
        }


    }
    function update_by_chave($chave, $valor){



        $this->update_by(  array('chave' => $chave),array(
            'valor' => $valor
        ) );

    }

    function get_all_options(){

        $rows = $this->get_all();
        $data = array();

        foreach($rows as $row){

            $data[$row['chave']] = $row['valor'];
        }

        return $data;

    }


}
