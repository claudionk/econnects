
<?php
class Enquete_pergunta_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = "enquete_pergunta";
    protected $primary_key = "enquete_pergunta_id";

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    public $validate = array(

        array(
            'field' => 'enquete_id',
            'label' => 'Enquete',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'enquete',
        ),
        array(
            'field' => 'tipo',
            'label' => 'Tipo',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'pergunta',
            'label' => 'Pergunta',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'opcoes',
            'label' => 'Opções',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'ordem',
            'label' => 'Ordem',
            'rules' => 'required',
            'groups' => 'default',
        ),
    );


}

