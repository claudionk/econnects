<?php
Class Qualificacao_Regua_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'qualificacao_regua';
    protected $primary_key = 'qualificacao_regua_id';

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
        array(
            'field' => 'regua_logica',
            'label' => 'Lógica',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'regua_valor',
            'label' => 'Valor',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'pontos',
            'label' => 'Pontos',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'regua_logica' => $this->input->post('regua_logica'),
            'regua_valor' => $this->input->post('regua_valor'),
            'pontos' => $this->input->post('pontos'),

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


    function get_regua_valor_options(){

        return array(
            'N1' => 'N1',
            'N2' => 'N2',
            'N3' => 'N3',
            'N4' => 'N4',
            'N5' => 'N5'
        );
    }

    function get_regua_valor_by_logica($regua_logica){



        $this->_database->where('regua_logica', $regua_logica );

        return $this->get_all();

    }
    function get_regua_logica_options(){

        return array(

            'N2' => 'N2',
            'N3' => 'N3',
            'N4' => 'N4',
            'N5' => 'N5'
        );
    }

    function get_regua_logica($regua){


        $regra =  array(

            'N5' => array(
                'N1' => 0,
                'N2' => 25,
                'N3' => 50,
                'N4' => 75,
                'N%' => 100
            ),
            'N4' => array(
                'N1' => 0,
                'N2' => 33,
                'N3' => 66,
                'N4' => 100
            ),
            'N3' => array(
                'N1' => 0,
                'N2' => 50,
                'N3' => 100
            ),
            'N2' => array(
                'N1' => 0,
                'N2' => 100
            )
        );

        return $regra[$regua];

    }


}
