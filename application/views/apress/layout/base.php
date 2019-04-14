<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
  <title>INDEX</title>
  <!-- CSS  -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/css/materialize.min.css">
  <link rel="stylesheet" href="<?php echo app_assets_url('css/main.css', 'apress');?>">
  <style>
  	@font-face {
    font-family: 'Gotham Book';
    src: url("<?php echo app_assets_url('fonts/Gotham-Book.eot', 'apress');?>") format("embedded-opentype")
    , url("<?php echo app_assets_url('fonts/Gotham-Book.eot?#iefix', 'apress');?>") format("embedded-opentype")
    , url("<?php echo app_assets_url('fonts/Gotham-Book.woff', 'apress');?>") format("woff")
    , url("<?php echo app_assets_url('fonts/Gotham-Book.ttf', 'apress');?>") format("truetype")
    , url("<?php echo app_assets_url('fonts/Gotham-Book.svg#Gotham-Book', 'apress');?>") format("svg");
	}
	@font-face {
	    font-family: 'Gotham Bold';
	    src: url("<?php echo app_assets_url('fonts/Gotham-Bold.eot', 'apress');?>") format("embedded-opentype")
	    , url("<?php echo app_assets_url('fonts/Gotham-Bold.eot?#iefix', 'apress');?>") format("embedded-opentype")
	    , url("<?php echo app_assets_url('fonts/Gotham-Bold.woff', 'apress');?>") format("woff")
	    , url("<?php echo app_assets_url('fonts/Gotham-Bold.ttf', 'apress');?>") format("truetype")
	    , url("<?php echo app_assets_url('fonts/Gotham-Bold.svg#Gotham-Bold', 'apress');?>") format("svg");
	}
	@font-face {
	    font-family: 'Gotham Medium';
	    src: url("<?php echo app_assets_url('fonts/Gotham-Medium.otf', 'apress');?>") format("opentype");
	}
	@font-face {
	    font-family: 'Gotham Light';
	    src: url("<?php echo app_assets_url('fonts/Gotham-Light.otf', 'apress');?>") format("opentype");
	}   
  </style>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>           
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/js/materialize.min.js"></script>
</head>
<body>
<ul id="slide-out" class="side-nav fixed">
	<li>
		<a href="#!"><img src="<?php echo app_assets_url('img/icones/x.png','apress');?>" style="float:right"></a>
	</li>
	<li>
		<ul class="collapsible">
			<li class="collection-item">
				<a class="collapsible-header"><img src="<?php echo app_assets_url('img/icones/contrate.png','apress');?>">Contrate</a>
				<div class="collapsible-body collection">
					<a class="collection-item" href="#!">SEGUROS</a>
					<a class="collection-item" href="#!"><i class="material-icons left">chevron_right</i> COMPRAR</a>
					<a class="collection-item" href="#!">SERVIÇOS</a>
					<a class="collection-item" href="#!"><i class="material-icons left">chevron_right</i> COMPRAR</a>
				</div>
			</li>
		</ul>
	</li>
	<li><a href="#!" class=""><img src="<?php echo app_assets_url('img/icones/protegido.png','apress');?>">Você Protegido</a></li>
	<li><a href="#!" class=""><img src="<?php echo app_assets_url('img/icones/apolice.png','apress');?>">Apólices</a></li>
	<li><a href="#!" class=""><img src="<?php echo app_assets_url('img/icones/atendimento.png','apress');?>">Atendimento</a></li>
</ul>
<div class="header">
	<div class="contaier">
		<a href="#" class="brand-logo left"><img src="http://sisconnects.com.br/assets/admin/upload/parceiros/494efe1480bdf61ba9015c2f8e0af7b5.png?version=1.0.0.4" style="margin:12px;"></a>
	  	<a href="#" data-activates="slide-out" class="button-collapse right"><i class="mdi-navigation-menu" style="margin:12px;"></i></a>
	</div>
</div>
<div class="container main valign-wrapper">
  <?php echo $contents;?>
 </div>
<script>
	$('.button-collapse').sideNav({
		menuWidth: 300,
		closeOnClick: true,
		edge: 'right',
	});
    $(document).ready(function() {
        $('.carousel.carousel-slider').carousel({ fullWidth: true });
    	$('.modal').modal();
    });
</script>
  </body>
</html>