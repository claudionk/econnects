<?php
if($_POST)
    $row = $_POST;

//print_r($row);
?>

<div class="row">
    <div class="col-md-12">

        <?php
            $original_field_name = $field_name;
            $field_name = 'ean';
        ?>
        <div class="col-md-3 <?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
            <div class="form-group">
                <label class=" control-label" for="<?php echo $field_name;?>">EAN</label>
                <input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" value="<?php echo isset($row[$field_name]) ?  $row[$field_name] : ''; ?>" style="width: 100%;" />
                <?php echo app_get_form_error($field_name); ?>
            </div>
        </div>

        <?php $field_name = 'equipamento_categoria_id'; ?>
        <div class="col-md-3 <?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
            <div class="form-group">
                <label class=" control-label" for="<?php echo $field_name;?>">CATEGORIA DO EQUIPAMENTO</label>
                <select name="<?php echo $field_name;?>" data-selected="<?php echo isset($row[$field_name]) ?  $row[$field_name] : ''; ?>" id="js-categorias-ajax" class="js-categorias-ajax" style="width: 100%;">
                    <option value="" selected="selected">Categoria do equipamento</option>
                </select>
                <?php echo app_get_form_error($field_name); ?>
            </div>
        </div>

        <?php $field_name = 'equipamento_marca_id'; ?>
        <div class="col-md-3 <?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
            <div class="form-group">
                <label class=" control-label" for="<?php echo $field_name;?>">MARCA DO EQUIPAMENTO</label>
                <select name="<?php echo $field_name;?>" data-selected="<?php echo isset($row[$field_name]) ?  $row[$field_name] : ''; ?>" id="js-marcas-ajax" class="js-<?php echo $field_name;?>-ajax" style="width: 100%;">
                    <option value="" selected="selected">Marca do equipamento</option>
                </select>
                <?php echo app_get_form_error($field_name); ?>
            </div>
        </div>

        <!--div class="col-md-3 <?php echo (app_is_form_error($original_field_name)) ? ' has-error' : ''; ?>">
            <div class="form-group">
                <label class=" control-label" for="<?php echo $original_field_name;?>"><?php echo $field_label ?></label>
                <input class="form-control" name="<?php echo $original_field_name;?>" value="<?php echo isset($row[$original_field_name]) ?  $row[$original_field_name] : ''; ?>" style="width: 100%;" />
                <?php echo app_get_form_error($field_name); ?>
            </div>
        </div-->

        <?php $field_name = 'equipamento_nome'; ?>
        <input type="hidden" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>" value="<?php echo isset($row[$field_name]) ?  $row[$field_name] : ''; ?>" />

        <?php $field_name = 'equipamento_id'; ?>
        <div class="col-md-3 <?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
            <div class="form-group">
                <label class=" control-label" for="<?php echo $field_name;?>">MODELO</label>
                <select name="<?php echo $field_name;?>" data-selected="<?php echo isset($row[$field_name]) ?  $row[$field_name] : ''; ?>" id="js-equipamentos-ajax" class="js-<?php echo $field_name;?>-ajax" style="width: 100%;">
                    <option value="" selected="selected">Modelo</option>
                </select>
                <?php echo app_get_form_error($field_name); ?>
            </div>
        </div>
    </div>
</div>
