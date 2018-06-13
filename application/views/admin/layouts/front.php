<!DOCTYPE html>
<html lang="en" ng-app="App">
<head>
    <title><?php echo "{$title}"; ?></title>

    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <meta name="description" content="<?php echo $meta_description; ?>">
    <!-- END META -->

    <!-- BEGIN STYLESHEETS -->
        <script src="<?php echo app_assets_url('template/js/libs/jquery/jquery-1.12.4.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/jquery/jquery-migrate-1.2.1.min.js', 'admin');?>"></script>
    <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/bootstrap.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/materialadmin.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/font-awesome.min.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/material-design-iconic-font.min.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/libs/bootstrap-datepicker/datepicker3.css", 'admin');?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/libs/toastr/toastr.css", 'admin');?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/libs/select2/select2.min.css", 'admin');?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("core/css/front.css", 'admin');?>" />

    <?php echo $css_for_layout;?>

    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/html5shiv.js', 'admin');?>"></script>
    <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/respond.min.js', 'admin');?>"></script>
    <![endif]-->
    <!--    <script src="<?php echo app_assets_url('template/js/core/source/angular.min.js', 'admin');?>"></script> -->

    <?php if(isset($enable_ckeditor)) :?>
        <script src="<?php echo app_assets_url("ckeditor/ckeditor.js", 'common');?>"></script>
        <script src="<?php echo app_assets_url("ckfinder/ckfinder.js", 'common');?>"></script>
    <?php endif?>

    <script type="text/javascript">
        var ADMIN_URL = '<?php echo base_url('admin'); ?>';
        var base_url = '<?php echo base_url() ?>';

        // Seta APP para Angular JS
        //var app = angular.module("App", []);
    </script>
    <script src="<?php echo app_assets_url('core/js/anchor.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('core/js/util.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('core/js/prettify.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/select2/select2.full.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/select2/i18n/pt-BR.js', 'admin');?>"></script>
</head>
<body class="menubar-hoverable header-fixed menubar-pin ">

    <section>
        <?php echo $contents;?>
    </section>

    <!-- Modal -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
            </div>
        </div>
    </div>
    <!-- Fim Modal-->

    <!-- BEGIN JAVASCRIPT -->
    <script src="<?php echo app_assets_url('template/js/libs/jquery-validation/dist/jquery.validate.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/bootstrap/bootstrap.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/spin.js/spin.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/autosize/jquery.autosize.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/moment/moment.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/bootstrap-datepicker/locales/bootstrap-datepicker.pt-BR.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/nanoscroller/jquery.nanoscroller.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/inputmask/jquery.inputmask.bundle.min.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/core/source/App.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/core/source/AppNavigation.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/core/source/AppOffcanvas.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/core/source/AppCard.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/core/source/AppForm.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/core/source/AppNavSearch.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/core/source/AppVendor.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('template/js/core/demo/Demo.js', 'admin');?>"></script>
    <script src="<?php echo app_assets_url('core/js/base.js', 'admin');?>"></script>
    <?php echo $js_for_layout;?>
    <!-- END JAVASCRIPT -->
</body>
</html>