<?php

if ( ! function_exists('app_assets_url'))
{
    function app_assets_url($uri = '', $context = 'site'){

        $CI =& get_instance();
        $assets_directory = $CI->config->item('app_assets_dir');
        $assets_version = $CI->config->item('app_assets_ver');
        $uri = strlen(parse_url($uri, PHP_URL_QUERY)) > 0 ? ($uri."&version=".$assets_version) : ($uri."?version=".$assets_version);
        $uri = $assets_directory . '/' .$context . '/' .$uri;
        return base_url($uri) ;

    }
}
if ( ! function_exists('app_assets_protocol'))
{
    function app_assets_protocol(){
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ){
            $protocol = (empty($_SERVER['HTTP_X_FORWARDED_PROTO']) OR strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) !== 'https') ? 'http' : 'https';
        }else{
            $protocol = (empty($_SERVER['HTTPS']) OR strtolower($_SERVER['HTTPS']) === 'off') ? 'http' : 'https';
        }
        return $protocol;
    }
}
if ( ! function_exists('app_assets_dir'))
{
    function app_assets_dir($uri = '', $context = 'site'){

        $CI =& get_instance();
        $assets_directory = $CI->config->item('app_assets_dir');
        $uri = $assets_directory . '/' .$context . '/' .$uri;
        return dirname(__FILE__) . "/../../{$uri}/";

    }
}
if ( ! function_exists('mb_str_pad')) {
    function mb_str_pad( $input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
    {
        $diff = strlen( $input ) - mb_strlen( $input );
        $ret=$input;
        if ($pad_length < strlen( $input )){
            if ($pad_type) {
                $ret = right($input, $pad_length);
            } else {
                $ret = left($input, $pad_length);
            }
        } else {
            $ret = str_pad( $input, $pad_length + $diff, $pad_string, $pad_type );
        }
        return $ret;
    }
}
if ( ! function_exists('right')) {
    function right( $str, $length )
    {
        return substr($str, -$length);
    }
}
if ( ! function_exists('left')) {
    function left( $str, $length )
    {
        return substr($str, 0, $length);
    }
}
if ( ! function_exists('app_current_controller'))
{
    function app_current_controller(){

        $CI =& get_instance();

        return $CI->router->fetch_class();

    }
}

function app_current_method(){

    $CI =& get_instance();

    return $CI->router->fetch_method();

}

function app_body_class(){

    $CI =& get_instance();

    $controller =  $CI->router->fetch_class();
    $method =  $CI->router->fetch_method();

    return "app-{$controller}-$method";
}

function is_current_controller($controller){

    if(is_array($controller)){
        if(in_array(app_current_controller(), $controller )){
            return  true;
        }else {
            return  false;
        }
    }else{
        if($controller == app_current_controller()){
            return  true;
        }else {
            return  false;
        }
    }
}

function is_current_method($method){

    if($method == app_current_method()){

        return  true;
    }else {
        return  false;
    }
}


if ( ! function_exists('app_template_tag'))
{
    function app_template_tag($data){
        $new_data = array();
        foreach($data as $key => $value ){
            $new_data['{{' .$key.'}}'] = $value;
        }

        return $new_data;

    }
}


function app_format_template($data, $pre= '{{', $pos = '}}'){

    $new_data = array();
    foreach($data as $key => $value){

        $new_data [$pre . $key. $pos ] = $value;
    }
    return $new_data;
}

function app_build_template_vars($data, $string){

    return app_parse_template(app_format_template($data), $string);
}

function app_parse_template($data, $string){

    $string =  str_replace( array_keys($data), array_values($data), $string);

    //Remove as variaveis que nÃ£o foram alteradas
    $string = preg_replace('/\{\{.*\}\}/', '', $string);

    return $string;

}

function app_create_cp_js_var($var_name , $data){

    $js = '';

    $data = app_format_template($data);
    foreach($data as $key => $value){

        $item = array(
            $key,
            $value,
            $value
        );

        $js .= "['". implode("','" , $item) .  "'],";

    }

    return "var {$var_name} = [" . $js . '];';
}

function app_date_mysql_to_mask($date, $format = 'd/m/Y H:i'){
    if($date != '0000-00-00 00:00:00' && $date != '') {
        return date($format, strtotime($date));
    }else {
        return '';
    }
}

function app_date_mask_to_mysql($date){

    if($date != '0000-00-00 00:00:00' && $date != '') {

        $date = preg_split('/\s/', trim($date));

        if(count($date) == 3){

            $dia = $date[0];
            $hora = $date[2];

            $date = implode('/', array_reverse(explode('/', $dia))) . ' ' . $hora;
            return date("Y-m-d H:i:s", strtotime($date));
        }
    }

    return '';

}

function app_date_only_numbers_to_mysql($date, $start = true)
{
    $date = str_replace("/", "", $date);
    $date = str_replace("-", "", $date);
    if(strlen($date) == 8)
    {
        $dia = substr($date, 0, 2);
        $mes = substr($date, 2, 2);
        $ano = substr($date, 4, 4);

        $date = "$ano-$mes-$dia ";
        $hour = ($start) ? "00:00:00" : "23:59:59";
        return $date.$hour;
    }
    return "";
}

/**
 * @description Calcula a diferença entre duas datas no formato dd/mm/yyyy
 * @param $d1 Data de início
 * @param $d2 Data de Fim
 * @param string $type Tipo Y = Ano, M = Mês, D = Dia, H = Hora e I = Minuto
 * @return float|int
 */
function app_date_get_diff_dias($d1, $d2, $type=''){

    if(!empty($d1) && !empty($d2)) {

        $d1 = explode('/', $d1);
        $d2 = explode('/', $d2);
        $type = strtoupper($type);
        switch ($type)
        {
            case 'Y':
                $X = 31536000;
                break;
            case 'M':
                $X = 2592000;
                break;
            case 'D':
                $X = 86400;
                break;
            case 'H':
                $X = 3600;
                break;
            case 'I':
                $X = 60;
                break;
            default:
                $X = 1;
        }
        return floor( (mktime(0, 0, 0, $d2[1], $d2[0], $d2[2]) - mktime(0, 0, 0, $d1[1], $d1[0], $d1[2] ) )/$X );

    }else{
        return 0;
    }


}

/**
 * @description Calcula a diferença entre duas datas no formato Mysql
 * @param date Mysql $d1
 * @param date Mysql $d2
 * @param string $type
 * @return float|int
 */
function app_date_get_diff_mysql($d1, $d2, $type=''){

    if(!empty($d1) && !empty($d2)) {

        $d1 = explode('-', $d1);
        $d2 = explode('-', $d2);
        $type = strtoupper($type);
        switch ($type)
        {
            case 'Y':
                $X = 31536000;
                break;
            case 'M':
                $X = 2592000;
                break;
            case 'D':
                $X = 86400;
                break;
            case 'H':
                $X = 3600;
                break;
            case 'I':
                $X = 60;
                break;
            default:
                $X = 1;
        }
        return floor( (mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]) - mktime(0, 0, 0, $d1[1], $d1[2], $d1[0] ) )/$X );

    }else{
        return 0;
    }

}

