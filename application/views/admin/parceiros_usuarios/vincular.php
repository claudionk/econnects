<?php
if($_POST){
  $row = $_POST;
}
?>
<div class="layout-app" ng-controller="AppController">
  <div class="row row-app">
    <div class="col-md-12">

      <div class="section-header">
        <ol class="breadcrumb">
          <li class="active"><?php echo app_recurso_nome();?></li>
          <li class="active"><?php echo $page_subtitle;?></li>
        </ol>
      </div>

      <div class="card">
        <div class="card-body">
          <a href="<?php echo base_url("{$current_controller_uri}/view/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
          </a>
          <!-- a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa fa-edit"></i> Salvar
          </a -->
        </div>
      </div>

      
    </div>
  </div>
</div>
