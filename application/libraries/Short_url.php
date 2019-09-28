<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Short URL
 */
class Short_url
{
    //Const KEY_GOOGLE = 'AIzaSyAs0yKw8RGL8QutAZ5nlzAwWIrd5kRfofM';
    Const KEY_GOOGLE = 'AIzaSyA-EDFSRLZ_i9upq_ANhHG0UD1EJ_mBHt8';

    public static function shorter($url)
    {

        $httpHeader = array(
            "Authorization: Basic dXN1X3Npc3RlbWE6c2lzMTIz", // id_usuario = 10058
            "cache-control: no-cache",
        );

        $CI =& get_instance();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $CI->config->item("URL_SGS") .'v1/api/encurtador/gerar',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('url'=>$url),
            CURLOPT_HTTPHEADER => $httpHeader
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl , CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return FALSE;
        } else {

            if($httpCode != 200){
                return FALSE;
            }
            else
            {
                $response = json_decode($response, true);
                if(isset($response['message'])){
                    return $response['message'];
                }else{
                    return FALSE;
                }
            }

        }

        return $url;
    }

    public static function shorterGoogle($url)
    {
        $httpHeader = array(
            "accept: application/json",
            "content-type: application/json",
            "cache-control: no-cache",
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.googleapis.com/urlshortener/v1/url?key=' . self::KEY_GOOGLE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "{\"longUrl\": \"{$url}\"}",
            CURLOPT_HTTPHEADER => $httpHeader
        ));


        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl , CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);


        if ($error) {
            return FALSE;
        } else {

            if($httpCode != 200){
                return FALSE;
            }
            else
            {
                $response = json_decode($response, true);
                if(isset($response['id'])){
                    return $response['id'];
                }else{
                    return FALSE;
                }
            }

        }

        return $url;
    }

}
