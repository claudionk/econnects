<?php

/**
 * SendMessages - sendSMS
 * Created on 2011.06.21
 * (c) 2011 Lleida Networks Serveis Telematics
 * @author David Tapia
 * @version 1.2.1
 * 
 */
include_once('pdo.php');
include_once('../events.php');
include_once('../virtualsms.php');
include_once('../lib/constants.php');

if (!class_exists('SendMessages')) {

    class SendMessages extends Events {

        var $dbh;
        var $sms;
        var $connected = false;
        var $pendingMessages = 0;

        function __construct() {
            // Conecto la BBDD
            $this->dbh = new PDOConfig();

            // Configuro los eventos
            parent::Events();
                       
            // Creo el objeto para enviar SMS
            $this->sms = new VirtualSMS(SMS_USER, SMS_PASS);
            $this->sms->setEvents($this);
            
            // Configuro las propiedades del protocolo
            $this->sms->getProtocolProperties()->setHost('sms2.lleida.net');

            // Configuro las propiedades del usuario
            $this->sms->getUserProperties()->setCustomizedSender("Lleidanet"); 

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

        // En este ejemplo no se usan las TRANSACCIONES!
        function sendAll() {
            $row = array();
            $sql = 'select id,text,recipient from sms where status=0';
            try {
                $stmt = $this->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->execute();
                while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) && $this->connected) {
                    if(!empty ($row['recipient']) && !empty ($row['text']) && !empty ($row['id'])){
                        $this->sms->sendTextSMS($row['id'], $row['text'], $this->sms->getValidNumber($row['recipient']));
                        $this->pendingMessages++;
                    }
                    else{
                        if(!empty ($row['id'])){
                            // Marco el envio como invalido
                            $this->doUpdateStatus($row['id'], '-1');
                        }
                    }
                }
                $stmt = null;
            }
            catch (PDOException $e) {
                // En caso que falle la BBDD lo marco como una desconexion
                $this->connected = false;
            }
        }

        function doUpdateStatus($id, $status) {
            $sql = "update sms set status = :status where id = :id";
            try {
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(":id", $id);
                $stmt->bindParam(":status", $status);
                $stmt->execute();

                // Netejo
                $stmt = null;
            }
            catch (PDOException $e) {
                
            }
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
            
            // En este caso nos fiamos del parametro $idEnvio
            // pero se pueden añadir tantas paranoias como se quieran
            $this->doUpdateStatus($idEnvio, $status);
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
            // Por ejemplo pueden guardar los mensajes en una tabla
            // En este caso se hace un insert con dos fechas:
            // date contiene la fecha del insert
            // timestamp es la fecha del mensaje
            $sql = "insert into incomingmo set ";
            $sql .= "sms_user = :user, sms_pass = encode(:pass, '" . PASSPHRASE . "'), ";
            $sql .= "date = UNIX_TIMESTAMP(), timestamp = :timestamp, sender = :sender, recipient = :recipient, text=:text";
            try {
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(":user", SMS_USER);
                $stmt->bindParam(":pass", SMS_PASS);
                $stmt->bindParam(":timestamp", $timeStamp);
                $stmt->bindParam(":sender", $sender);
                $stmt->bindParam(":recipient", $recipient);
                $stmt->bindParam(":text", $text);
                $stmt->execute();

                // Netejo
                $stmt = null;
            }
            catch (PDOException $e) {
                
            }
        }

        function incomingMMS($idIncomingMMS, $timeStamp, $sender, $recipient) {
            parent::incomingMMS($idIncomingMMS, $timeStamp, $sender, $recipient);
            // Por ejemplo pueden guardar los mensajes en una tabla
            // En este caso se hace un insert con dos fechas:
            // date contiene la fecha del insert
            // timestamp es la fecha del mensaje
            $sql = "insert into incomingmms set ";
            $sql .= "sms_user = :user, sms_pass = encode(:pass, '" . PASSPHRASE . "'), ";
            $sql .= "date = UNIX_TIMESTAMP(), timestamp = :timestamp, sender = :sender, recipient = :recipient";
            try {
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(":user", SMS_USER);
                $stmt->bindParam(":pass", SMS_PASS);
                $stmt->bindParam(":timestamp", $timeStamp);
                $stmt->bindParam(":sender", $sender);
                $stmt->bindParam(":recipient", $recipient);
                $stmt->execute();

                // Netejo
                $stmt = null;
            }
            catch (PDOException $e) {
                
            }
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

/*
 * 
 * CREATE TABLE  `send`.`sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(900) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `recipient` varchar(20) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
 * 
 */
?>