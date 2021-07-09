<?php

class ArquivoIntegracao {

    private $lineIndex = -1;
    private $aLine = array();
    private static $baseDirPath = "integracao/";

    private static function createFolderPath($integracao){
        $filePath = self::$baseDirPath.$integracao["integracao_id"]."/".$integracao["tipo"];
        return $filePath;
    }

    public static function saveFile($integracao, $fileName, $content){
        try {

            $data = array();
            $data["Name"] = $fileName;
            $data["Extension"] = pathinfo($fileName, PATHINFO_EXTENSION);
            $data["Folder"] = self::createFolderPath($integracao);
            $data["Content"] = $content;
            $responseData = self::callAPI("upload", [
                "Files" => [$data]
            ]);

            if(empty($responseData["status"])){
                if(!empty($responseData["message"])) {
                    throw new Exception($responseData["message"]);
                } else {
                    throw new Exception("Erro inesperado");
                }
            }

            return true;

        } catch (Exception $ex) {

            return false;

        }

    }

    public static function uploadFile($integracao, $filePath){
        if(file_exists($filePath)){
            return self::saveFile($integracao, basename($filePath), base64_encode(file_get_contents($filePath)));
        }else{
            return false;
        }
    }

    public static function openFile($integracao, $fileName){

        try {

            $data = array();
            $data["Name"] = $fileName;
            $data["Folder"] = self::createFolderPath($integracao);

            $responseData = self::callAPI("openFile", $data);

            if(empty($responseData["status"])){
                if(!empty($responseData["message"])) {
                    throw new Exception($responseData["message"]);
                } else {
                    throw new Exception("Erro inesperado");
                }
            }

            if(empty($responseData["data"])){
                throw new Exception("Campo esperado: data");
            }

            $arquivoIntegracao = new ArquivoIntegracao();
            $arquivoIntegracao->aLine = explode(PHP_EOL, base64_decode($responseData["data"]));
            return $arquivoIntegracao;

        } catch (Exception $ex) {
            return null;

        }

    }

    private static function callAPI($methodName, $inputData){
        $CI = &get_instance();
        $CI->load->library("SoapCurl");
        $soapCurl = new SoapCurl();
        $outputSoapCurl = $soapCurl->getAPI("uploads/$methodName", "POST", json_encode($inputData), null, true);
        var_dump($outputSoapCurl);
        print_r(json_encode($inputData));

        if(!empty($outputSoapCurl["error"])){
            throw new Exception($outputSoapCurl["error"]);
        }

        if(empty($outputSoapCurl["response"])){
            throw new Exception("Campo esperado: response");
        }

        return $outputSoapCurl["response"];
    }

    public function nextLine(){
        $this->lineIndex++;
        if($this->lineIndex < sizeof($this->aLine)){            
            return true;
        }
        return false;
    }

    public function getLine(){
        return $this->aLine[$this->lineIndex];
    }

}
