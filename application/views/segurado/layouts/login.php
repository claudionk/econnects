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
    <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/bootstrap.css", 'segurado');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/materialadmin.css", 'segurado');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/font-awesome.min.css", 'segurado');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/material-design-iconic-font.min.css", 'segurado');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("core/css/base.css", 'segurado');?>" />
    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/html5shiv.js', 'segurado');?>"></script>
    <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/respond.min.js', 'segurado');?>"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">

<!-- BEGIN LOGIN SECTION -->
<section class="section-account">
    <div class="img-backdrop" style="background-image: url('<?php echo app_assets_url('template/img/img16.jpg', 'segurado'); ?>')">
        
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
<script src="<?php echo app_assets_url('template/js/libs/jquery/jquery-1.11.2.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/jquery/jquery-migrate-1.2.1.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/bootstrap/bootstrap.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/spin.js/spin.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/autosize/jquery.autosize.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/nanoscroller/jquery.nanoscroller.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/App.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppNavigation.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppOffcanvas.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppCard.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppForm.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppNavSearch.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppVendor.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/demo/Demo.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('core/js/login.js', 'segurado');?>"></script>
<!-- END JAVASCRIPT -->

</body>
</html>