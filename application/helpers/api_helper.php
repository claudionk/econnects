<?php

function api_retira_timestamps($arr)
{
    $retira = array('deletado','criacao','alteracao_usuario_id','alteracao');

    foreach($retira as $r)
        unset($arr[$r]);

    return $arr;
}