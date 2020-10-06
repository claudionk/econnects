<?php
$this->load->view('admin/venda/equipamento/front/step', array('step' => 3));

if($_POST)
    $row = $_POST;
?>

<div class="step-six">
    <?php if($hasApp == true):?>
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
                <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/google-play.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
            </a>
        <?php } ?>
    <?php endif; ?>

    <?php if($hasApp == false): ?>
        <h3 class="text-ultra-bold text-success">SEU PEDIDO FOI CONFIRMADO COM SUCESSO</h3>
        <p class="text-sm-left">Você receberá no e-mail <b><?= $email; ?></b> o Bilhete do Seguro, as Condições Gerais do Seguro e o Termo de Autorização de Cobrança.</p>
    <?php endif; ?>

</div>

<?php if($hasApp == true):?>
    
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
                        <a href="" title="">
                            <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/google-play.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;margin: 0 auto;" class="img-responsive" />
                        </a>
                    <?php } ?>               
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $this->load->view('admin/venda/equipamento/components/btn-info'); ?>

<?php $this->load->view('admin/venda/equipamento/components/btn-whatsapp'); ?>

<?php $this->load->view('admin/venda/equipamento/components/footer'); ?>