function app_date_get_diff_master($d1, $d2){
    $dats = ['years' => 0, 'months' => 0, 'days' => 0, 'd' => 0, 'dd' => 0, 'hours' => 0, 'minutes' => 0, 'seconds' => 0 ];

    if(!empty($d1) && !empty($d2)) {
        $date1 = explode('-', $d1);
        $date2 = explode('-', $d2);

        $ano = $date1[0];
        $QtdeBissexto = 0;
        while ($ano <= $date2[0]) {
            $bissexto = date('L', mktime(0, 0, 0, 1, 1, $ano-1));
            if ($bissexto) $QtdeBissexto++;
            $ano++;
        }

        $dif = date_diff(
            date_create($d2),  
            date_create($d1)
        );

        $dats = [
            'years' => $dif->format('%y'),
            'months' => $dif->format('%m'),
            'days' => $dif->format('%a'),
            'd' => $dif->format('%d'),
            'hours' => $dif->format('%h'),
            'minutes' => $dif->format('%i'),
            'seconds' => $dif->format('%s'),
        ];

        $dats['dd'] = $dats['days'] - ((365*$dats['years']) + $QtdeBissexto);

    }
    return $dats;
}

function app_date_get_diff_vigencia($d1, $d2, $type=''){
    $dats = app_date_get_diff_master($d1, $d2);

    $years   = $dats['years'];
    $months  = $dats['months'];
    $days    = $dats['days'];
    $d       = $dats['d'];
    $hours   = $dats['hours'];
    $minutes = $dats['minutes'];
    $seconds = $dats['seconds'];

    $type = strtoupper($type);
    switch ($type)
    {
        case 'Y':
            $X = $years;
            break;
        case 'M':
            $X = (12 * $years) + $months + ($d > 0 ? 1 : 0);
            break;
        case 'D':
            $X = $days;
            break;
        case 'H':
            $X = $hours;
            break;
        case 'I':
            $X = $minutes;
            break;
        default:
            $X = $seconds;
    }

    return $X;
}

function app_date_get_diff($d1, $d2, $type=''){

    $dats = app_date_get_diff_master($d1, $d2);

    $years   = $dats['years'];
    $months  = $dats['months'];
    $days    = $dats['days'];
    $dd      = $dats['dd'];
    $hours   = $dats['hours'];
    $minutes = $dats['minutes'];
    $seconds = $dats['seconds'];

    $type = strtoupper($type);
    switch ($type)
    {
        case 'Y':
            $X = $years;
            break;
        case 'M':
            $X = (12 * $years) + $months + ($dd > 0 ? 1 : 0);
            break;
        case 'D':
            $X = $days;
            break;
        case 'H':
            $X = $hours;
            break;
        case 'I':
            $X = $minutes;
            break;
        default:
            $X = $seconds;
    }

    return $X;

}

function app_dateonly_mask_to_mysql($date)
{
    if ($date != '0000-00-00' && $date != '') {
        $date = explode(' ', $date)[0];
        return $date = implode('-', array_reverse( explode('/', $date) ) );
    } else {
        return '';
    }
}

function app_dateonly_mysql_to_mask($date)
{
    if($date != '0000-00-00' && $date != '') {
        return date("d/m/Y", strtotime($date));
    }else {
        return '';
    }
}
function app_cpf_to_mask($cpf)
{
    $string = substr($cpf, 0, 3).'.'.substr($cpf, 3,3).'.'.substr($cpf, 6,3).'-'.substr($cpf, 9, 2);
    return $string;
}

function app_cnpj_to_mask($cpf)
{
    $string = substr($cpf, 0, 2).'.'.substr($cpf, 2,3).'.'.substr($cpf, 5,3).'/'.substr($cpf, 8, 4).'-'.substr($cpf, 12,2);
    return $string;
}

function app_verifica_cpf_cnpj ($cpf_cnpj) {
    // Verifica CPF
    if ( strlen($cpf_cnpj ) === 11 ) {
        return 'CPF';
    }
    // Verifica CNPJ
    elseif ( strlen( $cpf_cnpj ) === 14 ) {
        return 'CNPJ';
    }
    // Não retorna nada
    else {
        return false;
    }
}

function app_clear_number($str){

    return preg_replace('/[^0-9]/', '', $str);
}

function app_char_alpha($index){
    //fixa o problema do indice do array comeÃ§ar em zero;
    $index = $index - 1;

    $char_list = range('A', 'Z' );

    return $char_list[$index];
}
function app_get_querystring_full(){
    $query = '';
    $url = parse_url($_SERVER['REQUEST_URI']);

    if(isset($url['query'])){

        $query = '?' . $url['query'];
    }

    return$query;
}

