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
    <script src="<?php echo app_assets_url('template/js/libs/jquery/jquery-1.12.4.min.js', 'segurado');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/jquery/jquery-migrate-1.2.1.min.js', 'segurado');?>"></script>
    <script src="<?php echo app_assets_url('core/js/jquery.mask.min.js', 'segurado');?>"></script>

    <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/bootstrap.css", 'segurado');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/materialadmin.css", 'segurado');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/font-awesome.min.css", 'segurado');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/material-design-iconic-font.min.css", 'segurado');?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/libs/bootstrap-datepicker/datepicker3.css", 'segurado');?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/libs/toastr/toastr.css", 'segurado');?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("template/css/{$theme}/libs/select2/select2.min.css", 'segurado');?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo app_assets_url("core/css/base.css", 'segurado');?>" />

    <?php echo $css_for_layout;?>

    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/html5shiv.js', 'segurado');?>"></script>
    <script type="text/javascript" src="<?php echo app_assets_url('template/js/libs/utils/respond.min.js', 'segurado');?>"></script>
    <![endif]-->
    <!--    <script src="<?php echo app_assets_url('template/js/core/source/angular.min.js', 'segurado');?>"></script> -->

    <?php if(isset($enable_ckeditor)) :?>
        <script src="<?php echo app_assets_url("ckeditor/ckeditor.js", 'common');?>"></script>
        <script src="<?php echo app_assets_url("ckfinder/ckfinder.js", 'common');?>"></script>
    <?php endif?>

    <script type="text/javascript">
        var ADMIN_URL = '<?php echo base_url('segurado'); ?>';
        var base_url = '<?php echo base_url() ?>';

        // Seta APP para Angular JS
        //var app = angular.module("App", []);
    </script>
    <script src="<?php echo app_assets_url('core/js/anchor.min.js', 'segurado');?>"></script>
    <script src="<?php echo app_assets_url('core/js/util.js', 'segurado');?>"></script>
    <script src="<?php echo app_assets_url('core/js/prettify.min.js', 'segurado');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/select2/select2.full.min.js', 'segurado');?>"></script>
    <script src="<?php echo app_assets_url('template/js/libs/select2/i18n/pt-BR.js', 'segurado');?>"></script>
</head>
<body class="menubar-hoverable header-fixed menubar-pin ">

<?php $this->load->view('segurado/partials/header');?>

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

    <?php $this->load->view('segurado/partials/menu_lateral');?>

</div><!--end #base-->
<!-- END BASE -->


<!-- Modal -->
<div id="modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- Fim Modal-->

<!-- BEGIN MODAL DELETE -->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="modal-delete-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="simpleModalLabel">Confirmar Exclusão</h4>
            </div>
            <div class="modal-body">
                <p>Deseja Realmente Excluir esse registro?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-confirmar-excluir">Confirmar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modal-termo_aceite">

    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal heading -->
            <div class="modal-header">
                <h3 class="modal-title">Termo de Aceite</h3>
            </div>
            <!-- // Modal heading END -->

            <!-- Modal body -->
            <div class="modal-body">
                <form id="aceitar_termo" method="post" action="<?php echo base_url("segurado/login/aceite_termo")?>" >
                <!-- Row -->
                <div class="row">
                    <div class="col-md-12">


                            <div><?php if(isset($userdata['parceiro_termo'])) { echo $userdata['parceiro_termo']; }; ?></div>

                    </div>
                </div>

                <!-- // Row END -->
                <div class="separator"></div>

                <!-- Form actions -->
                <div class="form-actions">
                    <button type="submit" id="btn_aceitar_termo" class="btn btn-primary"><i class="fa fa-check-circle"></i> Aceitar Termo</button>
                </div>
                <!-- // Form actions END -->

                </form>

            </div>
            <!-- // Modal body END -->

        </div>
    </div>

</div>


<!-- BEGIN JAVASCRIPT -->
<script src="<?php echo app_assets_url('template/js/libs/jquery-validation/dist/jquery.validate.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/bootstrap/bootstrap.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/spin.js/spin.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/autosize/jquery.autosize.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/moment/moment.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/bootstrap-datepicker/locales/bootstrap-datepicker.pt-BR.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/nanoscroller/jquery.nanoscroller.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/libs/inputmask/jquery.inputmask.bundle.min.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/App.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppNavigation.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppOffcanvas.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppCard.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppForm.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppNavSearch.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/source/AppVendor.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('template/js/core/demo/Demo.js', 'segurado');?>"></script>
<script src="<?php echo app_assets_url('core/js/base.js', 'segurado');?>"></script>
<?php echo $js_for_layout;?>
<!-- END JAVASCRIPT -->
</body>
</html>