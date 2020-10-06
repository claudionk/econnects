<?php
if ($_POST) {
    $row = $_POST;
}
?>

<form class="form form-cancelamento form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" >
    <input type="hidden" name="produto_parceiro_id" value="<?php if (isset($produto_parceiro_id)) echo $produto_parceiro_id; ?>" />
    <input type="hidden" name="pedido_id" id="pedido_id" value="<?php if (isset($pedido_id)) echo $pedido_id; ?>" />

    <!-- Widget -->
    <div class="row">
        <div class="col-md-6">
            <?php $this->load->view('admin/partials/validation_errors'); ?>
            <?php $this->load->view('admin/partials/messages'); ?>
        </div>
    </div>

    <!-- Collapsible Widgets -->
    <div class="row">
        <div class="col-md-12">

            <?php $this->load->view('admin/pedido/front/step', array('step' => 4, 'produto_parceiro_id' => $produto_parceiro_id, 'title' => 'DADOS BANCÁRIOS')); ?>

            <div class="col-md-12">
                    <?php $this->load->view('admin/pedido/dados_bancarios'); ?>
            </div>

            <div class="col-xs-12 btns" >
                <a class="btn btn-app btn-primary btn-proximo background-primary border-primary" onclick="$('#validateSubmitForm').submit();" >
                    Próximo <i class="fa fa-angle-right" aria-hidden="true"></i>
                </a>
            </div>

        </div>
    </div>
</form>
