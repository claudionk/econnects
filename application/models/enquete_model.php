
<?php
class Enquete_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = "enquete";
    protected $primary_key = "enquete_id";

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    public $validate = array(

        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'titulo',
            'label' => 'Título',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'texto_inicial',
            'label' => 'Texto inicial',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'texto_final',
            'label' => 'Texto final',
            'rules' => 'required',
            'groups' => 'default',
        ),
//        array(
//            'field' => 'data_corte',
//            'label' => 'Data de Corte',
//            'rules' => 'required',
//            'groups' => 'default',
//            'type' => 'date',
//        ),

    );

    /**
     * Insere form
     * @return bool
     */
    public function insert_form($array = array())
    {
        $this->load->model("Enquete_pergunta_model", "enquete_pergunta");
        $data = $this->get_form_data();


        $data = array_merge($data, $array);

        $id = parent::insert_form($data);

        if($id)
        {
            $this->sub_insert("enquete_pergunta", "enquete_pergunta", 'enquete_id', $id);
        }

        return $id;
    }

    /**
     * Update form
     * @return bool
     */
    public function update_form($array = array())
    {
        $this->load->model("Enquete_pergunta_model", "enquete_pergunta");

        $data = $this->get_form_data();

        $data = array_merge($data, $array);
        $update = parent::update_form($data);

        if($update)
        {
            $id = $this->input->post($this->primary_key);
            $this->sub_insert("enquete_pergunta", "enquete_pergunta", 'enquete_id', $id);
        }

        return $update;
    }


}

