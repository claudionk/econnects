<?php $this->load->view("segurado/partials/page_head"); ?>
<!-- Widget -->
<div class="card">
    <div class="card-body">
        <!-- Table -->
        <table class="table table-hover">

            <!-- Table heading -->
            <thead>
            <tr>
                <th width='15%'>Apólice</th>
                <th width='15%'>Data</th>
                <th width='15%'>Valor Total</th>
                <th width='5%'>Status</th>
                <th class="center" width='50%'>Ações</th>
            </tr>
            </thead>
            <!-- // Table heading END -->

            <!-- Table body -->
            <tbody>
            <!-- Table row -->
            <?php foreach($rows as $row) :?>
                <?php $status = ($row['apolice_status_id'] != 1) ? ' class="danger"' : '' ?>
                <tr<?php echo $status; ?>>
                    <td><?php echo $row['num_apolice'];?></td>
                    <td><?php echo app_dateonly_mysql_to_mask($row['criacao']);?></td>
                    <td><?php echo app_format_currency($row['valor_total']); ?></td>
                    <td><?php echo $row['apolice_status'];?></td>
                    <td class="center">
                        <?php if($row['apolice_status_id'] == 1) : ?>
                            <a target="_blank" href="<?php  echo $row['pdf'] ?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Imprimir </a>
                        <?php endif; ?>
                    </td>
                </tr>

            <?php endforeach; ?>
            <!-- // Table row END -->

            </tbody>
            <!-- // Table body END -->

        </table>
        <!-- // Table END -->

    </div>
</div>