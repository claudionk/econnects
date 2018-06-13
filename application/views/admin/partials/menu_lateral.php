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



            <!-- painel -->
            <?php
                $arrMenu = array();
                app_montar_menu($userdata['recursos'],  $arrMenu);
            
   
            
                print(implode("\n", $arrMenu));
            ?>

           <!-- END LEVELS -->

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

