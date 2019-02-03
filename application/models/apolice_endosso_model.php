<?php
Class Apolice_Endosso_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'apolice_endosso';
    protected $primary_key = 'apolice_endosso_id';

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

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_apolice_id($apolice_id) {
        $this->_database->where("{$this->_table}.apolice_id", $apolice_id);
        return $this;
    }

    function getProdutoParceiro($apolice_id) {
        $this->_database->select('pa.slug, pa.codigo_sucursal');
        $this->_database->from($this->_table);
        $this->_database->join("apolice a", "a.apolice_id = {$this->_table}.apolice_id", "inner");
        $this->_database->join("pedido p", "p.pedido_id = a.pedido_id", "inner");
        $this->_database->join("cotacao c", "c.cotacao_id = p.cotacao_id", "inner");
        $this->_database->join("produto_parceiro pp", "pp.produto_parceiro_id = c.produto_parceiro_id", "inner");
        $this->_database->join("parceiro pa", "pa.parceiro_id = pp.parceiro_id", "inner");

        $this->_database->where("a.apolice_id", $apolice_id);
        $this->_database->where('a.deletado', 0);
        $this->_database->where('p.deletado', 0);
        $this->_database->where('c.deletado', 0);
        $this->_database->where('pp.deletado', 0)
        $result = $this->get_all();
        if (!empty($result)) {
            return $result[0];
        }

        return null;
    }

    function max_seq_by_apolice_id($apolice_id) {
        $sequencia = 1;
        $endosso = 0;

        $this->_database->select_max('sequencial', 'seq_max');
        $this->_database->where("apolice_id", $apolice_id);
        $this->_database->where('integracao.deletado', 0);
        $result = $this->get_all();

        if (!empty($result)) {
            $sequencia = $result[0]['seq_max'] + 1;
            $endosso = $this->defineEndosso($sequencia, $apolice_id);
        }

        return [
            'sequencia' => $sequencia,
            'endosso' => $endosso,
        ];
    }

    public function defineTipo($tipo, $endosso, $capa = false) {
        /*
        Codigo do tipo de emissão:

        # ADESAO - A
        1 - Emissão da Apólice (nr_endosso = 0) - Sem Capa
        2 - Emissão da Apólice (nr_endosso = 0) - Com Capa
        3 - Demais parcelas   (nr_endosso <> 0)

        # ALTERAÇÃO - U
        4 - Alteração Cadastral

        # CANCELAMENTO - C
        5 - Cancelamento da apólice
        6 - Cancelamento por falta de pagamento
        */

        if ($tipo == 'A') {
            if ($endosso == '0') {
                $tipo = ($capa) ? 2 : 1;
            } else {
                $tipo = 3;
            }
        } elseif ($tipo == 'U') {
            $tipo = 4;
        } elseif ($tipo == 'C') {
            $tipo = 5;
        } elseif ($tipo == 'F') {
            $tipo = 6;
        }

        return $tipo;

    }

    public function defineEndosso($sequencial, $apolice_id) {
        $endosso = 0;

        if ($sequencial > 1 ) {
            $result = $this->getProdutoParceiro($apolice_id);
            if (!empty($result)) {

                if ($result['slug'] == 'generali') {
                    $endosso = $result['codigo_sucursal'] . '71'. str_pad($sequencial-1, 7, "0", STR_PAD_LEFT);
                }
            }
        }

        return $endosso;
    }

    public function insEndosso($tipo, $apolice_id, $produto_parceiro_pagamento_id, $parcela = 1){

        try{
            $this->load->model('produto_parceiro_pagamento_model', 'parceiro_pagamento');

            $dados_end = array();
            $dados_end['apolice_id'] = $apolice_id;

            // VALIDAÇÃO DE CAPA
            // caso seja recorrência terá capa
            if ($this->parceiro_pagamento->isRecurrent($produto_parceiro_pagamento_id)) {

                $capa = true;
                $dados_end['parcela'] = ($tipo == 'A') ? 0 : ;
                $dados_end['valor'] = 
                $dados_end['data_inicio_vigencia'] = 
                $dados_end['data_fim_vigencia'] = 

            } else {

                $capa = false;
                $dados_end['parcela'] = 1;
            }

            $seq_end = $this->max_seq_by_apolice_id($apolice_id);
            $dados_end['sequencial'] = $seq_end['sequencial'];
            $dados_end['endosso'] = $seq_end['endosso'];
            $dados_end['tipo'] = $this->defineTipo($tipo, $seq_end['endosso'], $capa);

            $this->insert($dados_end, TRUE);
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }


    }
}
