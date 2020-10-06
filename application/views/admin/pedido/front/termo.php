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

            <?php $this->load->view('admin/pedido/front/step', array('step' => 3, 'produto_parceiro_id' => $produto_parceiro_id, 'title' => 'SOLICITAÇÃO DE DESISTÊNCIA')); ?>

            <div id="term-block">
                <div id="term-block-content" style="border:2px solid #ddd; overflow-y: scroll; background:white;position: relative;width: 100%;scroll-behavior: auto;height: 204px;">
                    <div style="margin:5%">
                        <?php echo $solicitacao_desistencia; ?>
                    </div>
                </div>

                <div id="div-ask-read-term" class="col-xs-11">
                    <h5 class=" text-justify text-sm-left">
                        Por favor, leia o texto até o final para habilitar o campo de aceite do termo.
                    </h5>
                </div>
                <div class="col-xs-1">
                    <a href="javascript:void(0)" data-toggle="tooltip" class="tooltip-icon terms" data-placement="left" title="Leia o Termo até o final para habilitar o botão de Aceite do termo">
                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="col-xs-12">
                    <hr>
                </div>

                <?php if($isConfirmaEmail == true): ?>

                    <div class="col-xs-12">
                        <h5 class="text-sm-left">Você receberá no e-mail abaixo o Termo de Solicitação de Desistência do Seguro.</h5>
                        <input type="email" name="email" class="form-control" value="<?= $email; ?>">
                    </div>

                    <div class="col-xs-12">
                        <hr>
                    </div>

                <?php endif; ?>

                <div id="aceite-term-check" class="col-xs-11" style="display:none">
                    <label>
                        <input type="checkbox" id="check_termo" name="check_termo" /> Estou de acordo com os termos de uso.
                    </label>
                </div>

                <div class="col-xs-12 btns" id="btnSubmit" style="display: none;">
                    <a class="btn btn-app btn-primary btn-proximo background-primary border-primary" onclick="$('#validateSubmitForm').submit();" id="btn-proximo">
                        Próximo <i class="fa fa-angle-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
    function checkTerm() {
        if ($("#check_termo:checked").length) {
            $('#btnSubmit').show();
            $("html, body").animate({ scrollTop: $(document).height() }, 500);
        }
    }
    $('document').ready(function() {
        $('.step').css('width:auto')

        $('#check_termo').on('click', function() {
            checkTerm();
        });

        $('#term-block-content').on('scroll', function() {
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                $('#aceite-term-check').show()
            }
        })

    })
</script>