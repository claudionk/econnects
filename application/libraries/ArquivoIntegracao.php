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

            
            $files["filename"] = $fileName;
            $files["content"] = base64_encode($content);

            $fields["Extensao"] = pathinfo($fileName, PATHINFO_EXTENSION);
            $fields["Folder"] = self::createFolderPath($integracao);
            
            $responseData = self::callAPI("upload", [
                "files" => [$files],
                "fields" => [$fields]
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
            return self::saveFile($integracao, basename($filePath), file_get_contents($filePath));
        }else{
            return false;
        }
    }

    public static function openFile($integracao, $fileName){

        try {

            $content = self::downloadFile($integracao, $fileName);
            $arquivoIntegracao = new ArquivoIntegracao();
            $arquivoIntegracao->aLine = explode(PHP_EOL, base64_decode($content));
            return $arquivoIntegracao;

        } catch (Exception $ex) {
            return null;

        }

    }

    private static function downloadFile($integracao, $fileName){
        try {

            $data = array();
            $data["Name"] = $fileName;
            $data["Folder"] = self::createFolderPath($integracao);

            $responseData = self::callAPI("openFile", json_encode($data));

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

            return $responseData["data"];

        } catch (Exception $ex) {
            return null;

        }
    }

    public static function downloadFileToTMP($integracao, $fileName){
        $filePath   = self::createIntegracaoTmpPath($integracao, $fileName);
        $content    = self::downloadFile($integracao, $fileName);
        file_put_contents($filePath, $content);
        return $filePath;
    }

    private static function callAPI($methodName, $inputData){
        $CI = &get_instance();
        $CI->load->library("SoapCurl");
        $soapCurl = new SoapCurl();
        if($methodName == "upload"){

		    $fields = self::parseDataUpload(array("Files" => $inputData["fields"]), '', false);
            $files = self::parseDataUpload(array("Files" => $inputData["files"]), '', true);
        
            $boundary = uniqid();
            $delimiter = '-------------' . $boundary;
            $post_data = SoapCurl::build_data_files($boundary, $fields, $files);

            $soapCurl->putApiHeader("Accept", 'multipart/form-data');
            $soapCurl->putApiHeader("Content-Type", "multipart/form-data; boundary=" . $delimiter);
            $inputData = $post_data;
        }
        $outputSoapCurl = $soapCurl->getAPI("uploads/$methodName", "POST", $inputData, null);

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

    private static function parseDataUpload($a, $str = '', $returnArray = false)
    {
    	if (empty($a)){
			return [];
		}

    	$r = [];
        foreach ($a as $name => $_data)
        {
            if (is_array($_data)) {

                if ( empty($str) ) {
					$x = $name;
                } else {
                	$x = $str."[$name]";
                }

                $z = self::parseDataUpload($_data, $x, $returnArray);
                $r = array_merge($z, $r);
            } else {
            	if (!$returnArray){
                	$r[$str."[$name]"] = $_data;
            	} else {
                	$r[$str][$name] = $_data;
            	}
            }
        }
        return $r;
    }

    public static function createIntegracaoTmpPath($integracao, $fileName = null){
        $path = app_tmp_dir('integracao', 'uploads') . "{$integracao['integracao_id']}/{$integracao['tipo']}";
        if($fileName){
            $path .= "/".$fileName;
        }
        return $path;
    }


}
