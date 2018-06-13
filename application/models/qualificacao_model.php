<?php
/**
 * Class Qualificacao_Model
 *
 * @property CI_DB_active_record $_database
 *
 */

Class Qualificacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'qualificacao';
    protected $primary_key = 'qualificacao_id';

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
            'field' => 'ativo',
            'label' => 'Ativo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'produto_id',
            'label' => 'Produto',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'ativo' => $this->input->post('ativo'),
            'produto_id' => $this->input->post('produto_id')

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_produto(){
        $this->with_simple_relation('produto', 'produto_', 'produto_id', array('nome'));
        return $this;
    }

    function coreRanking(){


        /**
         * Parceiro
         */
        $this->_database->join('qualificacao_parceiro', "{$this->_table}.qualificacao_id = qualificacao_parceiro.qualificacao_id");
        $this->_database->join('parceiro', "qualificacao_parceiro.parceiro_id = parceiro.parceiro_id");

        /**
         * Questao
         */
        $this->_database->join('qualificacao_questao', "{$this->_table}.qualificacao_id = qualificacao_questao.qualificacao_id");

        /**
         * Opções
         */
        $this->_database->join('qualificacao_questao_opcao', "qualificacao_questao.qualificacao_questao_id = qualificacao_questao_opcao.qualificacao_questao_id");

        /**
         * Respostas
         */
        $this->_database->join('qualificacao_parceiro_resposta', "qualificacao_parceiro.qualificacao_parceiro_id = qualificacao_parceiro_resposta.qualificacao_parceiro_id");
        $this->_database->where('qualificacao_questao.qualificacao_questao_id = qualificacao_parceiro_resposta.qualificacao_questao_id');
        $this->_database->where('qualificacao_questao_opcao.qualificacao_questao_opcao_id = qualificacao_parceiro_resposta.qualificacao_questao_opcao_id');

        /**
         * Réguas
         */
        $this->_database->join('qualificacao_regua', "qualificacao_regua.regua_logica = qualificacao_questao.regua_logica AND qualificacao_regua.regua_valor = qualificacao_questao_opcao.regua_valor");

        /**
         * Campos
         */
        $this->_database->select('qualificacao.produto_id');
        $this->_database->select('parceiro.nome AS parceiro_nome');
        $this->_database->select('qualificacao.produto_id');
        $this->_database->select('qualificacao_parceiro.parceiro_id');



        return $this;
    }

    public function coreRakingByQuestaoFields(){




        $this->_database->select('qualificacao_questao.pergunta');
        $this->_database->select('qualificacao_questao.peso');
        $this->_database->select('qualificacao_questao.regua_logica');
        $this->_database->select('qualificacao_questao_opcao.nome AS opcao_nome');
        $this->_database->select('qualificacao_questao_opcao.regua_valor');
        $this->_database->select('qualificacao_parceiro_resposta.valor_exato');
        $this->_database->select('qualificacao_regua.pontos');
        $this->_database->select('(qualificacao_questao.peso * qualificacao_regua.pontos) as pontuacao');

        return $this;
    }

    public function coreRakingPontuacaoTotalByParceiro(){


        $this->_database->group_by('qualificacao_parceiro.parceiro_id');

        $this->_database->select('SUM(qualificacao_questao.peso * qualificacao_regua.pontos) AS pontuacao_total');

        $this->_database->order_by('pontuacao_total', 'DESC');


        return $this;
    }

    public function coreRakingFilterByQualificacaoParceiro($qualificacao_parceiro_id){

        $this->_database->where('qualificacao_parceiro.qualificacao_parceiro_id', $qualificacao_parceiro_id );

        return $this;
    }

    public function coreRakingFilterByQualificacao($qualificacao_id){

        $this->_database->where('qualificacao.qualificacao_id', $qualificacao_id );

        return $this;
    }

}
