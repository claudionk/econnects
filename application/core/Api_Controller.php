<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_Controller extends MY_Controller
{
    protected $parceiro;
    protected $usuario;
    protected $login;
    protected $login_mensagem;

    function __construct()
    {
        parent::__construct();

        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
        $this->output->set_header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Access-Control-Request-Method, Authorization');


        /*
         *
         header( "Access-Control-Allow-Origin: *" );
    header( "Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS" );
    header( "Access-Control-Allow-Headers: Origin, Content-Type, Accept, Access-Control-Request-Method, Authorization" );
         *
         */

        $this->load->model("parceiro_model", "parceiro_model");
        $this->load->model("usuario_model", "usuario_model");
        $this->load->model("usuario_webservice_model", "webservice_model");
        $this->load->library("Response");

        $codigo_acesso = $this->input->get_post("chave_acesso");
        $senha = $this->input->get_post("senha");
        $cpf = $this->input->get_post("cpf");
        $api_key = $this->input->get_post("api_key");

        // @todo retirar
//        $codigo_acesso = '59850237b1b9dce52fada8b90f99a83e';
//        $senha = '5175dwy4nummtpbo8dhtd20zxo';
        //$cpf = '305.566.468-00';
       // $api_key = '9f0ee936-227d-11e6-ae90-000c29226d79';
       // $api_key = '1b73251b-2280-11e6-ae90-000c29226d79';


        if($codigo_acesso && $senha){


            $parceiro = $this->parceiro_model->get_by(array(
                    'api_key' => $codigo_acesso,
                    'api_senha' => $senha
                )
            );

            if($parceiro)
            {
                if($cpf){
                    $usuario = $this->usuario_model->get_by(array(
                            'parceiro_id' => $parceiro['parceiro_id'],
                            'cpf' => $cpf
                        )
                    );

                    if($usuario) {
                        $this->usuario = $usuario;
                        $this->parceiro = $parceiro;
                        $this->login = true;
                    }else{
                        $this->login = false;
                        $this->login_mensagem = "Acesso negado, Usuário não cadastrado";
                    }
                }else{
                    $this->parceiro = $parceiro;
                    $this->login = true;
                    $this->session->set_userdata("parceiro_id", $parceiro['parceiro_id']);
                }

            }
            else
            {
                $this->login = false;
                $this->login_mensagem = "Acesso negado, parceiro inválido";
            }
        }else{
            if($api_key){


                $webservice = $this->webservice_model->getByAPI_KEY($api_key);

                $usuario = $this->usuario_model->get($webservice['usuario_id']);

                $parceiro = $this->parceiro_model->get($usuario['parceiro_id']);


                if($usuario && $webservice) {
                    $this->usuario = $usuario;
                    $this->parceiro = $parceiro;
                    $this->login = true;
                }else{
                    $this->login = false;
                    $this->login_mensagem = "Acesso negado, Usuário não encontrado";
                }


            }else{
                $this->login = false;
                $this->login_mensagem = "Acesso negado, API_KEY não informado";
            }
        }



        $response = new Response();
        if($this->login == false){
            $response->setDados(array());
            $response->setStatus(false);
            $response->setMensagem($this->login_mensagem);
            //Output
            exit($response->getJSON());
        }

    }
}
