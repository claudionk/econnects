
<?php
class Enquete_resposta_pergunta_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = "enquete_resposta_pergunta";
    protected $primary_key = "enquete_resposta_pergunta_id";

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    public $validate = array(

        array(
            'field' => 'enquete_resposta_id',
            'label' => 'Resposta',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'enquete_resposta',
        ),
        array(
            'field' => 'enquete_pergunta_id',
            'label' => 'Pergunta',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'enquete_pergunta'
        ),
        array(
            'field' => 'respondida',
            'label' => 'Respondida',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'resposta',
            'label' => 'Resposta',
            'rules' => 'required',
            'groups' => 'default',
        ),
    );

    /**
     * Relacionamento pergunta-resposta
     * @param $enquete_id
     * @return array
     */
    public function relacionamento_pergunta_resposta($enquete_id, $filtra_tipo = false)
    {
        $tipo_filtro = "";

        if($filtra_tipo)
            $tipo_filtro = "and (ep.tipo = 'select' OR ep.tipo = 'zero_a_dez')";



        $sql = "select ep.*, resposta, count(*) as quantidade from enquete_resposta_pergunta erp
            inner join enquete_resposta er on er.enquete_resposta_id = erp.enquete_resposta_id
            inner join enquete_pergunta ep on ep.enquete_pergunta_id = erp.enquete_pergunta_id
            
            where er.enquete_id = {$enquete_id} {$tipo_filtro}
            
            group by enquete_pergunta_id, resposta";

        $query = $this->_database->query($sql);

        $perguntas = $query->result_array();
        $perguntas_group = array();
        if($perguntas)
        {
            foreach($perguntas as $pergunta)
            {
                $perguntas_group[$pergunta['enquete_pergunta_id']][] = $pergunta;
            }
        }


        return $perguntas_group;


    }

}

