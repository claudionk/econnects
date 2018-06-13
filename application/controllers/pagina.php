<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pagina extends Site_Controller {


    public function index()
    {
        $data = array();
        /*$this->template->load('site/layouts/base', "site/home/index", $data );*/

        echo "Page";
    }
    public function teste()
    {
        $data = array();
        /*$this->template->load('site/layouts/base', "site/home/index", $data );*/

        echo "Page / Teste";
    }
}
