<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class anexo extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('pedido_model', 'model');
    }

    public function teste($apolice_id){

    }

}