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
        $this->load->model('apolice_model', 'model');
    }

    public function teste($apolice_id){
        $comunicacao = new Comunicacao();
        $comunicacao->enviaCronMensagens();
        exit();
        $pedidoSQL = "select * from apolice WHERE apolice_id = $apolice_id";
        $pedido = $this->db->query($pedidoSQL)->row();
        $output = $this->model->insertSeguroEquipamento($pedido->pedido_id);
        print_r($output);
    }

}