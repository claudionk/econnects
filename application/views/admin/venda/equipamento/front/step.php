<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <a id="menu-close" href="#" class="btn btn-default btn-lg pull-right toggle">
            <i class="glyphicon glyphicon-remove"></i>
        </a>
        <li class="sidebar-brand"></li>
        <li>
            <a href="#">CONTRATE</a>
        </li>
        <li>
            <a href="#about">SEGURO</a>
        </li>
        <li>
            <a href="#contact">SERVIÇOS</a>
        </li>
    </ul>
</div>

<div class="">
    <div class="header-logo-menu">
        <img src="<?php echo app_assets_url("upload/parceiros/494efe1480bdf61ba9015c2f8e0af7b5.png", 'admin'); ?>" alt="" title="" />

        <button aria-controls="bs-navbar" aria-expanded="false" class="navbar-toggle collapsed" data-target="#bs-navbar" data-toggle="collapse" type="button" id="menu-toggle">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>

    <ul class="list-inline">
        <li><img src="<?php $img = ($step >= 1) ? '2' : '1'; echo app_assets_url("core/images/icones/dados{$img}.png", 'admin');?>"></li>
        <li><img src="<?php $img = ($step >= 2) ? '2' : '1'; echo app_assets_url("core/images/icones/service{$img}.png", 'admin');?>"></li>
        <li><img src="<?php $img = ($step >= 3) ? '2' : '1'; echo app_assets_url("core/images/icones/doc{$img}.png", 'admin');?>"></li>
        <li><img src="<?php $img = ($step >= 4) ? '2' : '1'; echo app_assets_url("core/images/icones/money{$img}.png", 'admin');?>"></li>
        <li><img src="<?php $img = ($step >= 5) ? '2' : '1'; echo app_assets_url("core/images/icones/done{$img}.png", 'admin');?>"></li>
    </ul>

    <h2 class="text-light text-center title-h2"><?php echo $title; ?></h2>
</div>
<script>
    $("#menu-close").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
</script>
<!--

<div id="wizard" class="form-wizard form-wizard-horizontal">
    <div class="form-wizard-nav">
        <div class="progress">
            <div class="progress-bar progress-bar-primary" style="width: <?php echo ($step-1)*20 + 10 ?>%;"></div>
        </div>
        <ul class="nav nav-justified">

            <?php $max = 5; ?>

            <li class="<?php echo ($step == 1) ? 'active' : ''; if($step > 1) echo " done"; ?>">
                <a href="#"><span class="step">1<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Dados iniciais</span></a>
            </li>

            <li class="<?php echo ($step == 2) ? 'active' : ''; if($step > 2) echo " done"; ?>">
                <a href="#"><span class="step">2<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Cotação</span></a>
            </li>

            <li class="<?php echo ($step == 3) ? 'active' : ''; if($step > 3) echo " done"; ?>">
                <a href="#"><span class="step">3<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Contratação</span></a>
            </li>
            <li class="<?php echo ($step == 4) ? 'active' : ''; if($step > 4) echo " done"; ?>">
                <a href="#"><span class="step">4<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Pagamento</span></a>
            </li>
            <li class="<?php echo ($step == 5) ? 'active' : ''; if($step > 5) echo " done"; ?>">
                <a href="#"><span class="step">5<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Certificado / Bilhete</span></a>
            </li>

        </ul>
    </div>
    <br>
</div>
-->


