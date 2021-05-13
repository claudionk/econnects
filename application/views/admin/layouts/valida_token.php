<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo "{$title}"; ?></title>

        <!-- BEGIN META -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $meta_keywords; ?>">
        <meta name="description" content="<?php echo $meta_description; ?>">
        <meta name="google-site-verification" content="E9ztWkkDYh0MwD_lrG4Bxj29PaqcXhuBO-B4BHU7sBI" />
        <!-- END META -->

        <!-- BEGIN STYLESHEETS -->
        <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/fonts/fontsFamily.css", 'admin');?>" />
        <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/bootstrap.css", 'admin');?>" />
        <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/materialadmin.css", 'admin');?>" />
        <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/font-awesome.min.css", 'admin');?>" />
        <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/material-design-iconic-font.min.css", 'admin');?>" />
        <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("core/css/base.css", 'admin');?>" />
        <style>
            .load-gif { width: 60px; height: 60px; margin: 0 auto; border: 5px solid #1FEC7A; border-top: 5px solid transparent; border-radius: 50%; -webkit-animation: spin 1s linear infinite; animation: spin 1s linear infinite; }
            .load { top: 0; left: 0; width: 100vw;  height: 100vh;  display: none; position: fixed;  overflow: auto;  z-index: 9999;  text-align: center;  justify-content: space-between; background-color: rgb(0,0,0);  background-color: rgba(0,0,0,0.7);  }
            .load-child { margin: auto; }
            @-webkit-keyframes spin { 0% { -webkit-transform: rotate(0deg); } 100% { -webkit-transform: rotate(360deg); } }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        </style>
        <!-- END STYLESHEETS -->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/html5shiv.js', 'admin');?>"></script>
        <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/respond.min.js', 'admin');?>"></script>
        <![endif]-->
        <!-- <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script> -->
        <script src="https://www.google.com/recaptcha/api.js?hl=pt-BR"></script>
    </head>
    <body id="body-login">

        <!-- BEGIN LOGIN SECTION -->
        <div id="image-login-container">
            <img id="image-login" src="<?php echo app_assets_url('template/img/image-login.png', 'admin'); ?>">
        </div>
        <div id="form-login-container">
            <form id="form-login" class="form" action="<?php echo $valida_token_url;?>" accept-charset="utf-8" method="post">
                <h2 id="form-login-title"><b>ENTRE COM SEU CODIGO</b></h2>
                <p style="margin-bottom: 0px; color: #FFF;">O codigo foi enviado para o seu e-mail</p>
                <div class="form-login-group">
                    <input type="text" class="form-control form-login-input" id="code" name="code" />
                    <label class="form-login-label">DIGITE SEU CODIGO</label>
                </div>
                </br>
                <div class="row">
                    <div class="col-xs-12 text-left">
                        <p id="form-login-mensagem"><?php echo $this->session->flashdata('token_erro');?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 text-right">
                        <button id="form-login-submit" type="submit" >VALIDAR</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- END LOGIN SECTION -->

        <div id="load" class="load">
            <div class="load-child">
                <div class="load-gif"></div>
            </div>
        </div>

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
        <script>
            window.onbeforeunload = function( event ) { 
                var modal = document.getElementById("load");
                modal.style.display = "flex";
            }
        </script>
    </body>
</html>