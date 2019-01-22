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

                <form class="form-horizontal margin-none" id="formSearch" method="get">

                    <div class="row">
                        <?php $field_name = 'pedido_codigo'; $field_label = 'Código do Pedido'; ?>
                        <div class="col-md-3">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                        <?php $field_name = 'razao_nome'; $field_label = 'Nome do Cliente'; ?>
                        <div class="col-md-3">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                        <?php $field_name = 'cnpj_cpf'; $field_label = 'CPF do Cliente'; ?>
                        <div class="col-md-3">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                        <?php $field_name = 'data_nascimento'; $field_label = 'Data de nascimento do Cliente'; ?>
                        <div class="col-md-3">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control inputmask-date" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>

                        <?php $field_name = 'pedido_status_id'; $field_label = 'Status do pedido'; ?>
                        <div class="col-md-3">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <select class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                                <option value="0">Todos</option>
                                <?php foreach($pedido_status_list as $row) { ?>
                                    <option <?php echo $row[$field_name] == issetor($_GET[$field_name]) ? 'selected="selected"' : '' ?> value="<?php echo $row[$field_name] ?>"><?php echo $row['nome'] ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>


                        <?php $field_name = 'fatura_status_id'; $field_label = 'Status de Pagamento'; ?>
                        <div class="col-md-3">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <select class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                                <option value="0">Todos</option>
                                <?php foreach($fatura_status_list as $row) { ?>
                                    <option <?php echo $row[$field_name] == issetor($_GET[$field_name]) ? 'selected="selected"' : '' ?> value="<?php echo $row[$field_name] ?>"><?php echo $row['nome'] ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>


                        <?php $field_name = 'inadimplencia'; $field_label = 'Inadimplentes'; ?>
                        <div class="col-md-3">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <select class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                                <option value="0">Todos</option>
                                <?php foreach($inadimplente_list as $row) { ?>
                                    <option <?php echo $row[$field_name] == issetor($_GET[$field_name]) ? 'selected="selected"' : '' ?> value="<?php echo $row[$field_name] ?>"><?php echo $row['nome'] ?></option>
                                <?php } ?>
                                </select>
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
