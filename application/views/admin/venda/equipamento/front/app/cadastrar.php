
<section>
<?php $this->load->view('admin/venda/equipamento/front/head', array('step' => 2, 'produto_parceiro_id' =>  issetor($produto_parceiro_id), 'title' => 'CADASTRAR')); ?>

<div class="row">
    <div class="col-md-12"><?php $this->load->view('admin/partials/messages'); ?></div>
</div>

<form active="" class="form form-dados" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6 btns">
            <div class="form-group">
                <label class="control-label" for="seguradora" >Seguradora</label>
                <select class="form-control select2" name="seguradora" id="seguradora">
                    <option value="M">Seguradora 1</option>
                    <option value="N">Seguradora 2</option>
                    <option value="P">Seguradora 3</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="produto" >Produto</label>
                <select class="form-control select2" name="produto" id="produto">
                    <option value="M">Produto 1</option>
                    <option value="N">Produto 2</option>
                    <option value="P">Produto 3</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
       <div class="col-md-6 ">
            <div class="form-group">
                <label class="control-label" for="vigencia"> Vigência </label>
                <input class="form-control" type="text" name="vigencia" id="vigencia" />
            </div>
        </div>
    </div>

    <div class="row">
       <div class="col-md-6 ">
            <div class="form-group">
                <label class="control-label" for="valor"> Valor </label>
                <input class="form-control" type="text" name="valor" id="valor" />
            </div>
        </div>
    </div>

    <div class="row">
       <div class="col-md-6 ">
            <div class="form-group">
                <label class="control-label" for="num_apolice"> Número da Apólice </label>
                <input class="form-control" type="text" name="num_apolice" id="num_apolice" />
            </div>
        </div>
    </div>

    <div class="row">
       <div class="col-md-6 ">
            <div class="form-group">
                <a href="#" class="btn btn-lg">
                    <span class="glyphicon glyphicon-cloud-upload"></span> Upload
                </a>
            </div>
        </div>
    </div>

</form>
<div class="row">
    
        <div class="form-group btns">
            <button type="button" class="btn btn-app btn-primary btn-proximo border-primary background-primary">
                Cadastrar
            </button>
        </div>
    
</div>
</section>
