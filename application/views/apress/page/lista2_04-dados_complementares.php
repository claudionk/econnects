<style>
	.main{ justify-content: flex-start; }
  	.main .col *{ color: #5a5a5a; }
  	.main .col p{ margin:0px ; }
  	.main .col h5{ margin:0px ; text-wrap: nowrap; font-family:'Gotham Book'; font-size: 1rem !important; }
  	body{ background-color: #eeeeee; text-align: center }
  	.list-inline { padding-left: 0; margin-left: -5px; list-style: none; }
	.list-inline > li { display: inline-block;  }
	.list-inline > li img{ width: 60px; }
	input{ background-color: #ffffff !important; }
</style>
<div class="col">
	<ul class="list-inline" style="margin-top: 70px;">
		<li class="active"><img src="<?php echo app_assets_url('img/icones/done2.png','apress');?>"></li>
		<li><img src="<?php echo app_assets_url('img/icones/doc2.png','apress');?>"></li>
		<li><img src="<?php echo app_assets_url('img/icones/lock1.png','apress');?>"></li>
	</ul>
	<h5>DADOS COMPLEMENTARES</h5>
	<br>
	<p>Por gentileza nos envie uma cópia digital da Nota Fiscal do Produto</p>

	<a class="btn active" style="align-self: center;">PRÓXIMO <i class="material-icons right">chevron_right</i></a>
</div>