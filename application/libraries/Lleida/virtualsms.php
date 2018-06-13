<?php

/**
 * virtualsms.php
 * @author David Tapia (c) 2012 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 2.1.4
 *
 * VirtualSMS allows a quick acces to SMS/MMS services Lleida.net
 * It may be used by developers to send and receive SMS/MMS easily in their applications
 * There is no need to understand how the platform uses the protocol details
 * Once the properties are established and the control methods called, you are ready to send and receive SMS/MMS easily.
 *
 */
include_once('events.php');
include_once('lib/socket.php');
include_once('lib/bulksend.php');
include_once('lib/constants.php');
include_once('lib/savecommand.php');
include_once('lib/userproperties.php');
include_once('lib/protocolproperties.php');

if (!class_exists('VirtualSMS')) {
    define('VSMS_VERSION', '2.1');

    define('MAXDST', 30);
    define('GENERICS', 'LOGIN|SALDO|INFONUM|TARIFA|PONG|USERAGENT|QUIT');
    define('MT', '(D?F?[BU]?)?SUBMIT|WAPLINK|DST|MSG|FILEMSG|MMSMSG|ENVIA|(ACUSE(ON|OFF|ACK|MMS|MMSACK|MMSACKR)?)|TRANS');
    define('RCVSMS', 'ALLOWANSWER|INCOMINGMO|INCOMINGMOACK|INCOMINGMMS|INCOMINGMMSACK');
    define('MSIDSN', 'CHECKALL|CHECKNETWORK');
    define('RCOMMANDS', 'OK|BYE|RSALDO|RINFONUM|RTARIFA|PING|REJDST|ACK|RTRANS|RCHECKALL|RCHECKALLACK|RCHECKNETWORK|RCHECKNETWORKACK');
    define('COMMANDS', '(' . GENERICS . '|' . MT . '|' . RCVSMS . '|' . MSIDSN . '|' . RCOMMANDS . ')');

    class VirtualSMS {

        var $socket;
        var $events;
        // Propiedades del protocolo y del usuario
        var $protocolproperties;
        var $userproperties;
        // Colas de comandos enviados y recibidos
        var $aSavedCmd = array();
        var $contadorComandos = 400;
        var $dadesEnvioMultiple = array();
        var $isTrans = false;
        // Flags de control
        var $isConnected = false;
        var $isQuit = true;

        function VirtualSMS($user, $password) {
            $this->protocolproperties = new ProtocolProperties();
            $this->userproperties = new UserProperties($user, $password);
            $this->events = new Events();
        }

        function debug($log) {
            $this->protocolproperties->debug(' ' . $log);
        }

        function setEvents($e) {
            if (!empty($e) && is_a($e, 'Events')) {
                $this->events = $e;
            }
        }

        function getEvents() {
            return $this->events;
        }

        function setProtocolProperties($prop) {
            if (!empty($prop) && is_a($prop, 'ProtocolProperties')) {
                $this->protocolproperties = $prop;
            }
            else
                $this->protocolproperties = new ProtocolProperties();
        }

        function getProtocolProperties() {
            return $this->protocolproperties;
        }

        function setUserProperties($prop) {
            if (!empty($prop) && is_a($prop, 'UserProperties')) {
                $this->userproperties = $prop;
            }
            else
                $this->userproperties = new UserProperties();
        }

        function getUserProperties() {
            return $this->userproperties;
        }

        function connect() {
            // Si ya estoy conectado, vuelvo a conectarme ...
            $this->isConnected = false;
            if ($this->userproperties->getUser() == '' || $this->userproperties->getPassword() == '') {
                return $this->fireError(SMSMASS_ERR_NO_USER_OR_PASSWORD_FOUND, 'SMSMASS_ERR_NO_USER_OR_PASSWORD_FOUND', true);
            }
            else if ($this->protocolproperties->getHost() == '') {
                return $this->fireError(SMSMASS_ERR_NO_HOST_FOUND, 'SMSMASS_ERR_NO_HOST_FOUND', true);
            }
            else {
                $this->socket = new Socket(array('host' => $this->protocolproperties->getHost(), 'port' => $this->protocolproperties->getPort()));
                if ($this->socket->isConnected()) {
                    $this->sendToSocketAndSave('LOGIN ' . $this->userproperties->getUser() . ' ' . $this->userproperties->getPassword());
                }
                else {
                    return $this->fireError(SMSMASS_CONN_ERROR, 'No connected', true);
                }
            }
            return SMSMASS_CONN_CONNECTED;
        }

        function disconnect() {
            if ($this->isLogged()) {
                $this->isQuit = true;
                $this->sendToSocketAndSave('QUIT');
            }
            else {
                // Fuerzo el BYE
                $this->doBye();
            }
        }

        function getConnectionStatus() {
            return $this->isConnected();
        }

        function isConnected() {
            if ($this->socket != null) {
                return ($this->socket->isConnected() && $this->isConnected);
            }
            else {
                return false;
            }
        }

        // Esta funcion es necesaria para implementar daemons que esten
        // siempre conectados.
        // La idea es que el daemon implemente un bucle infinito de lectura
        // del socket.
        function readSocket($seconds = 1) {
            $data = '';
            $read = true;
            $live = 0;
            while ($read && $live < $seconds) {
                if (!$this->socket->getData($data)) {
                    break;
                }

                if ($data == '') {
                    $read = false;
                }
                else if (preg_match('#' . RCOMMANDS . '#i', strtoupper($data))) {
                    $read = false;
                }

                $this->getValidCommands($data);

                // Me espero $live segundos antes de liberar el thread unico del php
                // Si fuera multi thread esto no seria necesario.
                sleep(1);
                $live++;
            }
        }

        // Determina si el parametro $str esta codificado en latin1
        function isLatin1($str) {
            return (preg_match("/^[\\x00-\\xFF]*$/u", $str) === 1);
        }

        function isLogged() {
            if ($this->socket != null) {
                return ($this->socket->isConnected() && $this->isConnected && !$this->isQuit);
            }
            else {
                return false;
            }
        }

        function isInternationalNumberFormat($str) {
            return(preg_match('#^\+[0-9]#', $str) == 1);
        }

        function getValidNumber($num) {
            $num = str_replace(array("\n", "\r", "'", " ", ".", "(", ")", "-"), "", trim($num));
            
            if (empty($num)) {
                return '';
            }
            
            // Trec el + o el 00
            if ($num{0} == '+') {
                $num = substr($num, 1);
            }
            else if ($num{0} == '0' && $num{1} == '0') {
                // Trec el 00
                $num = substr($num, 2);
            }

            // Per si nomes han posat un '+' o un '00'
            if (empty($num)) {
                return '';
            }

            if (preg_match('#^34([0-9]{9})$#', $num))
                return '+' . $num;
            elseif (preg_match('#^[0-9]{9}$#', $num))
                return '+34' . $num;
            else
                return '+' . $num;
        }

        // True == ON, False == OFF
        function setAllowAnswer($estat) {
            if (is_bool($estat)) {
                $sw = 'OFF';
                if ($estat)
                    $sw = 'ON';
                if (isset($this->userproperties))
                    $this->userproperties->setAllowAnswer($estat);
                if ($this->isLogged()) {
                    $this->sendToSocketAndSave('ALLOWANSWER ' . $sw);
                }
            }
        }

        function acuseOn($mail = '') {
            if ($this->protocolproperties->isActivatedAcusesAck()) {
                $mail = trim($mail);
                if (!empty($mail)) {
                    $this->userproperties->setMailDeliveryReceipt($mail);
                }

                if ($this->isLogged()) {
                    $this->sendToSocketAndSave('ACUSEON ' . $this->userproperties->getMailDeliveryReceipt());
                }
            }
        }

        // SMS certificado con los valores del objeto userproperties
        function acuseOnCertifiedSMS($mail = '', $lang = 'X', $type = 'X', $name = 'X', $nid = 'X') {
            if ($this->protocolproperties->isActivatedAcusesAck()) {
                $mail = trim($mail);
                // Actualizo los valores del userProperties
                if (!empty($mail)) {
                    $this->userproperties->setMailDeliveryReceipt($mail);
                }

                if ($lang != 'X') {
                    $this->userproperties->setLang($lang);
                }

                if ($type != 'X') {
                    $this->userproperties->setAcuseType($type);
                }

                // Estos opcionales SOLO funcionan en los comandos SUBMIT!
                if ($name != 'X') {
                    $this->userproperties->setCertName($name);
                }

                if ($nid != 'X') {
                    $this->userproperties->setCertNameID($nid);
                }

                if ($this->isLogged()) {
                    $this->sendToSocketAndSave('ACUSEON [lang=' . $this->userproperties->getLang() . '] [cert_type=' . $this->userproperties->getAcuseType() . '] ' . $this->userproperties->getMailDeliveryReceipt());
                }
            }
        }

        function acuseOff() {
            if ($this->protocolproperties->isActivatedAcusesAck()) {
                if ($this->isLogged()) {
                    $this->sendToSocketAndSave('ACUSEOFF');
                }
            }
        }

        // Return the credits
        function getCredits() {
            if ($this->isLogged()) {
                $this->sendToSocketAndSave('SALDO');
                //usleep(400);
            }
            return $this->userproperties->getCredit();
        }

        // Command result in the log file or CATCH the event price()
        function getPrice($num) {
            if ($this->isLogged()) {
                $this->sendToSocketAndSave('TARIFA ' . $num);
            }
        }

        // Command result in the log file or CATCH the event operatorInfo()
        function getOperatorInfo($num) {
            if (!isset($num) || $num == '') {
                $this->events->operatorInfo('', 0, 0);
            }
            else {
                if ($this->isLogged()) {
                    $this->sendToSocketAndSave('INFONUM ' . $num);
                }
            }
        }

        // $num can be an array
        // $this->protocolproperties->isActivatedCheckerAck() true
        function checkall($num, $id = '') {
            return $this->check($num, 'CHECKALL', $id);
        }

        // $num can be an array
        // $this->protocolproperties->isActivatedCheckerAck() true
        function checknetwork($num, $id = '') {
            return $this->check($num, 'CHECKNETWORK', $id);
        }

        function getFileContentBase64($file) {
            $fh = fopen($file, 'rb');
            if (!$fh) {
                $this->protocolproperties->debug('Can not open the file ' . $file);
                return null;
            }
            $file_content = fread($fh, filesize($file));
            fclose($fh);
            $encodedfile = base64_encode($file_content);
            return $encodedfile;
        }

        // -----------------------------------------------------
        // $text, $data, $URL, $recipients pueden ser arrays.
        // -----------------------------------------------------
        // $dateTime FORMAT YYYYMMDDHHmm
        // 197609081600 == 1976.August.8 at 16:00 == 8 de Agosto de 1.976 a las 16:00

        function sendTextSMS($idEnvio, $text, $recipients, $dateTime = '') {
            $idEnvio = str_replace(array("\n", "\r", ":", " ", "(", ")"), "", trim($idEnvio));
            if (!$this->isLogged()) {
                $this->events->reply($idEnvio, SMSMASS_NOOK_NOT_CONNECTED_TO_HOST);
                return SMSMASS_NOOK_NOT_CONNECTED_TO_HOST;
            }
            else {
                if (empty($text)) {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_TEXT_NOT_FOUND);
                    return SMSMASS_NOOK_TEXT_NOT_FOUND;
                }

                if (empty($recipients)) {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_RECIPIENTS_NOT_FOUND);
                    return SMSMASS_NOOK_RECIPIENTS_NOT_FOUND;
                }

                $this->sendSMS($idEnvio, '', $text, $recipients, $dateTime);
                return SMSMASS_OK;
            }
        }

        // $sms->sendBinarySMS($id, getFileContentBase64($filename), $dst, '');
        function sendBinarySMS($idEnvio, $data, $recipients, $dateTime = '') {
            $idEnvio = str_replace(array("\n", "\r", ":", " ", "(", ")"), "", trim($idEnvio));
            if (!$this->isLogged()) {
                $this->events->reply($idEnvio, SMSMASS_NOOK_NOT_CONNECTED_TO_HOST);
                return SMSMASS_NOOK_NOT_CONNECTED_TO_HOST;
            }
            else {
                if (empty($data)) {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_DATA_NOT_FOUND);
                    return SMSMASS_NOOK_DATA_NOT_FOUND;
                }

                if (empty($recipients)) {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_RECIPIENTS_NOT_FOUND);
                    return SMSMASS_NOOK_RECIPIENTS_NOT_FOUND;
                }

                $this->sendSMS($idEnvio, 'B', $data, $recipients, $dateTime);
                return SMSMASS_OK;
            }
        }

        // $sms->sendUnicodeTextSMS($id, base64_encode('My UNICODE info'), $dst, '');
        function sendUnicodeTextSMS($idEnvio, $text, $recipients, $dateTime = '') {
            $idEnvio = str_replace(array("\n", "\r", ":", " ", "(", ")"), "", trim($idEnvio));
            if (!$this->isLogged()) {
                $this->events->reply($idEnvio, SMSMASS_NOOK_NOT_CONNECTED_TO_HOST);
                return SMSMASS_NOOK_NOT_CONNECTED_TO_HOST;
            }
            else {
                if (empty($text)) {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_TEXT_NOT_FOUND);
                    return SMSMASS_NOOK_TEXT_NOT_FOUND;
                }

                if (empty($recipients)) {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_RECIPIENTS_NOT_FOUND);
                    return SMSMASS_NOOK_RECIPIENTS_NOT_FOUND;
                }

                $this->sendSMS($idEnvio, 'U', $text, $recipients, $dateTime);
                return SMSMASS_OK;
            }
        }

        // $sms->sendWapPush($id, $subject, $text, $dst, $mimeType, getFileContentBase64($filename));
        function sendWapPush($idEnvio, $subject, $text, $recipients, $mimeType, $fileData) {
            $idEnvio = str_replace(array("\n", "\r", ":", " ", "(", ")"), "", trim($idEnvio));
            if (!$this->isLogged()) {
                $this->events->reply($idEnvio, SMSMASS_NOOK_NOT_CONNECTED_TO_HOST);
                return SMSMASS_NOOK_NOT_CONNECTED_TO_HOST;
            }
            else {
                $this->sendMSG($idEnvio, 'FILE', $subject, $text, $recipients, $mimeType, $fileData);
                return SMSMASS_OK;
            }
        }

        // $sms->sendMMS($id, $subject, $text, $dst, $mimeType, getFileContentBase64($filename));
        function sendMMS($idEnvio, $subject, $text, $recipients, $mimeType, $fileData) {
            $idEnvio = str_replace(array("\n", "\r", ":", " ", "(", ")"), "", trim($idEnvio));
            if (!$this->isLogged()) {
                $this->events->reply($idEnvio, SMSMASS_NOOK_NOT_CONNECTED_TO_HOST);
                return SMSMASS_NOOK_NOT_CONNECTED_TO_HOST;
            }
            else {
                $this->sendMSG($idEnvio, 'MMS', $subject, $text, $recipients, $mimeType, $fileData);
                return SMSMASS_OK;
            }
        }

        // $mms codificada en Base64, debe de ser un SMIL correcto
        function sendSMIL($idEnvio, $recipients, $mms) {
            $idEnvio = str_replace(array("\n", "\r", ":", " ", "(", ")"), "", trim($idEnvio));
            $this->sendMMSMSG($idEnvio, $recipients, $mms);
        }

        function sendWapLink($idEnvio, $subject, $URL, $recipients) {
            $idEnvio = str_replace(array("\n", "\r", ":", " ", "(", ")"), "", trim($idEnvio));
            if (!$this->isLogged()) {
                $this->events->reply($idEnvio, SMSMASS_NOOK_NOT_CONNECTED_TO_HOST);
                return SMSMASS_NOOK_NOT_CONNECTED_TO_HOST;
            }

            if (!isset($subject) || $subject == '') {
                $this->events->reply($idEnvio, SMSMASS_NOOK_SUBJECT_NOT_FOUND);
                return SMSMASS_NOOK_SUBJECT_NOT_FOUND;
            }

            $mtid = ' ';
            if (!empty($idEnvio)) {
                $mtid = ' [id=' . $idEnvio . '] ';
            }

            if (is_array($URL) && is_array($recipients)) {
                if (!$this->isAnyEmpty($URL) && !$this->isAnyEmpty($recipients)) {
                    if (count($URL) == count($recipients)) {
                        // 2 arrays,
                        $this->doWapLinkTrans($idEnvio, $subject, $URL, $recipients);
                        return SMSMASS_OK;
                    }
                    else {
                        $this->events->reply($idEnvio, SMSMASS_NOOK_NUMBER_OF_URL_AND_RECIPIENTS_NOT_MATCH);
                        return SMSMASS_NOOK_NUMBER_OF_URL_AND_RECIPIENTS_NOT_MATCH;
                    }
                }
                else {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_ANY_OR_ALL_URL_NOT_FOUND);
                    return SMSMASS_NOOK_ANY_OR_ALL_URL_NOT_FOUND;
                }
            }
            else if (!is_array($URL) && is_array($recipients)) {
                if (!$this->isAnyEmpty($recipients) && isset($URL)) {
                    // The same URL for all recipeints
                    $this->doWapLinkTrans($idEnvio, $subject, $URL, $recipients);
                    return SMSMASS_OK;
                }
                else {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_ANY_OR_ALL_RECIPIENTS_NOT_FOUND);
                    return SMSMASS_NOOK_ANY_OR_ALL_RECIPIENTS_NOT_FOUND;
                }
            }
            else if (is_array($URL) && !is_array($recipients)) {
                if (!$this->isAnyEmpty($URL) && isset($recipients)) {
                    // Multiple $URLs for only one recipient? ok...
                    foreach ($URL as $u) {
                        $this->sendToSocketAndSave('WAPLINK' . $mtid . $recipients . ' ' . $u . ' ' . $subject, $idEnvio);
                    }
                    return SMSMASS_OK;
                }
                else {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_ANY_OR_ALL_URL_NOT_FOUND);
                    return SMSMASS_NOOK_ANY_OR_ALL_URL_NOT_FOUND;
                }
            }
            else {
                $this->sendToSocketAndSave('WAPLINK' . $mtid . $recipients . ' ' . $URL . ' ' . $subject, $idEnvio);
                return SMSMASS_OK;
            }
            return SMSMASS_NOOK_GENERIC_ERROR;
        }

        // ============================================================================
        // PRIVATE methods, use under your responsibility
        // ============================================================================
        //private function check($num, $type = 'CHECKALL') {
        private function check($num, $type, $id = '') {
            if (!$this->isLogged()) {
                return $this->fireError(SMSMASS_NOOK_NOT_CONNECTED_TO_HOST, 'SMSMASS_NOOK_NOT_CONNECTED_TO_HOST', true);
            }
            else if (isset($num) && $this->protocolproperties->isActivatedCheckerAck()) {
                if (is_array($num)) {
                    foreach ($num as $n) {
                        $n = $this->getValidNumber($n);
                        if (!empty($n)) {
                            $this->sendToSocketAndSave($type . ' ' . $n, $id);
                        }
                    }
                    return SMSMASS_OK;
                }
                else {
                    $num = $this->getValidNumber($num);
                    if (!empty($num)) {
                        $this->sendToSocketAndSave($type . ' ' . $num, $id);
                        return SMSMASS_OK;
                    }
                    else {
                        return SMSMASS_NOOK_INVALID_NUMBER;
                    }
                }
            }
            else {
                return SMSMASS_NOOK_INVALID_NUMBER;
            }
        }

        private function sendMMSMSG($idEnvio, $recipients, $mms) {
            if (!$this->isLogged()) {
                $this->events->reply($idEnvio, SMSMASS_NOOK_NOT_CONNECTED_TO_HOST);
                return SMSMASS_NOOK_NOT_CONNECTED_TO_HOST;
            }
            else {
                $countRecipients = count($recipients);
                $v = new BulkSend($countRecipients, $idEnvio);
                $this->dadesEnvioMultiple[$idEnvio] = $v;

                $this->sendNDST($idEnvio, $recipients, MAXDST);
                $this->sendToSocketAndSave('MMSMSG ' . $mms, $idEnvio);

                $mmsid = ' ';
                if (!empty($idEnvio)) {
                    $mmsid = ' [id=' . $idEnvio . '] ';
                }

                // Por defecto lo desactivo
                $custom_cert = '';
                $tmp_name = $this->userproperties->getCertName();
                $tmp_name_id = $this->userproperties->getCertNameID();
                if (!empty($tmp_name)) {
                    $custom_cert = ' [cert_name=' . $tmp_name . ']';
                }

                if (!empty($tmp_name_id)) {
                    $custom_cert .= ' [cert_name_id=' . $tmp_name_id . '] ';
                }

                $this->sendToSocketAndSave('ENVIA' . $custom_cert . $mmsid, $idEnvio);
                return SMSMASS_OK;
            }
        }

        private function sendMSG($idEnvio, $tipus, $subject, $text, $recipients, $mimeType, $fileData) {
            if (!isset($subject) || $subject == '') {
                $this->events->reply($idEnvio, SMSMASS_NOOK_SUBJECT_NOT_FOUND);
                return SMSMASS_NOOK_SUBJECT_NOT_FOUND;
            }

            if (!isset($mimeType) || $mimeType == '' || !isset($fileData) || $fileData == '') {
                $this->events->reply($idEnvio, SMSMASS_NOOK_MIMETYPE_OR_DATA_NOT_FOUND);
                return SMSMASS_NOOK_MIMETYPE_OR_DATA_NOT_FOUND;
            }

            $txt = '';
            if (empty($text)) {
                $this->events->reply($idEnvio, SMSMASS_NOOK_TEXT_NOT_FOUND);
                return SMSMASS_NOOK_TEXT_NOT_FOUND;
            }
            else {
                if (is_array($text)) {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_TEXT_NOT_FOUND);
                    return SMSMASS_NOOK_TEXT_NOT_FOUND;
                }
                $txt = trim($text);
            }

            $countRecipients = count($recipients);
            $v = new BulkSend($countRecipients, $idEnvio);
            $this->dadesEnvioMultiple[$idEnvio] = $v;

            $this->sendNDST($idEnvio, $recipients, MAXDST);

            // Enviem el texte
            $text = str_replace("\n", "\\n", $text);
            $this->sendToSocketAndSave($tipus . 'MSG ' . $mimeType . ' ' . $fileData . ' ' . $subject . '|' . $text, $idEnvio);

            $mmsid = ' ';
            if (!empty($idEnvio)) {
                $mmsid = ' [id=' . $idEnvio . ']';
            }

            $custom_cert = '';
            $tmp_name = $this->userproperties->getCertName();
            $tmp_name_id = $this->userproperties->getCertNameID();
            if (!empty($tmp_name)) {
                $custom_cert = ' [cert_name=' . $tmp_name . ']';
            }

            if (!empty($tmp_name_id)) {
                $custom_cert .= ' [cert_name_id=' . $tmp_name_id . '] ';
            }

            // Enviem el missatge
            $this->sendToSocketAndSave('ENVIA' . $custom_cert . $mmsid, $idEnvio);
        }

        // ATTENTION!
        // Store the credit that the protocol receipt from the server
        // The server ever verify the credit.
        // You can change this value has you want. If you don't have credit in the server face...the server never send the SMS!
        private function setCredit($newCredit) {
            if (is_float($newCredit)) {
                $this->userproperties->setCredit($newCredit);
                $this->protocolproperties->debug(' Setting the new credit to ' . $this->userproperties->getCredit());
                if ($newCredit < 1) {
                    $this->events->noCredit();
                }
            }
        }

        private function doBye() {
            $this->protocolproperties->debug(' Bye...');
            $this->socket->disconnect();
            $this->events->disconnected();
            $this->isConnected = false;
            $this->isQuit = true;
            $this->socket = null;
        }

        private function fireError($errCode, $errMsg, $disconnect = false) {
            $this->protocolproperties->debug(' [ Error ] ' . $errCode . ':' . $errMsg);
            if ($disconnect == true) {
                if ($this->isLogged()) {
                    $this->doBye();
                }
                $this->events->connectionError($errCode, $errMsg);
            }
            return $errCode;
        }

        private function unsetFromCommandArray($item) {
            unset($this->aSavedCmd[intval($item)]);
            return true;
        }

        private function unsetFromBulkArray($item) {
            // La clau del array es el MT_USER_ID ??
            unset($this->dadesEnvioMultiple[$item]);
            $this->protocolproperties->debug(' Unset item ' . $item);
            return true;
        }

        // Deso els comandos enviats pel client
        private function sendToSocketAndSave($cadena, $id = '') {
            if ($this->socket->isConnected()) {
                // Si no ha definido un tag de comando dejo que lo decida la API
                $tag = $this->contadorComandos++;
                $cmd = $tag . ' ' . $cadena;
                // Guardo los comando que envia el cliente
                $this->aSavedCmd[intval($tag)] = new SaveCommand($id, $tag, $cmd);
                $this->socket->sendData($cmd . "\n");
                $this->protocolproperties->debug(' Client: ' . $cmd);
                $this->readSocket();
            }
            else {
                $this->fireError(SMSMASS_ERR_CANT_SEND_DATA, 'SMSMASS_ERR_CANT_SEND_DATA', true);
            }
        }

        // Per enviar les respostes als comandos del servidor, en aquest cas no
        // cal desar el comando
        private function sendToSocket($cadena, $tag) {
            if ($this->socket->isConnected()) {
                $cmd = $tag . ' ' . $cadena;
                $this->socket->sendData($cmd . "\n");
                $this->protocolproperties->debug(' Client: ' . $cmd);

                // Si es un PONG no recibire ninguna respuesta
                if (stristr($cadena, 'PONG') === FALSE) {
                    $this->readSocket();
                }
            }
            else {
                $this->fireError(SMSMASS_ERR_CANT_SEND_DATA, 'SMSMASS_ERR_CANT_SEND_DATA', true);
            }
        }

        private function getValidCommands($cmds) {
            $xComandos = explode("\n", $cmds);
            foreach ($xComandos as $cmd) {
                if ($cmd != '' && $cmd != ' ') {
                    $this->dataArrival($cmd);
                }
            }
        }

        private function dataArrival($cmd) {
            $this->protocolproperties->debug(' Server: ' . $cmd);

            if (strtoupper($cmd) == "PING TIMEOUT") {
                $this->fireError(SMSMASS_ERR_PING_TIMEOUT, '' . $cmd, true);
            }
            else {
                $split = $this->parseCommand($cmd);
                if ($split != null) {
                    $this->processCommand($split, $cmd);
                }
                else {
                    $this->fireError(SMSMASS_ERR_PROTOCOL_ERROR, 'Protocol error - Command unknow : ' . $cmd, false);
                }
            }
        }

        private function parseCommand($tagCmd) {
            $cmd = explode(" ", $tagCmd);
            if ($cmd == false) {
                return null;
            }

            if (isset($cmd) && count($cmd) > 1) {
                if ($this->isCommand($cmd[1])) {
                    return $cmd;
                }
            }

            return null;
        }

        private function isCommand($cmd) {
            return preg_match('#' . COMMANDS . '#i', strtoupper($cmd));
        }

        private function processCommand($split, $cadenaRebuda) {
            $splitcmd = '';
            if (is_array($split)) {
                $splitcmd = strtoupper($split[1]);
                switch (true) {
                    case (preg_match('#[D?F?[BU]?]?SUBMITOK#', $splitcmd) || $splitcmd === 'WAPLINKOK') :
                        // BSUBMITOK, USUBMITOK, etc...
                        if (!$this->isTrans) {
                            // Si no es una transaccio actualitza el credit
                            $this->setCredit(floatval($split[2] . '.' . $split[3]));
                            $this->events->reply($this->getIDbyTag($split[0]), SMSMASS_OK);
                        }
                        $this->unsetFromCommandArray($split[0]);
                        break;

                    case ($splitcmd === 'CHECKALLOK' || $splitcmd === 'CHECKNETWORKOK') :
                        // Añadir el evento reply? Y con que valores?
                        $this->events->reply($this->getIDbyTag($split[0]), SMSMASS_OK);
                        $this->setCredit(floatval($split[2] . '.' . $split[3]));
                        $this->unsetFromCommandArray($split[0]);
                        break;
                    case $splitcmd === 'OK':
                        $this->rebutOK($split, $this->getLiniaEnviada($split[0], $cadenaRebuda));
                        break;
                    case $splitcmd === 'NOOK':
                        $this->rebutNOOK($split, $this->getLiniaEnviada($split[0], $cadenaRebuda));
                        break;
                    case ($splitcmd === 'RSALDO'):
                        $this->unsetFromCommandArray($split[0]);
                        $this->setCredit(floatval($split[2] . '.' . $split[3]));
                        break;
                    case ($splitcmd === 'USERAGENTOK'):
                        $this->unsetFromCommandArray($split[0]);
                        break;
                    case $splitcmd === 'RINFONUM':
                        $this->unsetFromCommandArray($split[0]);
                        $this->events->operatorInfo($this->getLiniaEnviada($split[0], $cadenaRebuda), intval($split[2]), intval($split[3]));
                        break;
                    case $splitcmd === 'RTARIFA':
                        $this->unsetFromCommandArray($split[0]);
                        if (count($split) > 2) {
                            // <etiq> RTARIFA <codigo tarifa> <precio MT> <acuse> <remitente>
                            $this->events->price(implode(" ", array_slice($split, 2)));
                        }
                        break;
                    case $splitcmd === 'RTRANS':
                        // Inicialitzo a false per si es un error o ha acabat
                        $this->isTrans = false;
                        // Reaprofito la var $splitcmd
                        $splitcmd = strtoupper($split[2]);
                        switch (true) {
                            case $splitcmd === 'INICIAR':
                                if (strtoupper($split[3]) == 'OK') {
                                    // Al rebre el OK del TRANS INICIAR
                                    $this->isTrans = true;
                                }
                                else {
                                    // Se puede llegar a controlar el NOOK
                                    // SMSMASS_NOOK_TRANS;
                                }
                                $this->unsetFromCommandArray($split[0]);
                                break;
                            case $splitcmd === 'ABORTAR':
                                // Se puede llegar a controlar el NOOK
                                // SMSMASS_NOOK_INVALID_TRANS
                                $this->unsetFromCommandArray($split[0]);
                                break;
                            case $splitcmd === 'FIN':
                                $this->completeTrans($split, $cadenaRebuda);
                                break;
                            case $splitcmd === 'NOOK':
                                $this->unsetFromCommandArray($split[0]);
                                break;
                        }
                        break;
                    case $splitcmd === 'REJDST':
                        // No deberia de aparecer el comando REJDST!
                        $v = $this->dadesEnvioMultiple[$this->getIDbyTag($split[0])];
                        for ($i = 2; $i < count($split); $i++) {
                            $v->addFailRecipient($split[$i]);
                        }
                        // No lo quito del array de comandos
                        // Este punto es importante pq pertenece a un ENVIA!
                        //$this->unsetFromCommandArray($split[0]);
                        break;
                    case ($splitcmd === 'ACUSE' && $this->protocolproperties->isActivatedAcusesAck()):
                        $this->sendToSocket('ACUSEACK ' . $split[2], $split[0]);
                        if (count($split) > 7) {
                            $this->events->deliveryReceipt($split[4], $split[6], $split[3], implode(" ", array_slice($split, 7)), $this->getAcuseStat($split[5]), $this->getAcuseID($split[2]));
                        }
                        break;
                    case ($splitcmd === 'ACUSEMMS' && $this->protocolproperties->isActivatedAcusesAck()):
                        $this->sendToSocket('ACUSEMMSACK ' . $split[2], $split[0]);
                        if (count($split) > 6) {
                            $this->events->deliveryReceiptMMS($split[4], $split[6], $split[3], $this->getAcuseStat($split[5]), $this->getAcuseID($split[2]));
                        }
                        break;
                    case $splitcmd === 'ACUSEACKR': break;
                    case $splitcmd === 'ACUSEMMSACKR': break;
                    case ($splitcmd === 'INCOMINGMO' && $this->protocolproperties->isActivatedIncomingAck()):
                        $this->sendToSocket('INCOMINGMOACK ' . $split[2], $split[0]);
                        if (count($split) > 6) {
                            $this->events->incomingMo($split[2], $split[3], $split[4], $split[5], implode(" ", array_slice($split, 6)));
                        }
                        break;
                    case ($splitcmd === 'INCOMINGMMS' && $this->protocolproperties->isActivatedIncomingAck()):
                        $this->sendToSocket('INCOMINGMMSACK ' . $split[2], $split[0]);
                        if (count($split) > 5) {
                            $this->events->incomingMMS($split[2], $split[3], $split[4], $split[5]);
                        }
                        break;
                    case $splitcmd === 'BYE':
                        $this->unsetFromCommandArray($split[0]);
                        $this->doBye();
                        break;
                    case $splitcmd === 'PING':
                        $this->sendToSocket('PONG ' . $split[2], $split[0]);
                        break;
                    case ($splitcmd === 'RCHECKALL' && $this->protocolproperties->isActivatedCheckerAck()):
                        if (count($split) > 10) {
                            if ($split[6] == '-4') {
                                $this->events->serviceUnavailable();
                            }
                            else {
                                // $id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc
                                $this->events->checkall($split[2], $split[3], $split[4], $split[5], $split[6], $split[7], $split[8], $split[9], $split[10]);
                            }
                        }
                        else {
                            // Retorna un formato desconocido, la guardo en un fichero.
                            $rcheck = fopen('rcheckall.txt', 'a+');
                            $cmd = implode(' ', $split);
                            $cmd .= "\n";
                            fputs($rcheck, $cmd);
                            fclose($rcheck);
                            $this->protocolproperties->debug(' Generated a text file with the results [rcheckall.txt] : ' . $cmd);
                        }
                        // Responem amb el ACK
                        $this->sendToSocket('RCHECKALLACK ' . $split[2], $split[0]);
                        // Esta otra solucion tiene sus problemillas...
                        //$this->sendToSocketAndSave('RCHECKALLACK ' . $split[2]);
                        break;
                    case ($splitcmd === 'RCHECKNETWORK' && $this->protocolproperties->isActivatedCheckerAck()):
                        if (count($split) > 10) {
                            if ($split[6] == '-4') {
                                $this->events->serviceUnavailable();
                            }
                            else {
                                // $id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc
                                $this->events->checknetwork($split[2], $split[3], $split[4], $split[5], $split[6], $split[7], $split[8], $split[9], $split[10]);
                            }
                        }
                        else {
                            // Retorna un formato desconocido, la guardo en un fichero.
                            // Guardar los datos en un fichero
                            $rcheck = fopen('rchecknetwork.txt', 'a+');
                            $cmd = implode(' ', $split) . "\n";
                            fputs($rcheck, $cmd);
                            fclose($rcheck);
                            $this->protocolproperties->debug(' Generated a text file with the results [rchecknetwork.txt] : ' . $cmd);
                        }
                        // Responem amb el ACK
                        $this->sendToSocket('RCHECKNETWORKACK ' . $split[2], $split[0]);
                        break;
                    default :
                        $this->unsetFromCommandArray($split[0]);
                }
            }
            else {
                $this->fireError(SMSMASS_ERR_PROTOCOL_ERROR, 'Protocol error - Command unknow : ' . $cadenaRebuda, false);
            }
        }

        private function getAcuseStat($stat) {
            if ('ACKD' == strtoupper($stat))
                return SMSMASS_DEL_ACKED;
            else if ('BUFFRD' == strtoupper($stat))
                return SMSMASS_DEL_BUFFERED;
            else if ('DELIVRD' == strtoupper($stat))
                return SMSMASS_DEL_DELIVERED;
            else if ('FAILD' == strtoupper($stat))
                return SMSMASS_DEL_FAILED;
            else if ('EXPIRD' == strtoupper($stat))
                return SMSMASS_DEL_EXPIRED;
            else if ('DELETD' == strtoupper($stat))
                return SMSMASS_DEL_DELETED;
            return SMSMASS_DEL_UNKNOWN;
        }

        // Retorno la cadena enviada, pero si la troba al hash retorna el comando rebut
        private function getLiniaEnviada($tag, $cadenaRebuda) {
            $liniaEnviada = $cadenaRebuda;
            if (!array_key_exists(intval($tag), $this->aSavedCmd)) {
                $this->protocolproperties->debug(' Server command tag (' . $tag . ')');
            }
            else {
                $liniaEnviada = $this->aSavedCmd[intval($tag)]->getCommand();
            }
            return $liniaEnviada;
        }

        private function getIDbyTag($tag) {
            if (array_key_exists(intval($tag), $this->aSavedCmd)) {
                return $this->aSavedCmd[intval($tag)]->getID();
            }
            return '';
        }

        private function getID($cmd) {
            $cmd = explode(" ", $cmd);
            // Els mtid i mmsid sempre estaran a
            $pos = strpos($cmd[2], '[id=');
            if ($pos !== false) {
                // El comando conte el mtid o mmsid
                $tmp = substr($cmd[2], $pos + 4);
                return substr($tmp, 0, strlen($tmp) - 1);
            }
            return '';
        }

        private function getAcuseID($ack) {
            $pos = strpos($ack, '-');
            if ($pos !== false) {
                // El ack conte el mtid o mmsid
                return substr($ack, $pos + 1);
            }
            return '';
        }

        private function rebutOK($split, $enviat) {
            // Este setCredit al recibir un OK vacio hace saltar el evento noCredit()
            if (count($split) > 3) {
                $this->setCredit(floatval($split[2] . '.' . $split[3]));
            }

            if (preg_match('#LOGIN#i', $enviat)) {
                $this->isConnected = true;
                $this->isQuit = false;
                $this->events->connected();
                $this->sendToSocketAndSave('USERAGENT API PHP version ' . VSMS_VERSION);
                $this->unsetFromCommandArray($split[0]);
            }
            else if (preg_match("#(MSG|FILEMSG|MMSMSG)#", $enviat)) {
                $this->unsetFromCommandArray($split[0]);
            }
            else if (preg_match('#ENVIA#i', $enviat)) {
                $id = $this->getIDbyTag($split[0]);
                $v = $this->dadesEnvioMultiple[$id];
                if (!$v->isEmptyFailRecipient()) {
                    if ($v->isAllSendFailed()) {
                        // Si tots els destinataris eren erronis
                        $this->events->reply($id, SMSMASS_NOOK_ALL_RECIPIENT_INVALID);
                    }
                    else {
                        // Si alguns dels destinataris eren erronis
                        $this->events->reply($id, SMSMASS_OK_ANY_RECIPIENT_INVALID, $v->getFailedsRecipients());
                    }
                }
                else {
                    // Todo el envio correcto
                    $this->events->reply($id, SMSMASS_OK);
                }
                $this->unsetFromCommandArray($split[0]);
                $this->unsetFromBulkArray($id);
            }
            else if (preg_match('#OK#i', $enviat)) {
                // No faig res no s'ha trobat el comando ENVIAT i la var $enviat es el comando rebut
                $this->unsetFromCommandArray($split[0]);
            }
            else {
                $this->unsetFromCommandArray($split[0]);
            }
        }

        private function rebutNOOK($split, $enviat) {
            $this->protocolproperties->debug(' NOOK : ' . $enviat);
            if (preg_match('#SUBMIT|WAPLINK#i', $enviat)) {
                if ($this->isTrans) {
                    // Trec els opcionals
                    $tmp = str_replace("[allowanswer] ", "", $enviat);

                    // Faig el split per trobar els destins invalids o que han donat error
                    $splitLiniaEnviada = explode(' ', $tmp);
                    $desti = 3;
                    if (strtoupper($splitLiniaEnviada[1]) == 'DFSUBMIT') {
                        $desti = 5;
                    }
                    else if (strtoupper($splitLiniaEnviada[1]) == 'FSUBMIT' || strtoupper($splitLiniaEnviada[1]) == 'DSUBMIT') {
                        $desti = 4;
                    }

                    $this->dadesEnvioMultiple[$this->getIDbyTag($split[0])]->addFailRecipient($splitLiniaEnviada[$desti]);
                    $this->unsetFromCommandArray($split[0]);
                }
                else {
                    // Son NOOK de comandos individuals
                    $id = $this->getID($enviat);
                    $this->unsetFromCommandArray($split[0]);

                    if (count($split) == 4) {
                        // Mensaje de NOOK de dos palabras
                        if (stristr($split[2], 'invalid') != FALSE) {
                            // Si el destinatari no es correcte
                            $this->events->reply($id, SMSMASS_NOOK_ALL_RECIPIENT_INVALID);
                            //return SMSMASS_NOOK_ALL_RECIPIENT_INVALID;
                        }
                        else if (stristr($split[2], 'saldo') != FALSE) {
                            // Si no hi ha prou saldo
                            $this->events->reply($id, SMSMASS_NOOK_INSUFFICIENT_CREDIT);
                            //return SMSMASS_NOOK_INSUFFICIENT_CREDIT;
                        }
                        else {
                            $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                            //return SMSMASS_NOOK_GENERIC_ERROR;
                        }
                    }
                    elseif (count($split) == 6) {
                        if (stristr($split[3], 'fecha') != FALSE) {
                            $this->events->reply($id, SMSMASS_NOOK_INVALID_DATE);
                            //return SMSMASS_NOOK_INVALID_DATE;
                        }
                        else {
                            $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                            //return SMSMASS_NOOK_GENERIC_ERROR;
                        }
                    }
                    elseif (count($split) == 8) {
                        // x NOOK No se dispone de saldo suficiente
                        if (stristr($split[6], 'saldo') != FALSE) {
                            $this->events->reply($id, SMSMASS_NOOK_INSUFFICIENT_CREDIT);
                            //return SMSMASS_NOOK_INSUFFICIENT_CREDIT;
                        }
                        else {
                            $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                            //return SMSMASS_NOOK_GENERIC_ERROR;
                        }
                    }
                    else {
                        $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                        //return SMSMASS_NOOK_GENERIC_ERROR;
                    }
                }
            }
            else if (preg_match('#CHECKALL|CHECKNETWORK#i', $enviat)) {
                // !! Nuevo cambio, al añadir el parametro opcional $id en los
                // metodos checker puedo dar mas detalle del error (tengo el id)
                //
                $id = $this->getIDbyTag($split[0]);

                // Por si no ponen el $id
                // etiqueta checkall|checknetwork numero
                $splitLiniaEnviada = explode(' ', $enviat);

                if (count($split) == 4) {
                    // Mensaje de NOOK de dos palabras
                    if (stristr($split[2], 'saldo') != FALSE) {
                        // llamo al metodo de la clase events checkall o checknetwork segun el comando
                        if ($id == '') {
                            $this->events->{$splitLiniaEnviada[1]}('', time(), $splitLiniaEnviada[2], '1', '11', '--', '--', '0', '--');
                        }
                        else {
                            $this->events->reply($this->getIDbyTag($split[0]), SMSMASS_NOOK_INSUFFICIENT_CREDIT);
                        }
                        //return SMSMASS_NOOK_INSUFFICIENT_CREDIT;
                    }
                    else {
                        if ($id == '') {
                            $this->events->{$splitLiniaEnviada[1]}('', time(), $splitLiniaEnviada[2], '1', '12', '--', '--', '0', '--');
                        }
                        else {
                            $this->events->reply($this->getIDbyTag($split[0]), SMSMASS_NOOK_GENERIC_ERROR);
                        }
                        //return SMSMASS_NOOK_GENERIC_ERROR;
                    }
                }
                elseif (count($split) == 6) {
                    //x NOOK Servicio temporalmente no disponible
                    if (stristr($split[2], 'servicio') != FALSE) {
                        $this->events->serviceUnavailable();
                        //return SMSMASS_NOOK_SERVICE_UNAVAILABLE;
                    }
                    else {
                        if ($id == '') {
                            $this->events->{$splitLiniaEnviada[1]}('', time(), $splitLiniaEnviada[2], '1', '12', '--', '--', '0', '--');
                        }
                        else {
                            $this->events->reply($this->getIDbyTag($split[0]), SMSMASS_NOOK_GENERIC_ERROR);
                        }
                        //return SMSMASS_NOOK_GENERIC_ERROR;
                    }
                }
                elseif (count($split) == 8) {
                    // x NOOK No se dispone de saldo suficiente
                    if (stristr($split[6], 'saldo') != FALSE) {
                        // llamo al metodo de la clase events checkall o checknetwork segun el comando
                        if ($id == '') {
                            $this->events->{$splitLiniaEnviada[1]}('', time(), $splitLiniaEnviada[2], '1', '11', '--', '--', '0', '--');
                        }
                        else {
                            $this->events->reply($this->getIDbyTag($split[0]), SMSMASS_NOOK_INSUFFICIENT_CREDIT);
                        }
                        //return SMSMASS_NOOK_INSUFFICIENT_CREDIT;
                    }
                    else {
                        if ($id == '') {
                            $this->events->{$splitLiniaEnviada[1]}('', time(), $splitLiniaEnviada[2], '1', '12', '--', '--', '0', '--');
                        }
                        else {
                            $this->events->reply($this->getIDbyTag($split[0]), SMSMASS_NOOK_GENERIC_ERROR);
                        }
                        //return SMSMASS_NOOK_GENERIC_ERROR;
                    }
                }
                else {
                    if ($id == '') {
                        $this->events->{$splitLiniaEnviada[1]}('', time(), $splitLiniaEnviada[2], '1', '12', '--', '--', '0', '--');
                    }
                    else {
                        $this->events->reply($this->getIDbyTag($split[0]), SMSMASS_NOOK_GENERIC_ERROR);
                    }
                    //return SMSMASS_NOOK_GENERIC_ERROR;
                }
            }
            else if (stristr($enviat, 'LOGIN') != FALSE) {
                $this->isQuit = false;
                $this->isConnected = false;
                $this->unsetFromCommandArray($split[0]);
                if (count($split) > 2) {
                    // if (stristr($split[2], 'already') != FALSE) {
                    $this->fireError(SMSMASS_ERR_ALREADY_LOGGED, 'SMSMASS_ERR_ALREADY_LOGGED', true);
                }
                else {
                    $this->fireError(SMSMASS_ERR_INVALID_USER_OR_PASSWORD, 'SMSMASS_ERR_INVALID_USER_OR_PASSWORD', true);
                }
            }
            else if (preg_match("#(MSG|FILEMSG|MMSMSG)#", $enviat)) {
                $this->unsetFromCommandArray($split[0]);
            }
            else if (stristr($enviat, 'DST') != FALSE) {
                // Els REJDST ja estan controlats en el switch de comandos
                $this->unsetFromCommandArray($split[0]);
            }
            else if (stristr($enviat, 'ENVIA') != FALSE) {
                $id = $this->getIDbyTag($enviat);

                if (count($split) == 4) {
                    // Mensaje de NOOK de dos palabras
                    if (stristr($split[2], 'saldo') != FALSE) {
                        // Si no hi ha prou saldo
                        $this->events->reply($id, SMSMASS_NOOK_INSUFFICIENT_CREDIT);
                        //return SMSMASS_NOOK_INSUFFICIENT_CREDIT;
                    }
                    else {
                        $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                        //return SMSMASS_NOOK_GENERIC_ERROR;
                    }
                }
                elseif (count($split) == 8) {
                    // x NOOK No se dispone de saldo suficiente
                    if (stristr($split[6], 'saldo') != FALSE) {
                        $this->events->reply($id, SMSMASS_NOOK_INSUFFICIENT_CREDIT);
                        //return SMSMASS_NOOK_INSUFFICIENT_CREDIT;
                    }
                    elseif (stristr($split[6], 'ningun') != FALSE) {
                        $this->events->reply($id, SMSMASS_NOOK_ALL_RECIPIENT_INVALID);
                        //return SMSMASS_NOOK_ALL_RECIPIENT_INVALID;
                    }
                    else {
                        $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                        //return SMSMASS_NOOK_GENERIC_ERROR;
                    }
                }
                else {
                    $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                    //return SMSMASS_NOOK_GENERIC_ERROR;
                }

                $this->unsetFromCommandArray($split[0]);
                $this->unsetFromBulkArray($id);
            }
        }

        private function sendSMS($idEnvio, $mode, $text, $recipients, $dateTime) {
            if (is_array($text) && is_array($recipients)) {
                if (!$this->isAnyEmpty($text) && !$this->isAnyEmpty($recipients)) {
                    if (count($text) == count($recipients)) {
                        $this->doSubmitTrans($idEnvio, $mode, $text, $recipients, $dateTime);
                    }
                    else {
                        $this->events->reply($idEnvio, SMSMASS_NOOK_NUMBER_OF_TEXT_AND_RECIPIENTS_NOT_MATCH);
                    }
                }
                else {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_NUMBER_OF_TEXT_AND_RECIPIENTS_NOT_MATCH);
                }
            }
            else if (!is_array($text) && is_array($recipients)) {
                if (!$this->isAnyEmpty($recipients) && isset($text)) {
                    // The same text for all recipeints
                    if (!$this->userproperties->isAllowAnswer() && $this->userproperties->getCustomizedSender() == '' && $dateTime == '' && $mode == '') {
                        $v = new BulkSend(count($recipients), $idEnvio);
                        $this->dadesEnvioMultiple[$idEnvio] = $v;

                        $this->sendNDST($idEnvio, $recipients, MAXDST);
                        $this->sendToSocketAndSave('MSG ' . $text, $idEnvio);
                        $mtid = ' ';
                        if (!empty($idEnvio)) {
                            $mtid = ' [id=' . $idEnvio . '] ';
                        }

                        $custom_cert = '';
                        $tmp_name = $this->userproperties->getCertName();
                        $tmp_name_id = $this->userproperties->getCertNameID();
                        if (!empty($tmp_name)) {
                            $custom_cert = ' [cert_name=' . $tmp_name . ']';
                        }

                        if (!empty($tmp_name_id)) {
                            $custom_cert .= ' [cert_name_id=' . $tmp_name_id . '] ';
                        }


                        $this->sendToSocketAndSave('ENVIA' . $custom_cert . $mtid, $idEnvio);
                    }
                    else {
                        $this->doSubmitTrans($idEnvio, $mode, $text, $recipients, $dateTime);
                    }
                }
                else {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_ANY_OR_ALL_RECIPIENTS_NOT_FOUND);
                }
            }
            else if (is_array($text) && !is_array($recipients)) {
                if (!$this->isAnyEmpty($text) && isset($recipients)) {
                    // Multiple texts for only one recipient? ok...
                    foreach ($text as $t) {
                        $this->doSubmit($idEnvio, $mode, $t, $recipients, $dateTime);
                    }
                }
                else {
                    $this->events->reply($idEnvio, SMSMASS_NOOK_ANY_OR_ALL_TEXT_NOT_FOUND);
                }
            }
            else {
                $this->doSubmit($idEnvio, $mode, $text, $recipients, $dateTime);
            }
        }

        private function doSubmit($idEnvio, $mode, $text, $recipient, $dateTime) {
            // Modo desconocido -> Submit normal
            if ($mode != 'U' && $mode != 'B') {
                $mode = '';
            }

            $mtid = ' ';
            if (!empty($idEnvio)) {
                $mtid = ' [id=' . $idEnvio . '] ';
            }

            $dt = '';
            $dt2 = $dt;
            $aa = '';
            if ($this->userproperties->isAllowAnswer())
                $aa = '[allowanswer] ';

            if (preg_match('#[0-9]{12}#', $dateTime)) {
                // Transform the dateTime to UTC [+-] HHMM
                //$utime = strtotime($dateTime); // No me mola
                $utc = date('O'); // UTC del servidor!
                if (intval($utc) >= 0)
                    $utc = str_replace("+", "-", $utc);
                else
                    $utc = str_replace("-", "+", $utc);
                $dt = 'D';
                $dt2 = $dateTime . $utc . ' ';
            }

            $cs = $this->userproperties->getCustomizedSender();
            if ($cs != '') {
                $mode = 'F' . $mode;
                $cs = $cs . ' ';
            }

            // Por defecto lo desactivo
            $custom_cert = '';
            $tmp_name = $this->userproperties->getCertName();
            $tmp_name_id = $this->userproperties->getCertNameID();

            if (!empty($tmp_name)) {
                $custom_cert = ' [cert_name=' . $tmp_name . '] ';
            }

            if (!empty($tmp_name_id)) {
                $custom_cert .= ' [cert_name_id=' . $tmp_name_id . '] ';
            }

            // Enviem el missatge
            $this->sendToSocketAndSave($dt . $mode . 'SUBMIT' . $custom_cert . $mtid . $aa . $dt2 . $cs . $recipient . ' ' . $text, $idEnvio);
        }

        private function doSubmitTrans($idEnvio, $mode, $text, $recipients, $dateTime) {
            // Modo desconocido -> Submit normal
            if ($mode != '' && $mode != 'U' && $mode != 'B') {
                $mode = '';
            }

            $mtid = ' ';
            if (!empty($idEnvio)) {
                $mtid = ' [id=' . $idEnvio . '] ';
            }

            $aa = '';
            if ($this->userproperties->isAllowAnswer())
                $aa = '[allowanswer] ';

            $dt = '';
            $dt2 = $dt;
            if (preg_match('#[0-9]{12}#', $dateTime)) {
                // Transform the dateTime to UTC/GMT
                //$utime = strtotime($dateTime);
                $utc = date('O'); // UTC del servidor
                if (intval($utc) >= 0)
                    $utc = str_replace("+", "-", $utc);
                else
                    $utc = str_replace("-", "+", $utc);
                $dt = 'D';
                $dt2 = $dateTime . $utc . ' ';
            }

            $cs = $this->userproperties->getCustomizedSender();
            if ($cs != '') {
                $mode = 'F' . $mode;
                $cs = $cs . ' ';
            }

            // Por defecto lo desactivo
            $custom_cert = '';
            $tmp_name = $this->userproperties->getCertName();
            $tmp_name_id = $this->userproperties->getCertNameID();
            if (!empty($tmp_name)) {
                $custom_cert = ' [cert_name=' . $tmp_name . ']';
            }

            if (!empty($tmp_name_id)) {
                $custom_cert .= ' [cert_name_id=' . $tmp_name_id . '] ';
            }

            $countRecipients = count($recipients);
            $v = new BulkSend($countRecipients, $idEnvio);
            $this->dadesEnvioMultiple[$idEnvio] = $v;

            // Iniciem la transaccio
            $this->sendToSocketAndSave('TRANS INICIAR', $idEnvio);

            $j = 0;
            $MAX = 0;
            $textToSend = '';
            $blnNextText = false;
            if (is_array($text) && count($text) > 0) {
                $blnNextText = true;
                $textToSend = $text[$j];
                $MAX = count($text);
            }
            else {
                $textToSend = $text;
            }

            for ($i = 0; $i < $countRecipients; $i++) {
                // Enviem el missatge
                $this->sendToSocketAndSave($dt . $mode . 'SUBMIT' . $custom_cert . $mtid . $aa . $dt2 . $cs . $recipients[$i] . ' ' . $textToSend, $idEnvio);

                // Si hi ha mes RECIPIENTS que TEXT envia l'ultim.
                if ($blnNextText && $j < $MAX) {
                    $j++;
                    $textToSend = $text[$j];
                }
            }

            // Finalitzem la transaccio
            $this->sendToSocketAndSave('TRANS FIN', $idEnvio);
        }

        private function doWapLinkTrans($idEnvio, $subject, $URL, $recipients) {
            $v = new BulkSend(count($recipients), $idEnvio);
            $this->dadesEnvioMultiple[$idEnvio] = $v;

            $mtid = ' ';
            if (!empty($idEnvio)) {
                $mtid = ' [id=' . $idEnvio . '] ';
            }

            $countRecipients = count($recipients);
            $v = new BulkSend($countRecipients, $idEnvio);
            $this->dadesEnvioMultiple[$idEnvio] = $v;

            // Iniciem la transaccio
            $this->sendToSocketAndSave('TRANS INICIAR', $idEnvio);

            // Si URL es un unico string envia ese string,
            // Si es un array, enviara elemento a elemento, si hay mas destinatarios que URL
            // se les enviara la ultima URL del array.
            $j = 0;
            $MAX = 0;
            $urlToSend = '';
            if (is_array($URL) && count($URL) > 0) {
                $urlToSend = $URL[$j];
                $MAX = count($URL);
            }
            else
                $urlToSend = $URL;

            // Enviem els missatges
            for ($i = 0; $i < $countRecipients; $i++) {
                // Enviem el missatge
                $this->sendToSocketAndSave('WAPLINK' . $mtid . $recipients[$i] . ' ' . $urlToSend . ' ' . $subject, $idEnvio);
                // Si hi ha mes RECIPIENTS que TEXT envia l'ultim.
                if ($j < $MAX) {
                    $j++;
                    $urlToSend = $URL[$j];
                }
            }

            // Finalitzem la transaccio
            $this->sendToSocketAndSave('TRANS FIN', $idEnvio);
        }

        private function completeTrans($split) {
            $id = $this->getIDbyTag($split[0]);
            if (strtoupper($split[3]) == 'OK') {
                if (array_key_exists($id, $this->dadesEnvioMultiple)) {
                    $this->setCredit(floatval($split[4] . '.' . $split[5]));
                    $v = $this->dadesEnvioMultiple[$id];
                    if (!$v->isEmptyFailRecipient()) {
                        if ($v->isAllSendFailed()) {
                            // Si tots els destinataris eren erronis
                            $this->events->reply($id, SMSMASS_NOOK_ALL_RECIPIENT_INVALID);
                        }
                        else {
                            // Si alguns dels destinataris eren erronis
                            $this->events->reply($id, SMSMASS_OK_ANY_RECIPIENT_INVALID, $v->getFailedsRecipients());
                        }
                    }
                    else {
                        // Todo el envio correcto
                        $this->events->reply($id, SMSMASS_OK);
                    }

                    $this->unsetFromBulkArray($id);
                    $this->unsetFromCommandArray($split[0]);
                }
                else {
                    $this->events->reply($id, SMSMASS_NOOK_ABNORMAL_ERROR);
                }
            }
            else if (strtoupper($split[3]) == 'NOOK') {
                $id = $this->getIDbyTag($split[0]);
                if (count($split) == 6) {
                    // x RTRANS FIN NOOK Transaccion inexistente
                    if (stristr($split[5], 'inexistente') != FALSE) {
                        $this->events->reply($id, SMSMASS_NOOK_INVALID_TRANS);
                    }
                    else {
                        $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                    }
                }
                elseif (count($split) == 8) {
                    // x RTRANS FIN NOOK La fecha es incorrecta
                    if (stristr($split[5], 'fecha') != FALSE) {
                        $this->events->reply($id, SMSMASS_NOOK_INVALID_DATE);
                    }
                    else {
                        $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                    }
                }
                elseif (count($split) == 10) {
                    // x RTRANS FIN NOOK No se dispone de saldo suficiente
                    if (stristr($split[8], 'saldo') != FALSE) {
                        $this->events->reply($id, SMSMASS_NOOK_INSUFFICIENT_CREDIT);
                    }
                    else {
                        $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                    }
                }
                else {
                    $this->events->reply($id, SMSMASS_NOOK_GENERIC_ERROR);
                }

                // Borrem les dades de l'envio
                $this->unsetFromBulkArray($id);
                $this->unsetFromCommandArray($split[0]);
            }
            $this->isTrans = false;
        }

        private function isAnyEmpty($s) {
            if (isset($s)) {
                $blnEmpty = false;
                $countRecipients = count($s);
                for ($i = 0; $i < $countRecipients && !$blnEmpty; $i++) {
                    if (empty($s[$i]) || $s[$i] == '')
                        $blnEmpty = true;
                }
                return $blnEmpty;
            }
            else
                return true;
        }

        // Con el control de los numeros me cargo el comando REJDST
        private function sendNDST($idEnvio, $r, $n) {
            if (is_array($r)) {
                $telefons = '';
                $ini = 1;
                $i = 0;

                if (count($r) <= $n) {
                    foreach ($r as $v) {
                        $v = $this->getValidNumber($v);
                        if (!empty($v)) {
                            $telefons .= $v . ' ';
                        }
                    }
                    $this->sendToSocketAndSave('DST ' . $telefons, $idEnvio);
                }
                else {
                    foreach ($r as $v) {
                        $v = $this->getValidNumber($v);
                        if (!empty($v)) {
                            $telefons .= $v . ' ';
                        }
                        if ($i % $n == 0) {
                            if ($ini != 1) {
                                $this->sendToSocketAndSave('DST ' . $telefons, $idEnvio);
                                $telefons = '';
                            }
                            $ini = 0;
                        }
                        $i++;
                    }

                    if ($telefons != '') {
                        $this->sendToSocketAndSave('DST ' . $telefons, $idEnvio);
                    }
                }
            }
            else {
                $r = $this->getValidNumber($r);
                if (!empty($r)) {
                    $this->sendToSocketAndSave('DST ' . $r, $idEnvio);
                }
            }
        }

    }

} // if(!class_exists('VirtualSMS'))
?>