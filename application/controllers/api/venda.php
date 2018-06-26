<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Venda
 */
class Venda extends Api_Controller
{

  const PRECO_TIPO_TABELA = 1;
  const PRECO_TIPO_COBERTURA = 2;
  const PRECO_TIPO_VALOR_SEGURADO = 3;
  const PRECO_POR_EQUIPAMENTO = 5;

  const TIPO_CALCULO_NET = 1;
  const TIPO_CALCULO_BRUTO = 2;


  // const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
  // const FORMA_PAGAMENTO_FATURADO = 3;
  // const FORMA_PAGAMENTO_CARTAO_DEBITO = 6;
  // const FORMA_PAGAMENTO_BOLETO = 5;

  const FORMA_PAGAMENTO_CARTAO_CREDITO = 1;
  const FORMA_PAGAMENTO_FATURADO = 9;
  const FORMA_PAGAMENTO_CARTAO_DEBITO = 8;
  const FORMA_PAGAMENTO_BOLETO = 9;
  const FORMA_PAGAMENTO_TRANSF_BRADESCO = 2;
  const FORMA_PAGAMENTO_TRANSF_BB = 7;


  /**
     * Venda constructor.
     */
  public function __construct()
  {
    parent::__construct();

    $this->load->model('produto_parceiro_model', 'current_model');
    error_reporting(E_ERROR|E_PARSE);
  }

  /**
     * Busca liberação de acesso ao determinado usuário
     */
  public function busca_liberacao()
  {
    //Cria resposta
    $response = new Response();


    $this->load->model('usuario_webservice_model', 'webservice');


    $webservice = $this->webservice->get_by(array(
      'usuario_id' => $this->usuario['usuario_id']
    )
                                           );


    if($webservice){

      $dados_webservice = array();
      $dados_webservice['validade'] = date('Y-m-d H:i:s');
      $this->webservice->update($webservice['usuario_webservice_id'], $dados_webservice, TRUE);

      $response->setDados(array(
        'api_key' => $webservice['api_key'],
        'validade' => app_date_mysql_to_mask($webservice['validade']),

      ));
      $response->setStatus(true);

    }else{
      $dados_webservice = array();
      $dados_webservice['usuario_id'] = $this->usuario['usuario_id'];
      $dados_webservice['validade'] = date('Y-m-d H:i:s');

      $usuario_webservice_id = $this->webservice->insert($dados_webservice, TRUE);

      $webservice = $this->webservice->get($usuario_webservice_id);
      $response->setDados(array(
        'api_key' => $webservice['api_key'],
        'validade' => app_date_mysql_to_mask($webservice['validade']),

      ));
      $response->setStatus(true);

    }


    //Output
    $this->output
      ->set_content_type('application/json')
      ->set_output($response->getJSON());
  }


