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
            'rules' => 'required|enum[DIA,MES,ANO,VALOR,IDADE,COMISSAO]',
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
            'cobranca' => $this->input->post('cobranca'),
        );

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
    public function getValoresPlano( $produto_slug, $produto_parceiro_id, $produto_parceiro_plano_id, $equipamento_marca_id, $equipamento_categora_id, $valor_nota, $quantidade = 1, $data_nascimento = null, $equipamento_id = NULL, $servico_produto_id = NULL, $data_inicio_vigencia = NULL, $data_fim_vigencia = NULL, $comissao = NULL ){

        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('moeda_model', 'moeda');
        $this->load->model('moeda_cambio_model', 'moeda_cambio');
        $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');

        $moeda_padrao = $this->moeda->filter_by_moeda_padrao()->get_all();
        $moeda_padrao = $moeda_padrao[0];
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

        foreach ($arrPlanos as $plano){

            $valor_cobertura_plano = 0;
            switch ((int)$plano['precificacao_tipo_id']) {
                case $this->config->item("PRECO_TIPO_TABELA"):

                    $calculo = [];

                    if( $produto_slug == 'equipamento' ) {

                        $valor = $this
                            ->filter_by_produto_parceiro_plano($plano['produto_parceiro_plano_id'])
                            ->filter_by_tipo_equipamento('TODOS')
                            ->get_all();

                        $calculo = $this->getValorTabelaFixa($valor, $valor_nota, $comissao, $data_nascimento, $data_inicio_vigencia, $data_fim_vigencia);
                        $quantidade = $this->getQuantidade($quantidade, $data_inicio_vigencia, $data_fim_vigencia, $calculo['unidade']);

                        $calculo = $calculo['valor'] * $quantidade;

                    } elseif( $produto_slug == 'generico' || $produto_slug == 'seguro_saude' ) {

                        $vigencia = $this->plano->getInicioFimVigencia($plano['produto_parceiro_plano_id']);
                        $calculo = $this->getValorTabelaFixaGenerico($plano['produto_parceiro_plano_id'], $vigencia['dias'], $valor_nota, $data_nascimento, $comissao )*$quantidade;

                    }

                    if($calculo) {
                        $valores[$plano['produto_parceiro_plano_id']] = $calculo;

                        if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
                            $valores[$plano['produto_parceiro_plano_id']] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$plano['produto_parceiro_plano_id']]);
                        }
                    }

                    break;
                case $this->config->item("PRECO_TIPO_COBERTURA"):

                    $this->load->model("produto_parceiro_plano_precificacao_model", "produto_parceiro_plano_precificacao");
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
                case $this->config->item("PRECO_TIPO_VALOR_SEGURADO"):
                    $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
                    $valor = $this
                        ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                        ->filter_by_tipo_equipamento("TODOS")
                        ->get_all();
                    $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );
                    break;
                case $this->config->item("PRECO_POR_LINHA");
                    $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
                    $valor = $this
                        ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
                        ->filter_by_faixa( $valor_nota )
                        ->filter_by_tipo_equipamento("CATEGORIA")
                        ->filter_by_equipamento($equipamento_categora_id)
                        ->get_all();

                    $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );

                    break;
                case $this->config->item("PRECO_POR_EQUIPAMENTO");

                    $valor = $this
                        ->filter_by_produto_parceiro_plano($plano["produto_parceiro_plano_id"])
                        ->filter_by_faixa( $valor_nota )
                        ->filter_by_tipo_equipamento("EQUIPAMENTO")
                        ->filter_by_equipamento($equipamento_id)
                        ->get_all();

                    $calculo = $this->getValorTabelaFixa($valor, $valor_nota, $data_nascimento);

                    if($calculo) {
                        $calculo = $calculo['valor'] * $quantidade;
                        $valores[$plano['produto_parceiro_plano_id']] = $calculo;

                        if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
                            $valores[$plano['produto_parceiro_plano_id']] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$plano['produto_parceiro_plano_id']]);
                        }
                    }

                    break;
                case $this->config->item("PRECO_TIPO_FIXO_SERVICO"):

                    $this->load->model('produto_parceiro_plano_precificacao_servico_model', 'produto_parceiro_plano_precificacao_servico');
                    $preco = $this->produto_parceiro_plano_precificacao_servico
                        ->get_by(array(
                            'produto_parceiro_plano_id' => $plano['produto_parceiro_plano_id'],
                            'servico_produto_id' => $servico_produto_id,
                        ));

                    if($preco)
                    {
                        $valores[$plano['produto_parceiro_plano_id']] = (float) $preco['valor'] * (int) $quantidade;
                    }

                    break;
                default:
                    break;
                }
            }
        $valores['quantidade'] = $quantidade;

        return $valores;

    }

    private function getValorTabelaFixaGenerico($produto_parceiro_plano_id, $qntDias, $valor_nota = null, $data_nascimento = null, $comissao = null){

        $valor = $this->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
            ->filter_by_tipo('RANGE')
            ->get_all();

        foreach ($valor as $vl) {
            switch ($vl['unidade_tempo']) {
                case 'DIA':
                    $base = $qntDias;
                    break;
                case 'MES':
                    $base = floor($qntDias/30);
                    break;
                case 'ANO':
                    $base = floor($qntDias/365);
                    break;
                case 'VALOR':
                    $base = $valor_nota;
                    break;
                case 'IDADE':
                    $dn = new DateTime($data_nascimento);
                    $d = $dn->diff(new DateTime());
                    $base = $d->y;
                    break;
                case 'COMISSAO':
                    $base = $comissao;
                    break;
                default:
                    $base = 0;
                    break; 
            }

            if ($base >= $vl['inicial'] && $base <= $vl['final']) {
                return $vl['valor'];
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
    public function getValorTabelaFixa($valor, $valor_nota = null, $data_nascimento = null, $comissao = null, $data_inicio_vigencia = null, $data_fim_vigencia = null){

        $valores = ['unidade' => 1, 'valor' => null];
        if(count($valor) > 0)
        {
            foreach ($valor as $vl) {

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
                }

                if (!empty($base) && $base >= $vl['inicial'] && $base <= $vl['final']) {
                    if ($vl['cobranca'] == 'PORCENTAGEM') {
                        $valores['valor'] = app_calculo_porcentagem($vl['valor'], $valor_nota);
                    } else {
                        $valores['valor'] = $vl['valor'];
                    }
                    return $valores;
                }

            }

        }

        return null;

    }
}