function app_get_value($field, $default = ''){
    $ci = & get_instance();


    if(is_array($field)){

        foreach ($field as $key => $value){

            if(isset($_GET[$key][$value])){

                return $_GET[$key][$value];
            }else {

                return $default;
            }

        }
    }
    if(isset($_GET[$field])){

        return $_GET[$field];
    }else {

        return $default;
    }


}

function app_validate_cpf_cnpj ($cpf_cnpj) {
    // Verifica CPF
    if ( strlen($cpf_cnpj ) === 11 ) {
        return app_validate_cpf($cpf_cnpj);
    }
    // Verifica CNPJ
    elseif ( strlen( $cpf_cnpj ) === 14 ) {
        return app_validate_cnpj($cpf_cnpj);
    }
    // Não retorna nada
    else {
        return false;
    }
}

function app_validate_cpf($cpf) {

    // Elimina possivel mascara
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

    // Verifica se o numero de digitos informados Ã© igual a 11
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se nenhuma das sequÃªncias invalidas abaixo
    // foi digitada. Caso afirmativo, retorna falso
    else if ($cpf == '00000000000' ||
        $cpf == '11111111111' ||
        $cpf == '22222222222' ||
        $cpf == '33333333333' ||
        $cpf == '44444444444' ||
        $cpf == '55555555555' ||
        $cpf == '66666666666' ||
        $cpf == '77777777777' ||
        $cpf == '88888888888' ||
        $cpf == '99999999999') {

        return false;
        // Calcula os digitos verificadores para verificar se o
        // CPF Ã© vÃ¡lido
    } else {

        for ($t = 9; $t < 11; $t++) {

            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {

                return false;
            }
        }

        return true;
    }
}

function app_validate_cnpj($cnpj)
{
    $cnpj = trim($cnpj);
    $soma = 0;
    $multiplicador = 0;
    $multiplo = 0;


    # [^0-9]: RETIRA TUDO QUE NÃƒO Ã‰ NUMÃ‰RICO,  "^" ISTO NEGA A SUBSTITUIÃ‡ÃƒO, OU SEJA, SUBSTITUA TUDO QUE FOR DIFERENTE DE 0-9 POR "";
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    if(empty($cnpj) || strlen($cnpj) != 14)
        return FALSE;

    # VERIFICAÃ‡ÃƒO DE VALORES REPETIDOS NO CNPJ DE 0 A 9 (EX. '00000000000000')
    for($i = 0; $i <= 9; $i++)
    {
        $repetidos = str_pad('', 14, $i);

        if($cnpj === $repetidos)
            return FALSE;
    }

    # PEGA A PRIMEIRA PARTE DO CNPJ, SEM OS DÃGITOS VERIFICADORES
    $parte1 = substr($cnpj, 0, 12);

    # INVERTE A 1Âª PARTE DO CNPJ PARA CONTINUAR A VALIDAÃ‡ÃƒO    $parte1_invertida = strrev($parte1);
    $parte1_invertida = strrev($parte1);
    # PERCORRENDO A PARTE INVERTIDA PARA OBTER O FATOR DE CALCULO DO 1Âº DÃGITO VERIFICADOR
    for ($i = 0; $i <= 11; $i++)
    {
        $multiplicador = ($i == 0) || ($i == 8) ? 2 : $multiplicador;

        $multiplo = ($parte1_invertida[$i] * $multiplicador);

        $soma += $multiplo;

        $multiplicador++;
    }

    # OBTENDO O 1Âº DÃGITO VERIFICADOR
    $rest = $soma % 11;

    $dv1 = ($rest == 0 || $rest == 1) ? 0 : 11 - $rest;

    # PEGA A PRIMEIRA PARTE DO CNPJ CONCATENANDO COM O 1Âº DÃGITO OBTIDO
    $parte1 .= $dv1;

    # MAIS UMA VEZ INVERTE A 1Âª PARTE DO CNPJ PARA CONTINUAR A VALIDAÃ‡ÃƒO
    $parte1_invertida = strrev($parte1);

    $soma = 0;

    # MAIS UMA VEZ PERCORRE A PARTE INVERTIDA PARA OBTER O FATOR DE CALCULO DO 2Âº DÃGITO VERIFICADOR
    for ($i = 0; $i <= 12; $i++)
    {
        $multiplicador = ($i == 0) || ($i == 8) ? 2 : $multiplicador;

        $multiplo = ($parte1_invertida[$i] * $multiplicador);

        $soma += $multiplo;

        $multiplicador++;
    }

    # OBTENDO O 2Âº DÃGITO VERIFICADOR
    $rest = $soma % 11;

    $dv2 = ($rest == 0 || $rest == 1) ? 0 : 11 - $rest;

    # AO FINAL COMPARA SE OS DÃGITOS OBTIDOS SÃƒO IGUAIS AOS INFORMADOS (OU A SEGUNDA PARTE DO CNPJ)
    return ($dv1 == $cnpj[12] && $dv2 == $cnpj[13]) ? TRUE : FALSE;
}

function app_db_escape($string){

    $ci = & get_instance();

    return $ci->db->escape($string);
}

function app_set_value($field = '', $default = '')
{



    if ( ! isset($_POST[$field]))
    {
        $value =  $default;
    }else {

        $value =  $_POST[$field];
    }



    return form_prep($value, $field);
}

function app_has_value($field = '')
{



    if (isset($_POST[$field]))
    {
        return true;
    }else {

        return false;
    }
}

/**
 * @description  Formata telefone (11)X99999999
 * @param $numero
 * @return mixed|string
 */
function app_format_telefone($numero){

    $data = app_extract_telefone($numero);

    return (empty($data['ddd'])) ? $data['numero'] : "({$data['ddd']}){$data['numero']}";
}


function app_format_telefone_unitfour($numero){

    $data = app_extract_telefone($numero);

    $data['numero'] = substr_replace($data['numero'], '-', strlen($data['numero']) -4, 0);
    return (empty($data['ddd'])) ? "(11) {$data['numero']}" : "({$data['ddd']}) {$data['numero']}";
}

