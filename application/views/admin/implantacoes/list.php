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
                <?php $this->load->view('admin/implantacoes/search_form')?>
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
                                <th width='10%'>Parceiro</th>
                                <th width='20%'>Produto</th>
                                <th class="center" width='25%'>Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach ($rows as $row) : ?>
                                <tr>

                                    <td><?php echo '-';?></td>
                                    <td><?php echo $row['nome_prod_parc'];?></td>
                                    <td class="center">
                                        <a href="<?php echo base_url("{$current_controller_uri}/view/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-search"></i>  Visualizar </a>
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
