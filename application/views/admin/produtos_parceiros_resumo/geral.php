<?php if($row['geral_config']) : ?>

    <?php $field_name = 'salvar_cotacao_formulario';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Formulário Salvar cotação *</label>
        <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo ($row[$field_name] == '1') ? 'SIM' : 'NÃO'; ?>" /></div>
    </div>
    <?php $field_name = 'venda_habilitada_admin';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Venda pelo Admin *</label>
        <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo ($row[$field_name] == '1') ? 'SIM' : 'NÃO'; ?>" /></div>
    </div>
    <?php $field_name = 'venda_habilitada_web';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Venda pela WEB *</label>
        <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo ($row[$field_name] == '1') ? 'SIM' : 'NÃO'; ?>" /></div>
    </div>
    <?php $field_name = 'venda_carrinho_compras';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Venda com carrinho de compras *</label>
        <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo ($row[$field_name] == '1') ? 'SIM' : 'NÃO'; ?>" /></div>

    </div>
    <?php $field_name = 'venda_multiplo_cartao';?>

    <?php $field_name = 'calculo_tipo';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Tipo de cálculo</label>
        <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo $row[$field_name]['nome']; ?>" /></div>
    </div>

    <?php $field_name = 'apolice_sequencia';?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Certificado *</label>
        <div class="col-md-2"><input readonly class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo ($row[$field_name] == '1') ? 'SEQUENCIAL' : 'RANGE'; ?>" /></div>
    </div>

<?php else : ?>

    <strong>Não Configurado</strong>
<?php endif;  ?>