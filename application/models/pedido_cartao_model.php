<?php

Class Pedido_Cartao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'pedido_cartao';
    protected $primary_key = 'pedido_cartao_id';

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
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );


    //Retorna por slug
    function filter_by_pedido($pedido_id)
    {
        $this->_database->where('pedido_cartao.pedido_id', $pedido_id);
        //$this->_database->where('pedido_cartao.processado', 0);
        $this->_database->where('pedido_cartao.deletado', 0);
        $this->_database->where('pedido_cartao.ativo', 1);
        $this->_database->order_by('pedido_cartao.criacao', 'DESC');
        $this->_database->limit(1);
        return $this;
    }



    function get_last_transacao($pedido_id)
    {
        $sql = "
                    select pedido_cartao_transacao.*
                    from pedido_cartao 
                    inner join pedido_cartao_transacao ON pedido_cartao.pedido_cartao_id = pedido_cartao_transacao.pedido_cartao_id
                    
                    WHERE pedido_cartao.pedido_id = {$pedido_id} 
                    ORDER BY pedido_cartao_transacao.pedido_cartao_transacao_id DESC
                    LIMIT 1
            ";

        return  $this->_database->query($sql)->result_array();
    }

    function get_cartao_debito_pendente($pedido_id)
    {
        $sql = "
                    select *
                    from pedido_cartao 
                    inner join pedido_cartao_transacao ON pedido_cartao.pedido_cartao_id = pedido_cartao_transacao.pedido_cartao_id
                    
                    WHERE 
                         pedido_cartao.pedido_id = {$pedido_id}
                    and  pedido_cartao_transacao.result = 'REDIRECT' 
                    and  pedido_cartao_transacao.processado = 0 
                    ORDER BY pedido_cartao_transacao.pedido_cartao_transacao_id DESC
                    LIMIT 1
            ";

        return  $this->_database->query($sql)->result_array();
    }


    public function insert_cartao($pedido_id, $row)
    {
        $this->load->library('encrypt');

        if(isset($row) && is_array($row))
        {
            $row['numero'] = $this->encrypt->encode($row['numero']);
            $row['validade'] = $this->encrypt->encode($row['validade']);
            $row['nome'] = $this->encrypt->encode($row['nome']);
            $row['bandeira'] = $this->encrypt->encode($row['bandeira']);
            $row['codigo'] = $this->encrypt->encode($row['codigo']);
            $row['pedido_id'] = $pedido_id;

            unset($row['nome_cartao']);
            unset($row['pedido_cartao_id']);

            return $this->insert($row, true);
        }

        return false;
    }



    public function decode_cartao($row)
    {
        $this->load->library('encrypt');

        if(isset($row) && is_array($row))
        {
            $row['numero'] = $this->encrypt->decode($row['numero']);
            $row['validade'] = $this->encrypt->decode($row['validade']);
            $row['nome'] = $this->encrypt->decode($row['nome']);
            $row['bandeira'] = $this->encrypt->decode($row['bandeira']);
            $row['bandeira_cartao'] = $this->encrypt->decode($row['bandeira_cartao']);

            return $row;
        }

        return array();
    }



    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
