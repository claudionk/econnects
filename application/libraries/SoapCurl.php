<?php defined('BASEPATH') OR exit('No direct script access allowed');

class SoapCurl
{
    public $info;
    public $header;
    public $error;
    public $httpCode;
    private $urlCurl;
    private $methodCurl;
    private $fieldsCurl;
    private $headerCurl;
    private $timeout;

    public function __construct($config = null)
    {
        if(!empty($config)){
            $this->url = $config['urlCurl'];
            $this->method = $config['methodCurl'];
            $this->fields = emptyor($config['fieldsCurl'], null);
            $this->header = $config['headerCurl'];
            $this->timeout = !empty($config['timeout']) ? $config['timeout'] : 30;
            $this->setData();
        }

        $this->_ci = & get_instance();
    }

    public function setData()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_POSTFIELDS => $this->fields,
            CURLOPT_HTTPHEADER => $this->header
        ));
        $this->header = curl_exec($curl);
        $this->info = curl_getinfo($curl);
        $this->httpCode = curl_getinfo($curl , CURLINFO_HTTP_CODE);
        $this->error = curl_error($curl);
        curl_close($curl);
    }

    public function getFiletime()
    {
        return $this->info['filetime'];
    }

    public function getHttpStatus()
    {
        return $this->info['filetime'];
    }

    public function getXML()
    {
        $xml = simplexml_load_string((string) $this->header);
        if( $xml ){
            $xml = $xml->xpath('//soap:Body') ;
        }
        else{
            $xml = null;
        }
        return !empty( $xml[0] ) ? $xml[0]  : null ;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getHttpCode(){
        return $this->httpCode;
    }

    public function getAPI($endPoint, $method, $params = null, $timeout = null, $v3 = false)
    {

	    if (empty($v3)) {
            $url = $this->_ci->config->item("URL_SGS") ."v1/api/". $endPoint;
	    } else {
            $url = $this->_ci->config->item("URL_portal") ."PSFase3/sis-api-v1/api/". $endPoint;
	    }

	    $retorno = ['status' => false, 'erro' => '', 'newTry' => false];
	    $config = array(
			    'urlCurl'    => $url,
			    'methodCurl' => $method,
			    'headerCurl' => array(
				    "accept: application/json",
				    "Authorization: Basic dXN1X3Npc3RlbWE6c2lzMTIz" ,
				    "content-type: application/json",
				    "cache-control: no-cache",
				    ),
			   );

	    if (!empty($params)) {
		    $config['fieldsCurl'] = $params;
	    }

	    if (!empty($timeout)) {
		    $config['timeout'] = $timeout;
	    }

	    $this->soap = new SoapCurl($config);
	    $err        = $this->soap->getError();

	    if ($err) {
		    $retorno['erro'] = $err;
	    } else {
		    $info                = $this->soap->getInfo();
		    $httpCode            = $this->soap->getHttpCode();
		    $response            = $this->soap->getHeader();
		    $retorno['response'] = json_decode($response, true);
		    // $retorno['info'] = $info;

		    if (empty($retorno['response'])) {

			    $retorno['newTry']   = true;
			    $retorno['response'] = $response;

		    } elseif (isset($retorno['response']['status']) && empty($retorno['response']['status'])) {

			    if (!empty($retorno['response']['reason']['faultstring'])) {
				    $retorno['erro'] = $retorno['response']['reason']['faultstring'];
			    } else {
				    $retorno['erro'] = $retorno['response']["message"];
			    }

		    } else {

			    $retorno['status'] = true;

		    }

	    }

	    return $retorno;
    }
}