function app_format_cep($cep){

    $cep = app_retorna_numeros($cep);

    if(strlen($cep) == 8){
        $cep = substr_replace($cep, '-', strlen($cep) -3, 0);
    }

    return $cep;
}


function app_extract_telefone($numero){
    $numero = preg_replace('/([^0-9])/','',$numero);

    $data = array(
        'numero' => $numero,
        'ddd' => '',
    );

    if(strlen($numero) == 8 || strlen($numero) == 9 ){

        $data['numero'] = $numero;
        $data['ddd'] = '';

    }
    if(strlen($numero) == 10  || strlen($numero) == 11 ){

        $data['numero'] = substr($numero, 2);
        $data['ddd'] =  substr($numero, 0, 2);

    }

    return $data;
}


if ( ! function_exists('app_get_random_password'))
{

    function app_get_random_password($chars_min=6, $chars_max=8, $use_upper_case=false, $include_numbers=false, $include_special_chars=false)
    {
        $length = rand($chars_min, $chars_max);
        $selection = 'aeuoyibcdfghjklmnpqrstvwxz';
        if($include_numbers) {
            $selection .= "1234567890";
        }
        if($include_special_chars) {
            $selection .= "!@\"#$%&[]{}?|";
        }

        $password = "";
        for($i=0; $i<$length; $i++) {
            $current_letter = $use_upper_case ? (rand(0,1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];
            $password .=  $current_letter;
        }

        return $password;
    }

}

function app_get_select_html($field_select, $field_label,  $valores, $valor_selecionado, $valor_opt = 'chave', $texto_in='', $atributos = ''){
    $html = "";
    $html .= "\n<select name=\"{$field_select}\" id=\"{$field_select}\" {$atributos}>\n";
    if (!is_array($valor_selecionado) && !empty($texto_in)) $html .= "\t<option value=\"\">$texto_in</option>\n";
    foreach ($valores as  $valor){
        if($valor_opt == 'chave'){
            $html .= "\t<option value=\"{$valor[$field_select]}\"";
            if(is_array($valor_selecionado)){
                if(in_array($valor[$field_select], $valor_selecionado)){
                    $html .= " selected";
                }
            }else{
                if("{$valor[$field_select]}" == "{$valor_selecionado}") $html .= " selected";
            }
        } else {
            $html .= "\t<option value=\"{$valor[$field_label]}\"";
            if($valor[$field_label] == $valor_selecionado) $html .= " selected";
        }
        $html .= ">{$valor[$field_label]}</option>\n";
    }
    $html .= "</select>\n";
    return $html;
}



function app_get_km_list(){

    return array(
        0 =>'0 Km',
        1000 => '1000 Km',
        5000 => '5000 Km',
        10000 => '10000 Km',
        20000 => '20000 Km',
        30000 => '30000 Km',
        40000 => '40000 Km',
        50000 => '50000 Km',
        60000 => '60000 Km',
        70000 => '70000 Km',
        80000 => '80000 Km',
        90000 => '90000 Km',
        100000 => '100000 Km',
        110000 => '110000 Km',
        120000 => '120000 Km',
        130000 => '130000 Km',
        140000 => '140000 Km',
        150000 => '150000 Km'
    );
}

function app_youtube_id_by_url($url){

    $url = parse_url($url);

    if(isset($url['query'])){
        parse_str($url['query'], $data);
        if(isset($data['v'])){
            return $data['v'];
        }
    }
    return false;

}

function app_youtube_image_by_id($id, $scope = 'default'){

    $img = "http://img.youtube.com/vi/{$id}/{$scope}.jpg";
    return $img;

}
function app_youtube_embed_link_by_id($id){

    $img = "http://www.youtube.com/embed/{$id}?autoplay=1";
    return $img;

}

function app_get_slug($string) {

    $table = array(
        'Å '=>'S', 'Å¡'=>'s', 'Ä'=>'Dj', 'Ä‘'=>'dj', 'Å½'=>'Z', 'Å¾'=>'z', 'ÄŒ'=>'C', 'Ä'=>'c', 'Ä†'=>'C', 'Ä‡'=>'c',
        'Ã€'=>'A', 'Ã'=>'A', 'Ã‚'=>'A', 'Ãƒ'=>'A', 'Ã„'=>'A', 'Ã…'=>'A', 'Ã†'=>'A', 'Ã‡'=>'C', 'Ãˆ'=>'E', 'Ã‰'=>'E',
        'ÃŠ'=>'E', 'Ã‹'=>'E', 'ÃŒ'=>'I', 'Ã'=>'I', 'ÃŽ'=>'I', 'Ã'=>'I', 'Ã‘'=>'N', 'Ã’'=>'O', 'Ã“'=>'O', 'Ã”'=>'O',
        'Ã•'=>'O', 'Ã–'=>'O', 'Ã˜'=>'O', 'Ã™'=>'U', 'Ãš'=>'U', 'Ã›'=>'U', 'Ãœ'=>'U', 'Ã'=>'Y', 'Ãž'=>'B', 'ÃŸ'=>'Ss',
        'Ã '=>'a', 'Ã¡'=>'a', 'Ã¢'=>'a', 'Ã£'=>'a', 'Ã¤'=>'a', 'Ã¥'=>'a', 'Ã¦'=>'a', 'Ã§'=>'c', 'Ã¨'=>'e', 'Ã©'=>'e',
        'Ãª'=>'e', 'Ã«'=>'e', 'Ã¬'=>'i', 'Ã­'=>'i', 'Ã®'=>'i', 'Ã¯'=>'i', 'Ã°'=>'o', 'Ã±'=>'n', 'Ã²'=>'o', 'Ã³'=>'o',
        'Ã´'=>'o', 'Ãµ'=>'o', 'Ã¶'=>'o', 'Ã¸'=>'o', 'Ã¹'=>'u', 'Ãº'=>'u', 'Ã»'=>'u',  'Ã½'=>'y', 'Ã¾'=>'b',
        'Ã¿'=>'y', 'Å”'=>'R', 'Å•'=>'r', '/' => '-', ' ' => '-'
    );

    return strtolower(strtr($string, $table));
}

function app_get_url_anuncio($anuncio){

    $CI =& get_instance();
    $CI->load->model('anuncio_model');
    return $CI->anuncio_model->getAnuncioUrl($anuncio);

}
function app_get_url_acessorio($anuncio){

    $CI =& get_instance();
    $CI->load->model('acessorio_anuncio_model', 'acessorio_anuncio');
    return $CI->acessorio_anuncio->getAnuncioUrl($anuncio);

}



function app_merge_query_string($data){

    $data = (array) $data;
    $url_parts = parse_url($_SERVER['REQUEST_URI']);

    if(isset($url_parts['query'])){



        parse_str( $url_parts['query'], $query);


        $query = array_merge($query, $data );



        $url = http_build_query($query);

    }else {

        $url = http_build_query($data);

    }

    return $url;
}

function app_html_escape_br($string){
    return nl2br(html_escape($string));
}


function app_get_userdata($item){
    $CI =& get_instance();
    return $CI->session->userdata($item);

}
function app_format_currency($number, $symbol = false, $num_casas = 2){

    $result = number_format($number, $num_casas, ',' , '.');

    return ($symbol) ? 'R$ '.$result : $result;

}

function app_get_toprides($limit = 4){
    $CI = &get_instance();

    $CI->load->model('anuncio_model', 'anuncio_model');
    return $CI->anuncio_model->getSidebarTopRides($limit);

}

function app_get_banner_by_codigo($codigo, $rand = true){

    $CI = &get_instance();

    $CI->load->model('cms_banner_model', 'cms_banner');

    return $CI->cms_banner->getBannerByCodigo($codigo, $rand );

}

function app_get_firt_word($string){

    $words = preg_split('/\s/', $string);

    $word = trim($words[0]);
    return ucfirst($word);
}
function app_unformat_currency($value){

    $clearValue = preg_replace('/([^0-9\.,])/i', '', $value);
  
    $clearValue = str_replace( ".", "", $clearValue );
    $clearValue = str_replace( ",", ".", $clearValue );

    return $clearValue;
}

function app_unformat_percent($value){

    $clearValue = preg_replace('/([^0-9\.,])/i', '', $value);
    $clearValue = str_replace( ".", "", $clearValue );
    $clearValue = str_replace( ",", ".", $clearValue );
    return $clearValue;
}

function app_word_cut($string, $limit, $append =  '...'){

    if(strlen($string) > $limit){

        return mb_substr($string, 0, $limit, 'UTF-8' ) . $append;
    }else {

        return $string;
    }
}

function app_utf8_converter($array)
{
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
            $item = utf8_encode($item);
        }
    });

    return $array;
}
function app_retorna_numeros($string)
{
    return preg_replace('/[^0-9]/', '', $string);
}
/**
 * retorna tipo de cartÃ£o por nÃºmero
 * @param $number
 * @return string
 */
