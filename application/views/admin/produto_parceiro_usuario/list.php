<?php
if($_POST){
    $linha = $_POST;
    print_r($linha);
}
?>

<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>
            </div>
            <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="usuario_id" value="<?php if (isset($usuario_id)) echo $usuario_id; ?>"/>
                <input type="hidden" name="parceiro_id" value="<?php if (isset($parceiro_id)) echo $parceiro_id; ?>"/>
            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("admin/parceiros_usuarios/view/{$parceiro_id}"); ?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                        <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                            <i class="fa fa-edit"></i> Salvar
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>

                <!-- Widget -->
                <div class="card">

                    <div class="card-body">

                        <!-- Table -->
                        <table class="table table-hover">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th width='25%'>Parceiro</th>
                                <th width='25%'>Produto</th>
                                <th class="center" width='50%'>Comissão Usuário</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php foreach($rows as $row) :?>
                            <tr>

                                <input type="hidden" name="produto_parceiro_id[]" id="produto_parceiro_id_<?php echo $row['produto_parceiro_id']; ?>" value="<?php echo $row['produto_parceiro_id']; ?>"/>
                                <td><?php echo $row['nome_fantasia'];?></td>
                                <td><?php echo $row['nome'];?></td>
                                <td class="center">
                                    <?php $field_name =  "comissao_{$row['produto_parceiro_id']}" ?>
                                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                        <?php

                                        foreach($comissoes as $linha) { ?>
                                            <option name="" value="<?php echo $linha['comissao_id'] ?>"
                                                <?php if($row['comissao_id'] == $linha['comissao_id']) {echo " selected ";} ?> >
                                                <?php echo $linha['nome']; ?>
                                            </option>
                                        <?php }  ?>
                                    </select>

                                </td>
                            </tr>
                            <?php endforeach;?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <a href="<?php echo base_url("admin/parceiros_usuarios/view/{$parceiro_id}"); ?>" class="btn  btn-app btn-primary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                        <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                            <i class="fa fa-edit"></i> Salvar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>