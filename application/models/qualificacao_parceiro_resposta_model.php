<?php
Class Qualificacao_Parceiro_Resposta_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'qualificacao_parceiro_resposta';
    protected $primary_key = 'qualificacao_parceiro_resposta_id';

    //Configurações
    protected $return_type = 'array';

    /**
     * Desabilita o softdelete
     * @var bool
     */
    protected $soft_delete = FALSE;

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
            'field' => 'respostas',
            'label' => 'Respostas',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'qualificacao_parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {

        $respostas = $this->input->post('respostas');
        $qualificacao_parceiro_id = $this->input->post('qualificacao_parceiro_id');

        $data = array();

        foreach($respostas as $qualificacao_questao_id =>  $resposta){

            if(isset($resposta['qualificacao_questao_opcao_id']) && $resposta['qualificacao_questao_opcao_id'] !== '' ){

                $data[] = array(
                    'qualificacao_questao_id' => $qualificacao_questao_id,
                    'qualificacao_questao_opcao_id' => $resposta['qualificacao_questao_opcao_id'],
                    'valor_exato' => $resposta['valor_exato'],
                    'qualificacao_parceiro_id' => $qualificacao_parceiro_id,


                );
            }

        }



        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function insert_form()
    {
        $data = $this->get_form_data();
        $this->delete_by_qualificacao_parceiro($this->input->post('qualificacao_parceiro_id'));
        $this->insert_array($data);
    }


    function delete_by_qualificacao_parceiro($qualificacao_parceiro_id){

        return $this->delete_by( array('qualificacao_parceiro_id' => $qualificacao_parceiro_id));

    }

    function get_all_by_qualificacao_parceiro($qualificacao_parceiro_id)
    {


        $this->_database->where('qualificacao_parceiro_id', $qualificacao_parceiro_id );
        $rows = $this->get_all();

        $data = array();

        foreach($rows as $row){

            $data[$row['qualificacao_questao_id']] = $row;

        }

        return $data;
    }


}
