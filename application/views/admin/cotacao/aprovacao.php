<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome();?></li>
    </ol>
</div>

<div class="row">
    <div class="col-md-6">
        <?php $this->load->view('admin/partials/messages'); ?>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <!-- Table -->
        <table class="table table-hover">

            <!-- Table heading -->
            <thead>
            <tr>
                <th width='30%'>Cotação</th>
                <th width=''>Nome</th>
                <th width=''>Contato</th>
                <th width=''>Motivo</th>
                <th class="center" width='25%'>Ações</th>
            </tr>
            </thead>
            <!-- // Table heading END -->

            <!-- Table body -->
            <tbody>

            <!-- Table row -->
            <?php foreach($rows as $row) :?>
            <tr>

                <td><?php echo $row['codigo'];?></td>
                <td><?php echo $row['nome'];?></td>
                <td><?php echo $row['contato_telefone'];?></td>
                <td class="center">
                    <a href="<?php echo base_url("$current_controller_uri/view/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Ver' </a>
                    <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <!-- // Table row END -->

            </tbody>
            <!-- // Table body END -->

        </table>
        <!-- // Table END -->

        <?php echo $pagination_links ?>

    </div>
</div>