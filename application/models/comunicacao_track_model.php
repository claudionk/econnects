<?php

class Comunicacao_track_model extends MY_Model
{

//Dados da tabela e chave primária
    protected $_table = "comunicacao_track";
    protected $primary_key = "comunicacao_track_id";

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

    public $validate = array(

        array(
            'field' => 'comunicacao_id',
            'label' => 'Comunicacao id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'comunicacao'
        ),

        array(
            'field' => 'data_hora',
            'label' => 'Data Hora',
            'rules' => 'required',
            'groups' => 'default',
        ),

    );

    public function insert_or_update($cotacao_id){




        $this->load->model('comunicacao_model', 'comunicacao_model');
        $row = $this->with_simple_relation_foreign('comunicacao' , "comunicacao_", "comunicacao_id", "comunicacao_id", array(), 'inner')
            ->where('comunicacao.cotacao_id' , '=', $cotacao_id)->get_all(1, 0, false);

        $sql = "
            select comunicacao_track.*
            from comunicacao_track
            inner join comunicacao on comunicacao.comunicacao_id = comunicacao_track.comunicacao_id
            where comunicacao.cotacao_id={$cotacao_id}
        ";

        $query =$this->_database->query($sql);




        if($query->num_rows() == 0){

            $comunicacao = $this->comunicacao_model->where('comunicacao.cotacao_id' , '=', $cotacao_id)->get_all(0,0,false);

            if($comunicacao){
                $comunicacao = $comunicacao[0];
                $data = array();
                $data['comunicacao_id'] = $comunicacao['comunicacao_id'];
                $data['data_hora'] = date('Y-m-d H:i:s');
                $this->insert($data, TRUE);


            }


        }

    }
}