function app_get_card_type($number)
{
    $number=preg_replace('/[^\d]/','',$number);
    if (preg_match('/^3[47][0-9]{13}$/',$number))
    {
        return 'AMEX';
    }
    elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',$number))
    {
        return 'DINERS';
    }
    elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/',$number))
    {
        return 'Discover';
    }
    elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/',$number))
    {
        return 'JCB';
    }
    elseif (preg_match('/^5[1-5][0-9]{14}$/',$number))
    {
        return 'MASTERCARD';
    }
    elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/',$number))
    {
        return 'VISA';
    }
    else
    {
        return 'NOTACCEPTED';
    }
}

function app_dateonly_mask_mysql_null($date)
{
    if ($date != '0000-00-00' && $date != '')
    {
        return $date = implode('-', array_reverse( explode('/', $date) ) );
    }
    else
    {
        return '0000-00-00 00:00:00';
    }
}

function app_montar_menu($itens, &$html){

    foreach ($itens as $item) {


        $active = (is_current_controller($item['controllers'])) ? ' expanded ' : '';
        if(is_current_controller($item['controllers']) && count($item['itens']) == 0){
            $active = ' expanded active';
        }
        if($item['externo'] == 0){
            if(empty($item['controller'])){
                $url = "";
            }else{
                $url = base_url("admin/{$item['controller']}/{$item['acao']}");
                $url .= (!empty($item['acao']) && empty($item['parametros'])) ? "/{$item['parametros']}" : '';
            }
        }else{
            $url = $item['url'];
        }
        $class_sub = (count($item['itens']) > 0) ? 'gui-folder' : '';
        $icon = (empty($item['icon']) && $item['pai_id'] == 0) ? 'fa fa-folder-open fa-fw' : $item['icon'];
        $html[] = "<li class=\"{$class_sub}{$active}\">";
        $html[] = (empty($url)) ? "    <a>" : "    <a class=\"{$active}\" href=\"{$url}\" target=\"{$item['target']}\" >";
        $html[] = ($item['pai_id'] == 0) ? "        <div class=\"gui-icon\"><i class=\"{$icon}\"></i></div>" : "";
        $html[] = "        <span class=\"title\">{$item['nome']}</span>";
        $html[] = "    </a>";
        if($item['itens']){
            $html[] = "<ul>";
            app_montar_menu($item['itens'], $html);
            $html[] = "</ul>";
        }

        $html[] = "</li>";
    }
}


function app_montar_relacionamento_produto($itens){

    $html = array();


    foreach ($itens as $produto) {


        $html[] = "<ul class=\"relacionamento\" id=\"relacionamento_{$produto['produto']['produto_parceiro_id']}\">";
        $html[] = "<li><em>{$produto['produto']['nome']}<br/>";
        $html[] = "{$produto['produto']['parceiro']['nome']}<br/>";
        $html[] = "</em>";
        $html[] = "<strong>Markup: </strong>" . app_format_currency($produto['produto_configuracao']['markup'], FALSE, 3);
        $html[] = "<ul>";
        app_montar_relacionamento($produto['relacionamento'], $html);
        $html[] = "</ul>";
        $html[] = "</li>";
        $html[] = "</ul>";

    }



    return $html;
}

