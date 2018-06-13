<?php
Class Usuario_Acl_Recurso_Acao_Model extends MY_Model
{

    protected $_table = 'usuario_acl_recurso_acao';
    protected $primary_key = 'usuario_acl_recurso_acao_id';

    protected $return_type = 'array';
    protected $soft_delete = FALSE;

    protected $soft_delete_key = 'deletado';

    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();

    public $validate = array(
        array(
            'field' => 'usuario_acl_recurso_id',
            'label' => 'Recurso',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'usuario_acl_recurso'
        ),
        array(
            'field' => 'usuario_acl_acao_id',
            'label' => 'Ação',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'usuario_acl_acao'
        )
    );






    function get_form_data($just_check = false)
    {
        $data = array();

        $acoes = $this->input->post('acoes');
        $usuario_acl_recurso_id = $this->input->post('usuario_acl_recurso_id');

        foreach($acoes as $usuario_acl_acao_id){

                $data[] = array(
                    'usuario_acl_recurso_id' => $usuario_acl_recurso_id,
                    'usuario_acl_acao_id' => $usuario_acl_acao_id,
                    'criacao' => date('Y-m-d H:i:s'),
                    'alteracao_usuario_id' => $this->session->userdata('usuario_id')
                );
        }
        return $data;
    }



    function insert_form($array = array())
    {
        $data = $this->get_form_data();
        $this->delete_by_recurso($this->input->post('usuario_acl_recurso_id'));
        $this->insert_array($data);
    }


    function delete_by_recurso($usuario_acl_recurso_id){

        return $this->delete_by( array('usuario_acl_recurso_id' => $usuario_acl_recurso_id));

    }


    function get_acoes_by_recurso($usuario_acl_recurso_id)
    {


        $this->_database->where('usuario_acl_recurso_id', $usuario_acl_recurso_id );
        $rows = $this->get_all();

        $data = array();

        foreach($rows as $row){

            $data[$row['usuario_acl_acao_id']] = $row['usuario_acl_acao_id'];
        }

        return $data;
    }

    function get_acoes_grouped_by_recurso()
    {


        $rows = $this->get_all();
        $data = array();

        foreach($rows as $row){

            $data[$row['usuario_acl_recurso_id']][$row['usuario_acl_acao_id']] = true;
        }

        return $data;
    }



}
