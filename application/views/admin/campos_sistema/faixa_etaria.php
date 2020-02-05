<?php
if($_POST)
    $row = $_POST;
//print_r($list);
?>

<div class="col-md-12 form-group<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
    <div class="form-group">
        <label class=" control-label inli" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
    </div>
    <div class="form-group">
        <?php
        foreach($list[$field_name] as $linha) { ?>
            <div class="col-md-<?php echo $tamanho ?> form-group<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
                <label class=" control-label inli" for="qtd_<?php echo (int)$linha['inicial']; ?>" >De <?php echo (int)$linha['inicial']; ?> a <?php echo (int)$linha['final']; ?> anos</label>
             <input class="form-control" type="text" <?php echo issetor($class) ?> id="qtd_<?php echo (int)$linha['inicial']; ?>" name="faixa_etaria[<?php echo (int)$linha['inicial']; ?>_<?php echo (int)$linha['final']; ?>]" type="text" value="<?php echo isset($linha['faixa_etaria']) ? (int)$linha['faixa_etaria'] : 0; ?>" /> 
             </div>     
        <?php }  ?>
    
    <?php echo app_get_form_error($field_name); ?>
    </div>
</div>
