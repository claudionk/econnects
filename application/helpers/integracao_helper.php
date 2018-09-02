<?php
/**
 * Created by PhpStorm.
 * User: Danilo Quinelato
 * Date: 14/12/2017
 * Time: 17:27
 */



if ( ! function_exists('app_integracao_date')) {
    function app_integracao_date($formato, $dados = array())
    {

        return date($formato);

    }
}


if ( ! function_exists('app_integracao_get_sequencial')) {
    function app_integracao_get_sequencial($formato, $dados = array())
    {

        return str_pad($dados['log']['sequencia'], $dados['item']['tamanho'], $dados['item']['valor_padrao'], STR_PAD_LEFT);

    }
}

if ( ! function_exists('app_integracao_format_decimal')) {
    function app_integracao_format_decimal($formato, $dados = array())
    {

        $valor = explode('.', $dados['registro'][$formato]);
        return str_pad($valor[0], 13, '0', STR_PAD_LEFT) . str_pad($valor[1], 2, '0', STR_PAD_LEFT);

    }
}

if ( ! function_exists('app_integracao_format_porcentagem')) {
    function app_integracao_format_porcentagem($formato, $dados = array())
    {

        $valor = explode('.', $dados['registro'][$formato]);
        return str_pad($valor[0], 2, '0', STR_PAD_LEFT) . str_pad($valor[1], 2, '0', STR_PAD_LEFT);

    }
}

if ( ! function_exists('app_integracao_get_qnt_registros')) {
    function app_integracao_get_qnt_registros($formato, $dados = array())
    {

        return str_pad($dados['log']['quantidade_registros'] + 2, $dados['item']['tamanho'], $dados['item']['valor_padrao'], STR_PAD_LEFT);

    }
}


if ( ! function_exists('app_integracao_get_valor_total')) {
    function app_integracao_get_valor_total($formato, $dados = array())
    {

        $valor = 0.0;
        foreach ($dados['registro'] as $registro) {
            $valor += $registro['valor_premio_total'];
        }

        $valor = ($valor == 0) ? '0.0' : $valor;
        $valor = explode('.', $valor);
        return str_pad($valor[0], 13, '0', STR_PAD_LEFT) . str_pad($valor[1], 2, '0', STR_PAD_LEFT);

    }
}

if ( ! function_exists('app_integracao_mapfre_rf_total_registro')) {
    function app_integracao_mapfre_rf_total_registro($formato, $dados = array())
    {

        return str_pad($dados['global']['totalRegistros'], $formato, '0', STR_PAD_LEFT);

    }
}

if ( ! function_exists('app_integracao_mapfre_rf_total_itens')) {
    function app_integracao_mapfre_rf_total_itens($formato, $dados = array())
    {

          return str_pad($dados['global']['totalItens'], $formato, '0', STR_PAD_LEFT);

    }
}

if ( ! function_exists('app_integracao_mapfre_rf_total_certificado')) {
    function app_integracao_mapfre_rf_total_certificado($formato, $dados = array())
    {

          return str_pad((count($dados['registro'])), $formato, '0', STR_PAD_LEFT);

    }
}

if ( ! function_exists('app_integracao_get_total_registro')) {
    function app_integracao_get_total_registro($formato, $dados = array())
    {

          return str_pad(count($dados['registro']), $formato, '0', STR_PAD_LEFT);

    }
}
if ( ! function_exists('app_integracao_get_total_titulos')) {
    function app_integracao_get_total_titulos($formato, $dados = array())
    {
            $valor = 0.0;
            foreach ($dados['registro'] as $registro) {
                $valor += $registro['valor_custo_titulo'];
            }

            $valor = ($valor == 0) ? '0.0' : $valor;
            $valor = explode('.', $valor);
            $valor[1] = ((isset($valor[1])) || (empty(isset($valor[1]))) ) ? '0' : $valor[1];
             return str_pad($valor[0],  ($formato -8), '0', STR_PAD_LEFT) . str_pad($valor[1], 8, '0', STR_PAD_LEFT);
//            return str_pad($valor[0], ($formato) - 2, '0', STR_PAD_LEFT) . str_pad($valor[1], 2, '0', STR_PAD_LEFT);

    }
}
if ( ! function_exists('app_integracao_format_sequencia')) {


    function app_integracao_format_sequencia($formato, $dados = array())
    {

        if((isset($dados['registro'][0]['num_remessa'])) && (isset($dados['registro'][0]['capitalizacao_id'])) ){
            $num_remessa =  $dados['registro'][0]['num_remessa'] += 1;
            $CI =& get_instance();
            $CI->load->model('capitalizacao_model');
            $data_cap = array('num_remessa' => $num_remessa);
            $CI->capitalizacao_model->update($dados['registro'][0]['capitalizacao_id'], $data_cap, TRUE);

            return date('Y') . str_pad($num_remessa, 6, '0', STR_PAD_LEFT);
        }else{
            return date('Y') . '000001';
        }

    }

}

