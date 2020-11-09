<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require "Twilio/Twilio.php";
//require FCPATH . "application/libraries/Lleida/LLeida.php";

class Comunicacao
{
    private $_ci;
    private $destinatario;
    private $nome_destinatario;
    private $nome_parceiro;
    private $data_enviar;
    private $tabela;
    private $campo;
    private $chave;
    private $mensagem;
    private $mensagem_parametros;
    private $cotacao_id;
    private $url;
    public $teste = false;

    public function __construct()
    {
        $this->_ci = &get_instance();

        $this->_ci->load->model("comunicacao_model", "comunicacao_model");
        $this->_ci->load->model("comunicacao_agendamento_model", "comunicacao_agendamento_model");
        $this->_ci->load->model("comunicacao_template_model", "comunicacao_template");
        $this->_ci->load->model("comunicacao_tipo_model", "comunicacao_tipo");
        $this->_ci->load->model("comunicacao_evento_model", "comunicacao_evento");
        $this->_ci->load->model("comunicacao_engine_configuracao_model", "comunicacao_engine_configuracao");
        $this->_ci->load->model("produto_parceiro_comunicacao_model", "produto_parceiro_comunicacao");
        $this->_ci->load->model("parceiro_produto_model", "parceiro_produto");
        $this->_ci->load->model("parceiro_contato_model", "parceiro_contato");
        $this->_ci->load->model("comunicacao_log_model", "comunicacao_log");

        $this->setDataEnviar(date('Y-m-d H:i:s'));
        $this->SetCotacaoId(0);
    }

    /**
     * Envia cron de mensagens
     */
    public function enviaCronMensagens($data = [])
    {
        $mensagens = $this->_ci->comunicacao_model
            ->with_foreign()
            ->get_many_by(array(
                'comunicacao_status.slug' => "aguardando",
            ));

        if($this->teste == true){
            $mensagem = $this->_ci->comunicacao_model->with_foreign()->get(3229);
            $mensagem["mensagem_to"] = "vmarsanati@sissolucoes.com.br";
            $mensagens = array($mensagem);
        }            

        foreach ($mensagens as $mensagem) {

            $template = $this->_ci->comunicacao_template->get($mensagem['produto_parceiro_comunicacao_comunicacao_template_id']);

            $engine_id = $template['comunicacao_engine_configuracao_id']; //produto_parceiro_comunicacao_comunicacao_engine_configuracao_id

            $engine = $this->_ci->comunicacao_engine_configuracao->with_foreign()->get($engine_id);
            if ($engine) {
                $tipo_engine = $this->_ci->comunicacao_tipo->get($engine['comunicacao_engine_comunicacao_tipo_id']);

                $produto_parceiro_comunicacao_produto_parceiro_id = $mensagem["produto_parceiro_comunicacao_produto_parceiro_id"];

                $aProdutoParceiro = $this->_ci->parceiro_produto->get_many_by(array(
                    "produto_parceiro_id" => $produto_parceiro_comunicacao_produto_parceiro_id,
                    "deletado" => 0
                ));

                if(!empty($aProdutoParceiro)){
                    $produtoParceiro = $aProdutoParceiro[0];
                    $parceiroId = $produtoParceiro["parceiro_id"];

                    $aParceiroContato = $this->_ci->parceiro_contato->with_contato()->with_departamento_cargo()->filter_by_parceiro($parceiroId)->get_many_by(array(
                        "parceiro_contato.deletado" => 0,
                        "parceiro_contato.parceiro_contato_departamento_id" => 1
                    ));
                    
                    if(!empty($aParceiroContato)){
                        $parceiroContato = $aParceiroContato[0];
                        $mensagem["bcc"] = $parceiroContato["contato"];
                    }

                }      

                // popula os dados adicionais
                if ( !empty($data) )
                {
                    foreach ($data as $key => $value) {
                        $mensagem[$key] = $value;
                    }
                }

                $metodo      = "envia_{$tipo_engine['slug']}";
                $response    = $this->{$metodo}($mensagem, $engine);
                $update = array();

                $log_descricao = "";
                if (isset($response['status']) && $response['status']) {
                    $update['data_envio']            = date("Y-m-d H:i:s");
                    $update['comunicacao_status_id'] = 2;
                    $log_descricao                   = "Enviado com sucesso.";
                } else {
                    $update['comunicacao_status_id'] = 3;
                    $log_descricao                   = "Erro ao enviar";
                }

                if (isset($response['retorno'])) {
                    $update['retorno'] = substr($response['retorno'], 0, 254);
                }

                if (isset($response['retorno_codigo'])) {
                    $update['retorno_codigo'] = $response['retorno_codigo'];
                }

                //Seta log
                if ($comunicacao_id = $this->_ci->comunicacao_model->update($mensagem['comunicacao_id'], $update, true)) {
                    $log = array(
                        'comunicacao_id' => $mensagem['comunicacao_id'],
                        'descricao'      => $log_descricao,
                    );
                    $log['dados'] = "";

                    if (isset($response['retorno_codigo'])) {
                        $log['dados'] .= "[{$response['retorno_codigo']}] - ";
                    }

                    if (isset($response['retorno'])) {
                        $log['dados'] .= $response['retorno'];
                    }

                    $this->_ci->comunicacao_log->insert($log, true);
                }
            }
        }
    }

