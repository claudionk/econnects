<?php
Class Apolice_Cobertura_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'apolice_cobertura';
    protected $primary_key = 'apolice_cobertura_id';

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

    public function deleteByCotacao($cotacao_id){

        $this->db->where('cotacao_id', $cotacao_id);
        $this->db->delete($this->_table);

    }

    public function filterByApoliceID($apolice_id)
    {
        $this->db->where("{$this->_table}.apolice_id", $apolice_id);
        return $this;
    }

    /**
    * Retorna apenas registros de Emissão ou Cancelamento
    * @param char $tipo (A / C)
    * @return mixed
    */
    public function filterByTipo($tipo)
    {
        // Adesão
        if ($tipo == 'A')
        {
            $this->db->where("{$this->_table}.valor >= 0");
        } 
        // Cancelamento
        elseif ($tipo == 'C')
        {
            $this->db->where("{$this->_table}.valor <= 0");
        }
        return $this;
    }

    function filterByPedidoID($pedido_id){
        $this->_database->select("{$this->_table}.*, IFNULL(IFNULL(apolice_equipamento.valor_premio_net,apolice_generico.valor_premio_net),apolice_seguro_viagem.valor_premio_net) AS valor_premio_net", FALSE);
        $this->_database->join("apolice_equipamento","apolice_equipamento.apolice_id = {$this->_table}.apolice_id", "left");
        $this->_database->join("apolice_generico","apolice_generico.apolice_id = {$this->_table}.apolice_id", "left");
        $this->_database->join("apolice_seguro_viagem","apolice_seguro_viagem.apolice_id = {$this->_table}.apolice_id", "left");
        $this->_database->where("{$this->_table}.pedido_id", $pedido_id);
        $this->_database->where("{$this->_table}.deletado", 0);
        return $this;
    }

    function getByApoliceID($apolice_id){
        $this->_database->select("{$this->_table}.*, IFNULL(IFNULL(apolice_equipamento.valor_premio_net,apolice_generico.valor_premio_net),apolice_seguro_viagem.valor_premio_net) AS valor_premio_net", FALSE);
        $this->_database->join("apolice_equipamento","apolice_equipamento.apolice_id = {$this->_table}.apolice_id", "left");
        $this->_database->join("apolice_generico","apolice_generico.apolice_id = {$this->_table}.apolice_id", "left");
        $this->_database->join("apolice_seguro_viagem","apolice_seguro_viagem.apolice_id = {$this->_table}.apolice_id", "left");
        $this->_database->where("{$this->_table}.apolice_id", $apolice_id);
        return $this;
    }

    function filterByVigenciaCob($apolice_id){
        $this->_database->select("data_inicio_vigencia, data_fim_vigencia, cod_cobertura, apolice_cobertura_id");
        $this->_database->where("apolice_id", $apolice_id);
        $this->_database->group_by("data_inicio_vigencia, data_fim_vigencia");
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function OnlyCoberturas(){
        $this->_database->join("cobertura_plano","cobertura_plano.cobertura_plano_id = {$this->_table}.cobertura_plano_id");
        $this->_database->join("produto_parceiro_plano","produto_parceiro_plano.produto_parceiro_plano_id = cobertura_plano.produto_parceiro_plano_id");
        $this->_database->join("produto_parceiro","produto_parceiro.produto_parceiro_id = produto_parceiro_plano.produto_parceiro_id AND cobertura_plano.parceiro_id = produto_parceiro.parceiro_id");
        return $this;
    }

    /**
    * Retorna o valor do IOF e Prêmios de cada cobertura
    * @param int $apolice_id
    * @param int $pedido_id
    * @param int $apolice_movimentacao_tipo_id
    * @param char $cod_cobertura
    * @return mixed
    */
    function getValorIOF($apolice_id, $pedido_id, $apolice_movimentacao_tipo_id, $cod_cobertura = null)
    {
    	$this->load->model('apolice_model', 'apolice');

    	$controle_endosso = $this->apolice->isControleEndossoPeloClienteByPedidoId($pedido_id);

        // Pagamento Único
        if ($controle_endosso['tipo_pagto'] == 0)
        {
        	$query = $this->_database->query("CALL sp_cta_parcemissao_unico($apolice_id, $apolice_movimentacao_tipo_id, NULL)");
        } 
        // Pagamento Parcelado
        elseif ( $controle_endosso['tipo_pagto'] == 2 )
        {
	        $query = $this->_database->query("CALL sp_cta_parcemissao_parcelado($apolice_id, $apolice_movimentacao_tipo_id, NULL)");
        }
        else {
        	return null;
        }

        $reg = $query->result_array();
		$query->next_result();
		if ( empty($reg) ) return null;
		$coberturas = [];

		// soma os resultados por coberturas
		foreach ($reg as $key => $value)
		{
			if ( !empty($cod_cobertura) && $cod_cobertura != $value['cod_cobertura'] )
			{
				continue;
			}

			$coberturas[$value['cod_cobertura']] = [
				'premio_liquido' 		=> ( issetor($coberturas[$value['cod_cobertura']]['premio_liquido'], 0) + emptyor($value['premio_liquido'], 0) ),
				'valor_iof' 	 		=> ( issetor($coberturas[$value['cod_cobertura']]['valor_iof'], 0) 		+ emptyor($value['valor_iof'], 0) ),
				'premio_bruto' 	 		=> ( issetor($coberturas[$value['cod_cobertura']]['premio_bruto'], 0) 	+ emptyor($value['premio_liquido_total'], 0) ),
				'data_inicio_vigencia' 	=> issetor($coberturas[$value['cod_cobertura']]['data_inicio_vigencia'],  $value['ini_vig']),
				'data_fim_vigencia' 	=> issetor($coberturas[$value['cod_cobertura']]['data_fim_vigencia'], 	  $value['fim_vig']),
			];
		}

        return $coberturas;
    }

    // public function geraDadosCancelamento($pedido_id, $valor_base)
    public function geraDadosCancelamento($apolice_id, $valor_base, $produto_parceiro_plano_id = null, $ValuesCoberturas = [])
    {
        $valor_base = floatval( $valor_base );
        $coberturas = $this->getByApoliceID($apolice_id)->get_all();

        // Caso não tenha enviado o Id do Plano
        if ( empty($produto_parceiro_plano_id) )
        {
            $this->load->model('apolice_model', 'apolice');
            $apolice                   = $this->apolice->get($apolice_id);
            $produto_parceiro_plano_id = (!empty($apolice)) ? $apolice[0]['produto_parceiro_plano_id'] : null;
        }

        $dados_bilhete              = $this->apolice->defineDadosBilhete($produto_parceiro_plano_id);
        $percentual                 = false;
        $total                      = 0;
        $maior_valor                = 0;
        $maior_pos                  = 0;

        foreach ($coberturas as $i => $v)
        {
            $cobertura      = $v;
            $percentagem    = $valor_cobertura = $valor_config = 0;
            switch ($cobertura["mostrar"]) {
                case 'importancia_segurada':
                case 'descricao':
                case 'preco':
                    // encontra o percentual da cobertura referente ao premio liquido
                    $percentagem = $valor_config = floatval($cobertura["valor"] / $cobertura['valor_premio_net'] * 100);
                    $valor_cobertura = $valor_base * $percentagem / 100;
                    break;
                // case 'preco':
                //     $valor_cobertura = $valor_config = floatval($cobertura["valor_config"]);
                //     break;
            }

            // Caso tenha enviado o valor da cobertura
            if ( !empty($ValuesCoberturas[$cobertura['cod_cobertura']]) )
            {
                $valor_cobertura = $ValuesCoberturas[$cobertura['cod_cobertura']]['valor_restituido'];
                $valor_config = floatval($valor_cobertura / $cobertura['valor_premio_net'] * 100);
            }

            $valor_cobertura = round($valor_cobertura*-1,2);

            // se será totalizado para descontar o percentual
            if ($cobertura["mostrar"] != 'preco')
            {
                // encontra a cobertura de maior valor
                if ( $valor_cobertura < $maior_valor )
                {
                    $maior_valor = $valor_cobertura;
                    $percentual = $i;
                }

                $total += $valor_cobertura;
            }

            $dados = [
                'cotacao_id'            => $cobertura["cotacao_id"],
                'pedido_id'             => $cobertura["pedido_id"],
                'apolice_id'            => $cobertura["apolice_id"],
                'cobertura_plano_id'    => $cobertura["cobertura_plano_id"],
                'valor'                 => $valor_cobertura,
                'iof'                   => isempty($cobertura["iof"], 0),
                'importancia_segurada'  => isempty($cobertura["importancia_segurada"], 0),
                'mostrar'               => $cobertura["mostrar"],
                'valor_config'          => $valor_config,
                'cod_cobertura'         => $cobertura['cod_cobertura'],
                'data_inicio_vigencia'  => $cobertura['data_inicio_vigencia'],
                'data_fim_vigencia'     => $cobertura['data_fim_vigencia'],
                'cod_ramo'              => isempty($cobertura['cod_ramo'],     $dados_bilhete['cod_ramo']),
                'cod_produto'           => isempty($cobertura['cod_produto'],  $dados_bilhete['cod_produto']),
                'cod_sucursal'          => isempty($cobertura['cod_sucursal'], $dados_bilhete['cod_sucursal']),
                'criacao'               => date("Y-m-d H:i:s"),
            ];

            $idInsert = $this->insert($dados, TRUE);

            $coberturas[$i]["valor_cobertura"] = $valor_cobertura;
            $cob[$i]['cotacao_cobertura_id'] = $idInsert;
        }

        // Calcula a diferença se o cálculo for percentual
        if ($maior_valor && $total != ($valor_base * -1) ) // a base é positiva e o saldo será negativo em um cancelamento
        {
            $coberturas[$percentual]["valor_cobertura"] = round($coberturas[$percentual]["valor_cobertura"] - ($total - ($valor_base * -1)), 2); 
            $dd['valor'] = $coberturas[$percentual]["valor_cobertura"];
            $this->update($cob[$percentual]['cotacao_cobertura_id'], $dd);
        }

        return true;
    }

    public function geraDadosEmissao($cotacao_id, $pedido_id, $apolice_id, $produto_parceiro_plano_id)
    {
        $this->load->model('apolice_model', 'apolice');
        $this->load->model('cotacao_cobertura_model', 'cotacao_cobertura');

        $dados_bilhete = $this->apolice->defineDadosBilhete($produto_parceiro_plano_id);

        $coberturas = $this->cotacao_cobertura
            ->with_cobertura_plano()
            ->filterByID($cotacao_id)
            ->get_all();

        foreach ($coberturas as $cobertura) {

            $dados_apolice_cobertura = [
                'cotacao_id'            => $cotacao_id,
                'pedido_id'             => $pedido_id,
                'apolice_id'            => $apolice_id,
                'cobertura_plano_id'    => $cobertura["cobertura_plano_id"],
                'valor'                 => $cobertura["valor"],
                'iof'                   => $cobertura["iof"],
                'importancia_segurada'  => isempty($cobertura["importancia_segurada"], 0),
                'mostrar'               => $cobertura["mostrar"],
                'valor_config'          => $cobertura['valor_config'],
                'cod_cobertura'         => $cobertura['cod_cobertura'],
                'data_inicio_vigencia'  => $cobertura['data_inicio_vigencia'],
                'data_fim_vigencia'     => $cobertura['data_fim_vigencia'],
                'cod_ramo'              => isempty($cobertura['cod_ramo'], $dados_bilhete['cod_ramo']),
                'cod_produto'           => isempty($cobertura['cod_produto'], $dados_bilhete['cod_produto']),
                'cod_sucursal'          => isempty($cobertura['cod_sucursal'], $dados_bilhete['cod_sucursal']),
                'criacao'               => date("Y-m-d H:i:s"),
            ];

            $this->insert($dados_apolice_cobertura, true);
        }
    }

}
