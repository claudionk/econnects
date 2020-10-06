<div class="form-group lineDuplicate" >

    <?php
    $aux = emptyor($aux, '');
    $valueDefault = ($aux == '');

    $field_name = 'unidade_tempo';
    if ($valueDefault)
    {
        $val = isset($row[$field_name]) ? $row[$field_name] : '';
    } else {
        $val = isset($value[$field_name]) ? $value[$field_name] : '';
    }
    ?>
    <div class="col-md-2 control-label" for="<?php echo $field_name;?>">Unidade *</div>
    <div class="col-md-3">
        <select class="form-control <?php echo $field_name;?>" name="<?php echo $field_name.$aux;?>" id="<?php echo $field_name;?>">
            <option value="">Selecione</option>
            <option value="DIA"
                <?php if($val == 'DIA') {echo " selected ";} ?> >Dia
            </option>
            <option value="MES"
                <?php if($val == 'MES') {echo " selected ";} ?> >Mês
            </option>
            <option value="ANO"
                <?php if($val == 'ANO') {echo " selected ";} ?> >Ano
            </option>
            <option value="VALOR"
                <?php if($val == 'VALOR') {echo " selected ";} ?> >Valor
            </option>
            <option value="IDADE"
                <?php if($val == 'IDADE') {echo " selected ";} ?> >Idade
            </option>
            <option value="COMISSAO"
                <?php if($val == 'COMISSAO') {echo " selected ";} ?> >Comissão
            </option>
            <option value="GARANTIA_FABRICANTE"
                <?php if($val == 'GARANTIA_FABRICANTE') {echo " selected ";} ?> >Garantia do Fabricante
            </option>
        </select>
    </div>

    <?php
    $field_name = 'inicial';
    if ($valueDefault) 
    {
        $val = isset($row[$field_name]) ? number_format( (float)$row[$field_name] , 2 , "," , "") : set_value($field_name);
    } else {
        $val = isset($value[$field_name]) ? number_format( (float)$value[$field_name] , 2 , "," , "") : 0;
    }
    ?>
    <div class="col-md-1 control-label" for="<?php echo $field_name;?>">Início *</div>
    <div class="col-md-2"><input class="form-control <?php echo $field_name;?>" id="<?php echo $field_name ?>" name="<?php echo $field_name.$aux ?>" type="text" value="<?php echo $val ?>" /></div>

    <?php
    $field_name = 'final';
    if ($valueDefault) 
    {
        $val = isset($row[$field_name]) ? number_format( (float)$row[$field_name] , 2 , "," , "") : set_value($field_name);
    } else {
        $val = isset($value[$field_name]) ? number_format( (float)$value[$field_name] , 2 , "," , "") : 0;
    }
    ?>
    <div class="col-md-1 control-label" for="<?php echo $field_name;?>">Fim *</div>
    <div class="col-md-2"><input class="form-control <?php echo $field_name;?>" id="<?php echo $field_name ?>" name="<?php echo $field_name.$aux ?>" type="text" value="<?php echo $val ?>" /></div>

</div>