function app_montar_relacionamento($itens, &$html){




        foreach ($itens as $item) {

            $html[] = "<li>";
            $html[] = $item['parceiro']['nome']."<br/>";
            $html[] = "<strong>Comissão: </strong>" .app_format_currency($item['comissao'], false, 3);
            if ($item['itens']) {
                $html[] = "<ul>";
                app_montar_relacionamento($item['itens'], $html);
                $html[] = "</ul>";
            }
            $html[] = "</li>";
        }

}

/**
 * Se for setado uma variÃ¡vel ele a retorna, caso contrÃ¡rio retorna vazio
 * @param $var
 * @param bool $default
 * @return string
 */
function issetor(&$var, $default = ' ') {

    if(isset($var))
    {
        return $var;
    }
    else
    {
        return $default;
    }
}

/**
 * Se for setado uma variável ele a retorna, caso contrário retorna vazio
 * @param $var
 * @param bool $default
 * @return string
 */
function emptyor(&$var, $default = ' ') {

    if(!empty($var))
    {
        return $var;
    }
    else
    {
        return $default;
    }
}

/**
 * @description Se for uma variavel vazia ele retorna com o default
 * @param $var
 * @return string
 */

if ( ! function_exists('isempty')) {
    function isempty(&$var, $default = ' ')
    {
        return (strlen($var) > 0) ? $var : $default; // $default : $var;
    }
}

/**
 * admin url
 * @param string $uri
 * @return mixed
 */
function admin_url($uri = '')
{
    return base_url() . 'admin/' . $uri;
}

/**
 * Numero para letra (excel)
 * @param $n
 * @return string
 */
function app_num2alpha($n)
{
    for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r;
    return $r;
}

function app_is_form_error($field_name){

    $error = form_error($field_name);

    return !empty($error);
}

function app_get_form_error($field_name){

    $error = form_error($field_name);

    if(!empty($error)) {
        $error = strip_tags($error);
        return "<span id=\"{$field_name}-error\" class=\"help-block\">{$error}</span>";
    }else{
        return "";
    }

}


function app_calculo_valor($tipo, $quantidade, $valor){

    $result = $valor;

    if($tipo == 'PORCENTAGEM'){
        $result =  $valor - (( $quantidade / 100 ) * $valor);
    }elseif($tipo == 'MONETARIO'){
        $result = $valor - $quantidade;
    }

    return round($result, 2);
}
/**
 * Cálculo de porcentagem
 * @param $porcentagem float
 * @param $total float
 * @return float
 */
function app_calculo_porcentagem ( $porcentagem, $total ) {
    return ( $porcentagem / 100 ) * $total;
}
/**
 * Truncar Valor com Precisão definida
 * @param $number float
 * @param $precision int
 * @return float
 */
function truncate($number, $precision = 0) {
   // warning: precision is limited by the size of the int type
   $shift = pow(10, $precision);
   return intval($number * $shift)/$shift;
}

/**
 * Converte para UTF8 recursivamente
 * @param $array
 * @return mixed
 */
function utf8_converter($array)
{
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
            $item = utf8_encode($item);
        }
    });

    return $array;
}

/**
 * Trata Array com Objeto para Array
 * @param $array
 * @return mixed
 */
function convert_objeto_to_array($array)
{
    if ( is_array($array) ) {
        array_walk_recursive($array, function(&$item, $key){
            if(is_object($item)){
                $item = json_decode(json_encode($item),true);
            }
        });

        if (is_string($array)) {
            $array = json_decode( $array );
        }else {
            $array = (object)$array;
        }

    } elseif (in_array(preg_replace("/^(.)(.+?)(.)$/", "$1$3", trim($array) ), ['{}','[]'])) {
        $array = json_decode($array);
    }

    return $array;
}

/**
 * Verifica a permissão do usuário logado para uma ação
 * @param $recurso_slug
 * @param $acao_slug
 * @return bool
 */
function verifica_permissao($recurso_slug, $acao_slug)
{
    $CI = &get_instance();
    $CI->load->model("usuario_acl_permissao_model", "usuario_acl_permissao");
    $acao = $CI->usuario_acl_permissao
        ->with_foreign()
        ->get_by(array(
            'usuario_acl_tipo.usuario_acl_tipo_id' => $CI->session->userdata('usuario_acl_tipo_id'),
            'usuario_acl_acao.slug' => $acao_slug,
            'usuario_acl_recurso.slug' => $recurso_slug
    ));
    if($acao)
        return true;
    return false;
}


function app_get_acao_permitida ($acoes, $acao_id)
{
    foreach($acoes as $acao)
    {
        if($acao['usuario_acl_acao_id'] == $acao_id)
            return $acao['permitido'];
    }
    return 0;
}

function app_print_recursos($recursos)
{
    foreach($recursos as $recurso) {
        echo "
        <li class=''>
            <div idAcao='1' class='checkbox checkbox-inline checkbox-styled'>
                <label>
                    <input checked='' idAcao='1' idRecurso='{$recurso['usuario_acl_recurso_id']}' class='btRecurso' type='checkbox' name='recurso_acao[{$recurso['usuario_acl_recurso_id']}][1]'>
                    <span>{$recurso['nome']}</span>
                </label>
            </div>

            ".app_print_recurso_filho($recurso)."
        </li>
        ";
    }
}

