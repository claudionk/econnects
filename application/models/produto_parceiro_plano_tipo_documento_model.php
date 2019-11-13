<?php
class Produto_Parceiro_Plano_Tipo_Documento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'produto_parceiro_plano_tipo_documento';
    protected $primary_key = 'produto_parceiro_plano_tipo_documento_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');

    //Dados
    public $validate = array(
        array(
            'field'  => 'nome',
            'label'  => 'Nome',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'   => 'produto_parceiro_plano_id',
            'label'   => 'Produto / Parceiro / Plano',
            'rules'   => 'required',
            'groups'  => 'default',
            'foreign' => 'produto_parceiro_plano',
        ),
        array(
            'field'   => 'tipo_documento_id',
            'label'   => 'Documento',
            'rules'   => 'required',
            'groups'  => 'default',
            'foreign' => 'tipo_documento',
        ),
    );

    function with_tipo_documento(){
        $this->with_simple_relation('tipo_documento', '', 'tipo_documento_id', array('tipo_documento_id', 'nome', 'slug'));
        return $this;
    }

    public function get_by_id($id)
    {
        return $this->get($id);
    }

    public function filter_by_plano_id($produto_parceiro_plano_id)
    {
        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        return $this;
    }

    public function filter_by_plano_slug($plano_slug)
    {
        $this->_database->join('produto_parceiro_plano', "{$this->_table}.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id");
        $this->_database->where("produto_parceiro_plano.slug_plano", $plano_slug);
        $this->_database->where("produto_parceiro_plano.deletado", 0);
        return $this;
    }

    public function filter_by_tipo_doc_id($tipo_documento_id)
    {
        $this->_database->where("{$this->_table}.tipo_documento_id", $tipo_documento_id);
        return $this;
    }

    public function filter_by_tipo_doc_slug($tipo_documento_slug)
    {
        $this->_database->join('tipo_documento', "{$this->_table}.tipo_documento_id = tipo_documento.tipo_documento_id");
        $this->_database->where("tipo_documento.slug", $tipo_documento_slug);
        $this->_database->where("tipo_documento.deletado", 0);
        return $this;
    }

}
