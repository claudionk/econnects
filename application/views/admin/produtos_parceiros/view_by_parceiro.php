<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">

            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?> <span class="text-danger"><?php echo $parceiro['nome'];?></li>
                </ol>

            </div>

            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("admin/parceiros/index")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>

                        <a href="<?php echo base_url("$current_controller_uri/add_by_parceiro/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Adicionar
                        </a>

                        <a href="<?php echo base_url("admin/produtos_parceiros_apolice_multiplo_range/index/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Range Múltiplos
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

                    <div class="card-body">


                        <!-- Table -->
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th width='25%'>Nome fantasia</th>
                                <th width='25%'>Produto</th>
                                <th class="center" width='50%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                            <tr>

                                <td><?php echo $row['nome'];?></td>
                                <td><?php echo $row['produto_nome'];?></td>
                                <td class="center">
                                    <a href="<?php echo base_url("{$current_controller_uri}/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                    <a href="<?php echo base_url("admin/produtos_parceiros_configuracao_comissao/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Configurações </a>
                                    <a href="<?php echo base_url("admin/produtos_parceiros_planos/view_by_produto_parceiro/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Planos </a>
                                    <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                </td>
                            </tr>
                            <?php endforeach; echo $pagination_links?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->

                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>