<?php
Class Equipamento_Categoria_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'vw_Equipamentos_Linhas';
    protected $primary_key = 'equipamento_categoria_id';

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
            'field' => 'equipamento_categoria_pai',
            'label' => 'Categoria Pai',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'equipamento_categoria_mestre',
            'label' => 'Categoria Mestre',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'equipamento_categoria_nivel',
            'label' => 'Nível',
            'rules' => 'required',
            'groups' => 'default'
        ),
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
            'field' => 'codigo',
            'label' => 'Código',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    function filter_by_nviel($nivel){
        $this->_database->where("{$this->_table}.equipamento_categoria_nivel", $nivel);
        return $this;
    }

    function with_sub_categoria($categoria_pai_id = 0, $equipamento_marca_id = 0){
        $this->_database->distinct();
        $this->_database->join("vw_Equipamentos e", "e.equipamento_sub_categoria_id = {$this->_table}.equipamento_categoria_id");
        $this->_database->where("e.deletado", 0);
        $this->_database->where("{$this->_table}.equipamento_categoria_nivel > 1", NULL, FALSE);
        if (!empty($categoria_pai_id)) $this->_database->where("e.equipamento_categoria_id", $categoria_pai_id);
        if (!empty($equipamento_marca_id)) $this->_database->where("e.equipamento_marca_id", $equipamento_marca_id);
        return $this;
    }

    public function whith_multiples_ids($values = []) {
        $this->db->where_in("{$this->_table}.equipamento_categoria_id", $values);
        return $this;
    }

}
