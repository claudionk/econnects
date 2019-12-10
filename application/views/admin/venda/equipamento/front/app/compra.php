
<?php $this->load->view('admin/venda/equipamento/front/head', array('step' => 2, 'produto_parceiro_id' =>  issetor($produto_parceiro_id), 'title' => 'LANDING PAGE')); ?>

<div class="row">
    <div class="col-md-12"><?php $this->load->view('admin/partials/messages'); ?></div>
</div>

<div class="row">
    <div class="col-md-12 ">
            <div class="btns btn-fixed">
                <a href="<?php echo base_url("admin/venda_equipamento/equipamento/74?token=493b85cb650bf9b99813f1c4b29c36e1&layout=front")?>" class="btn btn-app btn-primary btn-proximo border-primary background-primary" >
                    Cotação
                </a>
            </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="text-page">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque rutrum, mi sit amet molestie elementum, orci dolor dictum lorem, id tincidunt nulla metus at quam. Quisque tempus lectus a magna varius, at vestibulum massa ultrices. Donec semper laoreet auctor. Proin accumsan bibendum accumsan. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas quis justo neque. Integer vel erat mi. Praesent imperdiet, erat at vestibulum varius, mi dolor suscipit turpis, eu porta nulla magna nec odio. Pellentesque at nisi pretium, euismod risus et, commodo nisi.

            Vivamus pulvinar tellus eget augue sollicitudin, sed malesuada libero molestie. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum bibendum vehicula lectus in eleifend. Aenean porttitor purus nec leo tempor, ac efficitur turpis posuere. Duis id enim ac arcu egestas commodo. Vestibulum ut leo dolor. Vestibulum sed nunc ut mi posuere finibus quis a quam. Curabitur nec justo vel orci porta tempor.
        </div>
    </div>
</div>

<?php $this->load->view('admin/venda/equipamento/front/footer'); ?>