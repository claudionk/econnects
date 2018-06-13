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
                <form class="form-horizontal margin-none" id="validateSubmitForm" method="get" autocomplete="off">

                    <div class="row">
                        <?php
                        $field_name = 'nome';
                        $field_label = 'Razão Social';
                        ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value(array('filter' => $field_name))?>" />
                            </div>
                        </div>
                        <?php
                        $field_name = 'nome_fantasia';
                        $field_label = 'Nome fantasia:';
                        ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value(array('filter' => $field_name))?>" />
                            </div>
                        </div>
                        <?php
                        $field_name = 'apelido';
                        $field_label = 'Apelido:';
                        ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value(array('filter' => $field_name))?>" />
                            </div>
                        </div>

                        <?php
                        $field_name = 'cnpj';
                        $field_label = 'CNPJ';
                        ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value(array('filter' => $field_name))?>" />
                            </div>
                        </div>
                        <?php
                        $field_name = 'codigo_susep';
                        $field_label = 'Código Susep';
                        ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value(array('filter' => $field_name))?>" />
                            </div>
                        </div>



                    </div>

                    <div class="col-md-12">
                    <!-- Form actions -->
                        <br />
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                        </div>
                        <br />
                    </div>


                </form>
            </div>
        </div>
    </div><!--end .panel -->

</div>
