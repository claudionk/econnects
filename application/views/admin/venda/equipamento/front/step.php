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
    <?php
    //var_dump($this->session->userdata('logado'));
    ?>

    <div class="header-logo-menu">
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-3">
                <img src="<?php echo app_assets_url("upload/parceiros/494efe1480bdf61ba9015c2f8e0af7b5.png", 'admin'); ?>" alt="" title="" />
            </div>

            <div class="col-md-9 col-sm-9 col-xs-9">

                <ul class="nav nav-pills pull-right">
                    <?php if( !empty($this->session->userdata('logado'))){ ?>
                    <li role="presentation">
                        <a href="javascript:void(0)" title="<?php echo $this->name; ?>" class="username">
                            <?php echo $this->name; ?>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="" title="">
                            <i class="fa fa-bell" aria-hidden="true"></i>
                        </a>
                    </li>
                    <?php } ?>
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

    <?php
    $step = $this->uri->segment(5);
    ?>
    <ul class="nav nav-pills nav-steps">
        <li class="item">
            <a href="" title="" class="step-radius <?php if($step >= 1){ echo 'active background-primary'; } ?>"> <i class="step-icons data <?php if($step >= 1){ echo 'active'; } ?>"></i> </a>
        </li>
        <li class="item">
            <a href="" title="" class="step-radius <?php if($step >= 2){ echo 'active background-primary'; } ?>"> <i class="step-icons plano <?php if($step >= 2){ echo 'active'; } ?>"></i> </a>
        </li>
        <li class="item">
            <a href="" title="" class="step-radius <?php if($step >= 3){ echo 'active background-primary'; } ?>"> <i class="step-icons pagamento <?php if($step >= 3){ echo 'active'; } ?>"></i> </a>
        </li>
        <li class="item">
            <a href="" title="" class="step-radius <?php if($step >= 4){ echo 'active background-primary'; } ?>"> <i class="step-icons lista <?php if($step >= 4){ echo 'active'; } ?>"></i> </a>
        </li>
        <li class="item">
            <a href="" title="" class="step-radius <?php if($step >= 5){ echo 'active background-primary'; } ?>"> <i class="step-icons feito <?php if($step >= 5){ echo 'active'; } ?>"></i> </a>
        </li>
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