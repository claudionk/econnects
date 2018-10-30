<?php
if($_POST)
    $row = $_POST;
?>

<div class="section-header">
        <ol class="breadcrumb">
            <li class="active"><?php echo app_recurso_nome();?> <span class="text-danger"><?php echo $parceiro['nome'];?></li>
        </ol>
    </div>

    <div class="card">

        <!-- Widget heading -->
        <div class="card-body">
            <a href="<?php echo base_url("admin/parceiros/index")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-edit"></i> Salvar
            </a>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <label>* É possível habilitar apenas o Produto ou cada Plano do produto</label>
            <form method="post" id="validateSubmitForm">
                <input type="hidden" name="parceiro_id" value="<?php echo $parceiro_id;?>" />


                <ul class="list acoes">

                    <?php foreach($produtos as $prod) : ?>

                        <li class="">

                            <div idAcao='1' class="checkbox checkbox-inline checkbox-styled">
                                <label>
                                    <h4>
                                    <input <?php echo (!empty($prod['ok'])) ? 'checked' : ''; ?> idAcao='1' idRecurso='<?php echo $prod['produto_parceiro_id'] ?>' class='btRecurso' type="checkbox" name='produto[<?php echo $prod['produto_parceiro_id'] ?>]'>
                                    
                                    <strong>Produto: </strong>  <?php echo $prod['nome_prod_parc'];?></h4>
                                </label>
                             </div>

                             <div class="acoes">
                            <?php foreach($prod['planos'] as $plan) : ?>

                                <div idAcao='<?= $plan['produto_parceiro_id'] ?>' class="checkbox checkbox-inline checkbox-styled">
                                    <label>
                                        <input <?php echo (!empty($plan['ok'])) ? 'checked' : ''; ?> idAcao='<?= $plan['produto_parceiro_id'] ?>' idRecurso='<?php echo $plan['produto_parceiro_id'] ?>' class='btRecurso' type="checkbox" name='plano[<?php echo $prod['produto_parceiro_id'] ?>][<?php echo $plan['produto_parceiro_plano_id'] ?>]'>
                                        <span><?php echo $plan['nome'] ?></span>
                                    </label>
                                 </div>

                            <?php endforeach;?>
                            </div>
                        </li>

                    <?php endforeach;?>
                </ul>

            </form>
        </div>
    </div>

    <div class="card">

        <!-- Widget heading -->
        <div class="card-body">
            <a href="<?php echo base_url("admin/parceiros/index")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-edit"></i> Salvar
            </a>
        </div>

    </div>
