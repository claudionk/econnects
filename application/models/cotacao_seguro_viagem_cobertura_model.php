<?php
Class Cotacao_Seguro_Viagem_Cobertura_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cotacao_seguro_viagem_cobertura';
    protected $primary_key = 'cotacao_seguro_viagem_cobertura_id';

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

    );

    function with_cobertura()
    {

        $this->_database->select('cobertura_plano.preco');
        $this->_database->select('cobertura_plano.porcentagem');
        $this->_database->select('cobertura.nome');
        $this->_database->join('cobertura_plano', 'cobertura_plano.cobertura_plano_id = cotacao_seguro_viagem_cobertura.cobertura_plano_id');
        $this->_database->join('cobertura', 'cobertura.cobertura_id = cobertura_plano.cobertura_id');
        $this->_database->where("cobertura_plano.deletado", 0);
        $this->_database->where("cobertura.deletado", 0);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    function with_cotacao($cotacao_id)
    {
        $this->_database->select('cotacao_seguro_viagem.*');
        $this->_database->join('cotacao_seguro_viagem', 'cotacao_seguro_viagem.cotacao_seguro_viagem_id = cotacao_seguro_viagem_cobertura.cotacao_seguro_viagem_id');
        $this->_database->where("cotacao_seguro_viagem.deletado", 0);
        $this->_database->where("cotacao_seguro_viagem.cotacao_id", $cotacao_id);
        return $this;
    }
}
