<?php
Class Cotacao_Seguro_Viagem_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cotacao_seguro_viagem';
    protected $primary_key = 'cotacao_seguro_viagem_id';

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
            'field' => 'cotacao_seguro_viagem_id',
            'label' => 'Cotacao seguro viagem id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'cotacao_seguro_viagem'
        ),

        array(
            'field' => 'cotacao_id',
            'label' => 'Cotacao id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'cotacao'
        ),

        array(
            'field' => 'produto_parceiro_plano_id',
            'label' => 'Produto parceiro plano id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'produto_parceiro_plano'
        ),

        array(
            'field' => 'produto_parceiro_id',
            'label' => 'Produto parceiro id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'produto_parceiro'
        ),

        array(
            'field' => 'seguro_viagem_motivo_id',
            'label' => 'Seguro viagem motivo id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'seguro_viagem_motivo'
        ),

        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'telefone',
            'label' => 'Telefone',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'origem_id',
            'label' => 'Origem id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'origem'
        ),

        array(
            'field' => 'destino_id',
            'label' => 'Destino id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'destino'
        ),

        array(
            'field' => 'data_saida',
            'label' => 'Data saida',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'data_retorno',
            'label' => 'Data retorno',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'qnt_dias',
            'label' => 'Qnt dias',
            'rules' => 'required|numeric',
            'groups' => 'default',
        ),

        array(
            'field' => 'num_passageiro',
            'label' => 'Num passageiro',
            'rules' => 'required|numeric',
            'groups' => 'default',
        ),

        array(
            'field' => 'repasse_comissao',
            'label' => 'Desconto comissao',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'comissao_corretor',
            'label' => 'Comissao corretor',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'desconto_condicional',
            'label' => 'Desconto condicional',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'desconto_cond_aprovado',
            'label' => 'Desconto cond aprovado',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'desconto_cond_aprovado_usuario',
            'label' => 'Desconto cond aprovado usuario',
            'rules' => 'required|numeric',
            'groups' => 'default',
        ),

        array(
            'field' => 'desconto_cond_aprovado_data',
            'label' => 'Desconto cond aprovado data',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'desconto_cond_enviar',
            'label' => 'Desconto cond enviar',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'premio_liquido',
            'label' => 'Premio liquido',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'iof',
            'label' => 'Iof',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'premio_liquido_total',
            'label' => 'Premio liquido total',
            'rules' => 'required',
            'groups' => 'default',
        ),

        array(
            'field' => 'alteracao_usuario_id',
            'label' => 'Alteracao usuario id',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'alteracao_usuario'
        ),

        array(
            'field' => 'estado_civil',
            'label' => 'estado_civil',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'rg_orgao_expedidor',
            'label' => 'rg_orgao_expedidor',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'rg_uf',
            'label' => 'rg_uf',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'rg_data_expedicao',
            'label' => 'rg_data_expedicao',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_01',
            'label' => 'aux_01',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_02',
            'label' => 'aux_02',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_03',
            'label' => 'aux_03',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_04',
            'label' => 'aux_04',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_05',
            'label' => 'aux_05',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_06',
            'label' => 'aux_06',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_07',
            'label' => 'aux_07',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_08',
            'label' => 'aux_08',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_09',
            'label' => 'aux_09',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'aux_10',
            'label' => 'aux_10',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'garantia_fabricante',
            'label' => 'garantia_fabricante',
            'rules' => '',
            'groups' => 'default'
        ),
    );

    /**
     * Verifica se possui desconto e o mesmo foi aprovado
     * @param $cotacao_id
     */
    public function verifica_possui_desconto($cotacao_id)
    {
        $cotacoes = $this->get_many_by(array(
            'cotacao_id' => $cotacao_id,
            'deletado' => 0
        ));

        if($cotacoes)
            foreach($cotacoes as $cotacao)
            {
                if($cotacao && $cotacao['desconto_condicional'] > 0)
                {
                    return true;
                }
            }
        return false;
    }

    /**
     * Verifica se possui desconto e o mesmo foi aprovado
     * @param $cotacao_id
     */
    public function verifica_desconto_aprovado($cotacao_id)
    {
        $cotacao = $this->get_by(array(
            'cotacao_id' => $cotacao_id,
            'deletado' => 0,
            'desconto_cond_aprovado' => 1
        ));
        if($cotacao)
            return true;
        return false;
    }

    function filterByCotacaoPlano($cotacao_id, $produto_parceiro_plano_id ){
        $this->_database->where("cotacao_seguro_viagem.cotacao_id", $cotacao_id);
        $this->_database->where("cotacao_seguro_viagem.deletado", 0);
        $this->_database->where("cotacao_seguro_viagem.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        return $this;
    }

    function filterByCotacao($cotacao_id){
        $this->_database->where("cotacao_seguro_viagem.cotacao_id", $cotacao_id);
        $this->_database->where("cotacao_seguro_viagem.deletado", 0);
        return $this;
    }

    function getCotacaoAprovacao($cotacao_id){

        $cotacao_id = (int)$cotacao_id;

        $sql = "
                SELECT
                cotacao_seguro_viagem.cotacao_seguro_viagem_id, cotacao_seguro_viagem.cotacao_id,
                cotacao_seguro_viagem.produto_parceiro_plano_id, produto_parceiro_plano.nome as plano,
                cotacao_seguro_viagem.seguro_viagem_motivo_id, seguro_viagem_motivo.nome as motivo,
                cotacao_seguro_viagem.email,
                cotacao_seguro_viagem.telefone,
                cotacao_seguro_viagem.produto_parceiro_id,
                cotacao_seguro_viagem.step,
                cotacao_seguro_viagem.origem_id, tb_origem.nome as origem,
                cotacao_seguro_viagem.destino_id, tb_destino.nome as destino,
                cotacao_seguro_viagem.data_saida, cotacao_seguro_viagem.data_retorno,
                cotacao_seguro_viagem.qnt_dias, cotacao_seguro_viagem.num_passageiro,
                cotacao_seguro_viagem.repasse_comissao,
                cotacao_seguro_viagem.comissao_corretor,
                cotacao_seguro_viagem.desconto_condicional,
                cotacao_seguro_viagem.desconto_condicional_valor,
                cotacao_seguro_viagem.premio_liquido,
                cotacao_seguro_viagem.iof,
                cotacao_seguro_viagem.premio_liquido_total
                FROM cotacao_seguro_viagem
                INNER JOIN produto_parceiro_plano ON produto_parceiro_plano.produto_parceiro_plano_id = cotacao_seguro_viagem.produto_parceiro_plano_id
                INNER JOIN seguro_viagem_motivo ON cotacao_seguro_viagem.seguro_viagem_motivo_id = seguro_viagem_motivo.seguro_viagem_motivo_id
                INNER join localidade AS tb_origem ON cotacao_seguro_viagem.origem_id = tb_origem.localidade_id
                INNER join localidade AS tb_destino ON cotacao_seguro_viagem.destino_id = tb_destino.localidade_id

                where
                cotacao_seguro_viagem.cotacao_id = {$cotacao_id}
                and cotacao_seguro_viagem.deletado = 0


        ";

        return $this->_database->query($sql)->result_array();

    }
    function getCotacaoLead($cotacao_id){

        $cotacao_id = (int)$cotacao_id;

        $sql = "
                SELECT
                cotacao_seguro_viagem.cotacao_seguro_viagem_id, cotacao_seguro_viagem.cotacao_id,
                cotacao_seguro_viagem.seguro_viagem_motivo_id, seguro_viagem_motivo.nome as motivo,
                cotacao_seguro_viagem.email,
                cotacao_seguro_viagem.telefone,
                cotacao_seguro_viagem.step,
                cotacao_seguro_viagem.produto_parceiro_id,
                cotacao_seguro_viagem.origem_id, tb_origem.nome as origem,
                cotacao_seguro_viagem.destino_id, tb_destino.nome as destino,
                cotacao_seguro_viagem.data_saida, cotacao_seguro_viagem.data_retorno,
                cotacao_seguro_viagem.qnt_dias, cotacao_seguro_viagem.num_passageiro,
                cotacao_seguro_viagem.repasse_comissao,
                cotacao_seguro_viagem.comissao_corretor,
                cotacao_seguro_viagem.desconto_condicional,
                cotacao_seguro_viagem.premio_liquido,
                cotacao_seguro_viagem.iof,
                cotacao_seguro_viagem.premio_liquido_total
                FROM cotacao_seguro_viagem
                INNER JOIN seguro_viagem_motivo ON cotacao_seguro_viagem.seguro_viagem_motivo_id = seguro_viagem_motivo.seguro_viagem_motivo_id
                INNER join localidade AS tb_origem ON cotacao_seguro_viagem.origem_id = tb_origem.localidade_id
                INNER join localidade AS tb_destino ON cotacao_seguro_viagem.destino_id = tb_destino.localidade_id

                where
                cotacao_seguro_viagem.cotacao_id = {$cotacao_id}
                and cotacao_seguro_viagem.deletado = 0


        ";

        return $this->_database->query($sql)->result_array();

    }

    function with_cotacao_seguro_viagem_pessoa()
    {

        $this->_database->select('cotacao_seguro_viagem_pessoa.cotacao_seguro_viagem_pessoa_id');
        $this->_database->select('cotacao_seguro_viagem_pessoa.contratante_passageiro');
        $this->_database->select('cotacao_seguro_viagem_pessoa.nome');
        $this->_database->select('cotacao_seguro_viagem_pessoa.cnpj_cpf');
        $this->_database->select('cotacao_seguro_viagem_pessoa.rg');
        $this->_database->select('cotacao_seguro_viagem_pessoa.data_nascimento');
        $this->_database->select('cotacao_seguro_viagem_pessoa.garantia_fabricante');
        $this->_database->select('cotacao_seguro_viagem_pessoa.endereco_cep');
        $this->_database->select('cotacao_seguro_viagem_pessoa.endereco_logradouro');
        $this->_database->select('cotacao_seguro_viagem_pessoa.endereco_numero');
        $this->_database->select('cotacao_seguro_viagem_pessoa.endereco_complemento');
        $this->_database->select('cotacao_seguro_viagem_pessoa.endereco_bairro');
        $this->_database->select('cotacao_seguro_viagem_pessoa.endereco_cidade');
        $this->_database->select('cotacao_seguro_viagem_pessoa.endereco_estado');
        $this->_database->select('cotacao_seguro_viagem_pessoa.contato_telefone');
        $this->_database->select('cotacao_seguro_viagem_pessoa.sexo');
        $this->_database->select('cotacao_seguro_viagem_pessoa.email');

        $this->_database->join('cotacao_seguro_viagem_pessoa', 'cotacao_seguro_viagem_pessoa.cotacao_seguro_viagem_id = cotacao_seguro_viagem.cotacao_seguro_viagem_id');
        $this->_database->where("cotacao_seguro_viagem_pessoa.deletado", 0);
        $this->_database->where("cotacao_seguro_viagem.deletado", 0);
        return $this;
    }

    function getValorTotal($cotacao_id){
        $result = $this->filterByCotacao($cotacao_id)
                       ->get_all();

        $valor = 0;
        foreach ($result as $item) {
            $valor += $item['premio_liquido_total'];
        }
        return $valor;
    }


    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }
}

