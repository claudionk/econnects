<?php
/**
 * logger.php
 * @author David Tapia (c) 2009 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.9.2
 */

if(!class_exists('Logger')){
    define('VSMS_LOGGER_VERSION', '1.9.2');
    // Se puede definir una ruta
    define('LOG_RUTE', '');
    class Logger{
        var $file;
        function Logger($filename='vsms'){
            if(!preg_match('#\.log#', $filename)) $this->file = LOG_RUTE.$filename.'.log';
            else $this->file = LOG_RUTE.$filename;
        }

        function debug($log){
            $log = $this->__adds($log);
            if(strpos(strtoupper($log), "LOGIN") === false){
                // No contiene la palabra LOGIN
                if(strpos(strtoupper($log), " MMSMSG ") == true){
                    // Quito los ficheros MMS del log
                    $log = substr($log, 0, 44);
                    // Hay un problema si el contador pasa de los tres digitos
                    // Alguna idea??
                    $log = $log." [...] Logger version ".VSMS_LOGGER_VERSION;
                    $log ="\n".' ['.date('Y-m-d H:i:s').'] '.$log;
                }
                else{
                    $log ="\n".' ['.date('Y-m-d H:i:s').'] '.$log;
                }
            }
            else{
                // Quito el passwd del comando login
                $cmd = explode(" ", $log);
                $log = '';
                for($i = 0; $i < 8; $i++){
                    $log .= $cmd[$i]." ";
                }
                //$log = substr($log, 0, 43);
                $log = $log."[...] Logger version ".VSMS_LOGGER_VERSION;
                $log ="\n".' ['.date('Y-m-d H:i:s').'] '.$log;
            }
            $fp = fopen($this->file, 'a+');
            fwrite($fp, $log);
            fclose($fp);
        }

        function __adds($text){
            if(!get_magic_quotes_gpc()) return addslashes(trim($text));
            else return trim($text);
        }

        function __strip($text){
            if(!get_magic_quotes_gpc()) return stripslashes(trim($text));
            else return trim($text);
        }
    }
} // if(!class_exists('logger'))
?>
