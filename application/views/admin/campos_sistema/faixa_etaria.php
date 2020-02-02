<?php
if($_POST)
    $row = $_POST;
//print_r($list);
?>

        <label class=" control-label" for="<?php echo $field_name;?>"><?php echo $field_label ?></label>
        <div class="col-md-12">
            <?php
                $count = 0;
            foreach($list[$field_name] as $linha) { 
                if ($count == 4){
                 <div class="col-md-12">
                }
            ?>
                <div class="col-md-<?php echo $tamanho ?>  form-group<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
                 De <?php echo (int)$linha['inicial']; ?> a <?php echo (int)$linha['final']; ?> anos
                 <input class="form-control" type="text" <?php echo issetor($class) ?> id="qtd" name="qtd" type="text" value="<?php echo  isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /> 
                 </div>     
            <?php 
            $count++;
            }  ?>
        </div>
    <?php echo app_get_form_error($field_name); ?>
    

