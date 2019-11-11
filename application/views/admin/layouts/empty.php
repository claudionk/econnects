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

    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/fonts/fontsFamily.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/bootstrap.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/materialadmin.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/font-awesome.min.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/material-design-iconic-font.min.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/libs/bootstrap-datepicker/datepicker3.css", 'admin');?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/libs/toastr/toastr.css", 'admin');?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/libs/select2/select2.min.css", 'admin');?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("core/css/base.css", 'admin');?>" />

    <?php echo $css_for_layout;?>

    <!-- END STYLESHEETS -->

  </head>
  <body class="menubar-hoverable header-fixed menubar-pin ">

    <?php $this->load->view('admin/partials/header');?>

    <!-- BEGIN BASE-->
    <div id="base">

      <!-- BEGIN OFFCANVAS LEFT -->
      <div class="offcanvas">
      </div><!--end .offcanvas-->
      <!-- END OFFCANVAS LEFT -->

      <!-- BEGIN CONTENT-->
      <div id="content">
        <section>
          <?php echo $contents;?>
        </section>
      </div><!--end #content-->
      <!-- END CONTENT -->

    </div><!--end #base-->
    <!-- END BASE -->

  </body>
</html>


