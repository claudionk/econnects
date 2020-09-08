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
        $pedidoSQL = "select * from apolice WHERE apolice_id = $apolice_id";
        $pedido = $this->db->query($pedidoSQL)->row();
        $this->model->executa_estorno_cancelamento($pedido->pedido_id);
        exit();
        $comunicacao = new Comunicacao();
        $comunicacao->teste = true;
        $comunicacao->enviaCronMensagens();
        exit();
        
    }

}