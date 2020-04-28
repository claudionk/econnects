<?php
Class Capitalizacao_Serie_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'capitalizacao_serie';
    protected $primary_key = 'capitalizacao_serie_id';

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
            'label' => 'Número de início',
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
            'field' => 'ativo',
            'label' => 'ativo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'serie_aberta',
            'label' => 'Tipo de Série',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_inicio',
            'label' => 'Início Distribuição',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_fim',
            'label' => 'Fim Distribuição',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
        array(
            'field' => 'num_serie',
            'label' => 'Número da Série',
            'rules' => 'callback_required_if[responsavel_num_sorte,1,Número da Série]',
            'groups' => 'default'
        ),
        array(
            'field' => 'solicita_range',
            'label' => 'Solicitar Range de Número da Sorte',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $len = strlen($this->input->post('numero_inicio'));
        $data =  array(
            'capitalizacao_id' => $this->input->post('capitalizacao_id'),
            'numero_inicio' => $this->input->post('numero_inicio'),
            'numero_fim' => str_pad((int)$this->input->post('numero_inicio') + (int)$this->input->post('quantidade'), $len, "0", STR_PAD_LEFT ) ,
            'quantidade' => app_retorna_numeros($this->input->post('quantidade')),
            'ativo' => $this->input->post('ativo'),
            'serie_aberta' => $this->input->post('serie_aberta'),
            'num_serie' => emptyor($this->input->post('num_serie'), 0),
            'solicita_range' => $this->input->post('solicita_range'),
            'data_inicio' => app_dateonly_mask_to_mysql($this->input->post('data_inicio')),
            'data_fim' => app_dateonly_mask_to_mysql($this->input->post('data_fim')),
        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_capitalizacao($capitalizacao_id){
        $this->_database->where("{$this->_table}.capitalizacao_id", $capitalizacao_id);
        return $this;
    }

    function filter_by_ativo($ativo){
        $this->_database->where("{$this->_table}.ativo", $ativo);
        return $this;
    }

    function filter_by_codigo_interno($codigo_interno){
        $this->_database->join("capitalizacao", "{$this->_table}.capitalizacao_id = capitalizacao.capitalizacao_id");
        $this->_database->where("capitalizacao.codigo_interno", $codigo_interno);
        $this->_database->where("capitalizacao.deletado", 0);
        return $this;
    }

    function updateRangeSolicitada( $capitalizacao_serie_id )
    {
        $sql = "
            UPDATE capitalizacao c
            INNER JOIN capitalizacao_serie cs ON c.capitalizacao_id = cs.capitalizacao_id
            SET cs.solicita_range = 0
            WHERE cs.capitalizacao_serie_id = $capitalizacao_serie_id AND c.deletado = 0 AND cs.deletado = 0 AND cs.solicita_range = 2;
        ";
        $result = $this->_database->query($sql);
    }

    public function existNumSorte($capitalizacao_serie_id, $numero_sorte)
    {
        $sql = "
            SELECT capitalizacao_serie.capitalizacao_serie_id
            FROM capitalizacao
            INNER JOIN capitalizacao_serie ON capitalizacao.capitalizacao_id = capitalizacao_serie.capitalizacao_id
            INNER JOIN capitalizacao_serie_titulo ON capitalizacao_serie.capitalizacao_serie_id = capitalizacao_serie_titulo.capitalizacao_serie_id 
                AND capitalizacao_serie_titulo.numero = '{$numero_sorte}'
                AND capitalizacao_serie_titulo.ativo = 1
            WHERE capitalizacao_serie.capitalizacao_serie_id = {$capitalizacao_serie_id}
            AND capitalizacao_serie.ativo = 1
            AND capitalizacao_serie.deletado = 0
        ";

        $result = $this->_database->query($sql);
        return !empty($result->num_rows());
    }

}
