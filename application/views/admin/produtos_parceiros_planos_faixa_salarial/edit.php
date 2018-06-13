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
                        <label>Faixa Salarial</label>
                        <select ng-model="faixa_salarial" id="faixa_salarial" multiple="multiple" name="faixa_salarial[]" class="multiselect">
                                <?php foreach ($faixa_salarial as $dado) : ?>
                                    <option value="<?php echo $dado['faixa_salarial_id'] ?>"><?php echo $dado['descricao'] ?></option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                </div>
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
        var faixa_salarial = [<?php foreach($row_faixa_salarial as $r) echo "{$r['faixa_salarial_id']},"; ?>];
    </script>

</form>