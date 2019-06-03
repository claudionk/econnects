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

    function lastSequencial($apolice_id) {
        $this->_database->where('apolice_id', $apolice_id);
        $this->_database->where('deletado', 0);
        $this->_database->order_by('sequencial', 'DESC');
        $this->_database->limit(1);
        $result = $this->get_all();
        if (!empty($result)) {
            return $result[0];
        }

        return null;
    }

    /**
     * Retorna a última parcela registrada da apólice
     * @param int $apolice_id
     * @return array
     * @author Davi Souto
     * @since  08/04/2019
     */
    function lastParcela($apolice_id, $parcela = null, $apolice_movimentacao_tipo_id = null) {

        if ( !empty($parcela) ) {
            $this->_database->where('parcela', $parcela);
        }

        if ( !empty($apolice_movimentacao_tipo_id) ) {
            $this->_database->where('apolice_movimentacao_tipo_id', $apolice_movimentacao_tipo_id);
        }

        $this->_database->where('apolice_id', $apolice_id);
        $this->_database->where('deletado', 0);
        $this->_database->order_by('parcela', 'DESC');
        $this->_database->order_by('sequencial', 'DESC');
        $this->_database->limit(1);
        $result = $this->get_all();
        if (!empty($result)) {
            return $result[0];
        }

        return null;
    }

    function max_seq_by_apolice_id($apolice_id) {
        $sequencia = 1;
        $endosso = 0;

        $result = $this->lastSequencial($apolice_id);

        // tratamento para gerar o endosso e o sequencial
        if (!empty($result)) {
            $sequencia = empty($result['sequencial']) ? 1 : $result['sequencial'] + 1;
            $endosso = $this->defineEndosso($sequencia, $apolice_id);
        }

        return [
            'sequencial'    => $sequencia,
            'endosso'       => $endosso,
        ];
    }

    /**
     * Gerar o id da transação do registro
     * Formação: nr_apolice + nr_endosso + cd_ramo + nr_parcela
     * @param int $apolice_id
     * @param string $endosso
     * @param int $parcela
     * @return int
     * @author Cristiano Arruda
     * @since  08/04/2019
     */
    function getIDTransacao($apolice_id, $endosso, $parcela) {
        $id_transacao = '';
        $this->load->model('apolice_model', 'apolice');
        $dadosPP = $this->apolice->defineApoliceCliente($apolice_id);

        // tratamento para gerar o id da transacao
        if (!empty($dadosPP)) {
            $id_transacao = $dadosPP['num_apolice'].$endosso.$dadosPP['cod_ramo'].$parcela;
        }

        return $id_transacao;
    }

    /**
     * Código do Movimento de Cobrança Tipo Movto Cobrança
     *   01 - Cobrança
     *   02 - Restituição
     *   03 - Sem movto de prêmio
     * @param int $apolice_id
     * @param int $tipo
     * @param int $parcela
     * @param boolean $devolucao_integral
     * @return int
     * @author Cristiano Arruda
     * @since  08/04/2019
     */
    public function defineMovCob($tipo, $parcela, $devolucao_integral = true)
    {
        if ( $parcela == 0 ) {
            $cd_mov_cob = 3;
        } elseif ( $tipo == 'C') {
            $cd_mov_cob = ($devolucao_integral) ? 3 : 2;
        } else {
            $cd_mov_cob = 1;
        }

        return $cd_mov_cob;
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
        } elseif ($tipo == 'P') {
            $tipo = 3;
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

        if ( $sequencial > 1 ) {
            $this->load->model('apolice_model', 'apolice');
            $dadosPP = $this->apolice->getProdutoParceiro($apolice_id);
            if ( !empty($dadosPP) ) {
                if ($dadosPP['slug'] == 'generali') {
                    $endosso = $dadosPP['codigo_sucursal'] . $dadosPP['cod_ramo']. str_pad($sequencial-1, 7, "0", STR_PAD_LEFT);
                }
            }
        }

        return $endosso;
    }

    public function insEndosso($tipo, $apolice_movimentacao_tipo_id, $pedido_id, $apolice_id, $produto_parceiro_pagamento_id, $parcela = null, $valor = null, $devolucao_integral = true){
        try{
            $this->load->model('apolice_model', 'apolice');
            $this->load->model('produto_parceiro_pagamento_model', 'parceiro_pagamento');
            $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');

            $apolice = $this->apolice->getApolice($apolice_id);

            $dados_end = array();
            $dados_end['apolice_id']                    = $apolice_id;
            $dados_end['pedido_id']                     = $pedido_id;
            $dados_end['apolice_movimentacao_tipo_id']  = $apolice_movimentacao_tipo_id;
            $dados_end['valor']                         = ( !$valor ) ? $apolice['valor_premio_net'] : $valor;
            $dados_end['data_inicio_vigencia']          = $apolice['data_ini_vigencia'];
            $dados_end['data_fim_vigencia']             = $apolice['data_fim_vigencia'];

            $seq_end                    = $this->max_seq_by_apolice_id($apolice_id);
            $dados_end['sequencial']    = $seq_end['sequencial'];
            $dados_end['endosso']       = $seq_end['endosso'];

            $is_controle_endosso_pelo_cliente = $this->apolice->isControleEndossoPeloClienteByPedidoId($pedido_id);

            // VALIDAÇÃO DE CAPA
            // caso seja recorrência terá capa
            // Quando o controle de endosso é manual pelo cliente também entra aqui - Davi Souto 08/04/2019
            if ($this->parceiro_pagamento->isRecurrent($produto_parceiro_pagamento_id) || $is_controle_endosso_pelo_cliente) {

                $capa = true;
                $dados_end['parcela'] = (empty($parcela)) ? 0 : $parcela;
                $dados_end['valor'] = ($dados_end['parcela'] == 0) ? 0 : $dados_end['valor'];

                // valida a vigência
                // caso seja cancelamento, a vigência deve ser a mesma da parcela cancelada
                if ($dados_end['parcela'] > 0 && $tipo != 'C') {

                    if ($dados_end['parcela'] > 1) {
                        $result = $this->lastSequencial($apolice_id);

                        $d1 = new DateTime($result['data_fim_vigencia']);
                        $d1->add(new DateInterval("P1D"));
                        $dados_end['data_inicio_vigencia'] = $d1->format('Y-m-d');
                    }

                    $vigencia = $this->produto_parceiro_plano->getInicioFimVigenciaCapa($apolice['produto_parceiro_plano_id'], $dados_end['data_inicio_vigencia'], $is_controle_endosso_pelo_cliente);
                    $dados_end['data_inicio_vigencia']  = $vigencia['inicio_vigencia'];
                    $dados_end['data_fim_vigencia']     = $vigencia['fim_vigencia'];

                }

            } else {

                $capa = false;
                $dados_end['parcela'] = 1;

            }

            $dados_end['cd_movimento_cobranca'] = $this->defineMovCob($tipo, $dados_end['parcela'], $devolucao_integral);
            $dados_end['tipo']                  = $this->defineTipo($tipo, $dados_end['endosso'], $capa);
            $dados_end['id_transacao']          = $this->getIDTransacao($apolice_id, $dados_end['endosso'], $dados_end['parcela']);

            // caso seja cancelamento, a vigência deve ser a mesma da parcela cancelada
            if ( $tipo == 'C' ) {
                $result = $this->lastParcela($apolice_id, $dados_end['parcela']);

                $dados_end['data_inicio_vigencia']  = $result['data_inicio_vigencia'];
                $dados_end['data_fim_vigencia']     = $result['data_fim_vigencia'];
                $dados_end['valor']                 = $result['valor'];
                $dados_end['id_transacao_canc']     = $result['id_transacao'];
            }

            $this->insert($dados_end, TRUE);

            // gera o registro adicional na Adesão da Capa
            if ($tipo == 'A' && $dados_end['parcela'] == 0) {
                return $this->insEndosso($tipo, $apolice_movimentacao_tipo_id, $pedido_id, $apolice_id, $produto_parceiro_pagamento_id, 1);
            }

            return $dados_end;

        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }

    }

    public function updateEndosso($apolice_id){

        $ret = $this->get_many_by([
            'apolice_id' => $apolice_id
        ]);

        foreach ($ret as $r) {
            $up['id_transacao'] = $this->getIDTransacao($apolice_id, $r['endosso'], $r['parcela']);

            // no caso de cancelamento
            if ($r['apolice_movimentacao_tipo_id'] == 2)
            {
                // recupera a última parcela de emissão
                $result = $this->lastParcela($apolice_id, $r['parcela'], 1);
                $up['id_transacao_canc'] = $result['id_transacao'];
            }

            $this->update($r['apolice_endosso_id'], $up);
        }

        return true;
    }

}
