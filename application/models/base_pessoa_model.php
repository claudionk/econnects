<?php
Class Base_Pessoa_Model extends MY_Model
{
  //Dados da tabela e chave primária
  protected $_table = 'base_pessoa';
  protected $primary_key = 'base_pessoa_id';

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
  );

  function get_by_id($id)
  {
    return $this->get($id);
  }

  function getByDoc( $documento, $produto_parceiro_id, $info_service ){


    $this->load->model('base_pessoa_contato_model', 'base_pessoa_contato');
    $this->load->model('base_pessoa_empresa_model', 'base_pessoa_empresa');
    $this->load->model('base_pessoa_endereco_model', 'base_pessoa_endereco');
    $this->load->model('base_pessoa_endereco_model', 'base_pessoa_endereco');

    $documento = app_retorna_numeros($documento);


    $meses = BASE_TEMPO_NOVA_PESQUISA;

    $sql = "
                SELECT 
                base_pessoa.*, 
                IF(CURRENT_TIMESTAMP < DATE_ADD(base_pessoa.ultima_atualizacao, INTERVAL {$meses} MONTH), 1, 0) as atualizado
                FROM 
                base_pessoa
                WHERE 
                base_pessoa.deletado = 0
                and base_pessoa.documento = '{$documento}'         

        ";

    $result = $this->_database->query($sql)->result_array();

    if($result) {
      $result = $result[0];

      if($result['atualizado'] == 0){
        //faz a atualização do cliente.
        $documento = app_retorna_numeros($documento);
        $documento = str_pad($documento, 11, '0', STR_PAD_LEFT);

        $this->updateCliente( $documento, $result['base_pessoa_id'], $produto_parceiro_id, $info_service );
        $result = $this->_database->query($sql)->result_array();
        $result = $result[0];
        $result['contato'] = $this->base_pessoa_contato->with_contato_tipo()->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
        $result['empresa'] = $this->base_pessoa_empresa->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
        $result['endereco'] = $this->base_pessoa_endereco->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();

      }else{
        $result['contato'] = $this->base_pessoa_contato->with_contato_tipo()->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
        $result['empresa'] = $this->base_pessoa_empresa->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
        $result['endereco'] = $this->base_pessoa_endereco->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();

        return $result;
      }
    } else {
      $this->updateCliente( $documento, 0, $produto_parceiro_id, $info_service );
      $result = $this->_database->query($sql)->result_array();
      if($result) {
        $result = $result[0];
        $result['contato'] = $this->base_pessoa_contato->with_contato_tipo()->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
        $result['empresa'] = $this->base_pessoa_empresa->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
        $result['endereco'] = $this->base_pessoa_endereco->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
        return $result;
      }else{
        return array();
      }
    }

  }


  public function updateCliente( $documento, $base_pessoa_id = 0, $produto_parceiro_id, $info_service ){


    $this->load->model('base_pessoa_contato_model', 'base_pessoa_contato');
    $this->load->model('base_pessoa_empresa_model', 'base_pessoa_empresa');
    $this->load->model('base_pessoa_endereco_model', 'base_pessoa_endereco');

    if( $info_service == "unitfour_pf" ) {
      $this->load->library("Unitfour", array('produto_parceiro_id' => $produto_parceiro_id));
      $result = $this->unitfour->getBasePessoaPF(app_retorna_numeros($documento));
    }

    if( $info_service == "ifaro_pf" ) {
      $this->load->library("Ifaro", array('produto_parceiro_id' => $produto_parceiro_id));
      $ifaro = $this->ifaro->getBasePessoaPF( app_retorna_numeros( $documento ) );
      $DataNascimento = date_create_from_format( "d/m/Y", $ifaro["DataNascimento"] );
      $result["DADOS_CADASTRAIS"] = array( "CPF" => $ifaro["CPF"],
                                           "NOME" => $ifaro["Nome"],
                                           "NOME_ULTIMO" => trim( strrchr( $ifaro["Nome"], " " ) ),
                                           "SEXO" => $ifaro["Sexo"],
                                           "NOME_MAE" => $ifaro["Mae"],
                                           "DATANASC" => date_format( $DataNascimento, "Y-m-d" ),
                                           "IDADE" => $ifaro["Idade"],
                                           "SIGNO" => $ifaro["Signo"],
                                           "RG" => $ifaro["RG"],
                                           "SITUACAO_RECEITA" => "REGULAR" );
      
      $iTelefones = $ifaro["Telefones"];
      foreach( $iTelefones as $row ) {
        $Telefones[] = array("TELEFONE" => "(" . trim( $row["DD"] ) . ") " . $row["Numero"], 
                             "RANKING" => ( $row["Tipo"] == "TELEFONE MÓVEL" ? 90 : $row["Ranking"] ) );
      }
      $result["TELEFONES"] = $Telefones;

      $iEmails = $ifaro["Emails"];
      foreach( $iEmails as $row ) {
        $Emails[] = array( "EMAIL" => trim( $row["EmailEndereco"] ), "RANKING" => $row["Ranking"] );
      }
      $result["EMAILS"] = $Emails;

      $iEnderecos = $ifaro["Enderecos"];
      foreach( $iEnderecos as $row ) {
        $Enderecos[] = array("LOGRADOURO" => trim( $row["Logadouro"] ), 
                             "NUMERO" => trim( $row["Numero"] ), 
                             "COMPLEMENTO" => trim( $row["Complemento"] ), 
                             "BAIRRO" => trim( $row["Bairro"] ), 
                             "CIDADE" => trim( $row["Cidade"] ), 
                             "UF" => trim( $row["UF"] ), 
                             "CEP" => trim( $row["CEP"] ), 
                             "RANKING" => $row["Ranking"] );
      }
      $result["ENDERECOS"] = $Enderecos;
      //die( print_r( $result, true ) );
    }

    if( $result && isset( $result["DADOS_CADASTRAIS"] ) ) {
      if( $base_pessoa_id > 0 ) {
        $this->base_pessoa_contato->delete_by( array( "base_pessoa_id" => $base_pessoa_id ) );
        $this->base_pessoa_empresa->delete_by( array( "base_pessoa_id" => $base_pessoa_id ) );
        $this->base_pessoa_endereco->delete_by( array( "base_pessoa_id" => $base_pessoa_id ) );
        $this->update_base_pessoa( $base_pessoa_id, $result );
      } else {
        $this->update_base_pessoa( $base_pessoa_id, $result );
      }
    } else {
      return array();
    }
  }


  function update_base_pessoa( $base_pessoa_id, $data, $info_service = "unitfour_pf" ){

    $this->load->model('base_pessoa_contato_model', 'base_pessoa_contato');
    $this->load->model('base_pessoa_empresa_model', 'base_pessoa_empresa');
    $this->load->model('base_pessoa_endereco_model', 'base_pessoa_endereco');


    if($base_pessoa_id > 0){
      $pessoa = $this->get($base_pessoa_id);
    }else{
      $pessoa = array('quantidade_atualziacao' => 0);
    }


    $cliente_id = $this->cliente_insert_update($data);
    $data_pessoa = array();
    $data_pessoa['cliente_id'] = $cliente_id;
    $data_pessoa['documento'] = isset($data['DADOS_CADASTRAIS'][0]['CPF']) ? $data['DADOS_CADASTRAIS'][0]['CPF'] : $data['DADOS_CADASTRAIS']['CPF'];
    $data_pessoa['nome'] = isset($data['DADOS_CADASTRAIS'][0]['NOME']) ? $data['DADOS_CADASTRAIS'][0]['NOME'] : $data['DADOS_CADASTRAIS']['NOME'];
    $data_pessoa['sobrenome'] = isset($data['DADOS_CADASTRAIS'][0]['NOME_ULTIMO']) ? $data['DADOS_CADASTRAIS'][0]['NOME_ULTIMO'] : $data['DADOS_CADASTRAIS']['NOME_ULTIMO'];
    $data_pessoa['sexo'] = isset($data['DADOS_CADASTRAIS'][0]['SEXO']) ? $data['DADOS_CADASTRAIS'][0]['SEXO'] : $data['DADOS_CADASTRAIS']['SEXO'];
    $data_pessoa['nome_mae'] = isset($data['DADOS_CADASTRAIS'][0]['NOME_MAE']) ?  $data['DADOS_CADASTRAIS'][0]['NOME_MAE'] : $data['DADOS_CADASTRAIS']['NOME_MAE'];
    $data_pessoa['data_nascimento'] = isset($data['DADOS_CADASTRAIS'][0]['DATANASC']) ? app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS'][0]['DATANASC']) : app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);
    $data_pessoa['signo'] = isset($data['DADOS_CADASTRAIS'][0]['SIGNO']) ? $data['DADOS_CADASTRAIS'][0]['SIGNO'] : $data['DADOS_CADASTRAIS']['SIGNO'];
    $data_pessoa['situacao_receita'] = isset($data['DADOS_CADASTRAIS'][0]['SITUACAO_RECEITA']) ? $data['DADOS_CADASTRAIS'][0]['SITUACAO_RECEITA'] : $data['DADOS_CADASTRAIS']['SITUACAO_RECEITA'];
    $data_pessoa['ultima_atualizacao'] = date('Y-m-d H:i:s');
    $data_pessoa['quantidade_atualziacao'] = ((int)$pessoa['quantidade_atualziacao']) + 1;

    if($base_pessoa_id > 0){
      $this->update($base_pessoa_id, $data_pessoa, TRUE);

    }else{
      $base_pessoa_id = $this->insert($data_pessoa, TRUE);
    }


    //telefones;
    if(isset($data['TELEFONES'])){
      foreach ($data['TELEFONES'] as $telefone){
        $telefone['TELEFONE'] = isset($telefone['TELEFONE']) ? $telefone['TELEFONE'] : $telefone;
        if(!empty($telefone['TELEFONE'])) {
          $data_contato = array();
          $data_contato['base_pessoa_id'] = $base_pessoa_id;
          $data_contato['nome'] = $data['DADOS_CADASTRAIS']['NOME'];
          $data_contato['ranking'] = isset($telefone['RANKING']) ? $telefone['RANKING'] : 1;
          $data_contato['contato'] = app_retorna_numeros($telefone['TELEFONE']);
          $data_contato['contato_tipo_id'] = (app_validate_mobile_phone(app_format_telefone_unitfour(app_retorna_numeros($telefone['TELEFONE'])))) ? 2 : 3;
          $this->base_pessoa_contato->insert($data_contato, true);
        }

      }
    }

    if(isset($data['EMAILS'])){
      foreach ($data['EMAILS'] as $email){
        if(isset($email['EMAIL'])) {
          $data_contato = array();
          $data_contato['base_pessoa_id'] = $base_pessoa_id;
          $data_contato['nome'] = $data['DADOS_CADASTRAIS']['NOME'];
          $data_contato['ranking'] = isset($email['RANKING']) ? $email['RANKING'] : 1;
          $data_contato['contato'] = mb_strtolower($email['EMAIL'], 'UTF-8');
          $data_contato['contato_tipo_id'] = 1;
          $this->base_pessoa_contato->insert($data_contato, true);
        }
      }
    }


    if(isset($data['ENDERECOS'])){
      foreach ($data['ENDERECOS'] as $item){
        $data_endereco = array();
        $data_endereco['base_pessoa_id'] = $base_pessoa_id;
        $data_endereco['ranking'] =  (isset($item['RANKING']) && !is_array($item['RANKING'])) ? $item['RANKING'] : 0;
        $data_endereco['endereco_cep'] = (isset($item['CEP']) && !is_array($item['CEP'])) ? app_format_cep($item['CEP']) : '';
        $data_endereco['endereco'] = (isset($item['LOGRADOURO']) && !is_array($item['LOGRADOURO'])) ? $item['LOGRADOURO'] : '';
        $data_endereco['endereco_bairro'] = (isset($item['BAIRRO']) && !is_array($item['BAIRRO'])) ? $item['BAIRRO'] : '';
        $data_endereco['endereco_numero'] = (isset($item['NUMERO']) && !is_array($item['NUMERO'])) ? app_retorna_numeros($item['NUMERO']) : '';
        $data_endereco['endereco_complemento'] = (isset($item['COMPLEMENTO']) && !is_array($item['COMPLEMENTO'])) ? $item['COMPLEMENTO'] : '';
        $data_endereco['endereco_cidade'] = (isset($item['CIDADE']) && !is_array($item['CIDADE'])) ? $item['CIDADE'] : '';
        $data_endereco['endereco_uf'] = (isset($item['UF']) && !is_array($item['UF'])) ? $item['UF'] : '';
        $this->base_pessoa_endereco->insert($data_endereco, true);
      }
    }


    if(isset($data['PARTICIPACAO_EMPRESA'])){
      foreach ($data['PARTICIPACAO_EMPRESA'] as $item){
        $data_empresa = array();
        $data_empresa['base_pessoa_id'] = $base_pessoa_id;
        $data_empresa['ranking'] =  (isset($item['RANKING']) && !is_array($item['RANKING'])) ? $item['RANKING'] : 0;
        $data_empresa['nome'] = (isset($item['NOME']) && !is_array($item['NOME'])) ? $item['NOME'] : '';
        $data_empresa['documento'] = (isset($item['DOCUMENTO']) && !is_array($item['DOCUMENTO'])) ? $item['DOCUMENTO'] : '';
        $data_empresa['participacao'] = (isset($item['PCT_PARTICIPACAO']) && !is_array($item['PCT_PARTICIPACAO'])) ? $item['PCT_PARTICIPACAO'] : '';
        $data_empresa['data_entrada'] = (isset($item['DATA_ENTRADA']) && !is_array($item['DATA_ENTRADA'])) ? app_dateonly_mask_to_mysql($item['DATA_ENTRADA']) : '';
        $this->base_pessoa_empresa->insert($data_empresa, true);
      }
    }


  }


  public function cliente_insert_update($data){

    //print_r($data);exit;
    $this->load->model('cliente_model', 'cliente');
    $this->load->model('cliente_contato_model', 'cliente_contato');
    $this->load->model('cliente_codigo_model', 'cliente_codigo');
    $this->load->model('localidade_cidade_model', 'localidade_cidade');


    if(!isset($data['DADOS_CADASTRAIS'])){
      return 0;
    }

    //verifica se o cliente existe
    $documento = isset($data['DADOS_CADASTRAIS'][0]['CPF']) ? app_retorna_numeros($data['DADOS_CADASTRAIS'][0]['CPF']) : app_retorna_numeros($data['DADOS_CADASTRAIS']['CPF']);
    $cliente = $this->cliente->filterByCPFCNPJ($documento)
      ->get_all();




    if(count($cliente) == 0){
      //insere novo cliente
      $data_cliente = array();
      $data_cliente['tipo_cliente'] = (app_verifica_cpf_cnpj(app_retorna_numeros($data['DADOS_CADASTRAIS']['CPF'])) == 'CNPJ') ? 'CO' : 'CF';
      $data_cliente['cnpj_cpf'] = app_retorna_numeros($data['DADOS_CADASTRAIS']['CPF']);
      $data_cliente['codigo'] = $this->cliente_codigo->get_codigo_cliente_formatado($data_cliente['tipo_cliente']);
      $data_cliente['colaborador_id'] = 1;
      $data_cliente['colaborador_comercial_id'] = 1;
      $data_cliente['titular'] = 1;
      $data_cliente['razao_nome'] = $data['DADOS_CADASTRAIS']['NOME'];
      $data_cliente['data_nascimento'] = app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);
      $data_cliente['cliente_evolucao_status_id'] = 6; //Salva como prospect
      $data_cliente['grupo_empresarial_id'] = 0;


      if(isset($data['ENDERECOS'])){
        $data_cliente['cep'] = (isset($item['CEP']) && !is_array($item['CEP'])) ? app_format_cep($item['CEP']) : '';
        $data_cliente['endereco'] = (isset($item['LOGRADOURO']) && !is_array($item['LOGRADOURO'])) ? $item['LOGRADOURO'] : '';
        $data_cliente['bairro'] = (isset($item['BAIRRO']) && !is_array($item['BAIRRO'])) ? $item['BAIRRO'] : '';
        $data_cliente['numero'] = (isset($item['NUMERO']) && !is_array($item['NUMERO'])) ? app_retorna_numeros($item['NUMERO']) : '';
        $data_cliente['complemento'] = (isset($item['COMPLEMENTO']) && !is_array($item['COMPLEMENTO'])) ? $item['COMPLEMENTO'] : '';
        if((isset($item['CIDADE']) && !is_array($item['CIDADE']))){
          $localidade_cidade = $this->localidade_cidade->get_by_nome($item['CIDADE']);
          if($localidade_cidade){
            $data_cliente['localidade_cidade_id'] = $localidade_cidade['localidade_cidade_id'];
          }
        }


      }


      $cliente_id = $this->cliente->insert($data_cliente, TRUE);

      $data_contato = array();
      $data_contato['cliente_id'] = $cliente_id;
      $data_contato['cliente_contato_nivel_relacionamento_id'] = 3;
      $data_contato['decisor'] = 1;
      $data_contato['nome'] = $data['DADOS_CADASTRAIS']['NOME'];
      $data_contato['data_nascimento'] =  app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);


      if(isset($data['TELEFONES'])){
        foreach ($data['TELEFONES'] as $telefone){
          $data_contato['contato'] = app_retorna_numeros($telefone['TELEFONE']);
          $data_contato['contato_tipo_id'] = (app_validate_mobile_phone(app_format_telefone_unitfour(app_retorna_numeros($telefone['TELEFONE'])))) ? 2 : 3;
          $this->cliente_contato->insert_contato($data_contato);
        }
      }

      if(isset($data['EMAILS'])){
        foreach ($data['EMAILS'] as $email){
          if(isset($email['EMAIL'])){
            $data_contato['contato'] = $email['EMAIL'];
            $data_contato['contato_tipo_id'] = 1;
            $this->cliente_contato->insert_contato($data_contato);
          }
        }
      }



    }else{
      //
      $cliente_id = $cliente[0]['cliente_id'];

      $data_cliente = array();
      $data_cliente['cnpj_cpf'] = app_retorna_numeros($data['DADOS_CADASTRAIS']['CPF']);
      $data_cliente['razao_nome'] = $data['DADOS_CADASTRAIS']['NOME'];
      $data_cliente['data_nascimento'] = app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);


      if(isset($data['ENDERECOS'])){
        $data_cliente['cep'] = (isset($item['CEP']) && !is_array($item['CEP'])) ? app_format_cep($item['CEP']) : '';
        $data_cliente['endereco'] = (isset($item['LOGRADOURO']) && !is_array($item['LOGRADOURO'])) ? $item['LOGRADOURO'] : '';
        $data_cliente['bairro'] = (isset($item['BAIRRO']) && !is_array($item['BAIRRO'])) ? $item['BAIRRO'] : '';
        $data_cliente['numero'] = (isset($item['NUMERO']) && !is_array($item['NUMERO'])) ? app_retorna_numeros($item['NUMERO']) : '';
        $data_cliente['complemento'] = (isset($item['COMPLEMENTO']) && !is_array($item['COMPLEMENTO'])) ? $item['COMPLEMENTO'] : '';
        if((isset($item['CIDADE']) && !is_array($item['CIDADE']))){
          $localidade_cidade = $this->localidade_cidade->get_by_nome($item['CIDADE']);
          if($localidade_cidade){
            $data_cliente['localidade_cidade_id'] = $localidade_cidade['localidade_cidade_id'];
          }
        }


      }


      $this->cliente->update($cliente_id, $data_cliente, TRUE);

      $data_contato = array();
      $data_contato['cliente_id'] = $cliente_id;
      $data_contato['cliente_contato_nivel_relacionamento_id'] = 3;
      $data_contato['decisor'] = 1;
      $data_contato['nome'] = $data['DADOS_CADASTRAIS']['NOME'];
      $data_contato['data_nascimento'] =  app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);


      if(isset($data['TELEFONES'])){
        foreach ($data['TELEFONES'] as $telefone){
          $telefone['TELEFONE'] = isset($telefone['TELEFONE']) ? $telefone['TELEFONE'] : $telefone;
          if(!empty($telefone['TELEFONE'])) {
            $data_contato['contato'] = app_retorna_numeros($telefone['TELEFONE']);
            $data_contato['contato_tipo_id'] = (app_validate_mobile_phone(app_format_telefone_unitfour(app_retorna_numeros($telefone['TELEFONE'])))) ? 2 : 3;
            $this->cliente_contato->insert_not_exist_contato($data_contato);
          }
        }
      }

      if(isset($data['EMAILS'])){
        foreach ($data['EMAILS'] as $email){
          if(isset($email['EMAIL'])) {
            $data_contato['contato'] = $email['EMAIL'];
            $data_contato['contato_tipo_id'] = 1;
            $this->cliente_contato->insert_not_exist_contato($data_contato);
          }
        }
      }


    }

    return $cliente_id;
  }


}

