<?php
class Apolice_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'apolice';
    protected $primary_key = 'apolice_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();

    //Dados
    public $validate = array(

    );

    public function disparaEventoErroApolice($pedido_id)
    {
        $this->load->model('produto_parceiro_configuracao_model', 'parceiro_configuracao');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_seguro_viagem_pessoa_model', 'cotacao_pessoa');
        $this->load->model('apolice_seguro_viagem_model', 'apolice_seguro_viagem');

        $this->load->model('apolice_numero_seq_model', 'apolice_seq');
        $this->load->model('produto_parceiro_apolice_range_model', 'apolice_range');

        $this->load->model('produto_parceiro_model', 'produto_parceiro');

        $pedido = $this->pedido->getPedidoProdutoParceiro($pedido_id);
        $pedido = $pedido[0];

        //obter numero da apolice;
        $configuracao = $this->parceiro_configuracao->filter_by_produto_parceiro($pedido['produto_parceiro_id'])->get_all();
        $configuracao = $configuracao[0];

        $produto_parceiro = $this->produto_parceiro->with_produto()->get($pedido['produto_parceiro_id']);

        if ($produto_parceiro['produto_slug'] == 'seguro_viagem') {
            $cotacao_salvas = $this->cotacao->with_cotacao_seguro_viagem()
                ->filterByID($pedido['cotacao_id'])
                ->get_all();
        } elseif ($produto_parceiro['produto_slug'] == 'equipamento') {

            $cotacao_salvas = $this->cotacao->with_cotacao_equipamento()
                ->filterByID($pedido['cotacao_id'])
                ->get_all();
        } elseif ($produto_parceiro["produto_slug"] == "generico" || $produto_parceiro["produto_slug"] == "seguro_saude") {

            $cotacao_salvas = $this->cotacao->with_cotacao_generico()
                ->filterByID($pedido['cotacao_id'])
                ->get_all();
        }

        //Eventos
        $evento                         = array();
        $evento['mensagem']             = array();
        $evento['mensagem']['apolices'] = "";
        $evento['mensagem']['nome']     = "";

        if ($produto_parceiro['produto_slug'] == 'seguro_viagem') {
            foreach ($cotacao_salvas as $cotacao_salva) {
                $cotacao_pessoas                 = $this->cotacao_pessoa->filter_by_seguro_viagem($cotacao_salva['cotacao_seguro_viagem_id'])->get_all();
                $evento['mensagem']['nome']      = $cotacao_pessoas[0]['nome'];
                $evento['destinatario_email']    = $cotacao_pessoas[0]['email'];
                $evento['destinatario_telefone'] = $cotacao_pessoas[0]['contato_telefone'];
                $evento['produto_parceiro_id']   = $pedido['produto_parceiro_id'];
            }

        } elseif ($produto_parceiro['produto_slug'] == 'equipamento') {

            $cotacao_salva                   = $cotacao_salvas[0];
            $evento['mensagem']['nome']      = $cotacao_salva['nome'];
            $evento['destinatario_email']    = $cotacao_salva['email'];
            $evento['destinatario_telefone'] = $cotacao_salva['telefone'];
            $evento['produto_parceiro_id']   = $pedido['produto_parceiro_id'];

        } elseif ($produto_parceiro["produto_slug"] == "generico" || $produto_parceiro["produto_slug"] == "seguro_saude") {

            $cotacao_salva                   = $cotacao_salvas[0];
            $evento['mensagem']['nome']      = $cotacao_salva['nome'];
            $evento['destinatario_email']    = $cotacao_salva['email'];
            $evento['destinatario_telefone'] = $cotacao_salva['telefone'];
            $evento['produto_parceiro_id']   = $pedido['produto_parceiro_id'];
        }

        /**
         * Dispara email
         */
        $comunicacao = new Comunicacao();
        $comunicacao->setMensagemParametros($evento['mensagem']);
        $comunicacao->setDestinatario($evento['destinatario_email']);
        $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
        $comunicacao->disparaEvento("apolice_nao_gerada_email", $evento['produto_parceiro_id']);

        /**
         * Dispara SMS
         */

        $comunicacao = new Comunicacao();
        $comunicacao->setMensagemParametros($evento['mensagem']);
        $comunicacao->setDestinatario(app_retorna_numeros($evento['destinatario_telefone']));
        $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
        $comunicacao->disparaEvento("apolice_nao_gerada_sms", $evento['produto_parceiro_id']);
    }

    public function insertApolice($pedido_id)
    {

        $this->load->model('pedido_model', 'pedido');

        $apolice = $this->get_many_by(array('pedido_id' => $pedido_id));

        if ($apolice) {
            return;
        }

        //$produto = $this->pedido->getPedidoProdutoParceiro( $pedido_id );

        $produto = $this->db->query("SELECT
                                      pedido.pedido_id,
                                      pedido.cotacao_id,
                                      pedido.produto_parceiro_pagamento_id,
                                      pedido.num_parcela,
                                      pedido.valor_parcela,
                                      cotacao.produto_parceiro_id,
                                      produto.slug,
                                      produto_parceiro.parceiro_id,
                                      produto_parceiro_apolice.template as template_apolice
                                  FROM
                                      pedido
                                      INNER JOIN cotacao ON ( cotacao.cotacao_id = pedido.cotacao_id )
                                      INNER JOIN  produto_parceiro ON ( cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id)
                                      INNER JOIN produto ON (produto.produto_id = produto_parceiro.produto_id)
                                      LEFT JOIN produto_parceiro_apolice ON ( produto_parceiro_apolice.produto_parceiro_id = produto_parceiro.produto_parceiro_id)
                                      LEFT JOIN cotacao_seguro_viagem ON ( cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id)
                                      LEFT JOIN cotacao_equipamento ON ( cotacao_equipamento.cotacao_id = cotacao.cotacao_id)
                                      LEFT JOIN cotacao_generico ON ( cotacao_generico.cotacao_id = cotacao.cotacao_id)
                                  WHERE
                                      pedido.pedido_id IN ($pedido_id) LIMIT 1")->result_array();

        if ($produto) {
            $produto = $produto[0];
            if ($produto['slug'] == 'seguro_viagem') {
                $this->insertSeguroViagem($pedido_id);
            } elseif ($produto['slug'] == 'equipamento') {
                $this->insertSeguroEquipamento($pedido_id);
            } elseif ($produto["slug"] == "generico" || $produto["slug"] == "seguro_saude") {
                $this->insertSeguroGenerico($pedido_id);
            }
        }

    }

    public function concluiApolice($pedido, $apolice_id)
    {
        $this->load->model('apolice_cobertura_model', 'apolice_cobertura');
        $this->load->model('apolice_movimentacao_model', 'movimentacao');

        $pedido_id = $pedido['pedido_id'];
        $produto_parceiro_id = $pedido['produto_parceiro_id'];
        $cotacao_id = $pedido['cotacao_id'];

        $this->insertCapitalizacao($produto_parceiro_id, $pedido_id);

        $this->movimentacao->insMovimentacao('A', $apolice_id, $pedido);

        $this->apolice_cobertura->deleteByCotacao($cotacao_id);

        $coberturas = $this->cotacao_cobertura
            ->filterByID($cotacao_id)
            ->get_all();

        foreach ($coberturas as $cobertura) {

            $dados_apolice_cobertura = [
                'cotacao_id'         => $cotacao_id,
                'pedido_id'          => $pedido_id,
                'apolice_id'         => $apolice_id,
                'cobertura_plano_id' => $cobertura["cobertura_plano_id"],
                'valor'              => $cobertura["valor"],
                'iof'                => $cobertura["iof"],
                'mostrar'            => $cobertura["mostrar"],
                'valor_config'       => $cobertura['valor_config'],
                'criacao'            => date("Y-m-d H:i:s"),
            ];

            $this->apolice_cobertura->insert($dados_apolice_cobertura, true);
        }

    }

    public function insertSeguroEquipamento($pedido_id)
    {

        $this->load->model('produto_parceiro_configuracao_model', 'parceiro_configuracao');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('cotacao_cobertura_model', 'cotacao_cobertura');

        $this->load->model('apolice_numero_seq_model', 'apolice_seq');
        $this->load->model('apolice_equipamento_model', 'apolice_equipamento');
        $this->load->model('produto_parceiro_apolice_range_model', 'apolice_range');

        $this->load->model('produto_parceiro_desconto_model', 'parceiro_desconto');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');

        $this->load->model("cliente_contato_model", "cliente_contato");
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_evolucao_model', 'cliente_evolucao');

        //Eventos
        $evento                         = array();
        $evento['mensagem']             = array();
        $evento['mensagem']['apolices'] = "";
        $evento['mensagem']['nome']     = "";
        $evento['mensagem']['anexos']   = array();

        $pedido = $this->pedido->getPedidoProdutoParceiro($pedido_id);
        $pedido = $pedido[0];

        //obter numero da apolice;
        $configuracao = $this->parceiro_configuracao->filter_by_produto_parceiro($pedido['produto_parceiro_id'])->get_all();
        $configuracao = $configuracao[0];

        //obter configurações de desconto
        $desconto_condicional = $this->parceiro_desconto->filter_by_produto_parceiro($pedido['produto_parceiro_id'])->get_all();
        if ($desconto_condicional) {
            $desconto_condicional = $desconto_condicional[0];
        }

        $cotacao_salvas = $this->cotacao->with_cotacao_equipamento()
            ->filterByID($pedido['cotacao_id'])
            ->get_all();

        log_message('debug', 'APOLICE 1');
        log_message('debug', 'COTAÇÃO: ' . print_r($cotacao_salvas, true));

        foreach ($cotacao_salvas as $cotacao_salva) {

            log_message('debug', 'APOLICE 2' . print_r($cotacao_salva, true));

            if ($desconto_condicional) {
                if ($cotacao_salva['desconto_condicional_valor'] > 0) {
                    $dados_saldo              = array();
                    $dados_saldo['utilizado'] = $desconto_condicional['utilizado'] + $cotacao_salva['desconto_condicional_valor'];
                    $this->parceiro_desconto->update($desconto_condicional['produto_parceiro_desconto_id'], $dados_saldo, true);
                }

            }

            log_message('debug', 'UPDATE STATUS CLIENTE');

            $data_cliente                               = array();
            $data_cliente['cliente_evolucao_status_id'] = 4;
            $this->cliente->update($cotacao_salva['cliente_id'], $data_cliente, true);

            $dados_apolice                              = array();
            $dados_apolice['pedido_id']                 = $pedido_id;
            $dados_apolice['produto_parceiro_plano_id'] = $cotacao_salva['produto_parceiro_plano_id'];
            $dados_apolice['parceiro_id']               = $cotacao_salva['parceiro_id']; //$this->session->userdata('parceiro_id'); //parceiro da venda
            $dados_apolice['apolice_status_id']         = 1;

            if ($configuracao['apolice_sequencia'] == 1) {
                //é número Sequencial
                $dados_apolice['num_apolice'] = $this->apolice_seq->get_proximo_codigo($pedido['produto_parceiro_id']);
            } else {
                //não é número Sequencial
                $dados_apolice['num_apolice'] = $this->apolice_range->get_proximo_codigo($pedido['produto_parceiro_id']);
            }

            $produto_parceiro_plano_id = $cotacao_salva["produto_parceiro_plano_id"];
            $vigencia = $this->produto_parceiro_plano->getInicioFimVigencia($cotacao_salva['produto_parceiro_plano_id'], null, $cotacao_salva);

            $apolice_id                                         = $this->insert($dados_apolice, true);
            $dados_equipamento                                  = array();
            $dados_equipamento['apolice_id']                    = $apolice_id;
            $dados_equipamento['produto_parceiro_pagamento_id'] = $pedido['produto_parceiro_pagamento_id'];
            $dados_equipamento['data_ini_vigencia']             = $vigencia['inicio_vigencia'];
            $dados_equipamento['data_fim_vigencia']             = $vigencia['fim_vigencia'];
            $dados_equipamento['data_adesao']                   = $vigencia['data_adesao'];
            $dados_equipamento['data_pagamento']                = $vigencia['data_adesao'];

            $dados_equipamento['cnpj_cpf']        = $cotacao_salva['cnpj_cpf'];
            $dados_equipamento['rg']              = $cotacao_salva['rg'];
            $dados_equipamento['nome']            = $cotacao_salva['nome'];
            $dados_equipamento['nome_mae']        = $cotacao_salva['nome'];
            $dados_equipamento['data_nascimento'] = $cotacao_salva['data_nascimento'];
            $dados_equipamento['sexo']            = $cotacao_salva['sexo'];
            $dados_equipamento['email']           = $cotacao_salva['email'];

            $dados_equipamento['ean']                      = $cotacao_salva['ean'];
            $dados_equipamento['equipamento_id']           = $cotacao_salva['equipamento_id'];
            $dados_equipamento['equipamento_nome']         = $cotacao_salva['equipamento_nome'];
            $dados_equipamento['equipamento_categoria_id'] = $cotacao_salva['equipamento_categoria_id'];
            $dados_equipamento['equipamento_marca_id']     = $cotacao_salva['equipamento_marca_id'];
            $dados_equipamento['nota_fiscal_data']         = $cotacao_salva['nota_fiscal_data'];
            $dados_equipamento['nota_fiscal_valor']        = $cotacao_salva['nota_fiscal_valor'];

            $dados_equipamento['estado_civil']       = $cotacao_salva['estado_civil'];
            $dados_equipamento['rg_orgao_expedidor'] = $cotacao_salva['rg_orgao_expedidor'];
            $dados_equipamento['rg_uf']              = $cotacao_salva['rg_uf'];
            $dados_equipamento['rg_data_expedicao']  = $cotacao_salva['rg_data_expedicao'];
            $dados_equipamento['aux_01']             = $cotacao_salva['aux_01'];
            $dados_equipamento['aux_02']             = $cotacao_salva['aux_02'];
            $dados_equipamento['aux_03']             = $cotacao_salva['aux_03'];
            $dados_equipamento['aux_04']             = $cotacao_salva['aux_04'];
            $dados_equipamento['aux_05']             = $cotacao_salva['aux_05'];
            $dados_equipamento['aux_06']             = $cotacao_salva['aux_06'];
            $dados_equipamento['aux_07']             = $cotacao_salva['aux_07'];
            $dados_equipamento['aux_08']             = $cotacao_salva['aux_08'];
            $dados_equipamento['aux_09']             = $cotacao_salva['aux_09'];
            $dados_equipamento['aux_10']             = $cotacao_salva['aux_10'];

            $dados_equipamento['endereco_logradouro']     = $cotacao_salva['endereco_logradouro'];
            $dados_equipamento['endereco_numero']         = $cotacao_salva['endereco_numero'];
            $dados_equipamento['endereco_complemento']    = $cotacao_salva['endereco_complemento'];
            $dados_equipamento['endereco_bairro']         = $cotacao_salva['endereco_bairro'];
            $dados_equipamento['endereco_cidade']         = $cotacao_salva['endereco_cidade'];
            $dados_equipamento['endereco_estado']         = $cotacao_salva['endereco_estado'];
            $dados_equipamento['endereco_cep']            = $cotacao_salva['endereco_cep'];
            $dados_equipamento['contato_telefone']        = $cotacao_salva['telefone'];
            $dados_equipamento['periodicidade_pagamento'] = 'U';
            $dados_equipamento['num_parcela']             = $pedido['num_parcela'];
            $dados_equipamento['valor_premio_total']      = round($cotacao_salva['premio_liquido_total'], 2);
            $dados_equipamento['valor_premio_net']        = round($cotacao_salva['premio_liquido'], 2);
            $dados_equipamento['comissao']                = $cotacao_salva['comissao_corretor'];
            $dados_equipamento['pro_labore']              = round(($cotacao_salva['premio_liquido_total'] - $cotacao_salva['premio_liquido']), 2);
            $dados_equipamento['valor_parcela']           = round($pedido['valor_parcela'], 2);
            $dados_equipamento['valor_estorno']           = 0;
            $dados_equipamento['valor_desconto']          = round($cotacao_salva['valor_desconto'], 2);

            $this->apolice_equipamento->insert($dados_equipamento, true);
            $this->concluiApolice($pedido, $apolice_id);

            $evento['mensagem']['apolices'] .= "Nome: {$dados_equipamento['nome']} - Apólice código: {$apolice_id} <br>";
            $evento['mensagem']['apolice_codigo'] = $this->get_codigo_apolice($apolice_id);
            $evento['mensagem']['anexos'][]       = $this->certificado($apolice_id, 'pdf_file');

        }

        if (isset($cotacao_salvas[0])) {
            log_message('debug', 'APOLICE 4');
            $cliente_contato            = array();
            $cliente_contato['nome']    = '';
            $cliente_contato['email']   = '';
            $cliente_contato['celular'] = '';
            $contatos                   = $this->cliente_contato->with_contato()->get_by_cliente($cotacao_salvas[0]['cliente_id']);
            if (count($contatos) > 0) {

                foreach ($contatos as $contato) {
                    $cliente_contato['nome']    = (empty($cliente_contato['nome'])) ? $contato['nome'] : $cliente_contato['nome'];
                    $cliente_contato['email']   = ($contato['contato_tipo_id'] == 1 && empty($cliente_contato['email'])) ? $contato['contato'] : $cliente_contato['email'];
                    $cliente_contato['celular'] = ($contato['contato_tipo_id'] == 2 && empty($cliente_contato['celular'])) ? $contato['contato'] : $cliente_contato['celular'];
                }
            }

            $evento['mensagem']['url'] = base_url();
            if (isset($cliente_contato) && isset($pedido['produto_parceiro_id'])) {

                log_message('debug', 'APOLICE 5');
                $evento['destinatario_email']    = isset($cliente_contato['email']) ? $cliente_contato['email'] : '';
                $evento['produto_parceiro_id']   = $pedido['produto_parceiro_id'];
                $evento['mensagem']['nome']      = isset($cliente_contato['nome']) ? $cliente_contato['nome'] : '';
                $evento['destinatario_telefone'] = isset($cliente_contato['celular']) ? $cliente_contato['celular'] : '';

                /**
                 * Dispara email
                 */

                log_message('debug', 'APOLICE DISPARO EMAIL');
                if (!empty($evento['destinatario_email'])) {
                    $comunicacao = new Comunicacao();
                    $comunicacao->setMensagemParametros($evento['mensagem']);
                    $comunicacao->setDestinatario($evento['destinatario_email']);
                    $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
                    $comunicacao->disparaEvento("apolice_gerada_email", $evento['produto_parceiro_id']);
                }

                /**
                 * Dispara SMS
                 */
                log_message('debug', 'APOLICE DISPARO SMS');
                log_message('debug', print_r($evento, true));
                if (!empty($evento['destinatario_telefone'])) {
                    $comunicacao = new Comunicacao();
                    $comunicacao->setMensagemParametros($evento['mensagem']);
                    $comunicacao->setDestinatario(app_retorna_numeros($evento['destinatario_telefone']));
                    $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
                    $comunicacao->disparaEvento("apolice_gerada_sms", $evento['produto_parceiro_id']);
                }

            }
        }

    }

    public function insertSeguroGenerico($pedido_id)
    {
        $this->load->model('produto_parceiro_configuracao_model', 'parceiro_configuracao');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_cobertura_model', 'cotacao_cobertura');

        $this->load->model('apolice_numero_seq_model', 'apolice_seq');
        $this->load->model('apolice_generico_model', 'apolice_generico');
        $this->load->model('produto_parceiro_apolice_range_model', 'apolice_range');

        $this->load->model('produto_parceiro_desconto_model', 'parceiro_desconto');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');

        $this->load->model("cliente_contato_model", "cliente_contato");
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_evolucao_model', 'cliente_evolucao');

        //Eventos
        $evento                         = array();
        $evento['mensagem']             = array();
        $evento['mensagem']['apolices'] = "";
        $evento['mensagem']['nome']     = "";
        $evento['mensagem']['anexos']   = array();

        $pedido = $this->pedido->getPedidoProdutoParceiro($pedido_id);
        $pedido = $pedido[0];

        //obter numero da apolice;
        $configuracao = $this->parceiro_configuracao->filter_by_produto_parceiro($pedido['produto_parceiro_id'])->get_all();
        $configuracao = $configuracao[0];

        //obter configurações de desconto
        $desconto_condicional = $this->parceiro_desconto->filter_by_produto_parceiro($pedido['produto_parceiro_id'])->get_all();
        if ($desconto_condicional) {
            $desconto_condicional = $desconto_condicional[0];
        }

        $cotacao_salvas = $this->cotacao->with_cotacao_generico()
            ->filterByID($pedido['cotacao_id'])
            ->get_all();

        log_message('debug', 'APOLICE 1');
        log_message('debug', 'COTAÇÃO: ' . print_r($cotacao_salvas, true));

        foreach ($cotacao_salvas as $cotacao_salva) {

            log_message('debug', 'APOLICE 2' . print_r($cotacao_salva, true));

            if ($desconto_condicional) {
                if ($cotacao_salva['desconto_condicional_valor'] > 0) {
                    $dados_saldo              = array();
                    $dados_saldo['utilizado'] = $desconto_condicional['utilizado'] + $cotacao_salva['desconto_condicional_valor'];
                    $this->parceiro_desconto->update($desconto_condicional['produto_parceiro_desconto_id'], $dados_saldo, true);
                }

            }

            log_message('debug', 'UPDATE STATUS CLIENTE');

            $data_cliente                               = array();
            $data_cliente['cliente_evolucao_status_id'] = 4;
            $this->cliente->update($cotacao_salva['cliente_id'], $data_cliente, true);

            $dados_apolice                              = array();
            $dados_apolice['pedido_id']                 = $pedido_id;
            $dados_apolice['produto_parceiro_plano_id'] = $cotacao_salva['produto_parceiro_plano_id'];
            $dados_apolice['parceiro_id']               = $cotacao_salva['parceiro_id']; //$this->session->userdata('parceiro_id'); //parceiro da venda
            $dados_apolice['apolice_status_id']         = 1;

            if ($configuracao['apolice_sequencia'] == 1) {
                //é número Sequencial
                $dados_apolice['num_apolice'] = $this->apolice_seq->get_proximo_codigo($pedido['produto_parceiro_id']);
            } else {
                //não é número Sequencial
                $dados_apolice['num_apolice'] = $this->apolice_range->get_proximo_codigo($pedido['produto_parceiro_id']);
            }

            $produto_parceiro_plano_id = $cotacao_salva["produto_parceiro_plano_id"];
            $vigencia = $this->produto_parceiro_plano->getInicioFimVigencia($produto_parceiro_plano_id, null, $cotacao_salva);

            log_message('debug', 'VIGENCIA: ' . print_r($vigencia, true));

            $apolice_id                                      = $this->insert($dados_apolice, true);
            $dados_generico                                  = array();
            $dados_generico['apolice_id']                    = $apolice_id;
            $dados_generico['produto_parceiro_pagamento_id'] = $pedido['produto_parceiro_pagamento_id'];
            $dados_generico['data_ini_vigencia']             = $vigencia['inicio_vigencia'];
            $dados_generico['data_fim_vigencia']             = $vigencia['fim_vigencia'];
            $dados_generico['data_adesao']                   = $vigencia['data_adesao'];
            $dados_generico['data_pagamento']                = $vigencia['data_adesao'];

            $dados_generico['cnpj_cpf']                = $cotacao_salva['cnpj_cpf'];
            $dados_generico['rg']                      = $cotacao_salva['rg'];
            $dados_generico['nome']                    = $cotacao_salva['nome'];
            $dados_generico['nome_mae']                = $cotacao_salva['nome'];
            $dados_generico['data_nascimento']         = $cotacao_salva['data_nascimento'];
            $dados_generico['sexo']                    = $cotacao_salva['sexo'];
            $dados_generico['email']                   = $cotacao_salva['email'];
            $dados_generico['endereco_logradouro']     = $cotacao_salva['endereco_logradouro'];
            $dados_generico['endereco_numero']         = $cotacao_salva['endereco_numero'];
            $dados_generico['endereco_complemento']    = $cotacao_salva['endereco_complemento'];
            $dados_generico['endereco_bairro']         = $cotacao_salva['endereco_bairro'];
            $dados_generico['endereco_cidade']         = $cotacao_salva['endereco_cidade'];
            $dados_generico['endereco_estado']         = $cotacao_salva['endereco_estado'];
            $dados_generico['endereco_cep']            = $cotacao_salva['endereco_cep'];
            $dados_generico['contato_telefone']        = $cotacao_salva['telefone'];
            $dados_generico['periodicidade_pagamento'] = 'U';
            $dados_generico['num_parcela']             = $pedido['num_parcela'];
            $dados_generico['valor_premio_total']      = round($cotacao_salva['premio_liquido_total'], 2);
            $dados_generico['valor_premio_net']        = round($cotacao_salva['premio_liquido'], 2);
            $dados_generico['comissao']                = $cotacao_salva['comissao_corretor'];
            $dados_generico['pro_labore']              = round(($cotacao_salva['premio_liquido_total'] - $cotacao_salva['premio_liquido']), 2);
            $dados_generico['valor_parcela']           = round($pedido['valor_parcela'], 2);
            $dados_generico['valor_estorno']           = 0;

            $dados_generico['estado_civil']       = $cotacao_salva['estado_civil'];
            $dados_generico['rg_orgao_expedidor'] = $cotacao_salva['rg_orgao_expedidor'];
            $dados_generico['rg_uf']              = $cotacao_salva['rg_uf'];
            $dados_generico['rg_data_expedicao']  = $cotacao_salva['rg_data_expedicao'];
            $dados_generico['aux_01']             = $cotacao_salva['aux_01'];
            $dados_generico['aux_02']             = $cotacao_salva['aux_02'];
            $dados_generico['aux_03']             = $cotacao_salva['aux_03'];
            $dados_generico['aux_04']             = $cotacao_salva['aux_04'];
            $dados_generico['aux_05']             = $cotacao_salva['aux_05'];
            $dados_generico['aux_06']             = $cotacao_salva['aux_06'];
            $dados_generico['aux_07']             = $cotacao_salva['aux_07'];
            $dados_generico['aux_08']             = $cotacao_salva['aux_08'];
            $dados_generico['aux_09']             = $cotacao_salva['aux_09'];
            $dados_generico['aux_10']             = $cotacao_salva['aux_10'];

            $this->apolice_generico->insert($dados_generico, true);
            $this->concluiApolice($pedido, $apolice_id);

            $evento['mensagem']['apolices'] .= "Nome: {$dados_generico['nome']} - Apólice código: {$apolice_id} <br>";
            $evento['mensagem']['apolice_codigo'] = $this->get_codigo_apolice($apolice_id);
            $evento['mensagem']['anexos'][]       = $this->certificado($apolice_id, 'pdf_file');
        }

        if (isset($cotacao_salvas[0])) {
            log_message('debug', 'APOLICE 4');
            $cliente_contato            = array();
            $cliente_contato['nome']    = '';
            $cliente_contato['email']   = '';
            $cliente_contato['celular'] = '';
            $contatos                   = $this->cliente_contato->with_contato()->get_by_cliente($cotacao_salvas[0]['cliente_id']);
            if (count($contatos) > 0) {

                foreach ($contatos as $contato) {
                    $cliente_contato['nome']    = (empty($cliente_contato['nome'])) ? $contato['nome'] : $cliente_contato['nome'];
                    $cliente_contato['email']   = ($contato['contato_tipo_id'] == 1 && empty($cliente_contato['email'])) ? $contato['contato'] : $cliente_contato['email'];
                    $cliente_contato['celular'] = ($contato['contato_tipo_id'] == 2 && empty($cliente_contato['celular'])) ? $contato['contato'] : $cliente_contato['celular'];
                }
            }

            $evento['mensagem']['url'] = base_url();
            if (isset($cliente_contato) && isset($pedido['produto_parceiro_id'])) {

                log_message('debug', 'APOLICE 5');
                $evento['destinatario_email']    = isset($cliente_contato['email']) ? $cliente_contato['email'] : '';
                $evento['produto_parceiro_id']   = $pedido['produto_parceiro_id'];
                $evento['mensagem']['nome']      = isset($cliente_contato['nome']) ? $cliente_contato['nome'] : '';
                $evento['destinatario_telefone'] = isset($cliente_contato['celular']) ? $cliente_contato['celular'] : '';

                /**
                 * Dispara email
                 */

                log_message('debug', 'APOLICE DISPARO EMAIL');
                if (!empty($evento['destinatario_email'])) {
                    $comunicacao = new Comunicacao();
                    $comunicacao->setMensagemParametros($evento['mensagem']);
                    $comunicacao->setDestinatario($evento['destinatario_email']);
                    $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
                    $comunicacao->disparaEvento("apolice_gerada_email", $evento['produto_parceiro_id']);
                }

                /**
                 * Dispara SMS
                 */
                log_message('debug', 'APOLICE DISPARO SMS');
                log_message('debug', print_r($evento, true));
                if (!empty($evento['destinatario_telefone'])) {
                    $comunicacao = new Comunicacao();
                    $comunicacao->setMensagemParametros($evento['mensagem']);
                    $comunicacao->setDestinatario(app_retorna_numeros($evento['destinatario_telefone']));
                    $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
                    $comunicacao->disparaEvento("apolice_gerada_sms", $evento['produto_parceiro_id']);
                }

            }
        }

    }

    public function insertSeguroViagem($pedido_id)
    {
        $this->load->model('produto_parceiro_configuracao_model', 'parceiro_configuracao');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_seguro_viagem_pessoa_model', 'cotacao_pessoa');
        $this->load->model('apolice_seguro_viagem_model', 'apolice_seguro_viagem');

        $this->load->model('apolice_numero_seq_model', 'apolice_seq');
        $this->load->model('produto_parceiro_apolice_range_model', 'apolice_range');

        $this->load->model('produto_parceiro_desconto_model', 'parceiro_desconto');

        $this->load->model("cliente_contato_model", "cliente_contato");
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_evolucao_model', 'cliente_evolucao');

        //Eventos
        $evento                         = array();
        $evento['mensagem']             = array();
        $evento['mensagem']['apolices'] = "";
        $evento['mensagem']['nome']     = "";
        $evento['mensagem']['anexos']   = array();

        $pedido = $this->pedido->with_seguro_viagem()->getPedidoProdutoParceiro($pedido_id);
        $pedido = $pedido[0];

        //obter numero da apolice;
        $configuracao = $this->parceiro_configuracao->filter_by_produto_parceiro($pedido['produto_parceiro_id'])->get_all();
        $configuracao = $configuracao[0];

        //obter configurações de desconto
        $desconto_condicional = $this->parceiro_desconto->filter_by_produto_parceiro($pedido['produto_parceiro_id'])->get_all();
        if ($desconto_condicional) {
            $desconto_condicional = $desconto_condicional[0];
        }

        $cotacao_salvas = $this->cotacao->with_cotacao_seguro_viagem()
            ->filterByID($pedido['cotacao_id'])
            ->get_all(0, 0, false);

        log_message('debug', 'APOLICE 1');

        foreach ($cotacao_salvas as $cotacao_salva) {

            log_message('debug', 'APOLICE 2');

            if ($desconto_condicional) {
                if ($cotacao_salva['desconto_condicional_valor'] > 0) {
                    $dados_saldo              = array();
                    $dados_saldo['utilizado'] = $desconto_condicional['utilizado'] + $cotacao_salva['desconto_condicional_valor'];
                    $this->parceiro_desconto->update($desconto_condicional['produto_parceiro_desconto_id'], $dados_saldo, true);
                }

            }

            $cotacao_pessoas = $this->cotacao_pessoa->filter_by_seguro_viagem($cotacao_salva['cotacao_seguro_viagem_id'])->get_all();

            log_message('debug', 'UPDATE STATUS CLIENTE');

            $data_cliente                               = array();
            $data_cliente['cliente_evolucao_status_id'] = 4;
            $this->cliente->update($cotacao_salva['cliente_id'], $data_cliente, true);

            $i = 0;
            foreach ($cotacao_pessoas as $cotacao_pessoa) {

                log_message('debug', 'APOLICE 3');

                $dados_apolice                              = array();
                $dados_apolice['pedido_id']                 = $pedido_id;
                $dados_apolice['produto_parceiro_plano_id'] = $cotacao_salva['produto_parceiro_plano_id'];
                $dados_apolice['parceiro_id']               = $cotacao_salva['parceiro_id']; //$this->session->userdata('parceiro_id'); //parceiro da venda
                $dados_apolice['apolice_status_id']         = 1;

                if ($configuracao['apolice_sequencia'] == 1) {
                    //é número Sequencial
                    $dados_apolice['num_apolice'] = $this->apolice_seq->get_proximo_codigo($pedido['produto_parceiro_id']);
                } else {
                    //não é número Sequencial
                    $dados_apolice['num_apolice'] = $this->apolice_range->get_proximo_codigo($pedido['produto_parceiro_id']);
                }

                $apolice_id                                           = $this->insert($dados_apolice, true);
                $dados_seguro_viagem                                  = array();
                $dados_seguro_viagem['apolice_id']                    = $apolice_id;
                $dados_seguro_viagem['seguro_viagem_motivo_id']       = $cotacao_salva['seguro_viagem_motivo_id'];
                $dados_seguro_viagem['produto_parceiro_pagamento_id'] = $pedido['produto_parceiro_pagamento_id'];
                $dados_seguro_viagem['data_ini_vigencia']             = $cotacao_salva['data_saida'];
                $dados_seguro_viagem['data_fim_vigencia']             = $cotacao_salva['data_retorno'];
                $dados_seguro_viagem['data_adesao']                   = date('Y-m-d');
                $dados_seguro_viagem['data_pagamento']                = date('Y-m-d');
                $dados_seguro_viagem['cnpj_cpf']                      = $cotacao_pessoa['cnpj_cpf'];
                $dados_seguro_viagem['nome']                          = $cotacao_pessoa['nome'];
                $dados_seguro_viagem['data_nascimento']               = $cotacao_pessoa['data_nascimento'];
                $dados_seguro_viagem['sexo']                          = $cotacao_pessoa['sexo'];
                $dados_seguro_viagem['email']                         = $cotacao_pessoa['email'];
                $dados_seguro_viagem['endereco_logradouro']           = $cotacao_pessoa['endereco_logradouro'];
                $dados_seguro_viagem['endereco_numero']               = $cotacao_pessoa['endereco_numero'];
                $dados_seguro_viagem['endereco_complemento']          = $cotacao_pessoa['endereco_complemento'];
                $dados_seguro_viagem['endereco_bairro']               = $cotacao_pessoa['endereco_bairro'];
                $dados_seguro_viagem['endereco_cidade']               = $cotacao_pessoa['endereco_cidade'];
                $dados_seguro_viagem['endereco_estado']               = $cotacao_pessoa['endereco_estado'];
                $dados_seguro_viagem['endereco_cep']                  = $cotacao_pessoa['endereco_cep'];
                $dados_seguro_viagem['contato_telefone']              = $cotacao_pessoa['contato_telefone'];
                $dados_seguro_viagem['origem_id']                     = $cotacao_salva['origem_id'];
                $dados_seguro_viagem['destino_id']                    = $cotacao_salva['destino_id'];
                $dados_seguro_viagem['periodicidade_pagamento']       = 'U';
                $dados_seguro_viagem['num_parcela']                   = $pedido['num_parcela'];
                $dados_seguro_viagem['valor_premio_total']            = round(($pedido['premio_liquido_total'] / count($cotacao_pessoas)), 2);
                $dados_seguro_viagem['valor_premio_net']              = round(($pedido['premio_liquido'] / count($cotacao_pessoas)), 2);
                $dados_seguro_viagem['comissao']                      = $cotacao_salva['comissao_corretor'];
                $dados_seguro_viagem['pro_labore']                    = round((($pedido['premio_liquido_total'] - $pedido['premio_liquido']) / count($cotacao_pessoas)), 2);
                $dados_seguro_viagem['valor_parcela']                 = round(($pedido['valor_parcela'] / count($cotacao_pessoas)), 2);
                $dados_seguro_viagem['valor_estorno']                 = 0;

                $this->apolice_seguro_viagem->insert($dados_seguro_viagem, true);
                $this->concluiApolice($pedido, $apolice_id);

                $evento['mensagem']['apolices'] .= "Nome: {$cotacao_pessoa['nome']} - Apólice código: {$apolice_id} <br>";
                $evento['mensagem']['apolice_codigo'] = $this->get_codigo_apolice($apolice_id);
                $evento['mensagem']['anexos'][]       = $this->certificado($apolice_id, 'pdf_file');
            }

            if (isset($cotacao_salvas[0])) {
                log_message('debug', 'APOLICE 4');
                $cliente_contato            = array();
                $cliente_contato['nome']    = '';
                $cliente_contato['email']   = '';
                $cliente_contato['celular'] = '';
                $contatos                   = $this->cliente_contato->with_contato()->get_by_cliente($cotacao_salvas[0]['cliente_id']);
                if (count($contatos) > 0) {

                    foreach ($contatos as $contato) {
                        //print_r($contato);exit;
                        $cliente_contato['nome']    = (empty($cliente_contato['nome'])) ? $contato['nome'] : $cliente_contato['nome'];
                        $cliente_contato['email']   = ($contato['contato_tipo_id'] == 1 && empty($cliente_contato['email'])) ? $contato['contato'] : $cliente_contato['email'];
                        $cliente_contato['celular'] = ($contato['contato_tipo_id'] == 2 && empty($cliente_contato['celular'])) ? $contato['contato'] : $cliente_contato['celular'];
                    }
                }

                $evento['mensagem']['url'] = base_url();
                if (isset($cliente_contato) && isset($pedido['produto_parceiro_id'])) {

                    log_message('debug', 'APOLICE 5');
                    $evento['destinatario_email']    = isset($cliente_contato['email']) ? $cliente_contato['email'] : '';
                    $evento['produto_parceiro_id']   = $pedido['produto_parceiro_id'];
                    $evento['mensagem']['nome']      = isset($cliente_contato['nome']) ? $cliente_contato['nome'] : '';
                    $evento['destinatario_telefone'] = isset($cliente_contato['celular']) ? $cliente_contato['celular'] : '';

                    /**
                     * Dispara email
                     */

                    log_message('debug', 'APOLICE DISPARO EMAIL');
                    if (!empty($evento['destinatario_email'])) {
                        $comunicacao = new Comunicacao();
                        $comunicacao->setMensagemParametros($evento['mensagem']);
                        $comunicacao->setDestinatario($evento['destinatario_email']);
                        $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
                        $comunicacao->disparaEvento("apolice_gerada_email", $evento['produto_parceiro_id']);
                    }

                    /**
                     * Dispara SMS
                     */
                    log_message('debug', 'APOLICE DISPARO SMS');
                    log_message('debug', print_r($evento, true));
                    if (!empty($evento['destinatario_telefone'])) {
                        $comunicacao = new Comunicacao();
                        $comunicacao->setMensagemParametros($evento['mensagem']);
                        $comunicacao->setDestinatario(app_retorna_numeros($evento['destinatario_telefone']));
                        $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
                        $comunicacao->disparaEvento("apolice_gerada_sms", $evento['produto_parceiro_id']);
                    }

                }
            }

        }

    }

    public function get_codigo_apolice($apolice_id)
    {
        $this->load->library('encrypt');

        $en = $this->encrypt->encode($apolice_id);
        return base64_encode($en);
    }

    public function getApolicePedido($pedido_id)
    {

        $this->load->model("pedido_model", "pedido");

        $pedido = $this->pedido->getPedidoProdutoParceiro($pedido_id);

        $this->_database->select("apolice.apolice_id, apolice.pedido_id, apolice.num_apolice")
            ->select("apolice.produto_parceiro_plano_id, apolice.apolice_status_id, apolice_status.nome as apolice_status_nome")
            ->select("apolice_status.slug as apolice_status_slug")
            ->select("produto_parceiro_plano.produto_parceiro_id")
            ->select("produto_parceiro.parceiro_id as parceiro_seg_id")
            ->select("parceiro_seg.slug as slug_parceiro_seg")
            ->select("parceiro_seg.nome as seguradora")
            ->select("parceiro.slug as slug_parceiro")
            ->select("parceiro.nome as parceiro")
            ->select("produto_parceiro.slug_produto")
            ->select("produto.nome as produto")
            ->join("apolice_status", "apolice.apolice_status_id = apolice_status.apolice_status_id", 'inner')
            ->join("produto_parceiro_plano", "apolice.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id", 'inner')
            ->join("produto_parceiro", "produto_parceiro_plano.produto_parceiro_id = produto_parceiro.produto_parceiro_id", 'inner')
            ->join("produto", "produto_parceiro.produto_id = produto.produto_id", 'inner')
            ->join("parceiro parceiro_seg", "parceiro_seg.parceiro_id = produto_parceiro.parceiro_id", 'inner')
            ->join("parceiro", "parceiro.parceiro_id = apolice.parceiro_id", 'inner');

        if ($pedido) {
            $pedido = $pedido[0];
            if ($pedido['slug'] == 'seguro_viagem') {
                $this->_database->select("apolice_seguro_viagem.*")
                    ->join("apolice_seguro_viagem", "apolice.apolice_id = apolice_seguro_viagem.apolice_id", 'inner');
            } elseif ($pedido['slug'] == 'equipamento') {
                $this->_database->select("apolice_equipamento.*")
                    ->join("apolice_equipamento", "apolice.apolice_id = apolice_equipamento.apolice_id", 'inner');
            } elseif ($pedido["slug"] == "generico" || $pedido["slug"] == "seguro_saude") {
                $this->_database->select("apolice_generico.*")
                    ->join("apolice_generico", "apolice.apolice_id = apolice_generico.apolice_id", 'inner');
            }

            $this->_database->where("apolice.pedido_id", $pedido_id);
            return $this->get_all();
        } else {
            return array();
        }

    }

    public function getApolice($apolice_id)
    {

        $this->load->model("pedido_model", "pedido");

        $apolice = $this->get($apolice_id);

        $pedido = $this->pedido->getPedidoProdutoParceiro($apolice['pedido_id']);

        $this->_database->select("apolice.apolice_id, apolice.pedido_id, apolice.num_apolice")
            ->select("apolice.produto_parceiro_plano_id, apolice.apolice_status_id, apolice_status.nome as apolice_status_nome")
            ->select("apolice_status.slug as apolice_status_slug")
            ->join("apolice_status", "apolice.apolice_status_id = apolice_status.apolice_status_id", 'inner');

        if ($pedido) {
            $pedido = $pedido[0];
            if ($pedido['slug'] == 'seguro_viagem') {
                $this->_database->select("apolice_seguro_viagem.*")
                    ->join("apolice_seguro_viagem", "apolice.apolice_id = apolice_seguro_viagem.apolice_id", 'inner');
            } elseif ($pedido['slug'] == 'equipamento') {
                $this->_database->select("apolice_equipamento.*")
                    ->join("apolice_equipamento", "apolice.apolice_id = apolice_equipamento.apolice_id", 'inner');
            } elseif ($pedido["slug"] == "generico" || $pedido["slug"] == "seguro_saude") {
                $this->_database->select("apolice_generico.*")
                    ->join("apolice_generico", "apolice.apolice_id = apolice_generico.apolice_id", 'inner');
            }

            $this->_database->where("apolice.apolice_id", $apolice_id);
            $arrApolice = $this->get_all();
            if ($arrApolice) {
                $arrApolice                 = $arrApolice[0];
                $arrApolice['produto_slug'] = $pedido['slug'];
                return $arrApolice;
            } else {
                return array();
            }
        } else {
            return array();
        }

        /*
        $apolice_id = (int)$apolice_id;

        $sql = "
        SELECT apolice.apolice_id,
        apolice.pedido_id,
        apolice.num_apolice,
        apolice.apolice_status_id,
        apolice.produto_parceiro_plano_id,
        apolice.parceiro_id,
        apolice_status.nome as apolice_status_nome,
        apolice_status.slug as apolice_status_slug,
        apolice_seguro_viagem.*
        FROM apolice
        INNER JOIN apolice_status ON apolice.apolice_status_id = apolice_status.apolice_status_id
        INNER JOIN apolice_seguro_viagem ON apolice.apolice_id = apolice_seguro_viagem.apolice_id
        WHERE
        apolice.deletado = 0
        AND apolice_seguro_viagem.deletado = 0
        AND apolice.apolice_id = {$apolice_id}
        ";

        return $this->_database->query($sql)->result_array();
         */
    }

    public function getApoliceAll($limit, $offset)
    {

        $sql = "
                    SELECT apolice.apolice_id,
                             apolice.pedido_id,
                             apolice.num_apolice,
                             apolice.apolice_status_id,
                             apolice.produto_parceiro_plano_id,
                             apolice.parceiro_id,
                             apolice_status.nome as apolice_status_nome,
                             apolice_status.slug as apolice_status_slug,
                             apolice_seguro_viagem.*
                    FROM apolice
                    INNER JOIN apolice_status ON apolice.apolice_status_id = apolice_status.apolice_status_id
                    INNER JOIN apolice_seguro_viagem ON apolice.apolice_id = apolice_seguro_viagem.apolice_id
                    WHERE
                    apolice.deletado = 0
                    AND apolice_seguro_viagem.deletado = 0
                    LIMIT {$offset}, {$limit}
        ";

        return $this->_database->query($sql)->result_array();

    }

    public function get_by_id($id)
    {
        return $this->get($id);
    }

    public function certificado($apolice_id, $export = '')
    {

        $this->load->model('pedido_model', 'pedido');
        $this->load->model('localidade_model', 'localidade');
        $this->load->model('parceiro_model', 'parceiro_model');
        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('cobertura_plano_model', 'plano_cobertura');
        $this->load->model('produto_parceiro_termo_model', 'termo');

        $this->load->library('parser');

        $data_template = array();

        $apolice = $this->getApolice($apolice_id);

        if (count($apolice) == 0) {
            $this->session->set_flashdata('fail_msg', 'Apólice não esta liberado'); //Mensagem de sucesso
            return false;
        }

        $dados = $this->pedido->getPedidoProdutoParceiro($apolice['pedido_id']);
        $dados = $dados[0];


        // Relacionamento corretora
        $data_template['rel_corretora_nome']         = '';
        $data_template['rel_corretora_cnpj']         = '';
        $data_template['rel_corretora_codigo_susep'] = '';
        if(isset($dados['produto_parceiro_id']) && !empty($dados['produto_parceiro_id']))
        {
            $this->load->model('parceiro_relacionamento_produto_model', 'parceiro_relacionamento_produto');
            $dados_prp = $this->parceiro_relacionamento_produto->filter_by_produto_parceiro($dados['produto_parceiro_id'])->with_parceiro()->filter_by_parceiro_tipo('2')->get_all();

            if (!empty($dados_prp)) {
                $data_template['rel_corretora_nome'] = $dados_prp[0]['parceiro_nome'];
                $data_template['rel_corretora_cnpj'] = app_cnpj_to_mask($dados_prp[0]['parceiro_cnpj']);
                $data_template['rel_corretora_codigo_susep'] = $dados_prp[0]['parceiro_codigo_susep'];
            } else {
                $data_template['rel_corretora_nome'] = '';
                $data_template['rel_corretora_cnpj'] = '';
                $data_template['rel_corretora_codigo_susep'] = '';
            }
                        
        }

        $template = $dados['template_apolice'];

        if (isset($apolice['origem_id'])) {
            $origem                  = $this->localidade->get($apolice['origem_id']);
            $data_template['origem'] = $origem['nome'];
        }

        if (isset($apolice['destino_id'])) {
            $destino                  = $this->localidade->get($apolice['destino_id']);
            $data_template['destino'] = $destino['nome'];
        }

        if (isset($apolice['nota_fiscal_data'])) {
            $data_template['nota_fiscal_data'] = app_dateonly_mysql_to_mask($apolice['nota_fiscal_data']);
        }
        if (isset($apolice['nota_fiscal_valor'])) {
            $data_template['nota_fiscal_valor'] = app_format_currency($apolice['nota_fiscal_valor']);
        }
        if (isset($apolice['nota_fiscal_numero'])) {
            $data_template['nota_fiscal_numero'] = $apolice['nota_fiscal_numero'];
        }

        $parceiro = $this->parceiro_model->get($apolice['parceiro_id']);

        $termo = $this->termo->filter_by_produto_parceiro($dados['produto_parceiro_id'])->get_all();
        $termo = (isset($termo[0])) ? $termo[0] : array('termo' => '');

        if ($parceiro["parceiro_tipo_id"] == 1) {
            $data_template['representante_nome']      = "&nbsp;";
            $data_template['representante_cnpj']      = "&nbsp;";
            $data_template['representante_susep']     = "&nbsp;";
            $data_template['representante_corretora'] = "&nbsp;";
            $data_template['representante_endereco']  = "&nbsp;";
            $data_template['representante_sucursal']  = "&nbsp;";
            $data_template['seguradora_razao']        = $parceiro['nome'];
            $data_template['seguradora_cnpj']         = $parceiro['cnpj'];
            $data_template['seguradora_susep']        = $parceiro['codigo_susep'];
            $data_template['seguradora_endereco']     = trim($parceiro['endereco']) . ", " . trim($parceiro['numero']);
            if (trim($parceiro['complemento']) != "") {
                $data_template['seguradora_endereco'] .= " - " . trim($parceiro['complemento']);
            }
            $data_template['seguradora_endereco'] .= " - " . trim($parceiro['bairro']) . " - CEP:" . trim($parceiro['cep']);
        } else {
            $data_template['representante_nome']      = $parceiro['nome'];
            $data_template['representante_cnpj']      = $parceiro['cnpj'];
            $data_template['representante_susep']     = $parceiro['codigo_susep'];
            $data_template['representante_corretora'] = $parceiro['nome'];
            $data_template['representante_corretora'] = $parceiro['endereco'];
            $data_template['representante_sucursal']  = "";
        }

        $data_template['termo']             = $termo['termo'];
        $data_template['assets']            = base_url('assets');
        $data_template['num_apolice']       = $apolice['num_apolice'];
        $data_template['num_certificado']   = $apolice['num_apolice'];
        $data_template['data_ini_vigencia'] = app_date_mysql_to_mask($apolice['data_ini_vigencia'], 'd/m/Y');
        $data_template['data_fim_vigencia'] = app_date_mysql_to_mask($apolice['data_fim_vigencia'], 'd/m/Y');

        $data_template['inicio_viagem'] = app_date_mysql_to_mask($apolice['data_ini_vigencia'], 'd/m/Y');
        $data_template['fim_viagem']    = app_date_mysql_to_mask($apolice['data_fim_vigencia'], 'd/m/Y');
        $data_template['data_pedido']   = app_date_mysql_to_mask($apolice['data_adesao'], 'd/m/Y');
        $data_template['data_adesao']   = app_date_mysql_to_mask($apolice['data_adesao'], 'd/m/Y');

        $data_template['premio_liquido'] = "R$ " . app_format_currency($apolice['valor_premio_net']);
        $data_template['premio_total']   = "R$ " . app_format_currency($apolice['valor_premio_total']);
        $data_template['valor_iof']      = "R$ " . app_format_currency($apolice['valor_premio_total'] - $apolice['valor_premio_net']);

        if ($apolice['num_parcela'] == "1") {
            $data_template['forma_pagamento'] = $apolice['num_parcela'] . " parcela de R$ " . app_format_currency($apolice['valor_premio_total']);
        } else {
            $data_template['forma_pagamento'] = $apolice['num_parcela'] . " parcelas de R$ " . app_format_currency($apolice['valor_premio_total']);
        }

        $data_template['parceiro']      = $parceiro['nome'];
        $data_template['cnpj_parceiro'] = app_cnpj_to_mask($parceiro['cnpj']);

        $plano      = $this->plano->get($apolice['produto_parceiro_plano_id']);
        $coberturas = $this->plano_cobertura->with_cobertura()->filter_by_produto_parceiro_plano($apolice['produto_parceiro_plano_id'])->get_all();

        $equipamento = $this->db->query("SELECT em.nome as marca, ec.nome as equipamento, ce.equipamento_nome as modelo, ae.imei FROM apolice_equipamento ae
                                          INNER JOIN apolice a ON (a.apolice_id=ae.apolice_id)
                                          INNER JOIN pedido p ON (p.pedido_id=a.pedido_id)
                                          INNER JOIN cotacao_equipamento ce ON (ce.cotacao_id=p.cotacao_id)
                                          INNER JOIN equipamento_categoria ec ON (ec.equipamento_categoria_id=ce.equipamento_categoria_id)
                                          INNER JOIN equipamento_marca em ON (em.equipamento_marca_id = ce.equipamento_marca_id)
                                          WHERE a.apolice_id=" . $apolice["apolice_id"])->result_array();

        if (sizeof($equipamento)) {
            $data_template['equipamento'] = $equipamento[0]["equipamento"];
            $data_template['modelo']      = $equipamento[0]["modelo"];
            $data_template['marca']       = $equipamento[0]["marca"];
            $data_template['imei']        = $equipamento[0]["imei"];
            $data_template['lmi_roubo']   = app_format_currency($apolice['nota_fiscal_valor']);
            $data_template['lmi_furto']   = app_format_currency($apolice['nota_fiscal_valor']);
            $data_template['lmi_quebra']  = app_format_currency($apolice['nota_fiscal_valor']);
        } else {
            $data_template['equipamento'] = "";
            $data_template['modelo']      = "";
            $data_template['marca']       = "";
            $data_template['imei']        = "";
        }

        $ccount = 0;
        foreach ($coberturas as $cobertura) {
            $ccount                                                     = $ccount + 1;
            $data_template["cobertura_" . trim($ccount) . "_descricao"] = $cobertura["cobertura_nome"];
            $data_template["lmi_" . trim($ccount)]                      = $cobertura["descricao"];
        }

        $pagamento = $this->pedido->getPedidoPagamento($apolice['pedido_id']);
        $pagamento = $pagamento[0];


        $data_template['pagamento_tipo_pagamento']      = $pagamento['tipo_pagamento'];
        $data_template['pagamento_bandeira']            = $pagamento['bandeira'];
        $data_template['pagamento_num_parcela']         = $pagamento['num_parcela'];
        //print_r($pagamento); exit;

        //@todo fazer listagem do numero de capitalização
        //$capitalizacao = array('numero' => $apolice['num_capitalizacao']);

        //dados segurado
        $data_template['segurado_rg']      = $apolice['rg'];
        $data_template['segurado_sexo']    = $apolice['sexo'];
        $data_template['profissao']        = "";
        $data_template['estado_civil']     = "";
        $data_template['contato_telefone'] = app_format_telefone($apolice['contato_telefone']);

        $data_template['segurado_sexo_masculino'] = " ";
        $data_template['segurado_sexo_feminino']  = " ";
        if ($apolice['sexo'] == "M") {
            $data_template['segurado_sexo']           = "Masculino";
            $data_template['segurado_sexo_masculino'] = "X";
        } else {
            $data_template['segurado_sexo']          = "Feminno";
            $data_template['segurado_sexo_feminino'] = "X";
        }


        $tot = strlen(trim($apolice['cnpj_cpf']));
        if($tot == 11)
            $_cpf_cnpj = app_cpf_to_mask($apolice['cnpj_cpf']);
        else if($tot == 14)
           $_cpf_cnpj = app_cnpj_to_mask($apolice['cnpj_cpf']); 
        else 
            $_cpf_cnpj = $apolice['cnpj_cpf']; 

        
        $data_template['segurado_nome']            = $apolice['nome'];
        $data_template['segurado_cnpj_cpf']        = $apolice['cnpj_cpf']; 
        $data_template['segurado_cnpj_cpf_2']      = $_cpf_cnpj;
        $data_template['segurado_data_nascimento'] = app_dateonly_mysql_to_mask($apolice['data_nascimento']);
        $data_template['segurado_endereco']        = $apolice['endereco_logradouro'];
        $data_template['segurado_numero']          = $apolice['endereco_numero'];
        $data_template['segurado_bairro']          = $apolice['endereco_bairro'];
        $data_template['segurado_cidade']          = $apolice['endereco_cidade'];
        $data_template['segurado_estado']          = $apolice['endereco_estado'];
        $data_template['segurado_cep']             = app_format_cep($apolice['endereco_cep']);
        $data_template['segurado_telefone']        = app_format_telefone($apolice['contato_telefone']);
        //$data_template['plano'] = $plano['nome'];

        $data_template['segurado'] = $this->load->view("admin/venda/{$apolice['produto_slug']}/certificado/dados_segurado", array('segurado' => $apolice), true);


        $viewseguro = 'dados_seguro';

        if($dados['produto_parceiro_id'] == 64)
        {
            $viewseguro = 'dados_seguro_tem';
        } 

        $data_template['seguro']   = $this->load->view("admin/venda/{$apolice['produto_slug']}/certificado/{$viewseguro}", array(
            'plano'      => $plano,
            'coberturas' => $coberturas,
            //  'capitalizacao' => $capitalizacao,
            'pagamento'  => $pagamento,
            'dados'      => $dados),
            true);


        error_log(print_r($data_template['seguro'], true) . "\n", 3, "/var/log/httpd/myapp.log");
        $data_template['premio']    = $this->load->view("admin/venda/{$apolice['produto_slug']}/certificado/premio", array('premio_liquido' => $apolice['valor_premio_net'], 'premio_total' => $apolice['valor_premio_total']), true);
        $data_template['pagamento'] = $this->load->view("admin/venda/{$apolice['produto_slug']}/certificado/pagamento", array('pagamento' => $pagamento), true);

        /*
        echo '<pre>';
        print_r($data_template);
        die;
        */
        

        $template = $this->parser->parse_string($template, $data_template, true);
        if (($export == 'pdf') || ($export == 'pdf_file')) {
            $this->custom_loader->library('pdf');
            $this->pdf->setPageOrientation('P');

            $this->pdf->AddPage();

            //$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            $destino_dir = FCPATH . "assets/files/{{$apolice['produto_slug']}}/certificado/";
            if (!file_exists($destino_dir)) {
                mkdir($destino_dir, 0777, true);
            }
            $this->pdf->SetMargins(5, 5, 5);
            $this->pdf->writeHTML($template, true, false, true, false, '');
            $destino = ($export == 'pdf') ? 'D' : 'F';
            $file    = ($export == 'pdf') ? "{$apolice['num_apolice']}.pdf" : "{$destino_dir}{$apolice['num_apolice']}.pdf";
            ob_end_clean();
            $this->pdf->Output($file, $destino);
            $this->custom_loader->unload_library('pdf');
            if ($export == 'pdf_file') {
                return "{$destino_dir}{$apolice['num_apolice']}.pdf";
            } else {
                exit;
            }

        } else {
            return $template;
        }

    }

    public function insertCapitalizacao($produto_parceiro_id, $pedido_id)
    {

        $this->load->model('produto_parceiro_capitalizacao_model', 'produto_parceiro_capitalizacao');
        $this->load->model('capitalizacao_model', 'capitalizacao');
        $this->load->model('capitalizacao_serie_titulo_model', 'capitalizacao_serie_titulo');

        //verifica se tem capitalização configurado
        $parceiro_capitalizacao = $this->produto_parceiro_capitalizacao->with_capitalizacao()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_capitalizacao_ativa()
            ->get_all();

        //capitalização
        if (count($parceiro_capitalizacao) > 0) {

            foreach ($parceiro_capitalizacao as $index => $item) {
                $capitalizacao = $this->capitalizacao->getTituloNaoUtilizado($item['capitalizacao_id']);

                if (count($capitalizacao) > 0) {
                    $capitalizacao                      = $capitalizacao[0];
                    $dados_capitalizacao                = array();
                    $dados_capitalizacao['pedido_id']   = $pedido_id;
                    $dados_capitalizacao['utilizado']   = 1;
                    $dados_capitalizacao['data_compra'] = date('Y-m-d H:i:s');
                    $this->capitalizacao_serie_titulo->update($capitalizacao['capitalizacao_serie_titulo_id'], $dados_capitalizacao, true);

                    /*
                $dados_tipo_apolice = array();
                $dados_tipo_apolice['apolice_id'] = $apolice_id;
                $dados_tipo_apolice['capitalizacao_serie_titulo_id'] = $capitalizacao['capitalizacao_serie_titulo_id'];
                $this->insert($dados_tipo_apolice, TRUE); */

                }

            }

        }

    }

    public function with_cliente($cliente_id)
    {
        $this->_database->select('apolice.*');
        $this->_database->select('pedido.valor_total as valor_total');
        $this->_database->select('apolice_status.nome as apolice_status');
        $this->_database->join('apolice_status', 'apolice_status.apolice_status_id = apolice.apolice_status_id', 'inner');
        $this->_database->join('pedido', 'pedido.pedido_id = apolice.pedido_id', 'inner');
        $this->_database->join('cotacao', 'cotacao.cotacao_id = pedido.cotacao_id', 'inner');
        $this->_database->where('cotacao.cliente_id', $cliente_id);
        $this->_database->order_by('cotacao.criacao', 'DESC');
        return $this;
    }

    public function getApoliceByNumero($num_apolice, $parceiro_id)
    {

        $this->_database->select("apolice.apolice_id, apolice.apolice_status_id, apolice_status.nome")
            ->join("apolice_status", "apolice.apolice_status_id = apolice_status.apolice_status_id", 'inner')
            ->where("apolice.num_apolice", $num_apolice)
            ->where("apolice.parceiro_id", $parceiro_id);
        return $this->get_all();
    }

    public function search_apolice_produto_parceiro_plano_id($num_apolice, $produto_parceiro_plano_id)
    {
        $sql = "
            SELECT apolice_id
            FROM apolice
            WHERE num_apolice = '{$num_apolice}' and produto_parceiro_plano_id = '{$produto_parceiro_plano_id}'
            LIMIT 1
        ";
        return (int) $this->_database->query($sql)->num_rows() == 1;
    }

    //Retorna por Número da Apolice
    function filter_by_numApolice($num_apolice, $tpa)
    {
        $this->db->join('pedido', "pedido.pedido_id = {$this->_table}.pedido_id", 'join');
        $this->db->join('cotacao', "cotacao.cotacao_id = pedido.cotacao_id", 'join');
        $this->db->join('produto_parceiro', "produto_parceiro.produto_parceiro_id = cotacao.produto_parceiro_id", 'join');
        $this->db->where('produto_parceiro.cod_tpa', $tpa);
        $this->db->where("{$this->_table}.num_apolice LIKE '%{$num_apolice}%'");
        return $this;
    }

    function getByRespoCobertura($status = 'ativa', $parceiro_slug = null)
    {
        $this->_database->distinct()
            // ->select("72 as parceiroID, apolice_movimentacao.apolice_movimentacao_id", FALSE)
            ->select("parceiro.parceiro_id as parceiroID, apolice_movimentacao.apolice_movimentacao_id, apolice_movimentacao_tipo.slug as slugMov")
            ->join("apolice_status", "apolice.apolice_status_id = apolice_status.apolice_status_id", 'inner')
            ->join("apolice_movimentacao", "apolice.apolice_id = apolice_movimentacao.apolice_id", 'inner')
            ->join("apolice_movimentacao_tipo", "apolice_movimentacao.apolice_movimentacao_tipo_id = apolice_movimentacao_tipo.apolice_movimentacao_tipo_id", 'inner')
            ->join("produto_parceiro_plano", "apolice.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id", 'inner')
            ->join("cobertura_plano", "produto_parceiro_plano.produto_parceiro_plano_id = cobertura_plano.produto_parceiro_plano_id", 'inner')
            ->join("parceiro", "parceiro.parceiro_id = cobertura_plano.parceiro_id", 'inner')
            ->where('apolice_status.slug', $status);

        if (!empty($parceiro_slug)) {
            $this->_database->where('parceiro.slug', $parceiro_slug);
        }

        // Remover
        // $this->_database->where('apolice.apolice_id', 14679);

        return $this->get_all();
    }

}
