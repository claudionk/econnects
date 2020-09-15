<?php
Class Usuario_Acl_Permissao_Model extends MY_Model
{



    protected $_table = 'usuario_acl_permissao';
    protected $primary_key = 'usuario_acl_permissao_id';

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
            'field' => 'permissoes',
            'label' => 'Permissões',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'usuario_acl_tipo_id',
            'label' => 'Tipo',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'usuario_acl_tipo'
        ),
        array(
            'field' => 'usuario_acl_acao_id',
            'label' => 'Ação',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'usuario_acl_acao'
        ),
        array(
            'field' => 'usuario_acl_recurso_id',
            'label' => 'Recurso',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'usuario_acl_recurso'
        )



    );

    function get_all_by_tipo($usuario_acl_tipo_id)
    {


        $this->_database->where('usuario_acl_tipo_id', $usuario_acl_tipo_id );
        $rows = $this->get_all();

        $data = array();

        foreach($rows as $row){

            $data[$row['usuario_acl_recurso_id']][$row['usuario_acl_acao_id']] = $row['usuario_acl_tipo_id'];
        }

        return $data;
    }



    function get_form_data($just_check = false){

        $data = array();

        $permissoes = $this->input->post('permissoes');
        $usuario_acl_tipo_id = $this->input->post('usuario_acl_tipo_id');


        foreach($permissoes as $usuario_acl_recurso_id => $acoes){

            foreach($acoes as $usuario_acl_acao_id){

                $data[] = array(
                       'usuario_acl_recurso_id' => $usuario_acl_recurso_id,
                       'usuario_acl_acao_id' => $usuario_acl_acao_id ,
                       'usuario_acl_tipo_id' => $usuario_acl_tipo_id,
                       'criacao' => date('Y-m-d H:i:s'),
                       'alteracao_usuario_id' => $this->session->userdata('usuario_id')
                );
            }

        }

       return $data;
    }



    function insert_form($array = array())
    {
        $data = $this->get_form_data();
        $this->delete_by_tipo($this->input->post('usuario_acl_tipo_id'));
        $this->insert_array($data);
    }


    function delete_by_tipo($usuario_acl_tipo_id){

        return $this->delete_by( array('usuario_acl_tipo_id' => $usuario_acl_tipo_id));

    }

    public function update_permissoes($tipo_id, $recurso_acoes)
    {
        $this->delete_by(array(
            'usuario_acl_tipo_id' => $tipo_id
        ));

        $i = 0;
        $recursos = array_keys($recurso_acoes);

        foreach ($recurso_acoes as $rec_array)
        {
            $x = 0;
            $acoes = array_keys($rec_array);
            foreach ($rec_array as $ac_array)
            {

                if(!$this->insert(array(
                    'usuario_acl_tipo_id' => $tipo_id,
                    'usuario_acl_acao_id' => $acoes[$x],
                    'usuario_acl_recurso_id' => $recursos[$i]
                ), true))
                {
                    return false;
                }

                $x++;
            }
            $i++;
        }
        return true;
    }

    function verify_permission($usuario_tipo_id, $acao, $recurso)
    {
        $this->_database->join('usuario_acl_recurso', $this->_table . '.usuario_acl_recurso_id = usuario_acl_recurso.usuario_acl_recurso_id AND usuario_acl_recurso.deletado = 0');
        $this->_database->join('usuario_acl_acao', $this->_table . '.usuario_acl_acao_id = usuario_acl_acao.usuario_acl_acao_id AND usuario_acl_acao.deletado = 0');

        $this->_database->where("{$this->_table}.usuario_acl_tipo_id", $usuario_tipo_id);
        $this->_database->where('usuario_acl_recurso.controller', $recurso);
        $this->_database->where('usuario_acl_acao.slug', $acao);
        $this->_database->limit(1);
        $row = $this->get_all();

        if ($row) {
            return true;
        } else {
            return false;
        }
    }

}
