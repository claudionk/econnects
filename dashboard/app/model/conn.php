<?php      
    $conn = null;
    if ($ambiente == 'PRODUCAO') {
        try {
            $conn = new PDO('mysql:host=191.243.196.9;dbname=sisconnects;charset=utf8', "cakira", "sis#1234", 
                            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        }
        catch (PDOException $e) {
            print $e->getMessage();
        } 
    }elseif($ambiente == 'HOMOLOGACAO'){
        try {
            $conn = new PDO('mysql:host=191.243.196.35;dbname=sisconnects;charset=utf8', "root", "EAQdmh91181", 
                            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        }
        catch (PDOException $e) {
            print $e->getMessage();
        } 
    }else{
        print('<br>Não foi possível conectar no ambiente!');
    }
    //print('<br>Conectado em: ' . $ambiente);
?>