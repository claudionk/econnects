<?php
Class Produto_Parceiro_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro';
    protected $primary_key = 'produto_parceiro_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'codigo_susep',
            'label' => 'Código SUSEP',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'produto_id',
            'label' => 'Produto',
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
            'field' => 'seguradora_id',
            'label' => 'Seguradora',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'venda_agrupada',
            'label' => 'Venda Agrupada',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'codigo_susep' => $this->input->post('codigo_susep'),
            'produto_id' => $this->input->post('produto_id'),
            'parceiro_id' => $this->input->post('parceiro_id'),
            'seguradora_id' => $this->input->post('seguradora_id'),
            'venda_agrupada' => $this->input->post('venda_agrupada')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_produto(){
        $this->with_simple_relation('produto', 'produto_', 'produto_id', array('nome', 'produto_ramo_id', 'slug'));
        return $this;
    }
    function with_produto_parceiro_configuracao(){
        $this->with_simple_relation('produto_parceiro_configuracao', 'produto_parceiro_configuracao_', 'produto_parceiro_id', array('pagamento_tipo', 'pagamento_periodicidade_unidade', 'pagamento_periodicidade', 'pagmaneto_cobranca', 'pagmaneto_cobranca_dia', 'pagamento_teimosinha'));
        return $this;
    }

    function with_parceiro(){
        $this->with_simple_relation('parceiro', 'parceiro_', 'parceiro_id', array('nome'));
        return $this;
    }

    function get_produtos_venda_admin($parceiro_id){


        $this->_database->select($this->_table.'.produto_parceiro_id');
        $this->_database->select($this->_table.'.parceiro_id');
        $this->_database->select($this->_table.'.produto_id');
        $this->_database->select($this->_table.'.nome');
        $this->_database->select('produto.slug, produto.nome');
        $this->_database->select('parceiro.nome as parceiro_nome');
        $this->_database->select('produto_parceiro_configuracao.venda_carrinho_compras, produto_parceiro_configuracao.venda_multiplo_cartao');
        $this->_database->from($this->_table);
        $this->_database->where($this->_table.'.deletado', 0);
        $this->_database->where($this->_table.'.parceiro_id', $parceiro_id);
        $this->_database->where('produto_parceiro_configuracao.deletado', 0);
        $this->_database->where('produto_parceiro_configuracao.venda_habilitada_admin', 1);
        $this->_database->where('produto.deletado', 0);
        $this->_database->join('produto', 'produto.produto_id = '.$this->_table.'.produto_id', 'inner');
        $this->_database->join('produto_parceiro_configuracao', $this->_table. '.produto_parceiro_id = produto_parceiro_configuracao.produto_parceiro_id', 'inner');
        $this->_database->join('parceiro', $this->_table. '.parceiro_id = parceiro.parceiro_id', 'inner');
        $this->_database->order_by('produto.nome', 'ASC');

        $query = $this->_database->get();

        if($query->num_rows() > 0)
            return $query->result_array();
        return array();
    }


    function get_produtos_venda_admin_parceiros($parceiro_id){


        $this->_database->select($this->_table.'.produto_parceiro_id');
        $this->_database->select($this->_table.'.parceiro_id');
        $this->_database->select($this->_table.'.produto_id');
        $this->_database->select($this->_table.'.nome');
        $this->_database->select('produto.slug, produto.nome');
        $this->_database->select('parceiro.nome as parceiro_nome');
        $this->_database->select('produto_parceiro_configuracao.venda_carrinho_compras, produto_parceiro_configuracao.venda_multiplo_cartao');
        $this->_database->from($this->_table);
        $this->_database->where($this->_table.'.deletado', 0);
        $this->_database->where('parceiro_relacionamento_produto.parceiro_id', $parceiro_id);
        $this->_database->where('parceiro_relacionamento_produto.deletado', 0);
        $this->_database->where('produto_parceiro_configuracao.deletado', 0);
        $this->_database->where('produto_parceiro_configuracao.venda_habilitada_admin', 1);
        $this->_database->where('produto.deletado', 0);
        $this->_database->join('produto', 'produto.produto_id = '.$this->_table.'.produto_id', 'inner');
        $this->_database->join('produto_parceiro_configuracao', $this->_table. '.produto_parceiro_id = produto_parceiro_configuracao.produto_parceiro_id', 'inner');
        $this->_database->join('parceiro_relacionamento_produto', $this->_table. '.produto_parceiro_id = parceiro_relacionamento_produto.produto_parceiro_id', 'inner');
        $this->_database->join('parceiro', $this->_table. '.parceiro_id = parceiro.parceiro_id', 'inner');
        $this->_database->order_by('produto.nome', 'ASC');

        $query = $this->_database->get();


        //exit($this->_database->last_query());



        if($query->num_rows() > 0)
            return $query->result_array();
        return array();
    }


    function  filter_by_parceiro($parceiro_id){

        $this->_database->where('parceiro_id', $parceiro_id);

        return $this;
    }
}
