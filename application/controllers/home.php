<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Site_Controller 
{
	public function index()
	{
            $data = array();
			redirect('/admin');
            //$this->template->load('site/layouts/base', "site/home/index", $data );
	}
}
