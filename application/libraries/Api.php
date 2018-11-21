<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Curl Class
 *
 * Work with remote servers via cURL much easier than using the native PHP bindings.
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Philip Sturgeon
 * @license         http://philsturgeon.co.uk/code/dbad-license
 * @link			http://philsturgeon.co.uk/code/codeigniter-curl
 */
class Api {

	protected $_ci;
	protected $stop;
	protected $url;

	function __construct($url = '')
	{
		$this->_ci = & get_instance();
		log_message('debug', 'cURL Class Initialized');

		$this->url = $this->_ci->config->item("URL_sisconnects") ."api/";
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

        if (isset($retornoJson->status) && empty($retornoJson->status)) {
            if(isset($retornoJson->message))
                $messagem = $retornoJson->message;
            else
                $messagem = $retornoJson->mensagem;

            header('X-Error-Message: '. $messagem, true, 500);
            $retorno["response"] = $messagem;
            if (isset($retornoJson->erros)) {
                $retorno["response"] = [
                    'mensagem' => $messagem,
                    'erros' => $retornoJson->erros
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
        $retorno = $this->execute($this->_ci->url."equipamento?ean=". $ean);

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function email($email){
        $email = urldecode($email);
        $result = false;
        $retorno = $this->_ci->execute($this->_ci->url."info/email?email=". $email ."&remetente=ti@sissolucoes.com.br");

        if (!empty($retorno)) {
            $response = json_decode($retorno);
            $result = !empty($response->{$email}->status) && $response->{$email}->code == '250';
        }

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($result);
        return;
    }

    public function enriqueceCPF($cpf, $produto_parceiro_id){
        $retorno = $this->_ci->execute($this->_ci->url."info?doc={$cpf}&produto_parceiro_id={$produto_parceiro_id}");

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function enriqueceEAN($ean){
        $retorno = $this->_ci->execute($this->_ci->url."equipamento?ean=$ean");

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function enriqueceModelo(){
        // $this->_ci->stop=true;
        $json = file_get_contents( "php://input" );
        $trat = $this->_ci->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;

        $retorno = $this->_ci->execute($this->_ci->url."equipamento/modelo", 'POST', $json, $email);

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function cotacao_campos($produto_parceiro_id){
        $retorno = $this->_ci->execute($this->_ci->url."campos?produto_parceiro_id=$produto_parceiro_id");

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function insereCotacao(){
        // $this->_ci->stop=true;
        $json = file_get_contents( "php://input" );
        $trat = $this->_ci->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;

        $retorno = $this->_ci->execute($this->_ci->url."cotacao", 'POST', $json, $email);

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function cotacao_contratar(){
        // $this->_ci->stop=true;
        $json = file_get_contents( "php://input" );
        $trat = $this->_ci->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;

        $retorno = $this->_ci->execute($this->_ci->url."cotacao/contratar", 'POST', $json, $email);

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function forma_pagamento_cotacao($cotacao_id){
        $retorno = $this->_ci->execute($this->_ci->url."pagamento/forma_pagamento_cotacao?cotacao_id=$cotacao_id");

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function pagamento_campos($forma_pagamento_id){
        $retorno = $this->_ci->execute($this->_ci->url."pagamento/campos?forma_pagamento_id=$forma_pagamento_id");

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function pagamento_pagar(){
        $json = file_get_contents( "php://input" );
        $trat = $this->_ci->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;
        $retorno = $this->_ci->execute($this->_ci->url."pagamento/pagar", "POST", $json, $email);

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function calculo_premio($cotacao_id){
        $retorno = $this->_ci->execute($this->_ci->url."cotacao/calculo?cotacao_id=$cotacao_id");

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function apolice(){
        $json = file_get_contents( "php://input" );
        $trat = $this->_ci->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;
        $retorno = $this->_ci->execute($this->_ci->url."apolice", "PUT", $json, $email);

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

    public function cancelar(){
        // $this->_ci->stop=true;
        $json = file_get_contents( "php://input" );
        $trat = $this->_ci->extractEmail($json);
        $json = $trat->json;
        $email = $trat->email;
        $retorno = $this->_ci->execute($this->_ci->url."apolice/cancelar", 'POST', $json, $email);

        $this->_ci->output
            ->set_content_type('application/json')
            ->set_output($retorno);
        return;
    }

}
