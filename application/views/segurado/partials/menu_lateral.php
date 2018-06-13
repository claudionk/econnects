<!-- BEGIN MENUBAR-->
<div id="menubar" class="menubar-inverse ">
    <div class="menubar-fixed-panel">
        <div>
            <a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                <i class="fa fa-bars"></i>
            </a>
        </div>
        <div class="expanded">
            <a href="<?php echo base_url('admin/home/')?>">
                <span class="text-lg text-bold text-primary ">Página Principal</span>
            </a>
        </div>
    </div>
    <div class="menubar-scroll-panel">

        <!-- BEGIN MAIN MENU -->
        <ul id="main-menu" class="gui-controls">

            <li class="<?php if (is_current_controller('home')) echo 'active expanded';?>">
                <a href="<?php echo base_url('segurado/home');?>">
                    <div class="gui-icon"><i class="md md-receipt"></i></div>
                    <span class="title">Apólices</span>
                </a>
            </li>

            <li class="<?php if (is_current_controller('dados')) echo 'active expanded';?>">
                <a href="<?php echo base_url('segurado/dados');?>">
                    <div class="gui-icon"><i class="md md-account-box"></i></div>
                    <span class="title">Meus Dados</span>
                </a>
            </li>
            <li class="">
                <a href="<?php echo base_url("segurado/login/logout/{$userdata['parceiro_id']}")?>">
                    <div class="gui-icon"><i class="fa fa-fw fa-power-off text-danger"></i></div>
                    <span class="title">Sair</span>
                </a>
            </li>


        </ul><!--end .main-menu -->
        <!-- END MAIN MENU -->

        <div class="menubar-foot-panel">
            <small class="no-linebreak hidden-folded">
                <span class="opacity-75">Copyright &copy; <?php echo date('Y');?></span> <strong>Zazz Tecnologia</strong>
            </small>
        </div>
    </div><!--end .menubar-scroll-panel-->
</div><!--end #menubar-->
<!-- END MENUBAR -->

