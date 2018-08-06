


<div class="row" id="pagamento-faturamento">

    <div class="col-md-12">
        <?php $hd = "";  ?>
        <?php foreach ($forma['pagamento'] as $bandeira) : ?>
            <?php $field_name = "parcelamento_{$bandeira['produto_parceiro_pagamento_id']}";?>
            <div <?php echo $hd; ?> class="form-group parcelamento parcelamento_<?php echo $bandeira['produto_parceiro_pagamento_id']; ?>">
                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Parcelamento *</label>
                <div class="col-md-4">
                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                        <?php foreach($bandeira['parcelamento'] as $parcela => $linha) : ?>
                            <option name="" value="<?php echo $parcela; ?>"
                                <?php if(isset($row[$field_name])){if($row[$field_name] == $parcela) {echo " selected ";};}; ?> >
                                <?php echo $linha; ?>
                            </option>
                        <?php endforeach;  ?>
                    </select>
                </div>
            </div>
            <?php $hd = 'style="display: none;"';  ?>
        <?php endforeach; ?>
    </div>

</div>