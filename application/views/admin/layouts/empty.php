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

    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/bootstrap.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/materialadmin.css", 'admin');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/font-awesome.min.css", 'admin');?>" />

  </head>
  <body class="menubar-hoverable header-fixed menubar-pin ">

    <!-- BEGIN BASE-->
    <div>

      <!-- BEGIN OFFCANVAS LEFT -->
      <div class="offcanvas">
      </div><!--end .offcanvas-->
      <!-- END OFFCANVAS LEFT -->

      <!-- BEGIN CONTENT-->
      <div class="content">
        <section>
          <?php echo $contents;?>
        </section>
      </div><!--end #content-->
      <!-- END CONTENT -->

    </div><!--end #base-->
    <!-- END BASE -->

  </body>
</html>


