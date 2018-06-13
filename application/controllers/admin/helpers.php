<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helpers extends Admin_Controller {


    public function buscar_cep($cep){

        $this->load->model('localidade_estado_model', 'estado');
        $this->load->model('localidade_cidade_model', 'cidade');


        $result = array(
            'success' => false,
            'data' => array()
        );
        $cep = app_clear_number($cep);


        if(strlen($cep) == 8){

            $url = "http://cep.republicavirtual.com.br/web_cep.php?cep={$cep}&formato=json";

            $data = (array) json_decode(file_get_contents($url));


            if(isset($data['resultado']) && $data['resultado'] > 0 ){


                if($data['uf'] != ''){

                    $estado = $this->estado->get_by_sigla($data['uf']);

                    if($estado){

                        $data['estado_id'] = $estado['localidade_estado_id'];
                        $data['estado_uf'] = $estado['sigla'];

                        $cidades = $this->cidade->getCidadesPorEstado($data['estado_id']);
                        $data['cidades'] = $cidades;


                    }else {

                        $data['estado_id'] = 0;
                        $data['cidades'] = array();
                    }


                }

                if($data['cidade'] != ''){


                    $estado = $this->cidade->get_by_nome($data['cidade']);

                    if($estado){

                        $data['cidade_id'] = $estado['localidade_cidade_id'];

                    }else {

                        $data['cidade_id'] = 0;
                    }


                }
                $result['data'] = $data;
                $result['success'] = true;
            }
        }


        echo json_encode($result);


    }



}
