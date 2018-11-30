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
        $total = isset($dados['global']['totalRegistros']) ? $dados['global']['totalRegistros'] : 0;
        return str_pad($total, $formato, '0', STR_PAD_LEFT);
    }
}

if ( ! function_exists('app_integracao_mapfre_rf_total_itens')) {
    function app_integracao_mapfre_rf_total_itens($formato, $dados = array())
    {
        $total = isset($dados['global']['totalItens']) ? $dados['global']['totalItens'] : 0;
        return str_pad($total, $formato, '0', STR_PAD_LEFT);
    }
}

if ( ! function_exists('app_integracao_mapfre_rf_total_certificado')) {
    function app_integracao_mapfre_rf_total_certificado($formato, $dados = array())
    {
        return str_pad( count($dados['registro']), $formato, '0', STR_PAD_LEFT);
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
        $valor[1] = ((!isset($valor[1])) || (empty(isset($valor[1]))) ) ? '00' : $valor[1];
        return str_pad($valor[0], ($dados['item']['tamanho']-($a[2]+1)), $dados['item']['valor_padrao'], STR_PAD_LEFT) .$a[3]. str_pad($valor[1], $a[2], '0', STR_PAD_LEFT);

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
        if(isset($dados['item']['integracao_id']) && isset($dados['global']['parceiro_id'])){
            $CI =& get_instance();
            $CI->load->model('integracao_model');
            $num_sequencia = (int)$CI->integracao_model->max_seq_by_parceiro_id($dados['global']['parceiro_id']);
            $num_sequencia++;
        }else{
            $num_sequencia = 1;
        }

        $data = date('Ymd');
        $num_sequencia = str_pad($num_sequencia,4, '0',STR_PAD_LEFT);

        $file = "{$formato}-{$num_sequencia}-{$data}.TXT";
        return  $file;
    }

}
if ( ! function_exists('app_integracao_format_file_name_ret_sis')) {

    function app_integracao_format_file_name_ret_sis($formato, $dados = array())
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

        $data = date('Ymd');
        $num_sequencia = str_pad($num_sequencia,4, '0',STR_PAD_LEFT);

        $file = "{$formato}_{$num_sequencia}_{$data}.TXT";
        return  $file;
    }

}
if ( ! function_exists('app_integracao_format_file_name_generali_conciliacao')) {

    function app_integracao_format_file_name_generali_conciliacao($formato, $dados = array())
    {
        return date('Ymd') ."_GBS.TXT";
    }

}
if ( ! function_exists('app_integracao_sequencia_mapfre_rf')) {

    function app_integracao_sequencia_mapfre_rf($formato, $dados = array())
    {
        if(isset($dados['item']['integracao_id']) && isset($dados['global']['parceiro_id'])){
            $CI =& get_instance();
            $CI->load->model('integracao_model');
            $num_sequencia = (int)$CI->integracao_model->max_seq_by_parceiro_id($dados['global']['parceiro_id']);
            $num_sequencia++;
            $CI->integracao_model->update($dados['item']['integracao_id'], array('sequencia' => $num_sequencia), TRUE);
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
        ];

        $CI =& get_instance();
        $CI->session->set_userdata("email", $dados->email);

        $dados->apikey = app_get_token($dados->email);

        return $dados;
    }

}
if ( ! function_exists('app_integracao_enriquecimento')) {

    function app_integracao_enriquecimento($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        $CI =& get_instance();

        if (!empty($formato)) {
            $geraDados = $dados['registro'];
            $geraDados['integracao_log_detalhe_id'] = $formato;

            unset($geraDados['id_log']);
            // unset($geraDados['nota_fiscal_valor_aux']);
            
            $CI->load->model("integracao_log_detalhe_dados_model", "integracao_log_detalhe_dados");
            $CI->integracao_log_detalhe_dados->insLogDetalheDados($geraDados);
        }

        $cpf = $dados['registro']['cpf'];
        $ean = $dados['registro']['ean'];
        $num_apolice = $dados['registro']['num_apolice'];

        echo "****************** CPF: $cpf - {$dados['registro']['tipo_transacao']}<br>";

        if ( !in_array($dados['registro']['tipo_transacao'], ['NS','XS','XX']) ) {
            $response->status = 2;
            return $response;
        }

        if (empty($num_apolice)){
            $response->msg[] = ['id' => 8, 'msg' => "Apólice não recebida no arquivo", 'slug' => "apolice"];
            return $response;
        }

        $acesso = app_integracao_generali_dados();
        $dados['registro']['produto_parceiro_id'] = $acesso->produto_parceiro_id;
        $dados['registro']['produto_parceiro_plano_id'] = $acesso->produto_parceiro_plano_id;
        $eanErro = true;
        $cpfErroMsg = $eanErroMsg = "";


        // usar a pesquisa por nome
        $CI->load->model("apolice_model", "apolice");

        //Busca a apólice pelo número
        $apolice = $CI->apolice->getApoliceByNumero($num_apolice, $acesso->parceiro_id);

        // Emissão
        if ( in_array($dados['registro']['tipo_transacao'], ['NS']) ) {

            if (!empty($apolice)) {
                // $response->msg[] = ['id' => 8, 'msg' => "Apólice já utilizada [{$num_apolice}]", 'slug' => "emissao"];
                $response->status = 2;
                return $response;
            }

            if (!empty($cpf)) $cpf = substr($cpf, -11);

            if( !app_validate_cpf($cpf) ){
                $response->msg[] = ['id' =>  2, 'msg' => "Campo CPF deve ser um CPF válido [{$cpf}]", 'slug' => 'cnpj_cpf'];
                return $response;
            }

            $enriquecido = app_get_api("enriqueceCPF/$cpf/". $acesso->produto_parceiro_id);

            if (!empty($enriquecido['status'])){
                $enriquecido = $enriquecido['response'];
                $response->cpf = $enriquecido;

                $dados['registro']['nome'] = $enriquecido->nome;
                $dados['registro']['sexo'] = $enriquecido->sexo;
                $dados['registro']['data_nascimento'] = $enriquecido->data_nascimento;

                // Endereço
                $ExtraEnderecos = $enriquecido->endereco;
                if( sizeof( $ExtraEnderecos ) ) {
                    $dados['registro']['endereco_logradouro'] = $ExtraEnderecos[0]->{"endereco"};
                    $dados['registro']['endereco_numero'] = $ExtraEnderecos[0]->{"endereco_numero"};
                    $dados['registro']['complemento'] = $ExtraEnderecos[0]->{"endereco_complemento"};
                    $dados['registro']['endereco_bairro'] = $ExtraEnderecos[0]->{"endereco_bairro"};
                    $dados['registro']['endereco_cidade'] = $ExtraEnderecos[0]->{"endereco_cidade"};
                    $dados['registro']['endereco_estado'] = $ExtraEnderecos[0]->{"endereco_uf"};
                    $dados['registro']['endereco_cep'] = str_replace("-", "", $ExtraEnderecos[0]->{"endereco_cep"});
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

            } else {
                $cpfErroMsg = $enriquecido['response'];
            }

            // Regras DE/PARA
            $dados['registro']['cnpj_cpf'] = $cpf;

            
            if (empty($dados['registro']['nome']))
                $dados['registro']['nome'] = "NOME SOBRENOME";

            if (empty($dados['registro']['endereco_estado']))
                $dados['registro']['endereco_estado'] = "SP";

            if (empty($dados['registro']['endereco_cidade']))
                $dados['registro']['endereco_cidade'] = "BARUERI";

            if (empty($dados['registro']['endereco_bairro']))
                $dados['registro']['endereco_bairro'] = $dados['registro']['endereco_cidade'];

            if (empty($dados['registro']['endereco_logradouro']))
                $dados['registro']['endereco_logradouro'] = "ALAMEDA RIO NEGRO";

            if (empty($dados['registro']['endereco_numero']))
                $dados['registro']['endereco_numero'] = '0';

            if (empty($dados['registro']['endereco_cep']))
                $dados['registro']['endereco_cep'] = '06454000';

            if (empty($dados['registro']['data_nascimento'])) {
                $dados['registro']['data_nascimento'] = '2000-01-01';
            } elseif (!app_validate_data_americana($dados['registro']['data_nascimento'])) {
                $dados['registro']['data_nascimento'] = '2000-01-01';
            }

            if (empty($dados['registro']['sexo']))
                $dados['registro']['sexo'] = 'M';


            if (!empty($ean)) {
                $ean = (int)$ean;
                $EANenriquecido = app_get_api("enriqueceEAN/$ean");
                // echo "<pre>";print_r($EANenriquecido);echo "</pre>";

                if (!empty($EANenriquecido['status'])){
                    $EANenriquecido = $EANenriquecido['response'];
                    $response->ean = $EANenriquecido;
                    $eanErro = false;

                    $dados['registro']['equipamento_id'] = $EANenriquecido->equipamento_id;
                    $dados['registro']['equipamento_nome'] = $EANenriquecido->nome;
                    $dados['registro']['equipamento_marca_id'] = $EANenriquecido->equipamento_marca_id;
                    $dados['registro']['equipamento_categoria_id'] = $EANenriquecido->equipamento_categoria_id;
                    $dados['registro']['equipamento_sub_categoria_id'] = $EANenriquecido->equipamento_sub_categoria_id;
                    $dados['registro']['imei'] = "";
                } else {
                    $inputField = [
                        'modelo' => $dados['registro']['equipamento_nome'],
                        'quantidade' => 1,
                        'emailAPI' => app_get_userdata("email"),
                    ];

                    $EANenriquecido = app_get_api("enriqueceModelo", "POST", json_encode($inputField));
                    // echo "<pre>";print_r($EANenriquecido);echo "</pre>";

                    if (!empty($EANenriquecido['status'])){
                        $EANenriquecido = $EANenriquecido['response']->dados[0];
                        $response->ean = $EANenriquecido;
                        $eanErro = false;

                        $dados['registro']['equipamento_id'] = $EANenriquecido->equipamento_id;
                        $dados['registro']['equipamento_nome'] = $EANenriquecido->nome;
                        $dados['registro']['equipamento_marca_id'] = $EANenriquecido->equipamento_marca_id;
                        $dados['registro']['equipamento_categoria_id'] = $EANenriquecido->equipamento_categoria_id;
                        $dados['registro']['equipamento_sub_categoria_id'] = $EANenriquecido->equipamento_sub_categoria_id;
                        $dados['registro']['imei'] = "";
                    } else {
                        $eanErroMsg = "Equipamento não identificado - [{$dados['registro']['equipamento_nome']}]";
                    }

                }
            }

            if ($eanErro){
                echo "<pre>";print_r($EANenriquecido);echo "</pre>";

                $response->msg[] = ['id' => 11, 'msg' => $eanErroMsg ." [{$ean}]", 'slug' => "enriquece_ean"];
                return $response;
            }
        
        // Cancelamento
        } else if ( in_array($dados['registro']['tipo_transacao'], ['XS','XX']) ) {

            if (empty($apolice)) {
                $response->msg[] = ['id' => 8, 'msg' => "Apólice não encontrada [{$num_apolice}]", 'slug' => "cancelamento"];
                return $response;
            } else {
                $apolice = $apolice[0];
                if ($apolice['apolice_status_id'] != 1) {
                    $response->msg[] = ['id' => 8, 'msg' => "Apólice {$num_apolice} já está Cancelada e/ou em um status inválido [{$apolice['nome']}]", 'slug' => "cancelamento"];
                    return $response;
                }

                $dados['registro']['apolice_id'] = $apolice['apolice_id'];
            }

        }

        // Campos para cotação
        $camposCotacao = app_get_api("cotacao_campos/". $acesso->produto_parceiro_id);
        if (empty($camposCotacao['status'])){
            $response->msg[] = ['id' => -1, 'msg' => $camposCotacao['response'], 'slug' => "cotacao_campos"];
            return $response;
        }

        $camposCotacao = $camposCotacao['response'];

        // Validar Regras
        $validaRegra = app_integracao_valida_regras($dados, $camposCotacao);
        // echo "<pre>";print_r($validaRegra);echo "</pre>";

        if (!empty($validaRegra->status)) {
            $dados['registro']['cotacao_id'] = !empty($validaRegra->cotacao_id) ? $validaRegra->cotacao_id : 0;
            $dados['registro']['fields'] = $validaRegra->fields;
            $emissao = app_integracao_emissao($formato, $dados);

            if (empty($emissao->status)) {
                $response->msg = $emissao->msg;
            } else {
                $response->status = true;
            }

        } else {
            if (!empty($response->msg)) {
                $response->msg = array_merge($validaRegra->errors, $response->msg);
            } else {
                $response->msg = $validaRegra->errors;
            }
        }

        return $response;
    }
}
if ( ! function_exists('app_get_api'))
{
    function app_get_api($service, $method = 'GET', $fields = [], $print = false){

        $acesso = app_integracao_generali_dados();

        $CI =& get_instance();
        $CI->session->set_userdata("email", $acesso->email);

        $url = $CI->config->item("URL_sisconnects") ."admin/api/{$service}";
        $header = ["Content-Type: application/json", "APIKEY: {$acesso->apikey}"];

        $CI->load->model('integracao_log_detalhe_api_model', 'integracao_log_detalhe_api');
        $insertArray = [
            'service' => $service, 
            'method' => $method, 
            'url' => $url, 
            'fields' => addslashes(print_r($fields, true)), 
            'header' => addslashes(print_r($header, true)),
            'status' => 0,
        ];
        $integracao_log_detalhe_api_id = $CI->integracao_log_detalhe_api->geraLogApiFail($insertArray);

        $retorno = soap_curl([
            'url' => $url,
            'method' => $method,
            'fields' => $fields,
            'header' => $header
        ]);

        if ($print){
            echo "<pre>";print_r($retorno);echo "</pre>";
            exit();
        }

        $ret = ['status' => false, 'response' => "Falha na chamada do serviço ($service)", 'ret' => $retorno];
        $response = (!empty($retorno["response"])) ? json_decode($retorno["response"]) : '';
        if (!empty($retorno["response"])){
            $response = json_decode($retorno["response"]);
            if (empty($response)){
                $ret['response'] = $retorno["response"];
            } else{
                $ret = ['status' => true, 'response' => $response];
            }
        }

        $dataApi = [
            'status' => $ret['status'],
            'response' => addslashes(print_r($retorno, true)),
            'retorno_amigavel' => addslashes(print_r($ret['response'], true)),
        ];
        $CI->integracao_log_detalhe_api->update($integracao_log_detalhe_api_id, $dataApi, TRUE);

        return $ret;
    }
}
if ( ! function_exists('app_integracao_valida_regras'))
{
    function app_integracao_valida_regras($dados, $camposCotacao){

        $response = (object) ['status' => false, 'msg' => '', 'errors' => [], 'fields' => []];

        if (empty($dados['registro'])){
            $response->msg = 'Nenhum dado recebido para validação';
            return $response;
        }

        $dados = $dados['registro'];
        // echo "<pre>";print_r($dados);echo "</pre>";

        // Emissão
        if ( in_array($dados['tipo_transacao'], ['NS']) ) {

            $errors = $fields = [];

            $now = new DateTime(date('Y-m-d'));

            // VIGÊNCIA
            if (empty($dados["nota_fiscal_data"])){
                $errors[] = ['id' => 4, 'msg' => "Campo DATA VENDA OU CANCELAMENTO deve ser obrigatório", 'slug' => "nota_fiscal_data"];
            } else {
                $d1 = new DateTime($dados["nota_fiscal_data"]);
                $d1->add(new DateInterval('P1D')); // Início de Vigência: A partir das 24h do dia em que o produto foi adquirido
                $dados["data_inicio_vigencia"] = $d1->format('Y-m-d');

                // Período de Vigência: 12 meses
                $diff = $now->diff($d1);
                if ($diff->m >= 12 && $diff->d > 0) {
                    $errors[] = ['id' => 5, 'msg' => "Campo DATA VENDA OU CANCELAMENTO deve ser inferior ou igual à 12 meses", 'slug' => "nota_fiscal_data"];
                }
            }

            // IDADE - Pessoa física maior de 18 anos
            // E-mail Patini - 28 de nov de 2018 17:19
            // if (!empty($dados["data_nascimento"]) && $dados["data_nascimento"] != '2000-01-01'){
            //     $d1 = new DateTime($dados["data_nascimento"]);

            //     $diff = $now->diff($d1);
            //     if ($diff->y < 18) {
            //         $errors[] = ['id' => 6, 'msg' => "A DATA DE NASCIMENTO deve ser igual ou superior à 18 anos", 'slug' => "data_nascimento"];
            //     }
            // }

            // Valida campos obrigatórios na cotação
            foreach ($camposCotacao as $TipoCampos) {
                foreach ($TipoCampos->campos as $campo) {

                    // echo $campo->label . " : ". $campo->nome_banco ." > ". $campo->validacoes ."<br>";
                    $fields[$campo->nome_banco] = isset($dados[$campo->nome_banco]) ? $dados[$campo->nome_banco] : '';
                    if (empty($campo->validacoes)) continue;

                    $validacoes = explode("|", $campo->validacoes);
                    foreach ($validacoes as $val) {

                        switch ($val) {
                            case "required":
                                if( !isset( $dados[$campo->nome_banco] ) || ( empty( trim($dados[$campo->nome_banco]) ) && $dados[$campo->nome_banco] != '0') )
                                    $errors[] = ['id' => 1, 'msg' => "Campo {$campo->label} é obrigatório", 'slug' => $campo->nome_banco];
                                break;
                            case "validate_cpf":
                                if( !empty($dados[$campo->nome_banco]) && !app_validate_cpf($dados[$campo->nome_banco]) )
                                    $errors[] = ['id' => 2, 'msg' => "Campo {$campo->label} deve ser um CPF válido [{$dados[$campo->nome_banco]}]", 'slug' => $campo->nome_banco];
                                break;
                            case "validate_data":
                                if( !empty($dados[$campo->nome_banco]) && !app_validate_data_americana($dados[$campo->nome_banco]) )
                                    $errors[] = ['id' => 3, 'msg' => "Campo {$campo->label} deve ser uma Data válida [{$dados[$campo->nome_banco]}]", 'slug' => $campo->nome_banco];
                                break;
                        }
                    }

                }
            }

            if (empty($errors)) {

                $fields['produto_parceiro_id'] = $dados['produto_parceiro_id'];
                $fields['produto_parceiro_plano_id'] = $dados['produto_parceiro_plano_id'];
                $fields['equipamento_nome'] = $dados['equipamento_nome'];
                if (!empty($dados['equipamento_marca_id']))
                    $fields['equipamento_marca_id'] = $dados['equipamento_marca_id'];
                if (!empty($dados['equipamento_categoria_id']))
                $fields['equipamento_categoria_id'] = $dados['equipamento_categoria_id'];
                $fields['ean'] = $dados['ean'];
                $fields['emailAPI'] = app_get_userdata("email");

                // Cotação
                $cotacao = app_get_api("insereCotacao", "POST", json_encode($fields));
                if (empty($cotacao['status'])) {
                    $response->errors = ['id' => -1, 'msg' => $cotacao['response'], 'slug' => "insere_cotacao"];
                    return $response;
                }

                $cotacao = $cotacao['response'];
                $cotacao_id = $cotacao->cotacao_id;
                $response->cotacao_id = $cotacao_id;

                // Cálculo do prêmio
                $calcPremio = app_get_api("calculo_premio/". $cotacao_id);
                if (empty($calcPremio['status'])){
                    $response->errors = ['id' => -1, 'msg' => $calcPremio['response'], 'slug' => "calcula_premio"];
                    return $response;
                }

                $calcPremio = $calcPremio['response'];
                $valor_premio = $calcPremio->premio_liquido_total;
                $premioValid = true;

                if ($valor_premio != $dados["premio_liquido"]) {
                    $pb = (float)$dados["premio_liquido"];
                    $is = (float)$dados["nota_fiscal_valor"];
                    $premioValid = false;

                    if ($is == 0) {
                        $percent = 0;
                    } else {
                        $percent = $pb / $is * 100;

                        // E-mail do Daniel Patini - 28 de nov de 2018 17:19

                        // Arredondamento do percentual (a.)
                        if ($percent >= 24.9 && $percent <= 25.99999999999999) {
                            $premioValid = true;
                        }

                        // Taxa era praticada com a MAPFRE (b.)
                        // if ($percent == 23) {
                        //     $premioValid = true;
                        // }

                        // Taxa era praticada com a MAPFRE para tablets (c.)
                        // if ($percent == 19) {
                        //     $premioValid = true;
                        // }
                    }

                }
                
                // // Se houve falha no premio, faz a validação pelo valor de nf
                // if (!$premioValid) {
                //     $is = (float)$dados["nota_fiscal_valor_aux"];

                //     if ($is == 0) {
                //         $percent = 0;
                //     } else {
                //         $percent = $pb / $is * 100;

                //         if ($percent >= 24.9 && $percent <= 25.99999999999999) {
                //             $premioValid = true;
                //         }

                //         if ($percent == 23) {
                //             $premioValid = true;
                //         }

                //         if ($percent == 19) {
                //             $premioValid = true;
                //         }
                //     }
                // }

                if (!$premioValid) {
                    $errors[] = ['id' => 7, 'msg' => "Valor do prêmio bruto [". $dados["premio_liquido"] ."] difere do prêmio calculado [". $valor_premio ."]", 'slug' => "premio_liquido"];
                }

                $response->fields = $fields;
            }

            if (!empty($errors)) {
                $response->errors = $errors;
                return $response;
            }

        }

        $response->status = true;
        return $response;
    }
}
if ( ! function_exists('app_integracao_emissao'))
{
    function app_integracao_emissao($format, $dados){
        $response = (object) ['status' => false, 'msg' => '', 'pedido_id' => 0];

        if (empty($dados['registro'])){
            $response->msg = 'Nenhum dado recebido para validação';
            return $response;
        }

        $dados = $dados['registro'];
        $cotacao_id = $dados["cotacao_id"];
        $num_apolice = $dados["num_apolice"];
        $fields = $dados["fields"];
        $fields['cotacao_id'] = $cotacao_id;
        // echo "<pre>";print_r($fields);echo "</pre>";

        // Emissão
        if ( in_array($dados['tipo_transacao'], ['NS']) ) {

            // Cotação Contratar
            $fields['emailAPI'] = app_get_userdata("email");
            $cotacao = app_get_api("cotacao_contratar", "POST", json_encode($fields));
            if (empty($cotacao['status'])) {
                $response->msg[] = ['id' => -1, 'msg' => $cotacao['response'], 'slug' => "cotacao_contratar"];
                return $response;
            }

            // Formas de Pagamento
            $formPagto = app_get_api("forma_pagamento_cotacao/$cotacao_id");
            if (empty($formPagto['status'])) {
                $response->msg[] = ['id' => -1, 'msg' => $formPagto['response'], 'slug' => "forma_pagamento_cotacao"];
                return $response;
            }

            $formPagto = $formPagto['response'];
            if (empty($formPagto)) {
                $response->msg[] = ['id' => -1, 'msg' => 'Nenhum Meio de Pagamento encontrado', 'slug' => "forma_pagamento_cotacao"];
                return $response;
            }

            foreach ($formPagto as $fPagto) {

                if ($fPagto->tipo->slug != 'cobranca_terceiros') 
                    continue;

                $forma_pagamento_id = $fPagto->pagamento[0]->forma_pagamento_id;
                $produto_parceiro_pagamento_id = $fPagto->pagamento[0]->produto_parceiro_pagamento_id;

                $camposPagto = [
                    "cotacao_id" => $cotacao_id,
                    "produto_parceiro_id" => $dados["produto_parceiro_id"],
                    "forma_pagamento_id" => $forma_pagamento_id,
                    "produto_parceiro_pagamento_id" => $produto_parceiro_pagamento_id,
                    "campos" => [],
                    "emailAPI" => app_get_userdata("email"),
                ];

                $efetuaPagto = app_get_api("pagamento_pagar", "POST", json_encode($camposPagto));
                if (empty($efetuaPagto['status'])) {
                    $response->msg[] = ['id' => -1, 'msg' => $efetuaPagto['response'], 'slug' => "pagamento_pagar"];
                    return $response;
                }

                $response->msg = $efetuaPagto['response']->mensagem;
                $response->pedido_id = $efetuaPagto['response']->dados->pedido_id;
                $response->apolice_id = $efetuaPagto['response']->dados->apolice_id;

                // método para salvar a apólice
                $fieldApolice = [
                    "apolice_id" => $response->apolice_id,
                    "num_apolice" => $num_apolice,
                    "emailAPI" => app_get_userdata("email"),
                ];

                $getApolice = app_get_api("apolice", "POST", json_encode($fieldApolice));
                if (empty($getApolice['status'])) {
                    $response->msg[] = ['id' => -1, 'msg' => $getApolice['response'], 'slug' => "apolice_get"];
                    return $response;
                }

                break;
            }

            if (empty($response->pedido_id)) {
                $response->msg[] = ['id' => -1, 'msg' => "Meio de Pagamento não configurado", 'slug' => "forma_pagamento_cotacao"];
                return $response;
            }

        // Cancelamento
        } else if ( in_array($dados['tipo_transacao'], ['XS','XX']) ) {
            
            // Cancelamento
            $cancelaApolice = app_get_api("cancelar", "POST", json_encode(["apolice_id" => $dados['apolice_id'], "emailAPI" => app_get_userdata("email")]));
            if (empty($cancelaApolice['status'])) {
                $response->msg[] = ['id' => 9, 'msg' => $cancelaApolice['response'], 'slug' => "cancelamento"];
                return $response;
            }

        }

        $response->status = true;
        return $response;
    }
}
if ( ! function_exists('app_integracao_apolice')) {
    function app_integracao_apolice($formato, $dados = array())
    {
        $num_apolice = $dados['registro']['num_apolice'];
        if (!is_numeric($num_apolice))
            return $num_apolice;

        $num_apolice_aux = $dados['registro']['cod_sucursal'] . $dados['registro']['cod_ramo'] . $dados['registro']['cod_tpa'];
        $num_apolice_aux .= str_pad(substr($num_apolice, 7, 8), 8, '0', STR_PAD_LEFT);

        return $num_apolice_aux;
    }
}
if ( ! function_exists('app_integracao_apolice_revert')) {
    function app_integracao_apolice_revert($formato, $dados = array())
    {
        $num_apolice = $dados['valor'];
        return "7840001".right($num_apolice, 8);
    }
}
if ( ! function_exists('app_integracao_id_transacao')) {
    function app_integracao_id_transacao($formato, $dados = array())
    {
        $num_apolice = app_integracao_apolice($formato, $dados);
        $num_apolice .= $dados['registro']['num_endosso'].$dados['registro']['cod_ramo'].$dados['registro']['num_parcela'];
        return $num_apolice;
    }
}
if ( ! function_exists('app_integracao_id_transacao_canc')) {
    function app_integracao_id_transacao_canc($formato, $dados = array())
    {
        $id_transacao = '';
        if ($dados['registro']['cod_motivo_cobranca'] == '02') {
            $id_transacao = app_integracao_apolice($formato, $dados);
            $id_transacao .= $dados['registro']['num_endosso'].$dados['registro']['cod_ramo'].$dados['registro']['num_parcela'];
        }
        return $id_transacao;
    }
}
if ( ! function_exists('app_integracao_retorno_generali_fail')) {
    function app_integracao_retorno_generali_fail($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => []];
        // echo "<pre>";print_r($dados['registro']);

        if (!isset($dados['log']['nome_arquivo']) || empty($dados['log']['nome_arquivo'])) {
            $response->msg[] = ['id' => 12, 'msg' => 'Nome do Arquivo inválido', 'slug' => "erro_interno"];
            return $response;
        }

        $file = str_replace("-RT-", "-EV-", $dados['log']['nome_arquivo']);
        $result_file = explode("-", $file);
        $file = $result_file[0]."-".$result_file[1]."-".$result_file[2]."-";

        $tipo_file = explode(".", $result_file[0]);
        $tipo_file = $tipo_file[2];

        $chave = '';
        switch ($tipo_file) {
            case 'CLIENTE':
                $chave = !empty($dados['registro']['cod_cliente']) ? (int)$dados['registro']['cod_cliente'] : '';
                break;
            case 'PARCEMS':
            case 'EMSCMS':
            case 'LCTCMS':
            case 'COBRANCA':
                $chave = !empty($dados['registro']['num_apolice']) ? trim($dados['registro']['num_apolice']) ."|" : '';
                break;
            case 'SINISTRO':
                // $chave = !empty($dados['registro']['cod_sinistro']) ? (int)$dados['registro']['cod_sinistro'] ."|". (int)$dados['registro']['cod_movimento'] : '';
                $chave = !empty($dados['registro']['cod_sinistro']) ? (int)$dados['registro']['cod_sinistro'] .'|' : '';
                break;
        }

        // echo "chave = $chave<br>";
        if (empty($chave)) {
            $response->msg[] = ['id' => 12, 'msg' => 'Chave não identificada', 'slug' => "erro_interno"];
            return $response;
        }

        // LIBERA TODOS OS QUE NAO FORAM LIDOS COMO ERRO E OS AINDA NAO FORAM LIBERADOS
        $CI =& get_instance();
        $CI->load->model('integracao_model');
        $CI->integracao_model->update_log_fail($file, $chave);

        $response->msg[] = ['id' => 12, 'msg' => $dados['registro']['cod_erro'] ." - ". $dados['registro']['descricao_erro'], 'slug' => "erro_retorno"];

        return $response;
    }
}
if ( ! function_exists('app_integracao_retorno_generali_success')) {
    function app_integracao_retorno_generali_success($formato, $dados = array())
    {

        if (!isset($dados['log']['nome_arquivo']) || empty($dados['log']['nome_arquivo'])) {
            return false;
        }

        $file = str_replace("-RT-", "-EV-", $dados['log']['nome_arquivo']);
        $result_file = explode("-", $file);
        $tipo_file = explode(".", $result_file[0]);
        $file = $result_file[0]."-".$result_file[1]."-".$result_file[2]."-";
        $sinistro = ($tipo_file[2] == 'SINISTRO');

        // LIBERA TODOS OS QUE NAO FORAM LIDOS COMO ERRO E OS AINDA NAO FORAM LIBERADOS
        $CI =& get_instance();
        $CI->load->model('integracao_model');
        $CI->integracao_model->update_log_sucess($file, $sinistro);

        return true;
    }
}
if ( ! function_exists('app_integracao_generali_sinistro')) {
    function app_integracao_generali_sinistro($formato, $dados = array())
    {
        $d = $dados['registro'];
        $integracao_log_detalhe_id = $formato;
        $valor = $d['vlr_movimento'];
        // Ajuste a menor
        if ($d['cod_tipo_mov'] == '2') {
            $valor *= -1;
        }

        $CI =& get_instance();
        $CI->db->query("INSERT INTO sissolucoes1.sis_exp_hist_carga (id_exp, data_envio, tipo_expediente, id_controle_arquivo_registros, valor) VALUES ({$d['id_exp']}, NOW(), '{$d['tipo_expediente']}', '{$integracao_log_detalhe_id}', {$valor}) ");
        $id_exp_hist_carga = $CI->db->insert_id();

        if ($d['tipo_expediente'] == 'ABE') {
            $q = $CI->db->query("SELECT id_exp FROM sissolucoes1.sis_exp_complemento WHERE id_exp = {$d['id_exp']}");
            if (empty($q->num_rows())) {
                $CI->db->query("INSERT INTO sissolucoes1.sis_exp_complemento (id_exp, id_sinistro_generali, id_usuario, dt_log, vcmotivolog) VALUES ({$d['id_exp']}, '{$d['cod_sinistro']}', 10058, NOW(), '{$d['desc_expediente']}') ");
            } else {
                $CI->db->query("UPDATE sissolucoes1.sis_exp_complemento SET id_sinistro_generali = '{$d['cod_sinistro']}', id_usuario = 10058, dt_log = NOW(), vcmotivolog = '{$d['desc_expediente']}' WHERE id_exp = {$d['id_exp']}");
            }
        }

        return $id_exp_hist_carga;
    }
}
