<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    /**
     * Realiza upload
     * @param $pasta
     * @param $name
     * @return bool
     */
    public function upload($pasta, $name)
    {
        $pasta = UPLOAD_PATH . $pasta;

        //Caso diretório não exista ele cria
        if(!file_exists($pasta))
        {
            mkdir($pasta, 0777, true);
        }

        //Carrega configurações
        $config['upload_path'] = $pasta;
        $config['allowed_types'] = UPLOAD_PERMITIDO;
        $config['max_size'] = 0;
        $config['encrypt_name'] = true;

        //Carrega biblioteca de upload
        $this->load->library('upload', $config);

        //Reseta erros
        $this->upload->error_msg = array();

        $multi = false;

        if($_FILES && is_array($_FILES[$name]['name']))
        {
            //Realiza upload
            $this->upload->do_multi_upload($name);

            //Realiza upload da imagem
            $file = $this->upload->get_multi_upload_data();
            $multi = true;
        }
        else
        {
            //Realiza upload
            $this->upload->do_upload($name);

            //Realiza upload da imagem
            $file = $this->upload->data();
        }


        //Caso retorne erros
        if ($this->upload->display_errors())
        {
            //var_dump($this->upload->display_errors());exit;
            return false; //Seta nulo
        }
        else
        {
            return $file['file_name']; //Retorna nome da imagem
        }
    }


    function __construct()
    {
        parent::__construct();
        $this->template->set('title', '');
        $this->template->set('meta_keywords', '');
        $this->template->set('meta_description', '');
    }
}