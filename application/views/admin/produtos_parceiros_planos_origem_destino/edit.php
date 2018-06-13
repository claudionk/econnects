<?php
if($_POST)
    $row = $_POST;
?>
<form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data" ng-controller="OrigemDestino">

    <div class="section-header">
        <ol class="breadcrumb">
            <li class="active"><?php echo app_recurso_nome();?></li>
            <li class="active"><?php echo $page_subtitle;?></li>
        </ol>
    </div>

    <div class="card">
        <div class="card-body">
            <a href="<?php echo base_url("admin/produtos_parceiros_planos/view_by_produto_parceiro/{$row['produto_parceiro_id']}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-edit"></i> Salvar
            </a>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <input type="hidden" name="produto_parceiro_plano_id" value="<?php echo $produto_parceiro_plano_id; ?>"/>


            <div class="row">
                <div class="col-md-6">
                    <?php $this->load->view('admin/partials/validation_errors');?>
                    <?php $this->load->view('admin/partials/messages'); ?>
                </div>

            </div>

            <!-- Row -->
            <div class="row">
                <div class="col-sm-12">

                    <div class="form-group">
                        <label>Origem</label>
                        <select ng-model="origem" id="origem" multiple="multiple" name="origem[]" class="multiselect">
                            <optgroup label="Continentes">
                                <?php foreach ($localidades['continente'] as $dado) : ?>
                                    <option value="<?php echo $dado['localidade_id'] ?>"><?php echo $dado['nome'] ?></option>
                                <?php endforeach; ?>
                            </optgroup>

                            <optgroup label="Paises">
                                <?php foreach ($localidades['pais'] as $dado) : ?>
                                    <option value="<?php echo $dado['localidade_id'] ?>"><?php echo $dado['nome'] ?></option>
                                <?php endforeach; ?>
                            </optgroup>

                            <optgroup label="Estados">
                                <?php foreach ($localidades['estado'] as $dado) : ?>
                                    <option value="<?php echo $dado['localidade_id'] ?>"><?php echo $dado['nome'] ?></option>
                                <?php endforeach; ?>
                            </optgroup>

                            <!--optgroup label="Cidades">
                                <-?php foreach ($localidades['cidade'] as $dado) : ?>
                                    <option value="<-?php echo $dado['localidade_id'] ?>"><?php echo $dado['nome'] ?></option>
                                <-?php endforeach; ?->
                            </optgroup-->
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">

                    <div class="form-group">
                        <label>Destino</label>
                        <select id="destino" name="destino[]" class="multiselect" multiple="multiple">
                            <optgroup label="Continentes">
                                <?php foreach ($localidades['continente'] as $dado) : ?>
                                    <option value="<?php echo $dado['localidade_id'] ?>"><?php echo $dado['nome'] ?></option>
                                <?php endforeach; ?>
                            </optgroup>

                            <optgroup label="Paises">
                                <?php foreach ($localidades['pais'] as $dado) : ?>
                                    <option value="<?php echo $dado['localidade_id'] ?>"><?php echo $dado['nome'] ?></option>
                                <?php endforeach; ?>
                            </optgroup>

                            <optgroup label="Estados">
                                <?php foreach ($localidades['estado'] as $dado) : ?>
                                    <option value="<?php echo $dado['localidade_id'] ?>"><?php echo $dado['nome'] ?></option>
                                <?php endforeach; ?>
                            </optgroup>

                            <!--optgroup label="Cidades">
                                <-?php foreach ($localidades['cidade'] as $dado) : ?>
                                    <option value="<-?php echo $dado['localidade_id'] ?>"><?php echo $dado['nome'] ?></option>
                                <-?php endforeach; ?->
                            </optgroup-->
                        </select>
                    </div>
                </div>

                <!-- // Row END -->

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <a href="<?php echo base_url("admin/produtos_parceiros_planos/view_by_produto_parceiro/{$row['produto_parceiro_id']}")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-edit"></i> Salvar
            </a>
        </div>
    </div>

    <script type="text/javascript">
        var origem = [<?php foreach($row_origem as $r) echo "{$r['localidade_id']},"; ?>];
        var destino = [<?php foreach($row_destino as $r) echo "{$r['localidade_id']},"; ?>];
    </script>

</form>