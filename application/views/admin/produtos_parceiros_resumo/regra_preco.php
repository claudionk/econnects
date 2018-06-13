<?php if($row['regrapreco_config']) : ?>

    <?php foreach ($row['regrapreco'] as $item) : ?>
        <div class="form-group">
            <label class="col-md-2 control-label"><?php echo $item['regra_preco_nome']; ?></label>
            <div class="col-md-2"><input readonly class="form-control" type="text" value="<?php echo $item['parametros']; ?>" /></div>
        </div>

    <?php endforeach; ?>

<?php else : ?>

    <strong>Não Configurado</strong>
<?php endif;  ?>