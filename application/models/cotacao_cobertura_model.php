<?php
Class Cotacao_Cobertura_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cotacao_cobertura';
    protected $primary_key = 'cotacao_cobertura_id';

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

    function filterByID($cotacao_id){
        $this->_database->where("cotacao_id", $cotacao_id);
        $this->_database->where("deletado", 0);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    public function deleteByCotacao($cotacao_id){
        $this->db->where('cotacao_id', $cotacao_id);
        $this->db->delete($this->_table);
    }

    public function geraCotacaoCobertura($cotacao_id, $produto_parceiro_id, $produto_parceiro_plano_id = null, $importancia_segurada = null, $premio_liquido = null) {

        $this->load->model('cobertura_plano_model', 'plano_cobertura');
        $this->load->model('cotacao_generico_cobertura_model', 'cotacao_generico_cobertura');
        $this->load->model('cotacao_equipamento_cobertura_model', 'cotacao_equipamento_cobertura');
        $this->load->model('cotacao_seguro_viagem_cobertura_model', 'cotacao_seguro_viagem_cobertura');

        $coberturas = $this->plano_cobertura->with_prod_parc($produto_parceiro_id, $produto_parceiro_plano_id)->get_all();
        $importancia_segurada = floatval( $importancia_segurada );

        // limpa os dados
        $this->deleteByCotacao($cotacao_id);
        $total = 0;
        $percentual = false;

        for( $i = 0; $i < sizeof( $coberturas ); $i++ ) {
            $cobertura = $coberturas[$i];
            $cobertura_plano_id = $cobertura["cobertura_plano_id"];
            $percentagem = $valor_cobertura = $valor_config = 0;

            switch ($cobertura["mostrar"]) {
                case 'importancia_segurada':
                    $percentual = $i;
                    $percentagem = $valor_config = floatval($cobertura["porcentagem"]);
                    $valor_cobertura = ( $importancia_segurada * $percentagem ) / 100;
                    break;
                case 'preco':
                    $valor_cobertura = $valor_config = floatval($cobertura["preco"]);
                    break;
                case 'descricao':
                    $percentual = $i;
                    $percentagem = $valor_config = floatval($cobertura["porcentagem"]);
                    // if ($percentagem==0) {
                    //     $percentagem = $valor_config = 100;
                    // }
                    $valor_cobertura = ( $premio_liquido * $percentagem ) / 100;
                    break;
            }

            $valor_cobertura = round($valor_cobertura, 2);

            $dados['cotacao_id'] = $cotacao_id;
            $dados['cobertura_plano_id'] = $cobertura_plano_id;
            $dados['valor'] = $valor_cobertura;
            $dados['iof'] = (empty($cobertura["usar_iof"])) ? 0 : $cobertura["iof"];
            $dados['mostrar'] = $cobertura["mostrar"];
            $dados['valor_config'] = $valor_config;
            $dados['criacao'] = date("Y-m-d H:i:s");
            $idInsert = $this->insert($dados, TRUE);

            $coberturas[$i]["valor_cobertura"] = $valor_cobertura;
            $cob[$i]['cotacao_cobertura_id'] = $idInsert;
            $total += $valor_cobertura;
        }

        $produto_parceiro = $this->produto_parceiro->with_produto()->get($produto_parceiro_id);
        if ($produto_parceiro['produto_slug'] == 'seguro_viagem') {
            $cobertura_adicional = $this->cotacao_seguro_viagem_cobertura;
        } elseif ($produto_parceiro['produto_slug'] == 'equipamento') {
            $cobertura_adicional = $this->cotacao_equipamento_cobertura;
        } else {
            $cobertura_adicional = $this->cotacao_generico_cobertura;
        }

        $cobertura_adicional = $cobertura_adicional->with_cotacao($cotacao_id)->get_all();
        for( $j = 0; $j < sizeof( $cobertura_adicional ); $j++ ) {
            $cobertura = $cobertura_adicional[$j];

            $dados_add['cotacao_id'] = $cotacao_id;
            $dados_add['cobertura_plano_id'] = $cobertura["cobertura_plano_id"];
            $dados_add['valor'] = $cobertura["valor"];
            $dados_add['iof'] = 0;
            $dados_add['mostrar'] = 'preco';
            $dados_add['valor_config'] = $cobertura["valor"];
            $dados_add['criacao'] = date("Y-m-d H:i:s");
            $this->insert($dados_add, TRUE);

            $coberturas[$i+$j]["valor_cobertura"] = $cobertura["valor"];
        }


        // Calcula a diferença se o cálculo for percentual
        if ($percentual && $total != $premio_liquido)
        {
            $coberturas[$percentual]["valor_cobertura"] = $coberturas[$percentual]["valor_cobertura"] + ($total - $premio_liquido);
            $dd['valor'] = $coberturas[$percentual]["valor_cobertura"];
            $this->update($cob[$percentual]['cotacao_cobertura_id'], $dd);
        }

        return $coberturas;
    }

    /**
     * Retorna todos
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function get_all($limit = 0, $offset = 0, $processa = true)
    {
        return parent::get_all($limit, $offset);
    }

}
