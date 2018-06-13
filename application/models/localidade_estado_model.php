<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Veiculo_Cor_Model
 *
 */
class Localidade_Estado_Model extends MY_Model {


    protected $_table = 'localidade_estado';
    protected $primary_key = 'localidade_estado_id';

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
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'localidade_pais_id',
            'label' => 'País',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'sigla',
            'label' => 'Sigla',
            'rules' => 'required|max_lenght[2]',
            'groups' => 'default'
        ),
    );

    protected $tipo = "estado";

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
   
    public function get_form_data($just_check = false){

        $data =  array(
            'nome' => $this->input->post('nome'),
            'localidade_pais_id' => $this->input->post('localidade_pais_id'),
            'sigla' => $this->input->post('sigla'),
        );
        return $data;
    }
    
    function get_by_range($perPage, $page)
    {
        $query = $this->db->get('localidade_estado', $perPage, (($page-1) * $perPage));
        return $query->result();
    }
    public function getNome($id)
    {
        $this->_database->select($this->_table. '.nome');
        $this->_database->from($this->_table);
        $this->_database->where($this->_table. '.' .$this->primary_key, $id);
        $this->_database->limit(1);

        $query = $this->_database->get();

        if ($query->num_rows() == 1) {
            $data = $query->result_array();
            return $data[0]['nome'];
        }else{
            return null;
        }
    }

    public function get_by_sigla($sigla){

        $this->_database->where('sigla', $sigla);
        $this->_database->limit(1);

        $rows = $this->get_all();


        if ($rows) {

            return $rows[0];
        }else{
            return null;
        }


    }

}