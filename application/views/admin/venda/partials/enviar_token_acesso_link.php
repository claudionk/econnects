<?php if ($layout != "front" && !empty($exibe_url_acesso_externo)) { ?>
    <!-- Trigger the modal with a button -->
    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_acesso_externo"><i class="fa fa-link"></i> ENVIO PARA <?php echo ($exibe_url_acesso_externo_tipo == 'cancelamento') ? 'CANCELAMENTO' : 'PAGAMENTO' ?> EXTERNO</button>
    <br>
    <br>
<?php } ?>