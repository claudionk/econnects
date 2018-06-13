<!-- BEGIN HEADER-->
<header id="header" >
    <div class="headerbar">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="headerbar-left">
            <ul class="header-nav header-nav-options">
                <li class="header-nav-brand" >
                    <div class="brand-holder">
                        <a href="<?php echo base_url('admin/home/')?>">
                            <span class="text-lg text-bold text-primary"><?php echo $title; ?></span>
                        </a>
                    </div>
                </li>
                <li>
                    <a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                        <i class="fa fa-bars"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="headerbar-right">
            <ul class="header-nav header-nav-options">
                <li>
                    <!-- Search form -->
                    <form class="navbar-search" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control" name="headerSearch" placeholder="Entre com a busca">
                        </div>
                        <button type="submit" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></button>
                    </form>
                </li>
                <?php echo $this->session->userdata("is_colaborador"); ?>

                <?php if($this->session->userdata("is_colaborador")) { ?>
                                                    <!--end .dropdown -->

                    <li class="dropdown hidden-xs">
                        <a href="javascript:void(0);" class="btn btn-icon-toggle btn-default" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-cubes"></i>
                        </a>
                        <ul class="dropdown-menu animation-expand">
                            <li class="dropdown-header"><strong>Selecione o parceiro</strong></li>

                            <?php if($parceiros = $this->session->userdata("parceiros")) foreach($parceiros as $parceiro) { ?>

                                <li class="trocarParceiro" id="<?php echo $parceiro['parceiro_id'] ?>">
                                    <a href="#" class="">
                                        <span class="text-light <?php if($this->session->userdata("parceiro_id") == $parceiro['parceiro_id']) {echo "text-danger";} ?>"><strong><?php echo $parceiro['nome'] ?></strong></span>
                                    </a>
                                </li>

                            <?php } ?>

                        </ul><!--end .dropdown-menu -->
                    </li>

                <?php }?>

            </ul><!--end .header-nav-options -->

            <ul class="header-nav header-nav-profile">
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">
                        <img src="<?php echo app_assets_url('template/img/avatar1.jpg', 'admin'); ?>" alt="" />
								<span class="profile-info">
									<?php echo $userdata['nome'];?>
									<small><?php echo $userdata['email']; ?></small>
								</span>
                    </a>
                    <ul class="dropdown-menu animation-dock">

                        <li class="dropdown-header">Config</li>
                        <li><a href="<?php echo base_url('admin/usuarios_configuracoes')?>">Trocar Senha</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo base_url("admin/login/logout/{$userdata['parceiro_id']}")?>"><i class="fa fa-fw fa-power-off text-danger"></i>Sair</a></li>
                    </ul><!--end .dropdown-menu -->
                </li><!--end .dropdown -->
            </ul><!--end .header-nav-profile -->
            <!--
            <ul class="header-nav header-nav-toggle">
                <li>
                    <a class="btn btn-icon-toggle btn-default" href="#offcanvas-search" data-toggle="offcanvas" data-backdrop="false">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>
                </li>
            </ul>--><!--end .header-nav-toggle -->
        </div><!--end #header-navbar-collapse -->
    </div>
</header>
<!-- END HEADER-->
