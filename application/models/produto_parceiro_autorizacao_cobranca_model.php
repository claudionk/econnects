<?php
Class Produto_parceiro_autorizacao_cobranca_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_autorizacao_cobranca';
    protected $primary_key = 'produto_parceiro_autorizacao_cobranca_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'autorizacao_cobranca',
            'label' => 'Autorização de Cobrança',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'termo_data_ini',
            'label' => 'Data Início',
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
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'autorizacao_cobranca' => $this->input->post('autorizacao_cobranca'),
            'termo_data_ini' => app_dateonly_mask_to_mysql($this->input->post('termo_data_ini')),
            'termo_data_fim' => ($this->input->post('termo_data_fim') != '') ? app_dateonly_mask_to_mysql($this->input->post('termo_data_fim')) : NULL,
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

    function update_last_row($produto_parceiro_id, $slug, $termo_data_ini){
        $ano= substr($termo_data_ini, 6);
        $mes= substr($termo_data_ini, 3,-5);
        $dia= substr($termo_data_ini, 0,-8);
        $termo_data_ini = $ano."-".$mes."-".$dia;
        
        $this->_database->query("UPDATE produto_parceiro_autorizacao_cobranca
                                    SET termo_data_fim = DATE_ADD('{$termo_data_ini}', INTERVAL -1 DAY)
                                  WHERE produto_parceiro_id = {$produto_parceiro_id}
                                    AND slug = '{$slug}'
                                    AND deletado = 0
                                    AND termo_data_fim IS NULL");    
        return true;
    }    
}
