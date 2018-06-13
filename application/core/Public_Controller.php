<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Public_Controller extends MY_Controller
{
    protected $noLogin = true;

    function __construct()
    {
        parent::__construct();


    }
}