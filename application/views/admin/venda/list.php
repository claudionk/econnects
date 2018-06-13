
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
<div class="col-md-12">
<h2 class="text-primary text-center">Selecione o Produto</h2>
    <?php foreach($rows as $row) :?>
        <div class="col-md-4">
            <div class="card card-type-pricing">
                <div class="card-body card-produto text-center style-gray">
                    <h2 class="text-light"><?php echo $row['nome'];?></h2>
                    <div class="price">
                        <img src="<?php echo app_assets_url('core/images/ico/ico_' . $row['slug']. '.png' , 'admin'); ?>">
                    </div>
                    <br>
                    <p class="opacity-50"><em><?php echo $row['parceiro_nome'];?></em></p>
                </div><!--end .card-body -->
                <div class="card-body">
                    <a href="<?php echo base_url("{$current_controller_uri}/iniciar_venda/{$row[$primary_key]}")?>" class="btn ink-reaction btn-raised btn-primary">Venda</a>
                </div><!--end .card-body -->
            </div><!--end .card -->
        </div><!--end .col -->
    <?php endforeach; ?>
</div>
<?php if(isset($carrinho) && count($carrinho) > 0) : ?>
<div class="col-md-12">
    <div class="col-md-6">
        <div class="card card-underline">
            <div class="card-head">
                <header>Carrinho de Compras</header>
            </div>
            <div class="card-body">
                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th width="40%">PEDIDO</th>
                            <th width="40%">PRODUTO</th>
                            <th width="20%">VALOR</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $valor_total = 0; ?>
                        <?php foreach ($carrinho as $pedido) : ?>
                            <tr>
                                <td><?php echo $pedido['codigo']; ?></td>
                                <td><?php echo $pedido['nome']; ?></td>
                                <td><?php  echo app_format_currency($pedido['valor_total'], false, 2 ); ?></td>
                            </tr>
                            <?php $valor_total += $pedido['valor_total']; ?>

                        <?php endforeach; ?>
                        <tr>
                            <td class="text-right" colspan="2"><strong>TOTAL: </strong></td>
                            <td><?php  echo app_format_currency($valor_total, false, 2 ); ?></td>
                        </tr>

                        </tbody>
                    </table>
                    <div class="card-body">
                        <a class="btn  btn-app btn-primary" href="<?php echo base_url("{$current_controller_uri}/pagamento_carrinho")?>">
                            <i class="fa fa-edit"></i> Efetuar pagamento
                        </a>
                    </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>