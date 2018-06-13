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

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                </div>

            </div>
            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">



                <!-- Widget -->
                <div class="card">

                    <!-- // Widget heading END -->

                    <div class="card-body">
                        <!-- Table -->
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>

                                <th width="20%">Nome do Parceiro</th>
                                <th class="center" width='5%'>Pontuação</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>


                            <?php if($rows) :?>
                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                                <tr>


                                    <td><?php echo $row['parceiro_nome'];?></td>
                                    <td><?php echo $row['pontuacao_total'];?></td>

                                </tr>
                            <?php endforeach;?>
                            <!-- // Table row END -->
                            <?php else :?>
                                <tr>
                                    <td colspan="2">Nenhum registro foi encontrado.</td>
                                </tr>
                            <?php endif;?>

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->

                    </div>
                </div>

                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                    </div>

                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>