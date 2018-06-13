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
                            $field_name = 'codigo';
                            $field_label = 'Código';
                            ?>
                            <div class="col-md-3">
                                <h5><?php echo $field_label;?></h5>
                                <div class="innerB">
                                    <input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo $this->input->get_post($field_name); ?>" />
                                </div>
                            </div>
                            <?php
                            $field_name = 'tipo_cliente';
                            $field_label = 'Tipo';
                            $field_get = $this->input->get_post($field_name);
                            ?>
                            <div class="col-md-3">
                                <h5><?php echo $field_label;?></h5>
                                <div class="innerB">
                                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                        <option name="" value="">Selecione</option>
                                        <option name="" value="CO" <?php if($field_get == 'CO') { echo 'selected' ;}?>>CO</option>
                                        <option name="" value="CF" <?php if($field_get == 'CF') { echo 'selected' ;}?>>CF</option>
                                    </select>
                                </div>
                            </div>



                            <?php
                            $field_name = 'cnpj_cpf';
                            $field_label = 'CNPJ/CPF';
                            ?>
                            <div class="col-md-3">
                                <h5><?php echo $field_label;?></h5>
                                <div class="innerB">
                                    <input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo $this->input->get_post($field_name); ?>" />
                                </div>
                            </div>
                            <?php
                            $field_name = 'nome_fantasia';
                            $field_label = 'Nome Fantasia';
                            ?>
                            <div class="col-md-3">
                                <h5><?php echo $field_label;?></h5>
                                <div class="innerB">
                                    <input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo $this->input->get_post($field_name); ?>" />
                                </div>
                            </div>


                            <?php
                            $field_name = 'razao_nome';
                            $field_label = 'Razão social / Nome';
                            ?>
                            <div class="col-md-3">
                                <h5><?php echo $field_label;?></h5>
                                <div class="innerB">
                                    <input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo $this->input->get_post($field_name); ?>" />
                                </div>
                            </div>

                            <?php
                            $field_name = 'cliente_evolucao_status_id';
                            $field_label = 'Status';
                            $field_get = $this->input->get_post($field_name);
                            ?>
                            <div class="col-md-3">
                                <h5><?php echo $field_label;?></h5>
                                <div class="innerB">
                                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                        <option name="" value="">Selecione</option>
                                        <?php foreach($evolucao_status as $linha) { ?>
                                            <option name="" value="<?php echo $linha[$field_name] ?>"
                                                <?php if(isset($field_get)){if($field_get == (int)$linha[$field_name]) {echo " selected ";};}; ?> >
                                                <?php echo $linha['descricao']; ?>
                                            </option>
                                        <?php }  ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                            $field_name = 'cliente_grupo_empresarial_id';
                            $field_label = 'Grupo empresarial';
                            $field_get = $this->input->get_post($field_name);
                            ?>
                            <div class="col-md-3">
                                <h5><?php echo $field_label;?></h5>
                                <div class="innerB">
                                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                        <option name="" value="">Selecione</option>
                                        <?php foreach($grupos_empresariais as $linha) { ?>
                                            <option name="" value="<?php echo $linha[$field_name] ?>"
                                                <?php if(isset($field_get)){if($field_get == (int)$linha[$field_name]) {echo " selected ";};}; ?> >
                                                <?php echo $linha['nome']; ?>
                                            </option>
                                        <?php }  ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                            $field_name = 'colaborador_comercial_id';
                            $field_label = 'Comercial responsável';
                            $field_get = $this->input->get_post($field_name);
                            ?>
                            <div class="col-md-3">
                                <h5><?php echo $field_label;?></h5>
                                <div class="innerB">
                                    <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                        <option name="" value="">Selecione</option>
                                        <?php foreach($colaboradores as $linha) { ?>
                                            <option name="" value="<?php echo $linha['colaborador_id'] ?>"
                                                <?php if(isset($field_get)){if($field_get == (int)$linha['colaborador_id']) {echo " selected ";};}; ?> >
                                                <?php echo $linha['nome']; ?>
                                            </option>
                                        <?php }  ?>
                                    </select>
                                </div>
                            </div>


                        </div>
                        <div class="col-md-12">
                            <BR/>
                        <div class="separator"></div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Pesquisar</button>
                        </div>
                            <BR/>
                        </div>


                </form>

            </div>
        </div>
    </div><!--end .panel -->

</div>

<!--

-->