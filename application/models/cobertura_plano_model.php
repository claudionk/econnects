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
        array(
            'field' => 'cod_cobertura',
            'label' => 'Código da Cobertura',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'cod_ramo',
            'label' => 'Código do Ramo',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'cod_produto',
            'label' => 'Código do Produto',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'cod_sucursal',
            'label' => 'Código da Sucursal',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'importancia_segurada',
            'label' => 'Importância Segurada',
            'rules' => '',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false) {

        //Dados
        $data =  array(
            'cobertura_id'              => $this->input->post('cobertura_id'),
            'produto_parceiro_plano_id' => $this->input->post('produto_parceiro_plano_id'),
            'parceiro_id'               => $this->input->post('parceiro_id'),
            'porcentagem'               => app_unformat_currency($this->input->post('porcentagem')),
            'preco'                     => app_unformat_currency($this->input->post('preco')),
            'custo'                     => app_unformat_currency($this->input->post('custo')),
            'mostrar'                   => $this->input->post('mostrar'),
            'descricao'                 => $this->input->post('descricao'),
            'cobertura_custo'           => $this->input->post('cobertura_custo'),
            'iof'                       => app_unformat_currency($this->input->post('iof')),
            'importancia_segurada'      => app_unformat_currency($this->input->post('importancia_segurada')),
            'usar_iof'                  => $this->input->post('usar_iof'),
            'diarias'                   => $this->input->post('diarias'),
            'franquia'                  => $this->input->post('franquia'),
            'carencia'                  => $this->input->post('carencia'),
            'cod_cobertura'             => $this->input->post('cod_cobertura'),
            'cod_ramo'                  => isempty($this->input->post('cod_ramo'), null),
            'cod_produto'               => isempty($this->input->post('cod_produto'), null),
            'cod_sucursal'              => isempty($this->input->post('cod_sucursal'), null),
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
        $this->_database->select("IF(produto_parceiro.parceiro_id = cobertura_plano.parceiro_id, 1, 0) AS cob_resp", FALSE);
        $this->_database->join("cobertura", "cobertura.cobertura_id = {$this->_table}.cobertura_id");
        $this->_database->join("produto_parceiro_plano", "produto_parceiro_plano.produto_parceiro_plano_id = {$this->_table}.produto_parceiro_plano_id");
        $this->_database->join("produto_parceiro", "produto_parceiro.produto_parceiro_id = produto_parceiro_plano.produto_parceiro_id #AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id");

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
        SELECT *, premio_liquido + valor_iof as premio_liquido_total
        FROM (

        SELECT 
        cobertura_plano.cod_cobertura,
        cobertura.nome as cobertura_nome,
        cobertura.slug as cobertura_slug,
        cobertura_plano.usar_iof,
        IF(rp.regra_preco_id IS NOT NULL, IFNULL(pprp.parametros,0), IF(cobertura_plano.usar_iof > 0, apolice_cobertura.iof, IFNULL(pprp.parametros,0))) as iof,
        cobertura_plano.diarias,
        cobertura_plano.carencia,
        cobertura_plano.franquia,
        apolice_cobertura.valor AS premio_liquido,
        apolice_cobertura.data_inicio_vigencia,
        apolice_cobertura.data_fim_vigencia,
        IFNULL( IFNULL(apolice_equipamento.nota_fiscal_valor, apolice_generico.nota_fiscal_valor), cobertura_plano.preco) AS importancia_segurada

       , IF(
            TRUNCATE(IF(rp.regra_preco_id IS NOT NULL, apolice_cobertura.valor * IFNULL(pprp.parametros,0) / 100, IF(apolice_cobertura.iof > 0, apolice_cobertura.valor * apolice_cobertura.iof / 100, 0) ),2)

            #add a diferenca do IOF total à cobertura de +valor
            + IF( menor.apolice_cobertura_id = apolice_cobertura.apolice_cobertura_id, menor.valor-menor.valor_t, 0) = 0,
            
                TRUNCATE(IF( menor.apolice_cobertura_id = apolice_cobertura.apolice_cobertura_id, IF( TRUNCATE(menor.valor, 2) = 0, 0.01, menor.valor), 0), 2)
            ,
                TRUNCATE(IF(rp.regra_preco_id IS NOT NULL, apolice_cobertura.valor * IFNULL(pprp.parametros,0) / 100, IF(apolice_cobertura.iof > 0, apolice_cobertura.valor * apolice_cobertura.iof / 100, 0) ),2)

                #add a diferenca do IOF total à cobertura de +valor
                + IF( menor.apolice_cobertura_id = apolice_cobertura.apolice_cobertura_id, menor.valor-menor.valor_t, 0)
        ) AS valor_iof
        ,IF(produto_parceiro.parceiro_id <> cobertura_plano.parceiro_id, 1, 0) AS assistencia
        FROM pedido
        INNER JOIN apolice ON apolice.pedido_id = pedido.pedido_id
        INNER JOIN produto_parceiro_plano ON apolice.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id
        INNER JOIN produto_parceiro ON produto_parceiro_plano.produto_parceiro_id = produto_parceiro.produto_parceiro_id
        INNER JOIN parceiro ON produto_parceiro.parceiro_id = parceiro.parceiro_id

        LEFT JOIN produto_parceiro_regra_preco pprp ON produto_parceiro_plano.produto_parceiro_id = pprp.produto_parceiro_id AND pprp.deletado = 0
        LEFT JOIN regra_preco rp on pprp.regra_preco_id = rp.regra_preco_id AND rp.slug = 'iof' 

        INNER JOIN apolice_cobertura ON apolice.apolice_id = apolice_cobertura.apolice_id
        INNER JOIN cobertura_plano ON apolice_cobertura.cobertura_plano_id = cobertura_plano.cobertura_plano_id #AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id
        INNER JOIN cobertura ON cobertura_plano.cobertura_id = cobertura.cobertura_id
        LEFT JOIN apolice_generico ON apolice_generico.apolice_id = apolice.apolice_id
        LEFT JOIN apolice_equipamento ON apolice_equipamento.apolice_id = apolice.apolice_id

        #caso o IOF seja menor que 0.01, soma as comissoes e identifica a de maior valor
        LEFT JOIN (
            SELECT apolice_id, max(apolice_cobertura_id) as apolice_cobertura_id, valor, valor_t
            FROM (
                SELECT apolice.apolice_id, ac.apolice_cobertura_id, x.regra_preco_id
                , IF( ROUND(IF(x.regra_preco_id IS NOT NULL, x.valor_por_cob, x.valor), 2) = 0, 0.01, IF(x.regra_preco_id IS NOT NULL, x.valor_por_cob, x.valor)) as valor
                , IF( ROUND(x.valor_t, 2) = 0, 0.01, x.valor_t) as valor_t
                FROM apolice_cobertura ac
                JOIN (
                    SELECT apolice.apolice_id, rp.regra_preco_id,
                        round(sum(IF(rp.regra_preco_id IS NOT NULL,  
                            
                            apolice_cobertura.valor * IFNULL(pprp.parametros,0) / 100
                            ,
                            IF(apolice_cobertura.iof > 0, apolice_cobertura.valor * apolice_cobertura.iof / 100, 0)
                            )
                        ), 2) as valor
                        , round(sum(TRUNCATE(IF(rp.regra_preco_id IS NOT NULL,  
                            
                            apolice_cobertura.valor * IFNULL(pprp.parametros,0) / 100
                            ,
                            IF(apolice_cobertura.iof > 0, apolice_cobertura.valor * apolice_cobertura.iof / 100, 0))
                        ,2)), 2) as valor_t
                        , round(
                            IF(rp.regra_preco_id IS NOT NULL, 
                                IFNULL(IFNULL(apolice_equipamento.pro_labore, apolice_generico.pro_labore),0)
                                , 
                                sum(TRUNCATE(
                                    IF(apolice_cobertura.iof > 0, apolice_cobertura.valor * apolice_cobertura.iof / 100, 0)
                                ,2))
                            ), 2) as valor_por_cob
                        , max(IF(cobertura_plano.cobertura_plano_id IS NOT NULL, apolice_cobertura.valor, 0)) c 
                    FROM pedido
                    INNER JOIN apolice ON apolice.pedido_id = pedido.pedido_id
                    INNER JOIN apolice_cobertura ON apolice.apolice_id = apolice_cobertura.apolice_id
                    INNER JOIN produto_parceiro_plano ppp ON apolice.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
                    INNER JOIN produto_parceiro ON ppp.produto_parceiro_id = produto_parceiro.produto_parceiro_id
                    LEFT JOIN produto_parceiro_regra_preco pprp ON ppp.produto_parceiro_id = pprp.produto_parceiro_id AND pprp.deletado = 0
                    LEFT JOIN regra_preco rp on pprp.regra_preco_id = rp.regra_preco_id AND rp.slug = 'iof' 
                    LEFT JOIN apolice_generico ON apolice_generico.apolice_id = apolice.apolice_id
                    LEFT JOIN apolice_equipamento ON apolice_equipamento.apolice_id = apolice.apolice_id

                    #APENAS COBERTURAS (SEM ASSISTENCIAS)
                    LEFT JOIN cobertura_plano ON apolice_cobertura.cobertura_plano_id = cobertura_plano.cobertura_plano_id AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id

                    WHERE apolice.apolice_id = {$apolice_id}
                        AND pedido.deletado = 0
                        AND apolice.deletado = 0
                        AND apolice_cobertura.deletado = 0
                        AND apolice_cobertura.valor > 0

                    GROUP BY apolice.apolice_id
                ) x ON x.apolice_id = ac.apolice_id AND x.c = ac.valor
                INNER JOIN apolice ON apolice.apolice_id = ac.apolice_id
                
            ) z  GROUP BY apolice_id
        ) AS menor ON apolice.apolice_id = menor.apolice_id

        WHERE 
            pedido.deletado = 0
            AND apolice.deletado = 0
            AND apolice_cobertura.deletado = 0
            AND cobertura_plano.deletado = 0
            AND apolice_cobertura.valor >= 0
            AND apolice.apolice_id = {$apolice_id}
        ) AS y
        ";

        $result = $this->db->query($sql)->result_array();
        return $result;
    }

}

