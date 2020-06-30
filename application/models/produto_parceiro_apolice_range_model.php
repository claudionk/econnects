<?php
Class Produto_Parceiro_Apolice_Range_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_apolice_range';
    protected $primary_key = 'produto_parceiro_apolice_range_id';

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
            'field' => 'numero_inicio',
            'label' => 'Número inicial',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'quantidade',
            'label' => 'Quantidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'habilitado',
            'label' => 'Habilitado',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $seq = $this->getSequencialVariavel($this->input->post('numero_inicio'), $this->input->post('quantidade'));

        $data =  array(
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
            'numero_inicio' => $this->input->post('numero_inicio'),
            'sequencia' => $this->input->post('numero_inicio'),
            'numero_fim' => $seq['start'] . $seq['end'],
            'quantidade' => app_retorna_numeros($this->input->post('quantidade')),
            'habilitado' => $this->input->post('habilitado'),
        );

        return $data;
    }

    function getSequencialVariavel($numero_inicio, $qtde, $data_template = [])
    {
        $this->load->library('parser');

        if (empty($data_template))
        {
            $data_template = [
                'sigla_loja' => '',
                'cod_sucursal' => '',
                'cod_ramo' => '',
                'cod_operacao' => '',
                'cod_produto' => '',
                'ano_AA' => '',
                'ano_AAAA' => '',
                'mes_MM' => '',
            ];
        }

        $onlyNumber = $this->parser->parse_string($numero_inicio, $data_template, true);
        $othersFields = str_replace($onlyNumber, '', $numero_inicio);
        $len = strlen($onlyNumber);

        return [
            'start' => $othersFields,
            'end' => str_pad((int)$onlyNumber + (int)$qtde, $len, "0", STR_PAD_LEFT ),
            'result' => $onlyNumber,
        ];
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    public function get_proximo_codigo($produto_parceiro_id)
    {
        $this->_database->select("{$this->_table}.*");
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->where("{$this->_table}.deletado", 0);
        $this->_database->where("{$this->_table}.habilitado", 1);
        $this->_database->where("{$this->_table}.sequencia < {$this->_table}.numero_fim");
        $this->_database->from($this->_table);
        $this->_database->limit(1);
        $query = $this->_database->get();
        if($query->num_rows() > 0) {
            $row = $query->result_array();
            $row = $row[0];

            $seqS = $this->getSequencialVariavel($row['sequencia'], 1);

            $row['sequencia'] = $seqS['start'] . $seqS['end'];

            $arr_update = array();
            $arr_update['sequencia'] = $row['sequencia'];
            $this->update($row['produto_parceiro_apolice_range_id'], $arr_update, TRUE);

            return $arr_update['sequencia'];
        }else{
            return 'NNNNNNNNNN';
        }
    }

    function filter_by_produto_parceiro($produto_parceiro_id)
    {
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        return $this;
    }

}
