<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Veiculo_Cor_Model
 *
 */
class Produto_Parceiro_Plano_Origem_Model extends MY_Model {


    protected $_table = 'produto_parceiro_plano_origem';
    protected $primary_key = 'produto_parceiro_plano_origem_id';

    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    protected $soft_delete_key = 'deletado';

    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();


    public $validate = array(
        array(
            'field' => 'produto_parceiro_plano_id',
            'label' => 'Plano',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'produto_parceiro_plano'
        ),
        array(
            'field' => 'localidade_id',
            'label' => 'Localidade',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'localidade'
        ),
    );


    function coreSelectLocalidadeProdutoParceiro($produto_parceiro_plano_id){
        $this->_database->select("localidade.localidade_id");
        $this->_database->select("localidade.tipo");
        $this->_database->select("localidade.nome");
        $this->_database->join('localidade', "{$this->_table}.localidade_id = localidade.localidade_id");
        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        return $this;
    }

    /**
     * Truncate nos dados com validação
     * @param $data
     * @param $validation
     * @return null
     */
    public function atualiza_localidade($rows, $plano_id)
    {
        $this->delete_by(array('produto_parceiro_plano_id' => $plano_id));

        foreach($rows as $localidade)
        {
            $data = array();
            $data['localidade_id'] = $localidade;
            $data['produto_parceiro_plano_id'] = $plano_id;

            if($this->validate($data))
            {
                if(!parent::insert($data, true))
                    return false;
            }
        }

        return true;
    }

}