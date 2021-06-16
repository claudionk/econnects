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
            'rules' => 'required|enum[DIA,MES,ANO,VALOR,IDADE,COMISSAO,GARANTIA_FABRICANTE,VIGENCIA]',
            'groups' => 'default'
        ),
        array(
            'field' => 'inicial',
            'label' => 'Inicial',
            'rules' => 'required|numericOrDecimal',
            'groups' => 'default'
        ),
        array(
            'field' => 'final',
            'label' => 'Final',
            'rules' => 'required|numericOrDecimal',
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
            'field' => 'equipamento_de_para',
            'label' => 'equipamento_de_para',
            'groups' => 'default'
        ),
        array(
            'field' => 'cobranca',
            'label' => 'cobranca',
            'rules' => 'required|enum[VALOR,PORCENTAGEM]',
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
            'inicial' => app_unformat_currency($this->input->post('inicial')),
            'final' => app_unformat_currency($this->input->post('final')),
            'valor' => app_unformat_currency($this->input->post('valor')),
            'equipamento' => '',
            'marca' => '',
            'tipo_restricao_marca' => (!empty($this->input->post('tipo_restricao_marca'))? $this->input->post('tipo_restricao_marca'): null),
            'equipamento_de_para' => $this->input->post('equipamento_de_para'),
            'cobranca' => $this->input->post('cobranca'),
        );

        if( !empty($this->input->post('marca')) ) {
            $data['marca'] = "'" . implode ( "','", $this->input->post('marca') ) . "'";
        }        

        if( !empty($this->input->post('equipamento')) ) {
            $data['equipamento'] = "'" . implode ( "','", $this->input->post('equipamento') ) . "'";
            if ($this->input->post('precificacao_tipo_id') == 5){
                $data["tipo_equipamento"] = "EQUIPAMENTO";
            }

            if ($this->input->post('precificacao_tipo_id') == 6) {
                $data["tipo_equipamento"] = "CATEGORIA";
            }
        }
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function filter_by_produto_parceiro_plano($produto_parceiro_plano_id){

        $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);

        return $this;
    }

    function filter_by_tipo($tipo){

        $this->_database->where("{$this->_table}.tipo", $tipo);

        return $this;
    }

    function filter_by_tipo_equipamento($tipo){

        //$this->_database->where( "({$this->_table}.tipo_equipamento='$tipo' OR {$this->_table}.tipo_equipamento='TODOS')" );
        $this->_database->where("{$this->_table}.tipo_equipamento", $tipo);
        //$this->_database->or_where("{$this->_table}.tipo_equipamento", "TODOS");

        return $this;
    }

    function filter_by_equipamento($equipamento){
        $this->_database->like("{$this->_table}.equipamento", "'{$equipamento}'");
        return $this;
    }

    function filter_by_marca($marca){
        $this->_database->where("IF({$this->_table}.tipo_restricao_marca = 'LIB', {$this->_table}.marca LIKE '%$marca%', 1)");
        $this->_database->where("IF({$this->_table}.tipo_restricao_marca = 'RES', {$this->_table}.marca NOT LIKE '%$marca%', 1)");
        return $this;
    }

    /**
    * Filtra o preço pela vigência, somente quando houver data de adesão
    * @param $dataAdesao
    * @return mixed|null
    */
    function filter_by_vigencia_equipamento($dataAdesao){
        if(isset($dataAdesao) && !empty($dataAdesao)){
            $this->_database->where("('$dataAdesao' >=", "{$this->_table}.dt_inicio_vigencia OR dt_inicio_vigencia IS NULL)", FALSE);
            $this->_database->where("('$dataAdesao' <=", "{$this->_table}.dt_final_vigencia OR dt_final_vigencia IS NULL)", FALSE);
        }
        return $this;
    }

    function filter_by_equipamento_de_para($equipamento_de_para, $equipamento = null){
        $this->_database->where("{$this->_table}.equipamento_de_para", "{$equipamento_de_para}");

        if (!empty($equipamento))
        {
            $this->_database->where("IF({$this->_table}.equipamento = '', 1, {$this->_table}.equipamento LIKE \"%'{$equipamento}'%\")");
        }

        return $this;
    }

    function filter_by_intevalo_dias($qnt, $unidade_tempo = 'DIA'){

        $this->_database->where("$qnt >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$qnt <=", "{$this->_table}.final", FALSE);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

    function filter_by_faixa_etaria( $qnt, $unidade_tempo = "IDADE" ){

        $this->_database->where("$qnt >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$qnt <=", "{$this->_table}.final", FALSE);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

    function filter_by_faixa($qnt){
        $this->_database->where("$qnt >=", "{$this->_table}.inicial", FALSE);
        $this->_database->where("$qnt <=", "{$this->_table}.final", FALSE);
        return $this;
    }

    function filter_by_garantia_fabricante($qnt){
        $this->_database->where("{$this->_table}.garantia_fabricante <", $qnt);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

    function filter_by_intevalo_menor($qnt, $unidade_tempo = 'DIA'){

        $this->_database->where("{$this->_table}.final <", $qnt);
        $this->_database->where("{$this->_table}.unidade_tempo", $unidade_tempo);
        return $this;
    }

    public function getQuantidade($quantidade = 1, $data_inicio_vigencia = null, $data_fim_vigencia = null, $unidade = 'M') {
        // Se estiver configurado para informar o inicio e fim de vigência, irá fazer o cálculo com esta base
        if ( !empty($data_inicio_vigencia) && !empty($data_fim_vigencia) )
        {
            $quantidade = app_date_get_diff_vigencia($data_inicio_vigencia, $data_fim_vigencia, $unidade);
        }

        $quantidade = ((int)$quantidade <=0) ? 1 : (int)$quantidade;

        return $quantidade;
    }

    /**
    * Retorna valores do plano
    * @param $produto_parceiro_id
    * @param int $num_passageiro
    * @return array
    */
    public function getValoresPlano( $data_preco )
    {
        $produto_slug = $data_preco['produto_slug'];
        $produto_parceiro_id = $data_preco['produto_parceiro_id'];
        $produto_parceiro_plano_id = $data_preco['produto_parceiro_plano_id'];
        $equipamento_marca_id = $data_preco['equipamento_marca_id'];
        $equipamento_categoria_id = $data_preco['equipamento_categoria_id'];
        $valor_nota = $data_preco['valor_nota'];
        $cotacao_id = emptyor($data_preco['cotacao_id'], 0);
        $cotacao_aux_id = emptyor($data_preco['cotacao_aux_id'], NULL);
        $valor_fixo = emptyor($data_preco['valor_fixo'], NULL);
        $quantidade = emptyor($data_preco['quantidade'], 1);
        $data_nascimento = emptyor($data_preco['data_nascimento'], null);
        $equipamento_sub_categoria_id = emptyor($data_preco['equipamento_sub_categoria_id'], NULL);
        $equipamento_de_para = emptyor($data_preco['equipamento_de_para'], NULL);
        $servico_produto_id = emptyor($data_preco['servico_produto_id'], NULL);
        $data_inicio_vigencia = emptyor($data_preco['data_inicio_vigencia'], NULL);
        $data_fim_vigencia = emptyor($data_preco['data_fim_vigencia'], NULL);
        $comissao = emptyor($data_preco['comissao'], NULL);
        $data_adesao = emptyor($data_preco['data_adesao'], NULL);
        $garantia_fabricante = emptyor($data_preco['garantia_fabricante'], 0);
        $cotacao = emptyor($data_preco['cotacao'], NULL);

        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('moeda_model', 'moeda');
        $this->load->model('moeda_cambio_model', 'moeda_cambio');
        $this->load->model('cotacao_saude_faixa_etaria_model', 'faixa_etaria');

        $moeda_padrao = $this->moeda->filter_by_moeda_padrao()->get_all();
        $moeda_padrao = $moeda_padrao[0];

        // TODO: Criar configuração no plano para definir se o valor é por unidade de vigênica
        // Ex: Casos onde o valor do plano é variável de acordo com a vigência está configurado por mês, logo, valor x 5 = valor final
        $quantidade = $this->getQuantidade($quantidade, $data_inicio_vigencia, $data_fim_vigencia, 'M');

        $produto_parceiro =  $this->current_model->get_by_id($produto_parceiro_id);
        if($produto_parceiro['venda_agrupada']) {
            $arrPlanos = $this->plano->distinct()
                ->order_by('produto_parceiro_plano.ordem', 'asc')
                ->with_produto_parceiro()
                ->with_produto()
                ->get_many_by(array(
                    'produto_parceiro.venda_agrupada' => 1
                ));
        }else{
            $arrPlanos = $this->plano->order_by('produto_parceiro_plano.ordem', 'asc')->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        }

        $valores = array();

        foreach ($arrPlanos as $plano)
        {
            $valor_cobertura_plano = 0;
            $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
            $getVigencia = $this->plano->getInicioFimVigencia($produto_parceiro_plano_id, $data_inicio_vigencia, $cotacao);

            if ( !empty($valor_fixo) )
            {
                $valores[$produto_parceiro_plano_id] = $valor_fixo;

            } else {

                switch ((int)$plano['precificacao_tipo_id']) 
                {
                    case $this->config->item("PRECO_TIPO_TABELA"):

                        $calculo = [];

                        if( $produto_slug == 'equipamento' ) {
                            
                            $valor = $this
                                ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                                ->filter_by_tipo_equipamento('TODOS')
                                ->filter_by_marca($equipamento_marca_id)
                                ->get_all();

                            $dataTabelaFixa = new stdClass();
                            $dataTabelaFixa->valor = $valor;
                            $dataTabelaFixa->item = 'original';
                            $dataTabelaFixa->resultado = 'exato';
                            $dataTabelaFixa->valor_nota = $valor_nota;
                            $dataTabelaFixa->data_nascimento = $data_nascimento;
                            $dataTabelaFixa->comissao = $comissao;
                            $dataTabelaFixa->data_inicio_vigencia = $data_inicio_vigencia;
                            $dataTabelaFixa->data_fim_vigencia = $data_fim_vigencia;
                            $dataTabelaFixa->garantia_fabricante = $garantia_fabricante;
                            $dataTabelaFixa->aIgnore = [];
                            $dataTabelaFixa->produto_parceiro_plano_id = $produto_parceiro_plano_id;
                            $dataTabelaFixa->getVigencia = $getVigencia;

                            $calculo = $this->getValorTabelaFixa($dataTabelaFixa);
                            $quantidade = $this->getQuantidade($quantidade, $data_inicio_vigencia, $data_fim_vigencia, $calculo['unidade']);

                            $calculo = $calculo['valor'] * $quantidade;

                        } elseif( $produto_slug == 'generico' ) {

                            $vigencia = $this->plano->getInicioFimVigencia($produto_parceiro_plano_id);
                            $calculo = $this->getValorTabelaFixaGenerico($produto_parceiro_plano_id, $vigencia['dias'], $valor_nota, $data_nascimento, $comissao ) * $quantidade;

                        } elseif( $produto_slug == 'seguro_saude' ) {

                            // consulta as faixa etarias informadas
                            $faixas = $this->faixa_etaria->filter_by_cotacao($cotacao_id);
                            if ( !empty($cotacao_aux_id) )
                            {
                                $faixas = $faixas->filter_by_cotacao_auxiliar( $produto_slug, $cotacao_aux_id );
                            }
                            $faixas = $faixas->get_all();

                            // trata variável para concatenar
                            $calculo = !empty($faixas) ? 0 : NULL; 
                            foreach ($faixas as $fx)
                            {
                                if ( !empty($cotacao_aux_id) )
                                {
                                    $qtde = 1;
                                } else {
                                    $qtde = $fx['quantidade'];
                                }

                                $calculo += $this->getValorTabelaFixaGenerico($produto_parceiro_plano_id, $fx['inicio'], $valor_nota, $data_nascimento, $comissao ) * $qtde;
                            }

                        }

                        if($calculo) {
                            $valores[$produto_parceiro_plano_id] = $calculo;

                            if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
                                $valores[$produto_parceiro_plano_id] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$produto_parceiro_plano_id]);
                            }
                        }

                        break;
                    case $this->config->item("PRECO_TIPO_COBERTURA"):

                        $this->load->model("produto_parceiro_plano_precificacao_model", "produto_parceiro_plano_precificacao");
                        $this->load->model("cobertura_plano_model", "plano_cobertura");

                        //Validação de "cob_resp", sendo [0] a corbertura é uma assistencia e [[1] é a seguradora ou responsável. 
                        $arrCoberturas = $this->plano_cobertura->with_prod_parc($produto_parceiro_id, $produto_parceiro_plano_id)->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)->get_all();

                        foreach ($arrCoberturas as $idx => $cob) {
                            if( $arrCoberturas[$idx]["mostrar"] == "importancia_segurada" ) {
                                $valor_cobertura_plano = $valor_cobertura_plano + floatval( $valor_nota ) * ( floatval( $arrCoberturas[$idx]["porcentagem"] ) / 100 );
                            }
                            if( $arrCoberturas[$idx]["mostrar"] == "preco" ) {
                                $valor_cobertura_plano = $valor_cobertura_plano + floatval( $arrCoberturas[$idx]["preco"] );
                            }
                            if( $arrCoberturas[$idx]["mostrar"] == "descricao") {
                                // Correção do cálculo de premio para não considerar no cálculo as assistências (CAP, Assistencias). Se "cob_resp" igual a 0 a corbertura e uma assistencia. 
                                if($arrCoberturas[$idx]["cob_resp"] == '1'){
                                    $valor_cobertura_plano = $valor_cobertura_plano + floatval( $arrCoberturas[$idx]["custo"] );
                                }                               
                            }
                        }
                        $valores[$produto_parceiro_plano_id] = $valor_cobertura_plano;
                        break;
                    case $this->config->item("PRECO_TIPO_VALOR_SEGURADO"):
                        $valor = $this
                            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                            ->filter_by_tipo_equipamento("TODOS")
                            ->get_all();

                        $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );
                        break;
                    case $this->config->item("PRECO_POR_LINHA");
                        $valor = $this
                            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                            ->filter_by_faixa( $valor_nota )
                            ->filter_by_tipo_equipamento("CATEGORIA")
                            ->filter_by_equipamento($equipamento_categoria_id)
                            ->get_all();

                        $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );

                        break;
                    case $this->config->item("PRECO_POR_EQUIPAMENTO");

                        $this->load->model("cotacao_model", "cotacao");

                        $try = true;
                        $eqSubCatId = $equipamento_sub_categoria_id;
                        $eqCatId = $equipamento_categoria_id;

                        while ($try)
                        {
                            $try = false;

                            // tratamento para identificar o equipamento
                            $eqSubCatId = emptyor($eqSubCatId, $eqCatId);

                            // Se não passar, tentar pegar a categoria/sub pelo modelo
                            if ( empty($equipamento_de_para) && empty($eqSubCatId) )
                            {
                                $cot = $this->cotacao->with_cotacao_equipamento()->with_cotacao_equipamento_modelo()->filterByID($cotacao_id)->get_all();
                                if ( !empty($cot) )
                                {
                                    $cot = $cot[0];
                                    $eqSubCatId = $cot['equipamento_sub_categoria_id'];
                                    $eqCatId = $cot['equipamento_categoria_id'];

                                    $eqSubCatId = emptyor($eqSubCatId, $eqCatId);
                                }
                            }

                            $query = $this
                                ->filter_by_produto_parceiro_plano($plano["produto_parceiro_plano_id"])
                                ->filter_by_faixa( $valor_nota )
                                ->filter_by_tipo_equipamento("EQUIPAMENTO")
                                ->filter_by_vigencia_equipamento($data_adesao);

                            // Caso tenha um DE x PARA
                            if ( !empty($equipamento_de_para) ) {

                                $valor = $query
                                    ->filter_by_equipamento_de_para($equipamento_de_para, $eqSubCatId)
                                    ->get_all();

                            } else {

                                $valor = $query
                                    ->filter_by_equipamento($eqSubCatId)
                                    ->get_all();

                                // nao encontrou resultado
                                // a categoria é diferente da sub
                                // existe categoria válida
                                if ( empty($valor) && $eqSubCatId <> $eqCatId && !empty($eqCatId) )
                                {
                                    $eqSubCatId = $eqCatId;
                                    $try = true;
                                }
                            }
                        }

                        $dataTabelaFixa = new stdClass();
                        $dataTabelaFixa->valor = $valor;
                        $dataTabelaFixa->item = 'original';
                        $dataTabelaFixa->resultado = 'exato';
                        $dataTabelaFixa->valor_nota = $valor_nota;
                        $dataTabelaFixa->data_nascimento = $data_nascimento;
                        $dataTabelaFixa->comissao = $comissao;
                        $dataTabelaFixa->data_inicio_vigencia = $data_inicio_vigencia;
                        $dataTabelaFixa->data_fim_vigencia = $data_fim_vigencia;
                        $dataTabelaFixa->garantia_fabricante = $garantia_fabricante;
                        $dataTabelaFixa->aIgnore = [];
                        $dataTabelaFixa->produto_parceiro_plano_id = $produto_parceiro_plano_id;
                        $dataTabelaFixa->getVigencia = $getVigencia;

                        $calculo = $this->getValorTabelaFixa($dataTabelaFixa);
                        if($calculo)
                        {
                            $calculo = $calculo['valor'];
                            $valores[$produto_parceiro_plano_id] = $calculo;

                            if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
                                $valores[$produto_parceiro_plano_id] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$produto_parceiro_plano_id]);
                            }
                        }

                        break;
                    case $this->config->item("PRECO_TIPO_FIXO_SERVICO"):

                        $this->load->model('produto_parceiro_plano_precificacao_servico_model', 'produto_parceiro_plano_precificacao_servico');
                        $preco = $this->produto_parceiro_plano_precificacao_servico
                            ->get_by(array(
                                'produto_parceiro_plano_id' => $produto_parceiro_plano_id,
                                'servico_produto_id' => $servico_produto_id,
                            ));

                        if($preco)
                        {
                            $valores[$produto_parceiro_plano_id] = (float) $preco['valor'] * (int) $quantidade;
                        }

                        break;
                    default:
                        break;
                }
            }
        }

        if ( !empty($valores))
        {
            $valores['quantidade'] = $quantidade;
        }

        return $valores;

    }

    /**
    * Retorna valores do plano
    * @param $produto_parceiro_id
    * @param int $num_passageiro
    * @return array
    */
    public function getValoresPlanoMulti( $data_preco )
    {
        $produto_slug = $data_preco['produto_slug'];
        $produto_parceiro_id = $data_preco['produto_parceiro_id'];
        $produto_parceiro_plano_id = $data_preco['produto_parceiro_plano_id'];
        $equipamento_marca_id = $data_preco['equipamento_marca_id'];
        $equipamento_categoria_id = $data_preco['equipamento_categoria_id'];
        $valor_nota = $data_preco['valor_nota'];
        $cotacao_id = emptyor($data_preco['cotacao_id'], 0);
        $cotacao_aux_id = emptyor($data_preco['cotacao_aux_id'], NULL);
        $valor_fixo = emptyor($data_preco['valor_fixo'], NULL);
        $quantidade = emptyor($data_preco['quantidade'], 1);
        $data_nascimento = emptyor($data_preco['data_nascimento'], null);
        $equipamento_sub_categoria_id = emptyor($data_preco['equipamento_sub_categoria_id'], NULL);
        $equipamento_de_para = emptyor($data_preco['equipamento_de_para'], NULL);
        $servico_produto_id = emptyor($data_preco['servico_produto_id'], NULL);
        $data_inicio_vigencia = emptyor($data_preco['data_inicio_vigencia'], NULL);
        $data_fim_vigencia = emptyor($data_preco['data_fim_vigencia'], NULL);
        $comissao = emptyor($data_preco['comissao'], NULL);
        $data_adesao = emptyor($data_preco['data_adesao'], NULL);
        $garantia_fabricante = emptyor($data_preco['garantia_fabricante'], 0);
        $vigencia_mes = emptyor($data_preco['vigencia_mes'], 0);

        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('moeda_model', 'moeda');
        $this->load->model('moeda_cambio_model', 'moeda_cambio');
        $this->load->model('cotacao_saude_faixa_etaria_model', 'faixa_etaria');

        $moeda_padrao = $this->moeda->filter_by_moeda_padrao()->get_all();
        $moeda_padrao = $moeda_padrao[0];

        // TODO: Criar configuração no plano para definir se o valor é por unidade de vigênica
        // Ex: Casos onde o valor do plano é variável de acordo com a vigência está configurado por mês, logo, valor x 5 = valor final
        $quantidade = $this->getQuantidade($quantidade, $data_inicio_vigencia, $data_fim_vigencia, 'M');

        $produto_parceiro =  $this->current_model->get_by_id($produto_parceiro_id);
        if($produto_parceiro['venda_agrupada']) {
            $arrPlanos = $this->plano->distinct()
                ->order_by('produto_parceiro_plano.ordem', 'asc')
                ->with_produto_parceiro()
                ->with_produto()
                ->get_many_by(array(
                    'produto_parceiro.venda_agrupada' => 1
                ));
        }else{
            $arrPlanos = $this->plano->order_by('produto_parceiro_plano.ordem', 'asc')->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        }

        $valores = $valoresMulti = array();

        foreach ($arrPlanos as $plano)
        {
            $valor_cobertura_plano = 0;
            $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
            $getVigencia = $this->plano->getInicioFimVigencia($produto_parceiro_plano_id);

            if ( !empty($valor_fixo) )
            {
                $valores[$produto_parceiro_plano_id] = $valor_fixo;

            } else {

                switch ((int)$plano['precificacao_tipo_id']) 
                {
                    case $this->config->item("PRECO_TIPO_TABELA"):

                        $calculo = [];

                        if( $produto_slug == 'equipamento' ) {

                            $valor = $this
                                ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                                ->filter_by_tipo_equipamento('TODOS')
                                ->get_all();

                            $dataTabelaFixa = new stdClass();
                            $dataTabelaFixa->valor = $valor;
                            $dataTabelaFixa->item = 'original';
                            $dataTabelaFixa->resultado = 'todos';
                            $dataTabelaFixa->valor_nota = $valor_nota;
                            $dataTabelaFixa->data_nascimento = $data_nascimento;
                            $dataTabelaFixa->comissao = $comissao;
                            $dataTabelaFixa->data_inicio_vigencia = $data_inicio_vigencia;
                            $dataTabelaFixa->data_fim_vigencia = $data_fim_vigencia;
                            $dataTabelaFixa->garantia_fabricante = $garantia_fabricante;
                            $dataTabelaFixa->vigencia_mes = $vigencia_mes;
                            $dataTabelaFixa->aIgnore = ['VIGENCIA'];
                            $dataTabelaFixa->produto_parceiro_plano_id = $produto_parceiro_plano_id;
                            $dataTabelaFixa->getVigencia = $getVigencia;

                            $calculo = $this->getValorTabelaFixa($dataTabelaFixa);

                            if ( !isset($calculo[0]) )
                            {
                                $calculo[] = $calculo;
                            }

                            foreach ($calculo as $k => $v)
                            {
                                $quantidade = $this->getQuantidade($quantidade, $data_inicio_vigencia, $data_fim_vigencia, $calculo[$k]['unidade']);
                                $calculo[$k]['valor'] = $calculo[$k]['valor'] * $quantidade;
                            }

                        } elseif( $produto_slug == 'generico' ) {

                            $vigencia = $this->plano->getInicioFimVigencia($produto_parceiro_plano_id);
                            $calculo = $this->getValorTabelaFixaGenerico($produto_parceiro_plano_id, $vigencia['dias'], $valor_nota, $data_nascimento, $comissao ) * $quantidade;

                        } elseif( $produto_slug == 'seguro_saude' ) {

                            // consulta as faixa etarias informadas
                            $faixas = $this->faixa_etaria->filter_by_cotacao($cotacao_id);
                            if ( !empty($cotacao_aux_id) )
                            {
                                $faixas = $faixas->filter_by_cotacao_auxiliar( $produto_slug, $cotacao_aux_id );
                            }
                            $faixas = $faixas->get_all();

                            // trata variável para concatenar
                            $calculo = !empty($faixas) ? 0 : NULL; 
                            foreach ($faixas as $fx)
                            {
                                if ( !empty($cotacao_aux_id) )
                                {
                                    $qtde = 1;
                                } else {
                                    $qtde = $fx['quantidade'];
                                }

                                $calculo += $this->getValorTabelaFixaGenerico($produto_parceiro_plano_id, $fx['inicio'], $valor_nota, $data_nascimento, $comissao ) * $qtde;
                            }

                        }

                        if($calculo)
                        {
                            foreach ($calculo as $k => $v)
                            {
                                $valoresMulti[$produto_parceiro_plano_id][] = [
                                    'unidade' => $v['dados']['unidade_tempo'],
                                    'inicio' => (float)$v['dados']['inicial'],
                                    'fim' => (float)$v['dados']['final'],
                                    'valor_base' => ($moeda_padrao['moeda_id'] != $plano['moeda_id']) ? $this->moeda_cambio->getValor($plano['moeda_id'], $v['valor']) : $v['valor'],
                                    'valor_liquido' => 0,
                                    'valor_bruto' => 0,
                                ];
                            }
                        }

                        break;
                    case $this->config->item("PRECO_TIPO_COBERTURA"):

                        $this->load->model("produto_parceiro_plano_precificacao_model", "produto_parceiro_plano_precificacao");
                        $this->load->model("cobertura_plano_model", "plano_cobertura");
                        
                        $arrCoberturas = $this->plano_cobertura->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)->get_all();
                        foreach ($arrCoberturas as $idx => $cob) {
                            if( $arrCoberturas[$idx]["mostrar"] == "importancia_segurada" ) {
                                $valor_cobertura_plano = $valor_cobertura_plano + floatval( $valor_nota ) * ( floatval( $arrCoberturas[$idx]["porcentagem"] ) / 100 );
                            }
                            if( $arrCoberturas[$idx]["mostrar"] == "preco" ) {
                                $valor_cobertura_plano = $valor_cobertura_plano + floatval( $arrCoberturas[$idx]["preco"] );
                            }
                            if( $arrCoberturas[$idx]["mostrar"] == "descricao") {
                                $valor_cobertura_plano = $valor_cobertura_plano + floatval( $arrCoberturas[$idx]["custo"] );
                            }
                        }
                        $valores[$produto_parceiro_plano_id] = $valor_cobertura_plano;
                        break;
                    case $this->config->item("PRECO_TIPO_VALOR_SEGURADO"):
                        $valor = $this
                            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                            ->filter_by_tipo_equipamento("TODOS")
                            ->filter_by_marca($equipamento_marca_id)
                            ->get_all();

                        $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );
                        break;
                    case $this->config->item("PRECO_POR_LINHA");
                        $valor = $this
                            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                            ->filter_by_faixa( $valor_nota )
                            ->filter_by_tipo_equipamento("CATEGORIA")
                            ->filter_by_equipamento($equipamento_categoria_id)
                            ->filter_by_marca($equipamento_marca_id)
                            ->get_all();

                        $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );

                        break;
                    case $this->config->item("PRECO_POR_EQUIPAMENTO");

                        $this->load->model("cotacao_model", "cotacao");

                        $try = true;
                        $eqSubCatId = $equipamento_sub_categoria_id;
                        $eqCatId = $equipamento_categoria_id;

                        while ($try)
                        {
                            $try = false;

                            // tratamento para identificar o equipamento
                            $eqSubCatId = emptyor($eqSubCatId, $eqCatId);

                            // Se não passar, tentar pegar a categoria/sub pelo modelo
                            if ( empty($equipamento_de_para) && empty($eqSubCatId) )
                            {
                                $cot = $this->cotacao->with_cotacao_equipamento()->with_cotacao_equipamento_modelo()->filterByID($cotacao_id)->get_all();
                                if ( !empty($cot) )
                                {
                                    $cot = $cot[0];
                                    $eqSubCatId = $cot['equipamento_sub_categoria_id'];
                                    $eqCatId = $cot['equipamento_categoria_id'];

                                    $eqSubCatId = emptyor($eqSubCatId, $eqCatId);
                                }
                            }

                            $query = $this
                                ->filter_by_produto_parceiro_plano($plano["produto_parceiro_plano_id"])
                                ->filter_by_faixa( $valor_nota )
                                ->filter_by_vigencia_equipamento($data_adesao)
                                ->filter_by_tipo_equipamento("EQUIPAMENTO")
                                ->filter_by_marca($equipamento_marca_id);

                            // Caso tenha um DE x PARA
                            if ( !empty($equipamento_de_para) ) {

                                $valor = $query
                                    ->filter_by_equipamento_de_para($equipamento_de_para)
                                    ->get_all();

                            } else {

                                $valor = $query
                                    ->filter_by_equipamento($eqSubCatId)
                                    ->get_all();

                                // nao encontrou resultado
                                // a categoria é diferente da sub
                                // existe categoria válida
                                if ( empty($valor) && $eqSubCatId <> $eqCatId && !empty($eqCatId) )
                                {
                                    $eqSubCatId = $eqCatId;
                                    $try = true;
                                }
                            }
                        }

                        $dataTabelaFixa = new stdClass();
                        $dataTabelaFixa->valor = $valor;
                        $dataTabelaFixa->item = 'original';
                        $dataTabelaFixa->resultado = 'todos';
                        $dataTabelaFixa->valor_nota = $valor_nota;
                        $dataTabelaFixa->data_nascimento = $data_nascimento;
                        $dataTabelaFixa->comissao = $comissao;
                        $dataTabelaFixa->data_inicio_vigencia = $data_inicio_vigencia;
                        $dataTabelaFixa->data_fim_vigencia = $data_fim_vigencia;
                        $dataTabelaFixa->garantia_fabricante = $garantia_fabricante;
                        $dataTabelaFixa->vigencia_mes = $vigencia_mes;
                        $dataTabelaFixa->aIgnore = ['VIGENCIA'];
                        $dataTabelaFixa->produto_parceiro_plano_id = $produto_parceiro_plano_id;
                        $dataTabelaFixa->getVigencia = $getVigencia;

                        $calculo = $this->getValorTabelaFixa($dataTabelaFixa);

                        if($calculo) {
                            $calculo = $calculo['valor'];
                            $valores[$produto_parceiro_plano_id] = $calculo;

                            if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
                                $valores[$produto_parceiro_plano_id] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$produto_parceiro_plano_id]);
                            }
                        }

                        break;
                    case $this->config->item("PRECO_TIPO_FIXO_SERVICO"):

                        $this->load->model('produto_parceiro_plano_precificacao_servico_model', 'produto_parceiro_plano_precificacao_servico');
                        $preco = $this->produto_parceiro_plano_precificacao_servico
                            ->get_by(array(
                                'produto_parceiro_plano_id' => $produto_parceiro_plano_id,
                                'servico_produto_id' => $servico_produto_id,
                            ));

                        if($preco)
                        {
                            $valores[$produto_parceiro_plano_id] = (float) $preco['valor'] * (int) $quantidade;
                        }

                        break;
                    default:
                        break;
                }
            }
        }

        // merge preservando os indices
        $valores = array_replace($valores, $valoresMulti);

        if ( !empty($valores))
        {
            // cria um padrão de retorno
            foreach ($valores as $key => $value) {
                if ( !is_array($value))
                {
                    $valores[$key] = [[
                        'unidade' => 'NAO_APLICAVEL',
                        'inicio' => 0,
                        'fim' => 0,
                        'valor_base' => $value,
                        'valor_liquido' => 0,
                        'valor_bruto' => 0,
                    ]];
                }
            }

            $valores['quantidade'] = $quantidade;
        }

        return $valores;
    }

    /**
    * Busca o valor da tabela FIXA
     * @param $produto_parceiro_plano_id
     * @param $equipamento_nome
     * @return mixed|null
     */
    private function getValorTabelaFixaGenerico($produto_parceiro_plano_id, $qnt, $valor_nota = null, $data_nascimento = null, $comissao = null){

        $tabela = $this->getTabelaFixaGenerico($produto_parceiro_plano_id, $qnt, $valor_nota, $data_nascimento, $comissao);
        if ( !empty($tabela) )
        {
            return $tabela['valor'];
        }

         return null;
    }

    /**
    * Busca a tabela FIXA
     * @param $produto_parceiro_plano_id
     * @param $equipamento_nome
     * @return mixed|null
     */
    public function getTabelaFixaGenerico($produto_parceiro_plano_id, $qnt, $valor_nota = null, $data_nascimento = null, $comissao = null){

        $valor = $this->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
            ->filter_by_tipo('RANGE')
            ->get_all();

        foreach ($valor as $vl) {
            switch ($vl['unidade_tempo']) {
                case 'DIA':
                    $base = $qnt;
                    break;
                case 'MES':
                    $base = floor($qnt/30);
                    break;
                case 'ANO':
                    $base = floor($qnt/365);
                    break;
                case 'VALOR':
                    $base = $valor_nota;
                    break;
                case 'IDADE':
                    if ( !empty($data_nascimento) && $data_nascimento != "0000-00-00" )
                    {
                        $dn = new DateTime($data_nascimento);
                        $d = $dn->diff(new DateTime());
                        $base = $d->y;
                    } else 
                    {
                        $base = $qnt;
                    }
                    break;
                case 'COMISSAO':
                    $base = $comissao;
                    break;
                default:
                    $base = 0;
                    break; 
            }

            if ($base >= $vl['inicial'] && $base <= $vl['final']) {
                return $vl;
            }
        }

        return null;
    }

    /**
    * Busca o Valor da tabela FIXA
    * @param $produto_parceiro_plano_id
    * @param $equipamento_nome
    * @return mixed|null
    */
    public function getValorTabelaFixa($dataTabelaFixa)
    {
        $valor = $dataTabelaFixa->valor;
        $item = emptyor($dataTabelaFixa->item, 'original');
        $resultado = emptyor($dataTabelaFixa->resultado, 'exato');
        $valor_nota = emptyor($dataTabelaFixa->valor_nota, null);
        $data_nascimento = emptyor($dataTabelaFixa->data_nascimento, null);
        $comissao = emptyor($dataTabelaFixa->comissao, null);
        $data_inicio_vigencia = emptyor($dataTabelaFixa->data_inicio_vigencia, null);
        $data_fim_vigencia = emptyor($dataTabelaFixa->data_fim_vigencia, null);
        $garantia_fabricante = emptyor($dataTabelaFixa->garantia_fabricante, null);
        $vigencia_mes = emptyor($dataTabelaFixa->vigencia_mes, null);
        $aIgnore = emptyor($dataTabelaFixa->aIgnore, []);
        $getVigencia = emptyor($dataTabelaFixa->getVigencia, []);

        $valores = ['unidade' => 1, 'valor' => null, 'dados' => []];
        $returnAll = [];
        if(count($valor) > 0)
        {
            if ($item == 'original')
            {
                $this->load->model('produto_parceiro_plano_precificacao_itens_config_model', 'produto_parceiro_plano_itens_config');
            }

            foreach ($valor as $vl)
            {
                $base = '';
                switch ($vl['unidade_tempo']) {
                    case 'DIA':
                        $base = date('d');
                        $valores['unidade'] = 'D';
                        break;
                    case 'MES':
                        $base = date('m');
                        $valores['unidade'] = 'M';
                        break;
                    case 'ANO':
                        // $base = date('Y');
                        $base = $this->getQuantidade(1, $data_inicio_vigencia, $data_fim_vigencia, 'Y');
                        $valores['unidade'] = 'Y';
                        break;
                    case 'VALOR':
                        $base = $valor_nota;
                        $valores['unidade'] = 'V';
                        break;
                    case 'IDADE':
                        $dn = new DateTime($data_nascimento);
                        $d = $dn->diff(new DateTime());
                        $base = $d->y;
                        $valores['unidade'] = 'I';
                        break;
                    case 'COMISSAO':
                        $base = $comissao;
                        $valores['unidade'] = 'C';
                        break;
                    case 'GARANTIA_FABRICANTE':
                        $base = $garantia_fabricante;
                        $valores['unidade'] = 'F';
                        break;
                    case 'VIGENCIA':
                        $base = emptyor($vigencia_mes, emptyor($getVigencia['meses'], 0), 0);
                        $valores['unidade'] = 'G';
                        break;
                }

                $valide = false;
                $range = ($base >= $vl['inicial'] && $base <= $vl['final']);

                // identificou algum valor recebido
                if (!empty($base))
                {
                    // solicitou ignorar filtro DE x PARA
                    if (!empty($aIgnore) && in_array($vl['unidade_tempo'], $aIgnore))
                    {
                        // Não passou filtro de vigencia, deverá exibir todos
                        if ( $vl['unidade_tempo'] == 'VIGENCIA' && empty($vigencia_mes))
                        {
                            $valide = true;
                        }
                    }

                    if (!$valide && $range)
                        $valide = true;
                }

                //print_pre([$vl['unidade_tempo'], $valide, $base, $aIgnore, $vl], false);
                if ($valide)
                {
                    $valores['dados'] = $vl;

                    if ($item == 'original')
                    {
                        $configs = $this->produto_parceiro_plano_itens_config->filter_by_produto_parceiro_plano_precificacao_itens($vl['produto_parceiro_plano_precificacao_itens_id'])->get_all();

                        if ( !empty($configs) )
                        {
                            $dataTabelaFixa->valor = $configs;
                            $dataTabelaFixa->item = 'config';
                            $ret = $this->getValorTabelaFixa($dataTabelaFixa);
                            if ( empty($ret) )
                            {
                                continue;
                            }
                        }

                        if ($vl['cobranca'] == 'PORCENTAGEM') {
                            $valores['valor'] = app_calculo_porcentagem($vl['valor'], $valor_nota);
                        } else {
                            $valores['valor'] = $vl['valor'];
                        }

                        $returnAll[] = $valores;
                    }

                    // calculo da cotação ignorando algum parâmetro para exibir multiplos valores
                    if ( empty($aIgnore) || !in_array($vl['unidade_tempo'], $aIgnore))
                    {
                        return $valores;
                    }
                }
            }
        }

        return ($resultado == 'todos') ? $returnAll : null;
    }

    public function get_all_faixa_etaria_by_produto($produto_parceiro_id, $cotacao_id = 0)
    {
        $cotacao_id = emptyor($cotacao_id, 0);
        $this->_database->distinct();
        $this->_database->select("{$this->_table}.inicial, {$this->_table}.final, ifnull(cotacao_saude_faixa_etaria.quantidade,0) as faixa_etaria", FALSE);
        $this->_database->join("produto_parceiro_plano", "produto_parceiro_plano.produto_parceiro_plano_id = {$this->_table}.produto_parceiro_plano_id");
        $this->_database->join("produto_parceiro", "produto_parceiro_plano.produto_parceiro_id = produto_parceiro.produto_parceiro_id");
        $this->_database->join("cotacao_saude_faixa_etaria", "cotacao_saude_faixa_etaria.cotacao_id = {$cotacao_id} and  cotacao_saude_faixa_etaria.inicio = {$this->_table}.inicial and cotacao_saude_faixa_etaria.deletado = 0", "left", false);
        $this->_database->where("produto_parceiro.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->where("produto_parceiro_plano.deletado", 0);
        $this->_database->where("{$this->_table}.unidade_tempo", 'IDADE');
        return $this;
    }

    function update_config()
    {
        $id = $this->input->post('produto_parceiro_plano_precificacao_itens_id');
        $data = $this->get_form_data();
        $this->update($id, $data, TRUE);
        $this->validItensMultiples($id);
    }

    function insert_config()
    {
        $data = $this->get_form_data();
        $id = $this->insert($data, TRUE);
        $this->validItensMultiples($id);
    }

    function validItensMultiples($id)
    {
        // Emissão
        if ( $this->input->post('unidade_tempo_') )
        {
            $this->load->model('produto_parceiro_plano_precificacao_itens_config_model', 'produto_parceiro_plano_itens_config');
            $this->produto_parceiro_plano_itens_config->remove_itens_config($id);

            foreach ($this->input->post('unidade_tempo_') as $key => $value)
            {
                $dt = [
                    'produto_parceiro_plano_precificacao_itens_id' => $id,
                    'unidade_tempo' => $value,
                    'inicial' => app_unformat_currency($this->input->post('inicial_')[$key]),
                    'final' => app_unformat_currency($this->input->post('final_')[$key]),
                ];

                $this->produto_parceiro_plano_itens_config->insert($dt, TRUE);
            }
        }
    }

}
