<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Powerbiz
{
    private $_ci;

    public function __construct($params = array())
    {
        $this->_ci = &get_instance();
        $this->_ci->load->model('produto_parceiro_servico_log_model', 'produto_parceiro_servico_log');

        foreach ($params as $property => $value) {
            $this->$property = $value;
        }
    }

     /**
     * Integração com a PowerBiz para Emissão
     * @param $mensagem
     * @param $engine
     * @return array
     */
    public function powerbiz_new($engine, $mensagem)
    {
        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;
        $Antes = new DateTime();

        $basic = base64_encode( $engine['servico_usuario'] .':'. $engine['servico_senha'] );
        $data = [
            'order' => [
                'sku' => $mensagem['param'],
            ]
        ];

        $config = array(
            'urlCurl'    => $engine['servico_url'] ."new",
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
        $data_log["idConsulta"] = $mensagem['apolice_movimentacao_id'];

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
     * Integração com a PowerBiz para Cancelamento
     * @param $mensagem
     * @param $engine
     * @return array
     */
    public function powerbiz_cancel($engine, $mensagem)
    {
        $response                   = array();
        $response['retorno']        = "";
        $response['retorno_codigo'] = "";
        $response['status']         = false;
        $brid = '';
        $Antes = new DateTime();
        $basic = base64_encode( $engine['servico_usuario'] .':'. $engine['servico_senha'] );

        // Consulta o log da emissão
        $logs = $this->_ci->produto_parceiro_servico_log
            ->filter_by_produto_parceiro_servico_id( $mensagem['produto_parceiro_servico_id'] )
            ->filter_by_idConsulta( $mensagem['apolice_movimentacao_id'] )
            ->filter_by_consulta('new')
            ->get_all();

        foreach ($logs as $l) {
            $ret = json_decode($l['retorno']);
            if (!empty($ret->code))
            {
                // sucesso
                $suc = ( (int)$ret->code == 0);
                if ( $suc )
                {
                    if ( !empty($ret->data->order->brid))
                    {
                        $brid = $ret->data->order->brid;
                        break;
                    }
                }
            }
        }

        if (empty($brid))
        {
            return $response;
        }

        $data = [
            'order' => [
                'sku' => $mensagem['param'],
                'brid' => $brid
            ]
        ];

        $config = array(
            'urlCurl'    => $engine['servico_url'] ."cancel",
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

        // Gera Log
        $Depois = new DateTime();
        $data_log["produto_parceiro_servico_id"] = $mensagem['produto_parceiro_servico_id'];
        $data_log["url"] = $config['urlCurl'];
        $data_log["consulta"] = "cancel";
        $data_log["retorno"] = $resp;
        $data_log["time_envio"] = $Antes->format( "H:i:s" );
        $data_log["time_retorno"] = $Depois->format( "H:i:s" );
        $data_log["parametros"] = $config['fieldsCurl'];
        $data_log["data_log"] = $Antes->format( "Y-m-d H:i:s" );
        $data_log["ip"] = ( isset( $_SERVER[ "REMOTE_ADDR" ] ) ? $_SERVER[ "REMOTE_ADDR" ] : "" );
        $data_log["idConsulta"] = $mensagem['apolice_movimentacao_id'];

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
                    // $data_log["idConsulta"] = $resp['data']['order']['brid'];
                } else {
                    $response['retorno']        = $resp['message'];
                    $response['retorno_codigo'] = $resp['code'];
                }

            }

        }

        $this->_ci->produto_parceiro_servico_log->insLog( $data_log["produto_parceiro_servico_id"], $data_log );
        return $response;

    }

}
