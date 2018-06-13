<div class="panel-group" id="accordion1">
    <div class="card panel">
        <div class="card-head" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-1" aria-expanded="false">
            <header class="search">Pesquisar</header>
            <div class="tools">
                <a class="btn btn-primary btn-rounded"><i class="fa fa-angle-down"></i></a>
            </div>
        </div>
        <div id="accordion1-1" class="collapse <?php if($_GET) echo 'in'?>" aria-expanded="false" style="height: 0px;">
            <div class="card-body">
                <form class="form-horizontal margin-none" id="validateSubmitForm" method="get" autocomplete="off">

                    <div class="row">
                        <?php
                        $field_name = 'pergunta';
                        $field_label = 'Pergunta';
                        ?>
                        <div class="col-md-3">
                            <div class="form-group">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value(array('filter' => $field_name))?>" />
                            </div>
                            </div>
                        </div>

                        <?php
                        $field_name = 'objetivo';
                        $field_label = 'Objetivo';
                        ?>
                        <div class="col-md-3">
                            <div class="form-group">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="filter[<?php echo $field_name;?>]" type="text" value="<?php echo app_get_value(array('filter' => $field_name))?>" />
                            </div>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                                    <i class="fa fa-edit"></i> Pesquisar
                                </a>
                            </div>

                        </div>


                    </div>

                </form>

            </div>
        </div>
    </div><!--end .panel -->

</div>