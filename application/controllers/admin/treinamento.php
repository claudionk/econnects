<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Treinamento extends Admin_Controller
{
    protected $layout = "base";

    public function __construct() 
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Treinamento');

        //Seta layout
        $layout = $this->session->userdata("layout");
        $this->layout = isset($layout) && !empty($layout) ? $layout : 'base';
    }
    
    public function index(){
        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/list");
    }

}
