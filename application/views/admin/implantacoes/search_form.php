<div class="panel-group" id="accordion1">
    <div class="card panel">
        <div class="card-head" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-1" aria-expanded="false">
            <header class="search">Pesquisar</header>
            <div class="tools">
                <a class="btn btn-floating-action btn-default-light"><i class="fa fa-angle-down"></i></a>
            </div>
        </div>
        <div id="accordion1-1" class="collapse <?php if($_GET) echo 'in'?>" aria-expanded="false" >
            <div class="card-body">
                <form class="form-horizontal margin-none" id="validateSubmitForm" method="get" autocomplete="off">

                    <div class="row">
                        <?php
                        $field_name = 'nome_fantasia';
                        $field_label = 'Parceiro';
                        ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value(array('filter' => $field_name))?>" />
                            </div>
                        </div>
                        <?php
                        $field_name = 'produto';
                        $field_label = 'Produto:';
                        ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value(array('filter' => $field_name))?>" />
                            </div>
                        </div>
                        <?php
                        $field_name = 'implantacao_status_id';
                        $field_label = 'Status:';
                        ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <select class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value($field_name)?>" />
                                <option value="0">Todos</option>
                                <?php foreach($implantacao_status as $row) { ?>
                                    <option <?php echo $row[$field_name] == issetor($_GET[$field_name]) ? 'selected="selected"' : '' ?> value="<?php echo $row[$field_name] ?>"><?php echo $row['nome'] ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-2">
                            <h5>&nbsp;</h5>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-sm btn-primary" onclick="$('#validateSubmitForm').submit()"><i class="fa fa-search"></i> Pesquisar</button>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div><!--end .panel -->

</div>
