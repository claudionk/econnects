<?php
Class Produto_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto';
    protected $primary_key = 'produto_id';

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
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'produto_ramo_id',
            'label' => 'Ramo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'produto_ramo_id' => $this->input->post('produto_ramo_id'),
            'slug' => $this->input->post('slug')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_produto_ramo(){

        $this->with_simple_relation('produto_ramo', 'produto_ramo_', 'produto_ramo_id', array('nome'));
        return $this;
    }

    public function get_by_produto_parceiro_id($produto_parceiro_id){
        $SQL = "SELECT 
            * 
        FROM 
            sisconnects.produto AS p
        INNER JOIN
            sisconnects.produto_parceiro AS pp
            ON pp.produto_id = p.produto_id
            
        WHERE 1 = 1
            AND pp.produto_parceiro_id = $produto_parceiro_id";
        return $this->db->query($SQL)->result();
    }
}
