<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Leonardo Lazarini
 * Date: 15/07/2016
 * Time: 15:05
 */


/**
 * Carrega um control
 * @param $type
 * @param $field_name
 * @param $descricao
 * @param $value
 * @param array $parametros
 */
function template_control($type, $field_name, $descricao, &$value, $parametros = array())
{
    if(!isset($value))
        $value = "";
    call_user_func("template_{$type}", $field_name, $descricao, $value, $parametros);
}


/**
 * Cria select
 * @param $field_name
 * @param $descricao
 * @param $value
 * @param $parametros
 */
function template_select($field_name, $descricao, $value, $parametros)
{
    if(empty($parametros['valor']))
        $parametros['valor'] = "valor";

    if(empty($parametros['descricao']))
        $parametros['descricao'] = "descricao";

    if(!isset($parametros['default_value']))
        $parametros['default_value'] = "0";

    if(!isset($parametros['id']))
        $field_id = $field_name;
    else
        $field_id = $parametros['id'];


    $non_selected = "";

    if(isset($parametros['default_non_selectable']) && $parametros['default_non_selectable'] == true)
        $non_selected = "style='display:none;'";

    if(isset($parametros['multiple']))
    {
        if(isset($parametros['tags']))
            $parametros['tags'] .= ' multiple';
        else
            $parametros['tags'] = 'multiple';

        if(isset($parametros['class']))
            $parametros['class'] .= ' multiselect';
        else
            $parametros['class'] = 'multiselect';
    }

    if(!empty($descricao))
        echo "<label class='control-label' for='$field_name'>{$descricao}</label>";

    echo "
        <select ".issetor($parametros['tags'])." class='form-control select ".issetor($parametros['class'])."' id='$field_id' name='$field_name'>
            ";
    if(isset($parametros['default']))
    {
        echo "<option $non_selected value='".issetor($parametros['default_value'])."'>{$parametros['default']}</option>";
    }

    if(isset($parametros['options']))
    {
        if(isset($parametros['options_default']))
        {
            echo "<option value='{$parametros['options_default'][$parametros['valor']]}'>{$parametros['options_default'][$parametros['descricao']]}</option>";
        }

        foreach($parametros['options'] as $option)
        {
            $selected = "";
            if(is_array($value))
            {
                if(isset($value['options']) && isset($value['valor']))
                {
                    foreach($value['options'] as $option_selected)
                    {
                        if($option_selected[$value['valor']] == $option[$parametros['valor']])
                            $selected = "selected";
                    }
                }
            }
            else
            {
                if($value == $option[$parametros['valor']])
                    $selected = "selected";
            }

            if($parametros['descricao'])
                echo "<option $selected value='{$option[$parametros['valor']]}'>{$option[$parametros['descricao']]}</option>";
        }
    }
    echo "
        </select>
    ";
}

/**
 * Campo hidden
 * @param $field_name
 * @param $descricao
 * @param $value
 * @param $parametros
 */
function template_hidden($field_name, $descricao, $value, $parametros)
{
    echo
    "
        <input ".issetor($parametros['tags'])." class='".issetor($parametros['class'])."' id='$field_name' name='$field_name' type='hidden' value='$value' />
    ";
}

/**
 * Email
 * @param $field_name
 * @param $descricao
 */
function template_email($field_name, $descricao, $value, $parametros)
{
    echo
    "
        <div class='form-group'>
            <label class='control-label' for='$field_name;'>{$descricao}</label>
            <input ".issetor($parametros['tags'])." class='form-control ".issetor($parametros['class'])."' id='$field_name' name='$field_name' type='email' value='$value' />
        </div>
    ";
}
/**
 * Text
 * @param $field_name
 * @param $descricao
 */
function template_textarea($field_name, $descricao, $value, $parametros)
{
    echo
    "
        <div class='form-group'>
            <label class='control-label' for='$field_name'>{$descricao}</label>
            <textarea rows='10' ".issetor($parametros['tags'])." style='width:100%' class='ace ".issetor($parametros['class'])."' id='$field_name' name='$field_name'>$value</textarea>


        </div>
    ";
}

