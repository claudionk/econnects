<?php
if($_POST)
    $row = $_POST;
?>

<div class="section-header">
        <ol class="breadcrumb">
            <li class="active"><?php echo app_recurso_nome();?></li>
        </ol>
    </div>

    <div class="card">

        <!-- Widget heading -->
        <div class="card-body">
            <a href="<?php echo base_url("admin/usuarios_acl_tipos/index")?>" class="btn  btn-app btn-primary">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                <i class="fa fa-edit"></i> Salvar
            </a>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" id="validateSubmitForm">
                <input type="hidden" name="usuario_acl_tipo_id" value="<?php echo $usuario_acl_tipo_id;?>" />

                <h4><strong>Grupo: </strong>  <?php echo $row['nome'];?></h4>

                <ul class="list acoes">

                    <?php foreach($recursos as $recurso) : ?>

                        <li class="">
                            <div idAcao='1' class="checkbox checkbox-inline checkbox-styled">
                                <label>
                                    <input <?php echo (app_get_acao_permitida($recurso['acoes'], 1)) ? 'checked' : ''; ?> idAcao='1' idRecurso='<?php echo $recurso['usuario_acl_recurso_id'] ?>' class='btRecurso' type="checkbox" name='recurso_acao[<?php echo $recurso['usuario_acl_recurso_id'] ?>][1]'>
                                    <span><?php echo $recurso['nome'] ?></span>
                                </label>
                             </div>

                            <?php echo app_print_recurso_filho($recurso); ?>
                        </li>

                    <?php endforeach;?>
                </ul>

            </form>
        </div>
    </div>
