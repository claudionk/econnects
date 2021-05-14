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
            
            .fa-check { color: #1FEC7A; font-size:12px; width: 12px; height: 12px; padding-right: 5px;}
            .fa-times { color: #EB1414; font-size:12px; width: 12px; height: 12px; padding-right: 5px;}
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
            <form id="form-login" class="form" action="<?php echo $reseta_senha_url;?>" accept-charset="utf-8" method="post">
                <h2 id="form-login-title"><b>ENTRE COM SUA NOVA SENHA</b></h2>
                <div class="form-login-group">
                    <input type="password" class="form-control form-login-input" id="password" name="password" />
                    <label class="form-login-label">DIGITE SUA SENHA</label>
                </div>
                <div class="form-login-group">
                    <input type="password" class="form-control form-login-input" id="confirm_pass" name="confirm_pass" />
                    <label class="form-login-label">DIGITE A CONFIRMACAO</label>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-left">
                        <p id="form-login-mensagem" style="margin:8px 0 0 0" ><?php echo $this->session->flashdata('pass_erro');?></p>
                    </div>
                </div>
                <div class="row" style="color:#FFF;">
                    <div id="errors" class="col-xs-12 text-left" style="margin-bottom: 8px;" >
                        <p id="case1" class="titulo"><span class="fa fa-times"></span>A senha deve conter ao menos 1 letra mai&uacute;scula.</p>
                        <p id="case2" class="titulo"><span class="fa fa-times"></span>A senha deve conter ao menos 1 letra min&uacute;scula</p>
                        <p id="case3" class="titulo"><span class="fa fa-times"></span>A senha deve conter ao menos 1 caracter especial.</p>
                        <p id="case4" class="titulo"><span class="fa fa-times"></span>A senha deve conter ao menos 1 n&uacute;mero.</p>
                        <p id="case5" class="titulo"><span class="fa fa-times"></span>A senha deve conter ao menos 10 caracteres.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 text-right">
                        <button id="form-login-submit" type="submit" >SALVAR</button>
                    </div>
                </div>
                <br>
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
            document.getElementById("form-login-submit").disabled = true;

            window.onbeforeunload = function( event ) { 
                var modal = document.getElementById("load");
                modal.style.display = "flex";
            }
            
            jQuery(document).ready(function() {
                jQuery("#password").passwordValidation({
                    "confirmField": "#confirm_pass"
                }, function(element, valid, match, failedCases) {

                    const check = '<span class="fa fa-check"></span>';
                    const times = '<span class="fa fa-times"></span>';

                    if (valid)            jQuery(element).css("border-color", "green");
                    if (!valid)           jQuery(element).css("border-color", "red");
                    if (valid && match)   jQuery("#confirm_pass").css("border-color", "green");
                    if (!valid || !match) jQuery("#confirm_pass").css("border-color", "red");

                    if (failedCases.length > 0 && !valid || !match)
                    {
                        const cases = [1,2,3,4,5];

                        cases.forEach(c => {
                            const found = failedCases.find(e => e.case == c);

                            if ( !found.missing ) 
                                jQuery("#errors #case" + found.case).html(check + found.message);
                            else 
                                jQuery("#errors #case" + found.case).html(times + found.message);
                        });

                        jQuery('#errors p').removeClass('text-success');
                        jQuery('#form-login-submit').prop('disabled', 'disabled');
                    }
                    else
                    {
                        jQuery('#errors p').addClass('text-success');
                        jQuery('#form-login-submit').prop('disabled', false);
                    }
                });
            });

            (function($) {
                $.fn.extend({
                    passwordValidation: function(_options, _callback, _confirmcallback) {
                        var CHARSETS  = {
                            upperCaseSet    : "A-Z", //All UpperCase
                            lowerCaseSet    : "a-z", //All LowerCase
                            digitSet        : "0-9", //All digits
                            specialSet      : "\\x20-\\x2F\\x3A-\\x40\\x5B-\\x60\\x7B-\\x7E\\x80-\\xFF", //All Other printable Ascii
                        }

                        var _defaults = {
                            minLength       : 10,	 //Minimum Length of password
                            minUpperCase    : 1,     //Minimum number of Upper Case Letters characters in password
                            minLowerCase    : 1,     //Minimum number of Lower Case Letters characters in password
                            minDigits       : 1,	 //Minimum number of digits characters in password
                            minSpecial      : 1,	 //Minimum number of special characters in password
                            maxRepeats      : 5,	 //Maximum number of repeated alphanumeric characters in password dhgurAAAfjewd <- 3 A's
                            maxConsecutive  : 5,     //Maximum number of alphanumeric characters from one set back to back
                            noUpper         : false, //Disallow Upper Case Lettera
                            noLower         : false, //Disallow Lower Case Letters
                            noDigit         : false, //Disallow Digits
                            noSpecial       : false, //Disallow Special Characters

                            failRepeats     : true,  //Disallow user to have x number of repeated alphanumeric characters ex.. ..A..a..A.. <- fails if maxRepeats <= 3 CASE INSENSITIVE
                            failConsecutive : true,  //Disallow user to have x number of consecutive alphanumeric characters from any set ex.. abc <- fails if maxConsecutive <= 3
                            confirmField    : undefined
                        };

                        //Ensure parameters are correctly defined
                        if ($.isFunction(_options))
                        {
                            if ($.isFunction(_callback))
                            {
                                if ($.isFunction(_confirmcallback))
                                {
                                    console.log("Warning in passValidate: 3 or more callbacks were defined... First two will be used.");
                                }
                                _confirmcallback = _callback;
                            }
                            _callback = _options;
                            _options  = {};
                        }

                        //concatenate user options with _defaults
                        _options = $.extend(_defaults, _options);
                        if (_options.maxRepeats < 2) _options.maxRepeats = 2;

                        function charsetToString()
                        {
                            return CHARSETS.upperCaseSet + CHARSETS.lowerCaseSet + CHARSETS.digitSet + CHARSETS.specialSet;
                        }

                        //GENERATE ALL REGEXs FOR EVERY CASE
                        function buildPasswordRegex()
                        {
                            var cases = [];

                            if (_options.noUpper)   cases.push({"regex": "(?=" + CHARSETS.upperCaseSet + ")", "message": "A senha nao pode conter letra maiuscula."});
                            else                    cases.push({"regex": "(?=" + ("[" + CHARSETS.upperCaseSet + "][^" + CHARSETS.upperCaseSet + "]*").repeat(_options.minUpperCase) + ")", "message": "A senha deve conter ao menos " + _options.minUpperCase + " letra maiuscula.",   "case":1, "missing":true});

                            if (_options.noLower)   cases.push({"regex": "(?=" + CHARSETS.lowerCaseSet + ")", "message": "A senha nao pode conter letra minuscula."});
                            else 				    cases.push({"regex": "(?=" + ("[" + CHARSETS.lowerCaseSet + "][^" + CHARSETS.lowerCaseSet + "]*").repeat(_options.minLowerCase) + ")", "message": "A senha deve conter ao menos " + _options.minLowerCase + " letra minuscula.",   "case":2, "missing":true});

                            if (_options.noSpecial) cases.push({"regex": "(?=" + CHARSETS.specialSet + ")",   "message": "A senha nao pode conter caracter."});
                            else 				    cases.push({"regex": "(?=" + ("[" + CHARSETS.specialSet   + "][^" + CHARSETS.specialSet   + "]*").repeat(_options.minSpecial)   + ")", "message": "A senha deve conter ao menos " + _options.minSpecial   + " caracter especial.", "case":3, "missing":true});

                            if (_options.noDigit)   cases.push({"regex": "(?=" + CHARSETS.digitSet + ")",     "message": "A senha nao pode conter numero."});
                            else 				    cases.push({"regex": "(?=" + ("[" + CHARSETS.digitSet     + "][^" + CHARSETS.digitSet     + "]*").repeat(_options.minDigits)    + ")", "message": "A senha deve conter ao menos "  + _options.minDigits   + " numero.",            "case":4, "missing":true});

                            cases.push({"regex":"[" + charsetToString() + "]{" + _options.minLength + ",}", "message":"A senha deve conter ao menos " + _options.minLength + " caracteres.", "case":5, "missing":true });

                            return cases;
                        }

                        var _cases        = buildPasswordRegex();
                        var _element      = this;
                        var $confirmField = (_options.confirmField != undefined) ? $(_options.confirmField) : undefined;

                        //Field validation on every captured event
                        function validateField()
                        {
                            var failedCases = [];

                            //Evaluate all verbose cases
                            $.each(_cases, function(i, _case) {
                                if( $(_element).val().search( new RegExp(_case.regex, "g")) == -1)
                                {
                                    failedCases.push({
                                        "case" : _case.case,
                                        "missing" : _case.missing,
                                        "message" : _case.message
                                    });
                                }
                                else
                                {
                                    failedCases.push({
                                        "case" : _case.case,
                                        "missing" : false,
                                        "message" : _case.message
                                    });
                                }
                            });

                            if (_options.failRepeats && $(_element).val().search(new RegExp("(.)" + (".*\\1").repeat(_options.maxRepeats - 1), "gi")) != -1)
                            {
                                failedCases.push("A senha não pode conter " + _options.maxRepeats + " do mesmo caractere. Maiúsculas e minúsculas contam como iguais.");
                            }
                            if (_options.failConsecutive && $(_element).val().search(new RegExp("(?=(.)" + ("\\1").repeat(_options.maxConsecutive) + ")", "g")) != -1)
                            {
                                failedCases.push("A senha não pode conter o mesmo caracter mais que " + _options.maxConsecutive + " vezes consecutivas.");
                            }

                            //Determine if valid
                            var count = 0;
                            failedCases.forEach(c => {
                                if (c.missing) count++;
                            });
                            var validPassword = (count == 0) && ($(_element).val().length >= _options.minLength);
                            var fieldsMatch   = true;

                            if ($confirmField != undefined)
                            {
                                fieldsMatch = ($confirmField.val() == $(_element).val());
                            }

                            _callback(_element, validPassword, validPassword && fieldsMatch, failedCases);
                        }

                        //Add custom classes to fields
                        this.each(function() {
                            //Validate field if it is already filled
                            if ($(this).val()) validateField().apply(this);

                            $(this).toggleClass("jqPassField", true);

                            if ($confirmField != undefined) $confirmField.toggleClass("jqPassConfirmField", true);
                        });

                        //Add event bindings to the password fields
                        return this.each(function() {
                            $(this).bind('keyup focus input proprtychange mouseup', validateField);

                            if ($confirmField != undefined) $confirmField.bind('keyup focus input proprtychange mouseup', validateField);
                        });
                    }
                });
            })(jQuery);
        </script>
    </body>
</html>