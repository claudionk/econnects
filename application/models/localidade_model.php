<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Veiculo_Cor_Model
 *
 */
class Localidade_Model extends MY_Model {


    protected $_table = 'localidade';
    protected $primary_key = 'localidade_id';

    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    protected $soft_delete_key = 'deletado';

    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');

    public $validate = array(
        array(
            'field' => 'tipo',
            'label' => 'Tipo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'id',
            'label' => 'ID',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    /**
     * Retorna por grupo
     * @return array
     */
    public function get_all_by_group()
    {
        $data = array();
        //$data['cidade'] = $this->get_many_by(array('tipo' => 'cidade'));
        $data['estado'] = $this->get_many_by(array('tipo' => 'estado'));
        $data['pais'] = $this->get_many_by(array('tipo' => 'pais'));
        $data['continente'] = $this->get_many_by(array('tipo' => 'continente'));

        return $data;
    }

    /**
     * Retorna pelo plano do parceiro
     * @param $parceiro_plano_id
     * @param string $tipo
     */
    public function get_by_parceiro($parceiro_plano_id, $tipo = 'origem')
    {
        $this->_database->distinct();
        $this->_database->select("localidade.*");
        $this->_database->from("produto_parceiro_plano plano");
        $this->_database->join("produto_parceiro_plano_{$tipo}", "produto_parceiro_plano_{$tipo}.produto_parceiro_plano_id = plano.produto_parceiro_plano_id AND produto_parceiro_plano_{$tipo}.deletado = 0");
        $this->_database->join("localidade", "localidade.localidade_id = produto_parceiro_plano_{$tipo}.localidade_id");
        $this->_database->where("plano.produto_parceiro_id = {$parceiro_plano_id}");

        $query = $this->_database->get();
        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return array();
    }


    /**
     * Atualiza dados do banco
     */
    public function atualiza_dados()
    {
        //Models
        $this->load->model("localidade_cidade_model", "localidade_cidade");
        $this->load->model("localidade_estado_model", "localidade_estado");
        $this->load->model("localidade_pais_model", "localidade_pais");
        $this->load->model("localidade_continente_model", "localidade_continente");

        $cidades = $this->localidade_cidade->get_all();
        $estados = $this->localidade_estado->get_all();
        $paises = $this->localidade_pais->get_all();
        $continentes = $this->localidade_continente->get_all();

        $this->_database->query("delete from {$this->_table}");


        foreach($cidades as $registro)
        {
            $data = array();
            $data['tipo'] = 'cidade';
            $data['id'] = $registro['localidade_cidade_id'];
            $data['nome'] = $registro['nome'];

            if(!$this->insert($data))
                return false;
        }

        foreach($estados as $registro)
        {
            $data = array();
            $data['tipo'] = 'estado';
            $data['id'] = $registro['localidade_estado_id'];
            $data['nome'] = $registro['nome'];

            if(!$this->insert($data))
                return false;
        }

        foreach($paises as $registro)
        {
            $data = array();
            $data['tipo'] = 'pais';
            $data['id'] = $registro['localidade_pais_id'];
            $data['nome'] = $registro['nome'];

            if(!$this->insert($data))
                return false;
        }

        foreach($continentes as $registro)
        {
            $data = array();
            $data['tipo'] = 'continente';
            $data['id'] = $registro['localidade_continente_id'];
            $data['nome'] = $registro['nome'];

            if(!$this->insert($data))
                return false;
        }
    }

}