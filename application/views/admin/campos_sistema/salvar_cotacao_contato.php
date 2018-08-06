<?php
if($_POST)
    $row = $_POST;

    $melhor_horario = array(
        'M' => 'MANHÃ',
        'T' => 'TARDE',
        'N' => 'NOITE',
        'C' => 'COMERCIAL',
        'Q' => 'QUALQUER HORARIO'

    );

    if(!isset($row['quantidade_contatos'])){
        $row['quantidade_contatos'] = 1;

        $i=0;
        if((isset($cotacao)) && ($cotacao['email'])){
            $row['cliente_terceiro'][$i] = 0;
            $row['contato_tipo_id'][$i] = 1;
            $row['contato'][$i] = $cotacao['email'];
            $i++;
        }
        if((isset($cotacao)) && ($cotacao['telefone'])){
            $row['cliente_terceiro'][$i] = 0;
            $row['contato_tipo_id'][$i] = 2;
            $row['contato'][$i] = $cotacao['telefone'];
            $i++;
        }
        $row['quantidade_contatos'] = $i;





    }


?>
<br />
<br />
<input type="hidden" name="quantidade_contatos" id="quantidade_contatos" value="<?php echo $row['quantidade_contatos']; ?>">
<div class="row"></div>
<h3>Contatos:</h3>
<?php for ($i = 0; $i < $row['quantidade_contatos']; $i++) : ?>
<div id="contato" class="contato contato_add_form form-group card card-underline" style="font-size: 12px">
       <?php $field_name = "cliente_terceiro[$i]"; ?>
        <div class="col-md-2<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
            <label class="radio-inline radio-styled radio-primary">
                <input name="<?php echo $field_name;?>" value="0" type="radio"
                    <?php if(isset($row['cliente_terceiro'])){if($row['cliente_terceiro'] == 0) {echo " checked ";};}; ?>
                    <?php if(isset($row['cliente_terceiro'][$i])){if($row['cliente_terceiro'][$i] == 0 ) {echo " checked ";};}; ?>
                ><span>Cliente</span>
            </label><br>
            <label class="radio-inline radio-styled radio-primary">
                <input name="<?php echo $field_name;?>" value="1" type="radio"
                    <?php if(isset($row['cliente_terceiro'])){if($row['cliente_terceiro'] == 1) {echo " checked ";};}; ?>
                    <?php if(isset($row['cliente_terceiro'][$i])){if($row['cliente_terceiro'][$i] == 1 ) {echo " checked ";};}; ?>
                ><span>Terceiro</span>
            </label>
            <?php echo app_get_form_error($field_name); ?>
        </div>
        <?php $field_name = "contato_nome[$i]"; ?>
        <div class="col-md-9<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
            <?php if(isset($row['cliente_terceiro'][$i]) && $row['cliente_terceiro'][$i] == 0) : ?>
                <input class="form-control" placeholder="Nome" disabled="disabled" id="contato_nome_<?php echo $i; ?>" name="<?php echo $field_name;?>" type="text" value="<?php if(isset($nome_segurado)){ echo $nome_segurado; } ?>" />
            <?php elseif(isset($row['cliente_terceiro'][$i]) && $row['cliente_terceiro'][$i] == 1) : ?>
                <input class="form-control" placeholder="Nome" id="contato_nome_<?php echo $i; ?>" name="<?php echo $field_name;?>" type="text" value="<?php if(isset($row['contato_nome'][$i])){ echo $row['contato_nome'][$i]; } ?>" />
            <?php else : ?>
                <input class="form-control" placeholder="Nome" id="contato_nome_<?php echo $i; ?>" name="<?php echo $field_name;?>" type="text" value="<?php if(isset($row['contato_nome'][$i])){ echo $row['contato_nome'][$i]; } ?>" />
            <?php endif; ?>
            <?php echo app_get_form_error($field_name); ?>
        </div>
        <div class="col-md-1 text-center">
            <?php if($i == 0) : ?>
                <button type="button" insert="#contato" class="btAdicionarContato btn btn-primary">+</button>
                <button type="button" class="btRetirarContato hidden btn btn-error">-</button>
            <?php else : ?>
                <button type="button" class="btRetirarContato btn btn-error">-</button>

            <?php endif; ?>

        </div>
        <?php $field_name = "contato_tipo_id[$i]"; ?>
        <div class="col-md-3<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
            <select class="form-control tipo_contato" name="<?php echo $field_name;?>" id="contato_tipo_id_<?php echo $i; ?>">
                <option name="" value="">Tipo</option>
                <?php
                foreach($contato_tipo as $linha) { ?>
                    <option name="" value="<?php echo $linha['contato_tipo_id'] ?>"
                        <?php if(isset($row['contato_tipo_id'])){if($row['contato_tipo_id'] == $linha['contato_tipo_id']) {echo " selected ";};}; ?>
                        <?php if(isset($row['contato_tipo_id'][$i])){if($row['contato_tipo_id'][$i] == $linha['contato_tipo_id']) {echo " selected ";};}; ?>
                        >
                        <?php echo $linha['nome']; ?>
                    </option>
                <?php }  ?>
            </select>
            <?php echo app_get_form_error($field_name); ?>
        </div>
        <?php $field_name = "contato[$i]"; ?>
        <div class="col-md-4<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
            <input class="form-control" placeholder="Contato" id="contato_<?php echo $i; ?>" name="<?php echo $field_name;?>" type="text" value="<?php if(isset($row['contato'][$i])){ echo $row['contato'][$i]; } ?>" />
            <?php echo app_get_form_error($field_name); ?>
        </div>
        <?php $field_name = "melhor_horario[$i]"; ?>
        <div class="col-md-3<?php echo (app_is_form_error($field_name)) ? ' has-error' : ''; ?>">
            <select class="form-control melhor_horario" name="<?php echo $field_name;?>" id="melhor_horario_<?php echo $i?>">
                <option name="" value="">Melhor Horário</option>
                <?php
                foreach($melhor_horario as $idx => $linha) { ?>
                    <option name="" value="<?php echo $idx; ?>"
                        <?php if(isset($row['melhor_horario'][$i])){if($row['melhor_horario'][$i] == $idx) {echo " selected ";};}; ?>
                    >
                        <?php echo $linha; ?>
                    </option>
                <?php }  ?>
            </select>
            <?php echo app_get_form_error($field_name); ?>
        </div>
    <div class="row">
        <br/>
        <br/>
        <br/>


    </div>
</div>
<?php endfor; ?>
