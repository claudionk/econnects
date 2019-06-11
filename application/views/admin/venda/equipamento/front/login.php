
<?php $this->load->view('admin/venda/equipamento/front/step', array('step' => 3, 'produto_parceiro_id' =>  issetor($produto_parceiro_id), 'title' => 'LOGIN')); ?>

<div class="col-md-12 col-sm-12 col-xs-12 icon-login">
    <i class="fa fa-lock color-primary border-primary" aria-hidden="true"></i>
</div>

<form method="post" id="formLogin" class="form-login">
    <input type="hidden" id="cliente_id"  name="cliente_id" value="<?php echo isset($cliente_id)? $cliente_id : ''; ?>" />

    <div class="col-md-6 message">
        <div id="toast-container" class="toast-top-right" aria-live="polite" role="alert">
            <div class="toast toast-error">
                <div class="toast-title">Atenção! </div>
                <div class="toast-message"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 ">
        <div class="form-group">
            <label class="control-label" for="cnpj_cpf">CPF</label>
            <input class="form-control inputmask-cpf" type="text" value="<?php echo isset($cnpj_cpf)? $cnpj_cpf : ''; ?>" disabled="disabled" />
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="form-group">
            <label class="control-label" for="password"> Senha </label>
            <input class="form-control" type="password" name="password" id="password" required="required" />
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="form-group">
            <label class="control-label" for="password_confirm"> Confirme sua senha </label>
            <input class="form-control" type="password" name="password_confirm" id="password_confirm" required="required" />
        </div>
    </div>

    <div class="col-md-6 ">
        <div class="form-group btns">
            <button type="button" class="btn btn-app btn-primary btn-proximo border-primary background-primary" id="btnFormLogin">
                Próximo <i class="fa fa-angle-right" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</form>

<?php $this->load->view('admin/venda/equipamento/components/btn-info'); ?>

<?php $this->load->view('admin/venda/equipamento/components/btn-whatsapp'); ?>

<?php $this->load->view('admin/venda/equipamento/components/footer'); ?>