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

    function lastSequencial($apolice_id, $apolice_movimentacao_tipo_id = null) {
        if ( !empty($apolice_movimentacao_tipo_id) )
            $this->_database->where('apolice_movimentacao_tipo_id', $apolice_movimentacao_tipo_id);

        $this->_database->where('apolice_id', $apolice_id);
        $this->_database->where('deletado', 0);
        $this->_database->order_by('sequencial', 'DESC');
        $this->_database->order_by('parcela', 'DESC');
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
    function lastParcela($apolice_id, $parcela = null, $apolice_movimentacao_tipo_id = null, $data_inicio_vigencia = null) {

        if ( !is_null($parcela) )
            $this->_database->where('parcela', $parcela);

        if ( !empty($apolice_movimentacao_tipo_id) )
            $this->_database->where('apolice_movimentacao_tipo_id', $apolice_movimentacao_tipo_id);

        if ( !empty($data_inicio_vigencia) )
            $this->_database->where('data_inicio_vigencia', $data_inicio_vigencia);

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

    /**
     * Retorna a última parcela registrada da apólice
     * @param int $apolice_id
     * @return array
     * @author Cristiano Arruda
     * @since  03/04/2020
     */
    function firstVecto($apolice_id, $data_cancelamento)
    {
        $this->_database->where('apolice_id', $apolice_id);
        $this->_database->where('apolice_movimentacao_tipo_id', 1);
        $this->_database->where("data_vencimento > '{$data_cancelamento}' ");
        $this->_database->where('deletado', 0);
        $this->_database->order_by('data_vencimento', 'ASC');
        $this->_database->limit(1);
        $result = $this->get_all();
        if (!empty($result)) {
            return $result[0];
        }

        return null;
    }

    function max_seq_by_apolice_id($apolice_id, $tipo_pagto = 0, $tipo = 'A', $multiplasVigencias = false, $cod_mov_cob = null)
    {
        $sequencia = 1;
        $endosso = 0;
        $result = $this->lastSequencial($apolice_id);

        // tratamento para gerar o endosso e o sequencial
        if (!empty($result))
        {
            // sequencial para o endosso e para a contagem do seq (são coisas distintas)
            $sequencia = emptyor($result['sequencial'], 0);
            $cd_movimento_cobranca = emptyor($result['cd_movimento_cobranca'], 0);

            // o sequencial é incrementado sempre que há alteração de movimento
            if ($cd_movimento_cobranca != $cod_mov_cob || $multiplasVigencias)
            {
                $sequencia++;
            }

            // Pagamento Unico
            if ($tipo_pagto == 0)
            {
                if ($tipo == 'A')
                {
                    $sequencia_end = 1;
                }
                if ($tipo == 'C')
                {
                    $sequencia_end = 2;
                }
            } else {
                $sequencia_end = $sequencia;
            }

            $endosso = $this->defineEndosso($sequencia_end, $apolice_id);
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
     * @param int $tipo
     * @param int $parcela
     * @param boolean $devolucao_integral
     * @return int
     * @author Cristiano Arruda
     * @since  08/04/2019
     */
    public function defineMovCob($tipo, $parcela, $tipo_pagto, $devolucao_integral = true, $possui_restituicao = false)
    {
        // é o registro de capa
        if ( $parcela == 0 )
        {
            $cd_mov_cob = 3;
        }
        elseif ( $tipo == 'C')
        {
            // nao é parcelado
            if ( $tipo_pagto != 2 )
            {
                $cd_mov_cob = 2;
            }
            else
            {
                // devolução integral ou vencimento inferior a data de cancelamento, envia com restituição
                $cd_mov_cob = (($devolucao_integral && $parcela==1) || $possui_restituicao) ? 2 : 3;
            }
        }
        // emissão
        else
        {
            $cd_mov_cob = 1;
        }

        return $cd_mov_cob;
    }

    public function defineTipo($tipo, $endosso, $capa = false)
    {
        /*
        Codigo do tipo de emissão:

        # ADESAO - A
        1 -  1 - Emissão da Apólice (nr_endosso = 0) - Sem Capa
        2 - 18 - Emissão da Apólice (nr_endosso = 0) - Com Capa
        3 - 20 - Demais parcelas   (nr_endosso <> 0)

        # ALTERAÇÃO - U
        4 - 7 - Alteração Cadastral

        # CANCELAMENTO - C
        5 - 10 - Cancelamento da apólice
        6 - 11- Cancelamento por falta de pagamento
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

    public function defineEndosso($sequencial, $apolice_id)
    {
        $endosso = 0;

        if ( $sequencial > 1 )
        {
            $this->load->model('apolice_model', 'apolice');
            $dadosPP = $this->apolice->getProdutoParceiro($apolice_id);
            if ( !empty($dadosPP) )
            {
                if ($dadosPP['slug'] == 'generali')
                {
                    $endosso = $dadosPP['cod_sucursal'] . $dadosPP['cod_ramo'] . str_pad($sequencial-1, 7, "0", STR_PAD_LEFT);
                }
            }
        }

        return $endosso;
    }

    public function insEndosso($tipo, $apolice_movimentacao_tipo_id, $pedido_id, $apolice_id, $produto_parceiro_pagamento_id, $parcela = null, $valor = null)
    {
        try
        {
            $this->load->model('apolice_model', 'apolice');
            $this->load->model('apolice_cobertura_model', 'apolice_cobertura');
            $this->load->model('produto_parceiro_pagamento_model', 'parceiro_pagamento');

            $apolice = $this->apolice->getApolice($apolice_id);

            $dados_end = array();
            $dados_end['apolice_id']                    = $apolice_id;
            $dados_end['pedido_id']                     = $pedido_id;
            $dados_end['apolice_movimentacao_tipo_id']  = $apolice_movimentacao_tipo_id;
            $dados_end['valor']                         = ( !$valor ) ? $apolice['valor_premio_net'] : $valor;
            $dados_end['data_inicio_vigencia']          = $apolice['data_ini_vigencia'];
            $dados_end['data_fim_vigencia']             = $apolice['data_fim_vigencia'];
            $dados_end['data_vencimento']               = $apolice['data_adesao'];

            $controle_endosso = $this->apolice->isControleEndossoPeloClienteByPedidoId($pedido_id);
            $max_parcela = $controle_endosso['num_parcela'];
            $tipo_pagto = $controle_endosso['tipo_pagto'];
            $cob_vig = [];

            // Pagamento Unico
            if ( $tipo_pagto == 0 )
            {
                $cob_vig = $this->apolice_cobertura->filterByVigenciaCob($apolice_id)->get_all();
            }

            $dados = [
                'tipo'               => $tipo,
                'parcela'            => $parcela,
                'valor'              => $valor,
                'tipo_pagto'         => $tipo_pagto,
                'max_parcela'        => $max_parcela,
                'apolice'            => $apolice,
                'dados_endosso'      => $dados_end,
                'dados_cobertura'    => $cob_vig,
            ];

            return $this->insEndossoCore($dados);

        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }

    }

    private function insEndossoCore($dados)
    {
        try
        {
            $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
            $this->load->model('pedido_model', 'pedido');

            $apolice = $dados['apolice'];
            $dados_end = $dados['dados_endosso'];
            $max_parcela = $dados['max_parcela'];
            $tipo_pagto = $dados['tipo_pagto'];
            $parcela = $dados['parcela'];
            $tipo = $dados['tipo'];
            $valor = $dados['valor'];
            $contador = emptyor($dados['contador'], 1);
            $apolice_id = $dados_end['apolice_id'];
            $cob_vig = emptyor($dados['dados_cobertura'], []);
            $dados_end['cod_cobertura'] = null;

            $vcto_inferior_cancel = true;
            $geraDadosEndosso = true;
            $executaInsert = false;
            $multiplasVigencias = false;
            $parcelaRestituicao = false;

            // VALIDAÇÃO DE CAPA
            // caso seja recorrência terá capa
            // Quando o controle de endosso é manual pelo cliente também entra aqui - Davi Souto 08/04/2019
            if ($tipo_pagto)
            {
                $max_parcela = ($tipo_pagto == 1) ? 1 : $max_parcela;
                $dados_end['parcela'] = (empty($parcela)) ? ($tipo == 'A' ? 0 : 1) : $parcela;

                if ($dados_end['parcela'] == 0)
                {
                    $dados_end['valor'] = 0;
                } else 
                {
                    // se foi informado um valor fixo
                    if ( $valor )
                    {
                        $dados_end['valor'] = $valor;
                    } else
                    {
                        // Se for um pagamento parcelado
                        if ($tipo_pagto == 2)
                        {
                            $dados_end['valor'] = round($apolice['valor_premio_net'] / $max_parcela, 2);

                            // Acerta o valor da última parcela
                            $total_recalc = $dados_end['valor'] * $max_parcela;
                            if ($parcela == $max_parcela && $total_recalc != $apolice['valor_premio_net'] )
                                $dados_end['valor'] += $apolice['valor_premio_net'] - $total_recalc;

                        } else 
                        {
                            $dados_end['valor'] = $dados_end['valor'];
                        }
                    }
                }

                // valida a vigência
                // caso seja cancelamento, a vigência deve ser a mesma da parcela cancelada
                if ($dados_end['parcela'] > 0 && $tipo != 'C')
                {
                    if ($dados_end['parcela'] > 1)
                    {
                        $result = $this->lastSequencial($apolice_id, $dados_end['apolice_movimentacao_tipo_id']);

                        if ($tipo_pagto == 1)
                        {
                            $d1 = new DateTime($result['data_fim_vigencia']);
                            $d1->add(new DateInterval("P1D"));
                            $dados_end['data_inicio_vigencia']  = $d1->format('Y-m-d');
                        }
                        $dados_end['data_vencimento'] = $result['data_vencimento'];

                        $vigencia = $this->produto_parceiro_plano->getDatasCapa($apolice['produto_parceiro_plano_id'], $dados_end['data_inicio_vigencia'], $dados_end['data_vencimento'], $tipo_pagto);
                        $dados_end['data_vencimento'] = $vigencia['data_vencimento'];

                        // no mensal, a vigencia não se altera
                        if ($tipo_pagto != 2) {
                            $dados_end['data_inicio_vigencia']  = $vigencia['inicio_vigencia'];
                            $dados_end['data_fim_vigencia']     = emptyor($vigencia['fim_vigencia'], $dados_end['data_fim_vigencia']);
                        }
                    }

                }

            } else 
            {
                $dados_end['parcela'] = 1;

                // Pagamento Unico
                if ( $tipo_pagto == 0 )
                {
                    if ( count($cob_vig) > 1 && count($cob_vig) >= $contador )
                    {
                        $multiplasVigencias = true;
                        $dados_end['data_inicio_vigencia'] = emptyor($cob_vig[$contador-1]['data_inicio_vigencia'], $dados_end['data_inicio_vigencia']);
                        $dados_end['data_fim_vigencia']    = emptyor($cob_vig[$contador-1]['data_fim_vigencia'], $dados_end['data_fim_vigencia']);
                        $dados_end['cod_cobertura']        = emptyor($cob_vig[$contador-1]['cod_cobertura'], $dados_end['cod_cobertura']);

                        // Nota: Nao faz ajuste de valor pois deve apenas mudar o sequencial
                    }
                }
            }

            // estes dados represntam a soma dos dias da(s) cobertura(s) enviada(s)
            $datas = $this->pedido->define_dias_cancelamento($apolice_id, $apolice['data_cancelamento'], $dados_end['cod_cobertura'], $apolice);
            $devolucao_integral = $datas['devolucao_integral'];
            $dias_utilizados = $datas['dias_utilizados'];

            // Não retorna dados se possuir vigência encerrada
            if ( empty($datas) )
            {
                $geraDadosEndosso = false;
            }
            // caso seja cancelamento
            elseif ( $tipo == 'C' )
            {
                // NAO FAZ O CANCELAMENTO
                // Mensal: após X dias e após inicio da vigencia
                if ( $tipo_pagto == 1 && empty($devolucao_integral) && !empty($dias_utilizados) )
                {
                    return null;
                }

                $ap_mov_tip_id = null;
                $dt_ini_vig = null;

                // No PU com multiplas vignecias deve pegar o ultimo dado de emissão, para dar continuidade
                if ( $multiplasVigencias )
                {
                    $ap_mov_tip_id = 1;
                    $dt_ini_vig = $dados_end['data_inicio_vigencia'];
                }
                $result = $this->lastParcela($apolice_id, $dados_end['parcela'], $ap_mov_tip_id, $dt_ini_vig );

                $dados_end['data_fim_vigencia']     = $result['data_fim_vigencia'];
                $dados_end['valor']                 = $result['valor'];
                $dados_end['id_transacao_canc']     = $result['id_transacao'];
                $dados_end['data_vencimento']       = $result['data_vencimento'];

                // verifica se o vencimento é inferior ao cancelamento
                // o vencimento refere-se ao "inicio" do vencimento, e o conceito para tratativa abaixo é o "final" do vencimento
                $vigencia = $this->produto_parceiro_plano->getDatasCapa($apolice['produto_parceiro_plano_id'], $dados_end['data_inicio_vigencia'], $dados_end['data_vencimento'], $tipo_pagto);
                $vcto_inferior_cancel = (app_date_get_diff_mysql($apolice['data_cancelamento'], $dados_end['data_vencimento'], 'D') <= 0); // Inicio do vencimento
                $vcto_inferior_entre_cancel = (app_date_get_diff_mysql($apolice['data_cancelamento'], $vigencia['data_vencimento'], 'D') <= 0); // Final do vencimento
                $vigente = (app_date_get_diff_mysql($apolice['data_cancelamento'], $dados_end['data_fim_vigencia'], 'D') >= 0); // Cobertura Dentro de Vigência

                /***
                 *** INICIO DE VIGÊNCIA ***
                 ***/
                // Não é integral
                if ( empty($devolucao_integral) )
                {
                    // Unico: Depois da vigencia
                    // Parcelado: Para todas as parcelas canceladas
                    if ( ($tipo_pagto == 2 && $vcto_inferior_cancel) || !empty($dias_utilizados) )
                    {
                        // Não é integral e Após a Vigência
                        if ( !empty($dias_utilizados) )
                        {
                            // deverá informar data posterior a data do cancelamento, ou seja, D+1 da data de cancelamento
                            $d1 = new DateTime( $apolice['data_cancelamento'] );
                            $d1->add(new DateInterval('P1D'));
                            $dados_end['data_inicio_vigencia'] = $d1->format('Y-m-d');
                        }

                        // Parcelado
                        if ( $tipo_pagto == 2 )
                        {
                            // *NAO* gera dados para enviar caso o vencimento seja anterior ao cancelamento
                            if ( !empty($dias_utilizados) && $vcto_inferior_entre_cancel )
                            {
                                $geraDadosEndosso = false;
                            } 
                            elseif ($vcto_inferior_cancel) {
                                // possui restituição parcial
                                $parcelaRestituicao = true;
                            }
                        }
                    }
                }

                // Emq qualquer caso que a cobertura não esteja vigente
				if ( empty($vigente) )
					$geraDadosEndosso = false;
            }

            // Valida se deve gerar o registro da parcela
            if ( $geraDadosEndosso )
            {
                $dados_end['cd_movimento_cobranca'] = $this->defineMovCob($tipo, $dados_end['parcela'], $tipo_pagto, $devolucao_integral, $parcelaRestituicao);
                $seq_end                            = $this->max_seq_by_apolice_id($apolice_id, $tipo_pagto, $tipo, $multiplasVigencias, $dados_end['cd_movimento_cobranca']);
                $dados_end['sequencial']            = $seq_end['sequencial'];
                $dados_end['endosso']               = $seq_end['endosso'];
                $dados_end['tipo']                  = $this->defineTipo($tipo, $dados_end['endosso'], $tipo_pagto);
                $dados_end['id_transacao']          = $this->getIDTransacao($apolice_id, $dados_end['endosso'], $dados_end['parcela']);
                $this->insert($dados_end, TRUE);
            }

            /**** Gera o registro adicional na Adesão da Capa ou Cancelamento de Parcelado ****/

            // Pagamento Unico - Adesao/Cancelamento multiplas coberturas
            if ( $tipo_pagto == 0 && $multiplasVigencias && count($cob_vig) > $contador)
            {
                $contador++;
                $executaInsert = true;
            }
            // Mensal - Adesao apenas a primeira parcela
            elseif ( $tipo_pagto == 1 && $tipo == 'A' && $dados_end['parcela'] == 0 )
            {
                $executaInsert = true;
            }
            // Parcelado - Adesao/Cancelamento de todas as parcelas
            elseif ( $tipo_pagto == 2 && $dados_end['parcela'] < $max_parcela )
            {
                $executaInsert = true;
            }

            // executa a proxima ação no endosso
            if ($executaInsert)
            {
                $dados['parcela'] = $dados_end['parcela']+1;
                $dados['valor'] =  null;
                $dados['dados_endosso'] = $dados_end;
                $dados['contador'] =  $contador;

                return $this->insEndossoCore($dados);
            }

            return $dados_end;

        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }

    }

    public function updateEndosso($apolice_id)
    {
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
