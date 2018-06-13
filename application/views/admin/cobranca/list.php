<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <!-- col-separator -->
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
                        <a href="javascript:void(0);" class="btn btn-app btn-primary btn-export">
                            <i class="fa  fa-download"></i> Exportar Excel
                        </a>
                    </div>
                </div>

                <input type="hidden" name="url_excel" id="url_excel" value="<?php echo base_url("{$current_controller_uri}/excel/")?>">

                <?php $this->load->view("admin/cobranca/search_form", array('parceiros' => $parceiros)); ?>


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
                                <th width='40%'>Item</th>
                                <th width='30%'>Tipo</th>
                                <th width='10%'>Quantidade</th>
                                <th width='10%'>Valor Unitario</th>
                                <th width='10%'>Valor Total</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                            <tr>

                                <td><?php echo $row['item'];?></td>
                                <td><?php echo $row['tipo'];?></td>
                                <td><?php echo $row['quantidade'];?></td>
                                <td><?php echo $row['valor'];?></td>
                                <td><?php echo $row['valor_total'];?></td>
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