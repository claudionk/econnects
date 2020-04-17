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

}
