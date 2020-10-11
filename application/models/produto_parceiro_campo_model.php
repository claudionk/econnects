<?php
Class Produto_Parceiro_Campo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_campo';
    protected $primary_key = 'produto_parceiro_campo_id';

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
            'field' => 'produto_parceiro_id',
            'label' => 'Produto parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'campo_tipo_id',
            'label' => 'Tipo de Campo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'campo_id',
            'label' => 'Campo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'label',
            'label' => 'Label',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'opcoes',
            'label' => 'Opções',
            'groups' => 'default'
        ),
        array(
            'field' => 'validacoes',
            'label' => 'Validações',
            'groups' => 'default'
        ),
        array(
            'field' => 'classe_css',
            'label' => 'Classe',
            'groups' => 'default'
        ),
        array(
            'field' => 'ordem',
            'label' => 'Ordem',
            'groups' => 'default'
        ),
        array(
            'field' => 'tamanho',
            'label' => 'Tamanho',
            'groups' => 'default'
        ),

    );

    public function get_form_data($just_check = false)
    {
        $data = parent::get_form_data($just_check);

        if(isset($data['validacoes']) && is_array($data['validacoes']))
        {
            $data['validacoes'] = implode("|", $data['validacoes']);
        }

        if(isset($data['classe_css']) && is_array($data['classe_css']))
        {
            $data['classe_css'] = implode("|", $data['classe_css']);
        }

        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_campo(){
        $this->with_simple_relation('campo', 'campo_', 'campo_id',
                array(
                       'nome', 'slug', 'nome_banco', 'nome_banco_equipamento', 'nome_banco_generico',
                      'nome_banco_viagem',  'opcoes', 'classes', 'function_salvar', 'function_exibir'));
        return $this;
    }

    function with_campo_tipo(){
        $this->_database->select('campo_tipo.nome, campo_tipo.slug');
        $this->_database->join('campo_tipo', "{$this->_table}.campo_tipo_id = campo_tipo.campo_tipo_id", 'inner');
        return $this;
    }

    function with_campo_classe(){
        $this->_database->select('campo_classe.nome as classe_nome, campo_classe.slug classe_slug');
        $this->_database->join('campo_classe', 'campo.campo_classe_id = campo_classe.campo_classe_id', 'inner');
        return $this;
    }

    function filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);

        return $this;
    }

    function filter_by_campo_tipo($campo_tipo_id){

        $this->_database->where("campo_tipo.campo_tipo_id", $campo_tipo_id);

        return $this;
    }

    function filter_by_campo_tipo_slug($slug){

        $this->_database->where("campo_tipo.slug", $slug);

        return $this;
    }

    function filter_by_campo_slug($slug){

        $this->_database->where("campo.slug", $slug);

        return $this;
    }

    function filter_by_validacoes(){

        $this->_database->where("{$this->_table}.validacoes <> '0'" );

        return $this;
    }
    

    function coreSelecCampoProdutoParceiro($produto_parceiro_id, $campo_tipo_id){
        $this->_database->select("{$this->_table}.campo_id");
        $this->_database->select("{$this->_table}.label");
        $this->_database->select("{$this->_table}.tamanho");
        $this->_database->select("{$this->_table}.opcoes");
        $this->_database->select("{$this->_table}.validacoes");
        $this->_database->select("{$this->_table}.classe_css");
        $this->_database->select("campo.nome");
        $this->_database->select("CASE produto.slug 
            WHEN 'equipamento' THEN
                campo.nome_banco_equipamento
            WHEN 'seguro_viagem' THEN
                campo.nome_banco_viagem
            WHEN 'generico' THEN
                campo.nome_banco_generico
            ELSE
                campo.nome_banco
        END AS nome_banco,", FALSE);
        $this->_database->select("campo.slug");
        $this->_database->select("campo.classes");
        $this->_database->join('campo', "campo.campo_id = {$this->_table}.campo_id", 'inner');
        $this->_database->join('campo_tipo', "campo_tipo.campo_tipo_id = {$this->_table}.campo_tipo_id", 'inner');
        $this->_database->join('produto_parceiro', "produto_parceiro.produto_parceiro_id = {$this->_table}.produto_parceiro_id", 'inner');
        $this->_database->join('produto', "produto_parceiro.produto_id = produto.produto_id", 'inner');

        $this->_database->where("campo_tipo.campo_tipo_id", $campo_tipo_id);
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->order_by("{$this->_table}.ordem", 'ASC');
        return $this;
    }

    function buscaCampoUsado($campo_id){
        // $this->_database->select("count(1) c");

        // $this->_database->select("campo.nome");
        // $this->_database->select("produto_parceiro.nome");
        // $this->_database->select("parceiro.nome_fantasia");
        // $this->_database->select("campo_tipo.nome");

        $this->_database->join('produto_parceiro', "produto_parceiro.produto_parceiro_id = {$this->_table}.produto_parceiro_id", 'inner');
        $this->_database->join('parceiro', "parceiro.parceiro_id = produto_parceiro.parceiro_id", 'inner');
        $this->_database->join('campo', "campo.campo_id = {$this->_table}.campo_id", 'inner');
        $this->_database->join('campo_tipo', "campo_tipo.campo_tipo_id = {$this->_table}.campo_tipo_id", 'inner');

        $this->_database->where("campo.campo_id", $campo_id);
        $this->_database->where("{$this->_table}.deletado", 0);
        return $this;
    }

    /**
     * @description Seta os dados do post
     * @param $produto_parceiro_id
     * @param $slug
     * @param $plano
     * @param $dados
     * @param $cont
     * @return mixed
     */
    public function setDadosCampos($produto_parceiro_id, $produto, $slug, $plano, &$dados, $cont = null, $field = 'default', $in = [])
    {
        $campos = $this->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug($slug)
            ->order_by("ordem", "asc")
            ->get_all();

        // auxiliar no nome do campo para diferenciar dependente/passageiro
        $aux = !empty($cont) ? "_{$cont}" : "";

        if($campos)
        {
            foreach ($campos as $index => $campo)
            {
                // $value = ($field == 'default') ? $this->input->post("plano_{$plano}{$aux}_{$campo['campo_nome_banco']}") : $this->input->post("{$campo['campo_nome_banco']}");
                if ($field == 'default') {
                    $value = $this->input->post("plano_{$plano}{$aux}_{$campo['campo_nome_banco']}");
                } else {
                    $value = $in[$campo['campo_nome_banco']];
                }
                if( $value )
                {
                    if($campo['campo_nome_banco'] == 'nota_fiscal_valor'){
                        if( strpos( $value, "," ) !== false || strpos( $value, "_" ) !== false )
                            $value = app_unformat_currency($value);
                    }

                    if($campo['campo_nome_banco'] == 'valor_desconto'){
                        if( strpos( $value, "," ) !== false || strpos( $value, "_" ) !== false )
                            $value = app_unformat_currency($value);
                    }

                    if($campo['campo_nome_banco'] == 'cnpj_cpf'){
                        $value = app_retorna_numeros($value);
                    }

                    if(($campo['campo_function_salvar']) && (function_exists($campo['campo_function_salvar']))){
                        $value = call_user_func($campo['campo_function_salvar'], $value);
                    }

                    if(!empty($campo["campo_nome_banco_{$produto}"])) {
                        $dados[$campo["campo_nome_banco_{$produto}"]] = $value;
                    }
                }
                
            }
        }
        return $dados;
    }

    /**
     * @param $produto_parceiro_id
     * @param $slug
     * @param $plano
     * @return array
     */
    public function setValidacoesCamposPlano($produto_parceiro_id, $slug, $planos){
        $campos = $this->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug($slug)
            ->filter_by_validacoes()
            ->order_by("ordem", "asc")
            ->get_all();

        $validacoes = array();
        $planos = explode(';', $planos);

        if(($campos) && ($planos)){
            foreach ($planos as $idx => $plano) {
                foreach ($campos as $index => $campo) {
                    $validacao = $campo['validacoes'];
                    if(strpos($campo['validacoes'], 'matches') !== FALSE){
                        $validacao = str_replace('matches[password]', '', $validacao);
                        $validacao = str_replace('||', '|', $validacao);
                    }
                    $validacoes[] = array(
                        'field' => "plano_{$plano}_{$campo['campo_nome_banco']}",
                        'label' => "{$campo['campo_nome']}",
                        'rules' => $validacao,
                        'groups' => $slug
                    );
                    if(($campo['campo_slug'] == 'password') && (strpos($campo['validacoes'], 'matches')!== FALSE)){
                        $validacoes[] = array(
                            'field' => "plano_{$plano}_{$campo['campo_nome_banco']}_confirm",
                            'label' => "{$campo['campo_nome']}",
                            'rules' => "trim|required|check_password_confirm[plano_{$plano}_{$campo['campo_nome_banco']}]",
                            'groups' => $slug
                        );
                    }
                }
            }
        }
        return $validacoes;

    }

    /**
     * @param $produto_parceiro_id
     * @param $slug
     * @param $plano
     * @param $adicionais
     * @param $texto_view
     * @return array
     */
    public function setValidacoesCamposAdicionais($produto_parceiro_id, $slug, $planos, $adicionais = array(), $texto_view = '' ){
        $campos = $this->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug($slug)
            ->filter_by_validacoes()
            ->order_by("ordem", "asc")
            ->get_all();

        $validacoes = array();
        $planos = explode(';', $planos);

        if(($campos) && ($planos)){
            foreach ($planos as $index => $plano) {
                for ($cont = 2; $cont <= $adicionais[$index]; $cont++ ) {
                    foreach ($campos as $campo) {
                        $validacao = $campo['validacoes'];
                        if(strpos($campo['validacoes'], 'matches') !== FALSE){
                            $validacao = str_replace('matches[password]', '', $validacao);
                            $validacao = str_replace('||', '|', $validacao);
                        }

                        $validacoes[] = array(
                            'field' => "plano_{$plano}_{$cont}_{$campo['campo_nome_banco']}",
                            'label' => "{$texto_view} {$cont} - {$campo['campo_nome']}",
                            'rules' => $validacao,
                            'groups' => "dados_segurado",
                        );
                    }
                }
            }
        }
        return $validacoes;

    }

    /**
     * @param $produto_parceiro_id
     * @param $slug
     * @return array
     */
    public function setValidacoesCampos($produto_parceiro_id, $slug){
        $campos = $this->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug($slug)
            ->order_by("ordem", "asc")
            ->get_all();

        $validacoes = array();

        //print_r($campos);exit;
        if($campos){
            foreach ($campos as $index => $campo) {

                $validacao = $campo['validacoes'];
                if(strpos($campo['validacoes'], 'matches') !== FALSE){
                    $validacao = str_replace('matches[password]', '', $validacao);
                    $validacao = str_replace('||', '|', $validacao);
                }
                $validacoes[] = array(
                    'field' => "{$campo['campo_nome_banco']}",
                    'label' => "{$campo['campo_nome']}",
                    'rules' => $validacao,
                    'groups' => $slug
                );
                if(($campo['campo_slug'] == 'password') && (strpos($campo['validacoes'], 'matches')!== FALSE)){
                    $validacoes[] = array(
                        'field' => "{$campo['campo_nome_banco']}_confirm",
                        'label' => "{$campo['campo_nome']}",
                        'rules' => "trim|required|check_password_confirm[{$campo['campo_nome_banco']}]",
                        'groups' => $slug
                    );
                }
            }
        }
        return $validacoes;

    }

    public function validate_campos($produto_parceiro_id, $slugsCampos = [], $values = [])
    {
        if ( !is_array($slugsCampos) ) {
            $slugsCampos = [$slugsCampos];
        }

        $validacao = [];
        foreach ($slugsCampos as $tipo_slug) {

            $result  = array(
                "status"    => false,
                "message"   => "Erro na validação dos campos",
                "mensagem"  => "Erro na validação dos campos",
                "group"     => $tipo_slug,
            );
            $erros = array();
            $campos = $this
                ->with_campo()
                ->with_campo_tipo()
                ->filter_by_produto_parceiro( $produto_parceiro_id )
                ->filter_by_campo_tipo_slug( $tipo_slug )
                ->order_by( "ordem", "ASC" )
                ->get_all();

            $validacao_ok = true;
            foreach( $campos as $campo ) {
                if( strpos( $campo["validacoes"], "required" ) !== false ) {
                    if( !isset( $values[$campo["campo_nome_banco_equipamento"]] ) || trim($values[$campo["campo_nome_banco_equipamento"]]) == '' ) {
                        $erros[] = "O campo ". $campo["campo_nome"] ." (". $campo["campo_nome_banco_equipamento"] .") não foi informado";
                        $validacao_ok = false;
                    }
                }
            }

            if( !$validacao_ok || sizeof( $erros ) > 0 ) {
                $result["erros"] = $erros;
                $result["errors"] = $erros;
                return $result;
            }

            foreach( $campos as $campo ) {
                $rule_check = "OK";
                if( strpos( $campo["validacoes"], "required" ) !== false && ( is_null( $values[$campo["campo_nome_banco_equipamento"]] ) || trim($values[$campo["campo_nome_banco_equipamento"]]) == ''  ) ) {
                    $rule_check = "O preenchimento do campo ". $campo["campo_nome_banco_equipamento"] ." é obrigatório";
                    $erros[] = $rule_check;
                } else {
                    if( isset($values[$campo["campo_nome_banco_equipamento"]]) && $values[$campo["campo_nome_banco_equipamento"]] != "0000-00-00" && !is_null( $values[$campo["campo_nome_banco_equipamento"]] )  ) {
                        if( strpos( $campo["validacoes"], "validate_data" ) !== false ) {
                            if ( !empty($values[$campo["campo_nome_banco_equipamento"]]) )
                            {
                                $valida_data = date_parse_from_format("Y-m-d", $values[$campo["campo_nome_banco_equipamento"]]);
                                if( !checkdate( $valida_data["month"], $valida_data["day"], $valida_data["year"] ) ) {
                                    $rule_check = "Data inválida (". $campo["campo_nome_banco_equipamento"] .")";
                                    $erros[] = $rule_check;
                                }
                            }
                        }
                        if( strpos( $campo["validacoes"], "validate_email" ) !== false ) {
                            if ( !empty($values[$campo["campo_nome_banco_equipamento"]]) )
                            {
                                $valida_email = filter_var( $values[$campo["campo_nome_banco_equipamento"]], FILTER_VALIDATE_EMAIL );
                                if( !$valida_email ) {
                                    $rule_check = "E-mail inválido (". $campo["campo_nome_banco_equipamento"] .")";
                                    $erros[] = $rule_check;
                                }
                            }
                        }
                        if( strpos( $campo["validacoes"], "validate_celular" ) !== false ) {
                            if( !empty($values[$campo["campo_nome_banco_equipamento"]]) && !app_validate_celular( $values[$campo["campo_nome_banco_equipamento"]] ) ) {
                                $rule_check = "Número de telefone celular inválido (". $campo["campo_nome_banco_equipamento"] .")";
                                $erros[] = $rule_check;
                            }
                        }
                        if( $campo["campo_nome_banco_equipamento"] == 'endereco_cep' ) {
                            if( !app_validate_cep($values[$campo["campo_nome_banco_equipamento"]]) ) {
                                $rule_check = "CEP inválido (". $campo["campo_nome_banco_equipamento"] .")";
                                $erros[] = $rule_check;
                            }
                        }
                        if( $campo["campo_nome_banco_equipamento"] == 'endereco_logradouro' ) {
                            if( !empty($values[$campo["campo_nome_banco_equipamento"]]) && strlen(trataRetorno($values[$campo["campo_nome_banco_equipamento"]])) < 3 ) {
                                $rule_check = "Logradouro precisa ter mais que 3 caracteres (". $campo["campo_nome_banco_equipamento"] .")";
                                $erros[] = $rule_check;
                            }
                        }
                        if( $campo["campo_nome_banco_equipamento"] == 'cnpj_cpf' ) {
                            if( strpos( $campo["validacoes"], "validate_cpf" ) !== false && !empty($values[$campo["campo_nome_banco_equipamento"]]) && !app_validate_cpf_cnpj( $values[$campo["campo_nome_banco_equipamento"]] ) ) {
                                $rule_check = "CPF / CNPJ não é válido (". $campo["campo_nome_banco_equipamento"] .")";
                                $erros[] = $rule_check;
                            }
                        }
                    }
                }

                $validacao[] = array(
                    "field" => $campo["campo_nome_banco_equipamento"],
                    "label" => $campo["campo_nome"],
                    "value" => isempty($values[$campo["campo_nome_banco_equipamento"]], ''),
                    "rules" => $campo["validacoes"],
                    "rule_check" => $rule_check,
                    "groups" => $tipo_slug
                );
            }

            if( !$validacao_ok || sizeof( $erros ) > 0 ) {
                $result["erros"] = $erros;
                $result["errors"] = $erros;
                return $result;
            }

        }

        $result = [
            "status"    => true,
            "message"   => 'OK',
            "mensagem"  => 'OK',
            'validacao' => $validacao,
        ];

        return $result;

    }

    public function getCamposProduto($produto_parceiro_id, $slug)
    {
        $api_key = app_get_token();
        $campos = array();

        $Url = $this->config->item('base_url') ."api/campos?produto_parceiro_id={$produto_parceiro_id}&slug={$slug}";

        $myCurl = curl_init();
        curl_setopt( $myCurl, CURLOPT_URL, $Url );
        curl_setopt( $myCurl, CURLOPT_FRESH_CONNECT, 1 );
        curl_setopt( $myCurl, CURLOPT_POST, 0 );
        curl_setopt( $myCurl, CURLOPT_VERBOSE, 0);
        curl_setopt( $myCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $myCurl, CURLOPT_HTTPHEADER, array( "Content-Type: application/json", "apikey: $api_key" ) );
        curl_setopt( $myCurl, CURLOPT_TIMEOUT, 15 );
        curl_setopt( $myCurl, CURLOPT_CONNECTTIMEOUT, 15 );
        $Response = curl_exec( $myCurl );
        curl_close( $myCurl );

        $Response = json_decode( $Response, true );
        if ( isset($Response[0]) )
        {
            $Response = $Response[0];
            $campos = $Response["campos"];
        }

        return [ 'campos' => $campos, 'token' => $api_key ];
    }

}
