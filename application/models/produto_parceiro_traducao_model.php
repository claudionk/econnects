<?php
Class Produto_Parceiro_Traducao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_traducao';
    protected $primary_key = 'produto_parceiro_traducao_id';

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
            'field' => 'original',
            'label' => 'Original',
            'rules' => '',
            'groups' => 'default'
        ),array(
            'field' => 'traducao',
            'label' => 'Tradução',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'original' => $this->input->post('original'),
            'traducao' => $this->input->post('traducao')

        );
        return $data;
    }

    function  filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);

        return $this;
    }

    /**
     * @description faz a tradução de um texto
     * @param $texto
     * @param $produto_parceiro_id
     */
    public function traducao($texto, $produto_parceiro_id){

        $traducao = $this->get_many_by(array(
            'produto_parceiro_id' => $produto_parceiro_id,
            'original' => $texto,
        ));

        if($traducao){
            return $traducao[0]['traducao'];
        }else{
            $data_traducao = array();
            $data_traducao['produto_parceiro_id'] = $produto_parceiro_id;
            $data_traducao['original'] = $texto;
            $data_traducao['traducao'] = $texto;
            $data_traducao['criacao'] = date('Y-m-d H:i:s');
            $data_traducao['alteracao'] = date('Y-m-d H:i:s');
            $this->insert($data_traducao, TRUE);
            return $texto;
        }


    }

}
