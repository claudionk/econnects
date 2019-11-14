    <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Impressao extends Site_Controller
{
    public function encode ($apolice_id)
    {
        $this->load->library('encrypt');

        $en = $this->encrypt->encode($apolice_id);
        echo base64_encode($en);

    }

    public function certificado()
    {
        $this->load->model('apolice_model', 'apolice');

        //Bibliotecas
        $this->load->library('parser');
        $this->load->library('encrypt');

        $data_template = array();

        $apolice_id = $this->encrypt->decode(base64_decode($this->input->get("apolice_id")));
        $apolice = $this->apolice->getApolice($apolice_id);

        if(count($apolice) == 0)
        {
            $this->session->set_flashdata('fail_msg', 'Apólice não esta liberado'); //Mensagem de sucesso
            exit("Código inválido. Informe nossa equipe.");
        }

        $result = $this->apolice->certificado($apolice_id, 'pdf');
        if($result !== FALSE){
            exit($result);
        }
    }


    public function certificado_api($apolice_id)
    {
        $this->load->model('apolice_model', 'apolice');

        $apolice = $this->apolice->getApolice($apolice_id);

        if(count($apolice) == 0)
        {
            $this->session->set_flashdata('fail_msg', 'Apólice não esta liberado'); //Mensagem de sucesso
            exit("Código inválido. Informe nossa equipe.");
        }

        $result = $this->apolice->certificado($apolice_id, 'pdf');
        if($result !== FALSE){
            exit($result);
        }
    }
}
