<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Veiculo_Cor_Model
 *
 */
class Produto_Parceiro_Plano_Faixa_Salarial_Model extends MY_Model {


    protected $_table = 'produto_parceiro_plano_faixa_salarial';
    protected $primary_key = 'produto_parceiro_plano_faixa_salarial_id';

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
            'field' => 'faixa_salarial_id',
            'label' => 'Faixa Salarial',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'faixa_salarial'
        ),
    );


    function coreSelectFaixaSalarialProdutoParceiro($produto_parceiro_plano_id){
        $this->_database->select("faixa_salarial.faixa_salarial_id");
        $this->_database->select("faixa_salarial.descricao");
        $this->_database->select("faixa_salarial.inicio");
        $this->_database->select("faixa_salarial.fim");
        $this->_database->select("faixa_salarial.ordem");
        $this->_database->join('faixa_salarial', "{$this->_table}.faixa_salarial_id = faixa_salarial.faixa_salarial_id");
        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        return $this;
    }

    /**
     * Truncate nos dados com validação
     * @param $data
     * @param $validation
     * @return null
     */
    public function atualiza_faixa_salarial($rows, $plano_id)
    {


        $this->delete_by(array('produto_parceiro_plano_id' => $plano_id));


        foreach($rows as $faixa_salarial)
        {
            $data = array();
            $data['faixa_salarial_id'] = $faixa_salarial;
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