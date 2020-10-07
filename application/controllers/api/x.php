<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class X extends CI_Controller {

    public function __construct() {
        parent::__construct();

        header( "Access-Control-Allow-Origin: *" );
        header( "Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS" );
        header( "Access-Control-Allow-Headers: Cache-Control, APIKEY, apikey, Content-type" );
        header( "Content-Type: application/json");

        $method = $_SERVER["REQUEST_METHOD"];
        if( $method == "OPTIONS" ) {
            die();
        }

        $this->load->database('default');

        $this->load->model('apolice_model', 'apolice');
        $this->load->model('apolice_status_model', 'apolice_status');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model("fatura_model", "fatura");
        $this->load->model("fatura_parcela_model", "fatura_parcela");

        $this->load->helper("api_helper");
    }

    public function geraendosso() {
        
        $this->load->model("apolice_endosso_model", "apolice_endosso");

      $sql = "
        SELECT p.pedido_id, a.num_apolice, a.apolice_id, p.produto_parceiro_pagamento_id, am.apolice_movimentacao_tipo_id
            from pedido p join apolice a on p.pedido_id = a.pedido_id
            join apolice_movimentacao am on a.apolice_id = am.apolice_id and am.deletado = 0
            where a.deletado = 0 and p.deletado = 0
            #and a.num_apolice in('710202630000027','710202630000028','710202630000029','710202630000030')
            #and a.apolice_id IN(301965, 301966, 301967, 301980, 301982, 301983, 301985, 301986, 301987, 301988, 301989, 301990)
            #and a.apolice_id IN(311964, 311966, 311968, 311040, 311041, 311969, 311143, 311774, 311811)
            #and a.apolice_id = 311143
            #and a.apolice_id IN(311964, 311040, 311143)
            and a.apolice_id IN(314462,314467)
            order by a.apolice_id, am.apolice_movimentacao_tipo_id
      ";
      $q = $this->db->query($sql)->result_array();

      foreach($q as $ap)
      {
        print_r($ap['num_apolice']);

        $pedido_id = $ap['pedido_id'];
        $apolice_id = $ap['apolice_id'];
        $produto_parceiro_pagamento_id = $ap['produto_parceiro_pagamento_id'];
        $apolice_movimentacao_tipo_id = $ap['apolice_movimentacao_tipo_id'];

        $this->apolice_endosso->insEndosso(($apolice_movimentacao_tipo_id == 1 ? 'A' : 'C'), $apolice_movimentacao_tipo_id, $pedido_id, $apolice_id, $produto_parceiro_pagamento_id);
        echo "<br>";
      }

        die(json_encode(array("status" => true, "message" => "Inserido com sucesso")));
    }



    public function geraendossocancelamento() {
        
        $this->load->model("apolice_endosso_model", "apolice_endosso");

      $sql = "
        SELECT p.pedido_id, a.num_apolice, a.apolice_id, p.produto_parceiro_pagamento_id
            from pedido p join apolice a on p.pedido_id = a.pedido_id
            where a.deletado = 0 and p.deletado = 0
            #and a.num_apolice in('710202630000027','710202630000028','710202630000029','710202630000030')
            #and a.apolice_id IN(301965, 301966, 301967, 301980, 301982, 301983, 301985, 301986, 301987, 301988, 301989, 301990)
            #and a.apolice_id IN(311964, 311966, 311968, 311040, 311041, 311969, 311143, 311774, 311811)
            #and a.apolice_id = 311143
            #and a.apolice_id IN(311964, 311040, 311143)
            and a.apolice_id IN(314631)
            order by a.apolice_id
      ";
      $q = $this->db->query($sql)->result_array();

      foreach($q as $ap)
      {
        print_r($ap['num_apolice']);

        $pedido_id = $ap['pedido_id'];
        $apolice_id = $ap['apolice_id'];
        $produto_parceiro_pagamento_id = $ap['produto_parceiro_pagamento_id'];
        $apolice_movimentacao_tipo_id = 2;

        $this->apolice_endosso->insEndosso(($apolice_movimentacao_tipo_id == 1 ? 'A' : 'C'), $apolice_movimentacao_tipo_id, $pedido_id, $apolice_id, $produto_parceiro_pagamento_id);
        echo "<br>";
      }

        die(json_encode(array("status" => true, "message" => "Inserido com sucesso")));
    }

}







