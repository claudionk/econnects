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
                        <a href="<?php echo base_url("admin/integracao/index")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                        <a href="<?php echo base_url("$current_controller_uri/add/{$integracao_id}")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Adicionar
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>
                <?php $this->load->view('admin/integracao_layout/search_form')?>
                <!-- Widget -->
                <div class="card">


                    <div class="card-body">
                        <!-- Table -->
                        <table class="table table-hover">
                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th class="center" width="10%">ID</th>
                                <th width='10%'>Ordem</th>
                                <th width='10%' >Tipo</th>
                                <th width='10%'>Início</th>
                                <th width='10%'>Fim</th>
                                <th width='20%'>Nome</th>
                                <th width='10%'>Campo</th>
                                <th class="center" width='20%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php  foreach($rows as $row) :?>
                            <tr>

                                <td class="center"><?php echo $row[$primary_key];?></td>
                                <td><?php echo $row['ordem'];?></td>
                                <td><?php echo $row['tipo'];?></td>
                                <td><?php echo $row['inicio'];?></td>
                                <td><?php echo $row['fim'];?></td>
                                <td><?php echo $row['nome'];?></td>
                                <td><?php echo $row['campo_tipo'];?></td>
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
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>