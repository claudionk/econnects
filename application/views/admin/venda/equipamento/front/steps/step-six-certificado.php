<?php $this->load->view('admin/venda/equipamento/front/step'); ?>

<?php
if($_POST)
    $row = $_POST;
?>

<div class="step-six">
    <h1 class="title">
        Baixe <br /> nosso app <br /> para emitir <br /> seu seguro
        <span class="subtitle">e tenha tudo na palma da mão!</span>
    </h1>

    <a href="" title="">
        <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/app-store.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;" />
    </a>

    <a href="" title="">
        <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/google-play.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;" />
    </a>
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

                <a href="" title="">
                    <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/app-store.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;" class="img-responsive" />
                </a>

                <a href="" title="">
                    <img src="<?php echo app_assets_url("modulos/venda/equipamento/images/google-play.png", 'admin'); ?>" alt="App Store" title="App Store" style="width: 210px;" class="img-responsive" />
                </a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/venda/equipamento/components/btn-info'); ?>

<?php $this->load->view('admin/venda/equipamento/components/btn-whatsapp'); ?>

<?php $this->load->view('admin/venda/equipamento/components/footer'); ?>