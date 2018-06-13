<?php

/**
 * bulksend.php
 * @author David Tapia (c) 2010 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.0
 */
if (!class_exists('SaveCommand')) {
    define('VSMS_SaveCommand_VERSION', '1.0');

    class SaveCommand {

        // ID del envio MT_USER_ID
        var $id = 0;
        // tag del comando, es un entero
        var $tag = 0;
        // El comando enviado
        var $cmdSend = '';

        function SaveCommand($id, $tag, $cmd) {
            $this->id = $id;
            $this->tag = $tag;
            $this->cmdSend = $cmd;
        }

        function getTag() {
            return $this->tag;
        }

        function getID() {
            return $this->id;
        }

        function getCommand() {
            return $this->cmdSend;
        }

    }

} // if(!class_exists('SaveCommand'))
?>