<?php $this->load->view("admin/partials/page_head"); ?>
<?php $this->load->view("admin/comissao_gerada/search_form"); ?>
<!-- Widget -->
<div class="card">

    <!-- Widget heading -->
    <div class="card-body">

        <a href="<?php echo base_url("$current_controller_uri/exportar_excel/". substr(strrchr($_SERVER['REQUEST_URI'], "?"), 1))?>" class="btn  btn-app btn-primary">
            <i class="fa  fa-download"></i> Exportar
        </a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <!-- Table -->
        <table class="table table-hover">

            <!-- Table heading -->
            <thead>
                <tr>
                    <th width='5%'>Pedido</th>
                    <th width='5%'>Data</th>
                    <th width='10%'>Parceiro</th>
                    <th width='10%'>Tipo</th>
                    <th width='30%'>Descrição</th>
                    <th width='5%'>Prêmio</th>
                    <th width='5%'>Comissão</th>
                    <th width='10%'>Valor Total</th>
                </tr>
            </thead>
            <!-- // Table heading END -->

            <!-- Table body -->
            <tbody>
                <!-- Table row -->
                <?php foreach($rows as $row) :?>
                    <tr>
                        <td><?php echo $row['pedido_codigo'];?></td>
                        <td><?php echo app_dateonly_mysql_to_mask($row['criacao']); ?></td>

                        <td><?php echo $row['parceiro_nome_fantasia'];?></td>
                        <td><?php echo $row['comissao_classe_nome'];?></td>
                        <td><?php echo $row['descricao'];?></td>
                        <td>R$ <?php echo app_format_currency($row['premio_liquido_total']); ?></td>
                        <td><?php echo app_format_currency($row['comissao']); ?>%</td>
                        <td>R$ <?php echo app_format_currency($row['valor']); ?></td>
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