    /**
     * Envia MMS
     * @param $mensagem
     * @param $engine
     * @return array
     */
    private function envia_mms($mensagem, $engine)
    {
        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;

        $config = array(
            "accountId"    => $engine['usuario'],
            "accountToken" => $engine['senha'],
            "from"         => $engine['servidor'],
        );

        $anexos = explode("|", trim($mensagem['mensagem_anexo'], "|"));

        //Cliente
        $client = new Services_Twilio($config['accountId'], $config['accountToken']);

        try
        {
            //Trata msg
            $mensagem['mensagem_to'] = "+55" . app_retorna_numeros($mensagem['mensagem_to']);

            $sms = $client->account->messages->sendMessage($config['from'], $mensagem['mensagem_to'], $mensagem['mensagem'], $anexos);

            $response['retorno_codigo'] = $sms->sid;
            $response['status']         = true;
        } catch (Exception $e) {
            $response['status']         = false;
            $response['retorno']        = $e->getMessage();
            $response['retorno_codigo'] = $e->getCode();
        }

        return $response;

    }

    /**
     * Envia SMS
     * @param $mensagem
     * @param $engine
     * @return array
     */
    private function envia_sms_Twilio($mensagem, $engine)
    {
        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;

        $config = array(
            "accountId"    => $engine['usuario'],
            "accountToken" => $engine['senha'],
            "from"         => $engine['servidor'],
        );

        // Step 3: instantiate a new Twilio Rest Client
        $client = new Services_Twilio($config['accountId'], $config['accountToken']);

        try
        {
            //Trata msg html_entity_decode($mensagem['mensagem'])
            $mensagem['mensagem_to']    = "+55" . app_retorna_numeros($mensagem['mensagem_to']);
            $sms                        = $client->account->messages->sendMessage($config['from'], $mensagem['mensagem_to'], html_entity_decode(strip_tags($mensagem['mensagem'])));
            $response['retorno_codigo'] = $sms->sid;
            $response['status']         = true;
        } catch (Exception $e) {
            $response['status']         = false;
            $response['retorno']        = $e->getMessage();
            $response['retorno_codigo'] = $e->getCode();
        }

        return $response;

    }

    private function envia_sms($mensagem, $engine)
    {
        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;

        $mensagem['mensagem_to'] = "+55" . app_retorna_numeros($mensagem['mensagem_to']);
        $text                    = html_entity_decode(strip_tags($mensagem['mensagem']));
        $text                    = preg_replace("/\r|\t|\n/", "", $text);
        $config                  = array(
            'urlCurl'    => 'https://api.infobip.com/sms/1/text/single',
            'methodCurl' => 'POST',
            'fieldsCurl' => "{  \n   \"from\":\"SIS\",\n   \"to\":\"{$mensagem['mensagem_to']}\",\n   \"text\":\"{$text}\"\n}",
            'headerCurl' => array(
                "accept: application/json",
                "authorization: Basic c2lzX2FuZHJlbGxvOjUxUzUwNXVMdWMwMzVAMTk5MA==",
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
                $resp                       = $resp['messages'][0];
                $response['retorno_codigo'] = $resp['messageId'];

                //sucesso
                if($resp['status']['name'] == "PENDING_ENROUTE"){
                    $response['status'] = true;
                }else
                {
                    $response['retorno'] = "O SMS para o Número {$resp['to']} retornou o status {$resp['status']['name']} [{$resp['status']['description']}]";
                }
            }

        }

        return $response;

    }

