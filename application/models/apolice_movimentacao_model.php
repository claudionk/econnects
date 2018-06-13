<?php
Class Apolice_Movimentacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'apolice_movimentacao';
    protected $primary_key = 'apolice_movimentacao_id';

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

    public function insMovimentacao($tipo, $apolice_id){

        try{
            $this->load->model('apolice_movimentacao_tipo_model', 'tipo');

            $tipo = $this->tipo->filter_by_slug($tipo)->get_all();
            $tipo = $tipo[0];

            $dados_mov = array();

            $dados_mov['apolice_movimentacao_tipo_id'] = $tipo['apolice_movimentacao_tipo_id'];
            $dados_mov['integracao_log_detalhe_id'] = 0;
            $dados_mov['apolice_id'] = $apolice_id;

            $this->insert($dados_mov, TRUE);
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }


    }


    function get_by_id($id)
    {
        return $this->get($id);
    }


}
