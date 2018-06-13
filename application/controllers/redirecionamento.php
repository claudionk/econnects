<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Redirecionamento extends Site_Controller
{

    public function carrega()
    {
        $this->load->library('encrypt');

        $url = rawurldecode($this->input->get("url"));
        $token = rawurldecode($this->input->get("token"));

        if($this->input->get("obj"))
        {
            $obj = rawurldecode($this->input->get("obj"));
            $obj = $this->encrypt->decode($obj);
            $obj = json_decode($obj, true);

            if(isset($obj['urls_pode_acessar']))
            {
                $this->session->set_userdata("urls_pode_acessar", $obj['urls_pode_acessar']);
            }
        }


        $url = $this->encrypt->decode($url);
        $token = $this->encrypt->decode($token);

        redirect($url . "?token={$token}&layout=" . issetor($obj['layout'], '') . "&context=" .issetor($obj['context'], '') );
    }

}