    /**
     * Envia um e-mail
     * @param $mensagem
     * @param $engine
     * @return array
     */
    private function envia_email($mensagem, $engine)
    {
        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;

        //Carrega
        if ($engine['comunicacao_engine_slug'] == 'sendpulse') {
            return $this->envia_email_sendpulse($mensagem, $engine);
        } elseif ($engine['comunicacao_engine_slug'] == 'google') {
            return $this->envia_email_google($mensagem, $engine);
        } elseif ($engine['comunicacao_engine_slug'] == 'lleida') {
            return $this->envia_email_lleida($mensagem, $engine);
        } else {
            return $response;
        }

    }

    //
    // Schedel [30/05/2018]
    //
    private function envia_email_lleida($mensagem, $engine)
    {
        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;

        //Carrega

        $config['smtp_host'] = "send.one.com"; // $engine['servidor'];
        $config['smtp_port'] = "587"; // $engine['porta'];
        $config['smtp_user'] = "luis@schedel.com.br"; // $engine['usuario'];
        $config['smtp_pass'] = "lalalelelili"; // $engine['senha'];
        $config['protocol']  = "smtp";
        $config['validate']  = true;
        $config['mailtype']  = 'html';
        $config['charset']   = 'utf-8';
        $config['newline']   = "\r\n";

        $this->_ci->load->library('email', $config);

        $this->_ci->email->from($mensagem['mensagem_from'], $mensagem['mensagem_from']);
        $this->_ci->email->subject($mensagem['mensagem_from_name']);
        $this->_ci->email->reply_to($mensagem['mensagem_from']);
        $this->_ci->email->to($mensagem['mensagem_to']);
        $this->_ci->email->cc($engine['parametros']);
        if(isset($mensagem["bcc"])){
            $this->_ci->email->bcc($mensagem["bcc"]);
        }   
        $this->_ci->email->message($mensagem['mensagem']);

        if (!empty($mensagem['mensagem_anexo'])) {
            $anexos = explode('|', $mensagem['mensagem_anexo']);
            foreach ($anexos as $anexo) {
                if(!empty($anexo)){
                    $this->_ci->email->attach($anexo);
                }                
            }
        }

        $result = $this->_ci->email->send();

        if ((isset($result)) && ($result === true)) {
            $response['status'] = true;
        } else {
            $response['retorno'] = 'ERRO ENVIANDO E-MAIL';
            $response['status']  = false;
        }
        return $response;
    }

    /**
     * Envia um e-mail
     * @param $mensagem
     * @param $engine
     * @return array
     */
    private function envia_email_sendpulse($mensagem, $engine)
    {
        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;

        //Carrega

        $this->_ci->load->library('sendpulse');

        $SPApiProxy = new SendpulseApi($engine['usuario'], $engine['senha'], 'file');

        //Seta dados
        /*
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = $engine['servidor'];
        $config['smtp_user'] = $engine['usuario'];
        $config['smtp_pass'] = $engine['senha'];
        $config['smtp_port'] = $engine['porta'];
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE; */

        // Send mail using SMTP
        $files = array();
        if (!empty($mensagem['mensagem_anexo'])) {
            $anexos = explode('|', $mensagem['mensagem_anexo']);
            foreach ($anexos as $anexo) {
                if(!empty($anexo)){
                    $files[basename($anexo)] = file_get_contents($anexo);
                }                
            }
        }
        $email = array(
            'html'        => $mensagem['mensagem'],
            'text'        => strip_tags($mensagem['mensagem']),
            'subject'     => $mensagem['mensagem_from_name'],
            'from'        => array(
                'name'  => $mensagem['mensagem_from'],
                'email' => $mensagem['mensagem_from'],
            ),
            'to'          => array(
                array(
                    'name'  => $mensagem['mensagem_to_name'],
                    'email' => $mensagem['mensagem_to'],
                ),
            ),
            'attachments' => $files,
        );

        $result = $SPApiProxy->smtpSendMail($email);
        if ((isset($result->result)) && ($result->result === true)) {
            $response['status'] = true;
        } else {
            $response['retorno'] = (isset($result->message)) ? $result->message : 'ERRO ENVIANDO E-MAIL';
            $response['status']  = false;
        }
        /*
        $this->_ci->email->initialize($config);
        $this->_ci->email->from($mensagem['mensagem_from'], $mensagem['mensagem_from_name']);
        $this->_ci->email->to($mensagem['mensagem_to']);
        $this->_ci->email->subject($mensagem['mensagem_from_name']);
        $this->_ci->email->set_newline("\r\n");
        $this->_ci->email->message($mensagem['mensagem']);

        if ($this->_ci->email->send())
        {
        $response['status'] = true;
        }
        else
        {
        $response['retorno'] = $this->_ci->email->print_debugger();
        $response['status'] = false;
        }
         */
        return $response;
    }