  /**
     * Busca produtos
     */
  public function busca_produtos()
  {
    //Cria resposta
    $response = new Response();
    
    if( $this->input->post("produto_id" ) != "" ) {
      $produto_id = $this->input->post("produto_id" );
      $produtos = $this->current_model->get_produtos_venda_admin($this->parceiro['parceiro_id'], $produto_id );
    } else {
      $produtos = $this->current_model->get_produtos_venda_admin($this->parceiro['parceiro_id']);
    }

    //Busca dados
    $relacionamento = $this->current_model->get_produtos_venda_admin_parceiros($this->parceiro['parceiro_id']);
    error_log( "Produto: $produto_id: " . print_r( $produtos, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

    //Se possuir dados
    if($produtos || $relacionamento)
    {
      $response->setDados(array_merge($produtos, $relacionamento));
      $response->setStatus(true);
    }
    else //Caso contrário
    {
      $response->setMensagem("Não possuem produtos para este parceiro.");
    }
    //Output
    $this->output
      ->set_content_type('application/json')
      ->set_output($response->getJSON());
  }

  /**
     * Busca planos com base no produto
     * @param null $id_produto
     */
  public function busca_planos($produto_parceiro_id = null)
  {
    //Cria resposta
    $response = new Response();

    //Carrega models
    $this->load->model("produto_parceiro_plano_model", "produto_parceiro_plano");
    $this->load->model("produto_parceiro_plano_destino_model", "produto_parceiro_plano_destino");
    $this->load->model("produto_parceiro_plano_origem_model", "produto_parceiro_plano_origem");

    if(!$produto_parceiro_id)
      $produto_parceiro_id = $this->input->post("produto_parceiro_id");

    if($produto_parceiro_id)
    {
      $planos = $this->produto_parceiro_plano->coreSelectPlanosProdutoParceiro($produto_parceiro_id)
        ->get_all_select();

      $i = 0; foreach ($planos as $plano)
      {
        $planos[$i]['origens'] = $this->produto_parceiro_plano_origem
          ->coreSelectLocalidadeProdutoParceiro($plano['produto_parceiro_plano_id'])
          ->get_all_select();

        $planos[$i]['destinos'] = $this->produto_parceiro_plano_destino
          ->coreSelectLocalidadeProdutoParceiro($plano['produto_parceiro_plano_id'])
          ->get_all_select();

        $i++;
      }

      $response->setStatus(true);
      $response->setDados($planos);
    }
    else
    {
      $response->setMensagem("O ID do Produto / Parceiro é necessário.");
    }

    //Output
    $this->output
      ->set_content_type('application/json')
      ->set_output($response->getJSON());
  }

  public function busca_motivo_viagem(){

    //Cria resposta
    $response = new Response();

    //Carrega models
    $this->load->model('seguro_viagem_motivo_model', 'motivo');

    $motivo = $this->motivo->coreSelectMotivoViagem()->get_all_select();

    $response->setStatus(true);
    $response->setDados($motivo);

    //Output
    $this->output
      ->set_content_type('application/json')
      ->set_output($response->getJSON());
  }


  public function busca_campos($produto_parceiro_id = null){

    //Cria resposta
    $response = new Response();

    $this->load->model("produto_parceiro_campo_model", "produto_parceiro_campo");
    $this->load->model("campo_tipo_model", "campo_tipo");

    if(!$produto_parceiro_id)
      $produto_parceiro_id = $this->input->post("produto_parceiro_id");


    if($produto_parceiro_id) {

      $result = array();
      $campos_tipo = $this->campo_tipo->coreSelecCampoTipo()->get_all_select();


      $i = 0;
      foreach ($campos_tipo as $index => $item) {

        $campos = $this->produto_parceiro_campo
          ->coreSelecCampoProdutoParceiro($produto_parceiro_id, $item['campo_tipo_id'])
          ->get_all_select();
        if($campos){
          $result[$i] = $item;
          $result[$i]['campos'] = $campos;
          $i++;

        }

      }

      $response->setStatus(true);
      $response->setDados($result);
    } else {
      $response->setMensagem("O ID do Produto / Parceiro é necessário.");
    }

    //Output
    $this->output
      ->set_content_type('application/json')
      ->set_output($response->getJSON());
  }


  public function inserir_cotacao($produto_parceiro_id = null){

    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('cotacao_generico_model', 'cotacao_generico');
    $this->load->model('produto_parceiro_campo_model', 'produto_parceiro_campo');


    $response = new Response();

    $result = array();
    if(!$produto_parceiro_id){
      $produto_parceiro_id = $this->input->post("produto_parceiro_id");
    }else{
      $response->setStatus(false);
      $response->setError(['campo produto_parceiro_id é obrigatório']);
      $response->setDados($result);        
    }

    $cotacao_id = $this->input->post("cotacao_id");

    $produto = $this->current_model->with_produto()->get($produto_parceiro_id);


    $campos = $this->produto_parceiro_campo->with_campo()
      ->with_campo_tipo()
      ->filter_by_produto_parceiro($produto_parceiro_id)
      ->filter_by_campo_tipo_slug('cotacao')
      ->order_by("ordem", "asc")
      ->get_all();

    $validacao = array();
    foreach ($campos as $campo) {

      $validacao[] = array(
        'field' => "{$campo['campo_nome_banco']}",
        'label' => "{$campo['campo_nome']}",
        'rules' => $campo['validacoes'],
        'groups' => 'cotacao'
      );
    }

    $this->cotacao->setValidate($validacao);
    
    //Verifica válido form
    if ($this->cotacao->validate_form('cotacao'))
    {
      $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $_POST);
      $cotacao_id = (int)$this->input->post('cotacao_id');

      if($produto['produto_slug'] == 'equipamento'){
        $cotacao_id = $this->cotacao_equipamento->insert_update($produto_parceiro_id, $cotacao_id);
      }elseif($produto['produto_slug'] == 'generico'){
        $cotacao_id = $this->cotacao_generico->insert_update($produto_parceiro_id, $cotacao_id);
      }


      $result['cotacao_id'] = $cotacao_id;
      
      

      $response->setStatus(true);
      $response->setDados($result);

      /*
            if($cotacao_id > 0)
            {
                $this->update_cotacao_formulario($produto_parceiro_id, $cotacao_id);
                redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2/{$cotacao_id}");
            }else{
                //adiciona cotação
                $cotacao_id = $this->insert_cotacao_formulario($produto_parceiro_id);
                $this->update_cotacao_formulario($produto_parceiro_id, $cotacao_id);
                redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2/{$cotacao_id}");
            }*/
    }else{

      $erros = $this->cotacao->error_array();
      $response->setStatus(false);
      $response->setError($erros);
      $response->setDados($result);

    }


    //Output
    $this->output
      ->set_content_type('application/json')
      ->set_output($response->getJSON());



  }

  public function editar_cotacao($produto_parceiro_id = null){

    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('cotacao_generico_model', 'cotacao_generico');
    $this->load->model('produto_parceiro_campo_model', 'produto_parceiro_campo');


    $response = new Response();

    $result = array();
    if(!$produto_parceiro_id)
      $produto_parceiro_id = $this->input->post("produto_parceiro_id");


    $cotacao_id = $this->input->post("cotacao_id");

    $produto = $this->current_model->with_produto()->get($produto_parceiro_id);


    $campos = $this->produto_parceiro_campo->with_campo()
      ->with_campo_tipo()
      ->filter_by_produto_parceiro($produto_parceiro_id)
      ->filter_by_campo_tipo_slug('cotacao')
      ->order_by("ordem", "asc")
      ->get_all();

    $validacao = array();
    foreach ($campos as $campo) {

      $validacao[] = array(
        'field' => "{$campo['campo_nome_banco']}",
        'label' => "{$campo['campo_nome']}",
        'rules' => $campo['validacoes'],
        'groups' => 'cotacao'
      );
    }

    $this->cotacao->setValidate($validacao);


    //Verifica válido form
    if ($this->cotacao->validate_form('cotacao'))
    {
      $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $_POST);
      $cotacao_id = (int)$this->input->post('cotacao_id');

      if($produto['produto_slug'] == 'equipamento'){

        $cotacao_id = $this->cotacao_equipamento->insert_update($produto_parceiro_id, $cotacao_id);
      }elseif($produto['produto_slug'] == 'generico'){
        $cotacao_id = $this->cotacao_generico->insert_update($produto_parceiro_id, $cotacao_id);
      }


      $result['cotacao_id'] = $cotacao_id;

      $response->setStatus(true);
      $response->setDados($result);

      /*
            if($cotacao_id > 0)
            {
                $this->update_cotacao_formulario($produto_parceiro_id, $cotacao_id);
                redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2/{$cotacao_id}");
            }else{
                //adiciona cotação
                $cotacao_id = $this->insert_cotacao_formulario($produto_parceiro_id);
                $this->update_cotacao_formulario($produto_parceiro_id, $cotacao_id);
                redirect("{$this->controller_uri}/seguro_viagem/{$produto_parceiro_id}/2/{$cotacao_id}");
            }*/
    }else{

      $erros = array();
      foreach ($campos as $index => $campo) {
        $error = form_error($campo['campo_nome_banco']);
        if(!empty($error)){
          $campo['error'] = $error;
          $erros[] = $campo;
        }
      }


      $erros = $this->cotacao->error_array();
      $response->setStatus(false);
      $response->setError($erros);
      $response->setDados($result);

    }


    //Output
    $this->output
      ->set_content_type('application/json')
      ->set_output($response->getJSON());



  }


