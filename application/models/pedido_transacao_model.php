<?php

Class Pedido_Transacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'pedido_transacao';
    protected $primary_key = 'pedido_transacao_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;
    
    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('mensagem', 'descricao');

    //Dados
    public $validate = array(

        array(
            'field' => 'pedido_status_id',
            'label' => 'Pedido status',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'pedido_status'
        ),
        array(
            'field' => 'mensagem',
            'label' => 'Mensagem',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => 'required',
            'groups' => 'default',
        ),
    );

    function insStatus($pedido_id, $slug_status, $descricao = ''){

        $this->load->model('pedido_status_model', 'status');
        $this->load->model('pedido_model', 'pedido');

        $status = $this->status->get_by_slug($slug_status);

        if(count($status) > 0){


            $user = $this->session->userdata('usuario_id');
            $data_trans = array();
            $data_trans['pedido_id'] = $pedido_id;
            $data_trans['pedido_status_id'] = $status['pedido_status_id'];
            $data_trans['mensagem'] = $status['nome'];
            $data_trans['descricao'] = $descricao;
            $data_trans['alteracao_usuario_id'] = ($user) ? $user : 0;

            $pedido_transacao_id = $this->insert($data_trans, TRUE);

            $data_pedido = array();
            $data_pedido['status_data'] = date('Y-m-d H:i:s');
            $data_pedido['pedido_status_id'] = $status['pedido_status_id'];
            $this->pedido->update($pedido_id, $data_pedido, TRUE);
            return $pedido_transacao_id;



        }else{
            return FALSE;
        }


    }


    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
