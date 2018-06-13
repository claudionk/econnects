<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>

            </div>

            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("admin/parceiros/index/")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>

                        <a href="<?php echo base_url("$current_controller_uri/add/")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Adicionar
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>
                <!-- Widget -->
                <div class="card">

                        <div class="card-body" style="display: none;">

                            <?php echo implode("\n", app_montar_relacionamento_produto($rows)); ?>
                        </div>
                    <div class="card-body">
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th width='10%'>Produto</th>
                                <th width='20%'>Parceiro</th>
                                <th class="center" width='25%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php  foreach ($rows as $row) : ?>
                                <tr>

                                    <td><?php echo $row['produto']['nome'];?></td>
                                    <td><?php echo $row['produto']['parceiro']['nome'];?></td>
                                    <td class="center">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary btn-parcelas" data-fatura="<?php echo $row['produto']['produto_parceiro_id'];?>">  <i class="fa fa-arrow-circle-down"></i>  Relacionamentos </a>
                                    </td>
                                </tr>
                                <tr style="display: none;" class="grid-parcela grid-grouped-<?php echo $row['produto']['produto_parceiro_id'];?>">
                                    <td colspan="3">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table">

                                                    <!-- Table heading -->
                                                    <thead>
                                                    <tr>
                                                        <th width='65%'>Parceiro</th>
                                                        <th class="center" width='25%'>Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <!-- // Table heading END -->

                                                    <!-- Table body -->
                                                    <tbody>

                                                    <!-- Table row -->
                                                    <?php foreach($row['produto']['lista'] as $lista) :?>
                                                        <tr>

                                                            <td><?php echo $lista['parceiro_nome'];?></td>
                                                            <td class="center">
                                                                <a href="<?php echo base_url("{$current_controller_uri}/edit/{$lista['parceiro_relacionamento_produto_id']}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                                                <a href="<?php echo base_url("$current_controller_uri/delete/{$lista['parceiro_relacionamento_produto_id']}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach;?>
                                                    <!-- // Table row END -->

                                                    </tbody>
                                                    <!-- // Table body END -->

                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="relacionamento_main_<?php echo $row['produto']['produto_parceiro_id']; ?>" class="card-body"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>

                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
<?php



        foreach ($rows as $produto) {
            ?>$("#relacionamento_<?php echo $produto['produto']['produto_parceiro_id']; ?>").orgChart({container: $("#relacionamento_main_<?php echo $produto['produto']['produto_parceiro_id']; ?>")});<?php
        }
        




?>
    });
</script>
