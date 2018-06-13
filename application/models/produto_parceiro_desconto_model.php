<?php
Class Produto_Parceiro_Desconto_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_desconto';
    protected $primary_key = 'produto_parceiro_desconto_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('descricao');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_ini',
            'label' => 'Data de Início',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_fim',
            'label' => 'Data final',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'habilitado',
            'label' => 'habilitado',
            'rules' => 'required|callback_check_desconto_habilitado',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor',
            'label' => 'Valor Total',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
            'descricao' => $this->input->post('descricao'),
            'data_ini' => app_dateonly_mask_to_mysql($this->input->post('data_ini')),
            'data_fim' => app_dateonly_mask_to_mysql($this->input->post('data_fim')),
            'valor' => app_unformat_currency($this->input->post('valor')),
            'habilitado' => $this->input->post('habilitado')
        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


    function  filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);

        return $this;
    }

    public function is_desconto_habilitado($produto_parceiro_id){

        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        $this->_database->where('habilitado', 1);

        $rows = $this->get_all();

        if($rows){
            return TRUE;
        }else{
            return FALSE;
        }


    }


}
