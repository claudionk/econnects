<?php
Class Cobertura_Plano_Model extends MY_Model {
    //Dados da tabela e chave primária
    protected $_table = 'cobertura_plano';
    protected $primary_key = 'cobertura_plano_id';

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
            'field' => 'cobertura_id',
            'label' => 'Cobertura',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'porcentagem',
            'label' => 'Porcentagem (%)',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'preco',
            'label' => 'Valor',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'mostrar',
            'label' => 'Exibição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'custo',
            'label' => 'Custo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'cobertura_custo',
            'label' => 'Cobertura Tipo Custo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'iof',
            'label' => 'IOF (%)',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'usar_iof',
            'label' => 'Usar IOF',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'diarias',
            'label' => 'Diárias',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'franquia',
            'label' => 'Franquia',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'carencia',
            'label' => 'Carência',
            'rules' => '',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false) {

        //Dados
        $data =  array(
            'cobertura_id' => $this->input->post('cobertura_id'),
            'produto_parceiro_plano_id' => $this->input->post('produto_parceiro_plano_id'),
            'parceiro_id' => $this->input->post('parceiro_id'),
            'porcentagem' => app_unformat_currency($this->input->post('porcentagem')),
            'preco' => app_unformat_currency($this->input->post('preco')),
            'custo' => app_unformat_currency($this->input->post('custo')),
            'mostrar' => $this->input->post('mostrar'),
            'descricao' => $this->input->post('descricao'),
            'cobertura_custo' => $this->input->post('cobertura_custo'),
            'iof' => app_unformat_currency($this->input->post('iof')),
            'usar_iof' => $this->input->post('usar_iof'),
            'diarias'  => $this->input->post('diarias'),
            'franquia' => $this->input->post('franquia'),
            'carencia' => $this->input->post('carencia')
        );
        return $data;
    }
  
    function get_by_id($id) {
        return $this->get($id);
    }

    function  filter_by_produto_parceiro_plano($produto_parceiro_plano_id) {
        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        return $this;
    }

    //Agrega Coberturas
    function with_cobertura($fields = array('nome')) {
        $this->with_simple_relation('cobertura', 'cobertura_', 'cobertura_id', $fields );
        return $this;
    }
  
    //Agrega Coberturas
    function with_cobertura_tipo($fields = array('cobertura_tipo.nome as cobertura_tipo')) {
        $this->_database->select($fields);
        $this->_database->join('cobertura_tipo', 'cobertura_tipo.cobertura_tipo_id = cobertura.cobertura_tipo_id');
        return $this;
    }

    //Agrega Coberturas
    function with_parceiro($fields = array('parceiro.nome as parceiro_nome')) {
        $this->_database->select($fields);
        $this->_database->join('parceiro', 'parceiro.parceiro_id = cobertura_plano.parceiro_id');
        return $this;
    }

    //Agrega Coberturas
    function select_total_porcentagem() {
        $this->_database->select_sum('porcentagem');
        return $this;
    }

    function with_prod_parc($produto_parceiro_id, $produto_parceiro_plano_id = null){

        $this->_database->select("{$this->_table}.*, cobertura.*, produto_parceiro_plano.*");
        $this->_database->join("cobertura", "cobertura.cobertura_id = {$this->_table}.cobertura_id");
        $this->_database->join("produto_parceiro_plano", "produto_parceiro_plano.produto_parceiro_plano_id = {$this->_table}.produto_parceiro_plano_id");

        $this->_database->where("produto_parceiro_plano.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->where("produto_parceiro_plano.deletado", 0);
        $this->_database->where("cobertura.cobertura_tipo_id", 1);
        if (!empty($produto_parceiro_plano_id)) {
            $this->_database->where("produto_parceiro_plano.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        }

        return $this;
    }

    function filter_adicional_by_cobertura_slug($cotacao_id = null, $slugs = [], $produto_parceiro_plano_id = null){

        $this->_database->join("cobertura", "cobertura.cobertura_id = {$this->_table}.cobertura_id");
        $this->_database->join("produto_parceiro_plano", "produto_parceiro_plano.produto_parceiro_plano_id = {$this->_table}.produto_parceiro_plano_id");
        $this->_database->where("produto_parceiro_plano.deletado", 0);
        $this->_database->where("cobertura.cobertura_tipo_id", 2);
        $this->_database->where_in("cobertura.slug", $slugs);
        if (!empty($produto_parceiro_plano_id)) {
            $this->_database->where("produto_parceiro_plano.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        }
        if (!empty($cotacao_id)) {
            $this->_database->join("cotacao", "produto_parceiro_plano.produto_parceiro_id = cotacao.produto_parceiro_id");
            $this->_database->where("cotacao.cotacao_id", $cotacao_id);
        }

        return $this;
    }

    function with_prod_parc_iof($produto_parceiro_plano_id){

        $this->_database->where("produto_parceiro_plano_id", $produto_parceiro_plano_id);
        $this->_database->where("usar_iof", 1);

        return $this;
    }

    public function getCoberturasApolice($apolice_id)
    {
        $sql = "
        select *, premio_liquido + valor_iof as premio_liquido_total
        from (

        select 
        cobertura_plano.cod_cobertura,
        cobertura.nome as cobertura,
        cobertura_plano.usar_iof,
        cobertura_plano.iof,
        cobertura_plano.diarias,
        cobertura_plano.carencia,
        cobertura_plano.franquia,
        apolice_cobertura.valor AS premio_liquido,
        #TRUNCATE(IF(apolice_cobertura.iof > 0, IF(TRUNCATE(apolice_cobertura.valor * apolice_cobertura.iof / 100,2) = 0, 0.01, apolice_cobertura.valor * apolice_cobertura.iof / 100), 0), 2) AS valor_iof,
        #ROUND(apolice_cobertura.valor + IF(apolice_cobertura.iof > 0, IF(ROUND(apolice_cobertura.valor * apolice_cobertura.iof / 100,2) = 0, 0.01, apolice_cobertura.valor * apolice_cobertura.iof / 100), 0), 2) AS premio_liquido_total,
        IFNULL( IFNULL(apolice_equipamento.nota_fiscal_valor, apolice_generico.nota_fiscal_valor), cobertura_plano.preco) AS importancia_segurada

        #se o IOF é menor que 0.01, joga o valor na maior
        , TRUNCATE(
                IF(
                    TRUNCATE(apolice_cobertura.valor * apolice_cobertura.iof / 100,2) = 0, 
                        IF(
                            menor.apolice_id IS NULL, 
                            IF(apolice_cobertura.iof > 0, 0.01, 0), 
                            IF( menor.apolice_cobertura_id = apolice_cobertura.apolice_cobertura_id, IF( TRUNCATE(menor.valor, 2) = 0, 0.01, menor.valor), 0)
                        ),
                        TRUNCATE(apolice_cobertura.valor * apolice_cobertura.iof / 100,2)
                        
                        #add a diferenca do IOF total à cobertura de +valor
                        + IF( menor.apolice_cobertura_id = apolice_cobertura.apolice_cobertura_id, menor.valor-menor.valor_t, 0)
                )
        ,2) AS valor_iof

        FROM pedido
        INNER JOIN apolice on apolice.pedido_id = pedido.pedido_id
        INNER JOIN apolice_cobertura on apolice.apolice_id = apolice_cobertura.apolice_id
        INNER JOIN cobertura_plano on apolice_cobertura.cobertura_plano_id = cobertura_plano.cobertura_plano_id
        INNER JOIN cobertura on cobertura_plano.cobertura_id = cobertura.cobertura_id
        LEFT JOIN apolice_generico ON apolice_generico.apolice_id = apolice.apolice_id
        LEFT JOIN apolice_equipamento ON apolice_equipamento.apolice_id = apolice.apolice_id

        #caso o IOF seja menor que 0.01, soma as comissoes e identifica a de maior valor
        LEFT JOIN (
            select apolice_id, max(apolice_cobertura_id) as apolice_cobertura_id, valor, valor_t
            from (
                select apolice.apolice_id, ac.apolice_cobertura_id apolice_cobertura_id, IF( ROUND(x.valor, 2) = 0, 0.01, x.valor) as valor, IF( ROUND(x.valor_t, 2) = 0, 0.01, x.valor_t) as valor_t
                from apolice_cobertura ac
                join (
                    select round(sum(IF(apolice_cobertura.iof > 0, apolice_cobertura.valor * apolice_cobertura.iof / 100, 0)), 2) as valor, round(sum(TRUNCATE(IF(apolice_cobertura.iof > 0, apolice_cobertura.valor * apolice_cobertura.iof / 100, 0),2)), 2) as valor_t, max(apolice_cobertura.valor) c
                    FROM pedido
                    INNER JOIN apolice ON apolice.pedido_id = pedido.pedido_id
                    INNER JOIN apolice_cobertura ON apolice.apolice_id = apolice_cobertura.apolice_id
                    LEFT JOIN apolice_generico ON apolice_generico.apolice_id = apolice.apolice_id
                    LEFT JOIN apolice_equipamento ON apolice_equipamento.apolice_id = apolice.apolice_id
                    where apolice.apolice_id = {$apolice_id}
                    and pedido.deletado = 0
                    and apolice.deletado = 0
                    and apolice_cobertura.deletado = 0
                ) x on x.c = ac.valor
                INNER JOIN apolice ON apolice.apolice_id = ac.apolice_id
                where apolice.apolice_id = {$apolice_id}
            ) z  group by apolice_id, valor
        ) AS menor ON apolice.apolice_id = menor.apolice_id

        where 
        pedido.deletado = 0
        and apolice.deletado = 0
        and apolice_cobertura.deletado = 0
        and cobertura_plano.deletado = 0
        and apolice_cobertura.valor > 0
        and apolice.apolice_id = {$apolice_id}
        ) as y
        ";

        $result = $this->db->query($sql)->result_array();
        return $result;
    }

}

