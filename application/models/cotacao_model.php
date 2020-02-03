<?php

Class Cotacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cotacao';
    protected $primary_key = 'cotacao_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;
    
    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('motivo', 'motivo_ativo', 'motivo_obs');

    //Dados
    public $validate = array(
        array(
            'field' => 'cotacao_status_id',
            'label' => 'Status',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'cotacao_status',
        ),

        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'parceiro'
        ),
        array(
            'field' => 'cliente_id',
            'label' => 'Cliente',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'cliente'
        ),
        array(
            'field' => 'codigo',
            'label' => 'Código',
            'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field' => 'cotacao_tipo',
            'label' => 'Tipo de cotação',
            'rules' => 'required',
            'groups' => 'default',
        ),

    );

    public function get_cotacao_produto($cotacao_id){

        $cotacao = $this->with_produto_parceiro()->get($cotacao_id);
        if (empty($cotacao)) return null;

        $produto_slug = $cotacao['produto_slug'];
        switch ($produto_slug) {
            case 'seguro_viagem':
                $cotacao = $this->with_produto_parceiro()->with_cotacao_seguro_viagem()->get($cotacao_id);
                break;
            case 'equipamento':
                $cotacao = $this->with_produto_parceiro()->with_cotacao_equipamento()->get($cotacao_id);
                break;
            case 'generico':
                $cotacao = $this->with_produto_parceiro()->with_cotacao_generico()->get($cotacao_id);
                break;
        }

        $cotacao['produto_slug'] = $produto_slug;
        return $cotacao;

    }


    /**
     *
     */
    public function with_clientes_contatos()
    {
        //$this->_database->select("cotacao_seguro_viagem.*");
        //$this->_database->select("cotacao_seguro_viagem_pessoa.*");
        //$this->_database->select("cotacao_equipamento.*");
        $this->_database->select("cliente.*");
        $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 1 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1) AS email");
        $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 2 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1)  AS celular");
        $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 3 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1) AS telefone");
        //$this->_database->join('cotacao_seguro_viagem', 'cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id', 'LEFT');
        //$this->_database->join('cotacao_seguro_viagem_pessoa', 'cotacao_seguro_viagem_pessoa.cotacao_seguro_viagem_id = cotacao_seguro_viagem.cotacao_seguro_viagem_id', 'LEFT');
        $this->_database->join('cliente', 'cliente.cliente_id = cotacao.cliente_id', 'LEFT');
        //$this->_database->where('cotacao_seguro_viagem.deletado', 0);
        $this->_database->order_by('cotacao.criacao', 'DESC');
        return $this;
    }

    /**
     * Join com Status de Cotação
     * @return $this
     */
    public function with_status()
    {
        $this->_database->select("cotacao_status.nome as status_nome, cotacao_status.slug as status_slug");
        $this->_database->join('cotacao_status', 'cotacao_status.cotacao_status_id = cotacao.cotacao_status_id');
        return $this;
    }

    public function with_parceiro()
    {
        $this->_database->select("parceiro.nome_fantasia");
        $this->_database->join('parceiro', 'parceiro.parceiro_id = cotacao.parceiro_id');
        return $this;
    }
    public function with_produto_parceiro()
    {
        $this->_database->select("produto_parceiro.nome as produto_nome");
        $this->_database->select("produto.slug as produto_slug");
        $this->_database->join('produto_parceiro', 'cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id');
        $this->_database->join('produto', 'produto_parceiro.produto_id = produto.produto_id');
        return $this;
    }


    public function filterPesquisa()
    {

        $filters = $this->input->get();
      //  print_r($filters);exit;

        if($filters) {
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case "cotacao_codigo":
                            $this->_database->like('cotacao.codigo', $value);
                            break;
                        case "razao_nome":
                            $this->_database->like('cliente.razao_nome', $value);
                            break;
                        case "cnpj_cpf":
                            $this->_database->like('cliente.cnpj_cpf', $value);
                            break;
                        case "data_nascimento":
                            $this->_database->where('cliente.data_nascimento', app_dateonly_mask_to_mysql($value));
                            break;
                    }


                }
            }

        }
        return $this;
    }    

    public function with_lead_clientes_contatos()
    {
        $this->_database->select("cliente.*");
        $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 1 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1) AS email");
        $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 2 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1)  AS celular");
        $this->_database->select("(SELECT contato FROM cliente_contato INNER JOIN contato on contato.contato_id = cliente_contato.contato_id WHERE cliente_contato.deletado = 0 AND contato.deletado = 0 AND contato.contato_tipo_id = 3 AND cliente_contato.cliente_id = cliente.cliente_id LIMIT 1) AS telefone");
     //   $this->_database->select("cotacao_seguro_viagem.*");
        $this->_database->join('cliente', 'cliente.cliente_id = cotacao.cliente_id', 'LEFT');
     //   $this->_database->join('cotacao_seguro_viagem', 'cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id', 'LEFT');
      //  $this->_database->where('cotacao_seguro_viagem.deletado', 0);
        return $this;
    }

    /**
     * Com cotação de seguro viagem
     * @return $this
     */
    public function with_cotacao_seguro_viagem() {
        $this->_database->select('cotacao_seguro_viagem.cotacao_seguro_viagem_id');
        $this->_database->select('cotacao_seguro_viagem.produto_parceiro_plano_id');
        $this->_database->select('cotacao_seguro_viagem.produto_parceiro_id');
        $this->_database->select('cotacao_seguro_viagem.seguro_viagem_motivo_id');
        $this->_database->select('cotacao_seguro_viagem.moeda_cambio_id');
        $this->_database->select('cotacao_seguro_viagem.email');
        $this->_database->select('cotacao_seguro_viagem.origem_id');
        $this->_database->select('cotacao_seguro_viagem.telefone');
        $this->_database->select('cotacao_seguro_viagem.destino_id');
        $this->_database->select('cotacao_seguro_viagem.data_saida');
        $this->_database->select('cotacao_seguro_viagem.data_retorno');
        $this->_database->select('cotacao_seguro_viagem.num_passageiro');
        $this->_database->select('cotacao_seguro_viagem.repasse_comissao');
        $this->_database->select('cotacao_seguro_viagem.comissao_corretor');
        $this->_database->select('cotacao_seguro_viagem.desconto_condicional');
        $this->_database->select('cotacao_seguro_viagem.desconto_condicional_valor');
        $this->_database->select('cotacao_seguro_viagem.premio_liquido');
        $this->_database->select('cotacao_seguro_viagem.premio_liquido_total');
        $this->_database->select('cotacao_seguro_viagem.iof');
        $this->_database->select('cotacao_seguro_viagem.desconto_cond_aprovado');
        $this->_database->select('cotacao_seguro_viagem.step');
        $this->_database->join('cotacao_seguro_viagem', 'cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id');
        $this->_database->where("cotacao_seguro_viagem.deletado", 0);
        return $this;
    }

    /**
     * Filtra por Id da cotação de Equipamento
     * @return $this
     */
    public function filter_by_cotacao_seguro_viagem_id( $cotacao_seguro_viagem_id ) {
        $this->_database->where("cotacao_seguro_viagem.cotacao_seguro_viagem_id", $cotacao_seguro_viagem_id);
        return $this;
    }
  
    /**
     * Com cotação de generico
     * @return $this
     */
    public function with_cotacao_generico() {
        $this->_database->select('cotacao_generico.*');
        $this->_database->join('cotacao_generico', 'cotacao_generico.cotacao_id = cotacao.cotacao_id');
        $this->_database->where("cotacao_generico.deletado", 0);
        return $this;
    }

    /**
     * Filtra por Id da cotação Generico
     * @return $this
     */
    public function filter_by_cotacao_generico_id( $cotacao_generico_id ) {
        $this->_database->where("cotacao_generico.cotacao_generico_id", $cotacao_generico_id);
        return $this;
    }
  
    /**
     * Com cotação de Equipamento
     * @return $this
     */
    public function with_cotacao_equipamento() {
        $this->_database->select('cotacao_equipamento.*');
        $this->_database->join('cotacao_equipamento', 'cotacao_equipamento.cotacao_id = cotacao.cotacao_id');
        $this->_database->where("cotacao_equipamento.deletado", 0);
        return $this;
    }

    /**
     * Filtra por Id da cotação de Equipamento
     * @return $this
     */
    public function filter_by_cotacao_equipamento_id( $cotacao_equipamento_id ) {
        $this->_database->where("cotacao_equipamento.cotacao_equipamento_id", $cotacao_equipamento_id);
        return $this;
    }

    /**
     * Com cotação de seguro viagem
     * @return $this
     */
    public function with_cotacao_produto_parceiro_plano()
    {
        $this->_database->select('produto_parceiro_plano.produto_parceiro_plano_id');
        $this->_database->select('produto.slug as produto_slug');
        $this->_database->join('produto_parceiro', 'produto_parceiro.produto_parceiro_id = cotacao.produto_parceiro_id');
        $this->_database->join('produto', 'produto_parceiro.produto_id = produto.produto_id');
        $this->_database->join('produto_parceiro_plano', 'produto_parceiro_plano.produto_parceiro_id = produto_parceiro_plano.produto_parceiro_id');
        return $this;
    }

    public function with_cotacao_seguro_viagem_plano()
    {
        $this->_database->select('produto_parceiro_plano.nome as plano_nome');
        $this->_database->select('produto_parceiro_plano.passivel_upgrade');
        $this->_database->select('produto_parceiro_plano.descricao as plano_descricao');
        $this->_database->select('produto_parceiro_plano.codigo_operadora as plano_codigo_operadora');

        $this->_database->join('produto_parceiro_plano', 'produto_parceiro_plano.produto_parceiro_plano_id = cotacao_seguro_viagem.produto_parceiro_plano_id');
        $this->_database->where("produto_parceiro_plano.deletado", 0);
        return $this;
    }

    public function with_cotacao_equipamento_plano()
    {
        $this->_database->select('produto_parceiro_plano.nome as plano_nome');
        $this->_database->select('produto_parceiro_plano.passivel_upgrade');
        $this->_database->select('produto_parceiro_plano.descricao as plano_descricao');
        $this->_database->select('produto_parceiro_plano.codigo_operadora as plano_codigo_operadora');

        $this->_database->join('produto_parceiro_plano', 'produto_parceiro_plano.produto_parceiro_plano_id = cotacao_equipamento.produto_parceiro_plano_id');
        $this->_database->where("produto_parceiro_plano.deletado", 0);
        return $this;
    }

    public function with_cotacao_generico_plano()
    {
        $this->_database->select('produto_parceiro_plano.nome as plano_nome');
        $this->_database->select('produto_parceiro_plano.passivel_upgrade');
        $this->_database->select('produto_parceiro_plano.descricao as plano_descricao');
        $this->_database->select('produto_parceiro_plano.codigo_operadora as plano_codigo_operadora');

        $this->_database->join('produto_parceiro_plano', 'produto_parceiro_plano.produto_parceiro_plano_id = cotacao_generico.produto_parceiro_plano_id');
        $this->_database->where("produto_parceiro_plano.deletado", 0);
        return $this;
    }

    public function with_cotacao_seguro_viagem_motivo()
    {
        $this->_database->select('seguro_viagem_motivo.nome as motivo_nome');

        $this->_database->join('seguro_viagem_motivo', 'seguro_viagem_motivo.seguro_viagem_motivo_id = cotacao_seguro_viagem.seguro_viagem_motivo_id');
        $this->_database->where("seguro_viagem_motivo.deletado", 0);
        return $this;
    }

    public function with_cotacao_seguro_viagem_moeda()
    {
        $this->_database->select('moeda_cambio.data_cambio');
        $this->_database->select('moeda_cambio.valor_real as cambio');
        $this->_database->select('moeda.nome as moeda');

        $this->_database->join('moeda_cambio', 'moeda_cambio.moeda_cambio_id = cotacao_seguro_viagem.moeda_cambio_id');
        $this->_database->join('moeda', 'moeda.moeda_id = moeda_cambio.moeda_id');
        return $this;
    }

    public function with_cotacao_seguro_viagem_produto()
    {
        $this->_database->select('produto_parceiro.nome as produto_nome');
        $this->_database->select('produto_parceiro.codigo_susep as produto_codigo_susep');

        $this->_database->join('produto_parceiro', 'produto_parceiro.produto_parceiro_id = cotacao_seguro_viagem.produto_parceiro_id');
        $this->_database->where("produto_parceiro.deletado", 0);
        return $this;
    }

    public function with_cotacao_equipamento_produto()
    {
        $this->_database->select('produto_parceiro.nome as produto_nome');
        $this->_database->select('produto_parceiro.codigo_susep as produto_codigo_susep');

        $this->_database->join('produto_parceiro', 'produto_parceiro.produto_parceiro_id = cotacao.produto_parceiro_id');
        return $this;
    }

    public function with_cotacao_generico_produto()
    {
        $this->_database->select('produto_parceiro.nome as produto_nome');
        $this->_database->select('produto_parceiro.codigo_susep as produto_codigo_susep');

        $this->_database->join('produto_parceiro', 'produto_parceiro.produto_parceiro_id = cotacao.produto_parceiro_id');
        return $this;
    }

    public function with_cotacao_equipamento_equipamento()
    {
        $this->_database->select('em.nome as equipamento_marca_nome');
        $this->_database->select('el.nome as equipamento_categoria_nome');
        $this->_database->select('esc.nome as equipamento_sub_categoria_nome');

        $this->_database->join('vw_Equipamentos_Marcas em', 'cotacao_equipamento.equipamento_marca_id = em.equipamento_marca_id', 'left');
        $this->_database->join('vw_Equipamentos_Linhas el', 'cotacao_equipamento.equipamento_categoria_id = el.equipamento_categoria_id', 'left');
        $this->_database->join('vw_Equipamentos_Linhas esc', 'cotacao_equipamento.equipamento_sub_categoria_id = esc.equipamento_categoria_id', 'left');
        return $this;
    }

    public function with_cotacao_seguro_viagem_origem_destino()
    {
        $this->_database->select('origem.tipo as origem_tipo');
        $this->_database->select('origem.nome as origem_nome');

        $this->_database->select('destino.tipo as destino_tipo');
        $this->_database->select('destino.nome as destino_nome');

        $this->_database->join('localidade as origem', 'origem.localidade_id = cotacao_seguro_viagem.origem_id');
        $this->_database->join('localidade as destino', 'destino.localidade_id = cotacao_seguro_viagem.destino_id');
        $this->_database->where("origem.deletado", 0);
        $this->_database->where("destino.deletado", 0);
        return $this;
    }

    public function with_seguro_viagem(){
        $this->_database->where("cotacao_seguro_viagem.deletado", 0);
        return $this->with_simple_relation('cotacao_seguro_viagem', 'cotacao_', 'cotacao_id', array('produto_parceiro_id'), 'inner');
    }

    function filterByID($cotacao_id){
        $this->_database->where("cotacao.cotacao_id", $cotacao_id);
        $this->_database->where("cotacao.deletado", 0);
        return $this;
    }

    function filterByStatus($cotacao_status_id){
        $this->_database->where("cotacao.cotacao_status_id", $cotacao_status_id);
        $this->_database->where("cotacao.deletado", 0);
        return $this;
    }

    public function tem_capitalizacao($cotacao_id)
    {
        $this->_database->join('cotacao_generico cg', 'cotacao.cotacao_id = cg.cotacao_id', 'left');
        $this->_database->join('cotacao_equipamento ce', 'cotacao.cotacao_id = ce.cotacao_id', 'left');
        $this->_database->join('cotacao_seguro_viagem csv', 'cotacao.cotacao_id = csv.cotacao_id', 'left');

        $this->_database->join('cobertura_plano cp', 'cp.produto_parceiro_plano_id = IFNULL(IFNULL(ce.produto_parceiro_plano_id, cg.produto_parceiro_plano_id), csv.produto_parceiro_plano_id) ');
        $this->_database->join('cobertura c', 'cp.cobertura_id = c.cobertura_id');

        $this->filterByID($cotacao_id);

        $this->_database->where("cotacao.deletado", 0);
        $this->_database->where("cp.deletado", 0);
        $this->_database->where("c.deletado", 0);
        $this->_database->where("c.slug", 'sorteio_mensal');
        return !empty($this->get_total());
    }

    /**
     * Retorna status por slug
     * @param $slug
     * @return int
     */
    public function getStatus($slug)
    {
        $this->load->model("cotacao_status_model", "cotacao_status");
        if($status = $this->cotacao_status->get_by(array("slug" => $slug)))
        {
            return $status['cotacao_status_id'];
        }
        return 0;
    }

    /**
     * Verifica se uma cotação ainda pode ser alterada
     * @param $cotacao_id id da cotação
     *
     */

    function isCotacaoValida($cotacao_id){

        $this->load->model('pedido_model', 'pedido');
        $this->load->model('apolice_model', 'apolice');
        $this->load->model('produto_parceiro_configuracao_model', 'prod_parc_config');

        $cotacao = $this->get($cotacao_id);
        $pedido = $this->pedido->filter_by_cotacao($cotacao_id)->get_all();

        if(!$cotacao){
            return false;
        }

        $conclui_em_tempo_real = $this->prod_parc_config->item_config($cotacao['produto_parceiro_id'], 'conclui_em_tempo_real');
        if ( $conclui_em_tempo_real ) {
            if ( $cotacao['cotacao_status_id'] != 1 && $cotacao['cotacao_status_id'] != 5 ) {
                return false;
            }

            if ( count($pedido) > 0 ) {
                return false;
            }
        } else {
            if ( count($pedido) > 0 ) {
                $apolice = $this->apolice->getApolicePedido($pedido[0]['pedido_id']);
                if ( count($apolice) > 0 ) {
                    return false;
                }
            }
        }

        return true;
    }

    function getCotacaoByDoc($documento = null, $cotacao_id = null){

        $this->_database->select("cotacao.cotacao_id, produto_parceiro.nome as produto_nome");
        $this->_database->select("produto.slug as produto_slug");
        $this->with_status();
        $this->with_parceiro();
        $this->_database->from("{$this->_table} as cotacao");
        $this->_database->join('pedido', 'cotacao.cotacao_id = pedido.cotacao_id');
        $this->_database->join('produto_parceiro', 'cotacao.produto_parceiro_id = produto_parceiro.produto_parceiro_id');
        $this->_database->join('produto', 'produto_parceiro.produto_id = produto.produto_id');
        $this->_database->join('cotacao_seguro_viagem', 'cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id AND cotacao_seguro_viagem.deletado = 0', 'left');
        $this->_database->join('cotacao_equipamento', 'cotacao_equipamento.cotacao_id = cotacao.cotacao_id AND cotacao_equipamento.deletado = 0', 'left');
        $this->_database->join('cotacao_generico', 'cotacao_generico.cotacao_id = cotacao.cotacao_id AND cotacao_generico.deletado = 0', 'left');
        $this->_database->join('apolice', 'pedido.pedido_id = apolice.pedido_id AND apolice.deletado = 0', 'left');
        $this->_database->where("cotacao_status.slug", "finalizada");
        $this->_database->where("cotacao.deletado", 0);
        $this->_database->where("apolice.apolice_id IS NULL");
        $this->_database->where("parceiro.parceiro_id", $this->parceiro_id);

        if ( !empty($cotacao_id) ) {
            $this->_database->where("cotacao.cotacao_id", $cotacao_id);
        }

        if ( !empty($documento) ) {
            $this->_database->where("REPLACE(REPLACE(REPLACE(IFNULL(IFNULL(cotacao_equipamento.cnpj_cpf, cotacao_generico.cnpj_cpf), cotacao_seguro_viagem.cnpj_cpf), '.', ''), '-', ''), '/', '') = '{$documento}'");
        }

        $query = $this->_database->get();
        $resp = $result = [];

        if($query->num_rows() > 0)
        {
            $resp = $query->result_array();
            foreach ($resp as $row) {
                $produto_slug = $row['produto_slug'];
                $cotacao_id = $row['cotacao_id'];
                switch ($produto_slug) {
                    case 'seguro_viagem':
                        $tableName = "cotacao_seguro_viagem";
                        break;
                    case 'equipamento':
                        $tableName = "cotacao_equipamento";
                        break;
                    default:
                        $tableName = "cotacao_generico";
                        break;
                }

                $this->_database->from($tableName);
                $this->_database->where("{$tableName}.cotacao_id", $cotacao_id);
                $q = $this->_database->get();
                $row['dados'] = ($q->num_rows() > 0) ? current($q->result_array()) : [];
                $result[] = $row;
            }
        }

        return $result;
    }

    //Retorna por slug
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    function setValidate($value){

        $this->validate = $value;
    }

    /**
     * Retorna todos
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function get_all($limit = 0, $offset = 0, $processa = true)
    {
      /*
        if($processa)
        {
            $parceiro_id = $this->session->userdata('parceiro_id');

            $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
        }*/
        return parent::get_all($limit, $offset);
    }

    /**
     * Retorna todos
     * @return mixed
     */
    public function get_total($processa = true)
    {
      /*
        if($processa)
        {
            //Efetua join com cotação
            $this->_database->join("cotacao as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

            $this->processa_parceiros_permitidos("cotacao_filtro.parceiro_id");
        }*/
        return parent::get_total(); // TODO: Change the autogenerated stub
    }

}
