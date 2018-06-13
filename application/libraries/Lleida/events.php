<?php
/**
 * events.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.5
 * 
 */
include_once("lib/logger.php");

if(!class_exists('Events')) {
    define('VSMS_EVENTS_VERSION','1.5');
    define('E_DEBUG', true); // Set to false, if you don't want debuger info

    class Events {
        var $log;
        var $objID;

        function Events() {
            $this->objID = $this->getObjectID(12);
            $this->log = new Logger('vsms');
            if(E_DEBUG) $this->log->debug('[ Event    ] '.$this->objID.' Object created!');
        }

        /**
         * It is made when the connection to the server is&nbsp; established&nbsp; and the user has identified himself correctly with the system.
         * <br>
         * <br>
         * <b>Comments</b><br>
         * <br>
         * Make use of the <b>Connected</b> event to confirm that both connection and identification have been done successfully.
         */
        function connected() {
            if(E_DEBUG) $this->log->debug('[ Event    ] '.$this->objID.' Connected to host!');
        }

        /**
         * It ends the connection with the SMS sending server
         */
        function disconnected() {
            if(E_DEBUG) $this->log->debug('[ Event    ] '.$this->objID.' Disconnected! See you soon.');
        }

        /**
         * It occurs when either the server or the operator notify a delivery receipt. <br>
         * To receive this event it is necessary to set the <b>MailDeliveryReceipt</b>&nbsp;property to the "INTERNAL" value.<br>
         * @param timeStamp Delivery receipt unixtimestamp arrival.<br/> <br/>
         * @param sendTimeStamp Unixtimestamp when the message was sent to the server.
         * @param recipient The message recipient from whom we receive the receipt.<br/> <br/>
         * @param text Written text sent in the message.<br/> <br/>
         * @param status The <b>status</b> parameter
         * @param id The submit <b>id</b> 
         */
        function deliveryReceipt($timeStamp, $sendTimeStamp, $recipient, $text, $status, $id='') {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' ID: '.$id);
                $this->log->debug('[ Event    ] '.$this->objID.' Timestamp: '.$timeStamp);
                $this->log->debug('[ Event    ] '.$this->objID.' SendTimestamp: '.$sendTimeStamp);
                $this->log->debug('[ Event    ] '.$this->objID.' Recipient: '.$recipient);
                $this->log->debug('[ Event    ] '.$this->objID.' Text: '.$text);
                $this->log->debug('[ Event    ] '.$this->objID.' Status: '.$status);
            }
        }        

        /**
         * It occurs when either the server or the operator notify a delivery receipt. <br>
         * To receive this event it is necessary to set the <b>MailDeliveryReceipt</b>&nbsp;property to the "INTERNAL" value.<br>
         * @param timeStamp Delivery receipt unixtimestamp arrival.<br/> <br/>
         * @param sendTimeStamp Unixtimestamp when the message was sent to the server.
         * @param recipient The message recipient from whom we receive the receipt.<br/> <br/>
         * @param status The <b>status</b> parameter
         * @param id The submit <b>id</b> 
         */
        function deliveryReceiptMMS($timeStamp, $sendTimeStamp, $recipient, $status, $id='') {
            //tag ACUSEMMS <idacuse> <+dst> <ux_data_acuse> <estat_acuse> <ux_data_envio>
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' ID: '.$id);
                $this->log->debug('[ Event    ] '.$this->objID.' Timestamp: '.$timeStamp);
                $this->log->debug('[ Event    ] '.$this->objID.' SendTimestamp: '.$sendTimeStamp);
                $this->log->debug('[ Event    ] '.$this->objID.' Recipient: '.$recipient);
                $this->log->debug('[ Event    ] '.$this->objID.' Status: '.$status);
            }
        }
        
        /**
         * It occurs when you receive a text message sent to a long number by a user, via the SMS server.
         * <br/>
         * @param idIncomingMo The unique identifier from the received message.
         * @param timeStamp Unixtimestamp message delivery.<br/> <br/>
         * @param sender Message sender phone number. International format: +34666666666<br/> <br/>
         * @param recipient SMS destination phone number. International format: +34666666666<br/> <br/>
         * @param text Received SMS text.<br/> <br/>
         */
        function incomingMo($idIncomingMo, $timeStamp, $sender, $recipient, $text) {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' Timestamp: '.$timeStamp);
                $this->log->debug('[ Event    ] '.$this->objID.' Incoming MO ID: '.$idIncomingMo);
                $this->log->debug('[ Event    ] '.$this->objID.' Sender: '.$sender);
                $this->log->debug('[ Event    ] '.$this->objID.' Recipient: '.$recipient);
                $this->log->debug('[ Event    ] '.$this->objID.' Text: '.$text);
            }
        }

        /**
         * It occurs when you receive a text message sent to a long number by a user, via the SMS server.
         * <br/>
         * @param $idIncomingMMS The unique identifier from the received message.
         * @param timeStamp Unixtimestamp message delivery.<br/> <br/>
         * @param sender Message sender phone number. International format: +34666666666<br/> <br/>
         * @param recipient MMS destination phone number. International format: +34666666666<br/> <br/>
         */
        function incomingMMS($idIncomingMMS, $timeStamp, $sender, $recipient) {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' Timestamp: '.$timeStamp);
                $this->log->debug('[ Event    ] '.$this->objID.' Incoming MMS ID: '.$idIncomingMMS);
                $this->log->debug('[ Event    ] '.$this->objID.' Sender: '.$sender);
                $this->log->debug('[ Event    ] '.$this->objID.' Recipient: '.$recipient);
            }
        }
        
        /**
         * It occurs when receiving the message status confirmation after being sent through SendBinarySMS, SendEMSLogo, SendNokiaGroupLogo, SendNokiaOperatorL or SendTextSMS method.
         * @param idEnvio Sending Identifier. This ID corresponds to the sent ID in any of the methods used to carry out the sending.<br/><br/>
         * @param status Message status. <br/>The Status parameter may take these values in Reply function<br/>
         * @param data Additional data to the Status code. If there is no data the parameter will be NULL.<br/>
         *
         * <br/><br/><b>Notice.</b> Data parameter is received when the code obtained in the Status parameter is SMSMASS_OK_ANY_RECIPIENT_INVALID. This variable has a string array where each element belongs to an invalid recipient.<br/><br/>
         */
        function reply($idEnvio, $status, $data=null) {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' Send ID: '.$idEnvio);
                $this->log->debug('[ Event    ] '.$this->objID.' Status: '.$status);
                if(isset($data) && is_array($data)) {
                    foreach($data as $v) {
                        $this->log->debug('[ Event    ] '.$this->objID.' INVALID RECIPIENT : '.$v);
                    }
                }
            }
        }

        /**
         * It occurs when any error is generated.
         *
         * <br/><br/>In most cases the very moment an error occurs, the connection to the server is closed, except for the cases SMSMASS_ERR_ANOTHER_CONNECTION_IN_PROGRESS and SMSMASS_ERR_PROTOCOL_ERROR.
         * @param errCode Error code generated<br/>
         * The ErrCode parameter may take the following values:
         * @param errMsg Generated error description<br/><br/>
         */
        function connectionError($errCode, $errMsg) {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' Code error: '.$errCode);
                $this->log->debug('[ Event    ] '.$this->objID.' Error: '.$errMsg);
            }
        }

        /**
         * It occurs when the balance is lower than 1 credit.
         * <br/>
         * Although this event shows us a balance lower than 1 credit, it is possible to consult the Credit property at any time to know the current balance.
         * <br/>
         */
        function noCredit() {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' No credit! ');
            }
        }

        /**
         * It occurs when the GetOperatorInfo method reply is received. It shows us the MCC code (Mobile Country Code) and MNC (Mobile Network Code) of the requested phone number or prefix.
         * <br/>
         * <br/>MCC and MNC codes may be obtained from the web gsmworld.com.
         * @param phoneOrPrefix Text specifying phone/prefix in international format corresponding to received MCC and MNC codes. Examples: "+34615666666" or "+34615".<br/>
         * <br/>
         * @param MCC Country Code (Mobile Country Code).<br/>
         * <br/>
         * @param MNC Network Code(Mobile Network Code).
         * <br/>
         */
        function operatorInfo($phoneOrPrefix, $MCC, $MNC) {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' Phone number or prefix: '.$phoneOrPrefix);
                $this->log->debug('[ Event    ] '.$this->objID.' MCC: '.$MCC);
                $this->log->debug('[ Event    ] '.$this->objID.' MNC: '.$MNC);
            }
        }

        // <etiq> CHECKALLOK <saldo restante (entera)> <saldo (decimal)>
        // <etiq> RCHECKALL <id> <timestamp> <numero> <ok> <rcode> <mcc> <mnc>
        // <etiq> RCHECKALLACK <id>
        function checkall($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc) {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' Timestamp: '.$timeStamp);
                $this->log->debug('[ Event    ] '.$this->objID.' Check ID: '.$id);
                $this->log->debug('[ Event    ] '.$this->objID.' Num: '.$num);
                $this->log->debug('[ Event    ] '.$this->objID.' OK: '.$ok);
                $this->log->debug('[ Event    ] '.$this->objID.' RCODE: '.$rcode);
                $this->log->debug('[ Event    ] '.$this->objID.' MCC: '.$mcc);
                $this->log->debug('[ Event    ] '.$this->objID.' MNC: '.$mnc);
                $this->log->debug('[ Event    ] '.$this->objID.' SPID: '.$spid);
                $this->log->debug('[ Event    ] '.$this->objID.' CC: '.$cc);
            }
        }

        // <etiq> CHECKNETWORKOK <saldo restante (entera)> <saldo (decimal)>
        // <etiq> RCHECKNETWORK <id> <timestamp> <numero> <ok> <rcode> <mcc> <mnc>
        // <etiq> RCHECKNETWORKACK <id>
        function checknetwork($id, $timeStamp, $num, $ok, $rcode, $mcc, $mnc, $spid, $cc) {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' Timestamp: '.$timeStamp);
                $this->log->debug('[ Event    ] '.$this->objID.' Check ID: '.$id);
                $this->log->debug('[ Event    ] '.$this->objID.' Num: '.$num);
                $this->log->debug('[ Event    ] '.$this->objID.' OK: '.$ok);
                $this->log->debug('[ Event    ] '.$this->objID.' RCODE: '.$rcode);
                $this->log->debug('[ Event    ] '.$this->objID.' MCC: '.$mcc);
                $this->log->debug('[ Event    ] '.$this->objID.' MNC: '.$mnc);
                $this->log->debug('[ Event    ] '.$this->objID.' SPID: '.$spid);
                $this->log->debug('[ Event    ] '.$this->objID.' CC: '.$cc);
            }
        }
        
        // Checker fuera de servicio temporalmente
        function serviceUnavailable(){
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' Service unavailable');
            }
        }

        // <etiq> RTARIFA <codigo tarifa> <precio MT> <acuse> <remitente>
        function price($price) {
            if(E_DEBUG) {
                $this->log->debug('[ Event    ] '.$this->objID.' Tarifa: '.$price);
            }
        }

        private function getObjectID($len) {
            // Only for debugger info
            if(!isset($this->objID) || $this->objID == '') {
                return substr(md5(rand(0,999)), 0, $len);
            }
            else {
                return $this->objID;
            }
        }
    }
} // if(!class_exists('Events'))
?>