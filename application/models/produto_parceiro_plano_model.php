<?php
Class Produto_Parceiro_Plano_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_plano';
    protected $primary_key = 'produto_parceiro_plano_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'descricao');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo_operadora',
            'label' => 'Código Operadora',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'produto_parceiro_id',
            'label' => 'Produto / Parceiro',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'produto_parceiro'
        ),
        array(
            'field' => 'moeda_id',
            'label' => 'Moeda',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'moeda'
        ),
        array(
            'field' => 'precificacao_tipo_id',
            'label' => 'Tipo de Precificação',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'unidade_tempo',
            'label' => 'Unidade de Tempo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'limite_vigencia',
            'label' => 'Limite de Vigência',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'passivel_upgrade',
            'label' => 'Passível de upgrade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'ordem',
            'label' => 'Ordem',
            'rules' => '',
            'groups' => 'default'
        ),
    );

    /**
     * Busca planos com esta destino
     * @param $localidade_id
     * @return $this
     */
    public function with_destino($localidade_id)
    {
        $this->_database->join("produto_parceiro_plano_destino", "produto_parceiro_plano_destino.produto_parceiro_plano_id = {$this->_table}.{$this->primary_key}");
        $this->_database->where("produto_parceiro_plano_destino.localidade_id = {$localidade_id}");
        $this->_database->where("produto_parceiro_plano_destino.deletado = 0");
        return $this;
    }


    public function with_faixa_salarial($faixa_salarial_id)
    {
        $this->_database->join("produto_parceiro_plano_faixa_salarial", "produto_parceiro_plano_faixa_salarial.produto_parceiro_plano_id = {$this->_table}.{$this->primary_key}");
        $this->_database->where("produto_parceiro_plano_faixa_salarial.faixa_salarial_id = {$faixa_salarial_id}");
        $this->_database->where("produto_parceiro_plano_faixa_salarial.deletado = 0");
        return $this;
    }

    /**
     * Busca planos com esta destino
     * @param $localidade_id
     * @return $this
     */
    public function with_origem($localidade_id)
    {
        $this->_database->join("produto_parceiro_plano_origem", "produto_parceiro_plano_origem.produto_parceiro_plano_id = {$this->_table}.{$this->primary_key}");
        $this->_database->where("produto_parceiro_plano_origem.localidade_id = {$localidade_id}");
        $this->_database->where("produto_parceiro_plano_origem.deletado = 0");
        return $this;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_produto_parceiro(){
        $this->with_simple_relation('produto_parceiro', 'produto_parceiro_', 'produto_parceiro_id', array('nome'));
        return $this;
    }

    function with_produto(){
        $this->_database->join("produto", "produto_parceiro.produto_id = produto.produto_id");
        $this->_database->where("produto.deletado = 0");
        return $this;
    }

    function  filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);

        return $this;
    }

    function coreSelectPlanosProdutoParceiro($produto_parceiro_id){
        $this->_database->select("{$this->_table}.produto_parceiro_plano_id");
        $this->_database->select("{$this->_table}.produto_parceiro_id");
        $this->_database->select("{$this->_table}.nome");
        $this->_database->select("{$this->_table}.descricao");
        $this->_database->select("{$this->_table}.codigo_operadora");
        $this->_database->select("{$this->_table}.limite_vigencia");
        $this->_database->select("{$this->_table}.unidade_tempo as limite_vigencia_unidade ");
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        return $this;
    }

    function coreSelectPlanosProduto(){
        $this->_database->select("{$this->_table}.*");
        $this->_database->select("produto.produto_id");
        $this->_database->select("produto.produto_ramo_id");
        $this->_database->select("produto.nome");
        $this->_database->select("produto.slug");
        $this->_database->join('produto_parceiro', "{$this->_table}.produto_parceiro_id = produto_parceiro.produto_parceiro_id");
        $this->_database->join('produto', "produto.produto_id = produto_parceiro.produto_id");

        return $this;
    }

    /**
     * Faz O Calculo da Vigência
     * @param $produto_parceiro_plano
     * @param $data_base
     */

    public function getInicioFimVigencia($produto_parceiro_plano_id, $data_base){

        $produto_parceiro_plano = $this->get($produto_parceiro_plano_id);

        $data_base = explode('-', $data_base);

        if(($produto_parceiro_plano['unidade_tempo'] == 'MES')) {
            $date_inicio = date('Y-m-d', mktime(0,0,0, $data_base[1] + $produto_parceiro_plano['inicio_vigencia'], $data_base[2], $data_base[0]));
            $data_base2 = explode('-', $date_inicio);
            $date_fim = date('Y-m-d', mktime(0,0,0, $data_base2[1] + $produto_parceiro_plano['limite_vigencia'], $data_base2[2], $data_base2[0]));
        }elseif($produto_parceiro_plano['unidade_tempo'] == 'ANO'){
            $date_inicio = date('Y-m-d', mktime(0,0,0, $data_base[1], $data_base[2], $data_base[0] + $produto_parceiro_plano['inicio_vigencia']));
            $data_base2 = explode('-', $date_inicio);
            $date_fim = date('Y-m-d', mktime(0,0,0, $data_base2[1], $data_base2[2], $data_base2[0] + $produto_parceiro_plano['limite_vigencia']));
        }else{
            $date_inicio = date('Y-m-d', mktime(0,0,0, $data_base[1], $data_base[2] + $produto_parceiro_plano['inicio_vigencia'], $data_base[0]));
            $data_base2 = explode('-', $date_inicio);
            $date_fim = date('Y-m-d', mktime(0,0,0, $data_base2[1], $data_base2[2] + $produto_parceiro_plano['limite_vigencia'], $data_base2[0]));
        }


        return array(
            'inicio_vigencia' => $date_inicio,
            'fim_vigencia' => $date_fim,
            'dias' => app_date_get_diff_mysql($date_inicio, $date_fim, 'D')
        );


    }
}

