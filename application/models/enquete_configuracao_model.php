
<?php
class Enquete_configuracao_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = "enquete_configuracao";
    protected $primary_key = "enquete_configuracao_id";

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    public $validate = array(

        array(
            'field' => 'enquete_id',
            'label' => 'Enquete',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'enquete',
        ),
        array(
            'field' => 'envio_tipo',
            'label' => 'Tipo de envio',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'envio_mensagem',
            'label' => 'Mensagem de envio',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'ativo',
            'label' => 'Ativo',
            'rules' => 'required',
            'groups' => 'default',
        ),
    );

    public function get_form_data($just_check = false, $from_post_array = "")
    {
        $data = parent::get_form_data($just_check, $from_post_array);

        return $data;
    }

    public function insert_form()
    {
        $this->load->model("Enquete_gatilho_configuracao_model", "enquete_gatilho_configuracao");

        $data = $this->get_form_data();

        $id =  $this->insert($data, true);

        $gatilhos = $this->input->post("gatilhos");

        if($gatilhos)
        {
            foreach($gatilhos as $gatilho)
            {
                $this->enquete_gatilho_configuracao->insert(array(
                    'enquete_configuracao_id' => $id,
                    'enquete_gatilho_id' => $gatilho
                ), true);
            }
        }

        return $id;
    }

    public function update_form()
    {
        $this->load->model("Enquete_gatilho_configuracao_model", "enquete_gatilho_configuracao");

        $data = $this->get_form_data();

        $gatilhos = $this->input->post("gatilhos");

        if($gatilhos)
        {
            $this->enquete_gatilho_configuracao->delete_by(array(
                'enquete_configuracao_id' => $this->input->post($this->primary_key),
            ));

            foreach($gatilhos as $gatilho)
            {
                $this->enquete_gatilho_configuracao->insert(array(
                    'enquete_configuracao_id' => $this->input->post($this->primary_key),
                    'enquete_gatilho_id' => $gatilho
                ), true);
            }
        }

        return $this->update($this->input->post($this->primary_key), $data, true);
    }

}

