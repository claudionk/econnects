
<?php
class Enquete_gatilho_configuracao_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = "enquete_gatilho_configuracao";
    protected $primary_key = "enquete_gatilho_configuracao_id";

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    public $validate = array(

        array(
            'field' => 'enquete_configuracao_id',
            'label' => 'Configuração',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'enquete_configuracao',
        ),
        array(
            'field' => 'enquete_gatilho_id',
            'label' => 'Gatilho',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'enquete_gatilho',
        ),
    );

}

