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
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_seguro_viagem_pessoa_model', 'cotacao_pessoa');
        $this->load->model('apolice_seguro_viagem_model', 'apolice_seguro_viagem');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');

        $pedido = $this->pedido->getPedidoProdutoParceiro($pedido_id);
        $pedido = $pedido[0];

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

    public function insertApolice($pedido_id, $etapa = 'pagamento')
    {

        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('produto_parceiro_configuracao_model', 'parceiro_configuracao');

        $apolice = $this->get_many_by(array('pedido_id' => $pedido_id));
        $apolice_id = null;

        if ($apolice) {
            return;
        }

        $produto = $this->pedido->getPedidoProdutoParceiro( $pedido_id );
        if ($produto) {
            $produto = $produto[0];

            $conclui_em_tempo_real = $this->parceiro_configuracao->item_config($produto['produto_parceiro_id'], 'conclui_em_tempo_real');
            if ($etapa == 'pagamento' && $conclui_em_tempo_real == false ) {


                $this->load->library("Short_url");

                if ($produto['slug'] == 'seguro_viagem') {
                    $cotacao_salvas = $this->cotacao->with_cotacao_seguro_viagem()
                        ->filterByID($produto['cotacao_id'])
                        ->get_all();
                } elseif ($produto['slug'] == 'equipamento') {

                    $cotacao_salvas = $this->cotacao->with_cotacao_equipamento()
                        ->filterByID($produto['cotacao_id'])
                        ->get_all();
                } elseif ($produto["slug"] == "generico" || $produto["slug"] == "seguro_saude") {

                    $cotacao_salvas = $this->cotacao->with_cotacao_generico()
                        ->filterByID($produto['cotacao_id'])
                        ->get_all();
                }

                //Eventos
                $evento                         = array();
                $evento['mensagem']             = array();
                $evento['mensagem']['apolices'] = "";
                $evento['mensagem']['nome']     = ""; 

                if ($produto['slug'] == 'seguro_viagem') {
                    foreach ($cotacao_salvas as $cotacao_salva) {
                        $cotacao_pessoas                 = $this->cotacao_pessoa->filter_by_seguro_viagem($cotacao_salva['cotacao_seguro_viagem_id'])->get_all();
                        $evento['mensagem']['nome']      = $cotacao_pessoas[0]['nome'];
                        $evento['destinatario_email']    = $cotacao_pessoas[0]['email'];
                        $evento['destinatario_telefone'] = $cotacao_pessoas[0]['contato_telefone'];
                        $evento['produto_parceiro_id']   = $produto['produto_parceiro_id'];
                    }

                } elseif ($produto['slug'] == 'equipamento') {

                    $cotacao_salva                   = $cotacao_salvas[0];
                    $evento['mensagem']['nome']      = $cotacao_salva['nome'];
                    $evento['destinatario_email']    = $cotacao_salva['email'];
                    $evento['destinatario_telefone'] = $cotacao_salva['telefone'];
                    $evento['produto_parceiro_id']   = $produto['produto_parceiro_id'];

                } elseif ($produto["slug"] == "generico" || $produto["slug"] == "seguro_saude") {

                    $cotacao_salva                   = $cotacao_salvas[0];
                    $evento['mensagem']['nome']      = $cotacao_salva['nome'];
                    $evento['destinatario_email']    = $cotacao_salva['email'];
                    $evento['destinatario_telefone'] = $cotacao_salva['telefone'];
                    $evento['produto_parceiro_id']   = $produto['produto_parceiro_id'];
                }

                /**
                 * Dispara email
                 */
                /*
                $comunicacao = new Comunicacao();
                $comunicacao->setMensagemParametros($evento['mensagem']);
                $comunicacao->setDestinatario($evento['destinatario_email']);
                $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
                $comunicacao->disparaEvento("apolice_nao_gerada_email", $evento['produto_parceiro_id']);
                */

                /**
                 * Dispara SMS
                 */

                $short_url = new Short_url();

                $evento['url'] = $this->config->item("URL_APLICATIVO");
                $evento['url'] = $short_url::shorter($evento['url']);

                $evento['mensagem']['cotacao'] = $produto['cotacao_id'];

                $comunicacao = new Comunicacao();
                $comunicacao->setMensagemParametros($evento['mensagem']);
                $comunicacao->setDestinatario(app_retorna_numeros($evento['destinatario_telefone']));
                $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
                $comunicacao->setUrl($evento['url']);
                $comunicacao->disparaEvento("url_aplicativo_sms", $evento['produto_parceiro_id']);

                return;
            }
            if ($etapa == 'contratar' && $conclui_em_tempo_real == true ) {
                return;
            }

            if ($produto['slug'] == 'seguro_viagem') {
                $apolice_id = $this->insertSeguroViagem($pedido_id);
            } elseif ($produto['slug'] == 'equipamento') {
                $apolice_id = $this->insertSeguroEquipamento($pedido_id);
            } elseif ($produto["slug"] == "generico" || $produto["slug"] == "seguro_saude") {
                $apolice_id = $this->insertSeguroGenerico($pedido_id);
            }
        }

        return $apolice_id;

    }

    public function concluiApolice($pedido, $apolice_id, $produto_parceiro_plano_id)
    {
        $this->load->model('apolice_cobertura_model', 'apolice_cobertura');        
        $this->load->model('apolice_movimentacao_model', 'movimentacao');
        $this->load->model('comissao_gerada_model', 'comissao_gerada');

        $pedido_id = $pedido['pedido_id'];
        $produto_parceiro_id = $pedido['produto_parceiro_id'];
        $cotacao_id = $pedido['cotacao_id'];

        // Dados da Capitalização
        $this->insertCapitalizacao($produto_parceiro_id, $pedido_id);

        // Dados das Coberturas. Nota: Precisa ser antes dos dados do Endosso
        $this->apolice_cobertura->deleteByCotacao($cotacao_id);
        $this->apolice_cobertura->geraDadosEmissao($cotacao_id, $pedido_id, $apolice_id, $produto_parceiro_plano_id);

        // Dados da Movimentação e Endosso
        $this->movimentacao->insMovimentacao('A', $apolice_id, $pedido);

        //Gerar comissão (Ação incluida no processo de concluir apolice para evitar uso da Cron que estava sobrepondo as requisições)
        $this->comissao_gerada->gerar_comissao_parceiro_relacionamento($pedido_id);

    }

    public function updateBilhete( $apolice_id, $num_apolice, $num_apolice_cliente = null ) {
        // retorna os dados da apólice
        $result = $this->get($apolice_id);

        $ret = [
            'status'  => false,
            'message' => "",
        ];

        if( empty($result) ){
            $ret['message'] = "Apólice não encontrada";
            return $ret;
        }
        if(empty($num_apolice_cliente)){
            if( $this->search_apolice_produto_parceiro_plano_id( $num_apolice , $result['produto_parceiro_plano_id'] ) ){
                $ret['message'] = "Já existe um certificado com o número {$num_apolice} em nossa base";
                return $ret;
            }
        }

        $this->load->model("apolice_endosso_model", "apolice_endosso");


        // Define o número da apólice do cliente
        $dados_bilhete                        = $this->defineDadosBilhete($result['produto_parceiro_plano_id']);
        $dados_apolice['num_apolice']         = $num_apolice;

        if (empty($num_apolice_cliente)) {
            $dados_apolice['num_apolice_cliente'] = $this->defineNumApoliceCliente([
                'cod_tpa'       => $dados_bilhete['cod_tpa'],
                'cod_sucursal'  => $dados_bilhete['cod_sucursal'],
                'cod_ramo'      => $dados_bilhete['cod_ramo'],
                'num_apolice'   => $dados_apolice['num_apolice'],
            ]);
        } else {
            $dados_apolice['num_apolice_cliente'] = $num_apolice_cliente;
        }        

        // Atualiza o numero da apólice
        $this->update($apolice_id, $dados_apolice, true);

        // atualiza dados do endosso
        $this->apolice_endosso->updateEndosso($apolice_id);

        $ret['status'] = true;
        $ret['result'] = $result;

        return $ret;
    }

    public function insertSeguroEquipamento($pedido_id)
    {

        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('cotacao_cobertura_model', 'cotacao_cobertura');
        $this->load->model('apolice_equipamento_model', 'apolice_equipamento');

        $this->load->model('produto_parceiro_desconto_model', 'parceiro_desconto');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('produto_parceiro_comunicacao_model', 'produto_parceiro_comunicacao');
        $this->load->model('comunicacao_evento_model', 'comunicacao_evento');

        $this->load->model("cliente_contato_model", "cliente_contato");
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_evolucao_model', 'cliente_evolucao');

        //Eventos
        $apolice_id                     = null;
        $evento                         = array();
        $evento['mensagem']             = array();
        $evento['mensagem']['apolices'] = "";
        $evento['mensagem']['nome']     = "";
        $evento['mensagem']['anexos']   = array();

        $pedido = $this->pedido->getPedidoProdutoParceiro($pedido_id);
        $pedido = $pedido[0];

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
            $data_cliente['cliente_evolucao_status_id'] = 4; // Salva como CLIENTE
            $this->cliente->update($cotacao_salva['cliente_id'], $data_cliente, true);

            $dados_apolice                              = array();
            $dados_apolice['pedido_id']                 = $pedido_id;
            $dados_apolice['produto_parceiro_plano_id'] = $cotacao_salva['produto_parceiro_plano_id'];
            $dados_apolice['parceiro_id']               = $cotacao_salva['parceiro_id']; //$this->session->userdata('parceiro_id'); //parceiro da venda
            $dados_apolice['apolice_status_id']         = 1;

            // Define os dados do CTA
            $dados_bilhete = $this->defineDadosBilhete($dados_apolice['produto_parceiro_plano_id']);
            $dados_apolice['cod_ramo']      = $dados_bilhete['cod_ramo'];
            $dados_apolice['cod_produto']   = $dados_bilhete['cod_produto'];
            $dados_apolice['cod_sucursal']  = $dados_bilhete['cod_sucursal'];

            // Define o número da apólice
            $dados_apolice['num_apolice']           = $this->defineNumApolice($pedido['produto_parceiro_id'], 'contratacao', $cotacao_salva['cotacao_id']);
            $dados_apolice['num_apolice_cliente']   = $this->defineNumApoliceCliente([
                'cod_tpa'       => $dados_bilhete['cod_tpa'],
                'cod_sucursal'  => $dados_bilhete['cod_sucursal'],
                'cod_ramo'      => $dados_bilhete['cod_ramo'],
                'num_apolice'   => $dados_apolice['num_apolice'],
            ]);

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
            $dados_equipamento['nome_mae']        = $cotacao_salva['nome_mae'];
            $dados_equipamento['data_nascimento'] = $cotacao_salva['data_nascimento'];
            $dados_equipamento['sexo']            = $cotacao_salva['sexo'];
            $dados_equipamento['email']           = $cotacao_salva['email'];
            $dados_equipamento['garantia_fabricante'] = $cotacao_salva['garantia_fabricante'];
            $dados_equipamento['cod_vendedor']        = $cotacao_salva['cod_vendedor'];
            $dados_equipamento['cod_loja']            = $cotacao_salva['cod_loja'];

            $dados_equipamento['ean']                           = $cotacao_salva['ean'];
            $dados_equipamento['equipamento_id']                = $cotacao_salva['equipamento_id'];
            $dados_equipamento['equipamento_nome']              = $cotacao_salva['equipamento_nome'];
            $dados_equipamento['equipamento_categoria_id']      = $cotacao_salva['equipamento_categoria_id'];
            $dados_equipamento['equipamento_sub_categoria_id']  = $cotacao_salva['equipamento_sub_categoria_id'];
            $dados_equipamento['equipamento_marca_id']          = $cotacao_salva['equipamento_marca_id'];
            $dados_equipamento['nota_fiscal_data']              = $cotacao_salva['nota_fiscal_data'];
            $dados_equipamento['nota_fiscal_valor']             = $cotacao_salva['nota_fiscal_valor'];
            $dados_equipamento['nota_fiscal_numero']            = $cotacao_salva['nota_fiscal_numero'];
            $dados_equipamento['imei']                          = $cotacao_salva['imei'];
            $dados_equipamento['cod_produto_sap']               = $cotacao_salva['cod_produto_sap'];

            $dados_equipamento['estado_civil']               = $cotacao_salva['estado_civil'];
            $dados_equipamento['rg_orgao_expedidor']         = $cotacao_salva['rg_orgao_expedidor'];
            $dados_equipamento['rg_uf']                      = $cotacao_salva['rg_uf'];
            $dados_equipamento['rg_data_expedicao']          = $cotacao_salva['rg_data_expedicao'];
            $dados_equipamento['aux_01']                     = $cotacao_salva['aux_01'];
            $dados_equipamento['aux_02']                     = $cotacao_salva['aux_02'];
            $dados_equipamento['aux_03']                     = $cotacao_salva['aux_03'];
            $dados_equipamento['aux_04']                     = $cotacao_salva['aux_04'];
            $dados_equipamento['aux_05']                     = $cotacao_salva['aux_05'];
            $dados_equipamento['aux_06']                     = $cotacao_salva['aux_06'];
            $dados_equipamento['aux_07']                     = $cotacao_salva['aux_07'];
            $dados_equipamento['aux_08']                     = $cotacao_salva['aux_08'];
            $dados_equipamento['aux_09']                     = $cotacao_salva['aux_09'];
            $dados_equipamento['aux_10']                     = $cotacao_salva['aux_10'];
            $dados_equipamento['tempo_uso']                  = $cotacao_salva['tempo_uso'];
            $dados_equipamento['imei2']                      = $cotacao_salva['imei2'];
            $dados_equipamento['latitude_longitude']         = $cotacao_salva['latitude_longitude'];
            $dados_equipamento['serial']                     = $cotacao_salva['serial'];
            $dados_equipamento['uuid']                       = $cotacao_salva['uuid'];
            $dados_equipamento['data_aceite_termo']          = $cotacao_salva['data_aceite_termo'];
            $dados_equipamento['numero_sorte']               = $cotacao_salva['numero_sorte'];
            $dados_equipamento['num_proposta_capitalizacao'] = $cotacao_salva['num_proposta_capitalizacao'];

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
            $dados_equipamento['comissao_premio']         = round($cotacao_salva['comissao_premio'], 2);

            $this->apolice_equipamento->insert($dados_equipamento, true);
            $this->concluiApolice($pedido, $apolice_id, $dados_apolice['produto_parceiro_plano_id']);

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
                    $comunicacaoEvento = $this->comunicacao_evento
                    ->with_foreign()
                    ->get_by(array('comunicacao_evento.slug' => "apolice_gerada_email"));

                    if($comunicacaoEvento){
                            $parceiro_comunicacao = $this->produto_parceiro_comunicacao
                            ->with_foreign()
                            ->with_parceiro()
                            ->get_by(array(
                                'produto_parceiro_comunicacao.produto_parceiro_id'   => $evento['produto_parceiro_id'],
                                'produto_parceiro_comunicacao.comunicacao_evento_id' => $comunicacaoEvento['comunicacao_evento_id']
                                )
                            );
                        
                        if ($parceiro_comunicacao) {

                            $comunicacao_template_mensagem = $parceiro_comunicacao['comunicacao_template_mensagem'];                         

                            $mathes = array();
                            $pattern = "/{anexo}(.*?){\/anexo}/s";
                            preg_match_all($pattern, $comunicacao_template_mensagem, $matches);
                            if(!empty($matches)){
                                $aAnexos = $matches[1];
                                foreach($aAnexos as $_anexo){
                                    if(method_exists($this, $_anexo)){
                                        $evento['mensagem']['anexos'][] = $this->{$_anexo}($apolice_id, "pdf_file");;
                                    }
                                }
                            }
                        }
                    }

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

        return $apolice_id;

    }

    public function insertSeguroGenerico($pedido_id)
    {
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_cobertura_model', 'cotacao_cobertura');
        $this->load->model('apolice_generico_model', 'apolice_generico');

        $this->load->model('produto_parceiro_desconto_model', 'parceiro_desconto');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');

        $this->load->model("cliente_contato_model", "cliente_contato");
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_evolucao_model', 'cliente_evolucao');

        //Eventos
        $apolice_id                     = null;
        $evento                         = array();
        $evento['mensagem']             = array();
        $evento['mensagem']['apolices'] = "";
        $evento['mensagem']['nome']     = "";
        $evento['mensagem']['anexos']   = array();

        $pedido = $this->pedido->getPedidoProdutoParceiro($pedido_id);
        $pedido = $pedido[0];

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

            // Define os dados do CTA
            $dados_bilhete = $this->defineDadosBilhete($dados_apolice['produto_parceiro_plano_id']);
            $dados_apolice['cod_ramo']      = $dados_bilhete['cod_ramo'];
            $dados_apolice['cod_produto']   = $dados_bilhete['cod_produto'];
            $dados_apolice['cod_sucursal']  = $dados_bilhete['cod_sucursal'];

            // Define o número da apólice
            $dados_apolice['num_apolice']           = $this->defineNumApolice($pedido['produto_parceiro_id'], 'contratacao', $cotacao_salva['cotacao_id']);
            $dados_apolice['num_apolice_cliente']   = $this->defineNumApoliceCliente([
                'cod_tpa'       => $dados_bilhete['cod_tpa'],
                'cod_sucursal'  => $dados_bilhete['cod_sucursal'],
                'cod_ramo'      => $dados_bilhete['cod_ramo'],
                'num_apolice'   => $dados_apolice['num_apolice'],
            ]);

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
            $dados_generico['garantia_fabricante']           = $cotacao_salva['garantia_fabricante'];
            $dados_generico['cod_vendedor']                  = $cotacao_salva['cod_vendedor'];
            $dados_generico['cod_loja']                      = $cotacao_salva['cod_loja'];

            $dados_generico['cnpj_cpf']                = $cotacao_salva['cnpj_cpf'];
            $dados_generico['rg']                      = $cotacao_salva['rg'];
            $dados_generico['nome']                    = $cotacao_salva['nome'];
            $dados_generico['nome_mae']                = $cotacao_salva['nome_mae'];
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
            $dados_generico['comissao_premio']         = round($cotacao_salva['comissao_premio'], 2);

            $dados_generico['estado_civil']                 = $cotacao_salva['estado_civil'];
            $dados_generico['rg_orgao_expedidor']           = $cotacao_salva['rg_orgao_expedidor'];
            $dados_generico['rg_uf']                        = $cotacao_salva['rg_uf'];
            $dados_generico['rg_data_expedicao']            = $cotacao_salva['rg_data_expedicao'];
            $dados_generico['aux_01']                       = $cotacao_salva['aux_01'];
            $dados_generico['aux_02']                       = $cotacao_salva['aux_02'];
            $dados_generico['aux_03']                       = $cotacao_salva['aux_03'];
            $dados_generico['aux_04']                       = $cotacao_salva['aux_04'];
            $dados_generico['aux_05']                       = $cotacao_salva['aux_05'];
            $dados_generico['aux_06']                       = $cotacao_salva['aux_06'];
            $dados_generico['aux_07']                       = $cotacao_salva['aux_07'];
            $dados_generico['aux_08']                       = $cotacao_salva['aux_08'];
            $dados_generico['aux_09']                       = $cotacao_salva['aux_09'];
            $dados_generico['aux_10']                       = $cotacao_salva['aux_10'];
            $dados_generico['tempo_uso']                    = $cotacao_salva['tempo_uso'];
            $dados_generico['imei2']                        = $cotacao_salva['imei2'];
            $dados_generico['latitude_longitude']           = $cotacao_salva['latitude_longitude'];
            $dados_generico['serial']                       = $cotacao_salva['serial'];
            $dados_generico['uuid']                         = $cotacao_salva['uuid'];
            $dados_generico['data_aceite_termo']            = $cotacao_salva['data_aceite_termo'];
            $dados_generico['numero_sorte']                 = $cotacao_salva['numero_sorte'];
            $dados_generico['num_proposta_capitalizacao']   = $cotacao_salva['num_proposta_capitalizacao'];

            $this->apolice_generico->insert($dados_generico, true);
            $this->concluiApolice($pedido, $apolice_id, $dados_apolice['produto_parceiro_plano_id']);

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

        return $apolice_id;

    }

    public function insertSeguroViagem($pedido_id)
    {
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_seguro_viagem_pessoa_model', 'cotacao_pessoa');
        $this->load->model('apolice_seguro_viagem_model', 'apolice_seguro_viagem');
        $this->load->model('produto_parceiro_desconto_model', 'parceiro_desconto');
        $this->load->model("cliente_contato_model", "cliente_contato");
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_evolucao_model', 'cliente_evolucao');

        //Eventos
        $apolice_id                     = null;
        $evento                         = array();
        $evento['mensagem']             = array();
        $evento['mensagem']['apolices'] = "";
        $evento['mensagem']['nome']     = "";
        $evento['mensagem']['anexos']   = array();

        $pedido = $this->pedido->with_seguro_viagem()->getPedidoProdutoParceiro($pedido_id);
        $pedido = $pedido[0];

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

                // Define os dados do CTA
                $dados_bilhete = $this->defineDadosBilhete($dados_apolice['produto_parceiro_plano_id']);
                $dados_apolice['cod_ramo']      = $dados_bilhete['cod_ramo'];
                $dados_apolice['cod_produto']   = $dados_bilhete['cod_produto'];
                $dados_apolice['cod_sucursal']  = $dados_bilhete['cod_sucursal'];

                // Define o número da apólice
                $dados_apolice['num_apolice']           = $this->defineNumApolice($pedido['produto_parceiro_id'], 'contratacao', $cotacao_salva['cotacao_id']);
                $dados_apolice['num_apolice_cliente']   = $this->defineNumApoliceCliente([
                    'cod_tpa'       => $dados_bilhete['cod_tpa'],
                    'cod_sucursal'  => $dados_bilhete['cod_sucursal'],
                    'cod_ramo'      => $dados_bilhete['cod_ramo'],
                    'num_apolice'   => $dados_apolice['num_apolice'],
                ]);

                $apolice_id                                           = $this->insert($dados_apolice, true);
                $dados_seguro_viagem                                  = array();
                $dados_seguro_viagem['apolice_id']                    = $apolice_id;
                $dados_seguro_viagem['seguro_viagem_motivo_id']       = $cotacao_salva['seguro_viagem_motivo_id'];
                $dados_seguro_viagem['produto_parceiro_pagamento_id'] = $pedido['produto_parceiro_pagamento_id'];
                $dados_seguro_viagem['garantia_fabricante']           = $cotacao_salva['garantia_fabricante'];
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
                $dados_seguro_viagem['tempo_uso']                     = $cotacao_salva['tempo_uso'];
                $dados_seguro_viagem['imei2']                         = $cotacao_salva['imei2'];
                $dados_seguro_viagem['latitude_longitude']            = $cotacao_salva['latitude_longitude'];
                $dados_seguro_viagem['serial']                        = $cotacao_salva['serial'];
                $dados_seguro_viagem['uuid']                          = $cotacao_salva['uuid'];
                $dados_seguro_viagem['data_aceite_termo']             = $cotacao_salva['data_aceite_termo'];
                $dados_seguro_viagem['comissao_premio']               = round($cotacao_salva['comissao_premio'], 2);
                $dados_seguro_viagem['numero_sorte']                  = $cotacao_salva['numero_sorte'];
                $dados_seguro_viagem['num_proposta_capitalizacao']    = $cotacao_salva['num_proposta_capitalizacao'];
                $dados_seguro_viagem['cod_vendedor']                  = $cotacao_salva['cod_vendedor'];
                $dados_seguro_viagem['cod_loja']                      = $cotacao_salva['cod_loja'];


                $this->apolice_seguro_viagem->insert($dados_seguro_viagem, true);
                $this->concluiApolice($pedido, $apolice_id, $dados_apolice['produto_parceiro_plano_id']);

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

        return $apolice_id;
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

        $this->_database->select("apolice.apolice_id, apolice.pedido_id, apolice.num_apolice, apolice.num_apolice_cliente")
            ->select("apolice.produto_parceiro_plano_id, apolice.apolice_status_id, apolice_status.nome as apolice_status_nome")
            ->select("apolice_status.slug as apolice_status_slug")
            ->select("produto_parceiro_plano.produto_parceiro_id")
            ->select("produto_parceiro_plano.codigo_operadora as cod_produto_seg")
            ->select("produto_parceiro.parceiro_id as parceiro_seg_id")
            ->select("parceiro_seg.slug as slug_parceiro_seg")
            ->select("parceiro_seg.nome as seguradora")
            ->select("parceiro.slug as slug_parceiro")
            ->select("parceiro.nome as parceiro")
            ->select("produto_parceiro.slug_produto")
            ->select("produto.nome as produto")
            ->select("produto.slug as slug_produto_seguro")
            ->join("apolice_status", "apolice.apolice_status_id = apolice_status.apolice_status_id", "inner")
            ->join("produto_parceiro_plano", "apolice.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id", "inner")
            ->join("produto_parceiro", "produto_parceiro_plano.produto_parceiro_id = produto_parceiro.produto_parceiro_id", "inner")
            ->join("produto", "produto_parceiro.produto_id = produto.produto_id", "inner")
            ->join("parceiro parceiro_seg", "parceiro_seg.parceiro_id = produto_parceiro.parceiro_id", "inner")
            ->join("parceiro", "parceiro.parceiro_id = apolice.parceiro_id", "inner")
            ->join("capitalizacao_serie_titulo", "apolice.pedido_id = capitalizacao_serie_titulo.pedido_id and capitalizacao_serie_titulo.deletado = 0", 'left');

        if ($pedido)
        {
            $pedido = $pedido[0];
            if ($pedido['slug'] == 'seguro_viagem')
            {
                $this->_database->join("apolice_seguro_viagem apolice_auxiliar", "apolice.apolice_id = apolice_auxiliar.apolice_id", "inner");
            } 
            elseif ($pedido['slug'] == 'equipamento')
            {
                $this->_database->join("apolice_equipamento apolice_auxiliar", "apolice.apolice_id = apolice_auxiliar.apolice_id", "inner");
            }
            elseif ($pedido["slug"] == "generico" || $pedido["slug"] == "seguro_saude")
            {
                $this->_database->join("apolice_generico apolice_auxiliar", "apolice.apolice_id = apolice_auxiliar.apolice_id", "inner");
            }

            $this->_database->select("apolice_auxiliar.*
                , IFNULL(apolice_auxiliar.numero_sorte, capitalizacao_serie_titulo.numero) as numero_sorte
                , IFNULL(apolice_auxiliar.num_proposta_capitalizacao, capitalizacao_serie_titulo.num_lote) as num_proposta_capitalizacao
                , IFNULL(av.rec_data_inicio_vigencia, apolice_auxiliar.data_ini_vigencia) as data_ini_vigencia
                , IFNULL(av.rec_data_fim_vigencia, apolice_auxiliar.data_fim_vigencia) as data_fim_vigencia
                 ", FALSE);
            $this->_database->join("apolice_vigencia_nm av", "apolice.apolice_id = av.apolice_id", "left");
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
                $arrApolice['valor_parcela'] = $pedido['valor_parcela'];
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

        $data_template = array();
        $template = '';
        $apolice = $this->getApolice($apolice_id);

        if (count($apolice) == 0) {
            $this->session->set_flashdata('fail_msg', 'Apólice não esta liberado'); //Mensagem de sucesso
            return false;
        }

        $dados = $this->pedido->getPedidoProdutoParceiro($apolice['pedido_id']);
        $dados = $dados[0];

        if ( !empty($dados['template_apolice']) )
        {
            $template = $this->templates($dados['template_apolice'], $apolice_id, $apolice, $dados);
        }

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
            if (ob_get_length()) ob_end_clean();
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
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        $this->load->model('capitalizacao_model', 'capitalizacao');
        $this->load->model('capitalizacao_serie_titulo_model', 'capitalizacao_serie_titulo');
        $this->load->model('apolice_equipamento_model', 'apolice_equipamento');
        $this->load->model('apolice_generico_model', 'apolice_generico');
        $this->load->model('apolice_seguro_viagem_model', 'apolice_seguro_viagem');

        $capitalizacoes = [];
        $apolices = $this->getApolicePedido($pedido_id);
        if ( empty($apolices) )
            return false;

        //verifica se tem capitalização configurado
        $parceiro_capitalizacao = $this->produto_parceiro_capitalizacao->with_capitalizacao()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_capitalizacao_ativa()
            ->get_all();

        foreach ($apolices as $apolice)
        {
            $plano_capitalizacao = $this->produto_parceiro_plano
                ->with_capitalizacao()
                ->filter_by_capitalizacao_ativa()
                ->get_by_id($apolice['produto_parceiro_plano_id']);

            if ( !empty($plano_capitalizacao) )
            {
                // get_by_id retorna apenas 1 registro
                $plano_capitalizacao['apolice_id']                 = $apolice['apolice_id'];
                $plano_capitalizacao['slug']                       = $apolice['slug_produto_seguro'];
                $plano_capitalizacao['numero_sorte']               = $apolice['numero_sorte'];
                $plano_capitalizacao['num_proposta_capitalizacao'] = $apolice['num_proposta_capitalizacao'];
                $capitalizacoes[] = $plano_capitalizacao;
            } else
            {
                //capitalização no produto
                if ( !empty($parceiro_capitalizacao) )
                {
                    // get_all retornar vários registros
                    foreach ($parceiro_capitalizacao as $key => $value) {
                        $value['apolice_id']                 = $apolice['apolice_id'];
                        $value['slug']                       = $apolice['slug_produto_seguro'];
                        $value['numero_sorte']               = $apolice['numero_sorte'];
                        $value['num_proposta_capitalizacao'] = $apolice['num_proposta_capitalizacao'];
                        $capitalizacoes[] = $value;
                    }
                }
            }
        }

        // caso não tenha capialização no plano
        if ( empty($capitalizacoes) )
            return false;

        foreach ($capitalizacoes as $index => $item)
        {
            // Dados de entrada
            $dados_capitalizacao                = array();
            $dados_capitalizacao['pedido_id']   = $pedido_id;
            $dados_capitalizacao['utilizado']   = 1;
            $dados_capitalizacao['data_compra'] = date('Y-m-d H:i:s');

            // Parceiro
            if ($item['capitalizacao_responsavel_num_sorte_distribuicao'] == 1)
            {
                // Recupera o número da Sorte
                $numero_sorte               = $item['numero_sorte'];
                $num_proposta_capitalizacao = $item['num_proposta_capitalizacao'];

                // Se foi enviado o número da sorte - Aceita "0"
                if ( !ifempty($numero_sorte) )
                {
                    // validar se está dentro da range
                    $capitalizacao_serie = $this->capitalizacao->getDadosSerie($item['capitalizacao_id'], $numero_sorte, $num_proposta_capitalizacao);
                    if ($capitalizacao_serie)
                    {
                        // Caso não exista os dados enviados
                        if ( empty($capitalizacao_serie['capitalizacao_serie_titulo_id']) )
                        {
                            // faz o insert dos dados completos
                            $dados_capitalizacao['capitalizacao_serie_id'] = $capitalizacao_serie['capitalizacao_serie_id'];
                            $dados_capitalizacao['contemplado'] = 0;
                            $dados_capitalizacao['numero'] = $numero_sorte;
                            $dados_capitalizacao['num_lote'] = emptyor($num_proposta_capitalizacao, 1);
                            $dados_capitalizacao['ativo'] = 1;
                            $this->capitalizacao_serie_titulo->insert($dados_capitalizacao, true);
                        }
                        else
                        {
                            // apenas atualiza que foi usado e em qual pedido
                            $this->capitalizacao_serie_titulo->update($capitalizacao_serie['capitalizacao_serie_titulo_id'], $dados_capitalizacao, true);
                        }
                    }
                }

            } else
            {
                $capitalizacao = $this->capitalizacao->getTituloNaoUtilizado($item['capitalizacao_id']);
                if (count($capitalizacao) > 0)
                {
                    $obj_aux = null;
                    $capitalizacao = $capitalizacao[0];
                    $this->capitalizacao_serie_titulo->update($capitalizacao['capitalizacao_serie_titulo_id'], $dados_capitalizacao, true);

                    // atualizar o número da sorte utilizado na apólice
                    if ($item['slug'] == 'seguro_viagem')
                    {
                        $obj_aux = $this->apolice_seguro_viagem;

                    } elseif ($item['slug'] == 'equipamento')
                    {
                        $obj_aux = $this->apolice_equipamento;

                    } elseif ($item["slug"] == "generico" || $item["slug"] == "seguro_saude")
                    {
                        $obj_aux = $this->apolice_generico;
                    }

                    if ( $obj_aux )
                    {
                        $obj_aux->update_by(
                            array('apolice_id' => $item['apolice_id']), array(
                                'numero_sorte' => $capitalizacao['numero'],
                                'num_proposta_capitalizacao' => $capitalizacao['num_lote']
                            )
                        );
                    }
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

    public function getApoliceByNumeroCliente($num_apolice, $parceiro_id)
    {
        $this->_database->select("apolice.apolice_id, apolice.apolice_status_id, apolice_status.nome")
            ->join("apolice_status", "apolice.apolice_status_id = apolice_status.apolice_status_id", 'inner')
            ->where("apolice.num_apolice_cliente", $num_apolice)
            ->where("apolice.parceiro_id", $parceiro_id);
        return $this->get_all();
    }

    public function search_apolice_produto_parceiro_plano_id($num_apolice, $produto_parceiro_plano_id)
    {
        $sql = "SELECT a.apolice_id
            FROM apolice a
            join produto_parceiro_plano ppp on a.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
            join produto_parceiro pp on ppp.produto_parceiro_id = pp.produto_parceiro_id and pp.deletado = 0
            WHERE 1
            and a.num_apolice = '{$num_apolice}'
            and pp.parceiro_id IN(
                select ppx.parceiro_id
                from produto_parceiro_plano pppx
                join produto_parceiro ppx on pppx.produto_parceiro_id = ppx.produto_parceiro_id and ppx.deletado = 0
                where pppx.produto_parceiro_plano_id = {$produto_parceiro_plano_id} and pppx.deletado = 0
            )
            AND a.deletado = 0
            LIMIT 1;
        ";
        return (int) $this->_database->query($sql)->num_rows() == 1;
    }

    //Retorna por Número da Apolice
    function search_apolice_produto_parceiro_id($produto_parceiro_id)
    {
        $this->db->join('pedido', "pedido.pedido_id = {$this->_table}.pedido_id", 'join');
        $this->db->join('cotacao', "cotacao.cotacao_id = pedido.cotacao_id", 'join');
        $this->db->join('produto_parceiro', "produto_parceiro.produto_parceiro_id = cotacao.produto_parceiro_id", 'join');
        $this->db->where('produto_parceiro.produto_parceiro_id', $produto_parceiro_id);
        $this->db->order_by('pedido.pedido_id', 'ASC');
        $this->db->limit(1);
        return $this;
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

    /**
     * Busca por número da apólice do cliente
     * @param string $num_apolice_cliente
     * @return array
     * @author Cristiano Arruda
     * @since  15/08/2019
     */
    function filter_by_numApoliceCliente($num_apolice_cliente)
    {
        $this->db->where("{$this->_table}.num_apolice_cliente = '{$num_apolice_cliente}' ");
        return $this;
    }

    public function defineNumApolice($produto_parceiro_id, $etapa = 'contratacao', $cotacao_id = NULL, $data_template = [])
    {
        $this->load->model('produto_parceiro_configuracao_model', 'parceiro_configuracao');
        $this->load->model('apolice_numero_seq_model', 'apolice_seq');
        $this->load->model('produto_parceiro_apolice_range_model', 'apolice_range');
        $this->load->model('produto_parceiro_apolice_multiplo_model', 'apolice_multiplo');
        $this->load->model('produto_parceiro_apolice_multiplo_range_model', 'apolice_multiplo_range');
        $this->load->model('cotacao_model', 'cotacao');

        //obter numero da apolice;
        $configuracao = $this->parceiro_configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        $configuracao = $configuracao[0];
        $num_apolice = NULL;

        // É cotação e NÃO gera apolice nessa etapa
        if ( $etapa == 'cotacao' && empty($configuracao['gera_num_apolice_cotacao']) )
            return NULL;

        // É contratação e gerou apolice na etapa de cotação
        if ( $etapa == 'contratacao' && !empty($configuracao['gera_num_apolice_cotacao']) )
        {
            if ( !empty($cotacao_id) )
            {
                $cotacao_salva = $this->cotacao->filterByID($cotacao_id)->get_all();
                if ( !empty($cotacao_salva) )
                {
                    $num_apolice = $cotacao_salva[0]['numero_apolice'];
                }
            }

            return $num_apolice;
        }

        if ($configuracao['apolice_sequencia'] == 1) {
            //é número Sequencial
            $num_apolice = $this->apolice_seq->get_proximo_codigo($produto_parceiro_id);
        } else {

            // o sequencial é composto por vários produtos
            if ($apolice_multiplo = $this->apolice_multiplo->get_by_produto_parceiro_id($produto_parceiro_id)->get_all() ) {
                $apolice_multiplo = $apolice_multiplo[0];

                $num_apolice = $this->apolice_multiplo_range->get_proximo_codigo($apolice_multiplo['produto_parceiro_apolice_multiplo_range_id']);
            } else {
                $num_apolice = $this->apolice_range->get_proximo_codigo($produto_parceiro_id);
            }

        }

        if ( empty($data_template) && $etapa == 'contratacao' && !empty($cotacao_id) )
        {
            $dadNumApol = $this->cotacao->getDadosNumApolice($cotacao_id);

            if ( !empty($dadNumApol) )
            {
                $data_template = $dadNumApol[0];
            }
        }

        // Faz o parse das variáveis
        $num_apolice = $this->apolice_range->getSequencialVariavel($num_apolice, 0, $data_template, true);

        return $num_apolice['result'];
    }

    /**
     * Busca dados da Seguradora para montagem do número do bilhete
     * @param $localidade_id
     * @return $this
     */
    public function defineDadosBilhete($produto_parceiro_plano_id)
    {
        $this->load->model('produto_parceiro_model', 'produto_parceiro');

        $dados = $this->produto_parceiro->getDadosToBilhete($produto_parceiro_plano_id);
        $result['cod_ramo']      = null;
        $result['cod_produto']   = null;
        $result['cod_sucursal']  = null;
        $result['cod_tpa']       = null;

        if ( !empty($dados) )
        {
            // salva os dados do CTA
            $dados = $dados[0];
            $result['cod_ramo']      = $dados['cod_ramo'];
            $result['cod_produto']   = $dados['cod_produto'];
            $result['cod_sucursal']  = $dados['cod_sucursal'];
            $result['cod_tpa']       = $dados['cod_tpa'];
        }

        return $result;
    }

    /**
     * Retorna true se o controle de endosso é feito pelo cliente (buscando pelo pedido)
     * @param int $pedido_id
     * @return bool
     * @author Davi Souto
     * @since  08/04/2019
     */
    function isControleEndossoPeloClienteByPedidoId($pedido_id)
    {
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('produto_parceiro_pagamento_model', 'parceiro_pagamento');

        // Carregar pedido
        $pedido = $this->pedido->getPedidosByID($pedido_id);
        if ( empty($pedido) )
        {
            return [ 'num_parcela' => 1, 'tipo_pagto' => false ];
        }

        $pedido                         = $pedido[0];
        $produto_parceiro_id            = $pedido['produto_parceiro_id'];
        $produto_parceiro_pagamento_id  = $pedido['produto_parceiro_pagamento_id'];
        $num_parcela                    = emptyor($pedido['num_parcela'], 1);
        $endosso                        = $this->isControleEndossoPeloClienteByProdutoParceiroId($produto_parceiro_id);
        $tipo_pagto                     = $this->parceiro_pagamento->isRecurrent($produto_parceiro_pagamento_id) ? 1 : $endosso;

        return [ 'num_parcela' => $num_parcela, 'tipo_pagto' => $tipo_pagto ];
    }

    /**
     * Retorna true se o controle de endosso é feito pelo cliente (buscando pelo produto_parceiro_id)
     * @param int $pedido_id
     * @return bool
     * @author Davi Souto
     * @since  08/04/2019
     */
    function isControleEndossoPeloClienteByProdutoParceiroId($produto_parceiro_id)
    {
        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');

        $configuracoes = $this->produto_parceiro_configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        $configuracoes = $configuracoes[0];

        return $configuracoes['endosso_controle_cliente'];
    }

    /**
     * Retorna os dados utilizado nas definições de campos do CTA
     * @param int $apolice_id
     * @return array
     * @author Cristiano Arruda
     * @since  08/04/2019
     */
    function getProdutoParceiro($apolice_id) {
        $this->_database->select('pa.slug, pp.cod_tpa');
        $this->_database->join("pedido p", "p.pedido_id = {$this->_table}.pedido_id", "inner");
        $this->_database->join("cotacao c", "p.cotacao_id = c.cotacao_id", "inner");
        $this->_database->join("produto_parceiro pp", "pp.produto_parceiro_id = c.produto_parceiro_id", "inner");
        $this->_database->join("parceiro pa", "pa.parceiro_id = pp.parceiro_id", "inner");
        $this->_database->where("{$this->_table}.apolice_id", $apolice_id);
        $this->_database->where("{$this->_table}.deletado", 0);
        $this->_database->where("p.deletado", 0);
        $this->_database->where("pp.deletado", 0);
        $result = $this->get_all();
        if (!empty($result)) {
            return $result[0];
        }
        return null;
    }

    /**
     * Valida o número da apólice do Cliente DE / PARA
     * @param array $dadosPP (códigos do CTA)
     * @return string
     * @author Cristiano Arruda
     * @since  08/04/2019
     */
    function defineNumApoliceCliente($dadosPP) {
        $num_apolice_cliente = '';

        if (!empty($dadosPP)) {
            // LASA RF+QA NOVOS
            if ($dadosPP['cod_tpa'] == '007') {
                $num_apolice_aux = $dadosPP['cod_sucursal'] . $dadosPP['cod_ramo'] . $dadosPP['cod_tpa'];
                $num_apolice_aux .= str_pad(substr($dadosPP['num_apolice'], 7, 8), 8, '0', STR_PAD_LEFT);
                $num_apolice_cliente = $num_apolice_aux;
            } else {
                $num_apolice_cliente = $dadosPP['num_apolice'];
            }
        }

        return $num_apolice_cliente;
    }

    function getByRespoCobertura($parceiro_slug = null)
    {
        $this->_database->distinct()
            // ->select("72 as parceiroID, produto_parceiro_plano.produto_parceiro_id, IFNULL(apm.apolice_movimentacao_id, apolice_movimentacao.apolice_movimentacao_id) AS apolice_movimentacao_id, apolice_movimentacao_tipo.slug as slugMov, produto_parceiro_servico.produto_parceiro_servico_id, produto_parceiro_servico.param", FALSE)
            ->select("parceiro.parceiro_id as parceiroID, produto_parceiro_plano.produto_parceiro_id, IFNULL(apm.apolice_movimentacao_id, apolice_movimentacao.apolice_movimentacao_id) AS apolice_movimentacao_id, apolice_movimentacao_tipo.slug as slugMov, produto_parceiro_servico.produto_parceiro_servico_id, produto_parceiro_servico.param", false)
            ->join("apolice_status", "apolice.apolice_status_id = apolice_status.apolice_status_id", 'inner')
            ->join("apolice_movimentacao", "apolice.apolice_id = apolice_movimentacao.apolice_id", 'inner')
            ->join("apolice_movimentacao apm", "apolice.apolice_id = apm.apolice_id AND apm.apolice_movimentacao_tipo_id = 1", 'left')
            ->join("apolice_movimentacao_tipo", "apolice_movimentacao.apolice_movimentacao_tipo_id = apolice_movimentacao_tipo.apolice_movimentacao_tipo_id", 'inner')
            ->join("produto_parceiro_plano", "apolice.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id", 'inner')
            ->join("cobertura_plano", "produto_parceiro_plano.produto_parceiro_plano_id = cobertura_plano.produto_parceiro_plano_id", 'inner')
            ->join("parceiro", "parceiro.parceiro_id = cobertura_plano.parceiro_id", 'inner')
            ->join("produto_parceiro_servico", "produto_parceiro_servico.produto_parceiro_id = produto_parceiro_plano.produto_parceiro_id AND produto_parceiro_servico.deletado = 0", 'inner')
            ->join("produto_parceiro_servico_log", "produto_parceiro_servico.produto_parceiro_servico_id = produto_parceiro_servico_log.produto_parceiro_servico_id AND apolice_movimentacao.apolice_movimentacao_id = produto_parceiro_servico_log.idConsulta AND produto_parceiro_servico_log.deletado = 0 AND produto_parceiro_servico_log.consulta = IF(apolice_movimentacao_tipo.slug = 'A', 'new', 'cancel')", 'left')
            ->where("apolice_status.slug IN('ativa','cancelada')")
            ->where('produto_parceiro_servico_log.produto_parceiro_servico_log_id IS NULL', null, FALSE);

        if (!empty($parceiro_slug)) {
            $this->_database->where('parceiro.slug', $parceiro_slug);
        }

        return $this->get_all();

    }

    /**
     * Valida se a apolice se encontra no status desejado
     * @param int $apolice_id
     * @param string $slug_status
     * @return boolean
     * @author Cristiano Arruda
     * @since  12/07/2019
     */
    function getApoliceStatus($apolice_id, $slug_status) {
        $this->_database->join("apolice_status as ast ", "ast.apolice_status_id = {$this->_table}.apolice_status_id", "inner");
        $this->_database->where("{$this->_table}.apolice_id", $apolice_id);
        $this->_database->where("ast.slug", $slug_status);
        return !empty($this->get_all());
    }

    public function setApoliceStatusBySlug_DAO($slug){
        $aApoliceStatusSlug = array("ativa", "cancelada");   
        if(!is_string($slug)){
            throw new Exception('O slug do status da apolice deve ser uma variável do tipo "String"');
        } else{
            if(!in_array($slug, $aApoliceStatusSlug)){
                throw new Exception('Slug "'.$slug.'" inválido para status da apolice (Opções Permitidas: ['.implode($aApoliceStatusSlug).']")');
            }else{                                
                $this->db->where("apolice_status.slug = '".$slug."'");
            }
        }
    }

    public function termo($apolice_id, $export = "", $produto_parceiro_id = null)
    {
        $this->load->model('pedido_model', 'pedido');        
        $this->load->model('produto_parceiro_termo_model', 'termo');

        $data_template = array();
        $template = '';
        if($apolice_id == null){
            $termo = $this->termo->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
            if ( !empty($termo) )
            {
                $termo = $termo[0];
                if ( !empty($termo['termo']) )
                {
                    $template = $termo['termo'];
                }
            }
        }else{
            $apolice = $this->getApolice($apolice_id);

            if (count($apolice) == 0) {
                $this->session->set_flashdata('fail_msg', 'Apólice não esta liberado'); //Mensagem de sucesso
                return false;
            }

            $dados = $this->pedido->getPedidoProdutoParceiro($apolice['pedido_id']);
            $dados = $dados[0];
            $produtoSlug = $apolice['produto_slug'];
            $termo = $this->termo->filter_by_produto_parceiro($dados['produto_parceiro_id'])->get_all();
            if ( !empty($termo) )
            {
                $termo = $termo[0];
                if ( !empty($termo['termo']) )
                {
                    $template = $this->templates($termo['termo'], $apolice_id, $apolice, $dados);
                }
            }
        }

        if (($export == 'pdf') || ($export == 'pdf_file')) {
            $this->custom_loader->library('pdf');
            $this->pdf->setPageOrientation('P');
            $this->pdf->AddPage();

            $destino_dir = FCPATH . "assets/files/{{$produtoSlug}}/termo/";
            if (!file_exists($destino_dir)) {
                mkdir($destino_dir, 0777, true);
            }
            $this->pdf->SetMargins(5, 5, 5);
            $this->pdf->writeHTML($template, true, false, true, false, '');
            $destino = ($export == 'pdf') ? 'D' : 'F';
            $file    = ($export == 'pdf') ? "{$termo['produto_parceiro_termo_id']}.pdf" : "{$destino_dir}{$termo['produto_parceiro_termo_id']}.pdf";
            if (ob_get_length()) ob_end_clean();
            $this->pdf->Output($file, $destino);
            $this->custom_loader->unload_library('pdf');
            if ($export == 'pdf_file') {
                return "{$destino_dir}{$termo['produto_parceiro_termo_id']}.pdf";
            } else {
                exit;
            }

        } else {
            return $template;
        }
    }

    public function autorizacao_cobranca($apolice_id, $export = "")
    {
        $this->load->model('pedido_model', 'pedido');        
        $this->load->model('produto_parceiro_autorizacao_cobranca_model', 'autorizacao_cobranca');

        $data_template = array();
        $template = '';
        $apolice = $this->getApolice($apolice_id);

        if (count($apolice) == 0) {
            $this->session->set_flashdata('fail_msg', 'Apólice não esta liberado'); //Mensagem de sucesso
            return false;
        }

        $dados = $this->pedido->getPedidoProdutoParceiro($apolice['pedido_id']);
        $dados = $dados[0];
        $autorizacao_cobranca = $this->autorizacao_cobranca->filter_by_produto_parceiro($dados['produto_parceiro_id'])->get_all();

        if ( !empty($autorizacao_cobranca) )
        {
            $autorizacao_cobranca = $autorizacao_cobranca[0];
            if ( !empty($autorizacao_cobranca['autorizacao_cobranca']) )
            {
                $template = $this->templates($autorizacao_cobranca['autorizacao_cobranca'], $apolice_id, $apolice, $dados);
            }
        }

        if (($export == 'pdf' || $export == 'pdf_file') && !empty($template)) {
            $this->custom_loader->library('pdf');
            $this->pdf->setPageOrientation('P');
            $this->pdf->AddPage();

            $destino_dir = FCPATH . "assets/files/{{$apolice['produto_slug']}}/autorizacao_cobranca/";
            if (!file_exists($destino_dir)) {
                mkdir($destino_dir, 0777, true);
            }
            $this->pdf->SetMargins(5, 5, 5);
            $this->pdf->writeHTML($template, true, false, true, false, '');
            $destino = ($export == 'pdf') ? 'D' : 'F';
            $file    = ($export == 'pdf') ? "{$autorizacao_cobranca['produto_parceiro_autorizacao_cobranca_id']}.pdf" : "{$destino_dir}{$autorizacao_cobranca['produto_parceiro_autorizacao_cobranca_id']}.pdf";
            if (ob_get_length()) ob_end_clean();
            $this->pdf->Output($file, $destino);
            $this->custom_loader->unload_library('pdf');
            if ($export == 'pdf_file') {
                return "{$destino_dir}{$autorizacao_cobranca['produto_parceiro_autorizacao_cobranca_id']}.pdf";
            } else {
                exit;
            }

        } else {
            return $template;
        }
    }

    public function solicitacao_desistencia($apolice_id, $export = "")
    {
        $this->load->model('pedido_model', 'pedido');        
        $this->load->model('produto_parceiro_solicitacao_desistencia_model', 'solicitacao_desistencia');

        $data_template = array();
        $template = '';
        $apolice = $this->getApolice($apolice_id);

        if (count($apolice) == 0) {
            $this->session->set_flashdata('fail_msg', 'Apólice não esta liberado'); //Mensagem de sucesso
            return false;
        }

        $dados = $this->pedido->getPedidoProdutoParceiro($apolice['pedido_id']);
        $dados = $dados[0];
        $solicitacao_desistencia = $this->solicitacao_desistencia->filter_by_produto_parceiro($dados['produto_parceiro_id'])->get_all();

        if ( !empty($solicitacao_desistencia) )
        {
            $solicitacao_desistencia = $solicitacao_desistencia[0];
            if ( !empty($solicitacao_desistencia['solicitacao_desistencia']) )
            {
                $template = $this->templates($solicitacao_desistencia['solicitacao_desistencia'], $apolice_id, $apolice, $dados);
            }
        }

        if (($export == 'pdf') || ($export == 'pdf_file')) {
            $this->custom_loader->library('pdf');
            $this->pdf->setPageOrientation('P');
            $this->pdf->AddPage();

            $destino_dir = FCPATH . "assets/files/{{$apolice['produto_slug']}}/solicitacao_desistencia/";
            if (!file_exists($destino_dir)) {
                mkdir($destino_dir, 0777, true);
            }
            $this->pdf->SetMargins(5, 5, 5);
            $this->pdf->writeHTML($template, true, false, true, false, '');
            $destino = ($export == 'pdf') ? 'D' : 'F';
            $file    = ($export == 'pdf') ? "{$solicitacao_desistencia['produto_parceiro_solicitacao_desistencia_id']}.pdf" : "{$destino_dir}{$solicitacao_desistencia['produto_parceiro_solicitacao_desistencia_id']}.pdf";
            if (ob_get_length()) ob_end_clean();
            $this->pdf->Output($file, $destino);
            $this->custom_loader->unload_library('pdf');
            if ($export == 'pdf_file') {
                return "{$destino_dir}{$solicitacao_desistencia['produto_parceiro_solicitacao_desistencia_id']}.pdf";
            } else {
                exit;
            }

        } else {
            return $template;
        }
    }

    public function templates($template, $apolice_id, $apolice = [], $dados = [])
    {
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('localidade_model', 'localidade');
        $this->load->model('parceiro_model', 'parceiro_model');
        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('cobertura_plano_model', 'plano_cobertura');
        $this->load->model('produto_parceiro_termo_model', 'termo');
        $this->load->model('Comissao_gerada_model', 'comissao_gerada');
        
        $this->load->library('parser');

        $data_template = array();

        if ( empty($apolice) )
        {
            $apolice = $this->getApolice($apolice_id);
        }

        if (count($apolice) == 0) {
            $this->session->set_flashdata('fail_msg', 'Apólice não esta liberado'); //Mensagem de sucesso
            return '';
        }

        if ( empty($dados) )
        {
            $dados = $this->pedido->getPedidoProdutoParceiro($apolice['pedido_id']);
            $dados = $dados[0];
        }

        // Relacionamento corretora
        $data_template['rel_corretora_nome']           = '';
        $data_template['rel_corretora_cnpj']           = '';
        $data_template['rel_corretora_codigo_susep']   = '';
        $data_template['rel_corretora_comissao']       = '';
        $data_template['rel_corretora_comissao_valor'] = '';

        if(isset($dados['produto_parceiro_id']) && !empty($dados['produto_parceiro_id']))
        {
            $this->load->model('parceiro_relacionamento_produto_model', 'parceiro_relacionamento_produto');
            $dados_prp = $this->parceiro_relacionamento_produto->filter_by_produto_parceiro($dados['produto_parceiro_id'])->with_parceiro()->filter_by_parceiro_tipo('2')->get_all(0, 0, false);

            if (!empty($dados_prp)) {
                $data_template['rel_corretora_nome'] = $dados_prp[0]['parceiro_nome'];
                $data_template['rel_corretora_cnpj'] = app_cnpj_to_mask($dados_prp[0]['parceiro_cnpj']);
                $data_template['rel_corretora_codigo_susep'] = $dados_prp[0]['parceiro_codigo_susep'];

                $aComissaoCorretor = $this->comissao_gerada->getByParceiroId($apolice["pedido_id"], $dados_prp[0]['parceiro_id']);
                if(sizeof($aComissaoCorretor)){
                    $comissaoCorretor = $aComissaoCorretor[0];
                    $data_template["rel_corretora_comissao"] = app_format_currency($comissaoCorretor["comissao"])."%";
                    $data_template["rel_corretora_comissao_valor"] = "R$ ".app_format_currency($comissaoCorretor["valor"]);
                }
            }
        }

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
        $termo    = $this->termo->filter_by_produto_parceiro($dados['produto_parceiro_id'])->get_all();
        $termo    = (isset($termo[0])) ? $termo[0] : array('termo' => '');

        $aComissaoGerada = $this->comissao_gerada->getByParceiroId($apolice["pedido_id"], $apolice['parceiro_id']);
        if(sizeof($aComissaoGerada)){
            $comissaoGerada = $aComissaoGerada[0];
            $data_template["representante_comissao"] = app_format_currency($comissaoGerada["comissao"])."%";
            $data_template["representante_comissao_valor"] = "R$ ".app_format_currency($comissaoGerada["valor"]);
        }else{
            $data_template["representante_comissao"] = "";
            $data_template["representante_comissao_valor"] = "";
        }
        
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
        $data_template['produto_nome']      = $dados['produto_nome'];
        $data_template['num_apolice']       = $apolice['num_apolice'];
        $data_template['num_certificado']   = $apolice['num_apolice'];
        $data_template['data_ini_vigencia'] = app_date_mysql_to_mask($apolice['data_ini_vigencia'], 'd/m/Y');
        $data_template['data_fim_vigencia'] = app_date_mysql_to_mask($apolice['data_fim_vigencia'], 'd/m/Y');

        $data_template['inicio_viagem'] = app_date_mysql_to_mask($apolice['data_ini_vigencia'], 'd/m/Y');
        $data_template['fim_viagem']    = app_date_mysql_to_mask($apolice['data_fim_vigencia'], 'd/m/Y');
        $data_template['data_pedido']   = app_date_mysql_to_mask($apolice['data_adesao'], 'd/m/Y');
        $data_template['data_adesao']   = app_date_mysql_to_mask($apolice['data_adesao'], 'd/m/Y');

        $data_template["data_ini_garantia_fornecedor"]  = $data_template['data_adesao'];
        $data_template["data_fim_garantia_fornecedor"]  = date('d/m/Y', strtotime('-1 days', strtotime($apolice['data_ini_vigencia'])));

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
        $coberturas = $this->plano_cobertura->with_cobertura(["nome", "slug"])->filter_by_produto_parceiro_plano($apolice['produto_parceiro_plano_id'])->get_all();
        $coberturasAll = $this->plano_cobertura->getCoberturasApolice($apolice["apolice_id"]);

        $equipamento = $this->db->query("SELECT 
                IFNULL(em.nome, eem.nome) as marca, 
                IFNULL(ec.nome, eec.nome) as categoria, 
                IFNULL(esc.nome, eesc.nome) as equipamento, 
                ce.equipamento_nome as modelo, 
                ae.numero_sorte,
                ae.imei 
                FROM apolice_equipamento ae
                INNER JOIN apolice a ON (a.apolice_id=ae.apolice_id)
                INNER JOIN pedido p ON (p.pedido_id=a.pedido_id)
                INNER JOIN cotacao_equipamento ce ON (ce.cotacao_id=p.cotacao_id)
                INNER JOIN cotacao c ON (c.cotacao_id=p.cotacao_id)
                
                LEFT JOIN vw_Equipamentos_Linhas ec ON c.lista_id = 1 AND ec.equipamento_categoria_id=ce.equipamento_categoria_id
                LEFT JOIN vw_Equipamentos_Linhas esc ON c.lista_id = 1 AND esc.equipamento_categoria_id=ce.equipamento_sub_categoria_id
                LEFT JOIN vw_Equipamentos_Marcas em ON c.lista_id = 1 AND em.equipamento_marca_id = ce.equipamento_marca_id
                
                LEFT JOIN equipamentos_elegiveis_categoria eec ON c.lista_id = eec.lista_id AND eec.equipamento_categoria_id=ce.equipamento_categoria_id
                LEFT JOIN equipamentos_elegiveis_categoria eesc ON c.lista_id = eesc.lista_id AND eesc.equipamento_categoria_id=ce.equipamento_sub_categoria_id
                LEFT JOIN equipamentos_elegiveis_marca eem ON c.lista_id = eem.lista_id AND eem.equipamento_marca_id = ce.equipamento_marca_id
                WHERE a.apolice_id =" . $apolice["apolice_id"])->result_array();

        $data_template['lmi_ge']      = "R$ ".app_format_currency($apolice['nota_fiscal_valor']);
        if (sizeof($equipamento)) {
            $data_template['categoria']     = $equipamento[0]["categoria"];
            $data_template['equipamento']   = $equipamento[0]["equipamento"];
            $data_template['modelo']        = $equipamento[0]["modelo"];
            $data_template['marca']         = $equipamento[0]["marca"];
            $data_template['imei']          = $equipamento[0]["imei"];
            $data_template['lmi_roubo']     = app_format_currency($apolice['nota_fiscal_valor']);
            $data_template['lmi_furto']     = app_format_currency($apolice['nota_fiscal_valor']);
            $data_template['lmi_quebra']    = app_format_currency($apolice['nota_fiscal_valor']);
        } else {
            $data_template['equipamento']   = "";
            $data_template['modelo']        = "";
            $data_template['marca']         = "";
            $data_template['imei']          = "";            
        }

        $ccount = 0;
        $data_template["franquia"] = "";
        $data_template["sorteio_valor"] = "";
        $data_template['numero_sorte']  = $apolice["numero_sorte"];


        foreach ($coberturas as $cobertura) {
            $ccount = $ccount + 1;
            $data_template["cobertura_" . trim($ccount) . "_descricao"] = $cobertura["cobertura_nome"];
            $data_template["lmi_" . trim($ccount)]                      = $cobertura["descricao"];
            $data_template["franquia"] = $cobertura["franquia"];
            if(in_array($cobertura["cobertura_slug"], array("capitalizacao_nro_sorte", "sorteio_mensal"))){
                $data_template["sorteio_valor"] = "R$ ".app_format_currency($cobertura["importancia_segurada"]);
            }
        }

    

        $pagamento = $this->pedido->getPedidoPagamento($apolice['pedido_id']);
        $pagamento = $pagamento[0];

        $data_template['pagamento_tipo_pagamento']      = $pagamento['tipo_pagamento'];
        $data_template['pagamento_bandeira']            = $pagamento['bandeira'];
        $data_template['pagamento_num_parcela']         = $pagamento['num_parcela'];

        //@todo fazer listagem do numero de capitalização
        //$capitalizacao = array('numero' => $apolice['num_capitalizacao']);

        //dados segurado
        $data_template['segurado_rg']           = $apolice['rg'];
        $data_template['segurado_sexo']         = $apolice['sexo'];
        $data_template['profissao']             = "";
        $data_template['estado_civil']          = "";
        $data_template['contato_telefone']      = app_format_telefone($apolice['contato_telefone']);
        $data_template['rg_orgao_expedidor']    = $apolice['rg_orgao_expedidor'];

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

        $viewvigencia = "";
        if ( !empty($dados['template_coberturas']) )
        {
            
            $viewseguro = emptyor($dados['template_coberturas'], '');                
            if(!empty($viewseguro)){
                $dadosPP = $this->getProdutoParceiro($apolice_id);                
                $parceiro_slug = $dadosPP["slug"];
                $seguroFilename = $viewseguro;
                $viewseguro = $parceiro_slug."/".$seguroFilename;
                $viewvigencia = $parceiro_slug."/vigencia/".$seguroFilename;
            }
            
        } else {
            if($dados['slug_parceiro'] == 'tem')
            {
                $viewseguro = 'dados_seguro_tem';
            } else
            {
                $viewseguro = 'dados_seguro_cobertura';
            }
        }

        $data_template['seguro']   = $this->load->view("admin/venda/{$apolice['produto_slug']}/certificado/{$viewseguro}", array(
            'plano'          => $plano,
            'coberturas'     => $coberturas,
            'coberturas_all' => $coberturasAll,
            //  'capitalizacao' => $capitalizacao,
            'premio_liquido' => $apolice['valor_premio_net'],
            'premio_bruto' => $apolice['valor_premio_total'],
            'pagamento'  => $pagamento,
            'dados'      => $dados),
            true);

        $data_template["seguro2"] = "";
        if($viewseguro == "generali/residencial"){
            $data_template["seguro2"] = $this->load->view("admin/venda/{$apolice['produto_slug']}/certificado/generali/residencial2", array(
                'coberturas_all' => $coberturasAll
                ),
                true);
        }

        if(isset($coberturasAll) && !empty($viewvigencia)){            

            $_coberturaAll = $coberturasAll[0];
            if($_coberturaAll["data_inicio_vigencia"]){                                
                
                $vigenciaContent = $coberturasAll;
                $isVigenciaLista = true;

            }else{
                
                $vigenciaContent = array();
                $vigenciaContent["data_inicio_vigencia"] = $data_template['data_ini_vigencia'];
                $vigenciaContent["data_fim_vigencia"] = $data_template['data_fim_vigencia'];                
                $isVigenciaLista = false;

            }   
            
            $data_template["vigencia"] = $this->load->view("admin/venda/{$apolice['produto_slug']}/certificado/$viewvigencia", array(
                'content' => $vigenciaContent,
                'isLista' => $isVigenciaLista
                ),
                true);
        
        }


        
      
        $data_template['premio']    = $this->load->view("admin/venda/{$apolice['produto_slug']}/certificado/premio", array('premio_liquido' => $apolice['valor_premio_net'], 'premio_total' => $apolice['valor_premio_total']), true);
        $data_template['pagamento'] = $this->load->view("admin/venda/{$apolice['produto_slug']}/certificado/pagamento", array('pagamento' => $pagamento), true);

        return $this->parser->parse_string($template, $data_template, true);
    }

    public function parse_string($template, $data){
        return $this->parser->parse_string($template, $data, true);
    }

}
