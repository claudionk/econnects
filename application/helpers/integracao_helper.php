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

          return str_pad((count($dados['registro'])*3)+2, $formato, '0', STR_PAD_LEFT);

    }
}

if ( ! function_exists('app_integracao_mapfre_rf_total_itens')) {
    function app_integracao_mapfre_rf_total_itens($formato, $dados = array())
    {

          return str_pad((count($dados['registro'])*3), $formato, '0', STR_PAD_LEFT);

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

        $a = explode(",", $formato);
        $valor = $dados[$a[0]][$a[1]];
        $valor = ($valor == 0) ? '0.0' : $valor;
        $valor = explode('.', $valor);
        $valor[1] = ((isset($valor[1])) || (empty(isset($valor[1]))) ) ? '00' : $valor[1];
        return str_pad($valor[0],  ($a[2]-2), '0', STR_PAD_LEFT) . str_pad($valor[1], 2, '0', STR_PAD_LEFT);

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
