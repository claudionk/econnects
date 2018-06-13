<div class="panel-group" id="accordion1">
    <div class="card panel">

        <div class="card-head" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-1" aria-expanded="false">
            <header class="search">Pesquisar</header>
            <div class="tools">
                <a class="btn btn-floating-action btn-default-light"><i class="fa fa-angle-down"></i></a>
            </div>
        </div>

        <div id="accordion1-1" class="collapse in" aria-expanded="false" style="height: 0px;">
            <div class="card-body">

                <form class="form-horizontal margin-none form-cobranca" method="get">

                    <div class="row">



                        <?php $field_name = 'parceiro_id'; $field_label = 'Parceiro:'; ?>
                        <div class="col-md-4">
                            <h5><?php echo $field_label;?></h5>
                            <select class="form-control" name="<?php echo $field_name;?>" id="filter_<?php echo $field_name;?>">
                                <option name="" value="">Selecione</option>
                                <?php

                                foreach($parceiros as $linha) { ?>
                                    <option name="" value="<?php echo $linha[$field_name] ?>"
                                        <?php if(app_get_value($field_name)){if(app_get_value($field_name) == $linha[$field_name]) {echo " selected ";};}; ?> >
                                        <?php echo $linha['nome']; ?>
                                    </option>
                                <?php }  ?>
                            </select>
                        </div>

                        <?php $field_name = 'data_inicio'; $field_label = 'Data Início'; ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control inputmask-date" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                        <?php $field_name = 'data_fim'; $field_label = 'Data Final'; ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control inputmask-date" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                    </div>

                    <!-- Form actions -->
                    <div class="col-md-12">
                        <div class="card-body">
                            <div class="form-actions">
                                <button type="submit" onclick="$('form').submit()" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Pesquisar</button>
                            </div>
                         </div>
                    </div>

                </form>
            </div>
        </div>
    </div><!--end .panel -->

</div>
