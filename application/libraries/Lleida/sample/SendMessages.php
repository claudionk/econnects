<?php

/**
 * SendMessages
 * Created on 2012.01.11
 * (c) 2011 Lleida Networks Serveis Telematics
 * @author David Tapia
 * @version 1.2.1
 * 
 */

include_once('../events.php');
include_once('../virtualsms.php');
include_once('../lib/constants.php');

if (!class_exists('SendMessages')) {

    class SendMessages extends Events {

        var $sms;
        var $connected = false;
        var $pendingMessages = 0;

        function __construct() {
            // Configuro los eventos
            parent::Events();
                       
            // Creo el objeto para enviar SMS
            $this->sms = new VirtualSMS(SMS_USER, SMS_PASS);
            $this->sms->setEvents($this);
            
            // Configuro las propiedades del protocolo
            $this->sms->getProtocolProperties()->setHost('sms2.lleida.net');

            // Configuro las propiedades del usuario
            $this->sms->getUserProperties()->setCustomizedSender('Lleidanet');

            // Conecto
            $this->sms->connect();
            
            // Configuro los acuses
            $this->sms->acuseOn('INTERNAL');
            
            // O bien puedo configurar el acuse certificado
            //$this->sms->acuseOnCertifiedSMS('noreply@lleida.net');
        }

        // Funcion para consultar si esta conectado
        function isConnected() {
            // Controlo que la session este conectada y que no haya saltado 
            // ningun evento de desconexion
            return $this->connected && $this->sms->isLogged();
        }

        function sendOneSMS($id, $text, $dst, $fecha='') {
            $this->sms->sendTextSMS($id, $text, $this->sms->getValidNumber($dst), $fecha);
            $this->pendingMessages++;
        }
        
        function sendMultipleSMS($id, $text, $dst, $fecha='') {
            $this->sms->sendTextSMS($id, $text, $dst, $fecha);
            // Solo se recibe un evento reply!
            $this->pendingMessages++;
        }
        
        function sendOneMMS($id, $subject, $txt, $dst, $mimeType, $file) {
            $fileData = $this->sms->getFileContentBase64($file);
            $this->sms->sendMMS($id, $subject, $txt, $this->sms->getValidNumber($dst), $mimeType, $fileData);
            $this->pendingMessages++;
        }
        
        function countPendingMessages() {
            return $this->pendingMessages;
        }

        function disconnect() {
            $this->sms->disconnect();
        }

        // ---------------------------------------------------------------------
        // Eventos relacionados con el Socket y la session del usuario
        // ---------------------------------------------------------------------
        function connected() {
            parent::connected();
            $this->connected = true;
        }

        function disconnected() {
            parent::disconnected();
            $this->connected = false;
        }

        function connectionError($errCode, $errMsg) {
            parent::connectionError($errCode, $errMsg);
            $this->connected = false;
        }

        function noCredit() {
            parent::noCredit();
            // En este caso se decide parar de enviar SMS al recibir el error del saldo
            $this->connected = false;
        }

        // ---------------------------------------------------------------------
        // Eventos de repuesta a los comandos de envio
        // ---------------------------------------------------------------------        
        function reply($idEnvio, $status, $data=null) {
            parent::reply($idEnvio, $status, $data);
            
            $this->pendingMessages--;

            // Si se implementan las transacciones se pueden tratar los 
            // destinos invalidos en $data
            // En caso que el envio sea transacional solo se recibe un evento
            // reply referente al total de la transaccion.
        }
        
        // ---------------------------------------------------------------------
        // Eventos para los ACUSES
        // ---------------------------------------------------------------------
        function deliveryReceipt($timeStamp, $sendTimeStamp, $recipient, $text, $status, $id='') {
            parent::deliveryReceipt($timeStamp, $sendTimeStamp, $recipient, $text, $status, $id);
        }

        function deliveryReceiptMMS($timeStamp, $sendTimeStamp, $recipient, $status, $id='') {
            parent::deliveryReceiptMMS($timeStamp, $sendTimeStamp, $recipient, $status, $id);
        }

        // ---------------------------------------------------------------------
        // Eventos para los mensajes entrantes (los que recibe el usuario)
        // ---------------------------------------------------------------------
        function incomingMo($idIncomingMo, $timeStamp, $sender, $recipient, $text) {
            parent::incomingMo($idIncomingMo, $timeStamp, $sender, $recipient, $text);
        }

        function incomingMMS($idIncomingMMS, $timeStamp, $sender, $recipient) {
            parent::incomingMMS($idIncomingMMS, $timeStamp, $sender, $recipient);
        }

        // ---------------------------------------------------------------------
        // Evento para obtener la tarifa segun el numero de destino
        // ---------------------------------------------------------------------
        // Para lanzar este evento:
        // $this->sms->getPrice($num);
        function price($price) {
            parent::price($price);
        }

        // ---------------------------------------------------------------------
        // Eventos relacionados con los CHECKER
        // ---------------------------------------------------------------------
        // El servicio de CHECKER puede estar fuera de servicio debido a 
        // mantenimiento de los diversos proveedores que existen
        function serviceUnavailable() {
            parent::serviceUnavailable();
        }

        function checkall($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc) {
            parent::checkall($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc);
        }

        function checknetwork($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc) {
            parent::checknetwork($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc);
        }

        // Para lanzar este evento:
        // $this->sms->getOperatorInfo($num);
        function operatorInfo($phoneOrPrefix, $MCC, $MNC) {
            parent::operatorInfo($phoneOrPrefix, $MCC, $MNC);
        }

    }

} // if(!class_exists('SendMessages'))
?>