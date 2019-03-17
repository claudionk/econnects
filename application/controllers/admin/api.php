<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Api extends Site_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->url = $this->config->item("base_url") ."api/";
        $this->stop = false;
    }
    
    public function execute($url, $method = 'GET', $fields = [], $email = null, $senha = null){
        $APIKEY = ( isset( $_SERVER["HTTP_APIKEY"] ) ) ? $_SERVER["HTTP_APIKEY"] : app_get_token($email, $senha);

        $retorno = soap_curl([
            'url' => $url,
            'method' => $method,
            'fields' => $fields,
            'header' => [
                "accept: application/json",
                "Content-Type: application/json",
                "APIKEY: ". $APIKEY
            ]
        ]);

        if ($this->stop){
            echo "metodo: $method<br>";
            echo "<pre>";print_r($fields);echo "</pre>";
            echo "<pre>";print_r($retorno);echo "</pre>";
        }

        if (empty($retorno)) return;
        if (!empty($retorno["error"])) return;
        if (empty($retorno["response"])) return;
        $retornoJson = json_decode($retorno["response"]);
        if ($this->stop){
            print_r($retornoJson);
        }

        $sucesso = (isset($retornoJson->status) && empty($retornoJson->status));
        if (!$sucesso) {
            $sucesso = (isset($retornoJson->success) && empty($retornoJson->success));
        }

        if ($sucesso) {
            if(isset($retornoJson->message))
                $messagem = $retornoJson->message;
            else {
                if(isset($retornoJson->mensagem))
                    $messagem = $retornoJson->mensagem;
                else
                    $messagem = $retornoJson->erros;
            }

            if (!is_array($messagem))
                header('X-Error-Message: '. $messagem, true, 500);
            else {
                $messagem = implode("\n", $messagem);
                header('X-Error-Message: Falha no Processamento', true, 500);
            }

            $retorno["response"] = $messagem;
            if (isset($retornoJson->erros) || isset($retornoJson->errors)) {
                $retorno["response"] = [
                    'status' => false,
                    'mensagem' => $messagem,
                    'erros' => isset($retornoJson->erros) ? $retornoJson->erros : $retornoJson->errors
                ];
            }
        }

        return $retorno["response"];
    }

    private function extractEmail($json){
        $email = null;

        $json = json_decode($json);
        if (!empty($json->emailAPI)) {
            $email = $json->emailAPI;
            unset($json->emailAPI);
        }
        $json = json_encode($json);

        return (object) ['json' => $json, 'email' => $email];
    }

    public function equipamento($ean, $email = null){
        $retorno = $this->execute($this->url."equipamento?ean=". $ean);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function email($email){
        $email = urldecode($email);
        $result = false;
        $retorno = $this->execute($this->url."info/email?email=". $email ."&remetente=ti@sissolucoes.com.br");

        if (!empty($retorno)) {
            $response = json_decode($retorno);
            $result = !empty($response->{$email}->status) && $response->{$email}->code == '250';
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output($result);
        return;
    }

    public function enriqueceCPF($cpf, $produto_parceiro_id){
        $retorno = $this->execute($this->url."info?doc={$cpf}&produto_parceiro_id={$produto_parceiro_id}");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function enriqueceEAN($ean){
        $retorno = $this->execute($this->url."equipamento?ean=$ean");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function enriqueceModelo(){
        // $this->stop=true;
        $json = file_get_contents( "php://input" );
        $trat = $this->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;

        $retorno = $this->execute($this->url."equipamento/modelo", 'POST', $json, $email);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function cotacao_campos($produto_parceiro_id){
        $retorno = $this->execute($this->url."campos?produto_parceiro_id=$produto_parceiro_id");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function insereCotacao(){
        // $this->stop=true;
        $json = file_get_contents( "php://input" );
        $trat = $this->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;

        $retorno = $this->execute($this->url."cotacao", 'POST', $json, $email);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function cotacao_contratar(){
        // $this->stop=true;
        $json = file_get_contents( "php://input" );
        $trat = $this->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;

        $retorno = $this->execute($this->url."cotacao/contratar", 'POST', $json, $email);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function forma_pagamento_cotacao($cotacao_id){
        $retorno = $this->execute($this->url."pagamento/forma_pagamento_cotacao?cotacao_id=$cotacao_id");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function pagamento_campos($forma_pagamento_id){
        $retorno = $this->execute($this->url."pagamento/campos?forma_pagamento_id=$forma_pagamento_id");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function pagamento_pagar(){
        $json = file_get_contents( "php://input" );
        $trat = $this->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;
        $retorno = $this->execute($this->url."pagamento/pagar", "POST", $json, $email);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function calculo_premio($cotacao_id){
        $retorno = $this->execute($this->url."cotacao/calculo?cotacao_id=$cotacao_id");

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function apolice(){
        $json = file_get_contents( "php://input" );
        $trat = $this->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;
        $retorno = $this->execute($this->url."apolice", "PUT", $json, $email);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function cancelar(){
        // $this->stop=true;
        $json = file_get_contents( "php://input" );
        $trat = $this->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;
        $retorno = $this->execute($this->url."apolice/cancelar", 'POST', $json, $email);

        $this->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }
}
