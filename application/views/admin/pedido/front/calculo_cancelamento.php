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

            <?php $this->load->view('admin/pedido/front/step', array('step' => 2, 'produto_parceiro_id' => $produto_parceiro_id, 'title' => 'Cálculo de Restituição')); ?>

            <div id="cancelamento_content">
                <div class="col-md-12">
                    <div class="modal-body">
                        <div style="text-align:center;margin:20px 0;">O valor a ser devolvido é de <strong id="vldevolucao" style="color:red;"><?php echo $valor_estorno_total ?></strong></div>
                        <div style="text-align:center;margin: 20px 0;"><b>Data vigência:</b> <?php echo $inicio_vigencia ?> <b>a</b> <?php echo $fim_vigencia ?></div>
                        <div style="text-align:center;margin: 0 0 20px 0;">Quantidade de dias utilizados: <b><?php echo $dias_utilizados ?></b></div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-xs-12 btns" >
                            <a class="btn btn-app btn-primary btn-proximo background-primary border-primary" id="btnNextSolicitacaoDesistencia" onclick="$('#validateSubmitForm').submit();" >
                                Próximo <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