  public function calculo_cotacao($produto_parceiro_id = null, $cotacao_id = null){

    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('cotacao_generico_model', 'cotacao_generico');
    $this->load->model('produto_parceiro_campo_model', 'produto_parceiro_campo');


    $response = new Response();

    $result = array();
    if(!$produto_parceiro_id)
      $produto_parceiro_id = $this->input->post("produto_parceiro_id");

    if(!$cotacao_id)
      $cotacao_id = $this->input->post("cotacao_id");

    $produto = $this->current_model->with_produto()->get($produto_parceiro_id);

    $params['cotacao_id'] = $cotacao_id;
    $params['produto_parceiro_id'] = $produto_parceiro_id;
    $params['parceiro_id'] = $produto['parceiro_id'];
    $params['equipamento_marca_id'] = $this->input->post('equipamento_marca_id');
    $params['equipamento_categoria_id'] = $this->input->post('equipamento_categoria_id');
    $params['quantidade'] = $this->input->post('quantidade');
    $params['coberturas'] = $this->input->post('coberturas');
    $params['repasse_comissao'] = $this->input->post('repasse_comissao');
    $params['desconto_condicional'] = $this->input->post('desconto_condicional');


    if($produto['produto_slug'] == 'equipamento'){
      error_log( "CALCULO_COTACAO (params): " . print_r( $params, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
      $result = $this->calculo_cotacao_equipamento($params);
    }elseif($produto['produto_slug'] == 'generico'){
      $result = $this->calculo_cotacao_generico($params);
    }





    if($result){
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($result));

    }else{
      //$response->setStatus(false);
      //$response->setDados($result);



      //Output
      $this->output
        ->set_content_type('application/json')
        ->set_output($response->getJSON());
    }


  }

  public function calculo_cotacao_equipamento($params =array())
  {
    $this->load->model('produto_parceiro_campo_model', 'campo');
    $this->load->model('pedido_model', 'pedido');
    $this->load->model('produto_parceiro_plano_model', 'plano');
    $this->load->model('cobertura_plano_model', 'plano_cobertura');
    $this->load->model('cobertura_model', 'cobertura');
    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('produto_parceiro_desconto_model', 'desconto');
    $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
    $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
    $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');


    $sucess = TRUE;
    $messagem = '';


    //print_r($_POST);

    $produto_parceiro_id = issetor($params['produto_parceiro_id'], 0);
    $parceiro_id = issetor($params['parceiro_id'], 0);
    $equipamento_marca_id = issetor($params['equipamento_marca_id'], 0);
    $equipamento_categoria_id = issetor($params['equipamento_categoria_id'], 0);
    $quantidade = issetor($params['quantidade'], 0);

    $coberturas_adicionais = issetor($params['coberturas'], array());

    $repasse_comissao = app_unformat_percent(issetor($params['repasse_comissao'], 0));
    $desconto_condicional= app_unformat_percent(issetor($params['desconto_condicional'], 0));
    $desconto = $this->desconto->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

    $desconto_upgrade = 0;
    $cotacao_id = issetor($params['cotacao_id'], 0);

    if($cotacao_id) {
      $cotacao = $this->cotacao->get($cotacao_id);




      if(($cotacao) && ((int)$cotacao['cotacao_upgrade_id']) > 0){

        $pedido_antigo = $this->pedido->get_by(array('cotacao_id' => $cotacao['cotacao_upgrade_id']));
        $desconto_upgrade = $pedido_antigo['valor_total'];

      }
    }


    $cotacao = $this->cotacao->with_cotacao_equipamento()->filterByID($cotacao_id)->get_all(0,0,false);
    $cotacao = $cotacao[0];


    $row =  $this->current_model->get_by_id($produto_parceiro_id);

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
    if($row['parceiro_id'] != $parceiro_id){


      $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $parceiro_id);

      $configuracao['repasse_comissao'] =  $rel['repasse_comissao'];
      $configuracao['repasse_maximo'] = $rel['repasse_maximo'];
      $configuracao['comissao'] = $rel['comissao'];


      //buscar o markup
      $markup = $this->relacionamento->get_comissao_markup($produto_parceiro_id, $parceiro_id);


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

    $comissao_corretor = ($configuracao['comissao'] - $repasse_comissao);
    
    error_log( "CALCULO_COTACAO_EQUIPAMENTO (equipamento_categoria_id): $equipamento_categoria_id\n", 3, "/var/log/httpd/myapp.log" );

    error_log( "CALCULO_COTACAO_EQUIPAMENTO #0: " . print_r( $params, true ). "\n", 3, "/var/log/httpd/myapp.log" );

    $valores_bruto = $this->getValoresPlano($produto_parceiro_id, $equipamento_marca_id, $equipamento_categoria_id, $cotacao['nota_fiscal_valor'], $quantidade);

    error_log( "CALCULO_COTACAO_EQUIPAMENTO #0: " . print_r( $valores_bruto, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
    $valores_cobertura_adicional_total = array();
    $valores_cobertura_adicional = array();
    if($coberturas_adicionais){

      foreach ($coberturas_adicionais as $coberturas_adicional) {
        $cobertura = explode(';', $coberturas_adicional);
        $vigencia = $this->plano->getInicioFimVigencia($cobertura[0], $cotacao['nota_fiscal_data']);

        $valor = $this->getValorCoberturaAdicional($cobertura[0], $cobertura[1], $vigencia['dias']);
        $valores_cobertura_adicional_total[$cobertura[0]] = (isset($valores_cobertura_adicional_total[$cobertura[0]])) ? ($valores_cobertura_adicional_total[$cobertura[0]] + $valor) : $valor;
        $valores_cobertura_adicional[$cobertura[0]][] = $valor;

      }

    }



    if(!$valores_bruto)
    {
      $result  = array(
        'sucess' => FALSE,
        'produto_parceiro_id' => $produto_parceiro_id,
        'repasse_comissao' => 0,
        'comissao' => 0,
        'desconto_upgrade' => 0,
        'desconto_condicional_valor' => 0,
        'valores_bruto' => 0,
        'valores_cobertura_adicional' => 0,
        'valores_totais_cobertura_adicional' => 0,
        'valores_liquido' => 0,
        'valores_liquido_total' => 0,
        'mensagem' => 'TABELA DE PREÇO NÃO CONFIGURADA',
        'quantidade' => $quantidade,
      );
      return $result;
    }


    if($row['venda_agrupada']){
      $arrPlanos = $this->plano->distinct()
        ->with_produto_parceiro()
        ->with_produto();

      if((isset($cotacao['origem_id'])) && ($cotacao['origem_id'])){
        $arrPlanos->with_origem($cotacao['origem_id']);
      }
      if((isset($cotacao['destino_id'])) && ($cotacao['destino_id'])){
        $arrPlanos->with_destino($cotacao['destino_id']);
      }
      if((isset($cotacao['faixa_salarial_id'])) && ($cotacao['faixa_salarial_id'])){
        $arrPlanos->with_faixa_salarial($cotacao['faixa_salarial_id']);
      }

      $arrPlanos = $arrPlanos
        ->get_many_by(array(
          'produto_parceiro.venda_agrupada' => 1
        ));

    }else {
      $arrPlanos = $this->plano->filter_by_produto_parceiro($produto_parceiro_id);

      if((isset($cotacao['origem_id'])) && ($cotacao['origem_id'])){
        $arrPlanos->with_origem($cotacao['origem_id']);
      }
      if((isset($cotacao['destino_id'])) && ($cotacao['destino_id'])){
        $arrPlanos->with_destino($cotacao['destino_id']);
      }
      if((isset($cotacao['faixa_salarial_id'])) && ($cotacao['faixa_salarial_id'])){
        $arrPlanos->with_faixa_salarial($cotacao['faixa_salarial_id']);
      }

      $arrPlanos = $arrPlanos->get_all();
    }
    $valores_liquido = array();

    //verifica o limite da vigencia dos planos
    $fail_msg = '';

    error_log( "CALCULO_COTACAO_EQUIPAMENTO #1: " . print_r( $configuracao, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
    /**
         * FAZ O CÁLCULO DO PLANO
         */
    $desconto_condicional_valor = 0;
    foreach ($arrPlanos as $plano){
      //precificacao_tipo_id

      switch ((int)$configuracao['calculo_tipo_id'])
      {
        case self::TIPO_CALCULO_NET:
          $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
          $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
          $valor = ($valor/(1-(($markup + $comissao_corretor)/100)));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
          break;
        case self::TIPO_CALCULO_BRUTO:
          $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
          $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
          $valor = ($valor) - (($valor) * (($markup + $comissao_corretor)/100));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
          break;
        default:
          break;


      }

    }


    $regra_preco = $this->regra_preco->with_regra_preco()
      ->filter_by_produto_parceiro($produto_parceiro_id)
      ->get_all();

    $iof = 0;
    $valores_liquido_total = array();
    foreach ($valores_liquido as $key => $value) {
      $valores_liquido_total[$key] = $value;
      foreach ($regra_preco as $regra) {
        $valores_liquido_total[$key] += (($regra['parametros']/100) * $value);
        $valores_liquido_total[$key] -= $desconto_upgrade;
        if( strtoupper($regra["regra_preco_nome"]) == "IOF" ) {
          $iof = $regra['parametros'];
        }
      }
    }

    error_log( "CALCULO_COTACAO_EQUIPAMENTO #1: " . print_r( $regra, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
    
    //Resultado
    $result  = array(
      'sucess' => $sucess,
      'produto_parceiro_id' => $produto_parceiro_id,
      'repasse_comissao' => $repasse_comissao,
      'comissao' => $comissao_corretor,
      'desconto_upgrade' => $desconto_upgrade,
      'desconto_condicional_valor' => $desconto_condicional_valor,
      'valores_bruto' => $valores_bruto,
      'valores_cobertura_adicional' => $valores_cobertura_adicional,
      'valores_totais_cobertura_adicional' => $valores_cobertura_adicional_total,
      'valores_liquido' => $valores_liquido,
      'valores_liquido_total' => $valores_liquido_total,
      'mensagem' => $messagem
    );

    //Salva cotação

    if($cotacao_id) {
      $cotacao_salva = $this->cotacao->with_cotacao_equipamento()
        ->filterByID($cotacao_id)
        ->get_all();

      $cotacao_salva = $cotacao_salva[0];
      $cotacao_eqp = array();
      $cotacao_eqp['repasse_comissao'] = $repasse_comissao;
      $cotacao_eqp['comissao_corretor'] = $comissao_corretor;
      $cotacao_eqp['desconto_condicional'] = $desconto_condicional;
      $cotacao_eqp['desconto_condicional_valor'] = $desconto_condicional_valor;
      $cotacao_eqp['premio_liquido'] = $valores_liquido[$plano['produto_parceiro_plano_id']];
      $cotacao_eqp['premio_liquido_total'] = $valores_liquido_total[$plano['produto_parceiro_plano_id']];
      $cotacao_eqp['iof'] = $iof;
      
      error_log( "CALCULO_COTACAO_EQUIPAMENTO #2: " . print_r( $cotacao_eqp, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
      
      $this->cotacao_equipamento->update($cotacao_salva['cotacao_equipamento_id'], $cotacao_eqp, TRUE);
    }

    return $result;

  }

  private function getValoresPlano($produto_parceiro_id, $equipamento_marca_id, $equipamento_categora_id, $valor_nota, $quantidade = 1){

    $this->load->model('produto_parceiro_plano_model', 'plano');
    $this->load->model('moeda_model', 'moeda');
    $this->load->model('moeda_cambio_model', 'moeda_cambio');

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
    error_log( "PRECO_POR_EQUIPAMENTO (equipamento_categora_id): ". print_r( $equipamento_categora_id, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
    error_log( "PRECO_POR_EQUIPAMENTO (arrPlanos): ". print_r( $arrPlanos, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
    foreach ($arrPlanos as $plano){
      switch ((int)$plano['precificacao_tipo_id'])
      {
        case self::PRECO_TIPO_TABELA:

          $calculo = $this->getValorTabelaFixa($plano['produto_parceiro_plano_id'], $equipamento_categora_id, $equipamento_marca_id, $valor_nota) *$quantidade;

          if($calculo)
            $valores[$plano['produto_parceiro_plano_id']] = $calculo;
          else
            return null;

          if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
            $valores[$plano['produto_parceiro_plano_id']] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$plano['produto_parceiro_plano_id']]);
          }
          break;
        case self::PRECO_TIPO_COBERTURA:
          break;
        case self::PRECO_TIPO_VALOR_SEGURADO:
          $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
          $this->load->model('equipamento_model', 'equipamento');
          $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
          error_log( "PRECO_TIPO_VALOR_SEGURADO: ". print_r( $produto_parceiro_plano_id, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
          $valor = $this->produto_parceiro_plano_precificacao_itens
            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
            ->filter_by_tipo_equipamento("TODOS")
            ->get_all();
          $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );
          break;
        case self::PRECO_POR_EQUIPAMENTO;
          $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
          $this->load->model('equipamento_model', 'equipamento');
          $produto_parceiro_plano_id = $plano["produto_parceiro_plano_id"];
          error_log( "PRECO_POR_EQUIPAMENTO: ". print_r( $produto_parceiro_plano_id, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
          $valor = $this->produto_parceiro_plano_precificacao_itens
            ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
            ->filter_by_faixa( $valor_nota )
            ->filter_by_tipo_equipamento("CATEGORIA")
            ->filter_by_equipamento($equipamento_categora_id)
            ->get_all();
          
          
          $valores[$produto_parceiro_plano_id] = floatval( $valor_nota ) * ( floatval( $valor[0]["valor"] ) / 100 );
          
          error_log( "PRECO_POR_EQUIPAMENTO (VALOR): ". print_r( $valor, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
          error_log( "PRECO_POR_EQUIPAMENTO: $valor_nota\n", 3, "/var/log/httpd/myapp.log" );
          break;
        default:
          break;


      }
    }
    error_log( "GENÉRICO (VALORES): ". print_r( $valores, true ) . "\n", 3, "/var/log/httpd/myapp.log" );
    return $valores;


  }

  private function getValorCoberturaAdicional($produto_parceiro_plano_id, $cobertura_plano_id, $qntDias){
    $this->load->model('cobertura_plano_model', 'cobertura_plano');

    $cobertura = $this->cobertura_plano->get_by(array(
      'produto_parceiro_plano_id' => $produto_parceiro_plano_id,
      'cobertura_plano_id' => $cobertura_plano_id,

    ));


    if($cobertura){
      return (app_calculo_porcentagem($cobertura['porcentagem'],$cobertura['preco'])*$qntDias);
    }else{
      return 0;
    }

  }

  /**
     * Busca o Valor da tabela FIXA
     * @param $produto_parceiro_plano_id
     * @param $equipamento_nome
     * @return mixed|null
     */

  private function getValorTabelaFixa($produto_parceiro_plano_id, $equipamento_categoria_id, $equipamento_marca_id, $valor_nota){


    $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'produto_parceiro_plano_precificacao_itens');
    $this->load->model('equipamento_model', 'equipamento');


    $valor = $this->produto_parceiro_plano_precificacao_itens
      ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
      ->filter_by_tipo_equipamento('TODOS')
      ->get_all();

error_log( "Valor nota: $valor_nota\nTipo cobranca: " . print_r( $valor, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

    if(count($valor) > 0)
    {
      $valor = $valor[0];
      if($valor['cobranca'] == 'PORCENTAGEM')
      {
        return app_calculo_porcentagem($valor['valor'], $valor_nota);
      }
      else
      {
        return $valor['valor'];
      }

    }else{


      //BUSCA POR CATEGORIA / LINHA
      $valor = $this->produto_parceiro_plano_precificacao_itens
        ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
        ->filter_by_tipo_equipamento('TODOS')
        ->filter_by_equipamento($equipamento_categoria_id)
        ->get_all();

error_log( "Valor nota: $valor_nota\nTipo cobranca: " . print_r( $valor, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

      if(count($valor) > 0) {
        $valor = $valor[0];
        if($valor['cobranca'] == 'PORCENTAGEM'){
          return app_calculo_porcentagem($valor['valor'], $valor_nota);
        }else{
          return $valor['valor'];
        }
      }
      /*
                        //BUSCA POR SUB CATEGORIA CATEGORIA / LINHA
                        $valor = $this->produto_parceiro_plano_precificacao_itens
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
      $valor = $this->produto_parceiro_plano_precificacao_itens
        ->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
        ->filter_by_tipo_equipamento('MARCA')
        ->filter_by_equipamento($equipamento_marca_id)
        ->get_all();

error_log( "Valor nota: $valor_nota\nTipo cobranca: " . print_r( $valor, true ) . "\n", 3, "/var/log/httpd/myapp.log" );

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


  public function calculo_cotacao_generico($params = array())
  {
    $this->load->model('produto_parceiro_campo_model', 'campo');
    $this->load->model('pedido_model', 'pedido');
    $this->load->model('produto_parceiro_plano_model', 'plano');
    $this->load->model('cobertura_plano_model', 'plano_cobertura');
    $this->load->model('cobertura_model', 'cobertura');
    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_generico_model', 'cotacao_generico');
    $this->load->model('produto_parceiro_desconto_model', 'desconto');
    $this->load->model('produto_parceiro_configuracao_model', 'configuracao');
    $this->load->model('produto_parceiro_regra_preco_model', 'regra_preco');
    $this->load->model('parceiro_relacionamento_produto_model', 'relacionamento');
    $this->load->model('servico_produto_model', 'servico_produto');


    $sucess = TRUE;
    $messagem = '';


    //print_r($_POST);


    $produto_parceiro_id = issetor($params['produto_parceiro_id'], 0);
    $parceiro_id = issetor($params['parceiro_id'], 0);
    $equipamento_marca_id = issetor($params['equipamento_marca_id'], 0);
    $equipamento_categoria_id = issetor($params['equipamento_categoria_id'], 0);
    $quantidade = issetor($params['quantidade'], 0);

    $coberturas_adicionais = issetor($params['coberturas'], array());

    $repasse_comissao = app_unformat_percent(issetor($params['repasse_comissao'], 0));
    $desconto_condicional= app_unformat_percent(issetor($params['desconto_condicional'], 0));
    $desconto = $this->desconto->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

    $desconto_upgrade = 0;
    $cotacao_id = issetor($params['cotacao_id'], 0);




    if($cotacao_id) {
      $cotacao = $this->cotacao->get($cotacao_id);




      if(($cotacao) && ((int)$cotacao['cotacao_upgrade_id']) > 0){

        $pedido_antigo = $this->pedido->get_by(array('cotacao_id' => $cotacao['cotacao_upgrade_id']));
        $desconto_upgrade = $pedido_antigo['valor_total'];

      }
    }


    $cotacao = $this->cotacao->with_cotacao_generico()->filterByID($cotacao_id)->get_all();
    $cotacao = $cotacao[0];


    $row =  $this->current_model->get_by_id($produto_parceiro_id);

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
    if($row['parceiro_id'] != $parceiro_id){


      $rel = $this->relacionamento->get_comissao($produto_parceiro_id, $parceiro_id);

      $configuracao['repasse_comissao'] =  $rel['repasse_comissao'];
      $configuracao['repasse_maximo'] = $rel['repasse_maximo'];
      $configuracao['comissao'] = $rel['comissao'];


      //buscar o markup
      $markup = $this->relacionamento->get_comissao_markup($produto_parceiro_id, $parceiro_id);



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

    $comissao_corretor = ($configuracao['comissao'] - $repasse_comissao);


    $servico_produto_id = $cotacao['servico_produto_id'] ? $cotacao['servico_produto_id'] : 0;
    $servico_produto = $this->servico_produto->get($servico_produto_id);


    if($servico_produto && $quantidade < $servico_produto['quantidade_minima'])
    {
      $quantidade = $servico_produto['quantidade_minima'];
    }

    $valores_bruto = $this->getValoresPlanoGenerico($produto_parceiro_id, $quantidade, $servico_produto_id);

    $valores_cobertura_adicional_total = array();
    $valores_cobertura_adicional = array();
    if($coberturas_adicionais){

      foreach ($coberturas_adicionais as $coberturas_adicional) {
        $cobertura = explode(';', $coberturas_adicional);
        $vigencia = $this->plano->getInicioFimVigencia($cobertura[0], date('Y-m-d'));

        $valor = $this->getValorCoberturaAdicionalGenerico($cobertura[0], $cobertura[1], $vigencia['dias']);
        $valores_cobertura_adicional_total[$cobertura[0]] = (isset($valores_cobertura_adicional_total[$cobertura[0]])) ? ($valores_cobertura_adicional_total[$cobertura[0]] + $valor) : $valor;
        $valores_cobertura_adicional[$cobertura[0]][] = $valor;

      }

    }



    if(!$valores_bruto)
    {
      $result = array(
        'sucess' => FALSE,
        'produto_parceiro_id' => $produto_parceiro_id,
        'repasse_comissao' => 0,
        'comissao' => 0,
        'desconto_upgrade' => 0,
        'desconto_condicional_valor' => 0,
        'valores_bruto' => 0,
        'valores_cobertura_adicional' => 0,
        'valores_totais_cobertura_adicional' => 0,
        'valores_liquido' => 0,
        'valores_liquido_total' => 0,
        'mensagem' => 'TABELA DE PREÇO NÃO CONFIGURADA',
        'quantidade' => $quantidade,
      );
      return $result;
    }


    if($row['venda_agrupada']){
      $arrPlanos = $this->plano->distinct()
        ->order_by('produto_parceiro_plano.ordem', 'asc')
        ->with_produto_parceiro()
        ->with_produto()
        ->get_many_by(array(
          'produto_parceiro.venda_agrupada' => 1
        ));

    }else {
      $arrPlanos = $this->plano->order_by('produto_parceiro_plano.ordem', 'asc')->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
    }
    $valores_liquido = array();

    //verifica o limite da vigencia dos planos
    $fail_msg = '';

    /**
         * FAZ O CÁLCULO DO PLANO
         */
    $desconto_condicional_valor = 0;
    foreach ($arrPlanos as $plano){
      //precificacao_tipo_id

      switch ((int)$configuracao['calculo_tipo_id'])
      {
        case self::TIPO_CALCULO_NET:
          $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
          $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
          $valor = ($valor/(1-(($markup + $comissao_corretor)/100)));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
          break;
        case self::TIPO_CALCULO_BRUTO:
          $valor = $valores_bruto[$plano['produto_parceiro_plano_id']];
          $valor += (isset($valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']])) ? $valores_cobertura_adicional_total[$plano['produto_parceiro_plano_id']] : 0;
          $valor = ($valor) - (($valor) * (($markup + $comissao_corretor)/100));
          $desconto_condicional_valor = ($desconto_condicional/100) * $valor;
          $valor -= $desconto_condicional_valor;
          $valores_liquido[$plano['produto_parceiro_plano_id']] = $valor;
          break;
        default:
          break;


      }

    }


    $regra_preco = $this->regra_preco->with_regra_preco()
      ->filter_by_produto_parceiro($produto_parceiro_id)
      ->get_all();

    $valores_liquido_total = array();
    foreach ($valores_liquido as $key => $value) {
      $valores_liquido_total[$key] = $value;
      foreach ($regra_preco as $regra) {
        $valores_liquido_total[$key] += (($regra['parametros']/100) * $value);
        $valores_liquido_total[$key] -= $desconto_upgrade;
      }
    }

    //Resultado
    $result  = array(
      'sucess' => $sucess,
      'produto_parceiro_id' => $produto_parceiro_id,
      'repasse_comissao' => $repasse_comissao,
      'comissao' => $comissao_corretor,
      'desconto_upgrade' => $desconto_upgrade,
      'desconto_condicional_valor' => $desconto_condicional_valor,
      'valores_bruto' => $valores_bruto,
      'valores_cobertura_adicional' => $valores_cobertura_adicional,
      'valores_totais_cobertura_adicional' => $valores_cobertura_adicional_total,
      'valores_liquido' => $valores_liquido,
      'valores_liquido_total' => $valores_liquido_total,
      'mensagem' => $messagem,
      'quantidade' => $quantidade,
    );

    //Salva cotação

    if($cotacao_id) {
      $cotacao_salva = $this->cotacao->with_cotacao_generico()
        ->filterByID($cotacao_id)
        ->get_all();

      $cotacao_salva = $cotacao_salva[0];
      $data_cotacao = array();
      $data_cotacao['repasse_comissao'] = $repasse_comissao;
      $data_cotacao['comissao_corretor'] = $comissao_corretor;
      $data_cotacao['desconto_condicional'] = $desconto_condicional;
      $data_cotacao['desconto_condicional_valor'] = $desconto_condicional_valor;
      $this->cotacao_generico->update($cotacao_salva['cotacao_generico_id'], $data_cotacao, TRUE);
    }

    //Seta sessão
    // $this->session->set_userdata("cotacao_{$produto_parceiro_id}", $cotacao);

    return $result;
  }

  private function getValoresPlanoGenerico($produto_parceiro_id, $quantidade = 1, $servico_produto_id = 0){

    $this->load->model('produto_parceiro_plano_model', 'produto_parceiro_plano');
    $this->load->model('produto_parceiro_plano_precificacao_servico_model', 'produto_parceiro_plano_precificacao_servico');
    $this->load->model('moeda_model', 'moeda');
    $this->load->model('moeda_cambio_model', 'moeda_cambio');


    $quantidade = ((int)$quantidade <=0) ? 1 : (int)$quantidade;

    $moeda_padrao = $this->moeda->filter_by_moeda_padrao()->get_all();
    $moeda_padrao = $moeda_padrao[0];

    $produto_parceiro =  $this->current_model->get_by_id($produto_parceiro_id);
    if($produto_parceiro['venda_agrupada']) {
      $arrPlanos = $this->produto_parceiro_plano->distinct()
        ->with_produto_parceiro()
        ->order_by('produto_parceiro_plano.ordem', 'asc')
        ->with_produto()
        ->get_many_by(array(
          'produto_parceiro.venda_agrupada' => 1
        ));
    }else{
      $arrPlanos = $this->produto_parceiro_plano->order_by('produto_parceiro_plano.ordem', 'asc')->filter_by_produto_parceiro($produto_parceiro_id)->get_all();
    }
    $valores = array();
    foreach ($arrPlanos as $plano){
      switch ((int)$plano['precificacao_tipo_id'])
      {
        case self::PRECO_TIPO_TABELA:

          $vigencia = $this->produto_parceiro_plano->getInicioFimVigencia($plano['produto_parceiro_plano_id'], date('Y-m-d'));

          $calculo = $this->getValorTabelaFixaGenerico($plano['produto_parceiro_plano_id'], $vigencia['dias'])*$quantidade;
          if($calculo)
            $valores[$plano['produto_parceiro_plano_id']] = $calculo;
          else
            return null;

          if($moeda_padrao['moeda_id'] != $plano['moeda_id']){
            $valores[$plano['produto_parceiro_plano_id']] = $this->moeda_cambio->getValor($plano['moeda_id'], $valores[$plano['produto_parceiro_plano_id']]);
          }
          break;
        case self::PRECO_TIPO_COBERTURA:
          break;
        case self::PRECO_TIPO_VALOR_SEGURADO:
          break;
        case self::PRECO_TIPO_FIXO_SERVICO:

          $preco = $this->produto_parceiro_plano_precificacao_servico
            ->get_by(array(
              'produto_parceiro_plano_id' => $plano['produto_parceiro_plano_id'],
              'servico_produto_id' => $servico_produto_id,
            ));

          if($preco)
          {
            $valores[$plano['produto_parceiro_plano_id']] = (float) $preco['valor'] * (int) $quantidade;
          }
          else
            return null;

          break;
        default:
          break;


      }
    }


    return $valores;


  }

  private function getValorCoberturaAdicionalGenerico($produto_parceiro_plano_id, $cobertura_plano_id, $qntDias){
    $this->load->model('cobertura_plano_model', 'cobertura_plano');

    $cobertura = $this->cobertura_plano->get_by(array(
      'produto_parceiro_plano_id' => $produto_parceiro_plano_id,
      'cobertura_plano_id' => $cobertura_plano_id,

    ));


    if($cobertura){
      return (app_calculo_porcentagem($cobertura['porcentagem'],$cobertura['preco'])*$qntDias);
    }else{
      return 0;
    }

  }

  private function getValorTabelaFixaGenerico($produto_parceiro_plano_id, $qntDias){
    $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'itens');

    $valor = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
      ->filter_by_intevalo_dias($qntDias)
      ->filter_by_tipo('RANGE')
      ->get_all();

    if(count($valor) > 0){
      return $valor[0]['valor'];
    }else{
      //não achou

      //Verifica se Busca por mês
      if(($qntDias >= 30) && ($qntDias % 30) == 0){
        $valor = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
          ->filter_by_intevalo_dias(floor($qntDias/30), 'MES')
          ->filter_by_tipo('RANGE')
          ->get_all();
        if(count($valor) > 0){
          return $valor[0]['valor'];
        }
      }


      //Verifica se Busca por ANO
      if(($qntDias >= 365) && ($qntDias % 365) == 0){
        $valor = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
          ->filter_by_intevalo_dias(floor($qntDias/365), 'ANO')
          ->filter_by_tipo('RANGE')
          ->get_all();
        if(count($valor) > 0){
          return $valor[0]['valor'];
        }
      }


      $ultimo = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
        ->filter_by_intevalo_menor($qntDias, 'DIA')
        ->order_by('produto_parceiro_plano_precificacao_itens.final', 'DESC')
        ->filter_by_tipo('RANGE')
        ->limit(1)
        ->get_all();

      if(is_array($ultimo) && sizeof($ultimo) > 0)
        $ultimo = $ultimo[0];


      $valor_adicional = $this->itens->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)
        ->filter_by_tipo('ADICIONAL')
        ->limit(1)
        ->get_all();

      if(is_array($valor_adicional) && sizeof($valor_adicional) > 0)
      {
        $valor_adicional = $valor_adicional[0];

        $valor = $ultimo['valor'];
        for ($i = $ultimo['final']; $i < $qntDias; $i++){
          $valor += $valor_adicional['valor'];
        }
        return $valor;
      }
      return null;


    }



  }


  public function contratar_plano($produto_parceiro_id = null, $cotacao_id = null, $produto_parceiro_plano_id = null){


    $response = new Response();

    $result = array();
    if(!$produto_parceiro_id)
      $produto_parceiro_id = $this->input->post("produto_parceiro_id");

    if(!$cotacao_id)
      $cotacao_id = $this->input->post("cotacao_id");

    if(!$produto_parceiro_plano_id)
      $produto_parceiro_plano_id = $this->input->post("produto_parceiro_plano_id");

    $produto = $this->current_model->with_produto()->get($produto_parceiro_id);

    $params['cotacao_id'] = $cotacao_id;
    $params['produto_parceiro_id'] = $produto_parceiro_id;
    $params['parceiro_id'] = $produto['parceiro_id'];
    $params['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
    $params['equipamento_marca_id'] = $this->input->post('equipamento_marca_id');
    $params['equipamento_categoria_id'] = $this->input->post('equipamento_categoria_id');
    $params['quantidade'] = $this->input->post('quantidade');
    $params['coberturas'] = $this->input->post('coberturas');
    $params['repasse_comissao'] = $this->input->post('repasse_comissao');
    $params['desconto_condicional'] = $this->input->post('desconto_condicional');


    if($produto['produto_slug'] == 'equipamento'){



      $result = $this->equipamento_contratar($params);
    }elseif($produto['produto_slug'] == 'generico'){
      //$result = $this->calculo_cotacao_generico($params);
    }





    if($result){
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($result));

    }else{
      //$response->setStatus(false);
      //$response->setDados($result);



      //Output
      $this->output
        ->set_content_type('application/json')
        ->set_output($response->getJSON());
    }


  }

  public function equipamento_contratar($params)
  {
    //Carrega models necessários
    $this->load->model('produto_parceiro_campo_model', 'campo');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('cliente_model', 'cliente');
    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('localidade_estado_model', 'localidade_estado');

    $produto_parceiro_id = issetor($params['produto_parceiro_id'], 0);
    $produto_parceiro_plano_id = issetor($params['produto_parceiro_plano_id'], 0);
    $parceiro_id = issetor($params['parceiro_id'], 0);

    $repasse_comissao = app_unformat_percent(issetor($params['repasse_comissao'], 0));
    $desconto_condicional= app_unformat_percent(issetor($params['desconto_condicional'], 0));

    $cotacao_id = issetor($params['cotacao_id'], 0);

    $result  = array(
      'sucess' => FALSE,
      'mensagem' => 'Contratação não finalizada',
    );


    if($cotacao_id > 0)
    {
      if($this->cotacao->isCotacaoValida($cotacao_id) == FALSE){
        $result  = array(
          'sucess' => FALSE,
          'mensagem' => 'Essa Cotação não é válida',
          'errors' => array(),
        );
        return $result;
      }


      //Verifica se cotação e carrossel foram setados na session
      $this->cotacao_equipamento->insert_update($produto_parceiro_id, $cotacao_id, 3);
    }else{
      $result  = array(
        'sucess' => FALSE,
        'mensagem' => 'Essa Cotação não é válida',
        'errors' => array(),
      );
      return $result;
    }




    $cotacao_salva = $this->cotacao->with_cotacao_equipamento()
      ->filterByID($cotacao_id)
      ->get_all(0,0,false);

    $cotacao_salva = $cotacao_salva[0];



    $validacao = $this->campo->setValidacoesCamposPlano($produto_parceiro_id, 'dados_segurado',"$produto_parceiro_plano_id");

    $this->cotacao->setValidate($validacao);
    if ($this->cotacao->validate_form('dados_segurado')) {

      $dados_cotacao = array();
      $dados_cotacao['step'] = 4;


      $this->campo->setDadosCampos($produto_parceiro_id, 'equipamento', 'dados_segurado', $produto_parceiro_plano_id,  $dados_cotacao);

      $dados_cotacao['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
      $this->cotacao_equipamento->update($cotacao_salva['cotacao_equipamento_id'], $dados_cotacao, TRUE);
      $this->cliente->atualizar($cotacao_salva['cliente_id'], $dados_cotacao);
      $result  = array(
        'sucess' => TRUE,
        'mensagem' => 'COTACÃO FINALIZADA (EFETUE O PAGAMENTO)',
        'errors' => array(),
      );
      return $result;


    }else{

      $erros = $this->cotacao->error_array();
      $result  = array(
        'sucess' => FALSE,
        'mensagem' => 'Essa Cotação não é válida',
        'errors' => $erros,
      );
      return $result;


    }



  }

  public function efetuar_pagamento($produto_parceiro_id = null, $cotacao_id = null, $pedido_id = 0, $tipo_forma_pagamento_id = null)
  {

    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_generico_model', 'cotacao_generico');
    $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('forma_pagamento_tipo_model', 'forma_pagamento_tipo');
    $this->load->model('forma_pagamento_bandeira_model', 'forma_pagamento_bandeira');
    $this->load->model('forma_pagamento_model', 'forma_pagamento');
    $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
    $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
    $this->load->model('pedido_model', 'pedido');


    if(!$produto_parceiro_id)
      $produto_parceiro_id = $this->input->post("produto_parceiro_id");

    if(!$cotacao_id)
      $cotacao_id = $this->input->post("cotacao_id");

    if(!$pedido_id)
      $pedido_id = $this->input->post("pedido_id");


    if(!$tipo_forma_pagamento_id)
      $tipo_forma_pagamento_id = $this->input->post('forma_pagamento_tipo_id');


    //Retorna cotação
    $cotacao = $this->cotacao->get_cotacao_produto($cotacao_id);
    switch ($cotacao['produto_slug']) {
      case 'seguro_viagem':
        $valor_total = $this->cotacao_seguro_viagem->getValorTotal($cotacao_id);
        break;
      case 'equipamento':
        $valor_total = $this->cotacao_equipamento->getValorTotal($cotacao_id);
        break;
      case 'generico':
        $valor_total = $this->cotacao_generico->getValorTotal($cotacao_id);
        break;
    }

    $data = array();
    $data['cotacao_id'] = $cotacao_id;
    $data['pedido_id'] = $pedido_id;
    $data['produto_slug'] = $cotacao['produto_slug'];
    $data['produto_parceiro_configuracao'] = $this->produto_parceiro_configuracao->get_by(array(
      'produto_parceiro_id' => $produto_parceiro_id
    ));
    $data['produto_parceiro_id'] = $produto_parceiro_id;





    $validacao = array();

    /**
         * Cartão de crédito
         */
    if($tipo_forma_pagamento_id == self::FORMA_PAGAMENTO_CARTAO_CREDITO){
      //cartão de crédito
      $validacao[] = array(
        'field' => "numero",
        'label' => "Número do Cartão",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );

      $validacao[] = array(
        'field' => "nome_cartao",
        'label' => "Nome",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );

      $validacao[] = array(
        'field' => "validade",
        'label' => "Validade",
        'rules' => "trim|required|valid_vencimento_cartao" ,
        'groups' => 'pagamento'
      );

      $validacao[] = array(
        'field' => "codigo",
        'label' => "Código",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );
      $validacao[] = array(
        'field' => "bandeira_cartao",
        'label' => "Bandeira",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );

      if(($data['produto_parceiro_configuracao']['pagamento_tipo'] == "RECORRENTE") && ($data['produto_parceiro_configuracao']['pagmaneto_cobranca'] == 'VENCIMENTO_CARTAO '))
      {
        $validacao[] = array(
          'field' => "dia_vencimento",
          'label' => "Dia do vencimento",
          'rules' => "trim|numeric|required" ,
          'groups' => 'pagamento'
        );
      }
    }else if ($tipo_forma_pagamento_id == self::FORMA_PAGAMENTO_CARTAO_DEBITO){
      //cartão de débito

      $validacao[] = array(
        'field' => "numero_debito",
        'label' => "Número do Cartão",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );

      $validacao[] = array(
        'field' => "nome_cartao_debito",
        'label' => "Nome",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );

      $validacao[] = array(
        'field' => "validade_debito",
        'label' => "Validade",
        'rules' => "trim|required|valid_vencimento_cartao" ,
        'groups' => 'pagamento'
      );

      $validacao[] = array(
        'field' => "codigo_debito",
        'label' => "Código",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );
      $validacao[] = array(
        'field' => "bandeira_cartao_debito",
        'label' => "Bandeira",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );

    }else if ($tipo_forma_pagamento_id == Self::FORMA_PAGAMENTO_BOLETO){
      //faturado
      $validacao[] = array(
        'field' => "sacado_nome",
        'label' => "Nome",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );
      $validacao[] = array(
        'field' => "sacado_documento",
        'label' => "CPF",
        'rules' => "trim|required|validate_cpf" ,
        'groups' => 'pagamento'
      );
      $validacao[] = array(
        'field' => "sacado_endereco",
        'label' => "Endereço",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );
      $validacao[] = array(
        'field' => "sacado_endereco_num",
        'label' => "Número",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );
      $validacao[] = array(
        'field' => "sacado_endereco_cep",
        'label' => "CEP",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );
      $validacao[] = array(
        'field' => "sacado_endereco_bairro",
        'label' => "Bairro",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );
      $validacao[] = array(
        'field' => "sacado_endereco_cidade",
        'label' => "Cidade",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );
      $validacao[] = array(
        'field' => "sacado_endereco_uf",
        'label' => "Estado",
        'rules' => "trim|required" ,
        'groups' => 'pagamento'
      );
    }else{ 				// if ($tipo_forma_pagamento_id == Self::FORMA_PAGAMENTO_FATURADO){
      //faturado
      $validacao[] = array();
    }


    //$this->load->library('form_validation');

    //print_r($validacao);exit;
    $this->cotacao->setValidate($validacao);


  error_log( "Validou ($tipo_forma_pagamento_id)\n", 3, "/var/log/httpd/myapp.log" );
    if ($this->cotacao->validate_form('pagamento')) {

      if($pedido_id == 0 || $pedido_id == "" ) {
        error_log( "Validou (" . print_r( $_POST, true ) . ")\n", 3, "/var/log/httpd/myapp.log" );
        $pedido_id = $this->pedido->insertPedido($_POST);

      } else {
        $this->pedido->updatePedido($pedido_id, $_POST);
        // $this->pedido->insDadosPagamento($_POST, $pedido_id);
      }

      //Se for faturamento, muda status para aguardando faturamento
      if($pedido_id && $tipo_forma_pagamento_id == self::FORMA_PAGAMENTO_FATURADO) {
        $status = $this->pedido->mudaStatus($pedido_id, "aguardando_faturamento");
      }

      $result  = array(
        'sucess' => TRUE,
        'mensagem' => 'Aguardando confirmação do pagamento',
        'errors' => array(),
        'dados' => array('pedido_id' => $pedido_id),
      );


    } else {

      $erros = $this->cotacao->error_array();
      $result  = array(
        'sucess' => FALSE,
        'mensagem' => 'Essa Cotação não é válida',
        'errors' => $erros,
      );

    }

    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($result));



  }


  public function buscar_apolice($view = ''){

    $this->load->model('apolice_model', 'apolice');

    $pedido_id = $this->input->post("pedido_id");

    $apolice = $this->apolice->getApolicePedido($pedido_id);

    if($apolice){




      $result = $this->apolice->certificado($apolice[0]['apolice_id'], $view);


      if($result !== FALSE){
        exit($result);
      }        

    }

  }

  public function forma_pagamento_cotacao($produto_parceiro_id = null, $cotacao_id = null){


    $this->load->model('cotacao_model', 'cotacao');
    $this->load->model('cotacao_generico_model', 'cotacao_generico');
    $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
    $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
    $this->load->model('forma_pagamento_tipo_model', 'forma_pagamento_tipo');
    $this->load->model('forma_pagamento_bandeira_model', 'forma_pagamento_bandeira');
    $this->load->model('forma_pagamento_model', 'forma_pagamento');
    $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
    $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
    $this->load->model('pedido_model', 'pedido');


    if(!$produto_parceiro_id)
      $produto_parceiro_id = $this->input->post("produto_parceiro_id");


    if(!$cotacao_id)
      $cotacao_id = $this->input->post("cotacao_id");


    $response = new Response();

    $cotacao = $this->cotacao->get_cotacao_produto($cotacao_id);
    switch ($cotacao['produto_slug']) {
      case 'seguro_viagem':
        $valor_total = $this->cotacao_seguro_viagem->getValorTotal($cotacao_id);
        break;
      case 'equipamento':
        $valor_total = $this->cotacao_equipamento->getValorTotal($cotacao_id);
        break;
      case 'generico':
        $valor_total = $this->cotacao_generico->getValorTotal($cotacao_id);
        break;
    }


    $forma_pagamento = array();
    $tipo_pagamento = $this->forma_pagamento_tipo->get_all();

    //Para cada tipo de pagamento
    foreach ($tipo_pagamento as $index => $tipo)
    {
      $forma = $this->produto_pagamento->with_forma_pagamento()
        ->filter_by_produto_parceiro($produto_parceiro_id)
        ->filter_by_forma_pagamento_tipo($tipo['forma_pagamento_tipo_id'])
        ->filter_by_ativo()
        ->get_all();

      $bandeiras = $this->forma_pagamento_bandeira
        ->get_many_by(array(
          'forma_pagamento_tipo_id' =>  $tipo['forma_pagamento_tipo_id']
        ));


      if(count($forma) > 0){

        foreach ($forma as $index => $item) {
          $parcelamento = array();
          for($i = 1; $i <= $item['parcelamento_maximo'];$i++){
            if($i <= $item['parcelamento_maximo_sem_juros']) {
              $parcelamento[$i] = "{$i} X ". app_format_currency($valor_total/$i) . " sem juros";
            }else{
              //$valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
              $valor = ($valor_total/(1-($item['juros_parcela']/100)))/$i;
              $parcelamento[$i] = "{$i} X ". app_format_currency($valor) . " com juros (". app_format_currency($item['juros_parcela']) ."%)";
            }
          }
          $forma[$index]['parcelamento'] = $parcelamento;
        }

        $forma_pagamento[] = array('tipo' => $tipo, 'pagamento' => $forma, 'bandeiras' => $bandeiras);
      }

    }


    if($forma_pagamento) {
      $response->setStatus(true);
      $response->setDados($forma_pagamento);
    }else{
      $response->setMensagem('Não existe forma de pagamento configuradas para esse produto');
    }

    //Output
    $this->output
      ->set_content_type('application/json')
      ->set_output($response->getJSON());

  }


  public function buscar_pedido($pedido_id = null){


    if(!$pedido_id)
      $pedido_id = $this->input->post('pedido_id');

    $this->load->model('pedido_model', 'pedido');
    $this->load->model('pedido_status_model', 'pedido_status');

    $pedido = $this->pedido->get($pedido_id);

    $result = array();
    $result['result'] = FALSE;
    $result['pedido_id'] = '';
    $result['pedido_status_id'] = '';
    $result['mensagem'] = '';

    if($pedido) {
      $pedido_status = $this->pedido_status->get($pedido['pedido_status_id']);
      $result['result'] = TRUE;
      $result['pedido_status_id'] = $pedido['pedido_status_id'];
      $result['pedido_status_descricao'] = $pedido_status['nome'];
      $result['pedido_id'] = $pedido['pedido_id'];

    }else{
      $result['mensagem'] = "PEDIDO NÃO ENCONTRADO";
    }


    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($result));
  }

}









