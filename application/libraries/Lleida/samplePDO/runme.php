<?php

/*
 *  instancia
 *  Created on 2011.06.21
 *  (c) 2011 Lleida Networks Serveis Telematics
 * 
 */

error_reporting(E_ALL);

define('SMS_USER', 'user');
define('SMS_PASS', 'pass');
define('PASSPHRASE', 'xms2;433jm,xz(10!)ds1d');

// Variables de configuracion de la base de datos
define('PDO_HOST', 'localhost');
define('PDO_NAME', 'your_database');
define('PDO_USER', 'your_user');
define('PDO_PASS', 'your_pass');

include_once('SendMessages.php');

$send = new SendMessages();

$send->sendAll();

while($send->countPendingMessages() != 0 && !$send->isConnected()){
    // Espero a tener todos los reply del envio
    sleep(5);
}

$send->disconnect();

?>
