<?php
/**
 * bulksend.php
 * @author David Tapia (c) 2010 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 2.0
 */

if(!class_exists('BulkSend')){
    define('VSMS_BULK_VERSION', '2.0');
    class BulkSend {
        var $id = 0;
        var $totalElements = 0;
        var $failedsRecipients = array();
        
        function BulkSend($n, $id) {
            $this->id = $id;
            $this->totalElements = $n;
        }

        function addFailRecipient($failedRecipient) {
            $this->failedsRecipients[] = $failedRecipient;
        }

        function getFailRecipientAt($i) {
            // El indice debe de ser 0..length
            if(array_key_exists($i, $this->failedsRecipients)) return $this->failedsRecipients[$i];
            else return '';
        }

        function isEmptyFailRecipient(){
            if(count($this->failedsRecipients) > 0) return false;
            else return true;
        }

        function isAllSendFailed(){
            return (count($this->failedsRecipients) == $this->totalElements);
        }

        function getFailedsRecipients() {
            return $this->failedsRecipients;
        }

        function getFailedsRecipientsToStrings() {
            if(!isset($this->failedsRecipients) || count($this->failedsRecipients) == 0) return '';
            $r = '';
            foreach ($this->failedsRecipients as $f){
                $r .= ' '.$f;
            }
            return $r;
        }

        function getSendElements() {
            return $this->totalElements - count($this->failedsRecipients);
        }
        
        function getTotal(){
            return $this->totalElements;
        }
        
        function getID(){
            return $this->id;
        }
    }
} // if(!class_exists('BulkSend'))
?>