<div class="row">
    <div class="col-md-12">
        <?php if($this->session->flashdata('loginerro')): ?>
            <div class="alert alert-danger fade in widget-inner">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <i class="fa fa-times"></i> <?php echo $this->session->flashdata('loginerro');?>
            </div>
        <?php endif;?>
    </div>

</div>

<div class="col-sm-3 center">
</div>
<div class="col-sm-6 center">
    <img width="100%" src="<?php echo $theme_logo; // app_assets_url('template/img/logo-connects.png', 'admin'); ?>">
    <br/>
    <br/>
    <span class="text-lg text-bold text-primary">Entre na sua conta</span>


    <br/><br/>
    <form class="form floating-label" action="<?php echo $login_form_url;?>" accept-charset="utf-8" method="post">
        <div class="form-group">
            <input type="text" class="form-control" id="username" name="login">
            <label for="username">E-mail:</label>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" id="password" name="password">
            <label for="password">Senha</label>
            <p class="help-block"><a href="#">Esqueceu a senha?</a></p>
        </div>
        <br/>

        <div class="row">

            <div class="col-xs-6 text-left"><!--
                <div class="checkbox checkbox-inline checkbox-styled">
                    <label>
                        <input type="checkbox"> <span>Remember me</span>
                    </label>
                </div>-->
            </div><!--end .col -->
            <div class="col-xs-6 text-right">
                <button class="btn btn-primary btn-raised" type="submit">Login</button>
            </div><!--end .col -->
        </div><!--end .row -->
    </form>
</div><!--end .col -->
<div class="col-sm-3 center">
</div>
