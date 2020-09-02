<?php
Class Cotacao_Equipamento_Model extends MY_Model
{
  //Dados da tabela e chave primária
  protected $_table = 'cotacao_equipamento';
  protected $primary_key = 'cotacao_equipamento_id';

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
      'field' => 'cotacao_id',
      'label' => 'Cotacao id',
      'rules' => 'required|numeric',
      'groups' => 'default',
      'foreign' => 'cotacao'
    ),

    array(
      'field' => 'produto_parceiro_id',
      'label' => 'Produto parceiro id',
      'rules' => 'required|numeric',
      'groups' => 'default',
      'foreign' => 'produto_parceiro_plano'
    ),
    array(
      'field' => 'equipamento_nome',
      'label' => 'Equipamento Nome',
      'rules' => 'required',
      'groups' => 'default',
    ),
    array(
      'field' => 'equipamento_categoria_id',
      'label' => 'Equipamento Categoria ID',
      'rules' => 'required',
      'groups' => 'default',
      // 'foreign' => 'equipamento_categoria',
    ),
    array(
      'field' => 'equipamento_marca_id',
      'label' => 'Equipamento Marca ID',
      'rules' => 'required',
      'groups' => 'default',
      // 'foreign' => 'equipamento_marca',
    ),
    array(
      'field' => 'step',
      'label' => 'Step',
      'rules' => '',
      'groups' => 'default',
    ),

    array(
      'field' => 'nome',
      'label' => 'Nome',
      'rules' => 'required',
      'groups' => 'default',
    ),
    array(
      'field' => 'email',
      'label' => 'E-mail',
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
      'field' => 'data_nascimento',
      'label' => 'Data Nascimento',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'nota_fiscal_data',
      'label' => 'Data Nota Fiscal',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'nota_fiscal_valor',
      'label' => 'Valor Nota Fiscal',
      'rules' => 'required',
      'groups' => 'default',
    ),
    array(
      'field' => 'nota_fiscal_numero',
      'label' => 'Número Nota Fiscal',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'nome_mae',
      'label' => 'Nome da Mãe',
      'rules' => 'required',
      'groups' => 'default',
    ),

    array(
      'field' => 'cnpj_cpf',
      'label' => 'CPF',
      'rules' => 'required|numeric',
      'groups' => 'default',
      'foreign' => 'origem'
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
      'field' => 'data_inicio_vigencia',
      'label' => 'data_inicio_vigencia',
      'rules' => '',
      'groups' => 'default'
    ),
  );

  public function __construct(){

    parent::__construct();


    $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
  }


  /**
     * Insere ou faz update na cotação de equipamento
     * @param $produto_parceiro_id
     * @param int $cotacao_id
     *
     */
    public function insert_update($produto_parceiro_id, $cotacao_id = 0, $step = 1, $coberturas_adicionais = null){

        $this->load->model('cliente_model', 'cliente');
        $this->load->model('cliente_codigo_model', 'cliente_codigo');
        $this->load->model('cotacao_codigo_model', 'cotacao_codigo');
        $this->load->model('cotacao_equipamento_cobertura_model', 'cotacao_equipamento_cobertura');
        $this->load->model('produto_parceiro_regra_preco_model', 'produto_parceiro_regra_preco');
        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        $this->load->model('apolice_model', 'apolice');
        $this->load->model('parceiro_model', 'parceiro');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');

        $cotacao = $this->session->userdata("cotacao_{$produto_parceiro_id}");
        $carrossel = $this->session->userdata("carrossel_{$produto_parceiro_id}");

        if($cotacao_id) {
            $cotacao_salva = $this->cotacao->with_cotacao_equipamento()->filterByID($cotacao_id)->get_all(0,0,false);
            $cotacao_salva = $cotacao_salva[0];
        }else{
            $cotacao_salva = array();
        }

        if(!$cotacao){
            $cotacao = $cotacao_salva;
        }

        $produto_parceiro =  $this->current_model->get_by_id($produto_parceiro_id);
        $configuracao = $this->produto_parceiro_configuracao->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if(count($configuracao) > 0){
            $configuracao = $configuracao[0];
        }else{
            $configuracao = array();
        }

        if($produto_parceiro['parceiro_id'] != $this->session->userdata('parceiro_id')){
            $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $this->session->userdata('parceiro_id'));
            $configuracao['repasse_comissao'] = issetor($rel['repasse_comissao'], 0);
            $configuracao['repasse_maximo'] = issetor($rel['repasse_maximo'], 0);
            $configuracao['comissao'] = issetor($rel['comissao'], 0);
        }

        $regra_preco = $this->produto_parceiro_regra_preco->with_regra_preco()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->get_all();

        $iof = 0;
        if($regra_preco && isset($regra_preco[0]) && isset($regra_preco[0]['parametros'])){
            $iof = app_format_currency($regra_preco[0]['parametros']);
        }

        //faz o Insert ou UPdate do Cliente
        $cliente = $this->cliente->cotacao_insert_update($cotacao);

        if($cotacao_id){
            $dt_cotacao = array();
            $dt_cotacao['usuario_cotacao_id'] = issetor($cotacao["usuario_cotacao_id"], $this->session->userdata('usuario_id'));
            $dt_cotacao['parceiro_id'] = ( isset( $cotacao["parceiro_id"]) ? $cotacao["parceiro_id"] : $this->session->userdata("parceiro_id") );
            $dt_cotacao['usuario_venda_id'] = 0;
            $dt_cotacao['cotacao_status_id'] = !empty($cotacao_salva["cotacao_status_id"]) ? $cotacao_salva["cotacao_status_id"] : 1;
            $dt_cotacao['alteracao_usuario_id'] = 1;
            if( isset( $cotacao["data_inicio_vigencia"] ) ) {
                $dt_cotacao["data_inicio_vigencia"] = app_dateonly_mask_to_mysql($cotacao["data_inicio_vigencia"]);
            }
            if( isset( $cotacao["data_fim_vigencia"] ) ) {
                $dt_cotacao["data_fim_vigencia"] = app_dateonly_mask_to_mysql($cotacao["data_fim_vigencia"]);
            }

            $this->cotacao->update($cotacao_id,  $dt_cotacao, TRUE);
        }

        $data_cotacao = array();
        $data_cotacao['step'] = $step;
        $data_cotacao['produto_parceiro_id'] = $produto_parceiro_id;

        if(isset($carrossel['plano'])) {

            $planos = explode(';', $carrossel['plano']);

            $valores = explode(';', $carrossel['valor']);
            $comissao_repasse = explode(';', $carrossel['comissao_repasse']);
            $desconto_condicional = explode(';', $carrossel['desconto_condicional']);
            $desconto_condicional_valor = explode(';', $carrossel['desconto_condicional_valor']);
            $valores_totais = explode(';', $carrossel['valor_total']);


            $data_cotacao['produto_parceiro_plano_id'] = $planos[0];
            $data_cotacao['cotacao_id'] = $cotacao_id;
            $data_cotacao['repasse_comissao'] = ((isset($comissao_repasse[0])) && ($comissao_repasse[0]))  ? app_unformat_percent($comissao_repasse[0]) : 0;
            $data_cotacao['comissao_corretor'] = $configuracao['comissao'] - ((isset($comissao_repasse[0])) && ($comissao_repasse[0]))  ? app_unformat_percent($comissao_repasse[0]) : 0;
            $data_cotacao['desconto_condicional'] = app_unformat_percent($desconto_condicional[0]);
            $data_cotacao['desconto_condicional_valor'] = app_unformat_percent($desconto_condicional_valor[0]);
            $data_cotacao['premio_liquido'] = app_unformat_currency($valores[0]);
            $data_cotacao['premio_liquido_total'] = app_unformat_currency($valores_totais[0]);
            $data_cotacao['iof'] = app_unformat_percent($iof);

        } else {
            if(isset($cotacao['repasse_comissao'])){
                $data_cotacao['repasse_comissao'] = app_unformat_currency($cotacao['repasse_comissao']);
            }
            if(isset($cotacao['comissao_corretor'])){
                $data_cotacao['comissao_corretor'] = app_unformat_currency($cotacao['comissao_corretor']);
            }
            if(isset($cotacao['desconto_condicional'])){
                $data_cotacao['desconto_condicional'] = app_unformat_currency($cotacao['desconto_condicional']);
            }
            if(isset($cotacao['desconto_condicional_valor'])){
                $data_cotacao['desconto_condicional_valor'] = app_unformat_currency($cotacao['desconto_condicional_valor']);
            }
            if(isset($cotacao['iof'])){
                $data_cotacao['iof'] = app_unformat_currency($cotacao['iof']);
            }
            if(isset($cotacao['premio_liquido']) && !empty($cotacao['premio_liquido'])){
                $data_cotacao['premio_liquido'] = app_unformat_currency($cotacao['premio_liquido']);
            }
            if(isset($cotacao['premio_liquido_total']) && !empty($cotacao['premio_liquido_total'])){
                $data_cotacao['premio_liquido_total'] = app_unformat_currency($cotacao['premio_liquido_total']);
            }
        }

        $data_cotacao = array_merge( $cotacao, $data_cotacao );
        unset( $data_cotacao["parceiro_id"] );
        unset( $data_cotacao["usuario_cotacao_id"] );
        unset( $data_cotacao["url_busca_cliente"] );

        if(isset($cotacao['produto_parceiro_plano_id'])){
            $data_cotacao['produto_parceiro_plano_id'] = $cotacao['produto_parceiro_plano_id'];
        }

        if(isset($cotacao['ean'])){
            $data_cotacao['ean'] = $cotacao['ean'];
        }
        if(isset($cotacao['equipamento_id'])){
            $data_cotacao['equipamento_id'] = $cotacao['equipamento_id'];
        }
        if(isset($cotacao['equipamento_nome'])){
            $data_cotacao['equipamento_nome'] = $cotacao['equipamento_nome'];
        }
        if(isset($cotacao['equipamento_marca_id'])){
            $data_cotacao['equipamento_marca_id'] = $cotacao['equipamento_marca_id'];
        }
        if(isset($cotacao['equipamento_categoria_id'])){
            $data_cotacao['equipamento_categoria_id'] = $cotacao['equipamento_categoria_id'];
        }
        if(isset($cotacao['nome'])){
            $data_cotacao['nome'] = $cotacao['nome'];
        }
        if(isset($cotacao['sexo'])){
            $data_cotacao['sexo'] = $cotacao['sexo'];
        }

        if(isset($cotacao['email'])){
            $data_cotacao['email'] = $cotacao['email'];
        }

        if(isset($cotacao['nome_mae'])){
            $data_cotacao['nome_mae'] = $cotacao['nome_mae'];
        }

        if(isset($cotacao['telefone'])){
            $data_cotacao['telefone'] = app_retorna_numeros($cotacao['telefone']);
        }

        if(isset($cotacao['cnpj_cpf'])){
            $data_cotacao['cnpj_cpf'] = app_completa_cpf_cnpj($cotacao['cnpj_cpf']);
        }

        if(isset($cotacao['rg'])){
            $data_cotacao['rg'] = $cotacao['rg'];
        }

        if(isset($cotacao['data_nascimento'])){
            $data_cotacao['data_nascimento'] =  app_dateonly_mask_to_mysql($cotacao['data_nascimento']);
        }
        
        if(isset($cotacao['garantia_fabricante'])){
            $data_cotacao['garantia_fabricante'] =  app_dateonly_mask_to_mysql($cotacao['garantia_fabricante']);
        }

        if(isset($cotacao['nota_fiscal_data'])){
            $data_cotacao['nota_fiscal_data'] =  app_dateonly_mask_to_mysql($cotacao['nota_fiscal_data']);
        }

        if(isset($cotacao['nota_fiscal_valor'])){
            if( strpos( $cotacao['nota_fiscal_valor'], "," ) !== false ) {
                $data_cotacao['nota_fiscal_valor'] = app_unformat_currency($cotacao['nota_fiscal_valor']);
            } else {
                $data_cotacao['nota_fiscal_valor'] = $cotacao['nota_fiscal_valor'];
            }
        }
        if(isset($cotacao['nota_fiscal_numero'])){
            $data_cotacao['nota_fiscal_numero'] =  $cotacao['nota_fiscal_numero'];
        }

        if(isset($cotacao['endereco_cep'])){
            $data_cotacao['endereco_cep'] = $cotacao['endereco_cep'];
        }
        if(isset($cotacao['endereco_logradouro'])){
            $data_cotacao['endereco_logradouro'] = $cotacao['endereco_logradouro'];
        }
        if(isset($cotacao['endereco_numero'])){
            $data_cotacao['endereco_numero'] = $cotacao['endereco_numero'];
        }
        if(isset($cotacao['endereco_complemento'])){
            $data_cotacao['endereco_complemento'] = $cotacao['endereco_complemento'];
        }
        if(isset($cotacao['endereco_bairro'])){
            $data_cotacao['endereco_bairro'] = $cotacao['endereco_bairro'];
        }
        if(isset($cotacao['endereco_cidade'])){
            $data_cotacao['endereco_cidade'] = $cotacao['endereco_cidade'];
        }
        if(isset($cotacao['endereco_estado'])){
            $data_cotacao['endereco_estado'] = $cotacao['endereco_estado'];
        }
        if(isset($cotacao['origem_id'])){
            $data_cotacao['origem_id'] = $cotacao['origem_id'];
        }
        if(isset($cotacao['destino_id'])){
            $data_cotacao['destino_id'] = $cotacao['destino_id'];
        }
        if(isset($cotacao['faixa_salarial_id'])){
            $data_cotacao['faixa_salarial_id'] = $cotacao['faixa_salarial_id'];
        }

        $data_cotacao['quantidade'] = issetor($cotacao['quantidade'], 1);

        if(isset($cotacao['estado_civil'])){
            $data_cotacao['estado_civil'] = $cotacao['estado_civil'];
        }

        if(isset($cotacao['rg_orgao_expedidor'])){
            $data_cotacao['rg_orgao_expedidor'] = $cotacao['rg_orgao_expedidor'];
        }

        if(isset($cotacao['rg_uf'])){
            $data_cotacao['rg_uf'] = $cotacao['rg_uf'];
        }

        if(!empty($cotacao['rg_data_expedicao']))
          $data_cotacao['rg_data_expedicao'] = app_dateonly_mask_to_mysql($cotacao['rg_data_expedicao']);
        else
          $data_cotacao['rg_data_expedicao'] = '0000-00-00';

        if(isset($cotacao['aux_01'])){
            $data_cotacao['aux_01'] = $cotacao['aux_01'];
        }
        if(isset($cotacao['aux_02'])){
            $data_cotacao['aux_02'] = $cotacao['aux_02'];
        }

        if(isset($cotacao['aux_03'])){
            $data_cotacao['aux_03'] = $cotacao['aux_03'];
        }

        if(isset($cotacao['aux_04'])){
            $data_cotacao['aux_04'] = $cotacao['aux_04'];
        }

        if(isset($cotacao['aux_05'])){
            $data_cotacao['aux_05'] = $cotacao['aux_05'];
        }

        if(isset($cotacao['aux_06'])){
            $data_cotacao['aux_06'] = $cotacao['aux_06'];
        }

        if(isset($cotacao['aux_07'])){
            $data_cotacao['aux_07'] = $cotacao['aux_07'];
        }

        if(isset($cotacao['aux_08'])){
            $data_cotacao['aux_08'] = $cotacao['aux_08'];
        }

        if(isset($cotacao['aux_09'])){
            $data_cotacao['aux_09'] = $cotacao['aux_09'];
        }

        if(isset($cotacao['aux_10'])){
            $data_cotacao['aux_10'] = $cotacao['aux_10'];
        }

        if( isset( $cotacao["data_inicio_vigencia"] ) ) {
            $data_cotacao["data_inicio_vigencia"] = app_dateonly_mask_to_mysql($cotacao["data_inicio_vigencia"]);
        }

        if( isset( $cotacao["data_fim_vigencia"] ) ) {
            $data_cotacao["data_fim_vigencia"] = app_dateonly_mask_to_mysql($cotacao["data_fim_vigencia"]);
        }

        if(isset($cotacao['valor_desconto'])){
            if( strpos( $cotacao['valor_desconto'], "," ) !== false ) {
                $data_cotacao['valor_desconto'] = app_unformat_currency($cotacao['valor_desconto']);
            } else {
                $data_cotacao['valor_desconto'] = $cotacao['valor_desconto'];
            }
        }

        if($cotacao_salva) {
            $cotacao_id = $cotacao_salva['cotacao_id'];
            $this->update($cotacao_salva['cotacao_equipamento_id'], $data_cotacao, TRUE);
            $cotacao_equipamento_id = $cotacao_salva['cotacao_equipamento_id'];
        } else {
            $parceiro_id = issetor( $cotacao["parceiro_id"], $this->session->userdata("parceiro_id") );
            $parceiro = $this->parceiro->get($parceiro_id);
            $produto_parceiro = $this->produto_parceiro->get($produto_parceiro_id);

            $data_template = [
                'sigla_loja'   => $parceiro['slug'],
                'cod_sucursal' => $produto_parceiro['cod_sucursal'],
                'cod_ramo'     => $produto_parceiro['cod_ramo'],
                'cod_operacao' => $produto_parceiro['cod_tpa'],
                'ano_AA'       => date('y'),
                'ano_AAAA'     => date('Y'),
                'mes_MM'       => date('m'),
            ];

            if ( !empty($data_cotacao['produto_parceiro_plano_id']) )
            {
                $plano = $this->produto_parceiro_plano->get($data_cotacao['produto_parceiro_plano_id']);
                $data_template['cod_produto'] = $plano['codigo_operadora'];
            }

            //salva cotacão
            $dt_cotacao = array();
            $dt_cotacao['cliente_id'] = $cliente['cliente_id'];
            $dt_cotacao['codigo'] = $this->cotacao_codigo->get_codigo_cotacao_formatado('BE');
            $dt_cotacao['cotacao_tipo'] = 'ONLINE';
            $dt_cotacao['numero_apolice'] = $this->apolice->defineNumApolice($produto_parceiro_id, 'cotacao', null, $data_template);
            $dt_cotacao['parceiro_id'] = $parceiro_id;
            $dt_cotacao['usuario_venda_id'] = 0;
            $dt_cotacao['cotacao_status_id'] = 1;
            $dt_cotacao['alteracao_usuario_id'] = $this->session->userdata('usuario_id');
            $dt_cotacao['produto_parceiro_id'] = $produto_parceiro_id;
            $dt_cotacao["usuario_cotacao_id"] = issetor($cotacao["usuario_cotacao_id"], $this->session->userdata('usuario_id'));

            if( isset( $data_cotacao["data_inicio_vigencia"] ) ) {
                $dt_cotacao["data_inicio_vigencia"] = $data_cotacao["data_inicio_vigencia"];
            }

            if( isset( $data_cotacao["data_fim_vigencia"] ) ) {
                $dt_cotacao["data_fim_vigencia"] = $data_cotacao["data_fim_vigencia"];
            }

            $cotacao_id = $this->cotacao->insert($dt_cotacao, TRUE);
            $data_cotacao['cotacao_id'] = $cotacao_id;
            $cotacao_equipamento_id = $this->insert($data_cotacao, TRUE);
        }

        if ( !empty($coberturas_adicionais) )
        {
            foreach ($coberturas_adicionais as $key => $value) {
                $valsCobAdd = explode(';', $value);
                $cobertura_adicional[] = $valsCobAdd[0];
                $cobertura_adicional_valor[] = $valsCobAdd[1];
            }
        } else {
          $cobertura_adicional = (!empty($carrossel['cobertura_adicional'])) ? explode(';', $carrossel['cobertura_adicional']) : array();
          $cobertura_adicional_valor = (!empty($carrossel['cobertura_adicional_valor'])) ? explode(';', $carrossel['cobertura_adicional_valor']) : array();
        }

        if($cobertura_adicional){
            $this->cotacao_equipamento_cobertura->delete_by('cotacao_equipamento_id', $cotacao_equipamento_id);
            foreach ($cobertura_adicional as $index => $item) {

                if(empty($item)) continue;

                $dados_cobertura_adicional = array();
                $dados_cobertura_adicional['cotacao_equipamento_id'] = $cotacao_equipamento_id;
                $dados_cobertura_adicional['cobertura_plano_id'] = $item;
                $dados_cobertura_adicional['valor'] = $cobertura_adicional_valor[$index];
                $this->cotacao_equipamento_cobertura->insert($dados_cobertura_adicional, TRUE);

            }
        }

        return $cotacao_id;
    }

  /**
     * Verifica se possui desconto e o mesmo foi aprovado
     * @param $cotacao_id
     */
  public function verifica_possui_desconto($cotacao_id)
  {
    $cotacao = $this->filterByCotacao($cotacao_id)->get_all();
    if($cotacao && $cotacao[0]['desconto_condicional'] > 0){
      return true;
    }else{
      return false;
    }

  }

  public function verifica_tempo_limite_de_uso($cotacao_id)
  {
    $cotacao = $this->filterByCotacao($cotacao_id)->get_all();
    
    if($cotacao) {
      $cotacao = $cotacao[0];
      if ($cotacao['produto_parceiro_plano_id'] > 0){
        $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
        return $this->produto_parceiro_plano->verifica_tempo_limite_de_uso($cotacao['produto_parceiro_id'], $cotacao['produto_parceiro_plano_id'], $cotacao['nota_fiscal_data'], $cotacao['data_adesao']);
      }
    }

    return null;
  }

  /**
     * Verifica se possui desconto foi aprovado
     * @param $cotacao_id
     */
  public function verifica_desconto_aprovado($cotacao_id)
  {
    $cotacao = $this->get($cotacao_id);
    if($cotacao && $cotacao['desconto_cond_aprovado'] > 0){
      return true;
    }else{
      return false;
    }

  }

  /**
     * Filtro Pelo número da cotação
     * @param $cotacao_id
     * @return $this
     */
  function filterByCotacao($cotacao_id){
    $this->_database->where("cotacao_equipamento.cotacao_id", $cotacao_id);
    $this->_database->where("cotacao_equipamento.deletado", 0);
    return $this;
  }

  /**
     * Busca o Valor Total da compra
     * @param $cotacao_id
     * @return int
     */
  function getValorTotal($cotacao_id){
    $result = $this->filterByCotacao($cotacao_id)
      ->get_all();
      
    $valor = 0;
    foreach ($result as $item) {
      $valor += $item['premio_liquido_total'];
    }
    return $valor;
  }

}











