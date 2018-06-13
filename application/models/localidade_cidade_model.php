<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Veiculo_Cor_Model
 *
 */
class Localidade_Cidade_Model extends MY_Model
{
    protected $_table = 'localidade_cidade';
    protected $primary_key = 'localidade_cidade_id';

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
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'localidade_estado_id',
            'label' => 'Estado',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'localidade_estado'
        )
    );

    public function __construct()
    {
        parent::__construct();

        $this->load->model("localidade_model", "localidade");
    }

    protected $tipo = "cidade";

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
            'localidade_estado_id' => $this->input->post('localidade_estado_id')
        );
        return $data;
    }
    public function getEstadoIdByCidadeId($id)
    {
        //Efetua Query
        $this->db->select($this->_table. '.localidade_estado_id');
        $this->db->from($this->_table);
        $this->db->where($this->_table. '.' . $this->primary_key, $id);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            $row = $query->result_array();
            return $row[0]['localidade_estado_id'];
        }
        else
        {
            return null;
        }
    }
    public function getEstadoById($id)
    {
        //Efetua Query
        $this->db->select($this->_table. '.localidade_estado_id ,'.$this->_table.'.nome');
        $this->db->from($this->_table);
        $this->db->where($this->_table. '.' . $this->primary_key, $id);
        $query = $this->db->get();
        
        if($query->num_rows() > 0)
        {
            $row = $query->result_array();
            return $row[0];
        }
        else
        {
            return null;
        }
    }
    //Seleciona cidades por estado e região
    public function getCidadesPorEstado($idEstado)
    {
        //Efetua Query
        $this->db->select($this->_table. '.localidade_cidade_id ,'.$this->_table.'.nome');
        $this->db->from($this->_table);
        $this->db->where($this->_table. '.localidade_estado_id', (int)$idEstado);
        $query = $this->db->get();
        
        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return null;
        }
    }

    public function get_by_nome($sigla){

        $this->_database->where('nome', $sigla);
        $this->_database->limit(1);

        $rows = $this->get_all();


        if ($rows) {

            return $rows[0];
        }else{
            return null;
        }


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

}