function app_print_recurso_filho($recurso)
{

    if(isset($recurso['acoes'])) {
        echo "<div class='acoes'>";

        foreach ($recurso['acoes'] as $acao)
        {
            if($acao['usuario_acl_acao_id'] != 1)
            {
                echo "
                <div idAcao='{$acao['usuario_acl_acao_id']}' class=\"checkbox checkbox-inline checkbox-styled\">
                    <label>
                        <input idAcao='{$acao['usuario_acl_acao_id']}' idRecurso='{$recurso['usuario_acl_recurso_id']}' class='btRecurso' type=\"checkbox\"";
                if($acao['permitido'])
                    echo ' checked ';
                echo "name='recurso_acao[{$recurso['usuario_acl_recurso_id']}][{$acao['usuario_acl_acao_id']}]'>
                        <span>{$acao['usuario_acl_acao_nome']}</span>
                    </label>
                </div>
                ";
            }
        }

        echo "</div>";
    }

    if(isset($recurso['filhos'])) {
        echo "<ul class='filhos' id='{$recurso['usuario_acl_recurso_id']}'>";

        foreach ($recurso['filhos'] as $filho) {
            echo "<li class='titulo'>";

            foreach ($recurso['acoes'] as $acao)
            {
                if($acao['usuario_acl_acao_id'] == 1)
                {
                    echo " 
                <div idAcao='{$acao['usuario_acl_acao_id']}' class=\"checkbox checkbox-inline checkbox-styled\">
                    <label>
                        <input idAcao='{$acao['usuario_acl_acao_id']}' idRecurso='{$filho['usuario_acl_recurso_id']}' class='btRecurso' type=\"checkbox\"";
                    if($acao['permitido'])
                        echo ' checked ';
                    echo "name='recurso_acao[{$filho['usuario_acl_recurso_id']}][1]'>
                        <span></span>
                    </label>
                </div>
                ";
                }
            }

            echo"<i class='fa fa-chevron-right'></i> {$filho['nome']}";

            app_print_recurso_filho($filho);

            echo "</li>";
        }

        echo "</ul>";
    }
}

/**
 * Retorna recurso atual
 * @return array
 */
function app_recurso($slug = null)
{
    if(!$slug)
        $slug = app_current_controller();
    $CI =& get_instance();
    $CI->load->model("usuario_acl_recurso_model", "recurso");
    $recurso = $CI->recurso->get_by(array("controller" => $slug));
    if($recurso)
        return $recurso;
    return array();
}

/**
 * Retorna nome do recurso atual
 * @return string
 */
function app_recurso_nome()
{
    $CI =& get_instance();
    $CI->load->model("usuario_acl_recurso_model", "recurso");
    $recurso = $CI->recurso->get_by(array("controller" => app_current_controller()));

    if($recurso && isset($recurso['nome']))
        return $recurso['nome'];
    return "";
}


function app_get_step_cotacao($cotacao_id){
    $CI =& get_instance();
    $CI->load->model("cotacao_model", "cotacao");
    $cotacao = $CI->cotacao->with_produto_parceiro()->get($cotacao_id);
    switch ($cotacao['produto_slug']) {
        case 'seguro_viagem':
            $cotacao = $CI->cotacao->with_produto_parceiro()->with_cotacao_seguro_viagem()->get($cotacao_id);
            break;
        case 'equipamento':
            $cotacao = $CI->cotacao->with_produto_parceiro()->with_cotacao_equipamento()->get($cotacao_id);
            break;
        case 'generico':
            $cotacao = $CI->cotacao->with_produto_parceiro()->with_cotacao_generico()->get($cotacao_id);
            break;
        case 'seguro_saude':
            $cotacao = $CI->cotacao->with_produto_parceiro()->with_cotacao_generico()->get($cotacao_id);
            break;
    }
    $array_cotacao_step = array(
        "1" => 'Dados Iniciais'
        , "2" => 'Cotação'
        , "3" => 'Contratação'
        , "4" => 'Pagamento'
        , "5" => 'Certificado / Bilhete'
    ) ;
    if( isset( $cotacao['step'] ) ){
        if( $array_cotacao_step[ (int)$cotacao['step'] ] ){
            return $array_cotacao_step[ (int)$cotacao['step'] ]  ;
        }
        else{
            return '';   
        }
    }
    else{
        return  'Erro ao Iniciar';   
    }
    /*
    if($cotacao['step'] == 1){
        return 'Dados Iniciais';
    }elseif($cotacao['step'] == 2){
        return 'Cotação';
    }elseif($cotacao['step'] == 3){
        return 'Contratação';
    }elseif($cotacao['step'] == 4){
        return 'Pagamento';
    }elseif($cotacao['step'] == 4){
        return 'Certificado / Bilhete';
    }*/
}

function app_format_contato($val, $tipo){

    switch ($tipo) {
        case 2:
        case 3:
        case 4:
            return app_format_telefone($val);
            break;
        default:
            return $val;
            break;
    }
}


function app_validate_data($date)
{
    if(@checkdate(substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4)))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function app_validate_data_americana($date)
{
    if(@checkdate(substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4)))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function app_validate_mobile_phone($phone) {
   // $exp_regular = '/^(\(11\) (9\d{4})-\d{4})|((\(1[2-9]{1}\)|\([2-9]{1}\d{1}\)) [5-9]\d{3}-\d{4})$/';
    $exp_regular = '#^\(\d{2}\) 9?[6789]\d{3}-\d{4}$#';
    $ret = preg_match($exp_regular, $phone);

    if($ret === 1)
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}

/**
 * Verifica se uma view existe
 * @param $view
 * @return bool
 */
function view_exists($view)
{
    if(file_exists(FCPATH . "application/views/{$view}.php"))
        return true;
    return false;
}

/**
 * @description Verifica se um determinado campo esta configurado
 * @param $campo
 * @param $produto_parcairo_id
 *
 */
function app_has_config_campo($campo_slug, $produto_parceiro_id){
    $CI =& get_instance();
    $CI->load->model('produto_parceiro_campo_model', 'produto_parceiro_campo');

    $campo = $CI->produto_parceiro_campo->with_campo()
        ->with_campo_tipo()
        ->filter_by_produto_parceiro($produto_parceiro_id)
        ->filter_by_campo_slug($campo_slug)
        ->get_all();


    return ($campo) ? $campo : FALSE;

}

