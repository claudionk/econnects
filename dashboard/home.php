<?php
    $ambiente = (isset($_GET['ambiente']) && $_GET['ambiente'] <> '') ? $_GET['ambiente'] : 'PRODUCAO';        
    require_once('./app/model/conn.php');
    require_once('./app/controller/controller.php');

    $controller = "dashboard";
    if (isset($_GET['c'])){
        $controller = $_GET['c'];
    }

    $data = call_user_func($controller);
    if (@$_GET['f'] == 'json') {
        header('Context-type: text/json');
        echo json_encode($data);
    } 
?>