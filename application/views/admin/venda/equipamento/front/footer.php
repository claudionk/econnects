<div class="btns-buyaccount">
	<div class="btn-whats">
	    <a href="https://api.whatsapp.com/send?phone=55<?php echo $whatsapp;?>&text=<?php echo $whatsapp_msg;?>." title="Whatsapp">
	        <i class="fa fa-whatsapp"></i>
	    </a>
	</div>
	<div class="btn-buy btn btn-app btn-primary btn-proximo border-primary background-primary">
	    <a href="<?php echo base_url("$current_uri/comprar")?>" title="Comprar">
	        Comprar
	    </a>
	</div>
	<div class="btn-myaccount btn btn-app btn-primary btn-proximo border-primary background-primary">
	    <a href="<?php echo base_url("$current_uri/login")?>" title="Minha Conta">
	        Minha Conta
	    </a>
	</div>
</div>