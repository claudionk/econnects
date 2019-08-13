<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo "{$title}"; ?></title>

    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <meta name="description" content="<?php echo $meta_description; ?>">
    <!-- END META -->

    <!-- BEGIN STYLESHEETS -->
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/fonts/fontsFamily.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/bootstrap.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/materialadmin.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/font-awesome.min.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/material-design-iconic-font.min.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("core/css/base.css", 'admin');?>" />
    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/html5shiv.js', 'admin');?>"></script>
    <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/respond.min.js', 'admin');?>"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">

<!-- BEGIN LOGIN SECTION -->
<section class="section-account">
    <div class="img-backdrop" style="background-image: url('<?php echo app_assets_url('template/img/img16.jpg', 'admin'); ?>')">
        
    </div>
    <div class="spacer"></div>


    <div class="card contain-sm">
        <div class="card-body">
            <div class="row">
                <?php echo $contents;?>
            </div><!--end .row -->
        </div><!--end .card-body -->
    </div><!--end .card -->
</section>
<!-- END LOGIN SECTION -->

<!-- BEGIN JAVASCRIPT -->
<script src="<?php echo app_assets_url('template/js/libs/jquery/jquery-1.11.2.min.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/jquery/jquery-migrate-1.2.1.min.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/bootstrap/bootstrap.min.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/spin.js/spin.min.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/autosize/jquery.autosize.min.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/nanoscroller/jquery.nanoscroller.min.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/popper.min.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/App.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppNavigation.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppOffcanvas.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppCard.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppForm.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppNavSearch.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppVendor.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/demo/Demo.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('core/js/login.js', 'admin');?>"></script>
<script src="<?php echo app_assets_url('core/js/SenhaForte.js', 'admin');?>"></script>
<!-- END JAVASCRIPT -->

</body>
</html>