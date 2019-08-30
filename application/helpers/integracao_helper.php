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

if ( ! function_exists('app_integracao_generali_total_itens')) {
    function app_integracao_generali_total_itens($formato, $dados = array())
    {
        $total = isset($dados['global']['totalItens']) ? $dados['global']['totalItens'] : 0;
        return str_pad($total, $formato, '0', STR_PAD_LEFT);
    }
}

if ( ! function_exists('app_integracao_mapfre_rf_total_itens')) {
    function app_integracao_mapfre_rf_total_itens($formato, $dados = array())
    {
        $total = count($dados['registro']) + 2;
        $total = issetor($total, 0);
        return str_pad($total, $formato, '0', STR_PAD_LEFT);
    }
}

if ( ! function_exists('app_integracao_mapfre_rf_total_registro')) {
    function app_integracao_mapfre_rf_total_registro($formato, $dados = array())
    {
        $total = isset($dados['global']['totalRegistros']) ? $dados['global']['totalRegistros'] : 0;
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
        // $a = explode(",", $formato);
        // $valor = $dados[$a[0]][$a[1]];
        // $valor = ($valor == 0) ? '0.0' : $valor;
        // $valor = explode('.', $valor);
        // $valor[1] = ((isset($valor[1])) || (empty(isset($valor[1]))) ) ? '00' : $valor[1];
        // return str_pad($valor[0],  ($a[2] -8), '0', STR_PAD_LEFT) . str_pad($valor[1], 8, '0', STR_PAD_LEFT);

        list( $name , $code , $casa , $separador ) = explode("|",$formato);

        if (empty($dados[ $name][ $code ])) {
                $dados[ $name][ $code ] = 0;
        }

        $valor = number_format( $dados[ $name][ $code ] , $casa , $separador , "" );
        $valor = explode($separador, $valor);
        return str_pad($valor[0], ($dados['item']['tamanho']-($casa+1)), $dados['item']['valor_padrao'], STR_PAD_LEFT) .$separador. $valor[1];
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
        $valor[0] = preg_replace("/[^0-9]/", "", $valor[0]);
        return str_pad($valor[0], ($dados['item']['tamanho']-($a[2]+1)), $dados['item']['valor_padrao'], STR_PAD_LEFT) .$a[3]. str_pad($valor[1], $a[2], '0', STR_PAD_RIGHT);

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
        if( isset( $dados['valor'] ) && !empty(trim($dados['valor'])) ){
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
if ( ! function_exists('app_integracao_file_name_sulacap')) {

    function app_integracao_file_name_sulacap($formato, $dados = array())
    {
        $data = date('dmy');
        $file = "{$formato}{$data}.txt";
        return $file;
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
if ( ! function_exists('app_integracao_format_file_name_novo_mundo')) {

    function app_integracao_format_file_name_novo_mundo($formato, $dados = array())
    {
        if ( empty($dados['registro'][0]['nome_arquivo']) ) {
            return '';
        }

        $file = str_replace(".REM", ".RET", $dados['registro'][0]['nome_arquivo']);
        return  $file;
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

    function app_integracao_generali_dados($data = [])
    {
        $operacao = app_get_userdata("operacao");

        if ( !empty($data) )
        {
            $dados = (object)$data;

        } elseif ( $operacao == 'lasa') {
            $dados = (object)[
                "email" => "lasa@econnects.com.br",
                "parceiro_id" => 30,
                "produto_parceiro_id" => 57,
                "produto_parceiro_plano_id" => 49,
            ];
        }

        $CI =& get_instance();
        $CI->session->set_userdata("email", $dados->email); // importante setar o e-mail antes de recuperar a apikey
        $dados->apikey = app_get_token($dados->email);
        $dados->parceiro = $operacao;
        $CI->session->set_userdata("apikey", $dados->apikey);
        return $dados;
    }

}
if ( ! function_exists('app_integracao_enriquecimento')) {

    function app_integracao_enriquecimento($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        $CI =& get_instance();
        $CI->session->sess_destroy();
        $CI->session->set_userdata("operacao", "lasa");

        if (!empty($formato)) {

            $geraDados['tipo_transacao']            = $dados['registro']['tipo_transacao'];
            $geraDados['cod_loja']                  = $dados['registro']['cod_loja'];
            $geraDados['num_apolice']               = $dados['registro']['num_apolice'];
            $geraDados['data_adesao_cancel']        = $dados['registro']['data_adesao_cancel'];
            $geraDados['telefone']                  = $dados['registro']['telefone'];
            $geraDados['endereco_logradouro']       = $dados['registro']['endereco_logradouro'];
            $geraDados['sexo']                      = $dados['registro']['sexo'];
            $geraDados['cod_produto_sap']           = $dados['registro']['cod_produto_sap'];
            $geraDados['ean']                       = $dados['registro']['ean'];
            $geraDados['marca']                     = $dados['registro']['marca'];
            $geraDados['equipamento_nome']          = $dados['registro']['equipamento_nome'];
            $geraDados['nota_fiscal_valor_desc']    = $dados['registro']['nota_fiscal_valor_desc'];
            $geraDados['nota_fiscal_data']          = $dados['registro']['nota_fiscal_data'];
            $geraDados['premio_liquido']            = $dados['registro']['premio_bruto'];
            $geraDados['premio_bruto']              = $dados['registro']['premio_bruto'];
            $geraDados['vigencia']                  = $dados['registro']['vigencia'];
            $geraDados['cod_vendedor']              = $dados['registro']['cod_vendedor'];
            $geraDados['cpf']                       = $dados['registro']['cpf'];
            $geraDados['nota_fiscal_numero']        = $dados['registro']['nota_fiscal_numero'];
            $geraDados['num_parcela']               = $dados['registro']['num_parcela'];
            $geraDados['nota_fiscal_valor']         = $dados['registro']['nota_fiscal_valor'];

            // Emissão
            if ( in_array($dados['registro']['tipo_transacao'], ['XS','XX','XD']) ) {
                $geraDados['data_cancelamento'] = $dados['registro']['data_adesao_cancel'];
            }

            $geraDados['integracao_log_detalhe_id'] = $formato;

            $CI->load->model("integracao_log_detalhe_dados_model", "integracao_log_detalhe_dados");
            $CI->integracao_log_detalhe_dados->insLogDetalheDados($geraDados);
        }

        $cpf = $dados['registro']['cpf'];
        $ean = $dados['registro']['ean'];
        $num_apolice = $dados['registro']['num_apolice'];

        echo "****************** CPF: $cpf - {$dados['registro']['tipo_transacao']}<br>";

        // Emissão
        if ( in_array($dados['registro']['tipo_transacao'], ['NS','XP']) )
        {
            $dados['registro']['acao']        = '1';
            $dados['registro']['data_adesao'] = $dados['registro']['data_adesao_cancel'];
        // Cancelamento
        } else if ( in_array($dados['registro']['tipo_transacao'], ['XS','XX','XD']) )
        {
            $dados['registro']['acao']              = '9';
            $dados['registro']['data_cancelamento'] = $dados['registro']['data_adesao_cancel'];
        } else {

            // XI = Cancelamento por Inadimplência
            switch ($dados['registro']['tipo_transacao']) {
                case 'XI':
                    $integracao_log_detalhe_erro_id = 13;
                    break;
                default:
                    $integracao_log_detalhe_erro_id = 14;
                    break;
            }
            $response->msg[] = ['id' => $integracao_log_detalhe_erro_id, 'msg' => "Registro recebido como {$dados['registro']['tipo_transacao']}", 'slug' => "ignorado"];
            return $response;

        }

        $acesso = app_integracao_generali_dados();
        $dados['registro']['produto_parceiro_id'] = $acesso->produto_parceiro_id;
        $dados['registro']['produto_parceiro_plano_id'] = $acesso->produto_parceiro_plano_id;
        $eanErro = true;
        $eanErroMsg = "";

        // validações iniciais
        $valid = app_integracao_inicio($acesso->parceiro_id, $num_apolice, $cpf, $ean, $dados, $acesso);
        if ( $valid->status !== true ) {
            $response = $valid;
            return $response;
        }

        // Campos para cotação
        $camposCotacao = app_get_api("cotacao_campos/". $acesso->produto_parceiro_id, 'GET', [], $acesso);
        if (empty($camposCotacao['status'])){
            $response->msg[] = ['id' => -1, 'msg' => $camposCotacao['response'], 'slug' => "cotacao_campos"];
            return $response;
        }

        $camposCotacao = $camposCotacao['response'];

        // Validar Regras
        $validaRegra = app_integracao_valida_regras($dados, $camposCotacao, true, $acesso);
        // echo "<pre>";print_r($validaRegra);echo "</pre>";

        if (!empty($validaRegra->status)) {
            $dados['registro']['cotacao_id'] = !empty($validaRegra->cotacao_id) ? $validaRegra->cotacao_id : 0;
            $dados['registro']['fields'] = $validaRegra->fields;
            $emissao = app_integracao_emissao($formato, $dados, $acesso);

            if (empty($emissao->status)) {

                if ( !empty($emissao->msg) ) {

                    if ( !is_array($emissao->msg) ) {
                        $response->msg[] = $emissao->msg;
                    } else {
                        $response->msg = $emissao->msg;
                    }

                } else {
                    $response->msg = $emissao->errors;
                }

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
    function app_get_api($service, $method = 'GET', $fields = [], $acesso = null)
    {

        $CI =& get_instance();
        $apikey = empty($acesso) ? $CI->session->userdata("apikey") : $acesso->apikey;
        $url = $CI->config->item("URL_sisconnects") ."admin/api/{$service}";
        $header = ["Content-Type: application/json", "APIKEY: {$apikey}"];

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

        $ret = ['status' => false, 'response' => "Falha na chamada do serviço ($service)", 'ret' => $retorno];
        $response = (!empty($retorno["response"])) ? json_decode($retorno["response"]) : '';
        if (!empty($retorno["response"])){
            $response = json_decode($retorno["response"]);
            if (empty($response)){
                $ret['response'] = $retorno["response"];
            } else{
                $ret = ['status' => isset($response->status) ? $response->status : true, 'response' => $response];
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
    function app_integracao_valida_regras($dados, $camposCotacao, $enriqueceCPF = true, $acesso = null){

        $response = (object) ['status' => false, 'msg' => '', 'errors' => [], 'fields' => []];

        if (empty($dados['registro'])){
            $response->msg = 'Nenhum dado recebido para validação';
            return $response;
        }

        $dados = $dados['registro'];
        // echo "<pre>";print_r($dados);echo "</pre>";

        // Emissão
        if ( $dados['acao'] == '1' ) {

            $errors = $fields = [];
            $now = new DateTime(date('Y-m-d'));

            // VIGÊNCIA
            if (empty($dados["data_adesao"])){
                $errors[] = ['id' => 4, 'msg' => "Campo DATA DA VENDA deve ser obrigatório", 'slug' => "data_adesao"];
            } else {
                if ( empty($dados["data_inicio_vigencia"]) ) {

                    $d1 = new DateTime($dados["data_adesao"]);
                    $d2 = $d1->format('Y-m-d');
                    $d1->add(new DateInterval('P1D')); // Início de Vigência: A partir das 24h do dia em que o produto foi adquirido
                    $dados["data_inicio_vigencia"] = $d1->format('Y-m-d');

                    // Período de Vigência: 12 meses
                    $diff = $now->diff($d1);
                    if ($d2 < date("Y-m-d", strtotime("-1 year"))) {
                        $errors[] = ['id' => 5, 'msg' => "Campo DATA VENDA OU CANCELAMENTO deve ser inferior ou igual à 12 meses", 'slug' => "data_inicio_vigencia"];
                    }
                }
            }

            // Enriquecimento do CPF
            $cpf = substr($dados['cpf'], -11);
            $dados['cnpj_cpf'] = $cpf;

            if ($enriqueceCPF)
            {
                $enriquecido = app_get_api("enriqueceCPF/$cpf/". $dados['produto_parceiro_id'], 'GET', [], $acesso);

                if (!empty($enriquecido['status'])){
                    $enriquecido = $enriquecido['response'];

                    $dados['nome'] = emptyor($dados['nome'], $enriquecido->nome);
                    $dados['sexo'] = emptyor($dados['sexo'], $enriquecido->sexo);
                    $dados['data_nascimento'] = emptyor($dados['data_nascimento'], $enriquecido->data_nascimento);

                    // Endereço
                    $ExtraEnderecos = $enriquecido->endereco;
                    if( sizeof( $ExtraEnderecos ) ) {
                        $rank=1000;
                        $index=0;

                        foreach ($ExtraEnderecos as $end) {
                            $rank = ($end->ranking <= $rank) ? $index : $rank;
                            $index++;
                        }

                        $dados['endereco_logradouro'] = emptyor($dados['endereco_logradouro'], $ExtraEnderecos[$rank]->{"endereco"});
                        $dados['endereco_numero'] = emptyor($dados['endereco_numero'], $ExtraEnderecos[$rank]->{"endereco_numero"});
                        $dados['complemento'] = emptyor($dados['complemento'], $ExtraEnderecos[$rank]->{"endereco_complemento"});
                        $dados['endereco_bairro'] = emptyor($dados['endereco_bairro'], $ExtraEnderecos[$rank]->{"endereco_bairro"});
                        $dados['endereco_cidade'] = emptyor($dados['endereco_cidade'], $ExtraEnderecos[$rank]->{"endereco_cidade"});
                        $dados['endereco_estado'] = emptyor($dados['endereco_estado'], $ExtraEnderecos[$rank]->{"endereco_uf"});
                        $dados['endereco_cep'] = emptyor($dados['endereco_cep'], str_replace("-", "", $ExtraEnderecos[$rank]->{"endereco_cep"}));
                        $dados['pais'] = "BRASIL";
                    }

                    $ExtraContatos = $enriquecido->contato;
                    if( sizeof( $ExtraContatos ) ) {
                        $rankTel=$rankCel=$rankEmail=1000;
                        $index=0;

                        // Valida o ranking
                        foreach ($ExtraContatos as $cont) {
                            switch ($cont->contato_tipo_id) {
                                // Telefone Residencial
                                case 3:
                                    $rankTel = ($cont->ranking <= $rankTel) ? $index : $rankTel;
                                    break;
                                
                                // Celular
                                case 2:
                                    $rankCel = ($cont->ranking <= $rankCel) ? $index : $rankCel;
                                    break;

                                // Email
                                case 1:
                                    $rankEmail = ($cont->ranking <= $rankEmail) ? $index : $rankEmail;
                                    break;
                            }
                            $index++;
                        }

                        // Telefone Residencial
                        if ($rankTel != 1000) {
                            $dados['ddd_residencial'] = left($ExtraContatos[$rankTel]->contato,2);
                            $dados['telefone_residencial'] = trim(right($ExtraContatos[$rankTel]->contato, strlen($ExtraContatos[$rankTel]->contato)-2));
                            $dados['telefone'] = trim($ExtraContatos[$rankTel]->contato);
                        }

                        // Celular
                        if ($rankCel != 1000) {
                            $dados['ddd_celular'] = left($ExtraContatos[$rankCel]->contato,2);
                            $dados['telefone_celular'] = trim(right($ExtraContatos[$rankCel]->contato, strlen($ExtraContatos[$rankCel]->contato)-2));
                        }

                        // Email
                        if ($rankEmail != 1000)
                            $dados['email'] = $ExtraContatos[$rankEmail]->contato;
                    }
                } // if (!empty($enriquecido['status']))

                // Regras DE/PARA
                if (empty($dados['nome']))
                    $dados['nome'] = "NOME SOBRENOME";

                if (empty($dados['endereco_estado']))
                    $dados['endereco_estado'] = "SP";

                if (empty($dados['endereco_cidade']))
                    $dados['endereco_cidade'] = "BARUERI";

                if (empty($dados['endereco_bairro']))
                    $dados['endereco_bairro'] = $dados['endereco_cidade'];

                if (empty($dados['endereco_logradouro']) || strlen($dados['endereco_logradouro']) <= 3)
                    $dados['endereco_logradouro'] = "ALAMEDA RIO NEGRO";

                if (empty($dados['endereco_numero']))
                    $dados['endereco_numero'] = '0';

                if (empty($dados['endereco_cep']) || $dados['endereco_cep'] == "99999999")
                    $dados['endereco_cep'] = '06454000';

                if (empty($dados['sexo']))
                    $dados['sexo'] = 'M';

                if (empty($dados['data_nascimento'])) {
                    $dados['data_nascimento'] = '2000-01-01';
                } elseif (!app_validate_data_americana($dados['data_nascimento'])) {
                    $dados['data_nascimento'] = '2000-01-01';
                }

            } // if ($enriqueceCPF)

            if (empty($dados['nome'])) {
                $errors[] = ['id' => 20, 'msg' => "Nome inválido/Não informado", 'slug' => "nome"];
            }

            if (empty($dados['sexo'])) {
                $errors[] = ['id' => 57, 'msg' => "Sexo inválido/não informado", 'slug' => "sexo"];
            }

            if (empty($dados['endereco_estado'])) {
                $errors[] = ['id' => 21, 'msg' => "UF inválido.", 'slug' => "uf"];
            }

            if (empty($dados['endereco_bairro'])) {
                $errors[] = ['id' => 22, 'msg' => "Bairro inválido.", 'slug' => "bairro"];
            }

            if (empty($dados['endereco_cidade'])) {
                $errors[] = ['id' => 23, 'msg' => "Cidade inválida.", 'slug' => "cidade"];
            }

            if (empty($dados['endereco_numero'])) {
                $errors[] = ['id' => 24, 'msg' => "Nº logradouro inválido.", 'slug' => "numero"];
            }

            if (empty($dados['endereco_logradouro']) || strlen($dados['endereco_logradouro']) <= 3) {
                $errors[] = ['id' => 25, 'msg' => "Logradouro inválido.", 'slug' => "logradouro"];
            }

            if (empty($dados['endereco_cep']) || $dados['endereco_cep'] == "99999999") {
                $errors[] = ['id' => 26, 'msg' => "CEP inválido.", 'slug' => "logradouro"];
            }

            if (!empty($dados['email']) && !app_valida_email($dados['email'])) {
                $errors[] = ['id' => 40, 'msg' => "E-mail inválido", 'slug' => "email"];
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
                $fields['data_adesao'] = $dados['data_adesao'];
                $fields['equipamento_nome'] = $dados['equipamento_nome'];

                if (!empty($dados['equipamento_marca_id']))
                    $fields['equipamento_marca_id'] = $dados['equipamento_marca_id'];

                if (!empty($dados['equipamento_categoria_id']))
                    $fields['equipamento_categoria_id'] = $dados['equipamento_categoria_id'];

                if (!empty($dados['equipamento_sub_categoria_id']))
                    $fields['equipamento_sub_categoria_id'] = $dados['equipamento_sub_categoria_id'];

                if (!empty($dados['equipamento_de_para']))
                    $fields['equipamento_de_para'] = $dados['equipamento_de_para'];

                if (!empty($dados['comissao_premio']))
                    $fields['comissao_premio'] = $dados['comissao_premio'];

                if (!empty($dados['data_inicio_vigencia']))
                    $fields['data_inicio_vigencia'] = $dados['data_inicio_vigencia'];

                if (!empty($dados['data_fim_vigencia']))
                    $fields['data_fim_vigencia'] = $dados['data_fim_vigencia'];

                $fields['ean'] = $dados['ean'];
                $fields['emailAPI'] = app_get_userdata("email");

                // Cotação
                $cotacao = app_get_api("insereCotacao", "POST", json_encode($fields), $acesso);
                if (empty($cotacao['status'])) {
                    if ( is_object($cotacao['response']) )
                    {
                        $erros = isset($cotacao['response']->erros) ? $cotacao['response']->erros : $cotacao['response']->errors;
                        foreach ($erros as $er) {
                            $response->errors[] = [
                                'id' => -1, 
                                'msg' => $er, 
                                'slug' => "insere_cotacao"
                            ];
                        }
                    } else {
                        $response->errors[] = ['id' => -1, 'msg' => $cotacao['response'], 'slug' => "insere_cotacao"];
                    }
                    return $response;
                }

                $cotacao = $cotacao['response'];
                $cotacao_id = $cotacao->cotacao_id;
                $response->cotacao_id = $cotacao_id;

                // Cálculo do prêmio
                $calcPremio = app_integracao_calcula_premio($cotacao_id, $dados["premio_bruto"], $dados["nota_fiscal_valor"], $acesso);
                if (empty($calcPremio['status'])){
                    $response->errors[] = ['id' => -1, 'msg' => $calcPremio['response'], 'slug' => "calcula_premio"];
                    return $response;
                }

                $premioValid = $calcPremio['response'];
                $valor_premio = $calcPremio['valor_premio'];

                if (!$premioValid) {
                    $errors[] = ['id' => 7, 'msg' => "Valor do prêmio bruto [". $dados["premio_bruto"] ."] difere do prêmio calculado [". $valor_premio ."]", 'slug' => "premio_liquido"];
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
if ( ! function_exists('app_integracao_calcula_premio'))
{
    function app_integracao_calcula_premio($cotacao_id, $premio_bruto, $is, $acesso = null){
        // Cálculo do prêmio
        $calcPremio = app_get_api("calculo_premio/". $cotacao_id, 'GET', [], $acesso);
        if (empty($calcPremio['status'])){
            return ['status' => false, 'response' => $calcPremio['response']];
        }

        $calcPremio = $calcPremio['response'];
        $valor_premio = $calcPremio->premio_liquido_total;
        $premioValid = true;
        $aceitaPorcentagem = false;
        $dif_accept = 0;

        // diferença do cálculo
        if (!empty($acesso) )
        {
            if ( $acesso->parceiro == 'lasa' ) {
                $dif_accept = 0.01;
            } elseif ( $acesso->parceiro == 'novomundo' ) {
                $dif_accept = 0.50;
            }
        }

        echo "Calculo do Premio: $valor_premio | $premio_bruto<br>";

        if ($valor_premio != $premio_bruto) {
            if ($valor_premio >= $premio_bruto-$dif_accept && $valor_premio <= $premio_bruto+$dif_accept) {
                $premioValid = true;
                echo "dif de R$ $dif_accept<br>";
            }else {

                $premioValid = false;

                if ($is > 0) {
                    // calcula o percentual
                    $percent = (float)$premio_bruto / (float)$is * 100;

                    echo "calculado $percent % <br>";

                    if (!$aceitaPorcentagem) {
                        // Arredondamento do percentual (a.)
                        if ($percent >= 24.9 && $percent <= 25.99999999999999) {
                            $premioValid = true;
                        }
                    } else {
                        // TRATAR DEMAIS PERCENTUAIS

                        // percentual liquido (remove o IOF)
                        $percent /= 1.0738;
                        $percRF = $percent * 0.6;
                        $percQA = $percent - $percRF;

                        echo "RF $percRF % <br>";
                        echo "QA $percQA % <br>";

                        $CI =& get_instance();
                        $CI->load->model('cobertura_plano_model', 'cobertura_plano');
                        $CI->cobertura_plano->update(281, ['porcentagem' => $percRF], TRUE);
                        $CI->cobertura_plano->update(282, ['porcentagem' => $percQA], TRUE);

                        return app_integracao_calcula_premio($cotacao_id, $premio_bruto, $is, $acesso);
                    }
                }
            }
        }

        return ['status'=> true, 'response'=> $premioValid, 'valor_premio'=> $valor_premio];
    }
}
if ( ! function_exists('app_integracao_emissao'))
{
    function app_integracao_emissao($format, $dados, $acesso = null){
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

        // Emissão
        if ( $dados['acao'] == '1' ) {

            // Cotação Contratar
            $fields['emailAPI'] = app_get_userdata("email");
            $cotacao = app_get_api("cotacao_contratar", "POST", json_encode($fields), $acesso);
            if (empty($cotacao['status'])) {
                if ( is_object($cotacao['response']) )
                {
                    $erros = isset($cotacao['response']->erros) ? $cotacao['response']->erros : $cotacao['response']->errors;
                    foreach ($erros as $er) {
                        $response->errors[] = [
                            'id' => -1, 
                            'msg' => $er, 
                            'slug' => "cotacao_contratar"
                        ];
                    }
                } else {
                    $response->errors[] = ['id' => -1, 'msg' => $cotacao['response'], 'slug' => "cotacao_contratar"];
                }
                return $response;
            }

            // Formas de Pagamento
            $formPagto = app_get_api("forma_pagamento_cotacao/$cotacao_id", 'GET', [], $acesso);
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

                $efetuaPagto = app_get_api("pagamento_pagar", "POST", json_encode($camposPagto), $acesso);
                if (empty($efetuaPagto['status'])) {
                    $response->msg[] = ['id' => -1, 'msg' => $efetuaPagto['response'], 'slug' => "pagamento_pagar"];
                    return $response;
                }

                $response->msg[] = $efetuaPagto['response']->mensagem;
                $response->pedido_id = $efetuaPagto['response']->dados->pedido_id;
                $response->apolice_id = $efetuaPagto['response']->dados->apolice_id;

                // método para salvar a apólice
                $fieldApolice = [
                    "apolice_id" => $response->apolice_id,
                    "num_apolice" => $num_apolice,
                    "emailAPI" => app_get_userdata("email"),
                ];

                $getApolice = app_get_api("apolice", "POST", json_encode($fieldApolice), $acesso);
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
        } else if ( $dados['acao'] == '9' ) {

            // Cancelamento
            $cancelaApolice = app_get_api("cancelar", "POST", json_encode( [
                "apolice_id" => $dados['apolice_id'], 
                "define_date" => $dados['data_adesao_cancel'], 
                "emailAPI" => app_get_userdata("email")
            ]), $acesso);
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
        $num_apolice_aux = $dados['registro']['cod_sucursal'] . $dados['registro']['cod_ramo'] . $dados['registro']['cod_tpa'];

        if ($dados['registro']['cod_tpa'] == '025')
        {
            $num_apolice_aux .= substr($num_apolice, 3, 3) . str_pad(substr($num_apolice, 10, 5), 5, '0', STR_PAD_LEFT);
        } else {
            $num_apolice_aux .= str_pad(substr($num_apolice, 7, 8), 8, '0', STR_PAD_LEFT);
        }

        return $num_apolice_aux;
    }
}
if ( ! function_exists('app_integracao_apolice_revert')) {
    function app_integracao_apolice_revert($formato, $dados = array())
    {
        $num_apolice_cliente = !empty(trim($dados['valor'])) ? trim($dados['valor']) : '';

        if(empty($num_apolice_cliente))
            return '';

        $CI =& get_instance();
        $CI->load->model('apolice_model');
        $result = $CI->apolice_model->filter_by_numApoliceCliente($num_apolice_cliente)->get_all();

        if (empty($result))
            return '';

        return $result[0]['num_apolice'];
    }
}
if ( ! function_exists('app_integracao_id_transacao')) {
    function app_integracao_id_transacao($formato, $dados = array())
    {
        $num_apolice = app_integracao_apolice($formato, $dados).$dados['registro']['num_endosso'];

        if ($dados['registro']['cod_tpa'] == '025')
        {
            $num_apolice .= $dados['registro']['cod_ramo_cob'];
        } else {
            $num_apolice .= $dados['registro']['cod_ramo'];
        }

        return $num_apolice.$dados['registro']['num_parcela'];
    }
}
if ( ! function_exists('app_integracao_id_transacao_canc')) {
    function app_integracao_id_transacao_canc($formato, $dados = array())
    {
        $id_transacao = '';
        if ( in_array($dados['registro']['cod_tipo_emissao'], ['10','11']) ) {
            $id_transacao = app_integracao_apolice($formato, $dados)."0";

            if ($dados['registro']['cod_tpa'] == '025')
            {
                $id_transacao .= $dados['registro']['cod_ramo_cob'];
            } else {
                $id_transacao .= $dados['registro']['cod_ramo'];
            }

            $id_transacao .= $dados['registro']['num_parcela'];
        }
        return $id_transacao;
    }
}
if ( ! function_exists('app_integracao_retorno_generali_fail')) {
    function app_integracao_retorno_generali_fail($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'coderr' => [] ]; 
        // echo "<pre>";print_r($dados['registro']);

        if (!isset($dados['log']['nome_arquivo']) || empty($dados['log']['nome_arquivo'])) {
            $response->msg[] = ['id' => 12, 'msg' => 'Nome do Arquivo inválido', 'slug' => "erro_interno"];
            return $response;
        }

        $CI =& get_instance();
        $CI->load->model('integracao_model');
        $proc = $CI->integracao_model->detectFileRetorno($dados['log']['nome_arquivo'], $dados['registro']);
        $chave = $proc['chave'];
        $file = $proc['file'];
        $sinistro = ($proc['tipo'] == 'SINISTRO');

        // echo "chave = $chave<br>";
        if (empty($chave)) {
            $response->msg[] = ['id' => 12, 'msg' => 'Chave não identificada', 'slug' => "erro_interno"];
            return $response;
        }

        // LIBERA TODOS OS QUE NAO FORAM LIDOS COMO ERRO E OS AINDA NAO FORAM LIBERADOS
        $CI->integracao_model->update_log_fail($file, $chave, $sinistro);

        $response->coderr = $dados['registro']['cod_erro']; 
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

        $CI =& get_instance();
        $CI->load->model('integracao_model');
        $proc = $CI->integracao_model->detectFileRetorno($dados['log']['nome_arquivo']);
        $file = $proc['file'];
        $sinistro = ($proc['tipo'] == 'SINISTRO');

        // LIBERA TODOS OS QUE NAO FORAM LIDOS COMO ERRO E OS AINDA NAO FORAM LIBERADOS
        $CI->integracao_model->update_log_sucess($file, $sinistro);

        return true;
    }
}
if ( ! function_exists('app_integracao_generali_sinistro')) {
    function app_integracao_generali_sinistro($formato, $dados = array())
    {
        $d = $dados['registro'];
        $integracao_log_detalhe_id = $formato;
        $valor = str_replace(array(",", "."), array("", "."), $d['vlr_movimento']);

        // Ações que zeram ou diminuem ou valor da reserva
        // Ajuste a menor, Cancelamento, Pagamento Total e Parcial
        if (in_array($d['cod_tipo_mov'], [2, 7, 9, 146]) ) {
            $valor *= -1;
        }

        $CI =& get_instance();
        $CI->db->query("INSERT INTO sissolucoes1.sis_exp_hist_carga (id_exp, data_envio, tipo_expediente, id_controle_arquivo_registros, valor) 
            SELECT {$d['id_exp']}, NOW(), '{$d['tipo_expediente']}', '{$integracao_log_detalhe_id}', {$valor} 
            FROM sissolucoes1.sis_exp a
            LEFT JOIN sissolucoes1.sis_exp_hist_carga b ON a.id_exp = b.id_exp AND b.`status` = 'P'
            WHERE a.id_exp = {$d['id_exp']} AND b.id_exp IS NULL
            ");
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
if ( ! function_exists('app_integracao_gera_sinistro')) {
    function app_integracao_gera_sinistro($formato, $dados = array())
    {
        $CI =& get_instance();
        $CI->db->query("call sp_gera_sinistro(27)");
    }
}
if ( ! function_exists('app_integracao_novo_mundo')) {
    function app_integracao_novo_mundo($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        $dados['registro']['sexo'] = 'M';
        $dados['registro']['data_nascimento'] = '1981-12-02';
        $reg = $dados['registro'];
        // echo "<pre>";print_r($reg);echo "</pre>";die();

        $CI =& get_instance();
        $CI->session->sess_destroy();
        $CI->session->set_userdata("operacao", "novomundo");

        if (!empty($formato)) {
            $geraDados['tipo_produto']              = $reg['tipo_produto'];
            $geraDados['tipo_operacao']             = $reg['acao'];
            $geraDados['ramo']                      = $reg['ramo'];
            $geraDados['agrupador']                 = $reg['agrupador'];
            $geraDados['cod_loja']                  = $reg['cod_loja'];
            $geraDados['num_apolice']               = $reg['num_apolice'];
            $geraDados['nota_fiscal_numero']        = $reg['nota_fiscal_numero'];
            $geraDados['cod_vendedor']              = $reg['cod_vendedor'];
            $geraDados['cpf_vendedor']              = $reg['cpf_vendedor'];
            $geraDados['nome_vendedor']             = $reg['nome_vendedor'];
            $geraDados['nome']                      = $reg['nome'];
            $geraDados['sexo']                      = $reg['sexo'];
            $geraDados['data_nascimento']           = $reg['data_nascimento'];
            $geraDados['ddd_residencial']           = $reg['ddd_residencial'];
            $geraDados['telefone']                  = $reg['telefone'];
            $geraDados['ddd_comercial']             = $reg['ddd_comercial'];
            $geraDados['telefone_comercial']        = $reg['telefone_comercial'];
            $geraDados['ddd_celular']               = $reg['ddd_celular'];
            $geraDados['telefone_celular']          = $reg['telefone_celular'];
            $geraDados['endereco']                  = $reg['endereco_logradouro'];
            $geraDados['endereco_numero']           = $reg['endereco_numero'];
            $geraDados['complemento']               = $reg['complemento'];
            $geraDados['endereco_bairro']           = $reg['endereco_bairro'];
            $geraDados['endereco_cidade']           = $reg['endereco_cidade'];
            $geraDados['endereco_estado']           = $reg['endereco_estado'];
            $geraDados['endereco_cep']              = $reg['endereco_cep'];
            $geraDados['email']                     = $reg['email'];
            $geraDados['tipo_pessoa']               = $reg['tipo_pessoa'];
            $geraDados['cpf']                       = $reg['cpf'];
            $geraDados['outro_doc']                 = $reg['outro_doc'];
            $geraDados['tipo_doc']                  = $reg['tipo_doc'];
            $geraDados['outro_doc']                 = $reg['outro_doc'];
            $geraDados['premio_liquido']            = $reg['premio_liquido'];
            $geraDados['premio_bruto']              = $reg['premio_bruto'];
            $geraDados['valor_iof']                 = $reg['valor_iof'];
            $geraDados['valor_custo']               = $reg['valor_custo'];
            $geraDados['num_parcela']               = 1;
            $geraDados['vigencia']                  = $reg['vigencia'];
            $geraDados['garantia_fabricante']       = $reg['garantia_fabricante'];
            $geraDados['marca']                     = $reg['marca'];
            $geraDados['modelo']                    = $reg['modelo'];
            $geraDados['cod_produto_sap']           = $reg['cod_produto_sap'];
            $geraDados['equipamento_nome']          = $reg['equipamento_nome'];
            $geraDados['num_serie']                 = $reg['num_serie'];
            $geraDados['nota_fiscal_data']          = $reg['nota_fiscal_data'];
            $geraDados['data_adesao_cancel']        = $reg['data_adesao_cancel'];
            $geraDados['data_inicio_vigencia']      = $reg['data_inicio_vigencia'];
            $geraDados['data_fim_vigencia']         = $reg['data_fim_vigencia'];
            $geraDados['ean']                       = $reg['ean'];
            $geraDados['nota_fiscal_valor']         = $reg['nota_fiscal_valor'];
            $geraDados['cod_cancelamento']          = $reg['cod_cancelamento'];
            $geraDados['data_cancelamento']         = $reg['data_cancelamento'];
            $geraDados['status_carga']              = $reg['status_carga'];
            $geraDados['status_reenvio']            = $reg['status_reenvio'];
            $geraDados['codigo_erro']               = $reg['codigo_erro'];
            $geraDados['dado_financ']               = $reg['dado_financ'];
            $geraDados['id_garantia_fornecedor']    = $reg['id_garantia_fornecedor'];
            $geraDados['id_garantia_loja']          = $reg['id_garantia_loja'];
            $geraDados['num_sorte']                 = $reg['num_sorte'];
            $geraDados['num_serie_cap']             = $reg['num_serie_cap'];
            $geraDados['canal_venda']               = $reg['canal_venda'];
            $geraDados['valor_prolabore']           = $reg['comissao_valor'];
            $geraDados['perc_prolabore']            = $reg['comissao_premio'];
            $geraDados['dias_canc_apos_venda']      = $reg['dias_canc_apos_venda'];
            $geraDados['produto_seg']               = $reg['produto_seg'];
            $geraDados['cod_faixa_preco']           = $reg['equipamento_de_para'];
            $geraDados['numero_seq_lote']           = $reg['numero_seq_lote'];
            $geraDados['arquivo_data']              = $reg['arquivo_data'];
            $geraDados['arquivo_hora']              = $reg['arquivo_hora'];
            $geraDados['versao_layout']             = $reg['versao_layout'];
            $geraDados['integracao_log_detalhe_id'] = $formato;

            $CI->load->model("integracao_log_detalhe_dados_model", "integracao_log_detalhe_dados");
            $CI->integracao_log_detalhe_dados->insLogDetalheDados($geraDados);
        }

        // definir operação pelo nome do arquivo ou por integracao?
        $acesso = app_integracao_novo_mundo_define_operacao($dados['log']['nome_arquivo']);
        if ( empty($acesso->status) ) {
            $response->status = 2;
            $response->msg[] = $acesso->msg;
            return $response;
        }

        // recupera as variaveis mais importantes
        $num_apolice    = $reg['num_apolice'];
        $cpf            = $reg['cpf'];
        $ean            = $reg['ean'];

        $dados['registro']['produto_parceiro_id']       = $acesso->produto_parceiro_id;
        $dados['registro']['produto_parceiro_plano_id'] = $acesso->produto_parceiro_plano_id;
        $dados['registro']['data_adesao']               = $dados['registro']['data_adesao_cancel'];
        $eanErro = true;
        $eanErroMsg = "";

        // validações iniciais
        $valid = app_integracao_inicio($acesso->parceiro_id, $num_apolice, $cpf, $ean, $dados, $acesso);
        if ( $valid->status !== true ) {
            $response = $valid;
            return $response;
        }

        // Campos para cotação
        $camposCotacao = app_get_api("cotacao_campos/". $acesso->produto_parceiro_id, 'GET', [], $acesso);
        if (empty($camposCotacao['status'])){
            $response->msg[] = ['id' => -1, 'msg' => $camposCotacao['response'], 'slug' => "cotacao_campos"];
            return $response;
        }

        $camposCotacao = $camposCotacao['response'];

        // Validar Regras
        $validaRegra = app_integracao_valida_regras($dados, $camposCotacao, false, $acesso);
        // echo "<pre>";print_r($validaRegra);echo "</pre>";die();

        if (!empty($validaRegra->status)) {
            $dados['registro']['cotacao_id'] = !empty($validaRegra->cotacao_id) ? $validaRegra->cotacao_id : 0;
            $dados['registro']['fields'] = $validaRegra->fields;
            $emissao = app_integracao_emissao($formato, $dados, $acesso);

            if (empty($emissao->status)) {

                if ( !empty($emissao->msg) ) {

                    if ( !is_array($emissao->msg) ) {
                        $response->msg[] = $emissao->msg;
                    } else {
                        $response->msg = $emissao->msg;
                    }

                } else {
                    $response->msg = $emissao->errors;
                }

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
if ( ! function_exists('app_integracao_novo_mundo_define_operacao')) {
    function app_integracao_novo_mundo_define_operacao($nome_arquivo)
    {
        /*
         * Nomenclatura dos arquivos:
         * ssssOOnnnn_xx_data.ext (len 27), onde:
         * ssss - Sigla do seguro: GAES - Garantia Estendida, ROFU - Roubo e Furto, QUAC - Quebra Acidental, APVE - AP Vendedor, APCA - AP Caixa, PRES - Prestamista
         * 0OO - Operação (031 Móvies e 032 Amazônia)
         * nnnn - Número sequencial da remessa.
         * xx - Sequência de envio de uma mesma remessa.
         * data - Data da geração do arquivo no formato YYYMMDD
         * ext - Extensão (REM = Remessa, RET = Retorno)
        */
        $result = (object) ['status' => false, 'msg' => []];

        if (empty($nome_arquivo)) {
            return $result;
        }

        if ( strlen($nome_arquivo) <> 27) {
            $result->msg = ['id' => -1, 'msg' => "Nome do arquivo deve possuir 27 caracteres.", 'slug' => "nome_arquivo"];
            return $result;
        }

        $result->produto = substr($nome_arquivo, 0, 4);
        $result->operacao = substr($nome_arquivo, 4, 3);
        $result->sequencial = substr($nome_arquivo, 7, 4);
        $result->sequencial_remessa = substr($nome_arquivo, 12, 2);
        $result->data = app_integracao_format_date_r("Ymd|Y-m-d", ['valor' => substr($nome_arquivo, 15, 8)]);

        switch ($result->operacao) {
            case '031':
                $result->parceiro_id = 72;
                $result->email = "novomundo@sisconnects.com.br";

                switch ($result->produto) {
                    case 'GAES':
                        $result->produto_parceiro_id = 80;
                        $result->produto_parceiro_plano_id = 103;
                        break;

                    case 'ROFU':
                        $result->produto_parceiro_id = 81;
                        $result->produto_parceiro_plano_id = 105;
                        break;

                    case 'QUAC':
                        $result->produto_parceiro_id = 82;
                        $result->produto_parceiro_plano_id = 106;
                        break;

                    default:
                        $result->msg = ['id' => 39, 'msg' => "Produto ({$result->produto}) não configurado", 'slug' => "produto"];
                        return $result;
                        break;
                }

                break;
            case '032':
                $result->parceiro_id = 76;
                $result->email = "novomundoamazonia@sisconnects.com.br";

                switch ($result->produto) {
                    case 'GAES':
                        $result->produto_parceiro_id = 83;
                        $result->produto_parceiro_plano_id = 107;
                        break;

                    case 'ROFU':
                        $result->produto_parceiro_id = 84;
                        $result->produto_parceiro_plano_id = 108;
                        break;

                    case 'QUAC':
                        $result->produto_parceiro_id = 85;
                        $result->produto_parceiro_plano_id = 109;
                        break;

                    default:
                        $result->msg = ['id' => 39, 'msg' => "Produto ({$result->produto}) não configurado", 'slug' => "produto"];
                        return $result;
                        break;
                }

                break;

            default:
                $result->msg = ['id' => -1, 'msg' => "Operação ({$result->operacao}) não configurada", 'slug' => "operacao"];
                return $result;
                break;
        }

        // Dados para definição do parceiro, produto e plano
        $acesso = app_integracao_generali_dados([
            "email" => $result->email,
            "parceiro_id" => $result->parceiro_id,
            "produto_parceiro_id" => $result->produto_parceiro_id,
            "produto_parceiro_plano_id" => $result->produto_parceiro_plano_id,
        ]);

        $result->apikey = $acesso->apikey;
        $result->parceiro = $acesso->parceiro;
        $result->status = true;
        return $result;
    }
}
if ( ! function_exists('app_integracao_inicio')) {
    function app_integracao_inicio($parceiro_id, $num_apolice = '', $cpf = '', $ean = '', &$dados = array(), $acesso = null)
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        $CI =& get_instance();
        $reg = $dados['registro'];

        if (empty($num_apolice)){
            $response->msg[] = ['id' => 27, 'msg' => "Apólice não recebida no arquivo", 'slug' => "apolice"];
            return $response;
        }

        $eanErro = true;
        $eanErroMsg = "";

        // usar a pesquisa por nome
        $CI->load->model("apolice_model", "apolice");
        $CI->load->model("integracao_log_detalhe_model", "integracao_log_detalhe");

        //Busca a apólice pelo número
        $apolice = $CI->apolice->getApoliceByNumero($num_apolice, $parceiro_id);

        // Emissão
        if ($reg['acao'] == '1') {

            if (!empty($apolice)) {
                $response->status = 2;
                $response->msg[] = ['id' => 16, 'msg' => "Apólice já emitida [{$num_apolice}]", 'slug' => "emissao"];
                return $response;
            }

            if (!empty($cpf)) $cpf = substr($cpf, -11);

            if( !app_validate_cpf($cpf) ){
                $response->msg[] = ['id' =>  2, 'msg' => "Campo CPF deve ser um CPF válido [{$cpf}]", 'slug' => 'cnpj_cpf'];
                return $response;
            }

            // Consulta com o ean enviado
            if (!empty($ean)) {
                $ean = (int)$ean;
                $EANenriquecido = app_get_api("enriqueceEAN/$ean", 'GET', [], $acesso);
                // echo "<pre>";print_r($EANenriquecido);echo "</pre>";

                if (!empty($EANenriquecido['status'])){
                    $EANenriquecido = $EANenriquecido['response'];
                    $response->ean = $EANenriquecido;
                    $eanErro = false;

                    $dados['registro']['equipamento_id'] = $EANenriquecido->equipamento_id;
                    $dados['registro']['equipamento_marca_id'] = $EANenriquecido->equipamento_marca_id;
                    $dados['registro']['equipamento_categoria_id'] = $EANenriquecido->equipamento_categoria_id;
                    $dados['registro']['equipamento_sub_categoria_id'] = $EANenriquecido->equipamento_sub_categoria_id;
                    $dados['registro']['imei'] = "";
                }
            }

            // se não encontrou por EAN busca por marca e nome
            if ($eanErro){
                $inputField = [
                    'modelo' => $dados['registro']['equipamento_nome'],
                    'marca' => $dados['registro']['marca'],
                    'quantidade' => 1,
                    'emailAPI' => app_get_userdata("email"),
                ];

                $EANenriquecido = app_get_api("enriqueceModelo", "POST", json_encode($inputField), $acesso);
                // echo "<pre>";print_r($EANenriquecido);echo "</pre>";

                if (!empty($EANenriquecido['status'])){
                    $EANenriquecido = $EANenriquecido['response']->dados[0];
                    $response->ean = $EANenriquecido;
                    $eanErro = false;

                    $dados['registro']['equipamento_id'] = $EANenriquecido->equipamento_id;
                    $dados['registro']['equipamento_marca_id'] = $EANenriquecido->equipamento_marca_id;
                    $dados['registro']['equipamento_categoria_id'] = $EANenriquecido->equipamento_categoria_id;
                    $dados['registro']['equipamento_sub_categoria_id'] = $EANenriquecido->equipamento_sub_categoria_id;
                    $dados['registro']['imei'] = "";
                } else {
                    $eanErroMsg = "Equipamento não identificado - [{$dados['registro']['equipamento_nome']}]";
                }

            }

            if ($eanErro){
                echo "<pre>";print_r($EANenriquecido);echo "</pre>";

                $response->msg[] = ['id' => 11, 'msg' => $eanErroMsg ." [{$ean}]", 'slug' => "enriquece_ean"];
                return $response;
            }

        // Cancelamento
        } else if ( $reg['acao'] = '9' ) {

            if ( empty($reg['data_cancelamento']) && !app_validate_data_americana($dados['data_cancelamento']) ) {
                $response->msg[] = ['id' => 42, 'msg' => "Data de Cancelamento inválida [{$dados['data_cancelamento']}]", 'slug' => "cancelamento"];
                return $response;
            }

            if (empty($apolice)) {
                $response->msg[] = ['id' => 8, 'msg' => "Apólice não encontrada [{$num_apolice}]", 'slug' => "cancelamento"];
                return $response;
            } else {

                $apolice = $apolice[0];
                if ($apolice['apolice_status_id'] != 1) {
                    $response->status = 2;
                    $response->msg[] = ['id' => 17, 'msg' => "Apólice {$num_apolice} já está Cancelada e/ou em um status inválido [{$apolice['nome']}]", 'slug' => "cancelamento"];
                    return $response;
                }

                $dados['registro']['apolice_id'] = $apolice['apolice_id'];
            }

        }

        $response->status = true;
        return $response;
    }
}
if ( ! function_exists('app_integracao_cap_data_sorteio')) {
    function app_integracao_cap_data_sorteio($formato, $dados = array())
    {
        $data = null;
        $pedido_id = issetor($dados["registro"]["pedido_id"], 0);
        $formato   = emptyor($formato, 'dmY');

        $CI =& get_instance();
        $CI->load->model("Capitalizacao_Sorteio_Model", "capitalizacao_sorteio");

        return $CI->capitalizacao_sorteio->defineDataSorteio($pedido_id, $formato);
    }
}
if ( ! function_exists('app_integracao_cap_remessa')) {
    function app_integracao_cap_remessa($formato, $dados = array())
    {
        $CI =& get_instance();
        $CI->load->model('capitalizacao_model');

        if((isset($dados['registro']['dados'][0]['num_remessa'])) && (isset($dados['registro']['dados'][0]['capitalizacao_id'])) ){
            $num_remessa = $dados['registro']['dados'][0]['num_remessa'] += 1;
            $data_cap    = array('num_remessa' => $num_remessa);
            $CI->capitalizacao_model->update($dados['registro']['dados'][0]['capitalizacao_id'], $data_cap, TRUE);

            return true;
        }else{
            return false;
        }
    }
}
