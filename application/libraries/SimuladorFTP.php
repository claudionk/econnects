<?php

class SimuladorFTP {

    private $hostname = '/var/www/webroot/simulador-ftp';

    public function __construct($config = array()) {
        if($config){
            $this->initalize($config);
        }
    }

    private function initalize($config){
        return true;
    }

    public function connect($config){
        return $this->initalize($config);
    }

    public function close(){

    }

    public function download($remote, $local = null, $mode = "auto") {
        $fileContents = file_get_contents($remote);
        file_put_contents($local, $fileContents);
        return true;
        
    }

    public function upload($local, $remote, $mode = "auto", $permissions = null) {
        $fileContents = file_get_contents($local);
        $filePath = $this->hostname.$remote;
        $dirPath = str_replace(basename($filePath), "", $filePath);
        if(!file_exists($dirPath)){
            mkdir($dirPath, 0777, true);
        }
        file_put_contents($filePath, $fileContents);
        return true;
    }

    public function list_files($remote) {
        $output = array();

        $dirPath = $this->hostname.$remote;
        $scandir = scandir($dirPath);
        foreach($scandir as $fileName){
            if(!in_array($fileName, [".", ".."])){
                $output[] = $dirPath.$fileName;
            }
        }

        return $output;
    }


    function delete_file($remote) {
        unlink($this->hostname.$remote);
    }
    


}