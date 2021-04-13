<?php
Class Equipamentos_Elegiveis_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'equipamentos_elegiveis';
    protected $primary_key = 'equipamento_id';

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
            'field' => 'equipamento_marca_id',
            'label' => 'Marca',
            'rules' => 'required',
            'groups' => 'default',
            // 'foreign' => 'equipamento_marca'
        ),
        array(
            'field' => 'equipamento_categoria_id',
            'label' => 'Categoria',
            'rules' => 'required',
            'groups' => 'default',
            // 'foreign' => 'equipamento_categoria'
        ), 
        array(
            'field' => 'equipamento_sub_categoria_id',
            'label' => 'Sub Categoria',
            'rules' => '',
            'groups' => 'default',
            // 'foreign' => 'equipamento_categoria',
            // 'foreign_key' => 'equipamento_categoria_id',
            // 'foreign_join' => 'left'
        ), 
        array(
            'field' => 'ean',
            'label' => 'EAN',
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
            'field' => 'tags',
            'label' => 'TAGS',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'marca',
            'label' => 'Marca',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'skus',
            'label' => 'SKUS',
            'rules' => '',
            'groups' => 'default'
        )
    );

    public function whith_multiples_ids($values = []) {
        $this->db->where_in('equipamento_id', $values);
        return $this;
    }

    public function whith_linhas() {
        $this->db->select("el.nome as categoria");
        $this->db->join("equipamentos_elegiveis_categoria el", "{$this->_table}.equipamento_sub_categoria_id = el.equipamento_categoria_id", "left");
        return $this;
    }

    public function match($lista_id = 1, $equipamento, $marca = null, $limit = 10, $categoria = null)
    {
        $where='';
        if (!empty($marca)) {
            $marca = $this->trata_string_match($marca);
            $where .= " AND MATCH(em.nome) AGAINST('{$marca}' IN BOOLEAN MODE) > 0 ";
        }

        if (!empty($categoria)) {
            $categoria = $this->trata_string_match($categoria);
            $where .= " AND MATCH(el.nome) AGAINST('{$categoria}' IN BOOLEAN MODE) > 0 ";
        }

        $equipamento_tratado = $this->trata_string_match($equipamento);

        $equip = $this->_database->query("
            SELECT MATCH(e.nome) against('{$equipamento_tratado}' IN BOOLEAN MODE) as indice, e.equipamento_id, e.equipamento_marca_id, e.equipamento_categoria_id, eCat.nome AS categoria, e.nome, e.ean, e.equipamento_sub_categoria_id
            FROM equipamentos_elegiveis e
            INNER JOIN equipamentos_elegiveis_marca em on e.equipamento_marca_id = em.equipamento_marca_id
            INNER JOIN equipamentos_elegiveis_categoria el on e.equipamento_categoria_id = el.equipamento_categoria_id
            LEFT JOIN equipamentos_elegiveis_categoria eCat on e.equipamento_sub_categoria_id = eCat.equipamento_categoria_id
            WHERE e.lista_id = $lista_id
                AND MATCH(e.nome) AGAINST('{$equipamento_tratado}' IN BOOLEAN MODE) > 0
                {$where}
            ORDER BY 1 DESC
            LIMIT {$limit}
        ");

        $row = null;
        if ($equip){
            $row = $equip->result();
        }

        return $row;
    }

    public function get_equipamentos($equipamento_categoria_id = null, $equipamento_marca_id = null)
    {
        if (empty($equipamento_categoria_id) && empty($equipamento_marca_id))
            return null;

        $where='1=1';
        if (!empty($equipamento_categoria_id)) {
            $where .= " AND e.equipamento_categoria_id = $equipamento_categoria_id";
        }

        if (!empty($equipamento_marca_id)) {
            $where .= " AND e.equipamento_marca_id = $equipamento_marca_id";
        }

        $equip = $this->_database->query("
            SELECT e.equipamento_id, e.equipamento_marca_id, e.equipamento_categoria_id, e.nome, e.ean, e.equipamento_sub_categoria_id
            FROM {$this->_table} e
            INNER JOIN equipamentos_elegiveis_marca em on e.equipamento_marca_id = em.equipamento_marca_id
            WHERE {$where}
            ORDER BY 1 DESC
        ");
        $row = null;
        if ($equip){
            $row = $equip->result();
        }

        return $row;
    }

    public function trata_string_match($string){
        if(empty($string)) return $string;

        $string = trim($string);
        $string = str_replace(' e ', ' ', $string);
        $string = str_replace('\'', '', $string);
        $string = str_replace('(', '', $string);
        $string = str_replace(')', '', $string);
        $string = str_replace('-', ' ', $string);
        $string = str_replace('+', '', $string);
        $string = str_replace('*', '', $string);
        $string = str_replace('>', '', $string);
        $string = str_replace('<', '', $string);
        $string = str_replace('~', '', $string);
        $string = str_replace('"', '', $string);
        // $string = str_replace( "CELULAR", "", strtoupper( $string ) );
        // $string = str_replace( "CEL", "", strtoupper( $string ) );
        $string = str_replace( "  ", " ", strtoupper( $string ) );
        $string = str_replace( " ", "* ", strtoupper( $string ) );

        //$string = preg_replace('/\s+/', '$1* ', $string);
        $string = preg_replace('/\s+[\W]\s+/', '$1', ' '.$string.' ');

        $string = trim($string);
        $string .= (isset($string) && substr($string, -1) != "*") ? "*" : "";

        return $string;
    }

    public function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    public function filterByEAN($ean='')
    {
        $this->_database->where("{$this->_table}.ean", $ean);
        return $this;
    }

}