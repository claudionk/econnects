<?php
Class Parceiro_Tipo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'parceiro_tipo';
    protected $primary_key = 'parceiro_tipo_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');

    const TIPO_SEGURADORA = 'seguradora';

    const TIPO_CORRETORA = 'corretora';



    //Dados
    public $validate = array(
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo_interno',
            'label' => 'Código',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'codigo_interno' => $this->input->post('codigo_interno')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function getIdByCodigoInterno($codigo){



        $this->_database->where('codigo_interno', $codigo );
        $this->_database->limit(1);

        $query = $this->get_all();

        if($query){

            return $query[0][$this->primary_key];

        }else {

            return false;
        }

    }

    function getIdTipoCorretora(){

        return $this->getIdByCodigoInterno(self::TIPO_CORRETORA);
    }

    function getIdTipoSeguradora(){

        return $this->getIdByCodigoInterno(self::TIPO_SEGURADORA);
    }




}
