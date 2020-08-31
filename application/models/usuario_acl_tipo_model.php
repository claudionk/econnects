<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Usuario_Acl_Tipo_Model
 *
 */
class Usuario_Acl_Tipo_Model extends MY_Model {


    protected $_table = 'usuario_acl_tipo';
    protected $primary_key = 'usuario_acl_tipo_id';

    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    protected $soft_delete_key = 'deletado';

    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');


    public $validate = array(
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_pai_id',
            'label' => 'Pai id',
            'rules' => '',
            'groups' => 'default'
        )

    );

    public function get_form_data($just_check = false){

        $data =  array(
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'parceiro_id' => $this->input->post('parceiro_id'),
            'parceiro_pai_id' => $this->input->post('parceiro_pai_id'),
        );


        return $data;
    }
    
    function filter_by_parceiro($parceiro_id)
    {
        $this->_database->where("{$this->_table}.parceiro_id", 0);
        $return = $this->get_all();

        $this->_database->where("{$this->_table}.parceiro_id", $parceiro_id);
        $result = $this->get_all();

        if ( empty($result) ){
            $this->_database->where("{$this->_table}.parceiro_id", $this->parceiro_pai_id);   
            $result = $this->get_all();
        }

        if ( !empty($result) )
        {
            foreach ($result as $r) {
                $return[] = $r;
            }
        }

        return $return;
    }
}