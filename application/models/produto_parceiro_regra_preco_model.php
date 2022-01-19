<?php
Class Produto_Parceiro_Regra_Preco_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_regra_preco';
    protected $primary_key = 'produto_parceiro_regra_preco_id';

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

    const TIPO_CALCULO_NET = 1;
    const TIPO_CALCULO_BRUTO = 2;

    //Dados
    public $validate = array(
        array(
            'field' => 'regra_preco_id',
            'label' => 'Regra Preço',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false) {
        //Dados
      if( intval( $this->input->post('regra_preco_id') ) == 2 ) {
        $data =  array(
            'regra_preco_id' => $this->input->post('regra_preco_id'),
            'parametros' => app_unformat_currency( $this->input->post('parametros') ),
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id')
        );
      } else {
        $data =  array(
            'regra_preco_id' => $this->input->post('regra_preco_id'),
            'parametros' => $this->input->post('parametros'),
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id')
        );
      }
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_regra_preco(){
        $this->with_simple_relation('regra_preco', 'regra_preco_', 'regra_preco_id', array('nome','slug'));
        return $this;
    }

    function  filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);

        return $this;
    }

    /**
    * Efetua calculo, retorna JSON
    */
    public function calculo_plano( $params = array(), $api = false ) {
        if (empty($params)) {
            $params = $_POST;
        }

        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('produto_parceiro_campo_model', 'campo');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model('produto_parceiro_plano_model', 'plano');
        $this->load->model('cobertura_plano_model', 'plano_cobertura');
        $this->load->model('cobertura_model', 'cobertura');
        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_cobertura_model', 'cotacao_cobertura');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('produto_parceiro_desconto_model', 'desconto');
        $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
        $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
        $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
        $this->load->model('servico_produto_model', 'servico_produto');

        $sucess = TRUE;
        $messagem = '';
        $desconto_upgrade = 0;
        $data_base                      = issetor($params['data_base'], null);
        $produto_parceiro_id            = issetor($params['produto_parceiro_id'], 0);
        $parceiro_id                    = issetor($params['parceiro_id'], 0);
        $equipamento_id                 = issetor($params['equipamento_id'], 0);
        $equipamento_marca_id           = issetor($params['equipamento_marca_id'], 0);
        $equipamento_sub_categoria_id   = issetor($params['equipamento_sub_categoria_id'], 0);
        $equipamento_categoria_id       = issetor($params['equipamento_categoria_id'], 0);
        $equipamento_de_para            = issetor($params['equipamento_de_para'], '');
        $quantidade                     = issetor($params['quantidade'], 0);
        $valor_fixo                     = issetor($params['valor_fixo'], NULL);
        $coberturas_adicionais          = issetor($params['coberturas_adicionais'], []);
        $pCoberturas                    = issetor($params['coberturas'], []);
        $repasse_comissao               = app_unformat_percent(issetor($params['repasse_comissao'],0));
        $desconto_condicional           = app_unformat_percent(issetor($params['desconto_condicional'],0));
        $desconto                       = $this->desconto->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        $cotacao_id                     = issetor($params['cotacao_id'], 0);
        $cotacao_aux_id                 = issetor($params['cotacao_aux_id'], 0);
        $garantia_fabricante            = issetor($params['garantia_fabricante'], 0);

        if($cotacao_id) {
            $pedido = $this->pedido->filter_by_cotacao($cotacao_id)->get_all();
            if( $pedido ) {
                ob_clean();
                return array( 
                    "status" => false, 
                    "message" => "Não foi possível efetuar o calculo. Motivo: já existe um pedido para essa cotação.",
                    "pedido_id" => $pedido[0]['pedido_id']
                );
            }

            $cotacao = $this->cotacao->get($cotacao_id);

            if(($cotacao) && ((int)$cotacao['cotacao_upgrade_id']) > 0){
                $pedido_antigo = $this->pedido->get_by(array('cotacao_id' => $cotacao['cotacao_upgrade_id']));
                $desconto_upgrade = $pedido_antigo['valor_total'];
            }
        }

        // Valida a vigencia enviada por cobertura
        foreach ($pCoberturas as $cob)
        {
            if ( empty($data_inicio_vigencia) && !empty($cob['data_inicio_vigencia']) && $cob['data_inicio_vigencia'] != "0000-00-00" )
                $data_inicio_vigencia = $cob['data_inicio_vigencia'];

            if ( empty($data_fim_vigencia) && !empty($cob['data_fim_vigencia']) && $cob['data_fim_vigencia'] != "0000-00-00" )
                $data_fim_vigencia = $cob['data_fim_vigencia'];

            if ( !empty($data_inicio_vigencia) && !empty($data_fim_vigencia) )
                break;
        }

        $row =  $this->produto_parceiro->with_produto()->get_by_id($produto_parceiro_id);

        if($row['produto_slug'] == 'seguro_viagem'){
            $cotacao = $this->cotacao->with_cotacao_seguro_viagem();
        }elseif($row['produto_slug'] == 'equipamento') {
            $cotacao = $this->cotacao->with_cotacao_equipamento();
        }elseif( $row["produto_slug"] == "generico" || $row["produto_slug"] == "seguro_saude" ) {
            $cotacao = $this->cotacao->with_cotacao_generico();
        }

        $cotacao = $cotacao
            ->filterByID($cotacao_id)
            ->get_all();

        $cotacao = $cotacao[0];
        $produto_parceiro_plano_id = $cotacao["produto_parceiro_plano_id"];
        $data_inicio_vigencia = emptyor($data_inicio_vigencia, issetor($cotacao['data_inicio_vigencia'], null));
        $data_fim_vigencia = emptyor($data_fim_vigencia, issetor($cotacao['data_fim_vigencia'], null));
        $garantia_fabricante = emptyor($garantia_fabricante, issetor($cotacao['garantia_fabricante'], 0));
        $servico_produto_id = issetor($cotacao['servico_produto_id'],0);
        $servico_produto = $this->servico_produto->get($servico_produto_id);
        if($servico_produto && $quantidade < $servico_produto['quantidade_minima'])
        {
            $quantidade = $servico_produto['quantidade_minima'];
        }

        $result  = array(
            'status' => FALSE,
            'mensagem' => 'TABELA DE PREÇO NÃO CONFIGURADA',
            'produto_parceiro_id' => $produto_parceiro_id,
            'repasse_comissao' => 0,
            'comissao' => 0,
            'comissao_corretor' => 0,
            'desconto_upgrade' => 0,
            'desconto_condicional_valor' => 0,
            'quantidade' => $quantidade,
        );

        if (!$api) {
            $result['valores_bruto'] = 0;
            $result['valores_cobertura_adicional'] = 0;
            $result['valores_totais_cobertura_adicional'] = 0;
            $result['valores_liquido'] = 0;
            $result['valores_liquido_total'] = 0;
        }

        // Comissão para realizar o calculo do premio
        $comissao = isempty($params['comissao_premio'], issetor($cotacao['comissao_premio'], 0));

        // Valida comissão negativa
        if (number_format($comissao, 2, ',', '.') < number_format(0, 2, ',', '.') && ($parceiro_id <> 72 && $parceiro_id <> 76))
        {
            $comissao = number_format($comissao, 2, ',', '.');
            $result['mensagem'] = "A comissão não pode ser negativa [{$comissao}%]";
            return $result;
        }

        if(count($desconto) > 0){
            $desconto = $desconto[0];
        }else{
            $desconto = array('habilitado' => 0);
        }

        $configuracao = $this->configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
        if(count($configuracao) > 0){
            $configuracao = $configuracao[0];
        }else{
            $configuracao = array();
        }

        $markup = 0;
        if($row["parceiro_id"] != $parceiro_id) {

            $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $parceiro_id, $comissao);
            if (!empty($rel))
            {
                $configuracao['repasse_comissao']   = $rel['repasse_comissao'];
                $configuracao['repasse_maximo']     = $rel['repasse_maximo'];
                $configuracao['comissao']           = $rel['comissao'];
                $configuracao['comissao_corretor']  = $this->relacionamento->get_comissao_corretor($produto_parceiro_id);
            }

            //buscar o markup
            $markup = $this->relacionamento->get_comissao_markup($produto_parceiro_id, $parceiro_id, $comissao);

            // Converte em string para igualar os tipos de variáveis
            // Apresentou rejeição após converter as 2 variaveis para float direto
            // Foi presico prmeiramente igualar em string para só então converter em float
            $markup = (string)$markup;
            $configuracao['markup'] = (string)$configuracao['markup'];

            $markup = (float)$markup;
            $configuracao['markup'] = (float)$configuracao['markup'];

            if ( $markup > $configuracao['markup'] )
            {
                $markup = number_format($markup, 2, ',', '.');
                $configuracao['markup'] = number_format($configuracao['markup'], 2, ',', '.');
                $result['mensagem'] = "O markup [{$markup}] ultrapassou o máximo permitido [{$configuracao['markup']}]";
                return $result;
            }

            $rel_desconto = $this->relacionamento->get_desconto($produto_parceiro_id, $parceiro_id);
            if(count($rel_desconto) > 0){
                $desconto['data_ini'] = $rel_desconto['desconto_data_ini'];
                $desconto['data_fim'] = $rel_desconto['desconto_data_fim'];
                $desconto['habilitado'] = $rel_desconto['desconto_habilitado'];
            }else{
                $desconto = array('habilitado' => 0);
            }
        }

        if($repasse_comissao > $configuracao['repasse_maximo'] ){
            $repasse_comissao = $configuracao['repasse_maximo'];
        }

        $repasse_comissao = str_pad(number_format((double)$repasse_comissao, 2, '.', ''), 5, "0", STR_PAD_LEFT);
        $comissao_corretor = (isempty($configuracao['comissao_corretor'], 0) - $repasse_comissao);

        $data_preco = [
            'cotacao_id' => $cotacao_id,
            'cotacao_aux_id' => $cotacao_aux_id,
            'valor_fixo' => $valor_fixo,
            'produto_slug' => $row['produto_slug'],
            'produto_parceiro_id' => $produto_parceiro_id,
            'produto_parceiro_plano_id' => $produto_parceiro_plano_id,
            'equipamento_marca_id' => $equipamento_marca_id,
            'equipamento_categoria_id' => $equipamento_categoria_id,
            'valor_nota' => $cotacao['nota_fiscal_valor'],
            'quantidade' => $quantidade,
            'data_nascimento' => $cotacao['data_nascimento'],
            'equipamento_sub_categoria_id' => $equipamento_sub_categoria_id,
            'equipamento_de_para' => $equipamento_de_para,
            'servico_produto_id' => $servico_produto_id,
            'data_inicio_vigencia' => $data_inicio_vigencia,
            'data_fim_vigencia' => $data_fim_vigencia,
            'comissao' => $comissao,
            'data_adesao' => emptyor($cotacao['data_adesao'], $cotacao['nota_fiscal_data']),
            'garantia_fabricante' => $cotacao['garantia_fabricante'],
        ];

        $valores_bruto = $this->produto_parceiro_plano_precificacao_itens->getValoresPlano($data_preco);

        $valores_cobertura_adicional_total = $valores_cobertura_adicional = array();

        if($coberturas_adicionais){
            foreach ($coberturas_adicionais as $coberturas_adicional) {
                $cobertura = explode(';', $coberturas_adicional);
                $vigencia = $this->plano->getInicioFimVigencia($cobertura[0], $data_base, $cotacao);

                $valor = $this->getValorCoberturaAdicional($cobertura[0], $cobertura[1], $vigencia['dias']);
                $valores_cobertura_adicional_total[$cobertura[0]] = (isset($valores_cobertura_adicional_total[$cobertura[0]])) ? ($valores_cobertura_adicional_total[$cobertura[0]] + $valor) : $valor;
                $valores_cobertura_adicional[$cobertura[0]][] = $valor;
            }
        }
        $erro_valores_bruto = $this->produto_parceiro_plano_precificacao_itens->erroValidacaoValoresPlano($valores_bruto);
        if($erro_valores_bruto) {
            $result["mensagem"] = $erro_valores_bruto;
            return $result;
        }

        $valores_liquido = $planoActual = $aFilter = array();

        $arrPlanos = $this->plano
            ->distinct()
            ->order_by('produto_parceiro_plano.ordem', 'asc')
            ->wtih_plano_habilitado($parceiro_id);

        if($row['venda_agrupada']){

            $arrPlanos = $arrPlanos
                ->with_produto_parceiro()
                ->with_produto();

            $aFilter = array(
                'produto_parceiro.venda_agrupada' => 1,
                //'produto_parceiro_plano.produto_parceiro_id' => $produto_parceiro_id,
            );

        }else {
            $arrPlanos = $arrPlanos
                ->filter_by_produto_parceiro($produto_parceiro_id);
        }

        if((isset($cotacao['origem_id'])) && ($cotacao['origem_id'])){
            $arrPlanos->with_origem($cotacao['origem_id']);
        }
        if((isset($cotacao['destino_id'])) && ($cotacao['destino_id'])){
            $arrPlanos->with_destino($cotacao['destino_id']);
        }
        if((isset($cotacao['faixa_salarial_id'])) && ($cotacao['faixa_salarial_id'])){
            $arrPlanos->with_faixa_salarial($cotacao['faixa_salarial_id']);
        }

        $arrPlanos = $arrPlanos->get_many_by($aFilter);

        /**
        * FAZ O CÁLCULO DO PLANO
        */
        $desconto_condicional_valor = 0;
        foreach( $arrPlanos as $plano ) {

            if ($plano['produto_parceiro_plano_id'] == $produto_parceiro_plano_id) {
                $planoActual = $plano;
            }

            //precificacao_tipo_id
            if (!$api || $plano['produto_parceiro_plano_id'] == $produto_parceiro_plano_id ) {
                if ( !empty($valor_fixo) )
                {
                    $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor_fixo;
                } 
                else 
                {
                    switch ((int)$configuracao['calculo_tipo_id']) {
                        case self::TIPO_CALCULO_NET:
                            $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
                            $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
                            $valor = ($valor/(1-($markup/100)));
                            $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
                            $valor -= $desconto_condicional_valor;
                            $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
                            break;
                        case self::TIPO_CALCULO_BRUTO:
                            $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
                            $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
                            $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
                            $valor -= $desconto_condicional_valor;
                            $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        // busca a cobertura
        $regra_preco = $this->with_regra_preco()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        $iof = 0;
        $valores_liquido_total = array();
        foreach ($valores_liquido as $key => $value) {
            $valores_liquido_total[$key] = $value;
            $valores_liquido_total_cobertura[$key] = 0;
            $valores_liquido_total_round[$key] = 0;
            $iof_calculado = false;

            // Tratando se tem IOF - default para todas as coberturas
            $r = $this->plano_cobertura->with_prod_parc_iof($key)->get_all();
            foreach ($r as $regra) {
                $iof_calculado = true;

                // trunca o valor do IOF por cobertura
                $iofPerc = ($regra['iof']/100) * ($regra['custo'] * $valores_bruto['quantidade']);
                $valores_liquido_total_cobertura[$key] += truncate($iofPerc, 2);
                $valores_liquido_total_round[$key] += $iofPerc;
                $iof = $regra['iof'];
            }

            if ($iof_calculado) {

                // arredonda a soma to IOF do bilhete
                $valores_liquido_total_cobertura[$key] = round($valores_liquido_total_cobertura[$key], 2);
                $valores_liquido_total_round[$key] = round($valores_liquido_total_round[$key], 2);

                // Caso o IOF calculado sobre o prêmio da apólice/bilhete seja = 0,00
                if ($valores_liquido_total_cobertura[$key] == 0)
                {
                    // deve-se atribuir à cobertura de maior peso na formação do prêmio o valor de 0,01, propagando o mesmo valor para o IOF da apólice;
                    $valores_liquido_total_cobertura[$key] = 0.01;
                } elseif ($valores_liquido_total_cobertura[$key] != $valores_liquido_total_round[$key])
                {
                    $valores_liquido_total_cobertura[$key] += $valores_liquido_total_round[$key] - $valores_liquido_total_cobertura[$key];
                }

                $valores_liquido_total[$key] += $valores_liquido_total_cobertura[$key];
                $valores_liquido_total[$key] -= $desconto_upgrade;
            }

            foreach ($regra_preco as $regra) {
                if( $iof_calculado && strtoupper($regra["regra_preco_nome"]) == "IOF" ) {
                    continue;
                }

                $valores_liquido_total[$key] += (($regra['parametros']/100) * $value);
                $valores_liquido_total[$key] -= $desconto_upgrade;

                if( strtoupper($regra["regra_preco_nome"]) == "IOF" ) {
                    $iof = $regra['parametros'];
                }
            }
        }

        if ( isset($valores_liquido) ) {
            foreach ($valores_liquido as $key => $value) {
                $valores_liquido[$key] = trim($valores_liquido[$key]);
            }
        }

        if ( isset($valores_liquido_total) ) {
            foreach ($valores_liquido_total as $key => $value) {
                $valores_liquido_total[$key] = trim($valores_liquido_total[$key]);
            }
        }

        //Resultado
        $result  = array(
            'status' => $sucess,
            'mensagem' => 'Cálculo realizado com sucesso',
            'produto_parceiro_id' => $produto_parceiro_id,
            'repasse_comissao' => $repasse_comissao,
            'comissao' => $comissao,
            'comissao_corretor' => $comissao_corretor,
            'desconto_upgrade' => $desconto_upgrade,
            'desconto_condicional_valor' => $desconto_condicional_valor,
            'premio_liquido' => number_format((float)$valores_liquido[$produto_parceiro_plano_id], 2, '.' , ''),
            'premio_liquido_total' => number_format((float)$valores_liquido_total[$produto_parceiro_plano_id], 2, '.' , ''),
            'iof' => (float)$iof,
        );

        if (!$api) {
            $result['valores_bruto'] = $valores_bruto;
            $result['valores_cobertura_adicional'] = $valores_cobertura_adicional;
            $result['valores_totais_cobertura_adicional'] = $valores_cobertura_adicional_total;
            $result['valores_liquido'] = $valores_liquido;
            $result['valores_liquido_total'] = $valores_liquido_total;
        }

        //Salva cotação
        if($cotacao_id) {

            if($row['produto_slug'] == 'seguro_viagem')
            {
                $cotacao_salva = $this->cotacao->with_cotacao_seguro_viagem();
                if ( !empty($cotacao_aux_id) )
                {
                    $cotacao_salva = $cotacao_salva->filter_by_cotacao_seguro_viagem_id($cotacao_aux_id);
                }

            }elseif($row['produto_slug'] == 'equipamento')
            {
                $cotacao_salva = $this->cotacao->with_cotacao_equipamento();
                if ( !empty($cotacao_aux_id) )
                {
                    $cotacao_salva = $cotacao_salva->filter_by_cotacao_equipamento_id($cotacao_aux_id);
                }
            }elseif( $row["produto_slug"] == "generico" || $row["produto_slug"] == "seguro_saude" )
            {
                $cotacao_salva = $this->cotacao->with_cotacao_generico();
                if ( !empty($cotacao_aux_id) )
                {
                    $cotacao_salva = $cotacao_salva->filter_by_cotacao_generico_id($cotacao_aux_id);
                }
            }

            $cotacao_salva = $cotacao_salva
                ->filterByID($cotacao_id)
                ->get_all();

            $cotacao_salva = $cotacao_salva[0];
            $coberturas = [];
            $cotacao_eqp = [];
            $cotacao_eqp['repasse_comissao'] = $repasse_comissao;
            $cotacao_eqp['comissao_corretor'] = $comissao_corretor;
            $cotacao_eqp['desconto_condicional'] = $desconto_condicional;
            $cotacao_eqp['desconto_condicional_valor'] = $desconto_condicional_valor;
            $cotacao_eqp['premio_liquido'] = round($valores_liquido[$planoActual['produto_parceiro_plano_id']], 2);
            $cotacao_eqp['premio_liquido_total'] = round($valores_liquido_total[$planoActual['produto_parceiro_plano_id']], 2);
            $cotacao_eqp['iof'] = $iof;

            if (!empty($produto_parceiro_plano_id)){

                if($row['produto_slug'] == 'seguro_viagem'){
                    $cotacaoUpdate = $this->cotacao_seguro_viagem;
                    $cotacao_item_id = $cotacao_salva['cotacao_seguro_viagem_id'];
                }elseif($row['produto_slug'] == 'equipamento') {
                    $cotacaoUpdate = $this->cotacao_equipamento;
                    $cotacao_item_id = $cotacao_salva['cotacao_equipamento_id'];
                }elseif( $row["produto_slug"] == "generico" || $row["produto_slug"] == "seguro_saude" ) {
                    $cotacaoUpdate = $this->cotacao_generico;
                    $cotacao_item_id = $cotacao_salva['cotacao_generico_id'];
                }

                $cotacaoUpdate->update($cotacao_item_id, $cotacao_eqp, TRUE);
            }

            // Só gera os dados de cobertura quando calcular todo o plano
            if ( empty($cotacao_aux_id) )
            {
                $coberturas = $this->cotacao_cobertura->geraCotacaoCobertura($cotacao_id, $produto_parceiro_id, $produto_parceiro_plano_id, $cotacao["nota_fiscal_valor"], $cotacao_eqp['premio_liquido'], $pCoberturas);
            }

            $result["importancia_segurada"] = $cotacao["nota_fiscal_valor"];
            $result["coberturas"] = $coberturas;

            error_log( "Cotação: " . print_r( $cotacao_salva, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
        }

        return $result;
    }

    private function getValorCoberturaAdicional($produto_parceiro_plano_id, $cobertura_plano_id, $qntDias){
        $this->load->model('cobertura_plano_model', 'cobertura_plano');

        $cobertura = $this->cobertura_plano->get_by(array(
            'produto_parceiro_plano_id' => $produto_parceiro_plano_id,
            'cobertura_plano_id' => $cobertura_plano_id,
        ));

        if($cobertura){
            switch ($cobertura['mostrar']) {
                case 'preco':
                    return $cobertura['preco'];
                    break;
                default:
                    return (app_calculo_porcentagem($cobertura['porcentagem'],$cobertura['preco'])*$qntDias);
                    break;
            }

        }else{
            return 0;
        }
    }

}

