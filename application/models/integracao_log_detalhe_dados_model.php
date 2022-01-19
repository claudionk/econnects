<?php
Class Integracao_Log_Detalhe_Dados_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao_log_detalhe_dados';
    protected $primary_key = 'integracao_log_detalhe_dados_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;
    protected $soft_insert = FALSE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();

    public function insLogDetalheDados($dados = []){
        return $this->insert($dados, TRUE);
    }

    function filterByLogID($integracao_log_id){
        $this->_database->where("integracao_log_detalhe_dados.integracao_log_detalhe_id", $integracao_log_id);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    public function getDadosByArquivo($integracao_log_id, $codigo = null, $tipo_segurado = [])
    {
        $this->_database->select("cliente_mailing.*");
        $this->_database->join("integracao_log_detalhe", "integracao_log_detalhe.integracao_log_detalhe_id = {$this->_table}.integracao_log_detalhe_id");
        $this->_database->join("cliente_mailing", "cliente_mailing.codigo = {$this->_table}.codigo");
        $this->_database->where("integracao_log_detalhe.integracao_log_id", $integracao_log_id);
        $this->_database->where("integracao_log_detalhe.integracao_log_status_id", 4);
        $this->_database->where("integracao_log_detalhe.deletado", 0);

        if ( !empty($codigo) )
        {
            $this->_database->where("{$this->_table}.codigo", $codigo);
        }

        if ( !empty($tipo_segurado) )
        {
            $this->_database->where_in("{$this->_table}.tipo_segurado", $tipo_segurado);
        }

        $this->_database->order_by("{$this->_table}.codigo", "ASC");
        return $this;
    }

    public function get_by_integracao_log_id($integracao_log_id){
        $SQL = "SELECT
            ildd.*
        FROM
            integracao_log_detalhe AS ild

        INNER JOIN
            integracao_log_detalhe_dados AS ildd
            ON ildd.integracao_log_detalhe_id = ild.integracao_log_detalhe_id
            AND ildd.deletado = 0
            
        WHERE 1 = 1
            AND ild.deletado = 0
            AND ild.integracao_log_id = $integracao_log_id";

        $query = $this->_database->query($SQL);
        return $query->result_array();
    }

}
