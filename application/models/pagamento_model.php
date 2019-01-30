<?php
class Pagamento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'produto_parceiro_pagamento';
    protected $primary_key = 'produto_parceiro_pagamento_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome_fatura');

    public function __construct()
    {
        parent::__construct();

        //Carrega modelos
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('produto_parceiro_pagamento_model', 'parceiro_pagamento');
    }

    public function run($pedido_id = 0)
    {

        $result = [];
        log_message('debug', 'INICIO PAGMAX');
        $pedidos = $this->pedido->getPedidoPagamentoPendente($pedido_id);

        log_message('debug', 'BUSCANDO PEDIDOS PENDENTES - ' . count($pedidos));
        foreach ($pedidos as $index => $pedido) {

            log_message('debug', 'PEDIDO ' . $pedido['pedido_id']);
            //verifica se exite cartão não processado
            try {
                //faz um lock no pedido
                log_message('debug', 'LOCK NO PEDIDO ' . $pedido['pedido_id']);
                $dados_pedido = array('lock' => 1);
                $this->pedido->update($pedido['pedido_id'], $dados_pedido, true);

                $parceiro_pagamento = $this->parceiro_pagamento->with_forma_pagamento()
                    ->with_forma_pagamento_tipo()
                    ->get($pedido['produto_parceiro_pagamento_id']);

                // PAGMAX
                if ($parceiro_pagamento['forma_pagamento_integracao_id'] == $this->config->item("INTEGRACAO_PAGMAX")) {

                    $this->load->model('pagmax_model', 'pagmax');
                    $result = $this->pagmax->realiza_pagamento($pedido['pedido_id'], $parceiro_pagamento['forma_pagamento_tipo_id']);

                }
                
                log_message('debug', 'UNLOCK NO PEDIDO ' . $pedido['pedido_id']);
                $dados_pedido = array('lock' => 0);
                $this->pedido->update($pedido['pedido_id'], $dados_pedido, true);
            } catch (Exception $e) {
                log_message('debug', 'UNLOCK NO PEDIDO (ERROR) ' . $pedido['pedido_id']);
                $dados_pedido = array('lock' => 0);
                $this->pedido->update($pedido['pedido_id'], $dados_pedido, true);
            }
        }

        return $result;
    }

}
