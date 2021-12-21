<?php
Class Equipamentos_Elegiveis_Marca_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'equipamentos_elegiveis_marca';
    protected $primary_key = 'equipamento_marca_id';

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
            'field' => 'descricao',
            'label' => 'Descrição',
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

    public function get_by_categoria($categoria_id)
    {
        $this->_database->where("{$this->_table}.equipamento_marca_id IN(SELECT DISTINCT equipamento_marca_id FROM equipamentos_elegiveis WHERE deletado = 0 AND equipamento_categoria_id = {$categoria_id})", NULL, FALSE);
        return $this;
    }

    public function whith_multiples_ids($values = []) {
        $this->db->where_in("{$this->_table}.equipamento_marca_id", $values);
        return $this;
    }
}
