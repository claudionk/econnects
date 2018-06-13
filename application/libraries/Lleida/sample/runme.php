<?php

/*
 *  runme
 *  Created on 2011.06.21
 *  (c) 2011 Lleida Networks Serveis Telematics
 * 
 */

error_reporting(E_ALL);

define('SMS_USER', 'user');
define('SMS_PASS', 'pass');
include_once('SendMessages.php');

$send = new SendMessages();

// -----------------------------------------------------------------------------
// Se envia un SMS
// -----------------------------------------------------------------------------
$id = 'id13';
$txt = "TESTSMS";
$dst = "+346522";
$send->sendOneSMS($id, $txt, $dst);

// -----------------------------------------------------------------------------
// Se envian multiples SMS's
// -----------------------------------------------------------------------------
$id = 1;
$txt = "TESTSMS multiple";
$dst = array("+346522", "+34444");
$send->sendMultipleSMS($id, $txt, $dst);

// -----------------------------------------------------------------------------
// Se envia un MMS
// -----------------------------------------------------------------------------
$id = 2;
$txt = "TESTMMS";
$dst = "+346522";
$subject = "SUBJECT";
$mimeType = "image/png";
$file = "snail.png";

if (!file_exists($file)) {
    echo "No existe el PNG";
}
else {
    $send->sendOneMMS($id, $subject, $txt, $dst, $mimeType, $file);
}

// -----------------------------------------------------------------------------
// Espero a tener todos los reply del envio
// -----------------------------------------------------------------------------
while ($send->countPendingMessages() != 0 && !$send->isConnected()) {
    sleep(1);
}

$send->disconnect();
?>