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
                        <a href="<?php echo base_url("$current_controller_uri/add")?>" class="btn  btn-app btn-primary">
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
                                <th class="center">Bilhete</th>
                                <th width='65%'>Nome</th>
                                <th width='65%'>CPF</th>
                                <th class="center" width='25%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                                <tr>

                                    <td class="center"><?php echo $row['num_apolice'];?></td>
                                    <td><?php echo $row['nome'];?></td>
                                    <td><?php echo (app_verifica_cpf_cnpj($row['cnpj_cpf']) == 'CPF') ? app_cpf_to_mask($row['cnpj_cpf']) : app_cnpj_to_mask($row['cnpj_cpf']); ?></td>
                                    <td class="center">
                                        <a target="_blank" href="<?php echo base_url("{$current_controller_uri}/certificado/{$row['apolice_id']}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-print"></i>  Imprimir </a>
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