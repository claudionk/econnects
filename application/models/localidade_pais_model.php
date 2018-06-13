<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Veiculo_Cor_Model
 *
 */
class Localidade_Pais_Model extends MY_Model {


    protected $_table = 'localidade_pais';
    protected $primary_key = 'localidade_pais_id';

    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    protected $soft_delete_key = 'deletado';

    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'sigla');

    public $validate = array(
        array(
            'field' => 'localidade_continente_id',
            'label' => 'Continente',
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

    protected $tipo = "pais";

    public function __construct()
    {
        parent::__construct();

        $this->load->model("localidade_model", "localidade");
    }

    /**
     * Insere em localidade também
     * @param $data
     * @param bool $skip_validation
     * @return bool
     */
    public function insert($data, $skip_validation = FALSE)
    {
        $insert_id = parent::insert($data, $skip_validation);

        $this->localidade->insert(array(
            'tipo' => $this->tipo,
            'nome' => $data['nome'],
            'id' => $insert_id
        ));

        return $insert_id;
    }

    /**
     * Deleta também no localidade
     * @param $id
     */
    public function delete($id)
    {
        $localidade = $this->localidade->get_by(array(
            'tipo' => $this->tipo,
            'id' => $id
        ));

        $this->localidade->delete($localidade['localidade_id']);

        parent::delete($id);
    }

    /**
     * Realiza update em localidade
     * @param $primary_value
     * @param $data
     * @param bool $skip_validation
     * @return bool
     */
    public function update($primary_value, $data, $skip_validation = FALSE)
    {
        $update = parent::update($primary_value, $data, $skip_validation);

        $localidade = $this->localidade->get_by(array(
            'tipo' => $this->tipo,
            'id' => $primary_value
        ));

        $localidade_update = $this->localidade->update($localidade['localidade_id'], array(
            'tipo' => $this->tipo,
            'id' => $primary_value,
            'nome' => $data['nome']
        ));

        return $update;
    }

    function get_by_range($perPage, $page)
    {
        $query = $this->db->get('localidade_estado', $perPage, (($page-1) * $perPage));
        return $query->result();
    }

    public function getNome($id)
    {
        $this->db->select($this->_table. '.nome');
        $this->db->from($this->_table);
        $this->db->where($this->_table. '.' .$this->primary_key, $id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1)
        {
            $data = $query->result_array();
            return $data[0]['nome'];
        }
        else
        {
            return;
        }
    }

}