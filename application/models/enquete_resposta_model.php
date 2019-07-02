
<?php
class Enquete_resposta_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = "enquete_resposta";
    protected $primary_key = "enquete_resposta_id";

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    public $validate = array(
        array(
            'field' => 'enquete_configuracao_id',
            'label' => 'Configuração',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'enquete_configuracao',
        ),
        array(
            'field' => 'enquete_id',
            'label' => 'Enquete',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'enquete',
        ),
        array(
            'field' => 'apolice_id',
            'label' => 'ID Apólice',
            'groups' => 'default',
        ),
        array(
            'field' => 'respondido',
            'label' => 'Respondido',
            'groups' => 'default',
        ),
        array(
            'field' => 'data_enviada',
            'label' => 'Data enviada',
            'groups' => 'default',
            'type' => 'date',
        ),
    );

    /**
     * Atualiza um formulário
     * @param $enquete_resposta_id
     */
    public function atualiza_formulario($enquete_resposta_id)
    {
        $this->load->model("Enquete_pergunta_model", "enquete_pergunta");
        $this->load->model("Enquete_resposta_pergunta_model", "enquete_resposta_pergunta");

        $enquete_resposta = $this->get($enquete_resposta_id);

        $uma = false;
        if($enquete_resposta)
        {
            $respostas = $this->input->post("resposta");

            if($respostas)
            {
                $perguntas = $this->enquete_pergunta->get_many_by(array(
                   'enquete_id' => $enquete_resposta['enquete_id']
                ));

                if($perguntas)
                {
                    foreach($perguntas as $pergunta)
                    {
                        $enquete_pergunta_id = $pergunta['enquete_pergunta_id'];
                        $resposta = $this->input->post("resposta[{$enquete_pergunta_id}]");


                        $erp = $this->enquete_resposta_pergunta->get_by(array(
                            'enquete_resposta_id' =>  $enquete_resposta_id,
                            'enquete_pergunta_id' => $enquete_pergunta_id,
                        ));

                        if(!$resposta || empty($resposta))
                            $resposta = null;

                        if(is_array($resposta))
                        {
                            $resposta = implode(",", $resposta);
                        }

                        if($erp)
                        {
                            $this->enquete_resposta_pergunta->update($erp['enquete_resposta_pergunta_id'], array(
                                'enquete_resposta_id' =>  $enquete_resposta_id,
                                'enquete_pergunta_id' => $enquete_pergunta_id,
                                'respondida' => $resposta ? 1 : 0,
                                'resposta' => $resposta,
                            ), true);
                        }
                        else
                        {
                            $this->enquete_resposta_pergunta->insert(array(
                                'enquete_resposta_id' =>  $enquete_resposta_id,
                                'enquete_pergunta_id' => $enquete_pergunta_id,
                                'respondida' => $resposta ? 1 : 0,
                                'resposta' => $resposta,
                            ), true);
                        }

                        $uma = true;
                    }
                }
            }

            //Possui alguma não respondida
            if($this->enquete_resposta_pergunta->get_by(array(
                'enquete_resposta_id' => $enquete_resposta_id,
                'respondida' => 0,
            )))
            {
                $this->enquete_resposta->update($enquete_resposta_id, array(
                    'respondido' => $uma ? 'parcial' : 'nao',
                    'data_respondido' => date("Y-m-d H:i:s"),
                ), true);
            }
            else
            {
                $this->enquete_resposta->update($enquete_resposta_id, array(
                    'respondido' => 'total',
                    'data_respondido' => date("Y-m-d H:i:s"),
                ), true);
            }

            return true;

        }


        return false;
    }

}