if ( ! function_exists('app_integracao_format_sequencia_cap_mapfre')) {


    function app_integracao_format_sequencia_cap_mapfre($formato, $dados = array())
    {

        if((isset($dados['registro'][0]['num_remessa'])) && (isset($dados['registro'][0]['capitalizacao_id'])) ){
            $num_remessa =  $dados['registro'][0]['num_remessa'] += 1;
            $CI =& get_instance();
            $CI->load->model('capitalizacao_model');
            $data_cap = array('num_remessa' => $num_remessa);
            $CI->capitalizacao_model->update($dados['registro'][0]['capitalizacao_id'], $data_cap, TRUE);

            return str_pad($num_remessa, 5, '0', STR_PAD_LEFT);
        }else{
            return '00001';
        }

    }

}

if ( ! function_exists('app_integracao_format_sequencia_mapfre')) {


    function app_integracao_format_sequencia_mapfre($formato, $dados = array())
    {

        if((isset($dados['registro'][0]['num_remessa'])) && (isset($dados['registro'][0]['capitalizacao_id'])) ){
            $num_remessa =  $dados['registro'][0]['num_remessa'] += 1;
            $CI =& get_instance();
            $CI->load->model('capitalizacao_model');
            $data_cap = array('num_remessa' => $num_remessa);
            $CI->capitalizacao_model->update($dados['registro'][0]['capitalizacao_id'], $data_cap, TRUE);

            return str_pad($num_remessa, 10, '0', STR_PAD_LEFT);
        }else{
            return '0000000001';
        }

    }

}

if ( ! function_exists('app_integracao_format_quantidade_inclusao')) {


    function app_integracao_format_quantidade_inclusao($formato, $dados = array())
    {


        $tamanho = (isset($dados['item']['tamanho'])) ? $dados['item']['tamanho'] : 10;
        $quantidade = 0;

        if (isset($dados['registro'])){
            foreach ($dados['registro'] as $registro) {
                if($registro['status'] == 'I'){
                    $quantidade++;
                }
            }
        }
        return str_pad($quantidade, $tamanho, '0', STR_PAD_LEFT);

    }

}
if ( ! function_exists('app_integracao_format_quantidade_exclusao')) {


    function app_integracao_format_quantidade_exclusao($formato, $dados = array())
    {


        $tamanho = (isset($dados['item']['tamanho'])) ? $dados['item']['tamanho'] : 10;
        $quantidade = 0;
        if (isset($dados['registro'])){
            foreach ($dados['registro'] as $registro) {
                if($registro['status'] == 'E'){
                    $quantidade++;
                }
            }
        }
        return str_pad($quantidade, $tamanho, '0', STR_PAD_LEFT);

    }

}

if ( ! function_exists('app_integracao_format_quantidade_alteracao')) {


    function app_integracao_format_quantidade_alteracao($formato, $dados = array())
    {


        $tamanho = (isset($dados['item']['tamanho'])) ? $dados['item']['tamanho'] : 10;
        $quantidade = 0;

        if (isset($dados['registro'])){
            foreach ($dados['registro'] as $registro) {
                if($registro['status'] == 'A'){
                    $quantidade++;
                }
            }
        }
        return str_pad($quantidade, $tamanho, '0', STR_PAD_LEFT);

    }

}

