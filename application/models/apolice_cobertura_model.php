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
        $this->_database->select("{$this->_table}.*, apolice_equipamento.valor_premio_net");
        $this->_database->join("apolice_equipamento","apolice_equipamento.apolice_id = {$this->_table}.apolice_id");
        $this->_database->where("{$this->_table}.pedido_id", $pedido_id);
        $this->_database->where("{$this->_table}.deletado", 0);
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    public function geraDadosCancelamento($pedido_id, $valor_base) {

        $coberturas = $this->filterByPedidoID($pedido_id)->get_all();
        $valor_base = floatval( $valor_base );

        foreach ($coberturas as $cobertura) {

            $percentagem = $valor_cobertura = $valor_config = 0;
            switch ($cobertura["mostrar"]) {
                case 'importancia_segurada':
                case 'descricao':
                case 'preco':
                    // encontra o percentual da cobertura referente ao premio liquido
                    $percentagem = $valor_config = floatval(round($cobertura["valor"] / $cobertura['valor_premio_net'],2));
                    $valor_cobertura = $valor_base * $percentagem;
                    break;
                // case 'preco':
                //     $valor_cobertura = $valor_config = floatval($cobertura["valor_config"]);
                //     break;
            }

            $dados['cotacao_id'] = $cobertura["cotacao_id"];
            $dados['pedido_id'] = $cobertura["pedido_id"];
            $dados['apolice_id'] = $cobertura["apolice_id"];
            $dados['cobertura_plano_id'] = $cobertura["cobertura_plano_id"];
            $dados['valor'] = $valor_cobertura*-1;
            $dados['mostrar'] = $cobertura["mostrar"];
            $dados['valor_config'] = $valor_config;
            $dados['criacao'] = date("Y-m-d H:i:s");

            $this->insert($dados, TRUE);
        }

        return true;
    }

}