    /**
     * Envia um e-mail
     * @param $mensagem
     * @param $engine
     * @return array
     */
    private function envia_email_google($mensagem, $engine)
    {
        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;

        //Carrega

        $config['smtp_host'] = $engine['servidor'];
        $config['smtp_port'] = $engine['porta'];
        $config['smtp_crypto'] = 'tls';    
        $config['smtp_timeout'] = '30';
        $config['protocol']  = 'smtp';
        //$config['validate']  = true;
        $config['mailtype']  = 'html';
        $config['charset']   = 'utf-8';
        $config['newline']   = "\r\n";
        $config['wordwrap'] = TRUE;

        $this->_ci->load->library('email', $config);

        //$this->_ci->email->initialize($config);
        //print_r($engine);exit('asd');
        $this->_ci->email->from($mensagem['mensagem_from'], $mensagem['mensagem_from']);
        $this->_ci->email->subject($mensagem['mensagem_from_name']);
        $this->_ci->email->reply_to($mensagem['mensagem_from']);
        $this->_ci->email->to($mensagem['mensagem_to']);
        $this->_ci->email->cc($engine['parametros']);
        if(isset($mensagem["bcc"])){
            $this->_ci->email->bcc($mensagem["bcc"]);
        }        
        $this->_ci->email->message($mensagem['mensagem']);

        if (!empty($mensagem['mensagem_anexo'])) {
            $anexos = explode('|', $mensagem['mensagem_anexo']);
            foreach ($anexos as $anexo) {
                if(!empty($anexo)){
                    $this->_ci->email->attach($anexo);
                }
                
            }
        }

        $result = $this->_ci->email->send();
        if($this->teste == true){
            print_r($this->_ci->email->print_debugger());
        }
        
        //$this->email->attach('/path/to/photo1.jpg');

        if ((isset($result)) && ($result === true)) {
            $response['status'] = true;
        } else {
            $response['retorno'] = 'ERRO ENVIANDO E-MAIL';
            $response['status']  = false;
        }

        return $response;
    }

