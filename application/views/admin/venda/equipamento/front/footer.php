
<div class="maisprodutos">
	<?php $this->load->view('admin/venda/equipamento/front/app/comprar'); ?>
</div>

<div class="btns-buyaccount btns">
	<div class="btn-whats">
	    <a href="https://api.whatsapp.com/send?phone=55<?php echo $whatsapp;?>&text=<?php echo $whatsapp_msg;?>." title="Whatsapp">
	        <i class="fa fa-whatsapp"></i>
	    </a>
	</div>
	 <div aria-controls="bs-navbar" aria-expanded="false" data-target="#bs-navbar" data-toggle="collapse" id="menu-comprar" class="btn-buy btn btn-app btn-primary btn-proximo border-primary background-primary">    
		        Mais Produtos
	</div>
	<div class="btn-myaccount btn btn-app btn-primary btn-proximo border-primary background-primary">
	    <a href="<?php echo base_url("$current_uri/login")?>" title="Minha Conta">
	        Minha Conta
	    </a>
	</div>
</div>

<script>
    $("#menu-comprar").click(function(e) {
        e.preventDefault();
        $(".maisprodutos").toggleClass("closed");
    });

    $("#menu-close-compras").click(function(e) {
        e.preventDefault();
        $(".maisprodutos").toggleClass("closed");
    });
</script>