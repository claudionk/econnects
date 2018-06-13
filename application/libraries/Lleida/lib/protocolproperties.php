<?php
/**
 * protocolproperties.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.3
 *
 * Fast server, active options;
 * Slow server, deactivate options!
 *
 * true to active, false to deactivate
 *
 */
include_once('logger.php');

if(!class_exists('ProtocolProperties')){
    define('VSMS_PROPERTIES_VERSION','1.3');

    class ProtocolProperties {
        var $log;
        var $objID;
        var $bDebug = true;
        var $m_Host = 'sms.lleida.net';
        var $m_Port = 2048;

        // ACK's
        var $m_ackAcuses = true;
        var $m_ackChecker = true;
        var $m_ackIncoming = true;

        function ProtocolProperties(){
            $this->objID = $this->getObjectID(12);
            if ($this->log == null) {
                $this->log = new Logger();
            }
        }
        
        private function getObjectID($len) {
            // Only for debugger info
            if (!isset($this->objID) || $this->objID == '') {
                return substr(md5(rand(0, 999)), 0, $len);
            }
            else {
                return $this->objID;
            }
        }
        
        function debug($log){
             if ($this->bDebug){
                 $this->log->debug('[ Protocol ] ' . $this->objID . $log);
             }
        }

        function setHost($newHost){
            $newHost = trim($newHost);
            if(!empty($newHost)){
                $this->m_Host = $newHost;
            }
        }

        function setPort($newPort){
            if($newPort < 0 || $newPort > 32767){
                $this->m_Port = 2048;
            }
            else{
                $this->m_Port = $newPort;
            }
        }

        function setDebugMode($deb){
            if(is_bool($deb)) $this->bDebug = $deb;
            else $this->bDebug = true;
        }

        function getDebugMode(){
            return $this->debug;
        }

        function getHost(){
            return $this->m_Host;
        }

        function getPort(){
            return $this->m_Port;
        }

        function activeAllAck(){
            $this->m_ackAcuses = true;
            $this->m_ackChecker = true;
            $this->m_ackIncoming = true;
        }

        function deactiveAllAck(){
            $this->m_ackAcuses = false;
            $this->m_ackChecker = false;
            $this->m_ackIncoming = false;
        }

        function setAcusesAck($bln){
            if(is_bool($bln)) $this->m_ackAcuses = $bln;
            else $this->m_ackAcuses = false;
        }

        function setCheckerAck($bln){
            if(is_bool($bln)) $this->m_ackChecker = $bln;
            else $this->m_ackChecker = false;
        }

        function setIncomingAck($bln){
            if(is_bool($bln)) $this->m_ackIncoming = $bln;
            else $this->m_ackIncoming = false;
        }

        function isActivatedAcusesAck(){
            return $this->m_ackAcuses;
        }

        function isActivatedCheckerAck(){
            return $this->m_ackChecker;
        }

        function isActivatedIncomingAck(){
            return $this->m_ackIncoming;
        }
    }
} // if(!class_exists('ProtocolProperties'))
?>