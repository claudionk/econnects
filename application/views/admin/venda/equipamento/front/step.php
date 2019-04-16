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
<ul class="list-inline" style="margin-top: 70px;">
    <li><img src="<?php $img = ($step >= 1) ? '2' : '1'; echo app_assets_url("core/images/icones/dados{$img}.png", 'admin');?>"></li>
    <li><img src="<?php $img = ($step >= 2) ? '2' : '1'; echo app_assets_url("core/images/icones/service{$img}.png", 'admin');?>"></li>
    <li><img src="<?php $img = ($step >= 3) ? '2' : '1'; echo app_assets_url("core/images/icones/money{$img}.png", 'admin');?>"></li>
    <li><img src="<?php $img = ($step >= 4) ? '2' : '1'; echo app_assets_url("core/images/icones/doc{$img}.png", 'admin');?>"></li>
    <li><img src="<?php $img = ($step >= 5) ? '2' : '1'; echo app_assets_url("core/images/icones/done{$img}.png", 'admin');?>"></li>
</ul>


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


