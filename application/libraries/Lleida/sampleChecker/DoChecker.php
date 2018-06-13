<?php

/**
 * DoChecker
 * Created on 2012.01.25
 * (c) 2012 Lleida Networks Serveis Telematics
 * @author David Tapia
 * @version 1.0
 * 
 */

/*
 * 
 * Tabla de valores para el rCode
-5 & Pendiente de RCHECKXXX (solo es de este ejemplo)
-4 & Fuera de servicio
-3 & Pendiente (solo es de este ejemplo)
-2 & Número inválido \\
-1 & No se puede resolver el estado del número\\
0  & Número correcto\\
1  & Formato inválido de la consulta\\
2  & Se ha superado el tiempo máximo de espera sin obtener respuesta\\
3  & El número no existe\\
4  & El número no permite el servicio de SMS\\
5  & Llamadas restringidas\\
6  & El terminal del número no esta bien configurado\\
7  & El número esta en roaming o no permite la entrada de SMS\\
8  & Error de la operadora\\
9  & El número esta apagado o fuera de cobertura\\
11 & No tiene saldo para hacer la consulta\\
12 & Error desconocido\\
13 & Pendiente de validación\\

 */

include_once('pdo.php');
include_once('../events.php');
include_once('../virtualsms.php');
include_once('../lib/constants.php');

if (!class_exists('DoChecker')) {

    class DoChecker extends Events {

        var $dbh;
        var $sms;
        var $connected = false;
        var $pendingCheckers = 0;

        function __construct() {
            // Conecto la BBDD
            $this->dbh = new PDOConfig();

            // Configuro los eventos
            parent::Events();
                       
            // Creo el objeto para enviar SMS
            $this->sms = new VirtualSMS(SMS_USER, SMS_PASS);
            $this->sms->setEvents($this);
            
            // Configuro las propiedades del protocolo
            $this->sms->getProtocolProperties()->setHost('sms3.lleida.net');

            // Conecto
            $this->sms->connect();
        }
        
        function readSocket(){
            $this->sms->readSocket();
        }

        // Funcion para consultar si esta conectado
        function isConnected() {
            // Controlo que la session este conectada y que no haya saltado 
            // ningun evento de desconexion
            return $this->connected && $this->sms->isLogged();
        }

        function doCheckall() {
            $row = array();
            $sql = 'select id,recipient from sms where rcode=-3';
            try {
                $stmt = $this->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->execute();
                while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) && $this->connected) {
                    if(!empty ($row['recipient']) && !empty ($row['id'])){
                        
                        // Marco el check con un error
                        $this->doUpdateRCodeByID($row['id'], '-1');
                        
                        $this->sms->checkall($this->sms->getValidNumber($row['recipient']), $row['id']);
                        
                        $this->pendingCheckers++;
                    }
                    else{
                        if(!empty ($row['id'])){
                            // Marco el envio como invalido
                            $this->doUpdateRCodeByID($row['id'], '-2');
                        }
                    }
                }
                $stmt = null;
            }
            catch (PDOException $e) {
                // En caso que falle la BBDD lo marco como una desconexion
                $this->connected = false;
                var_dump($e);
            }
        }

        function doUpdateRCodeByID($id, $rcode) {
            $sql = "update sms set rcode = :rcode where id = :id";
            try {
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(":id", $id);
                $stmt->bindParam(":rcode", $rcode);
                $stmt->execute();

                // Netejo
                $stmt = null;
            }
            catch (PDOException $e) {
                var_dump($e);
            }
        }
        
        function doUpdateRCode($recipient, $rcode) {
            // Es importante el detalle del rcode=-5 que es el estado "Pendiente de respuesta"
            $sql = "update sms set rcode = :rcode where recipient = :recipient AND rcode=-5";
            try {
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(":recipient", $recipient);
                $stmt->bindParam(":rcode", $rcode);
                $stmt->execute();

                // Netejo
                $stmt = null;
            }
            catch (PDOException $e) {
                var_dump($e);
            }
        }
        

        function countPendingCheckers() {
            return $this->pendingCheckers;
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
            //$this->connected = false;
        }

        // ---------------------------------------------------------------------
        // Eventos de repuesta a los comandos de envio
        // ---------------------------------------------------------------------        
        function reply($idEnvio, $status, $data=null) {
            parent::reply($idEnvio, $status, $data);
            
            // En los checekr no hay idEnvio es el numero
            
            if($status == SMSMASS_OK){
                // El estado 0 significa que esta pendiente de enviar
                // El estado 1 significa que se ha enviado
                // Como SMSMASS_OK == 1 -> Modifico el mensaje como pendiente
                $this->doUpdateRCodeByID($idEnvio, '-5');
            }
            else if($status == SMSMASS_NOOK_INSUFFICIENT_CREDIT){
                $this->doUpdateRCodeByID($idEnvio, '11');
            }
            else{
                // En caso de error guardo el tipo de error
                $this->doUpdateRCodeByID($idEnvio, -1);
            }
        }
        
        // ---------------------------------------------------------------------
        // Eventos relacionados con los CHECKER
        // ---------------------------------------------------------------------
        // El servicio de CHECKER puede estar fuera de servicio debido a 
        // mantenimiento de los diversos proveedores que existen
        function serviceUnavailable() {
            parent::serviceUnavailable();
            
            // Para el bucle
            $this->pendingCheckers = 0;
        }

        function checkall($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc) {
            parent::checkall($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc);
            
            // Los comandos RCHECKALL no vienen con el $id del usuario
            $this->doUpdateRCode($num, $rcode);
            $this->pendingCheckers--;
        }

        function checknetwork($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc) {
            parent::checknetwork($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc);
            
            // Los comandos RCHECKNETWORK no vienen con el $id del usuario
            $this->doUpdateRCode($num, $rcode);
            $this->pendingCheckers--;
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
  `status` int(11) NOT NULL DEFAULT '-3',
  `rcode` int(11) NOT NULL DEFAULT '-3',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
 * 
 */
?>