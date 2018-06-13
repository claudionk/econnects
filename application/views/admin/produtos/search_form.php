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
                        $field_label = 'Produto';
                        ?>
                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <div class="innerB">
                                <input class="form-control" id="filter_<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo app_get_value($field_name)?>" />
                            </div>
                        </div>
                        <?php
                        $field_name = 'produto_ramo_id';
                        $field_label = 'Ramo:';
                        ?>

                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option  value="">Selecione</option>
                                <?php

                                foreach($ramos as $linha) { ?>
                                    <option  value="<?php echo $linha[$field_name] ?>"
                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                        <?php echo $linha['nome']; ?>
                                    </option>
                                <?php }  ?>
                            </select>
                        </div>
                        <?php
                        $field_name = 'slug';
                        $field_label = 'Modelo Venda:';
                        ?>

                        <div class="col-md-2">
                            <h5><?php echo $field_label;?></h5>
                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                <option  value="">Selecione</option>
                                <?php

                                foreach($venda as $linha) { ?>
                                    <option  value="<?php echo $linha[$field_name] ?>"
                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                        <?php echo $linha['nome']; ?>
                                    </option>
                                <?php }  ?>
                            </select>
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