    /**
     * Dispara evento para parceiro_id
     * @param $slug
     * @param $parceiro_id
     */
    public function disparaEvento($slug, $produto_parceiro_id)
    {

        $evento = $this->_ci->comunicacao_evento
            ->with_foreign()
            ->get_by(array('comunicacao_evento.slug' => $slug));

        if ($evento) {
            $parceiro_comunicacao = $this->_ci->produto_parceiro_comunicacao
                ->with_foreign()
                ->with_parceiro()
                ->get_by(array(
                    'produto_parceiro_comunicacao.produto_parceiro_id'   => $produto_parceiro_id,
                    'produto_parceiro_comunicacao.comunicacao_evento_id' => $evento['comunicacao_evento_id'],
                )
                );

            if ($parceiro_comunicacao) {

                if (isset($parceiro_comunicacao['parceiro_nome_fantasia'])) {
                    $this->setNomeParceiro($parceiro_comunicacao['parceiro_nome_fantasia']);
                }

                $paramentros = $this->getMensagemParametros();

                $comunicacao_template_mensagem = $parceiro_comunicacao['comunicacao_template_mensagem'];

                $mathes = array();
                $pattern = "/{anexo}(.*?){\/anexo}/s";
                preg_match_all($pattern, $parceiro_comunicacao['comunicacao_template_mensagem'], $matches);
                if(!empty($matches)){
                    $aRemove = $matches[0];
                    $comunicacao_template_mensagem = str_replace($aRemove, "", $comunicacao_template_mensagem);
                }

                $parceiro_comunicacao['comunicacao_template_mensagem'] = $comunicacao_template_mensagem;

                //print_r($paramentros);print("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n");
                $anexos = '';
                if (isset($paramentros['anexos']) && is_array($paramentros['anexos'])) {
                    $anexos = trim(implode('|', $paramentros['anexos']), "|");
                }
                $dados = array(
                    'produto_parceiro_comunicacao_id' => $parceiro_comunicacao['produto_parceiro_comunicacao_id'],
                    'comunicacao_status_id'           => 1,
                    'mensagem'                        => $this->processaMensagem($parceiro_comunicacao['comunicacao_template_mensagem']),
                    'mensagem_from'                   => $parceiro_comunicacao['comunicacao_template_mensagem_de'],
                    'mensagem_from_name'              => $parceiro_comunicacao['comunicacao_template_mensagem_titulo'],
                    'mensagem_to'                     => $this->getDestinatario(),
                    'mensagem_to_name'                => $this->getNomeDestinatario(),
                    'data_enviar'                     => $this->getDataEnviar(),
                    'tabela'                          => $this->getTabela(),
                    'campo'                           => $this->getCampo(),
                    'chave'                           => $this->getChave(),
                    'mensagem_anexo'                  => $anexos,
                    'cotacao_id'                      => $this->getCotacaoId(),
                );

                if ($dt = $this->_ci->comunicacao_model->insert($dados, true)) {
                    return true;
                }

                if((isset($parceiro_comunicacao['disparo'])) && ($parceiro_comunicacao['disparo'] == -1)){
                    if ($dt = $this->_ci->comunicacao_agendamento_model->insert($dados, true))
                    {
                        return true;
                    }

                }else{
                    if($dt = $this->_ci->comunicacao_model->insert($dados, true))
                    {
                    return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Transforma mensagem
     * @param $mensagem
     * @return mixed
     */
    private function processaMensagem($mensagem)
    {
        $this->_ci->load->library("Parser");

        $parametros = array(
            'destinatario_endereco' => $this->getDestinatario(),
            'destinatario_nome'     => $this->getNomeDestinatario(),
            'parceiro_nome'         => $this->getNomeParceiro(),
            'url'                   => $this->getUrl(),
        );

        $params = array_merge($parametros, $this->getMensagemParametros());

        return $this->_ci->parser->parse_string($mensagem, $params, true);
    }

    /**
     * @return mixed
     */
    public function getDestinatario()
    {
        return $this->destinatario;
    }

    /**
     * @param mixed $destinatario
     */
    public function setDestinatario($destinatario)
    {
        $this->destinatario = $destinatario;
    }

    /**
     * @return mixed
     */
    public function getNomeDestinatario()
    {
        return $this->nome_destinatario;
    }

    /**
     * @return mixed
     */
    public function getNomeParceiro()
    {
        return $this->nome_parceiro;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $nome_destinatario
     */
    public function setNomeDestinatario($nome_destinatario)
    {
        $this->nome_destinatario = $nome_destinatario;
    }

    /**
     * @param mixed $nome_parceiro
     */
    public function setNomeParceiro($nome_parceiro)
    {
        $this->nome_parceiro = $nome_parceiro;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }


    /**
     * @return bool|string
     */
    public function getDataEnviar()
    {
        return $this->data_enviar;
    }

    /**
     * @param bool|string $data_enviar
     */
    public function setDataEnviar($data_enviar)
    {
        $this->data_enviar = $data_enviar;
    }

    /**
     * @return mixed
     */
    public function getTabela()
    {
        return $this->tabela;
    }

    /**
     * @param mixed $tabela
     */
    public function setTabela($tabela)
    {
        $this->tabela = $tabela;
    }

    /**
     * @return mixed
     */
    public function getCampo()
    {
        return $this->campo;
    }

    /**
     * @param mixed $campo
     */
    public function setCampo($campo)
    {
        $this->campo = $campo;
    }

    /**
     * @return mixed
     */
    public function getChave()
    {
        return $this->chave;
    }

    /**
     * @return mixed
     */
    public function getCotacaoId()
    {
        return $this->cotacao_id;
    }


    /**
     * @param mixed $chave
     */
    public function setChave($chave)
    {
        $this->chave = $chave;
    }

    /**
     * @return mixed
     */
    public function getMensagem()
    {
        return $this->mensagem;
    }

    /**
     * @param mixed $mensagem
     */
    public function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;
    }

    /**
     * @return mixed
     */
    public function getMensagemParametros()
    {
        if (is_array($this->mensagem_parametros)) {
            return $this->mensagem_parametros;
        }

        return array();
    }

    /**
     * @param mixed $mensagem_parametros
     */
    public function setMensagemParametros($mensagem_parametros)
    {
        $this->mensagem_parametros = $mensagem_parametros;
    }

  /**
     * @param mixed $cotacao_id
     */
  public function SetCotacaoId($cotacao_id)
  {
    $this->cotacao_id = $cotacao_id;
  }


}