function app_rel_sintetico_ajuste_num($numero)
{
    if(is_numeric($numero)){
        if($numero < 0)
            $numero = $numero * -1;
    } 
    return $numero;
}

/**
 * $description Faz a tradução de um texto
 * @param $texto
 * @param $produto_parceiro_id
 * @return mixed
 */
function app_produto_traducao($texto, $produto_parceiro_id){

    $CI =& get_instance();
    $CI->load->model('produto_parceiro_traducao_model', 'produto_parceiro_traducao');

    if($produto_parceiro_id == null || $produto_parceiro_id == 0)
        return $texto;

    return $CI->produto_parceiro_traducao->traducao($texto, $produto_parceiro_id);

}

/**
 * @description Formata cartão escondendo os números
 * @param $cc
 * @param string $maskingCharacter
 * @return bool|string
 */
function app_format_credit_card($cc, $maskingCharacter = 'X') {
    // REMOVE EXTRA DATA IF ANY
    $cc = str_replace(array('-', ' '), '', $cc);
    // GET THE CREDIT CARD LENGTH
    $cc_length = strlen($cc);
    $newCreditCard = substr($cc, -4);
    for ($i = $cc_length - 5; $i >= 0; $i--) {
        // ADDS HYPHEN HERE
        if ((($i + 1) - $cc_length) % 4 == 0) {
            $newCreditCard = ' ' . $newCreditCard;
        }
        $newCreditCard = $cc[$i] . $newCreditCard;
    }
    // REPLACE CHARACTERS WITH X EXCEPT FIRST FOUR AND LAST FOUR
    for ($i = 0; $i < $cc_length-1; $i++) {
        if ($newCreditCard[$i] == ' ') {
            continue;
        }
        $newCreditCard[$i] = $maskingCharacter;
    }
    // RETURN THE FINAL FORMATED AND MASKED CREDIT CARD NO
    return $newCreditCard;
}

function app_add_dias_uteis($str_data,$int_qtd_dias_somar = 7) {

    // Caso seja informado uma data do MySQL do tipo DATETIME - aaaa-mm-dd 00:00:00
    // Transforma para DATE - aaaa-mm-dd
    $str_data = substr($str_data,0,10);
    // Se a data estiver no formato brasileiro: dd/mm/aaaa
    // Converte-a para o padrão americano: aaaa-mm-dd
    if ( preg_match("@/@",$str_data) == 1 )
    {
        $str_data = implode("-", array_reverse(explode("/",$str_data)));
    }
    $array_data = explode('-', $str_data);
    $count_days = 0;
    $int_qtd_dias_uteis = 0;
    while ( $int_qtd_dias_uteis < $int_qtd_dias_somar )
    {
        $count_days++;
        if ( ( $dias_da_semana = gmdate('w', strtotime('+'.$count_days.' day', mktime(0, 0, 0, $array_data[1], $array_data[2], $array_data[0]))) ) != '0' && $dias_da_semana != '6' )
        {
            $int_qtd_dias_uteis++;
        }
    }
    return gmdate('d/m/Y',strtotime('+'.$count_days.' day',strtotime($str_data)));
}

function app_remove_especial_caracteres($str) {

    $from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
    $to = "aaaaeeiooouucAAAAEEIOOOUUC";
    $keys = array();
    $values = array();
    preg_match_all('/./u', $from, $keys);
    preg_match_all('/./u', $to, $values);
    $mapping = array_combine($keys[0], $values[0]);
    return strtr($str, $mapping);
}

if ( ! function_exists('soap_curl'))
{
    function soap_curl($config = array()){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $config['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $config['method'],
            CURLOPT_POSTFIELDS => $config['fields'],
            
        ));
        if (!empty($config['header'])) curl_setopt($curl, CURLOPT_HTTPHEADER, $config['header']);
        $header = curl_exec($curl);
        $info = curl_getinfo($curl);
        $httpCode = curl_getinfo($curl , CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        return [
            "response" => $header,
            "info" => $info,
            "httpCode" => $httpCode,
            "error" => $error,
        ];

    }
}

if ( ! function_exists('app_get_token'))
{
    function app_get_token($email = null, $senha = null){

        // solicitar outro token quando a API tiver vencida
        if ( !empty(app_get_userdata("tokenAPIvalid")) && app_get_userdata("tokenAPIvalid") > date("Y-m-d H:i:s"))
            return app_get_userdata("tokenAPI");

        $url = '';

        if (empty($email))
            $email = app_get_userdata("email");

        if (empty($senha))
            $url = '&forceEmail=1';

        $CI =& get_instance();



        $retorno = soap_curl([
            'url' => $CI->config->item('base_url') ."api/acesso?email={$email}&senha={$senha}&url={$url}",
            'method' => 'GET',
            'fields' => '',
            'header' => array(
                "accept: application/json",
            )
        ]);

        if (empty($retorno)) return;
        if (!empty($retorno["error"])) return;
        if (empty($retorno["response"])) return;

        $response = json_decode($retorno["response"]);
        if (empty($response->status)) return;

        $CI->session->set_userdata("tokenAPI", $response->api_key);
        $CI->session->set_userdata("tokenAPIvalid", $response->validade);

        return $response->api_key;
    }
}

if ( ! function_exists('app_search'))
{
    function app_search( $haystack, $needle, $index = NULL ) {
        if( is_null( $haystack ) ) {
            return -1;
        }

        $arrayIterator = new \RecursiveArrayIterator( $haystack );
        $iterator = new \RecursiveIteratorIterator( $arrayIterator );

        while( $iterator -> valid() ) {
            if( ( ( isset( $index ) and ( $iterator -> key() == $index ) ) or
                ( ! isset( $index ) ) ) and ( $iterator -> current() == $needle ) ) {

                return $arrayIterator -> key();
            }

            $iterator -> next();
        }

        return -1;
    }
}