if ( ! function_exists('app_integracao_format_quantidade_total')) {


    function app_integracao_format_quantidade_total($formato, $dados = array())
    {


        $tamanho = (isset($dados['item']['tamanho'])) ? $dados['item']['tamanho'] : 10;
        $quantidade = 0;
        if (isset($dados['registro'])){
            foreach ($dados['registro'] as $registro) {
                $quantidade++;
            }
        }
        return str_pad($quantidade, $tamanho, '0', STR_PAD_LEFT);

    }

}


if ( ! function_exists('app_integracao_format_str_pad')) {

    function app_integracao_format_str_pad($formato, $dados = array())
    {

        $a = explode(",", $formato);
        return str_pad(issetor($dados[$a[0]][$a[1]], 0) , $a[2], '0', STR_PAD_LEFT);
    }

}
if ( ! function_exists('app_integracao_format_moeda_pad')) {

    function app_integracao_format_moeda_pad($formato, $dados = array())
    {

        $a = explode(",", $formato);
        $valor = $dados[$a[0]][$a[1]];
        $valor = ($valor == 0) ? '0.0' : $valor;
        $valor = explode('.', $valor);
        $valor[1] = ((isset($valor[1])) || (empty(isset($valor[1]))) ) ? '00' : $valor[1];
        return str_pad($valor[0],  ($a[2] -8), '0', STR_PAD_LEFT) . str_pad($valor[1], 8, '0', STR_PAD_LEFT);

    }

}
if ( ! function_exists('app_integracao_format_decimal_pad')) {

    function app_integracao_format_decimal_pad($formato, $dados = array())
    {

        $a = explode("|", $formato);
        $valor = (!empty($dados[$a[0]][$a[1]])) ? $dados[$a[0]][$a[1]] : 0;
        $valor = ($valor == 0) ? '0.0' : $valor;
        $valor = explode('.', $valor);
        $valor[1] = ((isset($valor[1])) || (empty(isset($valor[1]))) ) ? '00' : $valor[1];
        return str_pad($valor[0],  ($a[2]-3), '0', STR_PAD_LEFT) .$a[3]. str_pad($valor[1], 2, '0', STR_PAD_LEFT);

    }

}
if ( ! function_exists('app_integracao_format_decimal_r')) {

    function app_integracao_format_decimal_r($formato, $dados = array())
    {

        $f = explode("|", $formato);
        $defaultValue = str_pad(0,  100, '0', STR_PAD_LEFT);
        $valor = (!empty($dados['valor'])) ? (int)$dados['valor'] : $defaultValue;

        $a = (int)left($valor, strlen($valor)-$f[0]);
        $b = $f[1];
        $c = right($valor, $f[0]);
        return $a.$b.$c;
    }

}
if ( ! function_exists('app_integracao_format_date_r')) {

    function app_integracao_format_date_r($formato, $dados = array())
    {

        $a = explode("|", $formato);
        
        $date = $dados['valor'];
        if( isset( $dados['valor'] ) && !empty($dados['valor']) ){
            $date = date_create_from_format( $a[0], $dados['valor'] );
            $date = $date->format($a[1]);
        }

        return $date;
    }

}           
if ( ! function_exists('app_integracao_format_file_name_capmapfre')) {

    function app_integracao_format_file_name_capmapfre($formato, $dados = array())
    {
        /*MCAP_II_NEW_PPPP_DDMMAA_SS.TXT*/
        $file = "MCAP_II_NEW_4284_". date('dmy'). '_01.TXT';
        return  $file;
    }

}
if ( ! function_exists('app_integracao_format_file_name_capmapfre_titulos')) {

    function app_integracao_format_file_name_capmapfre_titulos($formato, $dados = array())
    {
        /*MCAP_II_NEW_PPPP_DDMMAA_SS.TXT*/
        $file = "MCAP_II_PAG_4284_". date('dmy'). '_01.TXT';
        return  $file;
    }
}
if ( ! function_exists('app_integracao_format_int')) {

    function app_integracao_format_int($formato, $dados = array())
    {
        return isset($dados['valor']) ? (int)$dados['valor'] : 0;
    }

}
if ( ! function_exists('app_integracao_format_file_name_mapfre_assistencia')) {

    function app_integracao_format_file_name_mapfre_assistencia($formato, $dados = array())
    {
        $file = "SIS01_". date('dmY'). '_1.TXT';
        return  $file;
    }

}
if ( ! function_exists('app_integracao_format_file_name_mapfre_rf')) {

    function app_integracao_format_file_name_mapfre_rf($formato, $dados = array())
    {

        if(isset($dados['item']['integracao_id'])){

            $CI =& get_instance();
            $CI->load->model('integracao_model');
            $num_sequencia = (int)$CI->integracao_model->get($dados['item']['integracao_id'])['sequencia'];
            $num_sequencia++;
            $CI->integracao_model->update($dados['item']['integracao_id'], array('sequencia' => $num_sequencia), TRUE);
        }else{
            $num_sequencia = 1;
        }


        $codigo_revendedor = '0000001';
        $codigo_produto = '0000001';
        $data = date('dmY');
        $num_sequencia = str_pad($num_sequencia,5, '0',STR_PAD_LEFT);

        $file = "{$codigo_revendedor}{$codigo_produto}{$data}{$num_sequencia}.TXT";
        return  $file;
    }

}
if ( ! function_exists('app_integracao_format_file_name_mapfre_ge')) {

    function app_integracao_format_file_name_mapfre_ge($formato, $dados = array())
    {

        if(isset($dados['item']['integracao_id'])){

            $CI =& get_instance();
            $CI->load->model('integracao_model');
            $num_sequencia = (int)$CI->integracao_model->get($dados['item']['integracao_id'])['sequencia'];
            $num_sequencia++;
            $CI->integracao_model->update($dados['item']['integracao_id'], array('sequencia' => $num_sequencia), TRUE);
        }else{
            $num_sequencia = 1;
        }

        $num_produto = "731";
        $nome_estipulante = "SISSOLUCOESINTEGRADAS";
        $data = date('dmY');
        $num_sequencia = str_pad($num_sequencia,4, '0',STR_PAD_LEFT);

        $file = "{$num_produto}{$nome_estipulante}{$data}{$num_sequencia}.TXT";
        return  $file;
    }

}
if ( ! function_exists('app_integracao_format_file_name_generali')) {

    function app_integracao_format_file_name_generali($formato, $dados = array())
    {

        if(isset($dados['item']['integracao_id'])){

            $CI =& get_instance();
            $CI->load->model('integracao_model');
            $num_sequencia = (int)$CI->integracao_model->get($dados['item']['integracao_id'])['sequencia'];
            $num_sequencia++;
            $CI->integracao_model->update($dados['item']['integracao_id'], array('sequencia' => $num_sequencia), TRUE);
        }else{
            $num_sequencia = 1;
        }

        $nome = $formato;
        $data = date('dmYHis');
        $num_sequencia = str_pad($num_sequencia,4, '0',STR_PAD_LEFT);

        $file = "{$nome}{$data}{$num_sequencia}.TXT";
        return  $file;
    }

}
if ( ! function_exists('app_integracao_sequencia_mapfre_rf')) {

    function app_integracao_sequencia_mapfre_rf($formato, $dados = array())
    {

        if(isset($dados['item']['integracao_id'])){

            $CI =& get_instance();
            $CI->load->model('integracao_model');
            $num_sequencia = (int)$CI->integracao_model->get($dados['item']['integracao_id'])['sequencia'];
            $num_sequencia++;
        }else{
            $num_sequencia = 1;
        }

        $num_sequencia = str_pad($num_sequencia,6, '0',STR_PAD_LEFT);

        return $num_sequencia;
    }

}
if ( ! function_exists('app_integracao_generali_dados')) {

    function app_integracao_generali_dados()
    {
        $dados = (object)[
            "email" => "lasa@econnects.com.br",
            "parceiro_id" => 30,
            "produto_parceiro_id" => 57,
            "produto_parceiro_plano_id" => 49,
            "cobertura_plano_id" => 281,
        ];

         if ( empty(app_get_userdata("email")) ) {
            $CI =& get_instance();
            $CI->session->set_userdata("email", $dados->email);
        }

        return $dados;
    }

}
if ( ! function_exists('app_integracao_enriquecimento')) {

    function app_integracao_enriquecimento($formato, $dados = array())
    {
        $ret = ['cpf' => '', 'ean' => ''];
        $cpf = $dados['registro']['cpf'];
        $ean = $dados['registro']['ean'];
        // $ean = "7898058473500"; //****** REMOVER ****
        // $dados['registro']['email'] = "teste@gmail.com"; //****** REMOVER ****

        $acesso = app_integracao_generali_dados();

        if (!empty($cpf)) {
            $cpf = substr($cpf, -11);

            $enriquecido = app_get_api("enriqueceCPF/$cpf/". $acesso->produto_parceiro_id);
            // echo "<pre>";print_r($enriquecido);echo "</pre>";

            if (!empty($enriquecido['status'])){
                $enriquecido = $enriquecido['response'];
                $ret['cpf'] = $enriquecido;

                $dados['registro']['nome'] = $enriquecido->nome;
                $dados['registro']['sexo'] = $enriquecido->sexo;
                $dados['registro']['data_nascimento'] = $enriquecido->data_nascimento;
                $dados['registro']['cnpj_cpf'] = $cpf;

                // Endereço
                $ExtraEnderecos = $enriquecido->endereco;
                if( sizeof( $ExtraEnderecos ) ) {
                    $dados['registro']['endereco'] = $ExtraEnderecos[0]->{"endereco"};
                    $dados['registro']['numero'] = $ExtraEnderecos[0]->{"endereco_numero"};
                    $dados['registro']['complemento'] = $ExtraEnderecos[0]->{"endereco_complemento"};
                    $dados['registro']['bairro'] = $ExtraEnderecos[0]->{"endereco_bairro"};
                    $dados['registro']['cidade'] = $ExtraEnderecos[0]->{"endereco_cidade"};
                    $dados['registro']['uf'] = $ExtraEnderecos[0]->{"endereco_uf"};
                    $dados['registro']['cep'] = str_replace("-", "", $ExtraEnderecos[0]->{"endereco_cep"});
                    $dados['registro']['pais'] = "BRASIL";
                }

                // Contatos
                $ExtraContatos = $enriquecido->contato;
                $getTelefone = $getCelular = $getEmail = true;

                if( sizeof( $ExtraContatos ) ) {
                    foreach ($ExtraContatos as $contato) {
                        if (!$getTelefone && !$getCelular && !$getEmail)
                            break;

                        // Telefone Residencial
                        if ($contato->contato_tipo_id == 3 && $getTelefone ){ //TELEFONE RESIDENCIAL
                            $getTelefone=false;
                            $dados['registro']['ddd_residencial'] = left($contato->contato,2);
                            $dados['registro']['telefone_residencial'] = trim(right($contato->contato, strlen($contato->contato)-2));
                            $dados['registro']['telefone'] = trim($contato->contato);
                            continue;
                        }

                        // Celular
                        if ($contato->contato_tipo_id == 2 && $getCelular ){ // CELULAR
                            $getCelular=false;
                            $dados['registro']['ddd_celular'] = left($contato->contato,2);
                            $dados['registro']['telefone_celular'] = trim(right($contato->contato, strlen($contato->contato)-2));
                            continue;
                        }

                        // Email
                        if ($contato->contato_tipo_id == 1 && $getEmail ){ // CELULAR
                            $getEmail=false;
                            $dados['registro']['email'] = $contato->contato;
                            continue;
                        }
                    }
                }

            }

        }

        if (!empty($ean)) {
            $ean = (int)$ean;

            $enriquecido = app_get_api("enriqueceEAN/$ean");
            // echo "<pre>";print_r($enriquecido);echo "</pre>";

            if (!empty($enriquecido['status'])){
                $enriquecido = $enriquecido['response'];
                $ret['ean'] = $enriquecido;

                $dados['registro']['equipamento_id'] = $enriquecido->equipamento_id;
                $dados['registro']['equipamento_nome'] = $enriquecido->nome;
                $dados['registro']['equipamento_marca_id'] = $enriquecido->equipamento_marca_id;
                $dados['registro']['equipamento_categoria_id'] = $enriquecido->equipamento_categoria_id;
                $dados['registro']['equipamento_sub_categoria_id'] = $enriquecido->equipamento_sub_categoria_id;
                $dados['registro']['imei'] = "";
            }
        }

        // TODO: Validar Regras
        // echo "<pre>";print_r($ret);echo "</pre>";

        return $dados['registro'];
    }

}
if ( ! function_exists('app_get_api'))
{
    function app_get_api($service, $method = 'GET', $fields = [], $print = false){

        $retorno = soap_curl([
            // 'url' => "http://econnects-h.jelastic.saveincloud.net/api/info?doc={$cpf}&produto_parceiro_id={$produto_parceiro_id}",
            // 'url' => "http://localhost/econnects/admin/api/enriqueceCPF/$cpf",
            'url' => "http://localhost/econnects/admin/api/{$service}",
            'method' => $method,
            'fields' => $fields,
            'header' => ["Content-Type: application/json"]
        ]);

        if ($print){
            echo "<pre>";print_r($retorno);echo "</pre>";
            exit();
        }

        $ret = ['status' => false, 'response' => 'Falha na chamada do serviço'];
        $response = (!empty($retorno["response"])) ? json_decode($retorno["response"]) : '';
        if (!empty($retorno["response"])){
            $response = json_decode($retorno["response"]);
            if (empty($response)){
                $ret['response'] = $retorno["response"];
            } else{
                $ret = ['status' => true, 'response' => $response];
            }
        }
        return $ret;
    }
}
if ( ! function_exists('app_integracao_valida_regras'))
{
    function app_integracao_valida_regras($dados){
        return true;
    }
}
if ( ! function_exists('app_integracao_emissao'))
{
    function app_integracao_emissao($format, $dados){
        $dados = $dados['registro'];
        // echo "<pre>";print_r($dados);echo "</pre>";

        if (empty($dados))
            return false;

        $acesso = app_integracao_generali_dados();
        $dados['produto_parceiro_id'] = $acesso->produto_parceiro_id;
        $dados['produto_parceiro_plano_id'] = $acesso->produto_parceiro_plano_id;

        // Emissão
        if ($dados['tipo_transacao'] == 'NS') {

            echo "insere cotacao";
            echo "<pre>";print_r($dados);echo "</pre>";
            $cotacao = app_get_api("insereCotacao", "POST", json_encode($dados));
            echo "<pre>";print_r($cotacao);echo "</pre>";

            if (!empty($cotacao['status'])) {
                $cotacao = $cotacao['response'];

                $cotacao_id = $cotacao->cotacao_id;

                // Formas de Pagamento
                $formPagto = app_get_api("forma_pagamento_cotacao/$cotacao_id");
                echo "<pre>";print_r($formPagto);echo "</pre>";
                if (!empty($formPagto['status'])) {

                    $formPagto = $formPagto['response'];
                    foreach ($formPagto as $fPagto) {

                        if ($fPagto->tipo->slug != 'faturado') 
                            continue;

                        $forma_pagamento_id = $fPagto->pagamento[0]->forma_pagamento_id;
                        $produto_parceiro_pagamento_id = $fPagto->pagamento[0]->produto_parceiro_pagamento_id;
                        
                        $camposPagto = [
                            "cotacao_id" => $cotacao_id,
                            "produto_parceiro_id" => $acesso->produto_parceiro_id,
                            "forma_pagamento_id" => $forma_pagamento_id,
                            "produto_parceiro_pagamento_id" => $produto_parceiro_pagamento_id,
                            "campos" => [],
                        ];

                        $efetuaPagto = app_get_api("pagamento_pagar", "POST", json_encode($camposPagto));
                        echo "<pre>";print_r($efetuaPagto);echo "</pre>";

                        if (!empty($efetuaPagto['status'])) {

                            echo "Pagamento efetuado! <br>";

                        } else {
                            echo "Efetua Pagto Error: ($cotacao_id / $forma_pagamento_id) ". $efetuaPagto['response']."<br>";
                        }

                        break;
                    }

                } else {
                    echo "Formas de Pagto Error: ($cotacao_id) ". $formPagto['response']."<br>";
                }

            } else {
                echo "Cotação Error: ". $cotacao['response']."<br>";
            }



        // Cancelamento
        } else if ( in_array($dados['tipo_transacao'], ['XS','XI','XX']) ) {

        }

        echo "stop";
        exit();
        return true;
    }
}