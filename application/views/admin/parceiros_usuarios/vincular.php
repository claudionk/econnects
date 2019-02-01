<?php
if($_POST)
    $row = $_POST;
?>
 <?php // echo '<pre>'; print_r($itens); die; ?>
 <!-- cobertura[303][82] -->
<div class="section-header">
        <ol class="breadcrumb">
            <li class="active"><?php echo app_recurso_nome();?> <span class="text-danger"><?php echo $parceiro['nome'];?></li>
        </ol>
    </div>

    <div class="card">

        <!-- Widget heading -->
        <div class="card-body">

             <a href="<?php echo base_url("admin/parceiros_usuarios/view/$parceiro_id")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-edit"></i> Salvar
            </a>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <label>* É possível habilitar apenas a cobertura do produto</label>
            <form method="post" id="validateSubmitForm">
                <input type="hidden" name="parceiro_id" value="<?php echo $parceiro_id;?>" />
                <input type="hidden" name="usuario_id" value="<?php echo $id_usuario;?>" />


                <ul class="list acoes">
                    <?php $i = 0; ?>
                    <?php foreach($produtos as $prod) : ?>
                        <li class="">

                            <div>
                                 <label>
                                    <h4>                                   
                                    <strong>Produto: </strong>  <?php echo $prod['nome_prod_parc'];?></h4>
                                </label>
                            </div>

                             <div class="acoes">
                             <?php foreach($prod['planos'] as $plan) : ?>
                                <div>
                                  <label>
                                      <span><strong><?php echo $plan['nome'] ?></strong></span>
                                  </label>
                                </div>
                                <div>
                                  <?php foreach ($plan['cobertura'] as $k => $v) : ?>
                                    <div idAcao='<?= $i; ?>' class="checkbox checkbox-inline checkbox-styled">
                                      <label>
                                        <input type="checkbox" <?php print($v['selecionado'] > 0 ? 'checked' : ''); ?> name='cobertura[<?php echo $v['cobertura_plano_id'] ?>][<?php echo $v['cobertura_id'] ?>]'>
                                      </label>
                                    </div>
                                    <?php echo "<i>".$v['nome']."</i><br>" ?>
                                    <?php $i++; ?>
                                  <?php endforeach; ?>
                                </div>
                                <br>
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
            <a href="<?php echo base_url("admin/parceiros_usuarios/view/$parceiro_id")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-edit"></i> Salvar
            </a>
        </div>

    </div>


