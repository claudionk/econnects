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
        return str_pad($valor[0], ($dados['item']['tamanho']-($a[2] + strlen($a[3]) )), $dados['item']['valor_padrao'], STR_PAD_LEFT) .$a[3]. str_pad($valor[1], $a[2], '0', STR_PAD_RIGHT);
    }
}
if ( ! function_exists('app_integracao_format_decimal_r')) {

    function app_integracao_format_decimal_r($formato, $dados = array())
    {
        /**
        * $f[0] = qtde final de casas decimais
        * $f[1] = caracter final para separar os decimais
        * $f[2] = caracter inicial para separar os decimais
        * $f[3] = caracter inicial para separar os milhares
        **/
        $f = explode("|", $formato);
        $decimalIN = emptyor($f[2], '');
        $milharesIN = emptyor($f[3], '');

        if ( empty($dados['valor']) )
        {
            $valor = str_pad(0,  100, '0', STR_PAD_LEFT);
        } else
        {
            $valor = (int)str_replace($decimalIN, "", str_replace($milharesIN, "", $dados['valor']));
        }

        $a = $val = left($valor, strlen($valor)-$f[0]);
        $a = (int)$a;
        $b = $f[1];
        $c = right($valor, $f[0]);
        $result = $a.$b.$c;

        // verifica se possuia o sinal de negativo e depois perdeu para que volte a ser negativo
        if ( !(strpos($val, "-") === FALSE) && $a >= 0)
            $result *= -1;

        return $result;
    }
}
if ( ! function_exists('app_integracao_format_date_r')) {

    function app_integracao_format_date_r($formato, $dados = array())
    {
        $a = explode("|", $formato);
        $date = $dados['valor'];
        if( isset( $dados['valor'] ) && !empty(trim($dados['valor'])) && preg_replace('/\D/', '', $dados['valor']) != '00000000' ){
            $date = date_create_from_format( $a[0], $dados['valor'] );
            $date = $date->format($a[1]);
        }

        return $date;
    }

}
if ( ! function_exists('app_integracao_format_file_name_pagnet')) {
    function app_integracao_format_file_name_pagnet($formato, $dados = array())
    {
        /*MCAP_II_NEW_PPPP_DDMMAA_SS.TXT*/
        $file = "PAGNET_". date('dmyHis'). '.TXT';
        return  $file;
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
if ( ! function_exists('app_integracao_file_name_icatu')) {

    function app_integracao_file_name_icatu($formato, $dados = array())
    {
        $dados['registro'] = emptyor($dados['registro'][0], []);
        $cod_produto = emptyor($dados['registro']['cod_produto'], '');
        $sequencial = app_integracao_icatu_sequencia($formato, $dados);
        $sequencial = mb_str_pad($sequencial, 6, '0', STR_PAD_LEFT);
        $file = "{$formato}{$cod_produto}{$sequencial}.txt";
        return $file;
    }

}
if ( ! function_exists('app_integracao_csv_retorno_novomundo')) {

    function app_integracao_csv_retorno_novomundo($formato, $dados = array())
    {
        $CI=& get_instance();
        $CI->load->model('integracao_model');

        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];
        $os = $dados['registro']['id_exp'];
        $status_troca = $dados['registro']['status_troca'];

        switch($status_troca)
        {
            case "TROCA REALIZADA":
            case "CANCELADA":
                $ret = $CI->integracao_model->update_status_novomundo($os, $status_troca);
                if ( !empty($ret['status']) )
                {
                    $response->status = true;
                } else {
                    $msg = emptyor($ret['erro'], "Erro no processamento API");
                    $response->msg[] = ['id' => -1, 'msg' => $ret['erro'] ." [$os - {$status_troca}]", 'slug' => "voucher_retorno"];
                }
                break;
            default:
                break;
        }

        return $response;
    }

}
if ( ! function_exists('app_integracao_file_name_novomundo')) {

    function app_integracao_file_name_novomundo($formato, $dados = array())
    {
        $data = date('Ymd');
        $ext=isset($dados['item']['tipo_layout'])?strtolower($dados['item']['tipo_layout']):"csv";
        $file = "{$formato}{$data}.{$ext}";
        return $file;
    }

}
if ( ! function_exists('app_integracao_zip_extract_novomundo'))
{
    function app_integracao_zip_extract_novomundo($formato, $dados = array())
    {
        $CI =& get_instance();
        $CI->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');
        $CI->load->model('integracao_log_detalhe_campo_model', 'integracao_log_detalhe_campo');

        $diretorio  = $dados['registro']['file'];
        $arquivo    = $dados['registro']['fileget'];
        $diretorio  = str_replace($arquivo, "", $diretorio);
        $chave = $novo_diretorio = str_replace(".zip", "", $arquivo);

        // retira a data do nome do arquivo para gerar a chave do log
        if ( !empty($dados['registro']['fileRemove']) )
        {
            $quebraArquivo = explode("_", $novo_diretorio);
            $chave = $quebraArquivo[0];
        }

        // Gera o registro lido
        $integracao_log_detalhe_id = $CI->integracao_log_detalhe->insLogDetalhe($dados['log']['integracao_log_id'], 0, $chave, '');

        if(!file_exists($diretorio . $arquivo))
        {
            $CI->integracao_log_detalhe_campo->insLogDetalheCampo($integracao_log_detalhe_id, -1, 'caminho nao identiicado '. $diretorio.$arquivo, 'nm_zip');
            return false;
        }

        // print_pre( ['a', $diretorio . $novo_diretorio], false );
        if(!file_exists($diretorio . $novo_diretorio))
        {
            mkdir($diretorio . $novo_diretorio, 0777, true);
        }

        // print_pre( ['b', $diretorio.$arquivo, $diretorio.$novo_diretorio .'/'. $arquivo], false );
        rename( $diretorio.$arquivo, $diretorio.$novo_diretorio .'/'. $arquivo );

        $zip = new ZipArchive;
        $res = $zip->open($diretorio . $novo_diretorio . '/' . $arquivo);
        if ($res === TRUE) 
        {
            $zip->extractTo($diretorio . $novo_diretorio);
            $zip->close();

            // Não lê o arquivo novamente para nao entrar em um loop infinito
            $notRead[] = $arquivo;

            // só remove os arquivos filhos pois o backup está no pai
            if ( !empty($dados['registro']['fileRemove']) )
            {
                // remove o arquivo para não ler novamente e pq já manteve o backup o arquivo pai
                unlink($diretorio . $novo_diretorio . '/' . $arquivo);

                // Faz o upload do arquivo
                $ret = app_integracao_zip_extract_novomundo_upload($formato, $dados);

                // valida o retorno
                if ( empty($ret['status']) )
                {
                    $integracao_log_status_id = 5;
                    $ErroID = emptyor($ret['id'], -1);
                    $ErroMSG = emptyor($ret['erro'], '');
                    $ErroSLUG = emptyor($ret['slug'], 'nm_zip');
                    $CI->integracao_log_detalhe_campo->insLogDetalheCampo($integracao_log_detalhe_id, $ErroID, $ErroMSG, $ErroSLUG);
                } else
                {
                    $integracao_log_status_id = 4;
                }

                // gerar log de sucesso
                $CI->integracao_log_detalhe->update_by(
                    array('integracao_log_detalhe_id' => $integracao_log_detalhe_id),
                    array('integracao_log_status_id' => $integracao_log_status_id)
                );

                // Para a leitura dos arquivos
                return true;
            }

            $dir = new DirectoryIterator( $diretorio . $novo_diretorio );
            foreach($dir as $file)
            {
                // verifica se $file é diferente de '.' ou '..'
                if ( $file->isDot() )
                {
                    continue;
                }

                // recupera o nome do arquivo e seu path
                $caminho = $file->getPathname();
                $fileName = $file->getFilename();
                // print_pre(['c', $fileName, $caminho, $dados['registro']], false);

                // tratamento para nao ler novamente o mesmo arquivo (pai)
                if ( in_array($fileName, $notRead) )
                {
                    continue;
                }

                // altera os dados para procurar o novo arquivo
                $dados['registro']['file']       = $caminho;
                $dados['registro']['fileget']    = $fileName;
                $dados['registro']['fileRemove'] = true; // só remove os arquivos filhos pois o backup está no pai
                // print_pre( ['d', $dados['registro']], false);

                // se tiver um ZIP
                $pos = strpos(strtoupper($fileName), ".ZIP");
                if ($pos !== FALSE)
                {
                    // Faz a extração do arquivo ZIP
                    app_integracao_zip_extract_novomundo($formato, $dados);
                }
            }
        }
        else
        {
            $CI->integracao_log_detalhe_campo->insLogDetalheCampo($integracao_log_detalhe_id, -1, "Erro na extracao de arquivo: {$arquivo}", 'nm_zip');
            return false;
        }

        return true;
    }
}
if ( ! function_exists('app_integracao_docs_novo_mundo'))
{
    function app_integracao_docs_novo_mundo($fileName)
    {
        if (empty($fileName))
        {
            return null;
        }

        $docs = [ 
            [ 'doc' => 'CARTA_TROCA_', 'id' => 355 ],
            [ 'doc' => 'CARTEIRA_DE_IDENTIDADE_', 'id' => 100 ],
            [ 'doc' => 'NF_', 'id' => 108 ],
            [ 'doc' => 'CPF_', 'id' => 154 ],
            [ 'doc' => 'CARTEIRA_DE_MOTORISTA_COM_FOTO_', 'id' => 356 ],
        ];

        foreach ($docs as $key => $value) {
            $pos = strpos(strtoupper($fileName), $value['doc']);
            if ($pos !== FALSE)
            {
                return $value['id'];
            }
        }

        return null;
    }
}
if ( ! function_exists('app_integracao_zip_extract_novomundo_upload'))
{
    function app_integracao_zip_extract_novomundo_upload($formato, $dados = array())
    {
        $CI =& get_instance();
        $CI->load->library("SoapCurl");
        $SoapCurl = new SoapCurl();
        $retorno  = ['status' => false, 'erro' => "Falha no envio do Documento"];

        $diretorio      = $dados['registro']['file'];
        $arquivo        = $dados['registro']['fileget'];
        $diretorio      = str_replace($arquivo, "", $diretorio);
        $novo_diretorio = str_replace(".zip", "", $arquivo);
        $quebraArquivo  = explode("_", $arquivo);
        $num_cert       = $quebraArquivo[0];
        // print_pre( ['h', $num_cert, $dados['registro']], false);

        if(!file_exists($diretorio . $novo_diretorio))
        {
            $retorno['erro'] = "Caminho não identificado [{$diretorio}{$novo_diretorio}]";
            return $retorno;
        }

        $dir = new DirectoryIterator( $diretorio . $novo_diretorio );
        foreach($dir as $file)
        {
            // verifica se $file é diferente de '.' ou '..'
            if ( $file->isDot() )
            {
                continue;
            }

            // recupera o nome do arquivo e seu path
            $caminho = $file->getPathname();
            $fileName = $file->getFilename();
            $quebraArquivo = explode(".", $fileName);
            $ext_arquivo = strtolower(end($quebraArquivo));
            $id_documento = app_integracao_docs_novo_mundo($quebraArquivo[0]);
            // print_pre( [$id_documento, $ext_arquivo, $quebraArquivo], false );

            if ( empty($id_documento) )
            {
                $retorno['erro'] = "ID do documento não identificado";
                return $retorno;
            }

            $ret = $SoapCurl->getAPI("atendimento/ListaExpedienteByCertificadoVoucher/". $num_cert, "GET");
            if (empty($ret)){
                $retorno['erro'] = 'Sem resposta na pesquisa de Sinistro por Certificado';
                return $retorno;
            }elseif (empty($ret['status'])) {
                $retorno['erro'] = $ret['erro'];
                return $retorno;
            }elseif (empty($ret['response']['Expedientes'])) {
                $retorno['erro'] = "Nenhum expediente encontrado através do certificado {$num_cert}";
                return $retorno;
            }

            // recupera o numero da OS
            $id_exp =  $ret['response']['Expedientes'][0]['id_exp'];

            // print_pre( [$id_exp, $fileName, $caminho, $ext_arquivo, $id_documento, base64_encode( file_get_contents( $caminho ) )], false );

            try
            {
                $ret = $SoapCurl->getAPI("documentos/enviaDocumento", "POST", json_encode([
                    "idTipoDocumento" => $id_documento,
                    "Extensao" => $ext_arquivo,
                    "File" => base64_encode( file_get_contents( $caminho ) ) ,
                    "idExpediente" => $id_exp,
                ]), 900);
                // print_pre($ret, false);
                if (empty($ret)) {
                    $retorno['erro'] = 'Sem resposta no envio do Documento';
                    return $retorno;
                }elseif (empty($ret['status'])) {
                    $retorno['erro'] = $ret['erro'];
                    return $retorno;
                }
            }
            catch (Exception $e) 
            {
                $retorno['erro'] = $e->getMessage();
                return $retorno;
            }
        }

        $retorno = ['status' => true, 'erro' => ''];
        return $retorno;
    }
}
if ( ! function_exists('app_integracao_format_file_name_ret_sis'))
{
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
if ( ! function_exists('app_integracao_format_file_name_bidu')) {

    function app_integracao_format_file_name_bidu($formato, $dados = array())
    {

        if ( empty($dados['registro'][0]['nome_arquivo']) ) {
            return '';
        }

        $file = $dados['registro'][0]['nome_arquivo'];
        $pos = strpos($file, ".");

        if ( !($pos === FALSE) )
        {
            $file = substr($file, 0, $pos) ."_". date('Ymd') . substr($file, $pos, strlen($file));
        }

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
        } elseif ( $operacao == 'bidu') {
            $dados = (object)[
                "email" => "bidu@bidu.com.br",
                "parceiro_id" => 118,
                "produto_parceiro_id" => 58,
                "produto_parceiro_plano_id" => 0,
                "produto_slug" => 'plano_de_saude_hapvida',
                "plano_slug" => 'nosso_plano_hapvida',
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

if ( ! function_exists('app_integracao_rastrecall_sms_dados')) {

    function app_integracao_rastrecall_sms_dados()
    {
        $dados = (object)[
            "email" => "generali@econnects.com.br",
            "parceiro_id" => 32,
            "produto_parceiro_id" => 74,
            "produto_parceiro_plano_id" => 92,
            "token" => '7e443247fba9e33af5b7c0125e437969'
        ];

        $CI =& get_instance();
        $CI->session->set_userdata("email", $dados->email);

        $dados->apikey = app_get_token($dados->email);

        return $dados;
    }

}


if ( ! function_exists('app_integracao_rastrecall_sms')) {

    function app_integracao_rastrecall_sms($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        $CI =& get_instance();

        $CI->load->library("Short_url");


        if (!empty($formato)) {



            //$geraDados = $dados['registro'];
            //$geraDados['integracao_log_detalhe_id'] = $formato;

/*
            $geraDados['estipulante']           = $dados['registro']['estipulante'];
            $geraDados['nota_fiscal_numero']    = $dados['registro']['nota_fiscal_numero'];
            $geraDados['nota_fiscal_serie']     = $dados['registro']['nota_fiscal_serie'];
            $geraDados['nota_fiscal_emissao']   = app_dateonly_mask_to_mysql($dados['registro']['nota_fiscal_emissao']);
            $geraDados['segurado_nome']         = $dados['registro']['segurado_nome'];
            $geraDados['segurado_doc']          = $dados['registro']['segurado_doc'];
            $geraDados['segurado_rg_ie']        = $dados['registro']['segurado_rg_ie'];
            $geraDados['segurado_nascimento']   = app_dateonly_mask_to_mysql($dados['registro']['segurado_nascimento']);
            $geraDados['endereco_cep']          =  $dados['registro']['endereco_cep'];
            $geraDados['endereco']              =  $dados['registro']['endereco'];
            $geraDados['endereco_numero']       =  $dados['registro']['endereco_numero'];
            $geraDados['endereco_complemento']  =  $dados['registro']['endereco_complemento'];
            $geraDados['endereco_bairro']       =  $dados['registro']['endereco_bairro'];
            $geraDados['endereco_cidade']       =  $dados['registro']['endereco_cidade'];
            $geraDados['endereco_uf']           =  $dados['registro']['endereco_uf'];
            $geraDados['segurado_telefone']     =  $dados['registro']['segurado_telefone'];
            $geraDados['segurado_celular']      =  app_retorna_numeros($dados['registro']['segurado_celular']);
            $geraDados['segurado_email']        =  $dados['registro']['segurado_email'];
            $geraDados['equipamento']           =  $dados['registro']['equipamento'];
            $geraDados['equipamento_categoria'] =  $dados['registro']['equipamento_categoria'];
            $geraDados['equipamento_marca']     =  $dados['registro']['equipamento_marca'];
            $geraDados['equipamento_codigo_barra']     =  sprintf('%.0f', str_replace(",", "", $dados['registro']['equipamento_codigo_barra']) );
            $geraDados['equipamento_imei']     =  sprintf('%.0f', str_replace(",", "", $dados['registro']['equipamento_imei']) );
            $geraDados['nota_fiscal_valor']     =  app_format_currency($dados['registro']['nota_fiscal_valor']);

*/
            $geraDados['tipo_transacao']            = 'IN';
            $geraDados['cod_loja']                  = '';
            $geraDados['num_apolice']               = '';
            $geraDados['data_adesao_cancel']        = date('Y-m-d');
            $geraDados['telefone']                  = $dados['registro']['telefone'];
            $geraDados['endereco']                  = $dados['registro']['endereco_logradouro'];
            $geraDados['sexo']                      = '';
            $geraDados['cod_produto_sap']           = '';
            $geraDados['ean']                       =  sprintf('%.0f', str_replace(",", "", $dados['registro']['equipamento_codigo_barra']) );
            $geraDados['marca']                     = $dados['registro']['equipamento_marca'];
            $geraDados['equipamento_nome']          = $dados['registro']['equipamento'];
            $geraDados['nota_fiscal_valor_desc']    = app_format_currency($dados['registro']['nota_fiscal_valor']);
            $geraDados['nota_fiscal_data']          = app_dateonly_mask_to_mysql($dados['registro']['nota_fiscal_data']);
            $geraDados['premio_liquido']            = 0;
            $geraDados['vigencia']                  = '';
            $geraDados['cod_vendedor']              = '';
            $geraDados['cpf']                       = $dados['registro']['cnpj_cpf'];
            $geraDados['nota_fiscal_numero']        = $dados['registro']['nota_fiscal_numero'];
            $geraDados['num_parcela']               = '00';
            $geraDados['nota_fiscal_valor']         = app_format_currency($dados['registro']['nota_fiscal_valor']);
            $geraDados['integracao_log_detalhe_id'] = $formato;


            //deixando campos nos padrões
            $dados['registro']['nota_fiscal_data'] = app_dateonly_mask_to_mysql($dados['registro']['nota_fiscal_data']);
            $dados['registro']['data_nascimento'] = app_dateonly_mask_to_mysql($dados['registro']['data_nascimento']);


//$linhas = str_replace("'", " ", fgets($fh, 4096));

            $geraDados['integracao_log_detalhe_id'] = $formato;

            //unset($geraDados['id_log']);
            //unset($geraDados['tipo_arquivo']);

            $CI->load->model("integracao_log_detalhe_dados_model", "integracao_log_detalhe_dados");
            $CI->integracao_log_detalhe_dados->insLogDetalheDados($geraDados);
        }

        $cpf = $dados['registro']['cnpj_cpf'];
        $ean = sprintf('%.0f', str_replace(",", "", $dados['registro']['equipamento_codigo_barra']) );

        echo "****************** CPF: $cpf<br>";

        $acesso = app_integracao_rastrecall_sms_dados();
        $dados['registro']['produto_parceiro_id'] = $acesso->produto_parceiro_id;
        $dados['registro']['produto_parceiro_plano_id'] = $acesso->produto_parceiro_plano_id;
        $eanErro = true;
        $eanErroMsg = "";


        // usar a pesquisa por nome
        $CI->load->model("integracao_log_detalhe_model", "integracao_log_detalhe");


       // exit(app_format_telefone_unitfour(app_retorna_numeros($dados['registro']['segurado_celular'])));
        // valida Celular para geração da cotação
        if ( app_validate_mobile_phone(app_format_telefone_unitfour(app_retorna_numeros($dados['registro']['telefone']))) ) {


            if (!empty($cpf)) $cpf = substr($cpf, -11);

            if( !app_validate_cpf($cpf) ){
                $response->msg[] = ['id' =>  2, 'msg' => "Campo CPF deve ser um CPF válido [{$cpf}]", 'slug' => 'cnpj_cpf'];
                return $response;
            }

            if (!empty($ean)) {
                $ean = (int)$ean;
                $EANenriquecido = app_get_api("enriqueceEAN/$ean", 'GET', [], $acesso);
                //echo "<pre>";print_r($EANenriquecido);echo "</pre>";
                 //exit('ERRO');
                if (!empty($EANenriquecido['status'])){
                    $EANenriquecido = $EANenriquecido['response'];
                    $response->ean = $EANenriquecido;
                    $eanErro = false;

                    $dados['registro']['equipamento_id'] = $EANenriquecido->equipamento_id;
                    $dados['registro']['equipamento_marca_id'] = $EANenriquecido->equipamento_marca_id;
                    $dados['registro']['equipamento_categoria_id'] = $EANenriquecido->equipamento_categoria_id;
                    $dados['registro']['equipamento_sub_categoria_id'] = $EANenriquecido->equipamento_sub_categoria_id;
                    $dados['registro']['imei'] = sprintf('%.0f', str_replace(",", "", $dados['registro']['equipamento_imei']) );
                } else {
                    $inputField = [
                        'marca' => $dados['registro']['equipamento_marca'],
                        'modelo' => $dados['registro']['equipamento'],
                        'quantidade' => 1,
                        'emailAPI' => app_get_userdata("email"),
                    ];

                    $EANenriquecido = app_get_api("enriqueceModelo", "POST", json_encode($inputField), $acesso);
                    // echo "<pre>";print_r($EANenriquecido);echo "</pre>";exit;
                    if (!empty($EANenriquecido['status'])){
                        $EANenriquecido = $EANenriquecido['response']->dados[0];
                        $response->ean = $EANenriquecido;
                        $eanErro = false;

                        $dados['registro']['equipamento_id'] = $EANenriquecido->equipamento_id;
                        $dados['registro']['equipamento_marca_id'] = $EANenriquecido->equipamento_marca_id;
                        $dados['registro']['equipamento_categoria_id'] = $EANenriquecido->equipamento_categoria_id;
                        $dados['registro']['equipamento_sub_categoria_id'] = $EANenriquecido->equipamento_sub_categoria_id;
                        $dados['registro']['imei'] = sprintf('%.0f', str_replace(",", "", $dados['registro']['equipamento_imei']) );
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

        } else {
            //exit("Campo Celular deve ser um Celular válido [{$dados['registro']['segurado_celular']}]");
            $response->msg[] = ['id' =>  18, 'msg' => "Campo Celular deve ser um Celular válido [{$dados['registro']['telefone']}]", 'slug' => 'celular'];
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
        $validaRegra = app_integracao_rastrecall_valida_regras($dados, $camposCotacao);

        if (!empty($validaRegra->status)) {

            $dados['registro']['cotacao_id'] = !empty($validaRegra->cotacao_id) ? $validaRegra->cotacao_id : 0;
            $dados['registro']['fields'] = $validaRegra->fields;

            //Faz o Envio do SMS
            $evento                          = array();
            $evento['mensagem']['nome']      = $dados['registro']['nome'];
            $evento['destinatario_email']    = $dados['registro']['email'];
            $evento['destinatario_telefone'] = $dados['registro']['telefone'];
            $evento['produto_parceiro_id']   = $dados['registro']['produto_parceiro_id'];

            app_get_userdata("email");


            $evento['url'] = $CI->auth->generate_page_token(
                $acesso->token
                , array(
                    base_url("admin/venda_equipamento/equipamento/{$evento['produto_parceiro_id']}/1/{$dados['registro']['cotacao_id']}"),
                    base_url("admin/venda_equipamento/equipamento/{$evento['produto_parceiro_id']}/2/{$dados['registro']['cotacao_id']}"),
                    base_url("admin/venda_equipamento/equipamento/{$evento['produto_parceiro_id']}/3/{$dados['registro']['cotacao_id']}"),
                    base_url("admin/venda_equipamento/equipamento/{$evento['produto_parceiro_id']}/4/{$dados['registro']['cotacao_id']}"),
                    base_url("admin/venda_equipamento/equipamento/{$evento['produto_parceiro_id']}/5/{$dados['registro']['cotacao_id']}"),
                    base_url("admin/venda_equipamento/calculo"),
                    base_url("admin/gateway/consulta"),


                )
                , 'front'
                , ''
                , base_url("admin/venda_equipamento/equipamento/{$evento['produto_parceiro_id']}/2/{$dados['registro']['cotacao_id']}")
            );


            $short_url = new Short_url();
            $evento['url'] = $short_url::shorter($evento['url']);

            $comunicacao = new Comunicacao();
            $comunicacao->setMensagemParametros($evento['mensagem']);
            $comunicacao->setDestinatario(app_retorna_numeros($evento['destinatario_telefone']));
            $comunicacao->setNomeDestinatario($evento['mensagem']['nome']);
            $comunicacao->setUrl($evento['url']);
            $comunicacao->SetCotacaoId($dados['registro']['cotacao_id']);
            $comunicacao->disparaEvento("cotacao_gerada", $evento['produto_parceiro_id']);


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
            $geraDados['endereco']                  = $dados['registro']['endereco_logradouro'];
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
                $geraDados['data_cancelamento']         = $dados['registro']['data_adesao_cancel'];
                $dados['registro']['data_cancelamento'] = $dados['registro']['data_adesao_cancel'];
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

            // Trata o nome da marca retornada pela LASA
            $searchWord = 'CORRIGIR';
            if(preg_match("/{$searchWord}/i", $dados['registro']['marca'])) {
                $dados['registro']['marca'] = '';
            }

        // Cancelamento
        } else if ( in_array($dados['registro']['tipo_transacao'], ['XS','XX','XD']) )
        {
            $dados['registro']['acao']              = '9';
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
        $valid = app_integracao_inicio($acesso->parceiro_id, $num_apolice, $cpf, $ean, $dados, true, $acesso);
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
            'status' => !empty($ret['status']),
            'response' => addslashes(print_r($retorno, true)),
            'retorno_amigavel' => addslashes(print_r($ret['response'], true)),
        ];
        $CI->integracao_log_detalhe_api->update($integracao_log_detalhe_api_id, $dataApi, TRUE);

        return $ret;
    }
}
if ( ! function_exists('app_integracao_rastrecall_valida_regras'))
{
    function app_integracao_rastrecall_valida_regras($dados, $camposCotacao){


        $acesso = app_integracao_rastrecall_sms_dados();
        $response = (object) ['status' => false, 'msg' => '', 'errors' => [], 'fields' => []];

        if (empty($dados['registro'])){
            $response->msg = 'Nenhum dado recebido para validação';
            return $response;
        }

        $dados = $dados['registro'];
        // echo "<pre>";print_r($dados);echo "</pre>";

        // Emissão

        $errors = $fields = [];
        $now = new DateTime(date('Y-m-d'));

        // Enriquecimento do CPF
        $cpf = substr($dados['cnpj_cpf'], -11);
        $enriquecido = app_get_api("enriqueceCPF/$cpf/". $dados['produto_parceiro_id'], 'GET', [], $acesso);

        if (!empty($enriquecido['status'])){
            $enriquecido = $enriquecido['response'];

            $dados['nome'] = $enriquecido->nome;
            $dados['sexo'] = $enriquecido->sexo;
            $dados['data_nascimento'] = $enriquecido->data_nascimento;

            // Endereço
            $ExtraEnderecos = $enriquecido->endereco;
            if( sizeof( $ExtraEnderecos ) ) {
                $rank=1000;
                $index=0;

                foreach ($ExtraEnderecos as $end) {
                    $rank = ($end->ranking <= $rank) ? $index : $rank;
                    $index++;
                }

                $dados['endereco_logradouro'] = $ExtraEnderecos[$rank]->{"endereco"};
                $dados['endereco_numero'] = $ExtraEnderecos[$rank]->{"endereco_numero"};
                $dados['complemento'] = $ExtraEnderecos[$rank]->{"endereco_complemento"};
                $dados['endereco_bairro'] = $ExtraEnderecos[$rank]->{"endereco_bairro"};
                $dados['endereco_cidade'] = $ExtraEnderecos[$rank]->{"endereco_cidade"};
                $dados['endereco_estado'] = $ExtraEnderecos[$rank]->{"endereco_uf"};
                $dados['endereco_cep'] = str_replace("-", "", $ExtraEnderecos[$rank]->{"endereco_cep"});
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
        }

        // Regras DE/PARA
        $dados['cnpj_cpf'] = $cpf;

        if (empty($dados['nome']))
            $dados['nome'] = "NOME SOBRENOME";

        if (empty($dados['endereco_estado']))
            $dados['endereco_estado'] = "SP";

        if (empty($dados['endereco_cidade']))
            $dados['endereco_cidade'] = "BARUERI";

        if (empty($dados['endereco_bairro']))
            $dados['endereco_bairro'] = $dados['endereco_cidade'];

        if (empty($dados['endereco_logradouro']))
            $dados['endereco_logradouro'] = "ALAMEDA RIO NEGRO";

        if (empty($dados['endereco_numero']))
            $dados['endereco_numero'] = '0';

        if (empty($dados['endereco_cep']))
            $dados['endereco_cep'] = '06454000';

        // if (empty($dados['sexo']))
        //     $dados['sexo'] = 'M';

        // if (empty($dados['data_nascimento'])) {
        //     $dados['data_nascimento'] = '2000-01-01';
        // } elseif (!app_validate_data_americana($dados['data_nascimento'])) {
        //     $dados['data_nascimento'] = '2000-01-01';
        // }

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
            $fields['equipamento_nome'] = $dados['equipamento'];
            if (!empty($dados['equipamento_marca_id']))
                $fields['equipamento_marca_id'] = $dados['equipamento_marca_id'];
            if (!empty($dados['equipamento_categoria_id']))
                $fields['equipamento_categoria_id'] = $dados['equipamento_categoria_id'];
            $fields['ean'] = sprintf('%.0f', str_replace(",", "", $dados['equipamento_codigo_barra']) );
            $fields['emailAPI'] = app_get_userdata("email");

            // Cotação
            $cotacao = app_get_api("insereCotacao", "POST", json_encode($fields), $acesso);

            if (empty($cotacao['status'])) {
                $response->errors = ['id' => -1, 'msg' => $cotacao['response'], 'slug' => "insere_cotacao"];
                return $response;
            }


            $cotacao = $cotacao['response'];
            $cotacao_id = $cotacao->cotacao_id;
            $response->cotacao_id = $cotacao_id;

            $response->fields = $fields;
        }

        if (!empty($errors)) {
            $response->errors = $errors;
            return $response;
        }


        $response->status = true;
        return $response;
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
                    // $d1->add(new DateInterval('P1D')); // Início de Vigência: A partir das 24h do dia em que o produto foi adquirido
                    $dados["data_inicio_vigencia"] = $d1->format('Y-m-d');

                    // Período de Vigência: 12 meses
                    $diff = $now->diff($d1);
                    if ($d2 < date("Y-m-d", strtotime("-1 year"))) {
                        $errors[] = ['id' => 5, 'msg' => "Campo DATA VENDA OU CANCELAMENTO deve ser inferior ou igual à 12 meses", 'slug' => "data_inicio_vigencia"];
                    }
                }
            }

            // Enriquecimento do CPF
            $cpf = (app_verifica_cpf_cnpj($dados['cpf']) == 'CPF') ? substr($dados['cpf'], -11) : $cpf = substr($dados['cpf'], -14);
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
                    $dados['endereco_numero'] = 'SN';

                if (empty($dados['endereco_cep']) || $dados['endereco_cep'] == "99999999")
                    $dados['endereco_cep'] = '06454000';

                // if (empty($dados['sexo']))
                //     $dados['sexo'] = 'M';

                // if (empty($dados['data_nascimento'])) {
                //     $dados['data_nascimento'] = '2000-01-01';
                // } elseif (!app_validate_data_americana($dados['data_nascimento'])) {
                //     $dados['data_nascimento'] = '2000-01-01';
                // }

            } // if ($enriqueceCPF)

            if (empty($dados['nome'])) {
                $errors[] = ['id' => 20, 'msg' => "Nome inválido/Não informado", 'slug' => "nome"];
            }

            // if (empty($dados['sexo'])) {
            //     $errors[] = ['id' => 57, 'msg' => "Sexo inválido/não informado", 'slug' => "sexo"];
            // }

            if (empty($dados['endereco_estado'])) {
                $errors[] = ['id' => 21, 'msg' => "UF inválido.", 'slug' => "uf"];
            }

            if (empty($dados['endereco_bairro'])) {
                $errors[] = ['id' => 22, 'msg' => "Bairro inválido.", 'slug' => "bairro"];
            }

            if (empty($dados['endereco_cidade'])) {
                $errors[] = ['id' => 23, 'msg' => "Cidade inválida.", 'slug' => "cidade"];
            }

            // if (empty($dados['endereco_numero'])) {
            //     $errors[] = ['id' => 24, 'msg' => "Nº logradouro inválido.", 'slug' => "numero"];
            // }

            if (empty($dados['endereco_logradouro']) || strlen($dados['endereco_logradouro']) <= 3) {
                // E-mail de: Marcos Chelotti <Marcos_Chelotti@generali.com.br> - 22 de nov. de 2019 08:37
                $dados['endereco_logradouro'] = "RUA ". $dados['endereco_logradouro'];
            }

            if (empty($dados['endereco_cep']) || $dados['endereco_cep'] == "99999999") {
                $errors[] = ['id' => 26, 'msg' => "CEP inválido.", 'slug' => "logradouro"];
            }

            // if (!empty($dados['email']) && !app_valida_email($dados['email'])) {
            //     $errors[] = ['id' => 40, 'msg' => "E-mail inválido", 'slug' => "email"];
            // }

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
                            case "validate_cnpj":
                                if( !empty($dados[$campo->nome_banco]) && !app_validate_cpf_cnpj($dados[$campo->nome_banco]) )
                                    $errors[] = ['id' => 2, 'msg' => "Campo {$campo->label} deve ser um CPF / CNPJ válido [{$dados[$campo->nome_banco]}]", 'slug' => $campo->nome_banco];
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

                $fields['produto_parceiro_id']          = $dados['produto_parceiro_id'];
                $fields['produto_parceiro_plano_id']    = $dados['produto_parceiro_plano_id'];
                $fields['data_adesao']                  = isempty($dados['data_adesao'], null);
                $fields['equipamento_nome']             = isempty($dados['equipamento_nome'], null);
                $fields['equipamento_marca_id']         = isempty($dados['equipamento_marca_id'], null);
                $fields['equipamento_categoria_id']     = isempty($dados['equipamento_categoria_id'], null);
                $fields['equipamento_sub_categoria_id'] = isempty($dados['equipamento_sub_categoria_id'], null);
                $fields['equipamento_de_para']          = isempty($dados['equipamento_de_para'], null);
                $fields['comissao_premio']              = isempty($dados['comissao_premio'], null);
                $fields['data_inicio_vigencia']         = isempty($dados['data_inicio_vigencia'], null);
                $fields['data_fim_vigencia']            = isempty($dados['data_fim_vigencia'], null);
                $fields['numero_sorte']                 = isempty($dados['num_sorte'], null);
                $fields['num_proposta_capitalizacao']   = isempty($dados['num_serie_cap'], null);
                $fields['ean']                          = isempty($dados['ean'], null);
                $fields['coberturas']                   = isempty($dados['coberturas'], []);
                $fields['emailAPI']                     = app_get_userdata("email");

                // Retirar os acentos
                if ( !empty($fields) )
                {
                    $fields = app_utf8_converter($fields);
                }

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
                $calcPremio = app_integracao_calcula_premio($cotacao_id, $dados["premio_bruto"], issetor($dados["nota_fiscal_valor"],0), $acesso, issetor($dados["premio_liquido"],0), issetor($dados["valor_iof"],0), NULL, 0, $fields['coberturas'] );
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
    function app_integracao_calcula_premio($cotacao_id, $premio_bruto, $is, $acesso = null, $premio_liquido = NULL, $valor_iof = NULL, $valor_fixo = NULL, $qtde = 0, $coberturas = [])
    {
        $fields = [
            'cotacao_id' => $cotacao_id,
            'valor_fixo' => $valor_fixo,
            'coberturas' => $coberturas,
        ];

        // Cálculo do prêmio
        $calcPremio = app_get_api("calculo_premio", "POST", json_encode($fields), $acesso);
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

        echo "Calculo do Premio: $valor_premio | $premio_bruto | $premio_liquido | $valor_iof | $valor_fixo<br>";

        if ($valor_premio != $premio_bruto) {
            if ($valor_premio >= $premio_bruto-$dif_accept && $valor_premio <= $premio_bruto+$dif_accept) {
                echo "dif de R$ $dif_accept - $cotacao_id<br>";

                if ( $acesso->parceiro == 'novomundo' ) {
                    $qtde++;

                    if ($qtde >= 5)
                    {
                        $premioValid = false;
                    } else 
                    {
                        // encontra o liquido com casas decimais 
                        if ($qtde == 1)
                            $novo_liquido = $premio_bruto / ( 1 + round($valor_iof / $premio_liquido, 4));
                        elseif ($qtde == 2)
                            $novo_liquido = $premio_bruto / ( 1 + truncate($valor_iof / $premio_liquido, 4));
                        elseif ($qtde == 3)
                            $novo_liquido = $premio_bruto / 1.0738;
                        else
                            $novo_liquido = $premio_liquido;
                        echo "<pre>";
                        print_r( [$qtde, $premio_liquido, $valor_iof, $premio_bruto, ( 1 + round($valor_iof / $premio_liquido, 4)), $novo_liquido] );
                        echo "<br>";

                        return app_integracao_calcula_premio($cotacao_id, $premio_bruto, $is, $acesso, $premio_liquido, $valor_iof, $novo_liquido, $qtde, $coberturas);
                    }

                } else {
                    $premioValid = true;
                }

            }else {

                $premioValid = false;

                if ($is > 0) {
                    // calcula o percentual
                    $percent = (float)$premio_bruto / (float)$is * 100;

                    echo "calculado $percent % <br>";

                    if ( $aceitaPorcentagem ) {
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

                        return app_integracao_calcula_premio($cotacao_id, $premio_bruto, $is, $acesso, $premio_liquido, $valor_iof, $valor_fixo, $qtde, $coberturas);
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
                "define_date" => $dados['data_cancelamento'], 
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
if ( ! function_exists('app_integracao_retorno_generali_pagnet'))
{
    function app_integracao_retorno_generali_pagnet($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'coderr' => [] ]; 
        if (!isset($dados['log']['nome_arquivo']) || empty($dados['log']['nome_arquivo'])) {
            $response->msg[] = ['id' => 12, 'msg' => 'Nome do Arquivo inválido', 'slug' => "erro_interno"];
            return $response;
        }

        $chave = $dados['registro']['id_log'];
        $file = $dados['log']['nome_arquivo'];
        $cod_ocorrencia = $dados['registro']['ret_cod_ocorrencia'];
        $response->coderr = $dados['registro']['ret_inconsistencia1'];
        $ret_inconsistencia = [];
        $ret_inconsistenciass = [
            $dados['registro']['ret_inconsistencia1'],
            $dados['registro']['ret_inconsistencia2'],
            $dados['registro']['ret_inconsistencia3'],
            $dados['registro']['ret_inconsistencia4'],
            $dados['registro']['ret_inconsistencia5'],
        ];

        foreach ($ret_inconsistenciass as $key => $value)
        {
            if ( !empty($value) )
                $ret_inconsistencia[] = $value;
        }

        if (empty($chave)) {
            $response->msg[] = ['id' => 12, 'msg' => 'Chave não identificada', 'slug' => "erro_interno"];
            return $response;
        }

        $CI =& get_instance();
        $CI->load->model('integracao_model');
        $CI->load->model('integracao_log_detalhe_erro_model', 'log_erro');

        // LIBERA TODOS OS QUE NAO FORAM LIDOS COMO ERRO E OS AINDA NAO FORAM LIBERADOS
        if ( in_array($cod_ocorrencia, ['005']) )
        {
            $CI->integracao_model->update_log_fail(NULL, $chave, FALSE, TRUE);

            $response->msg[] = ['id' => 12, 'msg' => 'Rejeitado pelo Banco', 'slug' => "pagnet_retorno"];

            foreach ($ret_inconsistencia as $key => $value)
            {
                $id = 12; // retorno padrao
                $descricao_erro = "Nao identificado ( {$value} )";

                // identifica o ID através do DE x PARA
                $ret = $CI->log_erro->filterByCodErroParceiro($value, 'pagnet_retorno')->get_all();
                if ( !empty($ret) )
                {
                    $id = $ret[0]['integracao_log_detalhe_erro_id'];
                    $descricao_erro = $ret[0]['nome'];
                }

                $response->msg[] = ['id' => $id, 'msg' => $descricao_erro, 'slug' => "erro_retorno"];
            }
        } else if ( in_array($cod_ocorrencia, ['003']) ) {
            $response->status = true;
            $CI->integracao_model->update_log_sucess(NULL, FALSE, $chave, 'pagnet', TRUE);
            
            $response->msg[] = ['id' => 12, 'msg' => 'Realizado/Pago', 'slug' => "pagnet_retorno"];
        }
        else{
            if ( in_array($cod_ocorrencia, ['002']) )
            {
                $response->status = 2;
                $response->msg[] = ['id' => 12, 'msg' => 'Aguardando Retorno', 'slug' => "pagnet_retorno"];
            }
            else if ( in_array($cod_ocorrencia, ['004']) )
            {
                $response->status = 2;
                $response->msg[] = ['id' => 12, 'msg' => 'Cancelado', 'slug' => "pagnet_retorno"];
            }
            else if ( in_array($cod_ocorrencia, ['006']) )
            {
                $response->status = 2;
                $response->msg[] = ['id' => 12, 'msg' => 'Pagamento Rejeitado Ja Tratado  (igual a 0.A Pagar)', 'slug' => "pagnet_retorno"];
            }
        }

        if ( in_array($cod_ocorrencia, ['999']) )
        {
            $response->status = 2;
            $response->msg[] = ['id' => 12, 'msg' => 'Titulo Inativado', 'slug' => "pagnet_retorno"];
        }
        elseif ( in_array($cod_ocorrencia, ['990']) )
        {
            $response->status = 2;
            $response->msg[] = ['id' => 12, 'msg' => 'Título rejeitado PagNet - devolvido ao sistema de origem', 'slug' => "pagnet_retorno"];
        }
        elseif ( in_array($cod_ocorrencia, ['991']) )
        {
            $response->status = 2;
            $response->msg[] = ['id' => 12, 'msg' => 'Título rejeitado PagNet - aguardando acerto PagNet', 'slug' => "pagnet_retorno"];
        }
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
        if (in_array($d['cod_tipo_mov'], [2, 7, 9, 146,'P']) ) {
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
if ( ! function_exists('app_integracao_gera_chave_pagnet')) {
    function app_integracao_gera_chave_pagnet($formato, $dados = array())
    {
        $CI =& get_instance();
        $CI->db->query("call sisconnects.sp_gera_chave_pagnet(27);");
    }
}
if ( ! function_exists('app_integracao_novo_mundo')) {
    function app_integracao_novo_mundo($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

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
            $geraDados['sexo']                      = null;
            $geraDados['data_nascimento']           = null;
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
        $valid = app_integracao_inicio($acesso->parceiro_id, $num_apolice, $cpf, $ean, $dados, true, $acesso);
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
    function app_integracao_inicio($parceiro_id, $num_apolice = '', $cpf = '', $ean = '', &$dados = array(), $enriqueEquipamento = true, $acesso = null)
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

            if ( !app_validate_cpf_cnpj($cpf) ) {
                $response->msg[] = ['id' =>  2, 'msg' => "Campo CPF/CNPJ deve ser um documento válido [{$cpf}]", 'slug' => 'cnpj_cpf'];
                return $response;
            }

            $desc_doc = app_verifica_cpf_cnpj($cpf);
            $cpf = ($desc_doc == 'CPF') ? substr($cpf, -11) : substr($cpf, -14);

            if ($enriqueEquipamento)
            {
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
                        'marca'  => $dados['registro']['marca'],
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
if ( ! function_exists('app_integracao_quero_quero')) {
    function app_integracao_quero_quero($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        // Emissão
        if ( $dados['registro']['tipo_operacao'] == 'I' )
        {
            $dados['registro']['acao'] = '1';
        // Cancelamento
        } elseif ( $dados['registro']['tipo_operacao'] == 'C' )
        {
            $dados['registro']['acao'] = '9';
        } else {

            $response->msg[] = ['id' => -1, 'msg' => "Registro recebido como {$dados['registro']['tipo_operacao']}", 'slug' => "ignorado"];
            return $response;

        }

        $reg = $dados['registro'];
        // echo "<pre>";print_r($dados['registro']);echo "</pre>";die();

        $CI =& get_instance();
        $CI->session->sess_destroy();
        $CI->session->set_userdata("operacao", "queroquero");

        if (!empty($formato)) {
            $geraDados['sexo']                  = $reg['sexo'];
            $geraDados['estado_civil']          = $reg['estado_civil'];
            $geraDados['data_nascimento']       = $reg['data_nascimento'];
            $geraDados['cpf']                   = $reg['cpf'];
            $geraDados['tipo_operacao']         = $reg['tipo_operacao'];
            $geraDados['cod_loja']              = $reg['cod_loja'];
            $geraDados['nome_loja']             = $reg['nome_loja'];
            $geraDados['nome']                  = $reg['nome'];
            $geraDados['ddd_residencial']       = $reg['ddd_residencial'];
            $geraDados['telefone']              = $reg['telefone'];
            $geraDados['endereco']              = $reg['endereco_logradouro'];
            $geraDados['endereco_numero']       = $reg['endereco_numero'];
            $geraDados['complemento']           = $reg['endereco_complemento'];
            $geraDados['endereco_bairro']       = $reg['endereco_bairro'];
            $geraDados['endereco_cidade']       = $reg['endereco_cidade'];
            $geraDados['endereco_estado']       = $reg['endereco_estado'];
            $geraDados['endereco_cep']          = $reg['endereco_cep'];
            $geraDados['premio_bruto']          = $reg['premio_bruto'];
            $geraDados['logradouro_seguro']     = $reg['logradouro_seguro'];
            $geraDados['complemento_seguro']    = $reg['complemento_seguro'];
            $geraDados['numero_seguro']         = $reg['numero_seguro'];
            $geraDados['bairro_seguro']         = $reg['bairro_seguro'];
            $geraDados['cidade_seguro']         = $reg['cidade_seguro'];
            $geraDados['estado_seguro']         = $reg['estado_seguro'];
            $geraDados['cep_seguro']            = $reg['cep_seguro'];
            $geraDados['cod_vendedor']          = $reg['cod_vendedor'];
            $geraDados['data_adesao_cancel']    = $reg['data_adesao_cancel'];
            $geraDados['data_inicio_vigencia']  = $reg['data_inicio_vigencia'];
            $geraDados['data_fim_vigencia']     = $reg['data_fim_vigencia'];
            $geraDados['nome_vendedor']         = $reg['nome_vendedor'];
            $geraDados['email']                 = $reg['email'];
            $geraDados['num_apolice']           = $reg['num_apolice'];
            $geraDados['data_cancelamento']     = $reg['data_cancelamento'];
            $geraDados['num_parcela']           = $reg['num_parcela'];
            $geraDados['produto_seg']           = $reg['produto_seg'];
            $geraDados['num_sorte']             = $reg['num_sorte'];
            $geraDados['num_serie_cap']         = $reg['num_serie_cap'];
            $geraDados['integracao_log_detalhe_id'] = $formato;

            $CI->load->model("integracao_log_detalhe_dados_model", "integracao_log_detalhe_dados");
            $CI->integracao_log_detalhe_dados->insLogDetalheDados($geraDados);

            // remove para realizar o cálculo do prêmio sem multiplicar por 12 meses
            $dados['registro']['data_fim_vigencia'] = null;
        }

        // definir operação pelo nome do arquivo ou por integracao?
        $acesso = app_integracao_quero_quero_define_operacao($reg['produto_seg']);
        if ( empty($acesso->status) ) {
            $response->status = 2;
            $response->msg[] = $acesso->msg;
            return $response;
        }

        // recupera as variaveis mais importantes
        $num_apolice    = $reg['num_apolice'];
        $cpf            = $reg['cpf'];
        $ean            = null;

        $dados['registro']['produto_parceiro_id']       = $acesso->produto_parceiro_id;
        $dados['registro']['produto_parceiro_plano_id'] = $acesso->produto_parceiro_plano_id;
        $dados['registro']['data_adesao']               = $dados['registro']['data_adesao_cancel'];
        $eanErro = true;
        $eanErroMsg = "";

        // validações iniciais
        $valid = app_integracao_inicio($acesso->parceiro_id, $num_apolice, $cpf, $ean, $dados, false, $acesso);
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
if ( ! function_exists('app_integracao_cap_data_sorteio')) {
    function app_integracao_cap_data_sorteio($formato, $dados = array())
    {
        $data           = null;
        $pedido_id      = issetor($dados["registro"]["pedido_id"], 0);
        $data_sorteio   = issetor($dados["registro"]["data_sorteio"], null);
        $formato        = emptyor($formato, 'dmY');

        $CI =& get_instance();
        $CI->load->model("Capitalizacao_Sorteio_Model", "capitalizacao_sorteio");

        return $CI->capitalizacao_sorteio->defineDataSorteio($pedido_id, $formato, $data_sorteio);
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
if ( ! function_exists('app_integracao_quero_quero_define_operacao')) {
    function app_integracao_quero_quero_define_operacao($produto_seg)
    {
        $result = (object) ['status' => false, 'msg' => []];

        if (empty($produto_seg)) {
            $result->msg = ['id' => -1, 'msg' => "Código do Produto Seguradora Não informado", 'slug' => "cod_prod_seg"];
            return $result;
        }

        $result->parceiro_id = 80;
        $result->email = "queroquero@sisconnects.com.br";
        $result->produto_parceiro_id = 90;

        if ($produto_seg == '3621') {
            $result->produto_parceiro_plano_id = 114;
        } elseif ($produto_seg == '3622') {
            $result->produto_parceiro_plano_id = 115;
        } else {
            $result->msg = ['id' => 39, 'msg' => "Produto ({$result->produto}) não configurado", 'slug' => "produto"];
            return $result;
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
if ( ! function_exists('app_integracao_coop_define_operacao')) {
    function app_integracao_coop_define_operacao($dados = [])
    {
        $result = (object) ['status' => false, 'msg' => []];

        $equipamento_de_para = $dados['equipamento_de_para'];
        if (empty($equipamento_de_para)) {
            $result->msg = ['id' => -1, 'msg' => "Código da Garantia Não informada", 'slug' => "equipamento_de_para"];
            return $result;
        }

        $result->parceiro_id = 104;
        $result->email = "coop@sisconnects.com.br";
        $result->produto_parceiro_id = 108;
        $result->coberturas[] = ['cobertura' => 'garantia-estendida'];

        // NAO POSSUI DANOS ELETRICOS
        if (strpos($equipamento_de_para, "X") === FALSE) 
        {
            $result->produto_parceiro_plano_id = 164;
        } else 
        {
            $result->produto_parceiro_plano_id = 165;
            $result->coberturas[] = ['cobertura' => 'danos-eletricos'];
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
if ( ! function_exists('app_integracao_retorno_cta'))
{
    function app_integracao_retorno_cta($formato, $dados = array())
    {
        $sinistro = false;
        $pagnet = false; // Ainda não tem um padrão de retorno definido. Desenvolver esta funcionalidade após concluir este desenvolvimento do pagnet
        $response = (object) ['status' => false, 'msg' => [], 'coderr' => [] ]; 
        if (!isset($dados['log']['nome_arquivo']) || empty($dados['log']['nome_arquivo'])) {
            $response->msg[] = ['id' => 12, 'msg' => 'Nome do Arquivo inválido', 'slug' => "erro_interno"];
            return $response;
        }

        //Remove os caracteres não imprimíveis
        $dados['registro']['descricao_erro'] = preg_replace( '/[^[:print:]\r\n]/', '?',$dados['registro']['descricao_erro']);
        $data_processado    = date('d/m/Y', strtotime($dados['registro']['data_processado']));
        $mensagem_registro  = 'Cod: ' . $dados['registro']['cod_erro'] . ' - Mensagem: ' . $dados['registro']['descricao_erro'] . ' - Processado em: '. $data_processado;
        $chave              = $dados['registro']['id_log'];
        $file_registro      = $dados['registro']['nome_arquivo'];

        if (empty($chave))
        {
            $response->msg[] = ['id' => 12, 'msg' => 'Chave não identificada', 'slug' => "erro_interno"];
            return $response;
        }

        $CI =& get_instance();
        $CI->load->model('integracao_model');
        $CI->load->model('integracao_log_detalhe_erro_model', 'log_erro');

        $proc = $CI->integracao_model->detectFileRetorno($file_registro);
        $file = $proc['file'];
        $sinistro = ($proc['tipo'] == 'SINISTRO');

        //A - Acatado com sucesso (id=[4]), R - Rejeitado (Erro => id=[5]) ou P - Pendente (id=[3])
        if (!empty($dados['registro']['status']))
        {
            // Retorna o codigo e descrição do status de retorno do arquivo 
            $response->coderr = $dados['registro']['cod_erro']; 
            $response->msg[] = ['id' => 12, 'msg' => $dados['registro']['cod_erro'] ." - ". $dados['registro']['descricao_erro'], 'slug' => "erro_retorno"];

            if($dados['registro']['status'] == 'A'){
                $CI->integracao_model->update_log_detalhe_cta($file_registro, $chave, '4', $mensagem_registro, $sinistro, $pagnet);

                $response->status = true;
                return $response;
            }elseif($dados['registro']['status'] == 'R'){   
                $CI->integracao_model->update_log_detalhe_cta($file_registro, $chave, '5', $mensagem_registro, $sinistro, $pagnet);
                return $response;
            }elseif($dados['registro']['status'] == 'P'){
                $CI->integracao_model->update_log_detalhe_cta($file_registro, $chave, '3', $mensagem_registro, $sinistro, $pagnet);

                $response->status = true;
                return $response;
            }else{
                $response->msg[] = ['id' => 12, 'msg' => 'Status não identificado'];
                return $response;
            }
            return true;
        }else{
            $response->msg[] = ['id' => 12, 'msg' => 'Registro sem status definido'];
            return $response;
        }
    }
}
if ( ! function_exists('app_integracao_mailing')) {
    function app_integracao_mailing($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        $CI =& get_instance();
        $CI->session->sess_destroy();
        $CI->session->set_userdata("operacao", "bidu");
        $CI->load->model('cliente_contato_model', 'cliente_contato');
        $acesso = app_integracao_generali_dados();

        $reg = $dados['registro'];
        $reg['produto_parceiro_id'] = $acesso->produto_parceiro_id;
        $reg['documento'] = app_retorna_numeros(emptyor( $reg['cpf'] , $reg['cnpj'] ));

        if (empty($reg['documento']) || !app_validate_cpf_cnpj($reg['documento']) ){
            $response->msg[] = ['id' => -1, 'msg' => "O ". app_verifica_cpf_cnpj($reg['documento']) ." [{$reg['documento']}] não é válido", 'slug' => "cliente"];
            return $response;
        }

        if (!empty($formato)) {
            // valida o melhor horario
            $melhor_horario = 'Q';
            if ( !empty($POST['melhor_horario']) )
            {
                $melhor_horario = $CI->cliente_contato->melhorHorario( $reg['melhor_horario'], 'nome', 'slug' );
            }

            $mei = 'N';
            if (!empty($reg['mei']))
            {
                $mei = $reg['mei'] == 'SIM' ? 'S' : 'N';
            }

            $geraDados['integracao_log_detalhe_id'] = $formato;
            $geraDados['codigo']                    = $reg['codigo'];
            $geraDados['data']                      = $reg['data_mailing'];
            $geraDados['nome']                      = $reg['nome'];
            $geraDados['sexo']                      = $reg['sexo'];
            $geraDados['profissao']                 = $reg['profissao'];
            $geraDados['email']                     = $reg['email'];
            $geraDados['telefone']                  = $reg['telefone'];
            $geraDados['telefone_2']                = $reg['telefone_2'];
            $geraDados['melhor_horario']            = $melhor_horario;
            $geraDados['data_nascimento']           = $reg['data_nascimento'];
            $geraDados['documento']                 = $reg['documento'];
            $geraDados['tipo_pessoa']               = !empty($reg['cnpj']) ? 'PJ' : 'PF';
            $geraDados['0_a_18_anos']               = $reg['0_a_18_anos'];
            $geraDados['19_a_23_anos']              = $reg['19_a_23_anos'];
            $geraDados['24_a_28_anos']              = $reg['24_a_28_anos'];
            $geraDados['29_a_33_anos']              = $reg['29_a_33_anos'];
            $geraDados['34_a_38_anos']              = $reg['34_a_38_anos'];
            $geraDados['39_a_43_anos']              = $reg['39_a_43_anos'];
            $geraDados['44_a_48_anos']              = $reg['44_a_48_anos'];
            $geraDados['49_a_53_anos']              = $reg['49_a_53_anos'];
            $geraDados['54_a_58_anos']              = $reg['54_a_58_anos'];
            $geraDados['59_anos_ou_mais']           = $reg['59_anos_ou_mais'];
            $geraDados['cidade']                    = $reg['cidade'];
            $geraDados['uf']                        = $reg['uf'];
            $geraDados['tem_plano_saude']           = $reg['tem_plano_saude'];
            $geraDados['operadora_atual']           = $reg['operadora_atual'];
            $geraDados['mei']                       = $mei;
            $geraDados['razao_social']              = $reg['razao_social'];
            $geraDados['status']                    = $reg['status'];

            $CI->load->model("cliente_mailing_model", "cliente_mailing");
            $CI->cliente_mailing->insert($geraDados, TRUE);
        }

        // Campos para cotação
        $campos = app_get_api("cliente", 'POST', json_encode($reg), $acesso);
        if (empty($campos['status'])){
            $response->msg[] = ['id' => -1, 'msg' => $campos['response'], 'slug' => "cliente"];
            return $response;
        }

        $response->status = true;
        return $response;
    }
}
if ( ! function_exists('app_integracao_mailing_adesao')) {
    function app_integracao_mailing_adesao($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        $reg = $dados['registro'];

        $CI =& get_instance();
        $CI->session->sess_destroy();
        $CI->session->set_userdata("operacao", "bidu");

        if (!empty($formato)) {
            $geraDados['integracao_log_detalhe_id']     = $formato;
            $geraDados['codigo']                        = $reg['codigo'];
            $geraDados['nome_titular']                  = $reg['nome_titular'];
            $geraDados['nome']                          = $reg['nome'];
            $geraDados['tipo_segurado']                 = $reg['tipo_segurado'];
            $geraDados['situacao_beneficiario']         = $reg['situacao_beneficiario'];
            $geraDados['data_cancelamento']             = $reg['data_cancelamento'];
            $geraDados['operadora']                     = $reg['operadora'];
            $geraDados['num_apolice']                   = $reg['num_apolice'];
            $geraDados['data_inicio_vigencia']          = $reg['data_inicio_vigencia'];
            $geraDados['entidade']                      = $reg['entidade'];
            $geraDados['plano_operadora']               = $reg['plano_operadora'];
            $geraDados['acomodacao']                    = $reg['acomodacao'];
            $geraDados['email']                         = $reg['email'];
            $geraDados['telefone']                      = $reg['telefone'];
            $geraDados['cpf']                           = $reg['cpf'];
            $geraDados['rg']                            = $reg['rg'];
            $geraDados['rg_data_expedicao']             = $reg['rg_data_expedicao'];
            $geraDados['rg_orgao_expedidor']            = $reg['rg_orgao_expedidor'];
            $geraDados['nome_mae']                      = $reg['nome_mae'];
            $geraDados['data_nascimento']               = $reg['data_nascimento'];
            $geraDados['estado_civil']                  = $reg['estado_civil'];
            $geraDados['sexo']                          = $reg['sexo'];
            $geraDados['parentesco']                    = $reg['parentesco'];
            $geraDados['peso']                          = $reg['peso'];
            $geraDados['altura']                        = $reg['altura'];
            $geraDados['endereco_logradouro']           = $reg['endereco_logradouro'];
            $geraDados['endereco_numero']               = $reg['endereco_numero'];
            $geraDados['complemento']                   = $reg['endereco_complemento'];
            $geraDados['endereco_bairro']               = $reg['endereco_bairro'];
            $geraDados['endereco_cidade']               = $reg['endereco_cidade'];
            $geraDados['endereco_cep']                  = $reg['endereco_cep'];
            $geraDados['endereco_estado']               = $reg['uf'];
            $geraDados['data_adesao_cancel']            = $reg['data_adesao_cancel'];
            $geraDados['declaracao_nascido_vivo']       = $reg['declaracao_nascido_vivo'];
            $geraDados['cns']                           = $reg['cns'];
            $geraDados['pis']                           = $reg['pis'];
            $geraDados['operadora_anterior']            = $reg['operadora_anterior'];
            $geraDados['plano_anterior']                = $reg['plano_anterior'];
            $geraDados['acomodacao_anterior']           = $reg['acomodacao_anterior'];
            $geraDados['dia_vencimento']                = $reg['dia_vencimento'];
            $geraDados['reducao_carencia']              = $reg['reducao_carencia'];
            $geraDados['cpf_vendedor']                  = $reg['cpf_vendedor'];
            $geraDados['taxa_convenio']                 = $reg['taxa_convenio'];
            $geraDados['valor_saude']                   = $reg['valor_saude'];
            $geraDados['cod_faixa_preco']               = $reg['codigo_tabela_preco_saude'];
            $geraDados['modalidade_pagto']              = $reg['modalidade_pagto'];
            $geraDados['numero_proposta']               = $reg['numero_proposta'];
            $geraDados['codigo_plano']                  = $reg['codigo_plano'];
            $geraDados['id_plano']                      = $reg['id_plano'];
            $geraDados['plataforma_venda']              = $reg['plataforma_venda'];
            $geraDados['boleto_empresarial']            = $reg['boleto_empresarial'];
            $geraDados['cnpj']                          = $reg['cnpj'];
            $geraDados['data_vencimento']               = $reg['data_vencimento'];
            $geraDados['status_vencimento']             = $reg['status_vencimento'];
            $geraDados['data_pagamento']                = $reg['data_pagamento'];

            $CI->load->model("integracao_log_detalhe_dados_model", "integracao_log_detalhe_dados");
            $CI->integracao_log_detalhe_dados->insLogDetalheDados($geraDados);
        }

        $response->status = true;
        return $response;
    }
}
if ( ! function_exists('app_integracao_mailing_adesao_emitir')) {
    function app_integracao_mailing_adesao_emitir($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        $CI =& get_instance();

        if ( empty($dados['log']['integracao_log_id']) )
        {
            return $response;
        }
        $integracao_log_id = $dados['log']['integracao_log_id'];

        $CI->load->model('integracao_log_detalhe_dados_model', 'log_dados');
        $CI->load->model('produto_parceiro_plano_precificacao_itens_model', 'preco_item');
        $emissoes = $CI->log_dados->getDadosByArquivo($integracao_log_id, null, ['T','E'])->get_all();

        $acesso = app_integracao_generali_dados();
        
        foreach ($emissoes as $dados)
        {

            if ( app_verifica_cpf_cnpj(emptyor( $dados['cpf'] , $dados['cnpj'] )) == 'CPF' )
            {
                $acesso->produto_slug = 'plano_de_saude_hapvida';
                $acesso->plano_slug = 'nosso_plano_hapvida';
                $produto_parceiro_plano_id = 46;
            } else 
            {
                $acesso->produto_slug = 'plano_de_saude_hapvida_empresarial';
                $acesso->plano_slug = 'nosso_plano_hapvida_empresarial';
                $produto_parceiro_plano_id = 157;
            }

            $dados['produto_slug'] = $acesso->produto_slug;
            $dados['plano_slug'] = $acesso->plano_slug;
            $beneficiarios = $faixa_etaria = [];

            $tabela = $CI->preco_item->getTabelaFixaGenerico($produto_parceiro_plano_id, 1, null, $dados['data_nascimento']);
            if ( !empty($tabela) )
            {
                $inicial = (int)$tabela['inicial'];
                $final = (int)$tabela['final'];
                $faixa_etaria["{$inicial}_a_{$final}"] = empty($faixa_etaria["{$inicial}_a_{$final}"]) ? 1 : $faixa_etaria["{$inicial}_a_{$final}"] + 1;
            }

            // Monta a faixa etaria recebida
            // if ( !empty($dados['0_a_18_anos']) ) $faixa_etaria['0_a_18'] = $dados['0_a_18_anos'];
            // if ( !empty($dados['19_a_23_anos']) ) $faixa_etaria['19_a_23'] = $dados['19_a_23_anos'];
            // if ( !empty($dados['24_a_28_anos']) ) $faixa_etaria['24_a_28'] = $dados['24_a_28_anos'];
            // if ( !empty($dados['29_a_33_anos']) ) $faixa_etaria['29_a_33'] = $dados['29_a_33_anos'];
            // if ( !empty($dados['34_a_38_anos']) ) $faixa_etaria['34_a_38'] = $dados['34_a_38_anos'];
            // if ( !empty($dados['39_a_43_anos']) ) $faixa_etaria['39_a_43'] = $dados['39_a_43_anos'];
            // if ( !empty($dados['44_a_48_anos']) ) $faixa_etaria['44_a_48'] = $dados['44_a_48_anos'];
            // if ( !empty($dados['49_a_53_anos']) ) $faixa_etaria['49_a_53'] = $dados['49_a_53_anos'];
            // if ( !empty($dados['54_a_58_anos']) ) $faixa_etaria['54_a_58'] = $dados['54_a_58_anos'];
            // if ( !empty($dados['59_anos_ou_mais']) ) $faixa_etaria['59_a_200'] = $dados['59_anos_ou_mais'];

            // $qtde_beneficiarios = 0;
            // foreach ($faixa_etaria as $key => $value) {
            //     $qtde_beneficiarios += $value;
            // }

            $reg = $CI->log_dados->getDadosByArquivo($integracao_log_id, $dados['codigo'], ['D'])->get_all();
            
            // if ( count($reg) != $qtde_beneficiarios )
            // {
            //     $response->msg[] = ['id' => -1, 'msg' => "A quantidade de beneficiários informada [". count($reg) ."] diverge da quantidade inicial [". $qtde_beneficiarios ."] ", 'slug' => "cliente"];
            //     return $response;
            // }

            // Caso possua beneficiarios
            if ( !empty($reg))
            {
                foreach ($reg as $benef)
                {
                    $benef['documento'] = app_retorna_numeros(emptyor( $benef['cpf'] , $benef['cnpj'] ));
                    $beneficiarios[] = [
                        "cnpj_cpf" => $benef['documento'],
                        "email" => $benef['email'],
                        "nome" => $benef['nome'],
                        "sexo" => $benef['sexo'],
                        "telefone" => $benef['telefone'],
                        "data_nascimento" => $benef['data_nascimento'],
                        "rg" => $benef['rg'],
                        "estado_civil" => $benef['estado_civil'],
                        "nome_mae" => $benef['nome_mae'],
                    ];

                    $tabela = $CI->preco_item->getTabelaFixaGenerico($produto_parceiro_plano_id, 1, null, $benef['data_nascimento']);
                    if ( !empty($tabela) )
                    {
                        $inicial = (int)$tabela['inicial'];
                        $final = (int)$tabela['final'];
                        $faixa_etaria["{$inicial}_a_{$final}"] = empty($faixa_etaria["{$inicial}_a_{$final}"]) ? 1 : $faixa_etaria["{$inicial}_a_{$final}"] + 1;
                    }
                }
            }

            $dados['documento'] = app_retorna_numeros(emptyor( $dados['cpf'] , $dados['cnpj'] ));
            $sendData = [
                "produto_slug" => $dados['produto_slug'],
                "plano_slug" => $dados['plano_slug'],
                "campos" => [
                    [
                        "cnpj_cpf" => $dados['documento'],
                        "email" => $dados['email'],
                        "nome" => $dados['nome'],
                        "sexo" => $dados['sexo'],
                        "telefone" => $dados['telefone'],
                        "data_nascimento" => $dados['data_nascimento'],
                        "faixa_etaria" => $faixa_etaria,
                        "rg" => $dados['rg'],
                        "estado_civil" => $dados['estado_civil'],
                        "nome_mae" => $dados['nome_mae'],
                        "endereco_cep" => $dados['endereco_cep'],
                        "endereco_logradouro" => $dados['endereco_logradouro'],
                        "endereco_numero" => $dados['endereco_numero'],
                        "endereco_bairro" => $dados['endereco_bairro'],
                        "endereco_cidade" => $dados['endereco_cidade'],
                        "beneficiarios" => $beneficiarios
                    ]
                ],
                "meiopagamento" => [
                    "meio_pagto_slug" => "cobranca_terceiros",
                    "campos" => []
                ]
            ];

            // Campos para cotação
            $campos = app_get_api("emissao", 'POST', json_encode($sendData), $acesso);
            if (empty($campos['status'])){
                $response->msg[] = ['id' => -1, 'msg' => $campos['response'], 'slug' => "cliente"];
            }
        }

        // Caso houve alguma inconsistência
        if ( !empty($response->msg) )
        {
            return $response;
        }

        $response->status = true;
        return $response;

    }
}
if ( ! function_exists('app_integracao_coop')) {
    function app_integracao_coop($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'cpf' => [], 'ean' => []];

        // Emissão
        if ( $dados['registro']['tipo_transacao'] == 'FC' )
        {
            $dados['registro']['acao'] = '1';

        // Cancelamento
        } elseif ( $dados['registro']['tipo_transacao'] == 'NC' )
        {
            $dados['registro']['acao'] = '9';
        } else {
            $dados['registro']['acao'] = '0';
        }

        $reg = $dados['registro'];
        // print_pre($reg, false);

        $CI =& get_instance();
        $CI->session->sess_destroy();
        $CI->session->set_userdata("operacao", "coop");
        $vigencia = $reg['garantia_total'] - $reg['garantia_fabricante'];

        if (!empty($formato))
        {
            $geraDados['tipo_transacao']            = $reg['tipo_transacao'];
            $geraDados['tipo_operacao']             = $reg['acao'];
            $geraDados['cod_loja']                  = $reg['cod_loja'];
            $geraDados['num_apolice']               = $reg['num_apolice'];
            $geraDados['nota_fiscal_numero']        = $reg['nota_fiscal_numero'];
            $geraDados['cod_vendedor']              = $reg['cod_vendedor'];
            $geraDados['nome']                      = $reg['nome'];
            $geraDados['sexo']                      = null;
            $geraDados['data_nascimento']           = null;
            $geraDados['telefone']                  = $reg['telefone'];
            $geraDados['endereco']                  = $reg['endereco_logradouro'];
            $geraDados['endereco_numero']           = $reg['endereco_numero'];
            $geraDados['complemento']               = $reg['complemento'];
            $geraDados['endereco_bairro']           = $reg['endereco_bairro'];
            $geraDados['endereco_cidade']           = $reg['endereco_cidade'];
            $geraDados['endereco_estado']           = $reg['endereco_estado'];
            $geraDados['endereco_cep']              = $reg['endereco_cep'];
            $geraDados['tipo_pessoa']               = (app_verifica_cpf_cnpj($reg['cpf']) == 'CNPJ') ? 'PJ' : 'PF';
            $geraDados['cpf']                       = $reg['cpf'];
            $geraDados['premio_bruto']              = $reg['premio_bruto'];
            $geraDados['num_parcela']               = 1;
            $geraDados['vigencia']                  = $vigencia;
            $geraDados['garantia_fabricante']       = $reg['garantia_fabricante'];
            $geraDados['marca']                     = $reg['marca'];
            $geraDados['modelo']                    = $reg['modelo'];
            $geraDados['cod_produto_sap']           = $reg['cod_produto_sap'];
            $geraDados['equipamento_nome']          = $reg['equipamento_nome'];
            $geraDados['num_serie']                 = $reg['num_serie'];
            $geraDados['nota_fiscal_data']          = $reg['nota_fiscal_data'];
            $geraDados['data_adesao_cancel']        = $reg['data_adesao_cancel'];
            $geraDados['nota_fiscal_valor']         = $reg['nota_fiscal_valor'];
            $geraDados['cod_faixa_preco']           = $reg['equipamento_de_para'];
            $geraDados['num_sorte']                 = $reg['num_sorte'];
            $geraDados['num_serie_cap']             = $reg['num_serie_cap'];
            $geraDados['rg']                        = $reg['rg'];
            $geraDados['rg_data_expedicao']         = $reg['rg_data_expedicao'];
            $geraDados['rg_orgao_expedidor']        = $reg['rg_orgao_expedidor'];
            $geraDados['integracao_log_detalhe_id'] = $formato;

            // Cancelamento
            if ( $reg['acao'] == '9' ) {
                $geraDados['data_cancelamento']         = $dados['registro']['nota_fiscal_data'];
                $dados['registro']['data_cancelamento'] = $dados['registro']['nota_fiscal_data'];
            }

            $CI->load->model("integracao_log_detalhe_dados_model", "integracao_log_detalhe_dados");
            $CI->integracao_log_detalhe_dados->insLogDetalheDados($geraDados);
        }

        // Valida o tipo de transacao
        if ( $reg['acao'] == '0' )
        {
            $response->msg[] = ['id' => -1, 'msg' => "Registro recebido como {$dados['registro']['tipo_transacao']}", 'slug' => "ignorado"];
            return $response;
        }

        // definir dados da operação
        $acesso = app_integracao_coop_define_operacao($reg);
        if ( empty($acesso->status) )
        {
            $response->status = 2;
            $response->msg[] = $acesso->msg;
            return $response;
        }

        // Emissao
        if ( $reg['acao'] == '1' )
        {
            // gerar as vigências de GarantiaEstendida
            $idx = app_search( $acesso->coberturas, 'garantia-estendida', 'cobertura' );
            if ( $idx >= 0 )
            {
                // inicio da vigencia
                $dGE = new DateTime($reg["nota_fiscal_data"]);
                $dGE->add(new DateInterval("P{$reg['garantia_fabricante']}M")); //Adiciona a garantia do fabricante à data da nf
                // $dGE->add(new DateInterval("P1D")); //Adiciona a garantia do fabricante à data da nf
                $ini_vig_ge = $dGE->format('Y-m-d');

                // Fim da vigencia
                $dGE->add(new DateInterval("P{$vigencia}M")); //Adiciona a garantia do fabricante à data da nf
                $fim_vig_ge = $dGE->format('Y-m-d');

                $dados["registro"]["data_inicio_vigencia"] = $ini_vig_ge;
                $dados["registro"]["data_fim_vigencia"] = $fim_vig_ge;

                $acesso->coberturas[$idx]['data_inicio_vigencia'] = $ini_vig_ge;
                $acesso->coberturas[$idx]['data_fim_vigencia'] = $fim_vig_ge;
            }

            // gerar as vigências de DanosEletricos
            $idx = app_search( $acesso->coberturas, 'danos-eletricos', 'cobertura' );
            if ( $idx >= 0 )
            {
                $ini_vig_de = $reg['data_adesao_cancel'];
                $dDE = new DateTime($ini_vig_ge);
                $dDE->sub(new DateInterval("P1D")); //1 dia antes do inicio da vigencia da GE
                $fim_vig_de = $dDE->format('Y-m-d');

                $acesso->coberturas[$idx]['data_inicio_vigencia'] = $ini_vig_de;
                $acesso->coberturas[$idx]['data_fim_vigencia'] = $fim_vig_de;
            }
        }

        $dados["registro"]["coberturas"] = $acesso->coberturas;

        // recupera as variaveis mais importantes
        $num_apolice    = $reg['num_apolice'];
        $cpf            = $reg['cpf'];

        $dados['registro']['produto_parceiro_id']       = $acesso->produto_parceiro_id;
        $dados['registro']['produto_parceiro_plano_id'] = $acesso->produto_parceiro_plano_id;
        $dados['registro']['data_adesao']               = $dados['registro']['data_adesao_cancel'];
        $dados['registro']['equipamento_de_para']       = $vigencia . $dados['registro']['equipamento_de_para'];
        $dados['registro']['endereco_numero']           = emptyor($dados['registro']['endereco_numero'], '-'); // definição do Lee-GBS para qdo nao enviar o número não rejeitar
        $eanErro = true;
        $eanErroMsg = "";

        // validações iniciais
        $valid = app_integracao_inicio($acesso->parceiro_id, $num_apolice, $cpf, null, $dados, true, $acesso);
        if ( $valid->status !== true )
        {
            $response = $valid;
            return $response;
        }

        // Campos para cotação
        $camposCotacao = app_get_api("cotacao_campos/". $acesso->produto_parceiro_id, 'GET', [], $acesso);
        if (empty($camposCotacao['status']))
        {
            $response->msg[] = ['id' => -1, 'msg' => $camposCotacao['response'], 'slug' => "cotacao_campos"];
            return $response;
        }

        $camposCotacao = $camposCotacao['response'];

        // Validar Regras
        $validaRegra = app_integracao_valida_regras($dados, $camposCotacao, false, $acesso);
        // echo "<pre>";print_r($validaRegra);echo "</pre>";die();

        if (!empty($validaRegra->status))
        {
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
if ( ! function_exists('app_integracao_icatu_pedido'))
{
    function app_integracao_icatu_pedido($formato, $dados = array())
    {
        if ( empty($dados['registro']) )
        {
            return false;
        }

        $CI =& get_instance();
        $CI->load->model('capitalizacao_serie_model');
        $CI->load->model('integracao_log_model');

        // trava uma nova solicitação de range até obter retorno
        $CI->capitalizacao_serie_model->update($dados['registro']['capitalizacao_serie_id'], ['solicita_range' => 2], TRUE);

        // para controle do sequencial por codigo do porduto
        $CI->integracao_log_model->update($dados['log']['integracao_log_id'], ['retorno' => $dados['registro']['cod_produto']], TRUE);

        return true;
    }
}
if ( ! function_exists('app_integracao_icatu_sequencia'))
{
    function app_integracao_icatu_sequencia($formato, $dados = array())
    {
        if( !empty($dados['registro']['cod_produto']) && !empty($dados['log']['integracao_id']) )
        {
            $CI =& get_instance();
            $CI->load->model('integracao_log_model');
            return $CI->integracao_log_model->get_sequencia_by_cod_prod($dados['log']['integracao_id'], $dados['registro']['cod_produto']);
        }else{
            return 1;
        }
    }
}
if ( ! function_exists('app_integracao_icatu_pedido_retorno'))
{
    function app_integracao_icatu_pedido_retorno($formato, $dados = array())
    {
        $response = (object) ['status' => false, 'msg' => [], 'coderr' => [] ]; 
        if (!isset($dados['log']['nome_arquivo']) || empty($dados['log']['nome_arquivo'])) {
            $response->msg[] = ['id' => 12, 'msg' => 'Nome do Arquivo inválido', 'slug' => "erro_interno"];
            return $response;
        }

        // Dados de entrada
        $cod_produto        = $dados['registro']['cod_produto'];
        $num_sorte          = $dados['registro']['num_sorte'];
        $num_lote        = $dados['registro']['num_lote'];
        $ret_inconsistencia = [];
        $codsEr      = [
            $dados['registro']['cod_erro1'],
            $dados['registro']['cod_erro2'],
            $dados['registro']['cod_erro3'],
        ];

        foreach ($codsEr as $key => $value) {
            if ( $value != '000' )
                $ret_inconsistencia[] = $value;
        }

        $CI =& get_instance();
        $CI->load->model('integracao_log_detalhe_erro_model', 'log_erro');
        $CI->load->model('capitalizacao_serie_model', 'capitalizacao_serie');
        $CI->load->model('capitalizacao_serie_titulo_model', 'titulo');

        if (!empty($codigos_erros))
        {
            foreach ($ret_inconsistencia as $key => $value)
            {
                $id = 12; // retorno padrao
                $descricao_erro = "Código de Erro ( {$value} )";

                // identifica o ID através do DE x PARA
                $ret = $CI->log_erro->filterByCodErroParceiro($value, 'icatu_retorno')->get_all();
                if ( !empty($ret) )
                {
                    $id = $ret[0]['integracao_log_detalhe_erro_id'];
                    $descricao_erro = $ret[0]['nome'];
                }

                $response->msg[] = ['id' => $id, 'msg' => $descricao_erro, 'slug' => "erro_retorno"];
            }

            return $response;
        }

        $capitalizacao_serie = $CI->capitalizacao_serie->filter_by_codigo_interno( $cod_produto )->get_all();
        if ( empty($capitalizacao_serie) )
        {
            $response->msg[] = ['id' => 12, 'msg' => 'Nenhum Produto identificado com o código '. $cod_produto, 'slug' => "erro_interno"];
            return $response;
        }

        $capitalizacao_serie_id = $capitalizacao_serie[0]['capitalizacao_serie_id'];
        $solicita_range         = $capitalizacao_serie[0]['solicita_range'];

        // Valida se o número da sorte já foi inserido
        if ( $CI->capitalizacao_serie->existNumSorte($capitalizacao_serie_id, $num_sorte, $num_lote) )
        {
            $response->msg[] = ['id' => 12, 'msg' => "O número da sorte {$num_sorte} já foi recebido no produto {$cod_produto} e proposta {$num_lote}", 'slug' => "erro_interno"];
            return $response;
        }

        // Aguardando Retorno
        if ($solicita_range == 2)
            $CI->capitalizacao_serie->updateRangeSolicitada( $capitalizacao_serie_id );

        $CI->titulo->insert([
            'capitalizacao_serie_id' => $capitalizacao_serie_id,
            'contemplado' => 0,
            'num_lote' => $num_lote,
            'numero' => $num_sorte,
            'sequencial' => $dados['registro']['sequencial'],
            'utilizado' => 0,
            'ativo' => 1,
        ], FALSE);

        $response->status = true;
        return $response;
    }

}
