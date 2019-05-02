<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Cron extends Admin_Controller
{

    protected $noLogin = true;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('integracao_model', 'integracao');

    }

    public function evento($pedido_id)
    {

        $comunicacao = new Comunicacao();
        $comunicacao->disparaEvento("url_efetuar_pagamento_sms", 51);
    }

    public function set_acoes()
    {
        $this->load->model("usuario_acl_recurso_model", "usuario_acl_recurso");
        $this->load->model("usuario_acl_acao_model", "usuario_acl_acao");
        $this->load->model("usuario_acl_recurso_acao_model", "usuario_acl_recurso_acao");

        $recursos = $this->usuario_acl_recurso->get_all();

        foreach ($recursos as $recurso) {

            $ra = $this->usuario_acl_recurso_acao->get_by(array(
                'usuario_acl_recurso_id' => $recurso['usuario_acl_recurso_id'],
                'usuario_acl_acao_id'    => 1,
            ));

            if (!$ra) {
                $rec_ac = $this->usuario_acl_recurso_acao->insert(array(
                    'usuario_acl_recurso_id' => $recurso['usuario_acl_recurso_id'],
                    'usuario_acl_acao_id'    => 1,
                ));

            }
        }
    }

    public function getControllers()
    {
        $this->load->library('controllerlist');
        $this->load->model("usuario_acl_recurso_model", "usuario_acl_recurso");
        $this->load->model("usuario_acl_acao_model", "usuario_acl_acao");
        $this->load->model("usuario_acl_recurso_acao_model", "usuario_acl_recurso_acao");

        $controllers = $this->controllerlist->getControllers();

        foreach ($controllers as $name => $controller) {
            $recurso = $this->usuario_acl_recurso->with_deleted()->get_by(array(
                'controller' => $name,
            ));

            if (!$recurso) {
                echo $name . "<BR>";
            } else {
                foreach ($controller as $method) {
                    $acao = $this->usuario_acl_acao->get_by(array(
                        'slug' => $method,
                    ));

                    if (!$acao) {
                        $this->usuario_acl_acao->insert(array(
                            'slug' => $method,
                            'nome' => $method,
                        ));

                        $acao = $this->usuario_acl_acao->get_by(array(
                            'slug' => $method,
                        ));
                    }

                    $ra = $this->usuario_acl_recurso_acao->get_by(array(
                        'usuario_acl_recurso_id' => $recurso['usuario_acl_recurso_id'],
                        'usuario_acl_acao_id'    => $acao['usuario_acl_acao_id'],
                    ));

                    if (!$ra) {
                        $rec_ac = $this->usuario_acl_recurso_acao->insert(array(
                            'usuario_acl_recurso_id' => $recurso['usuario_acl_recurso_id'],
                            'usuario_acl_acao_id'    => $acao['usuario_acl_acao_id'],
                        ));

                    }
                }
            }

        }
    }

    public function faturamento()
    {
        $this->load->model("fatura_model", "fatura");
        $this->fatura->faturamento();
    }

    public function index()
    {
        $this->comunicacao();
    }

    public function comunicacao($data = [])
    {
        $comunicacao = new Comunicacao();
        $comunicacao->enviaCronMensagens($data);
    }

    public function comissao()
    {

        $this->load->model('comissao_gerada_model', 'comissao_gerada');
        // $this->comissao_gerada->gerar_comissao_parceiro();
        $this->comissao_gerada->gerar_comissao_parceiro_relacionamento();
        error_log(date("Y-m-d H:i:s") . " - Comissão gerada\n", 3, "/var/log/httpd/econnects.log");

    }

    /**
     * Cron para cotações 2 dias antes não finalizadas
     */
    public function cotacao_nao_finalizada()
    {
        $this->load->model("cotacao_model", "cotacao");
        $this->load->model("comunicacao_model");
        $this->load->library("Comunicacao");

        $cotacoes_abertas = $this->cotacao
            ->with_status()
            ->with_clientes_contatos()
            ->where("cotacao_status.slug", "=", "aberta")
            ->where("cotacao_seguro_viagem.data_saida", "<=", date("Y-m-d H:i:s", strtotime("+2 days")))
            ->get_all();

        $cotacoes_lead = $this->cotacao
            ->with_status()
            ->with_clientes_contatos()
            ->where("cotacao_status.slug", "=", "lead")
            ->where("cotacao_seguro_viagem.data_saida", "<=", date("Y-m-d H:i:s", strtotime("+2 days")))
            ->get_all();

        $cotacoes = array_merge($cotacoes_lead, $cotacoes_abertas);

        foreach ($cotacoes as $cotacao) {
            $verifica_ja_comunicado = $this->comunicacao_model->get_by(array(
                'tabela' => 'cotacao',
                'campo'  => 'cotacao_id',
                'chave'  => $cotacao['cotacao_id'],
            ));

            if (!$verifica_ja_comunicado) {
                $comunicacao = new Comunicacao();
                $comunicacao->setDestinatario($cotacao['email']);
                $comunicacao->setNomeDestinatario($cotacao['razao_nome']);
                $comunicacao->setMensagemParametros($cotacao);
                $comunicacao->setTabela("cotacao");
                $comunicacao->setCampo("cotacao_id");
                $comunicacao->setChave($cotacao['cotacao_id']);
                $comunicacao->disparaEvento("cotacao_aberta", $cotacao['produto_parceiro_id']);
            }

        }
    }

    public function integracao_r($integracao_id)
    {
        $this->integracao->run_r($integracao_id);
    }

    public function integracao_s($integracao_id)
    {
        $this->integracao->run_s($integracao_id);
    }

    public function integracao()
    {
        $this->integracao->run();
    }

    public function run()
    {

        $i = 10;
        while ($i > 0) {
            ini_set("memory_limit", "512M");
            set_time_limit(999999);
            ini_set('max_execution_time', 999999);
            $this->comunicacao();
            print($i . " -- " . date('H:i:s:u') . "  <br/>");
            sleep(5);
            $i--;
        }
    }

    public function cambio()
    {

        $this->load->model('moeda_cambio_model', 'moeda_cambio');
        $this->load->model('moeda_model', 'moeda');

        $this->load->library("Nusoap_lib");

        $moedas = $this->moeda->filter_by_atualizacao_cambio()->get_all();

        foreach ($moedas as $moeda) {
            //Monta array com dados
            $param = array(
                'in0' => $moeda['codigo_bc'],
            );
            try {
                $client = new nusoap_client('https://www3.bcb.gov.br/sgspub/JSP/sgsgeral/FachadaWSSGS.wsdl', array('soap_version' => SOAP_1_1));

                $response = $client->call('getUltimoValorVO', $param);

            } catch (Exception $e) {
                log_message('erro', 'ERRO CHAMADA WS ', print_r($e, true));
                print_r($e);
            }

            if (isset($response['ultimoValor']) &&
                isset($response['ultimoValor']['mes']) &&
                isset($response['ultimoValor']['dia']) &&
                isset($response['ultimoValor']['ano']) &&
                isset($response['ultimoValor']['valor'])) {

                $data      = date('Y-m-d', mktime(0, 0, 0, $response['ultimoValor']['mes'], $response['ultimoValor']['dia'], $response['ultimoValor']['ano']));
                $ja_existe = $this->moeda_cambio->filter_by_moeda($moeda['moeda_id'])->filter_by_data($data)->get_total();

                if ((int) $ja_existe == 0) {
                    $arr_dados = array();

                    $arr_dados['moeda_id']    = $moeda['moeda_id'];
                    $arr_dados['data_cambio'] = $data;
                    $arr_dados['valor_real']  = $response['ultimoValor']['valor'];
                    $this->moeda_cambio->insert($arr_dados, true);

                }

            }
        }

    }

    public function unitfour()
    {

        /*
        $this->load->library("Unitfour", array('produto_parceiro_id' => 41));

        $result  = $this->unitfour->getBasePessoaPF(app_retorna_numeros('02416313789'));
        print_r($result);exit; */

        ini_set("memory_limit", "512M");
        set_time_limit(999999);
        ini_set('max_execution_time', 999999);

        $this->load->model('base_pessoa_model', 'base_pessoa');

        $this->load->model('base_model', 'base_model');

        $registros = $this->base_pessoa->get_all();

        $i = 1;
        foreach ($registros as $index => $registro) {
            $unitfour = $this->base_pessoa->getByDoc($registro['documento'], 41);
            if (!$unitfour) {
                print("registro {$i} {$registro['cpf']}\n");
                print_r($unitfour);
            }
            $i++;
        }

    }
    public function cenario_cta()
    {
        $this->load->model('cta_movimentacao_model', 'cta_movimentacao');
        $this->cta_movimentacao->run();
    }

    /**
     * Coberturas de Parceiros Terceiros para integração
     */
    public function cobertura_parceiro()
    {
        $this->load->model("apolice_model", "apolice");
        $this->load->model('produto_parceiro_servico_model', 'produto_parceiro_servico');
        // $this->load->model("comunicacao_model");
        // $this->load->library("Comunicacao");

        $apolices = $this->apolice->getByRespoCobertura('ativa', 'powerbiz');
        $configs = [];

        foreach ($apolices as $apolice) {

            if ( !in_array($apolice['produto_parceiro_servico_id'], $configs) )
            {
                $config = $this->produto_parceiro_servico
                    ->with_servico()
                    ->with_servico_tipo()
                    ->filter_by_servico_tipo('powerbiz')
                    ->filter_by_produto_parceiro($apolice['produto_parceiro_servico_id'])
                    ->get_all();

                $configs[] = $apolice['produto_parceiro_servico_id'];

                if ( empty($config) ){
                    $config = $config[0];


                    if ($apolice['slugMov'] == 'A')
                    {
                        $this->powerbiz_new([ 
                            'apolice_id' => $apolice['apolice_id'], 
                            'param' => $apolice['param'], 
                            'produto_parceiro_servico_id' => $apolice['produto_parceiro_servico_id'] 
                        ]);
                    } else {
                        $this->powerbiz_cancel([ 
                            'apolice_id' => $apolice['apolice_id'], 
                            'param' => $apolice['param'], 
                            'produto_parceiro_servico_id' => $apolice['produto_parceiro_servico_id'] 
                        ]);
                    }
                }

            }
            
            // $verifica_ja_comunicado = $this->comunicacao_model->get_by(array(
            //     'tabela' => 'apolice_movimentacao',
            //     'campo'  => 'apolice_movimentacao_id',
            //     'chave'  => $apolice['apolice_movimentacao_id'],
            // ));

            // if (!$verifica_ja_comunicado) {
            //     $comunicacao = new Comunicacao();
            //     $comunicacao->setTabela("apolice_movimentacao");
            //     $comunicacao->setCampo("apolice_movimentacao_id");
            //     $comunicacao->setChave($apolice['apolice_movimentacao_id']);
            //     $comunicacao->disparaEvento( ($apolice['slugMov'] == 'A') ? "powerbiz_new" : "powerbiz_cancel", $apolice['parceiroID'] );

            //     // $this->comunicacao( [ 'apolice_id' => $apolice['apolice_id'] ] );
            // }

            
        }
    }

    /**
     * Integração com a PowerBiz para Emissão
     * @param $mensagem
     * @param $engine
     * @return array
     */
    private function powerbiz_new($mensagem)
    {

        // $mensagem['mensagem'] = strtr($mensagem['mensagem'], ["{apolice_id}" => $mensagem['apolice_id']]);
        // print_r($mensagem);die();
        $this->load->model('produto_parceiro_servico_log_model', 'produto_parceiro_servico_log');


        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;
        $Antes = new DateTime();

        $basic = base64_encode( $engine['usuario'] .':'. $engine['senha'] );
        $data = [
            'order' => [
                'sku' => $mensagem['param'],
            ]
        ];

        $config = array(
            'urlCurl'    => $engine['servidor'] ."new",
            'methodCurl' => 'POST',
            'fieldsCurl' => json_encode($data, true),
            'headerCurl' => array(
                "accept: application/json",
                "authorization: Basic ". $basic,
                "content-type: application/json",
                "cache-control: no-cache",
            ),
        );

        // print_r($config);echo "\n";die();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $config['urlCurl'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $config['methodCurl'],
            CURLOPT_POSTFIELDS     => $config['fieldsCurl'],
            CURLOPT_HTTPHEADER     => $config['headerCurl'],
        ));
        $resp     = curl_exec($curl);
        $info     = curl_getinfo($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err      = curl_error($curl);
        curl_close($curl);

        // print_r($config['urlCurl']);echo "\n";
        // print_r($err); echo "\n";
        // print_r($httpCode); echo "\n";
        // print_r($resp); echo "\n";
        // die();

        // Gera Log
        $Depois = new DateTime();
        $data_log["produto_parceiro_servico_id"] = $mensagem['produto_parceiro_servico_id'];
        $data_log["url"] = $config['urlCurl'];
        $data_log["consulta"] = "new";
        $data_log["retorno"] = $resp;
        $data_log["time_envio"] = $Antes->format( "H:i:s" );
        $data_log["time_retorno"] = $Depois->format( "H:i:s" );
        $data_log["parametros"] = $config['fieldsCurl'];
        $data_log["data_log"] = $Antes->format( "Y-m-d H:i:s" );
        $data_log["ip"] = ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ? $_SERVER[ "REMOTE_ADDR" ] : "" );

        if ($err) {
            $response['retorno']        = $err;
            $response['retorno_codigo'] = $httpCode;
        } else {

            if ($httpCode != "200") {
                $response['retorno']        = "HTTPCode: {$httpCode}";
                $response['retorno_codigo'] = $httpCode;
            } else {
                $resp = json_decode($resp, true);
                $code = (int)$resp['code'];

                // sucesso
                if ($code==0) {
                    $response['retorno_codigo'] = $resp['code'];
                    $response['status'] = true;
                    // $data_log["idConsulta"] = $resp['data']['order']['brid'];
                    $data_log["idConsulta"] = $mensagem['apolice_id'];
                } else {
                    $response['retorno']        = $resp['message'];
                    $response['retorno_codigo'] = $resp['code'];
                }

                


            }

        }

        $this->_ci->produto_parceiro_servico_log->insLog( $data_log["produto_parceiro_servico_id"], $data_log );
        return $response;
    }

    /**
     * Integração com a PowerBiz para Emissão
     * @param $mensagem
     * @param $engine
     * @return array
     */
    private function powerbiz_cancel($mensagem, $engine)
    {
        print_r($mensagem);
        die();

        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;

        $basic = base64_encode( $engine['usuario'] .':'. $engine['senha'] );
        $data = [
            'order' => [
                'sku' => '',
                'brid' => ''
            ]
        ];

        $config = array(
            'urlCurl'    => $engine['servidor'] ."cancel",
            'methodCurl' => 'POST',
            'fieldsCurl' => json_encode($data, true),
            'headerCurl' => array(
                "accept: application/json",
                "authorization: Basic ". $basic,
                "content-type: application/json",
                "cache-control: no-cache",
            ),
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $config['urlCurl'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $config['methodCurl'],
            CURLOPT_POSTFIELDS     => $config['fieldsCurl'],
            CURLOPT_HTTPHEADER     => $config['headerCurl'],
        ));
        $resp     = curl_exec($curl);
        $info     = curl_getinfo($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err      = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $response['retorno']        = $err;
            $response['retorno_codigo'] = $httpCode;
        } else {

            if ($httpCode != "200") {
                $response['retorno']        = "HTTPCode: {$httpCode}";
                $response['retorno_codigo'] = $httpCode;
            } else {
                $resp                       = json_decode($resp, true);
                $code                       = (int)$resp['code'];

                // sucesso
                if ($code==0) {
                    $response['retorno_codigo'] = $resp['code'];
                    $response['status'] = true;
                } else {
                    $response['retorno']        = $resp['message'];
                    $response['retorno_codigo'] = $resp['code'];
                }

            }

        }

        return $response;

    }

}
