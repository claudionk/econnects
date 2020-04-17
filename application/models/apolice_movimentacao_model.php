<?php
class Apolice_Movimentacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'apolice_movimentacao';
    protected $primary_key = 'apolice_movimentacao_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();

    //Dados
    public $validate = array(

    );

    public function insMovimentacao($tipo, $apolice_id, $pedido, $parcela = null)
    {

        try {
            $this->load->model('apolice_movimentacao_tipo_model', 'tipo');
            $this->load->model('apolice_endosso_model', 'apolice_endosso');
            // $this->load->model('apolice_chave_model', 'apolice_chave');

            $tipo_slug = $tipo;
            $tipo      = $this->tipo->filter_by_slug($tipo)->get_all();
            $tipo      = $tipo[0];

            $dados_mov                                 = array();
            $dados_mov['apolice_movimentacao_tipo_id'] = $tipo['apolice_movimentacao_tipo_id'];
            $dados_mov['integracao_log_detalhe_id']    = 0;
            $dados_mov['apolice_id']                   = $apolice_id;

            $this->insert($dados_mov, true);

            $pedido_id                     = $pedido['pedido_id'];
            $produto_parceiro_pagamento_id = $pedido['produto_parceiro_pagamento_id'];

            $this->apolice_endosso->insEndosso($tipo_slug, $tipo['apolice_movimentacao_tipo_id'], $pedido_id, $apolice_id, $produto_parceiro_pagamento_id, $parcela);

            // $this->apolice_chave->insChave($pedido_id, $apolice_id);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    public function get_by_id($id)
    {
        return $this->get($id);
    }

}
