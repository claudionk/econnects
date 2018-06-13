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
                        <a href="<?php echo base_url("admin/parceiros/index/") ?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                        <a href="<?php echo base_url("$current_controller_uri/add/{$parceiro_id}")?>" class="btn  btn-app btn-primary">
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
                    

                    <div class="card-body">

                        <!-- Table -->
                        <table class="table table-hover">
                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th width="20%">Nome</th>
                                <th width="15%">Departamento</th>
                                <th width="15%">Cargo</th>
                                <th width="15%">Tipo</th>
                                <th width="15%">Contato</th>
                                <th class="center" width='20%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->

                            <?php  foreach($rows as $row) :?>
                            <tr>

                                <td><?php echo $row['nome'];?></td>
                                <td><?php echo (empty($row['departamento'])) ? 'NENHUM' : $row['departamento'];?></td>
                                <td><?php echo (empty($row['cargo'])) ? 'NENHUM' : $row['cargo'];?></td>
                                <td><?php echo $row['contato_tipo'];?></td>
                                <td><?php echo app_format_contato($row['contato'], $row['contato_tipo_id']);?></td>
                                <td class="center">
                                    <a href="<?php echo base_url("{$current_controller_uri}/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                    <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                </td>
                            </tr>
                            <?php endforeach;?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->
                        <?php echo $pagination_links;?>
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>