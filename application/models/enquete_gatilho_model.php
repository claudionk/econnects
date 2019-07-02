
<?php

class Enquete_gatilho_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = "enquete_gatilho";
    protected $primary_key = "enquete_gatilho_id";

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    public $validate = array(

        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'parametro',
            'label' => 'Parâmetro',
            'rules' => 'required',
            'groups' => 'default',
        ),
    );

}

