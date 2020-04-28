<?php
Class Produto_Parceiro_Capitalizacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_capitalizacao';
    protected $primary_key = 'produto_parceiro_capitalizacao_id';

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
            'field' => 'capitalizacao_id',
            'label' => 'Capitalização',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'capitalizacao_id' => $this->input->post('capitalizacao_id'),
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_capitalizacao(){
        $this->with_simple_relation('capitalizacao', 'capitalizacao_', 'capitalizacao_id', array('nome','tipo_qnt_sorteio','qnt_sorteio','dia_corte','qtde_titulos_por_compra','valor_sorteio','valor_custo_titulo','serie','responsavel_num_sorte'));
        return $this;
    }

    function with_capitalizacao_tipo(){
        $this->_database->select('capitalizacao_tipo.nome as capitalizacao_tipo');
        $this->_database->join("capitalizacao_tipo", "capitalizacao.capitalizacao_tipo_id = capitalizacao_tipo.capitalizacao_tipo_id", "join");
        return $this;
    }

    function with_capitalizacao_sorteio(){
        $this->_database->select('capitalizacao_sorteio.nome as capitalizacao_sorteio');
        $this->_database->join("capitalizacao_sorteio", "capitalizacao.capitalizacao_sorteio_id = capitalizacao_sorteio.capitalizacao_sorteio_id", "join");
        return $this;
    }

    function filter_by_produto_parceiro($produto_parceiro_id)
    {
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        return $this;
    }

    function filter_by_capitalizacao_ativa()
    {
        $this->_database->where("capitalizacao.data_inicio <", date('Y-m-d H:i:s'));
        $this->_database->where("capitalizacao.data_fim >", date('Y-m-d H:i:s'));
        $this->_database->where("capitalizacao.ativo", 1);
        return $this;
    }

}
