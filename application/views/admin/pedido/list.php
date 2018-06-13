<?php $this->load->view("admin/partials/page_head"); ?>
<?php $this->load->view("admin/pedido/search_form"); ?>
<!-- Widget -->
<div class="card">
    <div class="card-body">
        <!-- Table -->
        <table class="table table-hover">

            <!-- Table heading -->
            <thead>
                <tr>
                    <th width='5%'><div class="checkbox checkbox-inline checkbox-styled"><label><input type="checkbox" name="selec_todos" id="checkAll"></label></div></th>
                    <th width='15%'>Pedido</th>
                    <th width='20%'>Cliente</th>
                    <th width='20%'>Data</th>
                    <th width='20%'>Valor Total</th>
                    <th width='20%'>Status</th>
                    <th class="center" width='25%'>Ações</th>
                </tr>
            </thead>
            <!-- // Table heading END -->

            <!-- Table body -->
            <tbody>
                <!-- Table row -->
                <?php foreach($rows as $row) :?>
                    <?php $status = ($row['pedido_status_id'] == 5 || $row['pedido_status_id'] == 10) ? ' class="danger"' : '' ?>
                    <tr<?php echo $status; ?>>
                        <td><div class="checkbox checkbox-inline checkbox-styled"><label><input type="checkbox" class="checkbox_row" name="selec_row[]" id="selec_row_<?php echo $row[$primary_key]; ?>"></label></div></td>
                        <td><?php echo $row['codigo'];?></td>
                        <td><?php echo $row['razao_nome'];?></td>
                        <td><?php echo app_date_mysql_to_mask($row['criacao']);?></td>
                        <td><?php echo app_format_currency($row['valor_total']); ?></td>
                        <td><?php echo $row['pedido_status_nome'];?></td>
                        <td class="center">
                            <a href="<?php echo admin_url("pedido/view/{$row[$primary_key]}") ?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Visualizar </a>
                        </td>
                    </tr>

                <?php endforeach; ?>
                <!-- // Table row END -->

            </tbody>
            <!-- // Table body END -->

        </table>
        <!-- // Table END -->
        <?php echo $pagination_links; ?>
    </div>
</div>