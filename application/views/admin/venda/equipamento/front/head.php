<div id="sidebar-wrapper">
    <a id="menu-close" href="#" class="btn btn-default btn-lg pull-right toggle">&times;</a>
    <ul class="sidebar-nav">
        <li>
            <a href="<?php echo base_url("$current_uri/comprar")?>" class="link"> COMPRAR </a>
        </li>
        <li>
            <a href=".nav-apolices" class="link" data-toggle="collapse" aria-expanded="true" > APOLICES <i class="fa fa-angle-up pull-right" aria-hidden="true"></i> </a>
        </li>
        <li class="nav-apolices collapse in" aria-expanded="true">
            <a href="#" class="link"> <i class="fa fa-angle-right" aria-hidden="true"></i> LISTAR </a>
        </li>
        <li class="nav-apolices collapse in" aria-expanded="true">
            <a href="#" class="link"> <i class="fa fa-angle-right" aria-hidden="true"></i> CADASTRAR </a>
        </li>
        <li>
            <a href=".nav-wallet" class="link" data-toggle="collapse" aria-expanded="false" > WALLET <i class="fa fa-angle-up pull-right" aria-hidden="true"></i> </a>
        </li>
        <li class="nav-wallet collapse">
            <a href="#" class="link"> <i class="fa fa-angle-right" aria-hidden="true"></i> FORMA DE PAGAMENTO </a>
        </li>
        <li class="nav-wallet collapse">
            <a href="#" class="link"><i class="fa fa-angle-right" aria-hidden="true"></i> PROMOÇÃO </a>
        </li>
        <li>
            <a href="#" class="link"> MINHA CONTA </a>
        </li>
    </ul>
</div>


<div class="">
    <?php if ($logado){ ?>

    <div class="header-logo-menu">
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-3">
                <img src="<?php echo $this->template->get('theme_logo'); ?>" alt="" title="" style="width: 90px;" />
            </div>

            <div class="col-md-9 col-sm-9 col-xs-9">

                <ul class="nav nav-pills pull-right">
                    <?php //if( !empty($this->session->userdata('logado'))){ ?>
                    <li role="presentation">
                        <a href="javascript:void(0)" title="<?php echo $this->name; ?>" class="username">
                            Meu Nome
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="" title="">
                            <i class="fa fa-bell" aria-hidden="true"></i>
                        </a>
                    </li>
                    <?php //} ?>
                    <li>
                        <button aria-controls="bs-navbar" aria-expanded="false" class="navbar-toggle collapsed" data-target="#bs-navbar" data-toggle="collapse" type="button" id="menu-toggle">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </li>
                </ul>

            </div>
        </div>
    </div>

        <?php if ( !isset($viewTitle) || !empty($viewTitle) ) { ?>
            <h2 class="text-light text-center title-h2">xxx<?php echo $title; ?></h2>
        <?php }
    } else { ?>

        <div class="header-logo-menu">
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-3">
                    <img src="<?php echo $this->template->get('theme_logo'); ?>" alt="" title="" style="width: 90px;" />
                </div>

                <div class="col-md-9 col-sm-9 col-xs-9">

                    <ul class="nav nav-pills text-center">

                        <li role="presentation">
                            <a href="javascript:void(0)" title="<?php echo $this->name; ?>" class="username">
                                <?php echo $title; ?>
                            </a>
                        </li>

                    </ul>

                </div>
            </div>
        </div>
    <?php } ?>

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