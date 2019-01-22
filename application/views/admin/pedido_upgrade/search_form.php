<div class="panel-group" id="accordion1">
    <div class="card panel">

        <div class="card-head" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-1" aria-expanded="false">
            <header class="search">Pesquisar</header>
            <div class="tools">
                <a class="btn btn-floating-action btn-default-light"><i class="fa fa-angle-down"></i></a>
            </div>
        </div>

        <div id="accordion1-1" class="collapse <?php if($_GET) echo 'in'?>" aria-expanded="false" style="height: 0px;">
            <div class="card-body">

                <form class="form-horizontal margin-none" method="get" id="formSearch">

                    <div class="row">
                        <?php $field_name = 'pedido_codigo'; $field_label = 'Pedido'; ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                        <?php $field_name = 'razao_nome'; $field_label = 'Cliente Nome'; ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                        <?php $field_name = 'cnpj_cpf'; $field_label = 'CPF:'; ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                        <?php $field_name = 'data_nascimento'; $field_label = 'Data de nascimento'; ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control inputmask-date" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                    </div>

                    <!-- Form actions -->
                    <div class="col-md-12">
                        <div class="form-actions">
                            <button type="submit" onclick="$('#formSearch').submit()" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Pesquisar</button>
                        </div>
                        <br>
                    </div>

                </form>
            </div>
        </div>
    </div><!--end .panel -->

</div>
