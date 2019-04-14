
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
 <style>
	.main{ justify-content: flex-start; }
  	.main .col *{ color: #5a5a5a; }
  	.main .col p{ margin:0px ; }
  	.main .col h5{ margin:0px ; text-wrap: nowrap; font-family:'Gotham Book'; font-size: 1rem !important; }
  	body{ background-color: #eeeeee; text-align: center }
  	.list-inline { padding-left: 0; margin-left: -5px; list-style: none; }
	.list-inline > li { display: inline-block;  }
	.list-inline > li img{ width: 40px; }
	input{ background-color: #ffffff !important; }
  	.collection { border: none  }
  	.collection .collection-item{ background-color: transparent; border: none; text-align: left }
</style>
<div class="container">
	<ul class="list-inline" style="margin-top: 70px;">
		<li><img src="img/icones/dados2.png"></li>
		<li><img src="img/icones/service2.png"></li>
		<li><img src="img/icones/money1.png"></li>
		<li><img src="img/icones/doc1.png"></li>
		<li><img src="img/icones/done1.png"></li>
	</ul>
	<h5>COTAÇÕES</h5>
	<br>
	 <div class="carousel carousel-slider center" data-indicators="true">
        <div class="carousel-item white" style="color:#444444" href="#one!">
            <h2>COMPLETO</h2>
            <h3><sup>R$</sup> 650<sup>,00</sup></h3>
            <ul class="collection">
            	<li class="collection-item">Roubo</li>
            	<li class="collection-item">Furto</li>
            	<li class="collection-item">Conserto em caso de quebra</li>
            </ul>
            <p><a class="modal-trigger" href="#modal1">Saiba mais</a></p>
			<a class="btn active" style="align-self: center;">QUERO ESSE</a>
        </div>
        <div class="carousel-item white" style="color:#444444" href="#two!">
            <h2>COMPLETO</h2>
            <h3><sup>R$</sup> 650<sup>,00</sup></h3>
            <ul class="collection">
            	<li class="collection-item">Roubo</li>
            	<li class="collection-item">Furto</li>
            	<li class="collection-item">Conserto em caso de quebra</li>
            </ul>
            <p><a href="#">Saiba mais</a></p>
			<a class="btn active" style="align-self: center;">QUERO ESSE</a>
        </div>
        <div class="carousel-item white" style="color:#444444" href="#three!">
            <h2>COMPLETO</h2>
            <h3><sup>R$</sup> 650<sup>,00</sup></h3>
            <ul class="collection">
            	<li class="collection-item">Roubo</li>
            	<li class="collection-item">Furto</li>
            	<li class="collection-item">Conserto em caso de quebra</li>
            </ul>
            <p><a href="#">Saiba mais</a></p>
			<a class="btn active" style="align-self: center;">QUERO ESSE</a>
        </div>
        <div class="carousel-item white" style="color:#444444" href="#four!">
            <h2>COMPLETO</h2>
            <h3><sup>R$</sup> 650<sup>,00</sup></h3>
            <ul class="collection">
            	<li class="collection-item">Roubo</li>
            	<li class="collection-item">Furto</li>
            	<li class="collection-item">Conserto em caso de quebra</li>
            </ul>
            <p><a href="#">Saiba mais</a></p>
			<a class="btn active" style="align-self: center;">QUERO ESSE</a>
        </div>
    </div>

  <div id="modal1" class="modal">
    <div class="modal-content">
      <h4>Modal Header</h4>
      <p>A bunch of text</p>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
    </div>
  </div>
</div>