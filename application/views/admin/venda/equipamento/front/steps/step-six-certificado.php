<?php $this->load->view('admin/venda/equipamento/front/step'); ?>

<?php
if ($_POST)
    $row = $_POST;
?>
<style>
    .confirm_message {
        display: none;
    }

    .load {
        display: block;
    }
</style>

<div class="step-six">
    <section class="load">
        <!-- Widget With Progress Bar -->
        <strong>Aguardando confirmação</strong>
        <div class="progress progress-primary" id="widget-progress-bar">
            <div class="progress-bar" style="width: 5%;">
                <strong class="text-progress-bar"></strong>
                <strong class="progress-bar"></strong></div>
        </div>
    </section>
    <section class="confirm_message">
        <?php if ($hasApp == true) : ?>
            <h1 class="title">
                Baixe <br /> nosso app <br /> para emitir <br /> seu seguro
                <span class="subtitle">e tenha tudo na palma da mão!</span>
            </h1>

            <?php if (emptyor($equipamento_marca_id, 30029) == 30029) { # APPLE 
            ?>
                <a href="" title="">
                    <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/app-store.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
                </a>
            <?php }

            if (emptyor($equipamento_marca_id, 30015) == 30015) { # SAMSUNG 
            ?>
                <a href="" title="">
                    <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/google-play.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
                </a>
            <?php } ?>
        <?php endif; ?>

        <?php if ($hasApp == false) : ?>
            <h3 class="text-ultra-bold text-success">SEU PEDIDO FOI CONFIRMADO COM SUCESSO</h3>
            <p class="text-sm-left">Você receberá no e-mail <b><?= $email; ?></b> o Bilhete do Seguro, as Condições Gerais do Seguro e o Termo de Autorização de Cobrança.</p>
        <?php endif; ?>

    </section>
</div>

<?php if ($hasApp == true) : ?>

    <!-- modal more info -->
    <div class="modal modal-app fade" id="modalBaixeApp" role="dialog">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <h1 class="title">
                        Baixe <br /> nosso app <br /> para emitir <br /> seu seguro
                        <span class="subtitle">e tenha tudo na palma da mão!</span>
                    </h1>

                    <?php if (emptyor($equipamento_marca_id, 30029) == 30029) { # APPLE 
                    ?>
                        <a href="" title="">
                            <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/app-store.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
                        </a>
                    <?php }

                    if (emptyor($equipamento_marca_id, 30015) == 30015) { # SAMSUNG 
                    ?>
                        <a href="" title="">
                            <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/google-play.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<script>
    let Payment = {
        pedidoId: null,
        loadPercentage: 0,

        getPedidoId: function() {
            return this.pedidoId;
        },
        setPedidoId: function(pedidoIdo) {
            this.pedidoId = pedidoIdo;
            return this;
        },
        confimView: function() {},
        errorMessage: function() {
            $(".load").hide();
            $(".confirm_message").hide();
            $('.fail_message').show();
        },
        execute: function() {
            this.load();
            $.ajax({
                    type: "POST",
                    url: base_url + "admin/gateway/executaPagamento/" + this.getPedidoId(),
                    cache: false,
                })
                .done(function(result) {
                    console.log(result)
                    $(".confirm_message").show();
                    $(".load").hide();

                })
                .fail(function(result) {
                    console.log(result)
                });
        },
        setLoadPercentage: function(loadPercentage) {
            this.loadPercentage += loadPercentage;
        },
        getLoadPercentage: function() {
            return this.loadPercentage;
        },
        load: function() {
            this.setLoadPercentage(this.getLoadPercentage() + 1)
            console.log(this.getLoadPercentage());
            $('.progress-bar').width(this.getLoadPercentage() + "%");
            if (this.getLoadPercentage() < 100) {
                setInterval(() => this.load(), 2000)
            }
        }
    }

    $(document).ready(function() {
        Payment.setPedidoId(<?php echo $pedido_id; ?>);
        Payment.execute()
    })
</script>

<?php $this->load->view('admin/venda/equipamento/components/btn-info'); ?>

<?php $this->load->view('admin/venda/equipamento/components/btn-whatsapp'); ?>

<?php $this->load->view('admin/venda/equipamento/components/footer'); ?>