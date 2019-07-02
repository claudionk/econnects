<div ng-controller="<?php echo $model_name ?>">

    <div class="section-header">
        <div class="row">
            <div class="col-md-6">
                <h2><?php echo $titulo;?></h2>
            </div>
            <div class="col-md-6 acoes text-right">
                <a href="<?php echo base_url("$controller_url/add")?>" class="btn  btn-app btn-primary">
                    <i class="fa  fa-plus"></i> Adicionar
                </a>
            </div>
        </div>
    </div>

    <?php $this->load->view('admin/partials/messages'); ?>

    <!-- Widget -->
    <div class="card">

        <div class="card-body">

            <div id="grid" ng-init="load_grid()"></div>

        </div>
    </div>

</div>



