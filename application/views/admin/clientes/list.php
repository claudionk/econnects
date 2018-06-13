<script type="text/javascript">
    $(document).ready(function () {

        $("input[name=cnpj_cpf]").inputmask("999.999.999-99?99999");

        $('input[name=cnpj_cpf]').live('keyup', function (e)
        {
            var query = $(this).val().replace(/[^a-zA-Z 0-9]+/g,'');;
            if (query.length == 11)
            {
                $("input[name=cnpj_cpf]").inputmask("999.999.999-99?99999");
            }
            if (query.length == 14)
            {
                $("input[name=cnpj_cpf]").inputmask("99.999.999/9999-99");
            }
        });
    });
</script>

<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
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
                        <a href="<?php echo base_url("$current_controller_uri/add/co")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Adicionar CO
                        </a>
                        <a href="<?php echo base_url("$current_controller_uri/add/cf")?>" class="btn  btn-app btn-primary">
                            <i class="fa  fa-plus"></i> Adicionar CF
                        </a>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>

                <?php $this->load->view('admin/clientes/search_form');?>
            </div>
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <div class="card-body">
                        <!-- Table -->
                        <table id="tabela" class="table table-hover">
                            <!-- Table heading -->
                            <thead>
                                <tr>
                                    <th width='5%'>Tipo</th>
                                    <th width='10%' class="center">Código</th>
                                    <th width='20%'>Nome/Razão Social</th>
                                    <th width='15%'>CPNJ/CPF</th>
                                    <th width='10%'>Status</th>
                                    <th class="center">Ações</th>
                                </tr>
                            </thead>

                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                            <tr>

                                <td><?php echo $row['tipo_cliente'];?></td>
                                <td class="center"><?php echo $row['codigo'];?></td>
                                <td><?php echo $row['razao_nome'];?></td>
                                <td><?php
                                    if($row['tipo_cliente'] == 'CF')
                                        echo app_cpf_to_mask($row['cnpj_cpf']);
                                    else
                                        echo app_cnpj_to_mask($row['cnpj_cpf']);?></td>
                                <td><?php echo $row['cliente_evolucao_status_descricao'];?></td>
                                <td class="center">
                                    <a href="<?php echo base_url("admin/clientes_evolucoes/index/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-angle-double-up"></i>  Evolução </a>
                                    <a href="<?php echo base_url("admin/clientes_contatos/index/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-book"></i>  Contatos </a>
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