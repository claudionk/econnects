<?php

Class App_autoload {

   public function __construct(){

       $ci = & get_instance();
       $ci->load->model('log_evento_model', 'log_evento');
       $ci->load->library('custom_loader');
   }

}