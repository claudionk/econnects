<?php
if($_POST)
    $row = $_POST;
?>
<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                    <li class="active"><?php echo $page_subtitle;?></li>
                </ol>

            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("admin/usuarios_acl_recursos/index")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                    <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                        <i class="fa fa-edit"></i> Salvar
                    </a>
                </div>

            </div>
            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">


                <!-- col-table-row -->
                <div class="col-table-row">
                    <form method="post" id="validateSubmitForm">
                        <input type="hidden" name="usuario_acl_recurso_id" value="<?php echo $usuario_acl_recurso_id;?>" />
                        <!-- col-app -->
                        <div class="col-app col-unscrollable">

                            <!-- col-app -->
                            <div class="row col-app innerAll">
                                <div class="col-md-6">
                                    <?php $this->load->view('admin/partials/validation_errors');?>
                                    <?php $this->load->view('admin/partials/messages'); ?>
                                </div>
                            </div>



                            <!-- col-app -->
                            <div class="row col-app innerAll">



                                    <div class="col-md-4">
                                        <div class="panel panel-success">
                                            <div class="panel-heading text-center"><strong><?php echo $recurso['nome']?></strong></div>
                                            <div class="panel-body">

                                                <?php foreach($acoes as $acao) :?>


                                                    <div class="checkbox">

                                                        <label  for="acao_<?php echo $acao['usuario_acl_acao_id'];?>">
                                                            <input type="checkbox" id="acao_<?php echo $acao['usuario_acl_acao_id'];?>" name="acoes[]"
                                                                <?php if( isset($current_acoes[$acao['usuario_acl_acao_id']]) ) echo 'checked="checked"';?>
                                                                   value="<?php echo $acao['usuario_acl_acao_id'];?>"  />  <?php echo $acao['nome']?>
                                                        </label>

                                                    </div>
                                                <?php endforeach;?>


                                            </div>
                                        </div>
                                    </div>




                            </div>


                        </div>
                        <div class="card">

                            <!-- Widget heading -->
                            <div class="card-body">
                                <a href="<?php echo base_url("admin/usuarios_acl_recursos/index")?>" class="btn  btn-app btn-primary">
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
    </div>
</div>
