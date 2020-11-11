<?php
Class Comissao_Gerada_Model extends MY_Model {
    //Dados da tabela e chave primária
    protected $_table = 'comissao_gerada';
    protected $primary_key = 'comissao_gerada_id';

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

    );

    function get_by_id($id)
    {
        return $this->get($id);
    }

    public function gerar_comissao_parceiro(){

        //busca vendas que não foram contabilziadas ainda
        $this->load->model('parceiro_relacionamento_produto_model', 'parceiro_relacionamento_produto');
        $this->load->model('comissao_classe_model', 'comissao_classe');

        $sql = "SELECT 
                cotacao.cotacao_id,
                #cotacao.parceiro_id,
                produto_parceiro.parceiro_id,
                pedido.pedido_id,
                pedido.codigo,
                cotacao_equipamento.produto_parceiro_id,
                cotacao_equipamento.premio_liquido,
                cotacao_equipamento.iof, 
                cotacao_equipamento.premio_liquido_total,
                cotacao_equipamento.comissao_corretor 
            FROM pedido
            INNER JOIN cotacao ON pedido.cotacao_id = cotacao.cotacao_id
            INNER JOIN cotacao_equipamento ON cotacao_equipamento.cotacao_id = cotacao.cotacao_id
            INNER JOIN produto_parceiro ON produto_parceiro.produto_parceiro_id = cotacao.produto_parceiro_id
            WHERE 
                pedido.pedido_status_id IN (3,8) 
                AND cotacao_equipamento.deletado = 0
                AND cotacao.deletado = 0
                AND pedido.deletado = 0
                AND pedido.pedido_id NOT IN (SELECT comissao_gerada.pedido_id FROM comissao_gerada WHERE comissao_gerada.deletado = 0 AND comissao_gerada.comissao_classe_id = 1 )";

        $result = $this->_database->query($sql)->result_array();
        $comissao_classe = $this->comissao_classe->get(1);

        foreach ($result as $item) {

            //retira o IOF da compra
            $premio_liquido_total = $item["premio_liquido"];
            $comissao_venda =  ($item['comissao_corretor']/100) * $premio_liquido_total;

            $data_comissao = array();
            $data_comissao['comissao_classe_id'] = 1;
            $data_comissao['pedido_id'] = $item['pedido_id'];
            $data_comissao['valor'] = $comissao_venda;
            $data_comissao['parceiro_id'] = $item['parceiro_id'];
            $data_comissao['premio_liquido_total'] = $premio_liquido_total;
            $data_comissao['comissao'] = $item['comissao_corretor'];
            $data_comissao['descricao'] = "COMISSÃO {$comissao_classe['nome']} (". app_format_currency($item['comissao_corretor'], false, 3) ."%) REFERENTE AO PEDIDO {$item['codigo']}";
            $data_comissao['faturado'] = 0;

            $this->insert($data_comissao, TRUE);

        }

    }

    public function gerar_comissao_parceiro_relacionamento(){

        //busca vendas que não foram contabilziadas ainda
        $this->load->model('parceiro_relacionamento_produto_model', 'parceiro_relacionamento_produto');
        $this->load->model('comissao_classe_model', 'comissao_classe');

        $sql = "SELECT 
                cotacao.cotacao_id,
                cotacao.parceiro_id,
                pedido.pedido_id,
                pedido.codigo,
                ifnull(ifnull(cotacao_equipamento.produto_parceiro_id, cotacao_generico.produto_parceiro_id), cotacao_seguro_viagem.produto_parceiro_id) as produto_parceiro_id,
                ifnull(ifnull(cotacao_equipamento.premio_liquido, cotacao_generico.premio_liquido), cotacao_seguro_viagem.premio_liquido) as premio_liquido,
                ifnull(ifnull(cotacao_equipamento.iof, cotacao_generico.iof), cotacao_seguro_viagem.iof) as iof, 
                ifnull(ifnull(cotacao_equipamento.premio_liquido_total, cotacao_generico.premio_liquido_total), cotacao_seguro_viagem.premio_liquido_total) as premio_liquido_total,
                ifnull(ifnull(cotacao_equipamento.comissao_corretor, cotacao_generico.comissao_corretor), cotacao_seguro_viagem.comissao_corretor) as comissao_corretor,
                ifnull(ifnull(cotacao_equipamento.comissao_premio, cotacao_generico.comissao_premio), cotacao_seguro_viagem.comissao_premio) as comissao_premio,
                parceiro_relacionamento_produto.parceiro_relacionamento_produto_id,
                parceiro_relacionamento_produto.comissao_tipo
            FROM pedido
            INNER JOIN cotacao ON pedido.cotacao_id = cotacao.cotacao_id
            LEFT JOIN cotacao_equipamento ON cotacao_equipamento.cotacao_id = cotacao.cotacao_id AND cotacao_equipamento.deletado = 0
            LEFT JOIN cotacao_seguro_viagem ON cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id AND cotacao_seguro_viagem.deletado = 0
            LEFT JOIN cotacao_generico ON cotacao_generico.cotacao_id = cotacao.cotacao_id AND cotacao_generico.deletado = 0
            INNER JOIN parceiro_relacionamento_produto ON cotacao.parceiro_id = parceiro_relacionamento_produto.parceiro_id AND parceiro_relacionamento_produto.produto_parceiro_id = ifnull(ifnull(cotacao_equipamento.produto_parceiro_id, cotacao_generico.produto_parceiro_id), cotacao_seguro_viagem.produto_parceiro_id)
            INNER JOIN parceiro ON parceiro_relacionamento_produto.parceiro_id = parceiro.parceiro_id
            LEFT JOIN comissao_gerada ON pedido.pedido_id = comissao_gerada.pedido_id AND comissao_gerada.deletado = 0 
            WHERE pedido.pedido_status_id IN (3,8,5) 
                AND cotacao.deletado = 0
                AND pedido.deletado = 0
                AND comissao_gerada.pedido_id IS NULL
        ";

        $result = $this->_database->query($sql)->result_array();

        foreach ($result as $item) {
            $this->geraComissaoRelacionamento($item, $item['parceiro_relacionamento_produto_id']);
        }
    }

    private function geraComissaoRelacionamento($item, $pai_id) {
        $parceiro_relacionamento = $this->parceiro_relacionamento_produto
            ->with_parceiro()
            ->with_parceiro_tipo()
            ->filter_by_produto_parceiro($item['produto_parceiro_id'])
            ->get($pai_id);

        if (empty($parceiro_relacionamento))
            return;

        $this->geraComissao($item, $parceiro_relacionamento);
    }

    private function geraComissao($item, $parceiro) {

        $comissao_classe_id = ( $parceiro["pai_id"] == 0 ) ? 1 : 4;
        $comissao_classe = $this->comissao_classe->get($comissao_classe_id);

        //retira o IOF da compra
        $premio_liquido_total = $item["premio_liquido"];

        // valida se possui uma Comissão específica da venda
        if ( !empty($item['comissao_tipo']) && !empty($item['comissao_premio']) && $item['comissao_premio'] > 0 && $parceiro['codigo_interno'] == 'representante')
        {
            $comissao_premio = $item['comissao_premio'] - ($item['comissao_tipo'] == 1 ? $item['comissao_corretor'] : 0);
        } else {
            $comissao_premio = $parceiro['comissao'];
        }

        $comissao_venda =  ($comissao_premio/100) * $premio_liquido_total;

        if(($parceiro['parceiro_id'] == 72 || $parceiro['parceiro_id'] == 76) && $comissao_venda < 0){
            $comissao_venda = 0.01;
        }

        $data_comissao = array();
        $data_comissao['comissao_classe_id']    = $comissao_classe['comissao_classe_id'];
        $data_comissao['pedido_id']             = $item['pedido_id'];
        $data_comissao['valor']                 = $comissao_venda;
        $data_comissao['parceiro_id']           = $parceiro['parceiro_id'];
        $data_comissao['parceiro_tipo_id']      = $parceiro['parceiro_tipo_id_parceiro'];
        $data_comissao['cod_parceiro']          = isempty($parceiro['cod_parceiro'], $parceiro['parceiro_codigo_corretor']);
        $data_comissao['premio_liquido_total']  = $premio_liquido_total;
        $data_comissao['comissao']              = $comissao_premio;
        $data_comissao['descricao']             = "COMISSÃO {$comissao_classe['nome']} (". app_format_currency($comissao_premio, false, 3) ."%) REFERENTE AO PEDIDO {$item['codigo']}";
        $data_comissao['faturado']              = 0;

        $this->insert($data_comissao, TRUE);

        if( $parceiro["pai_id"] != 0 ) {
            $this->geraComissaoRelacionamento($item, $parceiro['pai_id']);
        }

    }

    public function filterFromPesquisa(){

        if($this->input->get('pedido_codigo')){
            $this->_database->like("pedido.codigo", $this->input->get('pedido_codigo') );
        }

        if($this->input->get('parceiro_id')){
            $this->_database->where("{$this->_table}.parceiro_id", $this->input->get('parceiro_id') );
        }

        if($this->input->get('comissao_classe_id')){
            $this->_database->where("{$this->_table}.comissao_classe_id", $this->input->get('comissao_classe_id') );
        }

        if($this->input->get('data_inicio') && $this->input->get('data_fim')){
            $this->_database->where("{$this->_table}.criacao >= ", app_dateonly_mask_to_mysql($this->input->get('data_inicio') ) );
            $this->_database->where("{$this->_table}.criacao <= ", app_dateonly_mask_to_mysql($this->input->get('data_fim') ) );
        }

        return $this;
    }

    function with_comissao_classe($fields = array('nome'))
    {
        $this->with_simple_relation_foreign('comissao_classe', 'comissao_classe_', 'comissao_classe_id', 'comissao_classe_id', $fields );
        return $this;
    }

    function with_pedido($fields = array('codigo'))
    {
        $this->with_simple_relation_foreign('pedido', 'pedido_', 'pedido_id', 'pedido_id', $fields );
        return $this;
    }
    function with_parceiro($fields = array('nome_fantasia'))
    {
        $this->with_simple_relation_foreign('parceiro', 'parceiro_', 'parceiro_id', 'parceiro_id', $fields );
        return $this;
    }

    public function getByParceiroId($pedido_id, $parceiroId){
        $sql = "SELECT * FROM comissao_gerada WHERE pedido_id = $pedido_id AND parceiro_id = $parceiroId AND deletado = 0";
        $result = $this->_database->query($sql)->result_array();
        return $result;
    }

}
