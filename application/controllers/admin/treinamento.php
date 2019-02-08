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
        $data['url'] = "https://novogestao.woli.com.br/pt-BR/Login/Index?returnUrl='/'&idUnidadeHierarquica=1&redirecionaIndex=1&login=23814358325";

        // $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/list", $data);
        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/manutencao", $data);
    }

    public function webinar(){
        $data['url'] = "http://bit.ly/2NP66Fj";
        // $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/list", $data);
        $this->template->load("admin/layouts/{$this->layout}", "$this->controller_uri/manutencao", $data);
    }

}
