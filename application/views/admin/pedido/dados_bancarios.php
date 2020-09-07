<div class="row ajsRow">
    <div class="col-sm-4">
        <div class="form-group">
            <label for="">A conta bancária pertence ao</label>
            <select required id="segurado" name="segurado" class="form-control conta_terceiro">
                <option value="S" selected>Segurado</option>
                <option value="T">Terceiro</option>
            </select>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="">Tipo de favorecido</label>
            <select required id="tipofavorecido" name="tipofavorecido" class="form-control">
                <option value="PF">Pessoa física</option>
                <option value="PJ">Pessoa jurídica</option>
            </select>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="">Tipo de conta</label>
            <select required name="tipoconta" class="form-control">
                <option value="corrente">Conta Corrente</option>
                <option value="conta_facil">Conta Fácil</option>
                <option value="poupanca">Conta Poupança</option>
            </select>
        </div>
    </div>
</div>
<div class="row ajsRow">
    <div class="col-sm-4">
        <div class="form-group">
            <label for="">Nome do favorecido</label>
            <input required type="text" class="form-control" name="nome" value="">
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="">CPF/CNPJ Favorecido</label>
            <input required type="text" class="form-control" name="cpf_cnpj" value="">
        </div>
    </div>
</div>
<div class="row ajsRow">
    <div class="col-sm-4">
        <div class="form-group">
            <label for="">Banco do favorecido</label>
            <select required id="banco" name="banco" class="form-control ajsSel">
                <option value="" style="display:none">Selecione o banco</option>
                <?php foreach ($bancos as $banco) :  ?>
                    <option value="<?php echo $banco->codigo ?>"><?php echo $banco->nome ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-sm-8 no-padding">
        <div class="form-group">
            <div class="col-md-3">
                <label for="">Agência</label>
                <input required type="number" min="0"  pattern="[0-9]+" maxlength="4" class="form-control" id="" name="agencia" value="">
            </div>
            <div class="col-sm-4">
                <label for="">Conta</label>
                <input required type="number" min="0" class="form-control" id="conta" name="conta" value="">
            </div>
            <div class="col-sm-2 ">
                <label for="">Dígito</label>
                <input pattern="[a-zA-Z0-9]+" maxlength="1" required type="text" class="form-control" id="digito" name="digito" value="">
            </div>
        </div>
    </div>
</div>
<div class="row ajsRow">
    <div class="col-sm-12">
        <br>
        <font color="red"><b>Importante: </b></font>Preencha os dados corretamente.
        <br>
        <br>
    </div>
</div>