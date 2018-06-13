<?php
/* 
 *  PDOConfig
 *  Created on 2009.06.08
 *  @author David Tapia (c) 2009 LleidaNetworks Serveis Telem&agrave;tics, S.L.
 *  @version 1.1
 */


if(!defined('PDO_ENGINE')){
    define('PDO_ENGINE', 'mysql');
}

if(!defined('PDO_HOST')){
    define('PDO_HOST', 'localhost');
}

if(!defined('PDO_NAME')){
    define('PDO_NAME', 'your_database');
}

if(!defined('PDO_USER')){
    define('PDO_USER', 'your_user');
}

if(!defined('PDO_PASS')){
    define('PDO_PASS', 'your_pass');
}

class PDOConfig extends PDO {
    public function __construct() {
        $dns = PDO_ENGINE.':dbname='.PDO_NAME.";host=".PDO_HOST;
        try {
            parent::__construct($dns, PDO_USER, PDO_PASS);
        }
        catch (PDOException $e) {
        }
    }
}
?>