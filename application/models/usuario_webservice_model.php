<?php
Class Usuario_Webservice_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'usuario_webservice';
    protected $primary_key = 'usuario_webservice_id';

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

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function getByAPI_KEY($key){



        $this->_database->where('api_key', $key );
        $this->_database->limit(1);

        $query = $this->get_all();

        if($query){

            return $query[0];

        }else {

            return false;
        }

    }


}