/**
 * Text
 * @param $field_name
 * @param $descricao
 */
function template_text($field_name, $descricao, $value, $parametros)
{
    $type = isset($parametros['type']) ? $parametros['type'] : 'text';

    echo
    "
        <div class='form-group'>
            <label class='control-label' for='$field_name'>{$descricao}</label>
            <input ".issetor($parametros['tags'])." class='form-control ".issetor($parametros['class'])."' id='$field_name' name='$field_name' type='$type' value='$value' />
        </div>
    ";
}

/**
 * Text
 * @param $field_name
 * @param $descricao
 */
function template_date($field_name, $descricao, $value, $parametros)
{
    echo
        "
        <div class='form-group'>
            <div class='input-group' id='$field_name'>
                <div class='input-group-content'>
                    <label>{$descricao}</label>
                    <input ".issetor($parametros['tags'])." type='text' name='$field_name' value='".app_date_mysql_to_mask($value, "d/m/Y")."' class='form-control datepicker ".issetor($parametros['class'])."'>
                </div>
                <span class='input-group-addon'><i class='fa fa-calendar'></i></span>
            </div>
        </div>
    ";
}

/**
 * Text
 * @param $field_name
 * @param $descricao
 */
function template_password($field_name, $descricao, $value, $parametros)
{
    echo
    "
        <div class='form-group'>
            <label class='control-label' for='$field_name'>{$descricao}</label>
            <input class='form-control slug' id='$field_name' name='$field_name' type='password' value='' />
        </div>
    ";
}


/**
 * Imagem
 * @param $field_name
 * @param $descricao
 * @param $value
 * @param $parametros
 */
function template_image($field_name, $descricao, $value, $parametros)
{
    if(!isset($parametros['path']))
        $parametros['path'] = "";

    if(!isset($parametros['size']))
        $parametros['size'] = "250/250";

    echo "
        <div class='form-group'>

           ";
    if(isset($value) && $value != '' && $value) {
        echo "
            <div class='col-md-3'>
                <div class='row'>
                    <img src='".base_url("services/image/get_image/{$parametros['path']}/$value/{$parametros['size']}")."'  width='200'/>
                </div>
            </div>";
    }
    echo "
            <div class='col-md-6'>
                <label class=' control - label' for='$field_name'>$descricao</label>
                <input type='file' name='$field_name' class='margin - none' />
            </div>
        </div>
    ";
}

/**
 * Tabela
 * @param $rows
 * @param $fields
 * @param $actions
 */
function template_table($rows, $fields, $actions, $options = array())
{
    if(!isset($options['class']))
        $options['class'] = "table";

    echo "<table id='".issetor($options['id'])."' class='{$options['class']}'>

            <!-- Table heading -->
            <thead>
                <tr>";
                    foreach($fields as $field)
                    {
                        if(isset($field['name']))
                            echo "<th>{$field['name']}</th>";
                        else if(isset($field['id']))
                            echo "<th>{$field['id']}</th>";
                        else
                            echo "<th> </th>";
                    }
                    if($actions)
                    {
                        echo "<th></th>";
                    }
    echo "      </tr>
            </thead>
            <tbody>";
                foreach($rows as $i => $row)
                {
                    echo "<tr data-id='$i' data-ordem='$i'>";
                    $achou = false;
                    foreach($fields as $field)
                    {
                        if(isset($field['id']) && isset($row[$field['id']]))
                        {
                            $achou = true;
                            $valor = $row[$field['id']];

                            if(isset($field['type']) && $field['type'] == "ativo")
                            {
                                if($valor)
                                    $valor = "<span class='text-primary'>Ativo</span>";
                                else
                                    $valor = "<span class='text-danger'>Desativado</span>";
                            }
                            else if(isset($field['type']) && $field['type'] == "image")
                            {
                                if($valor && app_is_img($valor))
                                    $valor = "<img class='img img-responsive' width='100px' src='".app_assets_url("midias/auto_auto/$valor","upload")."'>";
                                else
                                    $valor = "<a href='".app_assets_url("midias/$valor", "upload")."' target='_blank'>Abrir</a>";
                            }


                            echo "<th class=''>{$valor}</th>";
                        }
                        else
                        {
                            echo "<th>---</th>";
                        }
                    }
                    if(!$achou)
                    {
                        echo "<th class=''></th>";
                    }

                    if($actions)
                    {
                        echo "<th class='text-right'>";

                        //print_r($actions);exit;
                        foreach($actions as $action)
                        {
                            if(isset($action['controller']) && $action['acao'] && app_check_permission( $action['acao'], $action['controller'])) {

                                echo "<a href='" . set_array_string(admin_url($action['link']), 'ROW', $row) . "'  data-toggle='tooltip' data-placement='bottom' title='' data-original-title='{$action['name']}' class='{$action['class']}'>";
                                if (isset($action['icon']))
                                    echo "<i class='{$action['icon']}'></i> ";
                                echo "</a>";
                            }
                        }
                        echo "</th>";
                    }

                    echo "</tr>";
                }
    echo "
            </tbody>
    </table>";

    if(isset($options['pagination_links']))
    {
        echo $options['pagination_links'];
    }

}


