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
    <?php
        $captchaSiteKey   = "6Lc2wW0aAAAAAFoYUU0aVcNNqBCgtqYHu-BaxI9p"; // meu
        $captchaSecretKey = "6Lc2wW0aAAAAAIBo0Kp38p7vnHdvSpaNfdAw7eI_"; // meu
        // $captchaSiteKey   = "6Lc7iVwaAAAAAE053b5-i1SwgOBomSwPVwhAH9de"; //sis
        // $captchaSecretKey = '6Lc7iVwaAAAAALYXf6wLEOnXU2w7j5j_vm5LHOmA'; //sis
    ?> 

    <?php
        /**
         * This is a PHP library that handles calling reCAPTCHA.
         *    - Documentation and latest version
         *          https://developers.google.com/recaptcha/docs/php
         *    - Get a reCAPTCHA API Key
         *          https://www.google.com/recaptcha/admin/create
         *    - Discussion group
         *          http://groups.google.com/group/recaptcha
         *
         * @copyright Copyright (c) 2014, Google Inc.
         * @link      http://www.google.com/recaptcha
         *
         * Permission is hereby granted, free of charge, to any person obtaining a copy
         * of this software and associated documentation files (the "Software"), to deal
         * in the Software without restriction, including without limitation the rights
         * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
         * copies of the Software, and to permit persons to whom the Software is
         * furnished to do so, subject to the following conditions:
         *
         * The above copyright notice and this permission notice shall be included in
         * all copies or substantial portions of the Software.
         *
         * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
         * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
         * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
         * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
         * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
         * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
         * THE SOFTWARE.
         */

        /**
         * A ReCaptchaResponse is returned from checkAnswer().
         */
        class ReCaptchaResponse
        {
            public $success;
            public $errorCodes;
        }

        class ReCaptcha
        {
            private static $_signupUrl = "https://www.google.com/recaptcha/admin";
            private static $_siteVerifyUrl =
                "https://www.google.com/recaptcha/api/siteverify?";
            private $_secret;
            private static $_version = "php_1.0";

            /**
             * Constructor.
             *
             * @param string $captchaSecretKey shared secret between site and ReCAPTCHA server.
             */
            function ReCaptcha($captchaSecretKey)
            {
                if ($captchaSecretKey == null || $captchaSecretKey == "") {
                    die("To use reCAPTCHA you must get an API key from <a href='"
                        . self::$_signupUrl . "'>" . self::$_signupUrl . "</a>");
                }
                $this->_secret=$captchaSecretKey;
            }

            /**
             * Encodes the given data into a query string format.
             *
             * @param array $data array of string elements to be encoded.
             *
             * @return string - encoded request.
             */
            private function _encodeQS($data)
            {
                $req = "";
                foreach ($data as $key => $value) {
                    $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
                }

                // Cut the last '&'
                $req=substr($req, 0, strlen($req)-1);
                return $req;
            }

            /**
             * Submits an HTTP GET to a reCAPTCHA server.
             *
             * @param string $path url path to recaptcha server.
             * @param array  $data array of parameters to be sent.
             *
             * @return array response
             */
            private function _submitHTTPGet($path, $data)
            {
                $req = $this->_encodeQS($data);
                $response = file_get_contents($path . $req);
                return $response;
            }

            /**
             * Calls the reCAPTCHA siteverify API to verify whether the user passes
             * CAPTCHA test.
             *
             * @param string $remoteIp   IP address of end user.
             * @param string $response   response string from recaptcha verification.
             *
             * @return ReCaptchaResponse
             */
            public function verifyResponse($remoteIp, $response)
            {
                // Discard empty solution submissions
                if ($response == null || strlen($response) == 0) {
                    $recaptchaResponse = new ReCaptchaResponse();
                    $recaptchaResponse->success = false;
                    $recaptchaResponse->errorCodes = 'missing-input';
                    return $recaptchaResponse;
                }

                $getResponse = $this->_submitHttpGet(
                    self::$_siteVerifyUrl,
                    array (
                        'secret' => $this->_secret,
                        'remoteip' => $remoteIp,
                        'v' => self::$_version,
                        'response' => $response
                    )
                );
                $answers = json_decode($getResponse, true);
                $recaptchaResponse = new ReCaptchaResponse();

                if (trim($answers ['success']) == true) {
                    $recaptchaResponse->success = true;
                } else {
                    $recaptchaResponse->success = false;
                    $recaptchaResponse->errorCodes = $answers [error-codes];
                }

                return $recaptchaResponse;
            }
        }
    ?>

    <?php
        $response = null;
        $reCaptcha = new reCaptcha($captchaSecretKey);

        if(isset($_POST['g-recaptcha-response'])){
            $response = $reCaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], $_POST['g-recaptcha-response']);
        }

        if($response != null && $response->success){
            echo "Captcha Validado";
        }
    ?>    

        <!-- BEGIN LOGIN SECTION -->
        <div id="image-login-container">
            <img id="image-login" src="<?php echo app_assets_url('template/img/image-login.png', 'admin'); ?>">
        </div>
        <div id="form-login-container">
            <form id="form-login" class="form" action="<?php echo $login_form_url;?>" accept-charset="utf-8" method="post">
                <h2 id="form-login-title"><b>ENTRE NA SUA CONTA</b></h2>
                <div class="form-login-group">
                    <input type="text" class="form-control form-login-input" id="username" name="login" placeholder=" "/>
                    <label class="form-login-label">DIGITE SEU E-MAIL</label>
                </div>
                <div class="form-login-group">
                    <input type="password" class="form-control form-login-input" id="password" name="password" placeholder=" " />
                    <label class="form-login-label">DIGITE SUA SENHA</label>
                </div>

                <div class="row">
                    <div class="col-xs-12 text-left">
                        <p id="form-login-esqueceu_a_senha"><a href="<?php echo $esqueceu_form_url;?>">Esqueceu a senha?</a></p>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-xs-12 text-left">
                        <p id="form-login-mensagem"><?php echo $this->session->flashdata('loginerro');?></p>
                    </div>                    
                </div>

                <div class="g-recaptcha" data-sitekey="<?php echo $captchaSiteKey;?>" data-callback="enableBtn"></div>
                </br>

                <div class="row">
                    <div class="col-xs-4 text-right">
                        <button id="form-login-submit" type="submit" disabled="disabled">LOGIN</button>
                    </div>
                    <div class="col-xs-8 text-left">
                    </div>
                </div>
                </br>
                </br>
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
        <script>
            function enableBtn(){
                document.getElementById("form-login-submit").disabled = false;
            }

            window.onbeforeunload = function( event ) { 
                var modal = document.getElementById("load");
                modal.style.display = "flex";
            }
        </script>
        <!-- END JAVASCRIPT -->
    </body>
</html>