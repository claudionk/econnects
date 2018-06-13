<?php

require_once("events.php");

exit("AQUI!");
class Lleida {

    private $ci;

    function __construct()
    {
        $this->ci = &get_instance();
    }

}