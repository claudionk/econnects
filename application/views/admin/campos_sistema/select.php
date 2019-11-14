<?php
if($_POST)
    $row = $_POST;

?>
<div class="col-md-<?php echo $tamanho ?> <?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
    <div class="form-group">
        <label class="control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
        <select class="<?php echo issetor($class) ?>" data-selected="<?php echo isset($row[$field_name]) ?  $row[$field_name] : ''; ?>" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>" style="width: 100%;">
            <option name="" value="">Selecione</option>
            <?php foreach($list as $linha) { ?>
                <option name="" value="<?php echo $linha ?>"
                    <?php if(isset($row[$field_name])){if($row[$field_name] == $linha) {echo " selected ";};}; ?> >
                    <?php echo $linha ?>
                </option>
            <?php }  ?>
        </select>
        <?php echo app_get_form_error($field_name); ?>
    </div>
</div>

<?php if ($field_name == 'equipamento_id'){ ?>
<input type="hidden" name="equipamento_nome" id="equipamento_nome" value="">
<?php } ?>
