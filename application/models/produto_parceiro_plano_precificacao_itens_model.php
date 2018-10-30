<?php
Class Produto_Parceiro_Plano_Precificacao_Itens_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_plano_precificacao_itens';
    protected $primary_key = 'produto_parceiro_plano_precificacao_itens_id';

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

    const PRECO_TIPO_TABELA = 1;
    const PRECO_TIPO_COBERTURA = 2;
    const PRECO_TIPO_VALOR_SEGURADO = 3;
    const PRECO_POR_EQUIPAMENTO = 5;
    const PRECO_POR_LINHA = 6;

    //Dados
    public $validate = array(

        array(
            'field' => 'produto_parceiro_plano_id',
            'label' => 'Produto parceiro',
            'rules' => 'required|numeric',
            'groups' => 'default',
            'foreign' => 'produto_parceiro_plano',
        ),
        array(
            'field' => 'tipo',
            'label' => 'Tipo',
            'rules' => 'required|enum[RANGE,ADICIONAL]',
            'groups' => 'default'
        ),
        array(
            'field' => 'unidade_tempo',
            'label' => 'Unidade',
            'rules' => 'required|enum[DIA,MES,ANO,VALOR,IDADE]',
            'groups' => 'default'
        ),
        array(
            'field' => 'inicial',
            'label' => 'Inicial',
            'rules' => 'required|numeric',
            'groups' => 'default'
        ),
        array(
            'field' => 'final',
            'label' => 'Final',
            'rules' => 'required|numeric',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor',
            'label' => 'Valor',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'equipamento',
            'label' => 'equipamento',
            'groups' => 'default'
        ),
        array(
            'field' => 'cobranca',
            'label' => 'cobranca',
            'groups' => 'default'
        ),
        array(
            'field' => 'tipo_equipamento',
            'label' => 'tipo_equipamento',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_plano_id' => $this->input->post('produto_parceiro_plano_id'),
            'tipo' => $this->input->post('tipo'),
            'unidade_tempo' => $this->input->post('unidade_tempo'),
            'inicial' => $this->input->post('inicial'),
            'final' => $this->input->post('final'),
            'valor' => app_unformat_currency($this->input->post('valor')),
            'equipamento' => $this->input->post('equipamento'),
        );
        if( $data["equipamento"] != "" ) {
          $data["cobranca"] = "PORCENTAGEM";
          $data["tipo_equipamento"] = "CATEGORIA";
        }
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


    function  filter_by_produto_parceiro_plano($produto_parceiro_plano_id){

        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);

        return $this;
    }

    function  filter_by_tipo($tipo){

        $this->_database->where("{$this->_table}.tipo", $tipo);

        return $this;
    }

    function  filter_by_tipo_equipamento($tipo){

        //$this->_database->where( "({$this->_table}.tipo_equipamento='$tipo' OR {$this->_table}.tipo_equipamento='TODOS')" );
        $this->_database->where("{$this->_table}.tipo_equipamento", $tipo);
        //$this->_database->or_where("{$this->_table}.tipo_equipamento", "TODOS");

        return $this;
    }
    function  filter_by_equipamento($equipamento){

        $this->_database->like("{$this->_table}.equipamento", "'{$equipamento}'");

        return $this;
    }

    function  filter_by_intevalo_dias($qnt, $unidade_tempo = 'DIA'){

        $this->_database->where("$qnt >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$qnt <=", "{$this->_table}.final", FALSE);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

    function  filter_by_faixa_etaria( $qnt, $unidade_tempo = "IDADE" ){

        $this->_database->where("$qnt >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$qnt <=", "{$this->_table}.final", FALSE);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

    function  filter_by_faixa($qnt){
        $this->_database->where("$qnt >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$qnt <=", "{$this->_table}.final", FALSE);
        return $this;
    }



    function  filter_by_intevalo_menor($qnt, $unidade_tempo = 'DIA'){

        $this->_database->where("{$this->_table}.final <", $qnt);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

    /**
    * Busca o Valor da tabela FIXA
    * @param $produto_parceiro_plano_id
    * @param $equipamento_nome
    * @return mixed|null
    */
    public function getValorTabelaFixa($produto_parceiro_plano_id, $equipamento_categoria_id, $equipamento_marca_id, $valor_nota = null, $data_nascimento = null){

        $valor = $this
            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
            ->filter_by_tipo_equipamento('TODOS')
            ->get_all();

        $valores = [];

        if(count($valor) > 0)
        {
            foreach ($valor as $vl) {
                if ($vl['cobranca'] == 'PORCENTAGEM') {
                    return app_calculo_porcentagem($vl['valor'], $valor_nota);
                } else {
                    $base = '';
                    switch ($vl['unidade_tempo']) {
                        case 'DIA':
                            $base = date('d');
                            break;
                        case 'MES':
                            $base = date('m');
                            break;
                        case 'ANO':
                            $base = date('Y');
                            break;
                        case 'VALOR':
                            $base = $valor_nota;
                            break;
                        case 'IDADE':
                            $dn = new DateTime($data_nascimento);
                            $d = $dn->diff(new DateTime());
                            $base = $d->y;
                            break;
                    }

                    if (!empty($base) && $base >= $vl['inicial'] && $base <= $vl['final']) {
                        return $vl['valor'];
                    }

                }
            }

        }else{

            //BUSCA POR CATEGORIA / LINHA
            $valor = $this
                ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                ->filter_by_tipo_equipamento('CATEGORIA')
                ->filter_by_equipamento($equipamento_categoria_id)
                ->get_all();

            if(count($valor) > 0) {
                $valor = $valor[0];
                if($valor['cobranca'] == 'PORCENTAGEM'){
                    return app_calculo_porcentagem($valor['valor'], $valor_nota);
                }else{
                    return $valor['valor'];
                }
            }

            /*
            $this->load->model('equipamento_model', 'equipamento');
            //BUSCA POR SUB CATEGORIA CATEGORIA / LINHA
            $valor = $this
                ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                ->filter_by_tipo_equipamento('POR CATEGORIA / LINHA')
                ->filter_by_equipamento($equipamento['equipamento_sub_categoria_id'])
                ->get_all();

            if(count($valor) > 0) {
                $valor = $valor[0];
                if($valor['cobranca'] == 'PORCENTAGEM'){
                    return app_calculo_porcentagem($valor['valor'], $valor_nota);
                }else{
                    return $valor['valor'];
                }
            }*/

            //BUSCA POR MARCA
            $valor = $this
                ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                ->filter_by_tipo_equipamento('MARCA')
                ->filter_by_equipamento($equipamento_marca_id)
                ->get_all();

            if(count($valor) > 0) {
                $valor = $valor[0];
                if($valor['cobranca'] == 'PORCENTAGEM'){
                    return app_calculo_porcentagem($valor['valor'], $valor_nota);
                }else{
                    return $valor['valor'];
                }
            }
        }

        return null;

    }

    /**
    * Retorna valores do plano
    * @param $produto_parceiro_id
    * @param int $num_passageiro
    * @return array
    */
    public function getValoresPlano( $produto_parceiro_id, $equipamento_marca_id, $equipamento_categora_id, $valor_nota, $quantidade = 1, $data_nascimento = null ){

        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('moeda_model', 'moeda');
        $this->load->model('moeda_cambio_model', 'moeda_cambio');
        $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');

        $moeda_padrao = $this->moeda->filter_by_moeda_padrao()->get_all();
        $moeda_padrao = $moeda_padrao[0];
        $quantidade = ((int)$quantidade <=0) ? 1 : (int)$quantidade;

        $produto_parceiro =  $this->current_model->get_by_id($produto_parceiro_id);
        if($produto_parceiro['venda_agrupada']) {
            $arrPlanos = $this->plano->distinct()
                ->with_produto_parceiro()
                ->with_produto()
                ->get_many_by(array(
                    'produto_parceiro.venda_agrupada' => 1
                ));
        }else{
            $arrPlanos = $this->plano->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        }

        $valores = array();
        foreach ($arrPlanos as $plano){

            $valor_cobertura_plano = 0;
            switch ((int)$plano['precificacao_tipo_id']) {
                case self::PRECO_TIPO_TABELA:

                    $calculo = $this->getValorTabelaFixa($plano['produto_parceiro_plano_id'], $equipamento_categora_id, $equipamento_marca_id, $valor_nota, $data_nascimento) * $quantidade;

                    if($calculo)
                        $valores[$plano['produto_parceiro_plano_id']] = $calculo;
                    else
                        return null;

                    if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
                        $valores[$plano['produto_parceiro_plano_id']] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$plano['produto_parceiro_plano_id']]);
                    }

                    break;
                case self::PRECO_TIPO_COBERTURA:

                    $this->load->model("produto_parceiro_plano_precificacao_model", "produto_parceiro_plano_precificacao");
                    $this->load->model("equipamento_model", "equipamento");
                    $this->load->model("cobertura_plano_model", "plano_cobertura");
                    $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
                    $arrCoberturas = $this->plano_cobertura->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)->get_all();
                    foreach ($arrCoberturas as $idx => $cob) {
                        if( $arrCoberturas[$idx]["mostrar"] == "importancia_segurada" ) {
                            $valor_cobertura_plano = $valor_cobertura_plano + floatval( $valor_nota ) * ( floatval( $arrCoberturas[$idx]["porcentagem"] ) / 100 );
                        }
                        if( $arrCoberturas[$idx]["mostrar"] == "preco" ) {
                            $valor_cobertura_plano = $valor_cobertura_plano + floatval( $arrCoberturas[$idx]["preco"] );
                        }
                    }
                    $valores[$produto_parceiro_plano_id] = $valor_cobertura_plano;
                    break;
                case self::PRECO_TIPO_VALOR_SEGURADO:
                    $this->load->model('equipamento_model', 'equipamento');
                    $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
                    $valor = $this
                    ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                    ->filter_by_tipo_equipamento("TODOS")
                    ->get_all();
                    $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );
                    break;
                case self::PRECO_POR_LINHA;
                    $this->load->model('equipamento_model', 'equipamento');
                    $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
                    $valor = $this
                        ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                        ->filter_by_faixa( $valor_nota )
                        ->filter_by_tipo_equipamento("CATEGORIA")
                        ->filter_by_equipamento($equipamento_categora_id)
                        ->get_all();

                    $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );

                    break;
                default:
                    break;
                }
            }

        return $valores;

    }

}
