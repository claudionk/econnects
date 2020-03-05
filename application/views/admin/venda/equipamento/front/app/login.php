
<?php $this->load->view('admin/venda/equipamento/front/head', array('step' => 2, 'produto_parceiro_id' =>  issetor($produto_parceiro_id), 'title' => '')); ?>

<form method="post" id="formLogin" class="form-login" action="<?php echo base_url("$current_uri/home")?>">
    <input type="hidden" id="cliente_id"  name="cliente_id" value="<?php echo isset($cliente_id)? $cliente_id : ''; ?>" />

    <h2 class="text-light text-center text title-h2 text-uppercase">Entrar com seu Celular</h2>
    <div class="col-md-6 ">
        <div class="form-group">
            <input class="form-control inputmask-celular" apponlynumbers="" autofocus="" type="text" placeholder="Escreva seu número">
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="form-group btns">
            <button type="submit" class="btn btn-app btn-primary btn-proximo border-primary background-primary">
                Entrar
            </button>
        </div>
    </div>

    <h2 class="text-light text-center text title-h2 text-uppercase">Autenticar com Redes Sociais</h2>
    <div class="col-xs-6 ">
        <div class="form-group">
            <button class="btn btn-facebook" type="button" /> <i class="fa fa-facebook"></i> Facebook
        </div>
    </div>
    <div class="col-xs-6 ">
        <div class="form-group">
            <button class="btn btn-google" type="button" /> <img src="<?php echo app_assets_url("images/google.png", "common") ?>" width="24"> Google
        </div>
    </div>
    <div class="col-xs-12 ">
        <div class="form-group">
        </div>
    </div>
    <div class="col-xs-12 ">
        <div class="form-group">
        </div>
    </div>

    <h2 class="text-light text-center text title-h2 text-uppercase">Ainda não sou cadastrado</h2>
    <div class="col-md-6 ">
        <div class="form-group">
            <label class="control-label" for="nome"> Nome </label>
            <input class="form-control" type="text" name="nome" id="nome" />
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="form-group">
            <label class="control-label" for="cnpj_cpf">CPF</label>
            <input class="form-control inputmask-cpf" autocomplete="off" name="cnpj_cpf" id="cnpj_cpf" type="text" value="<?php echo isset($cnpj_cpf)? $cnpj_cpf : ''; ?>" <?php if (!empty($cnpj_cpf)) { ?> disabled="disabled"<?php } ?> />
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="form-group">
            <label class="control-label" for="celular"> Celular </label>
            <input class="form-control inputmask-celular" type="text" name="celular" id="celular" />
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="form-group" style="margin-bottom: 0px;margin-top: 10px;">
            <label class="control-label" for="password"> Senha </label>
            <input class="form-control" type="password" name="password" id="password" onkeyup="validarSenhaForca()" />
        </div>
        <div class="form-group row" style="margin-bottom: 0px; height: 20px;">
            <div class="col-sm-5" style=" margin-top: 10px;">
                <div id="erroSenhaForca"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="form-group">
            <label class="control-label" for="password_confirm"> Confirme sua senha </label>
            <input class="form-control" type="password" name="password_confirm" id="password_confirm" />
        </div>
    </div>

    <div class="col-md-6 ">
        <div class="form-group btns">
            <button type="button" class="btn btn-app btn-primary btn-proximo border-primary background-primary" style="margin-bottom: 0px">
                Cadastrar
            </button>
        </div>
    </div>
</form>