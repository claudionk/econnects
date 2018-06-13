<?php

Class Cobertura_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cobertura';
    protected $primary_key = 'cobertura_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;
    
    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'descricao');

    //Dados
    public $validate = array(
        array(
            'field' => 'cobertura_tipo_id',
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
            'field' => 'produto_id',
            'label' => 'Produto',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'cobertura_tipo_id' => $this->input->post('cobertura_tipo_id'),
            'produto_id' => $this->input->post('produto_id'),
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
        );

        return $data;
    }


    //Agrega tipo de cobertura
    function with_cobertura_tipo($fields = array('nome'))
    {
        $this->with_simple_relation('cobertura_tipo', 'cobertura_tipo_', 'cobertura_tipo_id', $fields );
        return $this;
    }

    //Agrega Produto
    function with_cobertura_produto($fields = array('nome'))
    {
        $this->with_simple_relation('produto', 'produto_', 'produto_id', $fields );
        return $this;
    }


    function  filter_by_produto($produto_id){

        $this->_database->where("{$this->_table}.produto_id", $produto_id);

        return $this;
    }

    function getCoberturasProdutoParceiroPlano($produto_parceiro_id){

        $sql = "
                SELECT DISTINCT cobertura.* 
                FROM cobertura
                INNER JOIN cobertura_plano ON cobertura.cobertura_id = cobertura_plano.cobertura_id
                INNER JOIN produto_parceiro_plano ON produto_parceiro_plano.produto_parceiro_plano_id = cobertura_plano.produto_parceiro_plano_id
                WHERE
                 produto_parceiro_plano.produto_parceiro_id = {$produto_parceiro_id}
                 AND cobertura.deletado = 0
                 AND cobertura_plano.deletado = 0
                 AND produto_parceiro_plano.deletado = 0      
                 ORDER BY cobertura_plano.ordem                      
        ";

        return  $this->_database->query($sql)->result_array();

    }

    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}
