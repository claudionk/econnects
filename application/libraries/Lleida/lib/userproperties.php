<?php
/**
 * userproperties.php
 * @author David Tapia (c) 2010 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.6
 */
if(!class_exists('UserProperties')){
    define('VSMS_UPROPERTIES_VERSION','1.6');

    class UserProperties {
        var $m_User = '';
        var $m_Pass = '';
        var $m_Credit = 0;
        var $m_MailDeliveryReceipt = 'INTERNALID';
        var $m_CustomizedSender = '';
        var $m_AllowAnswer = true;
        var $m_Lang = 'ES';
        var $m_Type = 'D';
        var $m_CertName = '';
        var $m_CertNameID = '';

        function UserProperties($user, $passwd){
            $this->setDefaultProperties($user, $passwd);
        }

        function setUser($newUser){
            $newUser = trim($newUser);
            if(!empty($newUser)){
                $this->m_User = $newUser;
            }
        }

        function setPassword($newPass){
            $newPass = trim($newPass);
            if(!empty($newPass)){
                $this->m_Pass = $newPass;
            }
        }

        function setLang($lang){
            $ul = strtoupper($lang);
            switch (true) {
                case $ul === "ES":
                case $ul === "CA":
                case $ul === "EN":
                case $ul === "FR":
                case $ul === "DE":
                case $ul === "IT":
                case $ul === "NL":
                case $ul === "PT":
                case $ul === "PL":
                case $ul === "SE":
                    $this->m_Lang = $ul;
                    break;
                default :
                    $this->m_Lang = 'ES';
                    break;
            }            
        }

        function setAcuseType($type){
            $ut = strtoupper($type);
            switch (true) {
                case $ut === "D":
                case $ut === "T":
                    $this->m_Type = $ut;
                    break;
                default :
                    $this->m_Type = 'D';
                    break;
            }            
        }

        function setCredit($credit){
            $this->m_Credit = $credit;
        }
        
        // El nombre del titular del certificado
        function setCertName($name){
            $this->m_CertName = str_replace(' ', '%', str_replace(array("\n", "\r", "'", "(", ")"), "", trim($name)));
        }
        
        // El DNI, CIF o ID de la empresa
        function setCertNameID($nid){
            $this->m_CertNameID = str_replace(' ', '%', str_replace(array("\n", "\r", "'", "(", ")"), "", trim($nid)));
        }

        function setMailDeliveryReceipt($newMailDeliveryReceipt){
            $this->m_MailDeliveryReceipt = $this->getValidEmail($newMailDeliveryReceipt);
        }

        function setCustomizedSender($newCustomizedSender){
            $nCustomizedSender = str_replace(array("\n", "\r", "'", ".", "(", ")"), "", trim($newCustomizedSender));
            //$nCustomizedSender = trim($this->replaceChars($newCustomizedSender));
            if($this->isInternationalNumberFormat($nCustomizedSender)){
                if($nCustomizedSender{0} == '+'){
                    // Quito el mas
                    $nCustomizedSender = substr($nCustomizedSender, 1, strlen($nCustomizedSender) - 1);
                }
                // Quito todo lo que exceda de 15
                if(strlen($nCustomizedSender) > 15){
                    $nCustomizedSender = substr($nCustomizedSender, 0, 15);
                }
            }
            else{
                // Es un texto
                // Los espacios se deben de escapar por el simbolo %
                $nCustomizedSender = str_replace(' ', '%', $nCustomizedSender);
                if(strlen($nCustomizedSender) > 11){
                    $nCustomizedSender = substr($nCustomizedSender, 0, 11);
                }
            }
            $this->m_CustomizedSender = $nCustomizedSender;
        }

        function setAllowAnswer($newAllowAnswer){
            if(is_bool($newAllowAnswer)) $this->m_AllowAnswer = $newAllowAnswer;
            else $this->m_AllowAnswer = true;
        }

        function setDefaultProperties($user, $passwd){
            if(strlen($user) > 0){
                $this->m_User = trim($user);
            }
            else{
                $this->m_User = '';
            }

            if(strlen($passwd) > 0){
                $this->m_Pass = trim($passwd);
            }
            else{
                $this->m_Pass = '';
            }

            $this->m_Credit = 0;
            $this->m_MailDeliveryReceipt = 'INTERNALID';
            $this->m_CustomizedSender = '';
            $this->m_CertName = '';
            $this->m_CertNameID = '';
        }

        function getUser(){
            return $this->m_User;
        }

        function getPassword(){
            return $this->m_Pass;
        }

        function getCredit(){
            return $this->m_Credit;
        }
        
        function getCertName(){
            return $this->m_CertName;
        }
        
        function getCertNameID(){
            return $this->m_CertNameID;
        }

        function getMailDeliveryReceipt(){
            return $this->m_MailDeliveryReceipt;
        }

        function getCustomizedSender(){
            return $this->m_CustomizedSender;
        }

        function getLang(){
            return $this->m_Lang;
        }

        function getAcuseType(){
            return $this->m_Type;
        }

        function getValidEmail($email) {
            $email = str_replace(array("\n", "\r", "'", " ", "(", ")"), "", trim($email));
            if (strtoupper($email) == 'INTERNAL') {
                return 'INTERNALID';
            }

            if (strtoupper($email) == 'INTERNALID') {
                return 'INTERNALID';
            }

            if (function_exists('filter_var')) { 
                // //Introduced in PHP 5.2
                if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
                    return 'INTERNALID'; // No es un email
                }
            }
            else {
                $r = preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
                if (!$r || $r === FALSE) {
                    return 'INTERNALID';
                }
            }
            return $email;
        }
        
        function isAllowAnswer(){
            return $this->m_AllowAnswer;
        }

        function isCertifiedSMS(){
            return $this->m_Cert;
        }

        // ================================================
        // PRIVATE methods, use under your responsibility
        // ================================================

        private function isInternationalNumberFormat($str){
            return(preg_match('#\+?[0-9]#', $str) == 1);
        }
    }
} // if(!class_exists('ProtocolProperties'))
?>
