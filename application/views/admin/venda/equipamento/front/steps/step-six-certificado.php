<?php
$this->load->view('admin/venda/equipamento/front/step', array('step' => 3));

if($_POST)
    $row = $_POST;
?>

<div class="step-six">
    <h1 class="title">
        Baixe <br /> nosso app <br /> para emitir <br /> seu seguro
        <span class="subtitle">e tenha tudo na palma da mão!</span>
    </h1>

    <?php if (emptyor($equipamento_marca_id, 30029) == 30029) { # APPLE ?>
        <a href="" title="">
            <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/app-store.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
        </a>
    <?php }

    if (emptyor($equipamento_marca_id, 30015) == 30015) { # SAMSUNG ?>
        <a href="<?php echo $this->config->item('URL_APLICATIVO') ?>" title="">
            <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/google-play.png", 'admin'); ?>" alt="Play Store" title="Play Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
        </a>
    <?php } ?>
</div>

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

                <?php if (emptyor($equipamento_marca_id, 30029) == 30029) { # APPLE ?>
                    <a href="" title="">
                        <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/app-store.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
                    </a>
                <?php }

                if (emptyor($equipamento_marca_id, 30015) == 30015) { # SAMSUNG ?>
                    <a href="<?php echo $this->config->item('URL_APLICATIVO') ?>" title="">
                        <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/google-play.png", 'admin'); ?>" alt="Play Store" title="Play Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/venda/equipamento/components/btn-info'); ?>

<?php $this->load->view('admin/venda/equipamento/components/btn-whatsapp'); ?>

<?php $this->load->view('admin/venda/equipamento/components/footer'); ?>
