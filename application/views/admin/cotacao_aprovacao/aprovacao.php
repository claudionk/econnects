<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome();?></li>
    </ol>
</div>

<?php $this->load->view("admin/cotacao_aprovacao/search_form"); ?>

<div class="row">
    <div class="col-md-6">
        <?php $this->load->view('admin/partials/messages'); ?>
    </div>
</div>
<form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">
    <input type="hidden" name="frmAprovacao" value="1">
<div class="card">
    <div class="card-body">

        <!-- Table -->
        <form>
            <table class="table table-hover">

                <!-- Table heading -->
                <thead>
                <tr>
                    <th width='2%'><div class="checkbox checkbox-inline checkbox-styled"><label><input type="checkbox" name="selec_todos" id="checkAll"></label></div></th>
                    <th width='8%'>Cotação</th>
                    <th width='20%'>Parceiro</th>
                    <th width='10%'>Produto</th>
                    <th width='5%'>Desconto</th>
                    <th width='5%'>Repasse</th>
                    <th width='5%'>Comissão</th>
                    <th width='5%'>Valor</th>
                    <th width='20%'>Data /hora</th>
                    <th class="center" width='25%'>Ações</th>
                </tr>
                </thead>
                <!-- // Table heading END -->

                <!-- Table body -->
                <tbody>

                <!-- Table row -->
                <?php foreach($rows as $row): ?>
                    <tr>
                        <td><div class="checkbox checkbox-inline checkbox-styled"><label><input type="checkbox" class="checkbox_row" name="selec_row[]" id="selec_row_<?php echo $row[$primary_key]; ?>" value="<?php echo $row[$primary_key]; ?>"></label></div></td>
                        <td><?php echo $row['codigo'];?></td>
                        <td><?php echo $row['nome_fantasia'];?></td>
                        <td><?php echo $row['produto_nome'];?></td>
                        <td><?php echo app_format_currency($row['desconto_condicional_valor'], false, 2); ?></td>
                        <td><?php echo app_format_currency($row['repasse_comissao'], false, 2); ?> %</td>
                        <td><?php echo app_format_currency($row['comissao_corretor'], false, 2); ?> %</td>
                        <td><?php echo app_format_currency($row['premio_liquido_total'], false, 2); ?></td>
                        <td><?php echo app_date_mysql_to_mask($row['criacao']);?></td>
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

<div class="card">

    <!-- Widget heading -->
    <div class="card-body">
        <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
            <i class="fa  fa-check-square-o"></i> Aprovar Desconto
        </a>
    </div>

</div>
</form>