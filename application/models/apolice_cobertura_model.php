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

    function filterByPedidoID($pedido_id){
        $this->_database->select("{$this->_table}.*, IFNULL(IFNULL(apolice_equipamento.valor_premio_net,apolice_generico.valor_premio_net),apolice_seguro_viagem.valor_premio_net) AS valor_premio_net", FALSE);
        $this->_database->join("apolice_equipamento","apolice_equipamento.apolice_id = {$this->_table}.apolice_id", "left");
        $this->_database->join("apolice_generico","apolice_generico.apolice_id = {$this->_table}.apolice_id", "left");
        $this->_database->join("apolice_seguro_viagem","apolice_seguro_viagem.apolice_id = {$this->_table}.apolice_id", "left");
        $this->_database->where("{$this->_table}.pedido_id", $pedido_id);
        $this->_database->where("{$this->_table}.deletado", 0);
        return $this;
    }

    function filterByVigenciaCob($apolice_id){
        $this->_database->select("{$this->_table}.data_inicio_vigencia, {$this->_table}.data_fim_vigencia");
        $this->_database->where("{$this->_table}.apolice_id", $apolice_id);
        $this->_database->group_by("{$this->_table}.data_inicio_vigencia, {$this->_table}.data_fim_vigencia");
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    public function geraDadosCancelamento($pedido_id, $valor_base)
    {
        $this->load->model('apolice_model', 'apolice');

        $valor_base                 = floatval( $valor_base );
        $coberturas                 = $this->filterByPedidoID($pedido_id)->get_all();
        $apolice                    = $this->apolice->getApolicePedido($pedido_id);
        $produto_parceiro_plano_id  = (!empty($apolice)) ? $apolice[0]['produto_parceiro_plano_id'] : null;
        $dados_bilhete              = $this->apolice->defineDadosBilhete($produto_parceiro_plano_id);
        $percentual                 = false;
        $total                      = 0;
        $maior_valor                = 0;
        $maior_pos                  = 0;

        foreach ($coberturas as $i => $v) {

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
                'cotacao_id'         => $cobertura["cotacao_id"],
                'pedido_id'          => $cobertura["pedido_id"],
                'apolice_id'         => $cobertura["apolice_id"],
                'cobertura_plano_id' => $cobertura["cobertura_plano_id"],
                'valor'              => $valor_cobertura,
                'iof'                => isempty($cobertura["iof"], 0),
                'mostrar'            => $cobertura["mostrar"],
                'valor_config'       => $valor_config,
                'cod_cobertura'      => $cobertura['cod_cobertura'],
                'cod_ramo'           => isempty($cobertura['cod_ramo'],     $dados_bilhete['cod_ramo']),
                'cod_produto'        => isempty($cobertura['cod_produto'],  $dados_bilhete['cod_produto']),
                'cod_sucursal'       => isempty($cobertura['cod_sucursal'], $dados_bilhete['cod_sucursal']),
                'criacao'            => date("Y-m-d H:i:s"),
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

}
