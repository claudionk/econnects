<?php
Class Colaborador_Parceiro_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'colaborador_parceiro';
    protected $primary_key = 'colaborador_parceiro_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();
    
    //Dados
    public $validate = array(
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'colaborador_id',
            'label' => 'Colaborador',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    /**
     * Realiza update nos parceiros
     * @param $colaborador_id
     * @param $parceiros
     * @return bool
     */
    public function updateParceiros($colaborador_id, $parceiros)
    {
        $this->delete_by(array('colaborador_id' => $colaborador_id));

        foreach($parceiros as $parceiro)
        {
            if(!$this->insert(array(
                'colaborador_id' => $colaborador_id,
                'parceiro_id' => $parceiro
            ), true))
                return false;
        }
        return true;
    }

    public function getParceirosPorColaborador($colaborador_id)
    {
        $this->load->model("parceiro_model", "parceiro");
        $parceiros = $this->parceiro->get_all();
        $parceiros_colaborador = $this->get_many_by(array('colaborador_id' => $colaborador_id));

        $array = array();
        $i = 0;
        foreach($parceiros as $parceiro)
        {
            $array[] = $parceiro;
            $array[$i]['selecionado'] = false;

            foreach($parceiros_colaborador as $pc)
            {
                if($parceiro['parceiro_id'] == $pc['parceiro_id'])
                {
                    $array[$i]['selecionado'] = true;
                }
            }
            $i++;
        }

        return $array;
    }

}
