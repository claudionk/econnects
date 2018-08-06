<?php

Class Pedido_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'pedido';
    protected $primary_key = 'pedido_id';

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

    // const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
    // const FORMA_PAGAMENTO_FATURADO = 3;
    // const FORMA_PAGAMENTO_CARTAO_DEBITO = 6;
    // const FORMA_PAGAMENTO_BOLETO = 5;

  const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
  const FORMA_PAGAMENTO_FATURADO = 9;
  const FORMA_PAGAMENTO_CARTAO_DEBITO = 8;
  const FORMA_PAGAMENTO_BOLETO = 9;
  const FORMA_PAGAMENTO_TRANSF_BRADESCO = 2;
  const FORMA_PAGAMENTO_TRANSF_BB = 7;
    
    //Dados
    public $validate = array(
        array(
            'field' => 'cotacao_id',
            'label' => 'Cotação',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'cotacao',
        ),
        array(
            'field' => 'produto_parceiro_pagamento_id',
            'label' => 'Parceiro Pagamento',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'produto_parceiro_pagamento'
        ),
        array(
            'field' => 'pedido_status_id',
            'label' => 'Status do pedido',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'pedido_status'
        ),
    );


    function getPedidoCarrinho($usuario_id){



        $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.codigo, pedido.codigo")
                                    ->select("pedido.valor_total, produto_parceiro.nome,  produto_parceiro.produto_parceiro_id")
                                    ->join("cotacao", "pedido.cotacao_id = cotacao.cotacao_id", 'inner')
                                    ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
                                    ->where("pedido.pedido_status_id", 13)
                                    ->where("pedido.alteracao_usuario_id", $usuario_id);

        $carrinho = $this->get_all();

/*
        $sql = "
                SELECT 
                     pedido.pedido_id, pedido.cotacao_id, pedido.codigo, pedido.codigo, pedido.valor_total, produto_parceiro.nome,  produto_parceiro.produto_parceiro_id
                FROM pedido
                INNER JOIN cotacao ON pedido.cotacao_id = cotacao.cotacao_id
                INNER JOIN cotacao_seguro_viagem ON cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id
                INNER JOIN produto_parceiro ON produto_parceiro.produto_parceiro_id = cotacao_seguro_viagem.produto_parceiro_id
                WHERE
                    pedido.deletado = 0 AND
                    cotacao.deletado = 0 AND
                    cotacao_seguro_viagem.deletado = 0 AND
                    produto_parceiro.deletado = 0 AND 
                    pedido.pedido_status_id = 13 AND
                    pedido.alteracao_usuario_id = {$usuario_id}

	
	

        ";

        return $this->_database->query($sql)->result_array(); */
        return $carrinho;

    }

    function getPedidosByID($pedidos){


        //$pedidos =  implode(',', $pedidos);

        $this->_database->select("pedido.pedido_id, pedido.pedido_status_id, pedido_status.nome as pedido_status_nome")
            ->select("pedido.cotacao_id, pedido.codigo, pedido.codigo, pedido.valor_total, produto_parceiro.nome,  produto_parceiro.produto_parceiro_id")
            ->join("pedido_status", "pedido_status.pedido_status_id = pedido.pedido_status_id", 'inner')
            ->join("cotacao", "pedido.cotacao_id = cotacao.cotacao_id", 'inner')
            ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')

            ->where_in("pedido.pedido_id", $pedidos);

        $pedidos = $this->get_all();
        return ($pedidos) ? $pedidos : array();

        /*

        if($pedidos) {
            $sql = "
                    SELECT 
                         pedido.pedido_id, pedido.pedido_status_id, pedido_status.nome as pedido_status_nome, pedido.cotacao_id, pedido.codigo, pedido.codigo, pedido.valor_total, produto_parceiro.nome,  produto_parceiro.produto_parceiro_id
                    FROM pedido
                    INNER JOIN pedido_status ON pedido_status.pedido_status_id = pedido.pedido_status_id
                    INNER JOIN cotacao ON pedido.cotacao_id = cotacao.cotacao_id
                    INNER JOIN cotacao_seguro_viagem ON cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id
                    INNER JOIN produto_parceiro ON produto_parceiro.produto_parceiro_id = cotacao_seguro_viagem.produto_parceiro_id
                    WHERE
                        pedido.deletado = 0 AND
                        cotacao.deletado = 0 AND
                        cotacao_seguro_viagem.deletado = 0 AND
                        produto_parceiro.deletado = 0 AND 
                        pedido.pedido_id IN ({$pedidos}) 
        
        
    
            ";
            return $this->_database->query($sql)->result_array();
        }else{
            return array();
        }
*/


    }


    function getPedidoPagamentoPendente($pedido_id = 0){

        $this->_database->distinct()
            ->select("pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela")
            ->select("pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug")
            ->join("produto_parceiro_pagamento", "pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id", 'inner')
            ->join("forma_pagamento", "forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id", 'inner')
            ->join("forma_pagamento_tipo", "forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id", 'inner')
            ->join("forma_pagamento_integracao", "forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id", 'inner')
            ->join("fatura", "fatura.pedido_id = pedido.pedido_id", 'inner')
            ->join("fatura_parcela", "fatura.fatura_id = fatura_parcela.fatura_id", 'inner')
            ->where_in("pedido.pedido_status_id", array(2,3,15,4))
            ->where_in("pedido.lock", 0)
            ->where("fatura_parcela.data_vencimento <=", date('y-m-d'))
            ->where("fatura_parcela.fatura_status_id", 1);

        if($pedido_id > 0){
            $this->_database->where_in("pedido.pedido_id", $pedido_id);
        }

        $pedidos = $this->get_all();
        //exit($this->_database->last_query());

        //log_message('debug', 'BUSCANDO PEDIDOS PENDENTES QUERY - ' . $this->_database->last_query());
        return ($pedidos) ? $pedidos : array();

        /*
        $sql = "
                    SELECT 
                        pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela, pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug
                    FROM pedido
                    INNER JOIN produto_parceiro_pagamento on pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id
                    INNER JOIN  on 
                    INNER JOIN  on 
                    INNER JOIN  on 
                    WHERE 
                      pedido. = 2  
                      AND pedido. = 0   
            ";



        return $this->_database->query($sql)->result_array();*/

    }


    function getPedidoPagamentoPendenteDebito($pedido_id = 0){


        $this->_database->select("pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela")
            ->select("pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug")
            ->join("produto_parceiro_pagamento", "pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id", 'inner')
            ->join("forma_pagamento", "forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id", 'inner')
            ->join("forma_pagamento_tipo", "forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id", 'inner')
            ->join("forma_pagamento_integracao", "forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id", 'inner')
            ->where_in("pedido.pedido_status_id", 14)
            ->where_in("pedido.lock", 0);

        if($pedido_id > 0){
            $this->_database->where_in("pedido.pedido_id", $pedido_id);
        }

        $pedidos = $this->get_all();
        return ($pedidos) ? $pedidos : array();

        /*
        $pedido_id = (int)$pedido_id;

        $sql = "
                    SELECT 
                        pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela, pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug
                    FROM pedido
                    INNER JOIN produto_parceiro_pagamento on pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id
                    INNER JOIN forma_pagamento on forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id
                    INNER JOIN forma_pagamento_tipo on forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id
                    INNER JOIN forma_pagamento_integracao on forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id
                    WHERE 
                      pedido.pedido_status_id = 14 
                      AND pedido.lock = 0   
            ";

        if($pedido_id > 0){
            $sql .= " AND pedido.pedido_id = {$pedido_id} ";
        }

        return $this->_database->query($sql)->result_array();*/

    }

    function getPedidoCanceladoEstorno($pedido_id = 0){

        $this->_database->select("pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela")
            ->select("pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug")
            ->select("pedido_cartao_transacao.tid, pedido_cartao_transacao.pedido_cartao_transacao_id")
            ->join("produto_parceiro_pagamento", "pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id", 'inner')
            ->join("forma_pagamento", "forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id", 'inner')
            ->join("forma_pagamento_tipo", "forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id", 'inner')
            ->join("forma_pagamento_integracao", "forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id", 'inner')
            ->join("pedido_cartao", "pedido.pedido_id = pedido_cartao.pedido_id", 'inner')
            ->join("pedido_cartao_transacao", "pedido_cartao.pedido_cartao_id = pedido_cartao_transacao.pedido_cartao_id", 'inner')
            ->where_in("pedido.pedido_status_id", 5)
            ->where_in("pedido.lock", 0)
            ->where_in("pedido_cartao_transacao.result", 'OK');


        if($pedido_id > 0){
            $this->_database->where_in("pedido.pedido_id", $pedido_id);
        }

        $pedidos = $this->get_all();
        return ($pedidos) ? $pedidos : array();


        /*

        $pedido_id = (int)$pedido_id;

        $sql = "
                    SELECT 
                        pedido.pedido_id, pedido.codigo, pedido.valor_total, pedido.valor_parcela, pedido.num_parcela, forma_pagamento.nome, forma_pagamento.slug, pedido_cartao_transacao.tid, pedido_cartao_transacao.pedido_cartao_transacao_id
                    FROM pedido
                    INNER JOIN produto_parceiro_pagamento on pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id
                    INNER JOIN forma_pagamento on forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id
                    INNER JOIN forma_pagamento_tipo on forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id
                    INNER JOIN forma_pagamento_integracao on forma_pagamento_integracao.forma_pagamento_integracao_id = forma_pagamento_tipo.forma_pagamento_integracao_id
                    INNER JOIN pedido_cartao on pedido.pedido_id = pedido_cartao.pedido_id
                    INNER JOIN pedido_cartao_transacao ON pedido_cartao.pedido_cartao_id = pedido_cartao_transacao.pedido_cartao_id
                    WHERE 
                      pedido.pedido_status_id = 5 
                      AND pedido.lock = 0   
                      AND pedido_cartao_transacao.result = 'OK'
            ";

        if($pedido_id > 0){
            $sql .= " AND pedido.pedido_id = {$pedido_id} ";
        }

        return $this->_database->query($sql)->result_array(); */

    }



    public function filterPesquisa()
    {

        $filters = $this->input->get();
        //  print_r($filters);exit;

        if($filters) {
            foreach ($filters as $key => $value)
            {
                if (!empty($value)) {
                    switch ($key) {
                        case "pedido_codigo":
                            $this->_database->like('pedido.codigo', $value);
                            break;
                        case "razao_nome":
                            $this->_database->like('cliente.razao_nome', $value);
                            break;
                        case "cnpj_cpf":
                            $this->_database->like('cliente.cnpj_cpf', $value);
                            break;
                        case "data_nascimento":
                            $this->_database->where('cliente.data_nascimento', app_dateonly_mask_to_mysql($value));
                            break;
                        case "pedido_status_id":
                            $this->_database->where('pedido.pedido_status_id', ($value));
                            break;
                        case "fatura_status_id":
                            $this->_database->where('fatura.fatura_status_id', ($value));
                            break;

                        case "inadimplencia":

                            $hoje = date("Y-m-d");

                            $this->_database->distinct();
                            $this->_database->join("fatura_parcela", "fatura_parcela.fatura_id = fatura.fatura_id");
                            $this->_database->join("fatura_status ps", "ps.fatura_status_id = fatura_parcela.fatura_status_id");
                            $this->_database->where("fatura_parcela.data_vencimento < NOW()");
                            $this->_database->where("ps.slug != 'faturado'");
                            $this->_database->order_by("fatura_parcela.data_vencimento asc");

                            break;
                    }


                }
            }

        }
        return $this;
    }

    public function filterAPI($param = array())
    {


        if($param) {
            foreach ($param as $key => $value)
            {
                if (!empty($value)) {
                    switch ($key) {
                        case "apolice_id":
                            $this->_database->like('apolice.apolice_id', $value);
                            break;
                        case "num_apolice":
                            $this->_database->like('apolice.num_apolice', $value);
                            break;
                        case "documento":
                            $this->_database->like('cliente.cnpj_cpf', $value);
                            break;
                        case "pedido_id":
                            $this->_database->like('pedido.pedido_id', $value);
                            break;                        
                    }


                }
            }

        }
        return $this;
    }

    public function isInadimplente($pedido_id){

        $this->_database->distinct();
        $this->_database->join("fatura", "fatura.pedido_id = pedido.pedido_id");
        $this->_database->join("fatura_parcela", "fatura_parcela.fatura_id = fatura.fatura_id");
        $this->_database->join("fatura_status ps", "ps.fatura_status_id = fatura_parcela.fatura_status_id");
        $this->_database->where("fatura_parcela.data_vencimento < NOW()");
        $this->_database->where("ps.slug != 'faturado'");
        $this->_database->where("pedido.pedido_id = {$pedido_id}");
        $this->_database->order_by("fatura_parcela.data_vencimento asc");

        $total = $this->get_total();

        if($total > 0){
            return true;
        }else{
            return false;
        }



    }

    public function filterNotCarrinho()
    {

        $this->_database->where('pedido.pedido_status_id !=', 13);

        return $this;
    }
    public function filterCliente($cliente_id)
    {

        $this->_database->where('cotacao.cliente_id', $cliente_id);
        $this->_database->where_in('pedido.pedido_status_id', array(2,3,4,5,6,7,8,10,11,12,14,15));

        return $this;
    }

    public function build_faturamento( $engine = "generico" ) {
      if( $engine == "seguro_saude" ) {
        $engine = "generico";
      }
       $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id, pedido.num_parcela, pedido.valor_parcela")
            ->select("cotacao_{$engine}.*")
            ->select("cotacao.produto_parceiro_id, produto.slug")
            ->select("produto_parceiro.parceiro_id")
            ->select("apolice.*")
            ->select("apolice_{$engine}.*")
            ->select("fatura.*")

            ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
            ->join("cotacao_{$engine}", "cotacao_{$engine}.cotacao_id = cotacao.cotacao_id", 'inner')
            ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
            ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
            ->join("apolice", "apolice.pedido_id = pedido.pedido_id", 'inner')
            ->join("apolice_{$engine}", "apolice_{$engine}.apolice_id = apolice.apolice_id", 'inner')
            ->join("fatura", "fatura.pedido_id = pedido.pedido_id", 'inner');
        return $this;

    }

    function getPedidoProdutoParceiro( $pedido_id = 0 ){



        $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela")
            ->select("cotacao.produto_parceiro_id, produto.slug")
            ->select("produto_parceiro.parceiro_id")
            ->select("produto_parceiro_apolice.template as template_apolice")
            ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
            ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
            ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
            ->join("produto_parceiro_apolice", "produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'left')
            ->join("cotacao_seguro_viagem", "cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id", 'left')
            ->join("cotacao_equipamento", "cotacao_equipamento.cotacao_id = cotacao.cotacao_id", 'left')
            ->join("cotacao_generico", "cotacao_generico.cotacao_id = cotacao.cotacao_id", 'left')
            ->where_in("pedido.pedido_id", $pedido_id);


        $pedidos = $this->get_all();
        if($pedidos){
            $pedido = $pedidos[0];
            if($pedido['slug'] == 'seguro_viagem'){
                $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela")
                ->select("cotacao.produto_parceiro_id, produto.slug")
                    ->select("cotacao_seguro_viagem.*")
                    ->select("produto_parceiro.parceiro_id")
                    ->select("produto_parceiro_apolice.template as template_apolice")
                    ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
                    ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
                    ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
                    ->join("produto_parceiro_apolice", "produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'left')
                    ->join("cotacao_seguro_viagem", "cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id", 'left')
                    ->where_in("pedido.pedido_id", $pedido_id);
                $pedidos = $this->get_all();
            }elseif ($pedido['slug'] == 'equipamento'){
                $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela")
                    ->select("cotacao_equipamento.*")
                    ->select("cotacao.produto_parceiro_id, produto.slug")
                    ->select("produto_parceiro.parceiro_id")
                    ->select("produto_parceiro_apolice.template as template_apolice")
                    ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
                    ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
                    ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
                    ->join("produto_parceiro_apolice", "produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'left')
                    ->join("cotacao_equipamento", "cotacao_equipamento.cotacao_id = cotacao.cotacao_id", 'left')
                    ->where_in("pedido.pedido_id", $pedido_id);
                $pedidos = $this->get_all();
            }elseif ( $pedido['slug'] == "generico" || $pedido['slug'] == "seguro_saude" ){
                $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela")
                    ->select("cotacao_generico.*")
                    ->select("cotacao.produto_parceiro_id, produto.slug")
                    ->select("produto_parceiro.parceiro_id")
                    ->select("produto_parceiro_apolice.template as template_apolice")
                    ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
                    ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
                    ->join("produto", "produto.produto_id = produto_parceiro.produto_id", 'inner')
                    ->join("produto_parceiro_apolice", "produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'left')
                    ->join("cotacao_generico", "cotacao_generico.cotacao_id = cotacao.cotacao_id", 'left')
                    ->where_in("pedido.pedido_id", $pedido_id);
                $pedidos = $this->get_all();
            }

            return ($pedidos) ? $pedidos : array();

        }else{
            return array();
        }


/*
        if($pedidos){
            $pedido = $pedidos[0];



            $this->_database->select("pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela,")
                ->select("cotacao.produto_parceiro_id")
                ->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id", 'inner')
                ->join("produto_parceiro", "cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
                ->where_in("pedido.pedido_id", $pedido_id);


            $pedidos = $this->get_all();
        }else{
            return array();
        }



        $pedido_id = (int)$pedido_id;

        $sql = "
                SELECT pedido.pedido_id, pedido.cotacao_id, pedido.produto_parceiro_pagamento_id,pedido.num_parcela, pedido.valor_parcela, 
                
                
                ,
                ,
                (SELECT produto_parceiro_apolice.template FROM produto_parceiro_apolice where produto_parceiro_apolice.produto_parceiro_id = cotacao_seguro_viagem.produto_parceiro_id and produto_parceiro_apolice.deletado = 0 LIMIT 1) as template_apolice
                from pedido
                inner JOIN cotacao ON cotacao.cotacao_id = pedido.cotacao_id
                inner JOIN cotacao_seguro_viagem ON cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id
                inner join produto_parceiro ON produto_parceiro.produto_parceiro_id = cotacao_seguro_viagem.produto_parceiro_id
                WHERE pedido.pedido_id = {$pedido_id} and cotacao_seguro_viagem.deletado = 0 and cotacao.deletado = 0
        ";

        return $this->_database->query($sql)->result_array();*/

    }


    public function with_seguro_viagem(){

        $this->_database->select("cotacao_seguro_viagem.*")
            ->join("cotacao_seguro_viagem", "cotacao_seguro_viagem.cotacao_id = pedido.cotacao_id");

        return $this;
    }

    function getPedidoPagamento($pedido_id = 0){

        $this->_database->select("pedido.num_parcela, pedido.valor_parcela, pedido.valor_total")
            ->select(", forma_pagamento_tipo.nome as tipo_pagamento,  forma_pagamento.nome as bandeira")
            ->join("produto_parceiro_pagamento", "pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id", 'inner')
            ->join("forma_pagamento", "forma_pagamento.forma_pagamento_id = produto_parceiro_pagamento.forma_pagamento_id", 'inner')
            ->join("forma_pagamento_tipo", "forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id", 'inner')
            ->where_in("pedido.pedido_id", $pedido_id);

        $pedidos = $this->get_all();
        return ($pedidos) ? $pedidos : array();


        /*



        $pedido_id = (int)$pedido_id;

        $sql = "
                select pedido.num_parcela, pedido.valor_parcela, pedido.valor_total, forma_pagamento_tipo.nome as tipo_pagamento,  forma_pagamento.nome as bandeira
                from pedido
                inner join produto_parceiro_pagamento ON pedido.produto_parceiro_pagamento_id = produto_parceiro_pagamento.produto_parceiro_pagamento_id
                inner join forma_pagamento ON produto_parceiro_pagamento.forma_pagamento_id = forma_pagamento.forma_pagamento_id
                inner join forma_pagamento_tipo ON forma_pagamento_tipo.forma_pagamento_tipo_id = forma_pagamento.forma_pagamento_tipo_id
                WHERE pedido.pedido_id = {$pedido_id}
        ";

        return $this->_database->query($sql)->result_array();
*/
    }

    public function with_pedido_status(){

        return $this->with_simple_relation('pedido_status', 'pedido_status_', 'pedido_status_id', array('nome'), 'inner');
    }

    public function with_fatura(){

        $this->_database->where('fatura.deletado', 0);
        return $this->with_simple_relation('fatura', 'fatura_', 'pedido_id', array('tipo'), 'inner');

    }
    public function with_apolice(){

        $this->_database->where('apolice.deletado', 0);
        return $this->with_simple_relation('apolice', 'apolice_', 'pedido_id', array('num_apolice', 'apolice_id'), 'inner');

    }


    public function with_cotacao_cliente_contato(){
        $this->_database->select('cliente.razao_nome, cotacao_equipamento.equipamento_nome, equipamento_marca.nome as marca, equipamento_categoria.nome as categoria');
        $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 1 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1) AS email");
        $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 2 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1)  AS celular");
        $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 3 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1) AS telefone");
        $this->_database->join('cotacao', 'cotacao.cotacao_id = pedido.cotacao_id', 'inner');
        $this->_database->join('cliente', 'cliente.cliente_id = cotacao.cliente_id', 'inner');
      	$this->_database->join("cotacao_equipamento", "cotacao_equipamento.cotacao_id = cotacao.cotacao_id", 'left');
        $this->_database->join("equipamento_marca", "equipamento_marca.equipamento_marca_id = cotacao_equipamento.equipamento_marca_id", 'left');
        $this->_database->join("equipamento_categoria", "equipamento_categoria.equipamento_categoria_id = cotacao_equipamento.equipamento_categoria_id", 'left');

        return $this;
    }

    public function with_cotacao(){
        $this->_database->join('cotacao', 'cotacao.cotacao_id = pedido.cotacao_id', 'inner');
        return $this;
    }

    function filter_by_pedido($pedido_id)
    {
        $this->_database->where('pedido.pedido_id', $pedido_id);
        return $this;
    }

    function filter_by_cotacao($cotacao_id)
    {
        $this->_database->where('pedido.cotacao_id', $cotacao_id);
        return $this;
    }

    function filter_by_upgrade()
    {
        //$this->_database->join("cotacao", "cotacao.cotacao_id = pedido.cotacao_id");
        //$this->_database->join("cotacao_seguro_viagem", "cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id");
        $this->_database->join("produto_parceiro", "produto_parceiro.produto_parceiro_id = cotacao.produto_parceiro_id");
        $this->_database->join("produto_parceiro_plano", "produto_parceiro_plano.produto_parceiro_id = produto_parceiro.produto_parceiro_id");
        $this->_database->where("produto_parceiro_plano.passivel_upgrade = 1");
        $this->_database->where_in("pedido.pedido_status_id", array(3,8));

        return $this;
    }

    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    function isPermiteCancelar($pedido_id){

        $this->load->model("apolice_model", "apolice");

        $result = FALSE;
        $pedido = $this->get($pedido_id);

        if($pedido){


            if(($pedido['pedido_status_id'] == 3) || ($pedido['pedido_status_id'] == 8) || ($pedido['pedido_status_id'] == 12) ) {

                $apolices = $this->apolice->getApolicePedido($pedido_id);


                if ($apolices) {

                    foreach ($apolices as $apolice) {
                        $fim_vigencia = explode('-', $apolice['data_fim_vigencia']);
                        $fim_vigencia = mktime(0, 0, 0, $fim_vigencia[1], $fim_vigencia[2], $fim_vigencia[0]);
                        if (mktime(0, 0, 0, date('m'), date('d'), date('Y')) < $fim_vigencia) {
                            $result = TRUE;
                        }

                    }


                }
            }

        }

        return $result;

    }

    function isPermiteUpgrade($pedido_id){

        $this->load->model("apolice_model", "apolice");
        $this->load->model("produto_parceiro_plano_model", "produto_parceiro_plano");

        $result = FALSE;
        $pedido = $this->get($pedido_id);


        if($pedido){


            if(($pedido['pedido_status_id'] == 3) || ($pedido['pedido_status_id'] == 8)) {

                $apolices = $this->apolice->getApolicePedido($pedido_id);


                if ($apolices) {

                    foreach ($apolices as $apolice) {

                        $plano = $this->produto_parceiro_plano->get($apolice['produto_parceiro_plano_id']);

                        if($plano && $plano['passivel_upgrade'] == 1) {


                            $fim_vigencia = explode('-', $apolice['data_fim_vigencia']);
                            $fim_vigencia = mktime(0, 0, 0, $fim_vigencia[1], $fim_vigencia[2], $fim_vigencia[0]);

                            if (mktime(0, 0, 0, date('m'), date('d'), date('Y')) < $fim_vigencia) {
                                $result = TRUE;
                            }

                        }
                    }


                }
            }

        }

        return $result;

    }

    function cancelamento($pedido_id){

        $this->load->model('produto_parceiro_cancelamento_model', 'cancelamento');
        $this->load->model("apolice_model", "apolice");

        $result = array(
            'result' => FALSE,
            'mensagem' => '',
            'redirect' => "admin/pedido/index",
        );


        $pedido = $this->get($pedido_id);



        //varifica se existe o registro
        if(!$pedido){
            $result['result'] = FALSE;
            $result['mensagem'] = 'Não foi possível encontrar o registro.';
            $result['redirect'] = "admin/pedido/index";
            return $result;
        }


        //varifica se é permitido cancelar
        if(!$this->isPermiteCancelar($pedido_id)){
            $result['result'] = FALSE;
            $result['mensagem'] = 'Não foi possível cancelar esse PEDIDO.';
            $result['redirect'] = "admin/pedido/view/{$pedido_id}";
            return $result;
        }


        //pega as configurações de cancelamento do pedido
        $produto_parceiro = $this->getPedidoProdutoParceiro($pedido_id);


        if(!$produto_parceiro){
            $result['result'] = FALSE;
            $result['mensagem'] = 'Produto não encontrado.';
            $result['redirect'] = "admin/pedido/view/{$pedido_id}";
            return $result;
        }

        $produto_parceiro = $produto_parceiro[0];
        $produto_parceiro_cancelamento = $this->cancelamento->filter_by_produto_parceiro($produto_parceiro['produto_parceiro_id'])->get_all();


        if(!$produto_parceiro_cancelamento){
            $result['result'] = FALSE;
            $result['mensagem'] = 'Não existe regras de cancelamento configuradas para esse produto';
            $result['redirect'] = "admin/pedido/view/{$pedido_id}";
            return $result;
        }

        $produto_parceiro_cancelamento = $produto_parceiro_cancelamento[0];


        $apolices = $this->apolice->getApolicePedido($pedido_id);

        if(!$apolices){
            $result['result'] = FALSE;
            $result['mensagem'] = 'Apólices não encontrada';
            $result['redirect'] = "admin/pedido/view/{$pedido_id}";
            return $result;
        }

        $apolice = $apolices[0];

        //pega início e fim da vigencia
        $fim_vigencia = explode('-', $apolice['data_fim_vigencia']);
        $fim_vigencia = mktime(0, 0, 0, $fim_vigencia[1], $fim_vigencia[2], $fim_vigencia[0]);

        $inicio_vigencia = explode('-', $apolice['data_ini_vigencia']);
        $inicio_vigencia = mktime(0, 0, 0, $inicio_vigencia[1], $inicio_vigencia[2], $inicio_vigencia[0]);


        $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        //exit("$hoje > $inicio_vigencia");

        if ($hoje >= $inicio_vigencia) {
            //Já comeceu a vigencia
            if($produto_parceiro_cancelamento['seg_depois_hab'] == 0){
                //não pode executar cancelamento antes do início da vigência
                $result['result'] = FALSE;
                $result['mensagem'] = 'Cancelamento não permitido após o início da vigência';
                $result['redirect'] = "admin/pedido/view/{$pedido_id}";
                return $result;
            }else{
                // pode efetuar o cancelamento depois do início da vigência
                if($produto_parceiro_cancelamento['seg_depois_dias'] != 0){
                    // verifica a quantidade de dias que pode executar o cancelamento antes do inicio da vigência
                    $qnt_dias = app_date_get_diff_dias(app_dateonly_mysql_to_mask($apolice['data_ini_vigencia']), date('d/m/Y'), 'D');
                    if($qnt_dias > $produto_parceiro_cancelamento['seg_depois_dias']){
                        //não pode executar cancelamento com limite de dias antes do início da vigência
                        $result['result'] = FALSE;
                        $result['mensagem'] = "Cancelamento só é permitido até {$produto_parceiro_cancelamento['seg_depois_dias']} dia(s) após o início da vigência";
                        $result['redirect'] = "admin/pedido/view/{$pedido_id}";
                        return $result;
                    }
                }

                // efetuar o cancelamento
                $this->executa_extorno_cancelamento($pedido_id, TRUE);
            }


        }elseif ($hoje < $inicio_vigencia){
            //Ainda não começo a vigência
            if($produto_parceiro_cancelamento['seg_antes_hab'] == 0){
                //não pode executar cancelamento antes do início da vigência
                $result['result'] = FALSE;
                $result['mensagem'] = 'Cancelamento não permitido antes do início da vigência';
                $result['redirect'] = "admin/pedido/view/{$pedido_id}";
                return $result;
            }else{
                // pode efetuar o cancelamento antes do início da vigência
                if($produto_parceiro_cancelamento['seg_antes_dias'] != 0){
                    // verifica a quantidade de dias que pode executar o cancelamento antes do inicio da vigência
                    $qnt_dias = app_date_get_diff_dias(date('d/m/Y'), app_dateonly_mysql_to_mask($apolice['data_ini_vigencia']), 'D');
                    if($qnt_dias < $produto_parceiro_cancelamento['seg_antes_dias']){
                        //não pode executar cancelamento com limite de dias antes do início da vigência
                        $result['result'] = FALSE;
                        $result['mensagem'] = "Cancelamento só é permitido até {$produto_parceiro_cancelamento['seg_antes_dias']} dia(s) antes do início da vigência";
                        $result['redirect'] = "admin/pedido/view/{$pedido_id}";
                        return $result;
                    }
                }

                // efetuar o cancelamento
                $this->executa_extorno_cancelamento($pedido_id, FALSE);
            }

        }


        $result['result'] = TRUE;
        $result['mensagem'] = 'Pedido cancelado com sucesso.';
        $result['redirect'] = "admin/pedido/view/{$pedido_id}";
        return $result;


    }


    function executa_extorno_cancelamento($pedido_id, $vigente = FALSE, $ins_movimentacao = TRUE){


        $this->load->model('produto_parceiro_cancelamento_model', 'cancelamento');
        $this->load->model("apolice_model", "apolice");
        $this->load->model("fatura_model", "fatura");
        $this->load->model("apolice_seguro_viagem_model", "apolice_seguro_viagem");
        $this->load->model('pedido_transacao_model', 'pedido_transacao');
        $this->load->model('apolice_movimentacao_model', 'movimentacao');


        $pedido = $this->get($pedido_id);




        //pega as configurações de cancelamento do pedido
        $produto_parceiro = $this->getPedidoProdutoParceiro($pedido_id);


        $produto_parceiro = $produto_parceiro[0];
        $produto_parceiro_cancelamento = $this->cancelamento->filter_by_produto_parceiro($produto_parceiro['produto_parceiro_id'])->get_all();

        $produto_parceiro_cancelamento = $produto_parceiro_cancelamento[0];



        $apolices = $this->apolice->getApolicePedido($pedido_id);

        $apolice = $apolices[0];

        $valor_estorno_total = 0;

        if($vigente == FALSE){
            //FAZ CALCULO DO VALOR COMPLETO

            foreach ($apolices as $apolice) {

                $valor_premio = $apolice['valor_premio_total'];

                $valor_estorno = app_calculo_valor($produto_parceiro_cancelamento['seg_antes_calculo'], $produto_parceiro_cancelamento['seg_antes_valor'], $valor_premio);

                $dados_apolice = array();

                $dados_apolice['data_cancelamento'] = date('Y-m-d H:i:s');
                $dados_apolice['valor_estorno'] = $valor_estorno;
                $valor_estorno_total += $valor_estorno;
                $this->apolice_seguro_viagem->update($apolice['apolice_seguro_viagem_id'],  $dados_apolice, TRUE);

                if($ins_movimentacao) {
                    $this->movimentacao->insMovimentacao('C', $apolice['apolice_id']);
                }

            }


        }else{
            //FAZ CALCULO DO VALOR PARCIAL

            $dias_restantes = app_date_get_diff_dias(date('d/m/Y'), app_dateonly_mysql_to_mask($apolice['data_fim_vigencia']), 'D');
            $dias_utilizado = app_date_get_diff_dias(app_dateonly_mysql_to_mask($apolice['data_ini_vigencia']), date('d/m/Y'),  'D') + 1;
            $dias_total = app_date_get_diff_dias(app_dateonly_mysql_to_mask($apolice['data_ini_vigencia']), app_dateonly_mysql_to_mask($apolice['data_fim_vigencia']),  'D') + 1;

            $porcento_nao_utilziado = (($dias_restantes / $dias_total) * 100);


            foreach ($apolices as $apolice) {

                $valor_premio = $apolice['valor_premio_total'];

                $valor_premio = (($porcento_nao_utilziado / 100) * $valor_premio);


                $valor_estorno = app_calculo_valor($produto_parceiro_cancelamento['seg_depois_calculo'], $produto_parceiro_cancelamento['seg_depois_valor'], $valor_premio);

                $dados_apolice = array();

                $dados_apolice['data_cancelamento'] = date('Y-m-d H:i:s');
                $dados_apolice['valor_estorno'] = $valor_estorno;
                $valor_estorno_total += $valor_estorno;
                $this->apolice_seguro_viagem->update($apolice['apolice_seguro_viagem_id'],  $dados_apolice, TRUE);

                $this->movimentacao->insMovimentacao('C', $apolice['apolice_id']);

            }

        }


        $this->pedido_transacao->insStatus($pedido_id, 'cancelado', "PEDIDO CANCELADO COM SUCESSO");

        $this->fatura->insertFaturaEstorno($pedido_id, $valor_estorno_total);

    }

    function executa_extorno_upgrade($pedido_id){


        $this->load->model('produto_parceiro_cancelamento_model', 'cancelamento');
        $this->load->model("apolice_model", "apolice");
        $this->load->model("fatura_model", "fatura");
        $this->load->model("apolice_seguro_viagem_model", "apolice_seguro_viagem");
        $this->load->model("apolice_equipamento_model", "apolice_equipamento");
        $this->load->model('pedido_transacao_model', 'pedido_transacao');
        $this->load->model('apolice_movimentacao_model', 'movimentacao');



        $apolices = $this->apolice->getApolicePedido($pedido_id);

        $apolice = $apolices[0];

        $produto = $this->getPedidoProdutoParceiro($pedido_id);

        $produto = $produto[0];

        $valor_estorno_total = 0;

        foreach ($apolices as $apolice) {


            if($produto['slug'] == 'seguro_viagem') {
                $valor_premio = $apolice['valor_premio_total'];
                $valor_estorno = $valor_premio;
                $dados_apolice = array();
                $dados_apolice['data_cancelamento'] = date('Y-m-d H:i:s');
                $dados_apolice['valor_estorno'] = $valor_estorno;
                $valor_estorno_total += $valor_estorno;
                $this->apolice_seguro_viagem->update($apolice['apolice_seguro_viagem_id'], $dados_apolice, TRUE);
            }elseif($produto['slug'] == 'equipamento'){
                $valor_premio = $apolice['valor_premio_total'];
                $valor_estorno = $valor_premio;
                $dados_apolice = array();
                $dados_apolice['data_cancelamento'] = date('Y-m-d H:i:s');
                $dados_apolice['valor_estorno'] = $valor_estorno;
                $valor_estorno_total += $valor_estorno;
                $this->apolice_seguro_viagem->update($apolice['apolice_equipamento_id'], $dados_apolice, TRUE);
            }


        }


        $this->fatura->insertFaturaEstorno($pedido_id, $valor_estorno_total);

    }

    /**
     * Retorna todos permitidos
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function get_all($limit = 0, $offset = 0)
    {
        //Efetua join com cotação
        $this->_database->join("cotacao as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

        $this->processa_parceiros_permitidos("cotacao_filtro.parceiro_id");

        return parent::get_all($limit, $offset);
    }

    /**
     * Retorna todos
     * @return mixed
     */
    public function get_total()
    {
        //Efetua join com cotação
        $this->_database->join("cotacao as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

        $this->processa_parceiros_permitidos("cotacao_filtro.parceiro_id");

        return parent::get_total(); // TODO: Change the autogenerated stub
    }

    /**
     * Extrai relatório de vendas
     */
    public function extrairRelatorioVendas()
    {
        $this->_database->select("{$this->_table}.*, csv.*, c.*, ps.nome as status, p.nome_fantasia,
                                  pp.nome as nome_produto_parceiro, pr.nome as nome_produto, ppp.nome as plano_nome");

        $this->_database->from($this->_table);

        $this->_database->join("cotacao c", "c.cotacao_id = {$this->_table}.cotacao_id", "inner");
        $this->_database->join("cotacao_status cs", "cs.cotacao_status_id = c.cotacao_status_id", "inner");
        $this->_database->join("pedido_status ps", "ps.pedido_status_id = {$this->_table}.pedido_status_id", "inner");
        $this->_database->join("parceiro p", "p.parceiro_id = c.parceiro_id", "inner");
        $this->_database->join("cotacao_seguro_viagem csv", "csv.cotacao_id = {$this->_table}.cotacao_id and csv.deletado = 0", "left");
        $this->_database->join("produto_parceiro_plano ppp", "ppp.produto_parceiro_plano_id = csv.produto_parceiro_plano_id", "left");
        $this->_database->join("produto_parceiro pp", "pp.produto_parceiro_id = csv.produto_parceiro_id", "left");
        $this->_database->join("produto pr", "pr.produto_id = pp.produto_id", "left");

        $this->_database->where("cs.slug = 'finalziada'"); /* @TODO MUDAR PARA finalizada */
        $query = $this->_database->get();


        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return array();

    }



    /**
     * Muda status do pedido
     * @param $id_pedido
     * @param $status
     * @return bool
     */
    public function mudaStatus($id_pedido, $status)
    {
        $this->load->model("pedido_status_model", "pedido_status");

        $pedidoStatus = $this->pedido_status->get_by(array(
            'slug' => $status
        ));

        if($pedidoStatus)
        {
            $retorno = $this->update($id_pedido, array(
                'pedido_status_id' => $pedidoStatus['pedido_status_id']
            ), true);

            if($retorno)
                return true;
        }
        return false;
    }


    /**
     * Insere pedido
     * @param $dados
     * @return mixed
     */
    public function insertPedido($dados)
    {

        //Carrega models
        $this->load->model('fatura_model', 'fatura');
        $this->load->model('fatura_parcela_model', 'fatura_parcela');
        $this->load->model('pedido_codigo_model', 'pedido_codigo');
        $this->load->model('pedido_cartao_model', 'pedido_cartao');
        $this->load->model('pedido_transacao_model', 'pedido_transacao');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');

        $this->load->model('forma_pagamento_model', 'forma_pagamento');

        //se é debito ou credito
        if($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_CARTAO_CREDITO){
            $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
        }elseif($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_CARTAO_DEBITO){
            $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
        }elseif($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_FATURADO){
            $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_FATURADO)->limit(1)->get_all();
            $item = $item[0];
        }elseif ($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_BOLETO){
            $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_BOLETO)->limit(1)->get_all();
            $item = $item[0];
        }

        $cotacao = $this->cotacao->get_cotacao_produto($dados['cotacao_id']);
        switch ($cotacao['produto_slug']) {
            case 'seguro_viagem':
                $valor_total = $this->cotacao_seguro_viagem->getValorTotal($dados['cotacao_id']);
                break;
            case 'equipamento':
                $valor_total = $this->cotacao_equipamento->getValorTotal($dados['cotacao_id']);
                break;
            case 'generico':
                $valor_total = $this->cotacao_generico->getValorTotal($dados['cotacao_id']);
                break;
            case 'seguro_saude':
                $valor_total = $this->cotacao_generico->getValorTotal($dados['cotacao_id']);
                break;
        }

        //Se for um upgrade
        /*
        if((int)$cotacao['cotacao_upgrade_id'] > 0)
        {
            $upgrade_id = (int)$cotacao['cotacao_upgrade_id'];
            $upgrade = $this->cotacao->get($upgrade_id);

            $pedido_upgrade = $this->pedido->get_by(array('cotacao_id' => $upgrade['cotacao_id']));

            $valor_total = $valor_total + $pedido_upgrade['valor_total'];
        } */

        $parcelamento = array();
        if( isset( $item['parcelamento_maximo'] ) ) {
          for($i = 1; $i <= $item['parcelamento_maximo'];$i++){
            if($i <= $item['parcelamento_maximo_sem_juros']) {
              $parcelamento[$i] = $valor_total/$i;
            }else{
              $valor = ($valor_total/(1-($item['juros_parcela']/100)))/$i;
              // $valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
              $parcelamento[$i] = $valor;
            }
          }
        }
      
      //error_log( print_r( $dados, true ) . "\n", 3, "/var/log/nginx/360.log" );
      //error_log( print_r( $item, true ) . "\n", 3, "/var/log/nginx/360.log" );
      
        $dados_pedido = array();
        $dados_pedido['cotacao_id'] = $dados['cotacao_id'];
        $dados_pedido['produto_parceiro_pagamento_id'] = isset( $item['produto_parceiro_pagamento_id'] ) ? $item['produto_parceiro_pagamento_id'] : "";
        $dados_pedido['pedido_status_id'] = 1;
        $dados_pedido['codigo'] = $this->pedido_codigo->get_codigo_pedido_formatado('BE');
        $dados_pedido['status_data'] = date('Y-m-d H:i:s');
        $dados_pedido['valor_total'] = $valor_total;
        $dados_pedido['num_parcela'] = $dados["parcelamento_{$dados['bandeira']}"];
        $dados_pedido['valor_parcela'] = $parcelamento[$dados["parcelamento_{$dados['bandeira']}"]];
        $dados_pedido['alteracao_usuario_id'] = $this->session->userdata('usuario_id');

        $pedido_id = $this->insert($dados_pedido, TRUE);
        $this->pedido_transacao->insStatus($pedido_id, 'criado');

        $this->insDadosPagamento($dados, $pedido_id);

        //altera o status da cotação
        $this->cotacao->update($dados['cotacao_id'], array('cotacao_status_id' => 2), TRUE);


        $this->fatura->insFaturaParcelas($pedido_id, $dados['cotacao_id'], 1, $valor_total, $dados_pedido['num_parcela'], $dados_pedido['valor_parcela'], $dados['produto_parceiro_id']);


        return $pedido_id;

    }

    public function updatePedido($pedido_id, $dados){


        $this->load->model('fatura_model', 'fatura');
        $this->load->model('fatura_parcela_model', 'fatura_parcela');

        $this->load->model('pedido_codigo_model', 'pedido_codigo');
        $this->load->model('pedido_cartao_model', 'pedido_cartao');
        $this->load->model('pedido_transacao_model', 'pedido_transacao');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
        $this->load->model('forma_pagamento_model', 'forma_pagamento');



        $this->load->model('produto_parceiro_capitalizacao_model', 'parceiro_capitalizacao');
        $this->load->model('capitalizacao_model', 'capitalizacao');
        $this->load->model('capitalizacao_serie_titulo_model', 'titulo');


      
        //  $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
        if($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_CARTAO_CREDITO) {
            $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
        }elseif($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_CARTAO_DEBITO){
            $item = $this->produto_pagamento->get_by_id($dados['bandeira_debito']);
        }elseif($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_FATURADO){
            $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_FATURADO)->limit(1)->get_all();
            $item = $item[0];
        }elseif ($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_BOLETO){
            $item = $this->produto_pagamento->with_forma_pagamento()->filter_by_forma_pagamento_tipo(self::FORMA_PAGAMENTO_BOLETO)->limit(1)->get_all();
            $item = $item[0];
        }

        $cotacao = $this->cotacao->get_cotacao_produto($dados['cotacao_id']);
        switch ($cotacao['produto_slug']) {
            case 'seguro_viagem':
                $valor_total = $this->cotacao_seguro_viagem->getValorTotal($dados['cotacao_id']);
                break;
            case 'equipamento':
                $valor_total = $this->cotacao_equipamento->getValorTotal($dados['cotacao_id']);
                break;
            case 'generico':
                $valor_total = $this->cotacao_generico->getValorTotal($dados['cotacao_id']);
                break;
            case 'seguro_saude':
                $valor_total = $this->cotacao_generico->getValorTotal($dados['cotacao_id']);
                break;
        }

		
       $item['juros_parcela'] = (isset($item['juros_parcela'])) ? $item['juros_parcela'] : 0;
       $item['parcelamento_maximo'] = (isset($item['parcelamento_maximo'])) ? $item['parcelamento_maximo'] : 1;
      $item['parcelamento_maximo_sem_juros'] = (isset($item['parcelamento_maximo_sem_juros'])) ? $item['parcelamento_maximo_sem_juros'] : 1;
        $parcelamento = array();
        for($i = 1; $i <= $item['parcelamento_maximo'];$i++){
            if($i <= $item['parcelamento_maximo_sem_juros']) {
                $parcelamento[$i] = $valor_total/$i;
            }else{
                $valor = ($valor_total/(1-($item['juros_parcela']/100)))/$i;
                //$valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
                $parcelamento[$i] = $valor;
            }
        }


        $dados_pedido = array();
        $dados_pedido['produto_parceiro_pagamento_id'] = $item['produto_parceiro_pagamento_id'];
        $dados_pedido['valor_total'] = $valor_total;
        $dados_pedido['num_parcela'] = $dados["parcelamento_{$dados['bandeira']}"];
        $dados_pedido['valor_parcela'] = $parcelamento[$dados["parcelamento_{$dados['bandeira']}"]];
        $dados_pedido['alteracao_usuario_id'] = $this->session->userdata('usuario_id');

        $this->update($pedido_id,  $dados_pedido, TRUE);
        $this->pedido_transacao->insStatus($pedido_id, 'alteracao');


        //altera o status da cotação
        $this->cotacao->update($dados['cotacao_id'], array('cotacao_status_id' => 2), TRUE);

        //insere faturamento



        $this->fatura->deleteFaturamento($pedido_id);
        $this->fatura->insFaturaParcelas($pedido_id, $dados['cotacao_id'], 1, $valor_total, $dados_pedido['num_parcela'],
                                         $dados_pedido['valor_parcela'], $cotacao['produto_parceiro_id']);

        return $pedido_id;

    }

    public function insDadosPagamento($dados, $pedido_id){

        $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
        $this->load->model('forma_pagamento_model', 'forma_pagamento');
        $this->load->model('pedido_cartao_model', 'pedido_cartao');
        $this->load->model('pedido_transacao_model', 'pedido_transacao');

        if($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_CARTAO_CREDITO){

            $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
            $forma_pagamento = $this->forma_pagamento->get_by_id($item['forma_pagamento_id']);

            $this->load->library('encrypt');

            $dados_cartao = array();
            $dados_cartao['pedido_id'] = $pedido_id;
            $dados_cartao['numero'] = $this->encrypt->encode(app_retorna_numeros($dados['numero']));
            $dt = explode('/', $dados['validade']);
            $date = mktime(0, 0, 0, (int)trim($dt[0]), 1, (int)trim($dt[1]));
            $dados_cartao['validade'] = $this->encrypt->encode(date('Ym',$date));
            $dados_cartao['nome'] = $this->encrypt->encode($dados['nome_cartao']);
            $dados_cartao['codigo'] = $this->encrypt->encode($dados['codigo']);
            $dados_cartao['bandeira'] = $this->encrypt->encode($forma_pagamento['slug']);
            $dados_cartao['bandeira_cartao'] = $this->encrypt->encode($dados['bandeira_cartao']);

            //Se possuir, insere
            if($this->input->post("dia_vencimento"))
                $dados_cartao['dia_vencimento'] =  $this->encrypt->encode($dados['dia_vencimento']);

            $dados_cartao['processado'] = 0;
            //
            $this->pedido_cartao->update_by(
                array('pedido_id' =>$pedido_id),array(
                    'ativo' => 0
                )
            );


            $this->pedido_cartao->insert($dados_cartao, true);
            $this->pedido_transacao->insStatus($pedido_id, 'aguardando_pagamento');
        }elseif($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_CARTAO_DEBITO){

            $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
            $forma_pagamento = $this->forma_pagamento->get_by_id($item['forma_pagamento_id']);

            $this->load->library('encrypt');

            $dados_cartao = array();
            $dados_cartao['pedido_id'] = $pedido_id;
            $dados_cartao['numero'] = $this->encrypt->encode(app_retorna_numeros($dados['numero_debito']));
            $dt = explode('/', $dados['validade_debito']);
            $date = mktime(0, 0, 0, (int)trim($dt[0]), 1, (int)trim($dt[1]));
            $dados_cartao['validade'] = $this->encrypt->encode(date('Ym',$date));
            $dados_cartao['nome'] = $this->encrypt->encode($dados['nome_cartao_debito']);
            $dados_cartao['codigo'] = $this->encrypt->encode($dados['codigo_debito']);
            $dados_cartao['bandeira'] = $this->encrypt->encode($forma_pagamento['slug']);
            $dados_cartao['bandeira_cartao'] = $this->encrypt->encode($dados['bandeira_cartao_debito']);


            $dados_cartao['processado'] = 0;
            $this->pedido_cartao->update_by(
                array('pedido_id' => $pedido_id),
                array('ativo' => 0)
            );

            $this->pedido_cartao->insert($dados_cartao, true);
            $this->pedido_transacao->insStatus($pedido_id, 'aguardando_pagamento');
        }elseif($dados['forma_pagamento_tipo_id'] == self::FORMA_PAGAMENTO_BOLETO){

            print_r($dados);exit;
            $item = $this->produto_pagamento->get_by_id($dados['bandeira']);
            $forma_pagamento = $this->forma_pagamento->get_by_id($item['forma_pagamento_id']);

            $this->load->library('encrypt');

            $dados_cartao = array();
            $dados_cartao['pedido_id'] = $pedido_id;
            $dados_cartao['numero'] = $this->encrypt->encode(app_retorna_numeros($dados['numero_debito']));
            $dt = explode('/', $dados['validade_debito']);
            $date = mktime(0, 0, 0, (int)trim($dt[0]), 1, (int)trim($dt[1]));
            $dados_cartao['validade'] = $this->encrypt->encode(date('Ym',$date));
            $dados_cartao['nome'] = $this->encrypt->encode($dados['nome_cartao_debito']);
            $dados_cartao['codigo'] = $this->encrypt->encode($dados['codigo_debito']);
            $dados_cartao['bandeira'] = $this->encrypt->encode($forma_pagamento['slug']);
            $dados_cartao['bandeira_cartao'] = $this->encrypt->encode($dados['bandeira_cartao_debito']);


            $dados_cartao['processado'] = 0;
            $this->pedido_cartao->update_by(
                array('pedido_id' => $pedido_id),
                array('ativo' => 0)
            );

            $this->pedido_cartao->insert($dados_cartao, true);
            $this->pedido_transacao->insStatus($pedido_id, 'aguardando_pagamento');
        }

    }
}










