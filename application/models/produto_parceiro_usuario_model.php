<?php
Class Produto_Parceiro_Usuario_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_usuario';
    protected $primary_key = 'produto_parceiro_usuario_id';

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
            'field' => 'comissao',
            'label' => 'Comissão',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_id' => app_clear_number($this->input->post('produto_parceiro_id')),
            'comissao' => app_unformat_currency($this->input->post('comissao')),

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


    function getProdutosRelacionamento($usuario_id, $parceiro_id){


        $sql = "

            SELECT 
                produto_parceiro.produto_parceiro_id, 
                parceiro.nome_fantasia, 
                produto_parceiro.nome, 
                produto_parceiro_usuario.produto_parceiro_usuario_id,
                produto_parceiro_usuario.comissao_id
            FROM produto_parceiro 
            INNER JOIN parceiro ON parceiro.parceiro_id = produto_parceiro.parceiro_id 
            LEFT JOIN produto_parceiro_usuario ON (produto_parceiro_usuario.produto_parceiro_id = produto_parceiro.produto_parceiro_id AND produto_parceiro_usuario.usuario_id = {$usuario_id} AND produto_parceiro_usuario.deletado = 0) 
            WHERE 
              produto_parceiro.deletado = 0 AND 
              parceiro.deletado = 0 AND 
              produto_parceiro.parceiro_id = {$parceiro_id}
        ";

        $produtos =  $this->_database->query($sql)->result_array();

        $sql = "

                SELECT 
                   produto_parceiro.produto_parceiro_id, 
                   parceiro.nome_fantasia, 
                   produto_parceiro.nome, 
                   produto_parceiro_usuario.produto_parceiro_usuario_id,
                    produto_parceiro_usuario.comissao_id
                FROM produto_parceiro 
                INNER JOIN produto ON produto.produto_id = produto_parceiro.produto_id 
                INNER JOIN produto_parceiro_configuracao ON produto_parceiro.produto_parceiro_id = produto_parceiro_configuracao.produto_parceiro_id 
                INNER JOIN parceiro_relacionamento_produto ON produto_parceiro.produto_parceiro_id = parceiro_relacionamento_produto.produto_parceiro_id 
                INNER JOIN parceiro ON produto_parceiro.parceiro_id = parceiro.parceiro_id 
                LEFT JOIN produto_parceiro_usuario ON (produto_parceiro_usuario.produto_parceiro_id = produto_parceiro.produto_parceiro_id AND produto_parceiro_usuario.usuario_id = {$usuario_id} AND produto_parceiro_usuario.deletado = 0)
                WHERE 
                    produto_parceiro.deletado = 0 AND 
                    parceiro_relacionamento_produto.parceiro_id = {$parceiro_id} AND 
                    parceiro_relacionamento_produto.deletado = 0 AND 
                    produto_parceiro_configuracao.deletado = 0 AND 
                    produto.deletado = 0 

        ";




        $produtos_relacionamento = $this->_database->query($sql)->result_array();

        return array_merge($produtos, $produtos_relacionamento);

    }

}