function template_set_vars($conteudo)
{
    $conteudo = str_replace("{ASSETS}", base_url("assets/"), $conteudo);
    $conteudo = str_replace("{UPLOAD}", base_url("assets/upload/"), $conteudo);
    $conteudo = str_replace("{BASE_URL}", base_url(), $conteudo);
    $conteudo = str_replace("{SITE_URL}", base_url(), $conteudo);
    $conteudo = str_replace("{IMG}", base_url('services/image/get_image'), $conteudo);

    return $conteudo;
}


/**
 * Transforma conteudo
 * @param $content
 */
function template_content_transform_json(&$content)
{
    $CI = &get_instance();

    $chave_inicio = strpos($content, "{");
    $chave_fim = strpos($content, "}");

    if($chave_inicio >= 0 && $chave_fim >=0)
    {
        $var = substr($content, $chave_inicio + 1, $chave_fim - $chave_inicio - 1);
        $var_complete = "{".$var."}}";

        $function_fim = strpos($var, "{");
        $function = trim(substr($var, 0, $function_fim));

        $options = substr($var, $function_fim + 1);
        $options = substr($options, 0, strlen($options) -1);
        $options = explode("|", $options);

        $args = array();
        foreach($options as $option)
        {
            $option = explode("=", $option);

            if(sizeof($option) == 2)
                $args[trim($option[0])] = trim($option[1]);
        }

        if($function)
        {
            $html = call_user_func("template_{$function}", $args);
            $content = str_replace($var_complete, $html, $content);
            return template_content_transform_json($content);
        }
        else
        {
            return ($content);
        }
    }
    else
    {
        return $content;
    }
}
/**
 * Transforma conteudo
 * @param $content
 */
function template_content_transform(&$content)
{
    $CI = &get_instance();

    $chave_inicio = strpos($content, "[");
    $chave_fim = strpos($content, "]");

    if($chave_inicio && $chave_fim)
    {
        $var = substr($content, $chave_inicio + 1, $chave_fim - $chave_inicio - 1);
        $var_complete = "[".$var."]]";

        $function_fim = strpos($var, "[");
        $function = trim(substr($var, 0, $function_fim));

        $options = substr($var, $function_fim + 1);
        $options = substr($options, 0, strlen($options) -1);
        $options = explode(",", $options);

        $args = array();
        foreach($options as $option)
        {
            $option = explode("=", $option);

            if(sizeof($option) == 2)
                $args[trim($option[0])] = trim($option[1]);
        }

        if($function && $args)
        {
            $html = call_user_func("template_{$function}", $args);
            $content = str_replace($var_complete, $html, $content);
            return template_content_transform($content);
        }
        else
        {
            return ($content);
        }
    }
    else
    {
        return $content;
    }
}
