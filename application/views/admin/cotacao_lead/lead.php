
<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome();?></li>
    </ol>
</div>

<?php $this->load->view("admin/cotacao_lead/search_form"); ?>


<div class="row">
    <div class="col-md-6">
        <?php $this->load->view('admin/partials/messages'); ?>
    </div>
</div>

<!-- Widget -->
<div class="card">

    <div class="card-body">
        <!-- Table -->
        <form>
        <table class="table table-hover">

            <!-- Table heading -->
            <thead>
            <tr>
                <th width='5%'><div class="checkbox checkbox-inline checkbox-styled"><label><input type="checkbox" name="selec_todos" id="checkAll"></label></div></th>
                <th width='15%'>Cotação</th>
                <th width='20%'>Nome</th>
                <th width='10%'>Celular</th>
                <th width='15%'>Data /hora</th>
                <th width='10%'>Status</th>
                <th class="center" width='25%'>Ações</th>
            </tr>
            </thead>
            <!-- // Table heading END -->

            <!-- Table body -->
            <tbody>

            <!-- Table row -->
            <?php foreach($rows as $row): ?>
            <tr>
                <td><div class="checkbox checkbox-inline checkbox-styled"><label><input type="checkbox" class="checkbox_row" name="selec_row[]" id="selec_row_<?php echo $row[$primary_key]; ?>"></label></div></td>
                <td><?php echo $row['codigo'];?></td>
                <td><?php echo $row['razao_nome'];?></td>
                <td><?php echo app_format_telefone($row['celular']);?></td>
                <td><?php echo app_date_mysql_to_mask($row['criacao']);?></td>
                <td><?php echo app_get_step_cotacao($row['step']);?></td>
                <td class="center">
                    <a href="<?php echo base_url("$current_controller_uri/view/{$row[$primary_key]}") ?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Mais detalhes </a>
                    <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <!-- // Table row END -->

            </tbody>
            <!-- // Table body END -->

        </table>
        </form>
        <!-- // Table END -->


        <?php echo $pagination_links ?>

    </div>
</div>
