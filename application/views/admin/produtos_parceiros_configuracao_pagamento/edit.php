<?php
if($_POST)
    $row = $_POST;
?>
<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <!-- col-separator.box -->
            <!-- col-separator.box -->
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>

            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">

                    <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                    <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                        <i class="fa fa-edit"></i> Salvar
                    </a>
                </div>

            </div>

            <div class="col-separator col-unscrollable bg-none box col-separator-first">

                <!-- col-table -->
                <div class="col-table">

                    <!-- col-table-row -->
                    <div class="col-table-row">

                        <!-- col-app -->
                        <div class="col-app col-unscrollable">

                            <!-- col-app -->
                            <div class="col-app">

                                <!-- Form -->
                                <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">
                                    <input type="hidden" name="<?php echo $primary_key ?>" value="<?php if (isset($row[$primary_key])) echo $row[$primary_key]; ?>"/>
                                    <input type="hidden" name="new_record" value="<?php echo $new_record; ?>"/>
                                    <input type="hidden" name="produto_parceiro_id" value="<?php echo $produto_parceiro_id; ?>"/>
                                    <!-- Widget -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php $this->load->view('admin/partials/validation_errors');?>
                                                <?php $this->load->view('admin/partials/messages'); ?>
                                            </div>

                                        </div>

                                    <div class="card">


                                        <?php  $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>

                                        <div class="card-body tab-content">

                                            <!-- Row -->
                                            <div class="row innerLR">

                                                <div class="relativeWrap">
                                                    <div class="widget widget-tabs widget-tabs-double-2 widget-tabs-responsive">

                                                        <!-- Tabs Heading -->

                                                         <!-- // Tabs Heading END -->

                                                        <div class="widget-body">
                                                            <div class="tab-content">
                                                                <div class="col-md-12">
                                                                    <div class="card tabs-left style-default-light">
                                                                         <!-- Tab content -->
                                                                        <?php  $this->load->view('admin/produtos_parceiros_configuracao/sub_tab_financeiro');?>
                                                                         <div class="card-body tab-content style-default-bright">
                                                                            <div id="tabGeral" class="tab-pane active widget-body-regular">



                                                                    <?php $field_name = 'venda_multiplo_cartao';?>
                                                                    <!--
                                                                    <div class="radio radio-styled">
                                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Venda com multiplos cartões *</label>
                                                                        <label class="radio-inline radio-styled radio-primary">
                                                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                                   value="1" <?php if (isset($row[$field_name]) && $row[$field_name] == '1') echo 'checked="checked"'; ?> />
                                                                            Sim
                                                                        </label>
                                                                        <label class="radio-inline radio-styled radio-primary">
                                                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                                   value="0" <?php if (isset($row[$field_name]) && $row[$field_name] == '0') echo 'checked="checked"'; ?> />
                                                                            Não
                                                                        </label>
                                                                    </div> -->

                                                                     <?php $field_name = 'pagamento_tipo';?>
                                                                    <div class="radio radio-styled">
                                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Tipo de cobrança *</label>
                                                                        <label class="radio-inline radio-styled radio-primary">
                                                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                                   value="UNICO" <?php if (isset($row[$field_name]) && $row[$field_name] == 'UNICO') echo 'checked="checked"'; ?> />
                                                                            <span>Único </span>
                                                                        </label>
                                                                        <label class="radio-inline radio-styled radio-primary">
                                                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                                   value="RECORRENTE" <?php if (isset($row[$field_name]) && $row[$field_name] == 'RECORRENTE') echo 'checked="checked"'; ?> />
                                                                            <span>Recorrente</span>
                                                                        </label>
                                                                    </div>

                                                                    <?php $field_name = 'pagamento_periodicidade';?>
                                                                    <div class="form-group tipo-pagamento-filho">
                                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Periodicidade *</label>
                                                                        <div class="col-md-2"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                        <?php $field_name = 'pagamento_periodicidade_unidade';?>
                                                                        <div class="col-md-2">
                                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                                <option name="" value="">Selecione</option>
                                                                                <option name="" value="DIA"
                                                                                    <?php if(isset($row[$field_name])){if($row[$field_name] == 'DIA') {echo " selected ";};}; ?> >Dia
                                                                                </option>
                                                                                <option name="" value="MES"
                                                                                    <?php if(isset($row[$field_name])){if($row[$field_name] == 'MES') {echo " selected ";};}; ?> >Mês
                                                                                </option>
                                                                                <option name="" value="ANO"
                                                                                    <?php if(isset($row[$field_name])){if($row[$field_name] == 'ANO') {echo " selected ";};}; ?> >Ano
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    <?php $field_name = 'pagmaneto_cobranca';?>
                                                                    <div class="radio radio-styled tipo-pagamento-filho">
                                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Dia da cobrança *</label>
                                                                        <label class="radio-inline radio-styled radio-primary">
                                                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                                   value="VENCIMENTO_CARTAO" <?php if (isset($row[$field_name]) && $row[$field_name] == 'VENCIMENTO_CARTAO') echo 'checked="checked"'; ?> />
                                                                            <span>Data de vencimento do cartão </span>
                                                                        </label>
                                                                        <label class="radio-inline radio-styled radio-primary">
                                                                            <input type="radio" id="radio1" name="<?php echo $field_name; ?>" class="required styled"
                                                                                   value="DATA_COMPRA" <?php if (isset($row[$field_name]) && $row[$field_name] == 'DATA_COMPRA') echo 'checked="checked"'; ?> />
                                                                            <span>Data da compra</span>
                                                                        </label>
                                                                    </div>
                                                                    <?php $field_name = 'pagmaneto_cobranca_dia';?>
                                                                    <div class="form-group tipo-pagamento-filho tipo-pagamento-filho-two">
                                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Dias após o vencimento *</label>
                                                                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                    <?php $field_name = 'pagamento_teimosinha';?>
                                                                    <div class="form-group tipo-pagamento-filho tipo-pagamento-filho-two">
                                                                        <label class="col-md-4 control-label" for="<?php echo $field_name;?>">Teimosinha *</label>
                                                                        <div class="col-md-4"><input class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                </div>
                                                                         </div>

                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <!-- // Row END -->
                                            
                                        </div>
                                    </div>
                                    <!-- // Widget END -->
                                    <!-- Widget heading -->
                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">

                                            <a href="<?php echo base_url("admin/produtos_parceiros/view_by_parceiro/{$produto_parceiro['parceiro_id']}")?>" class="btn  btn-app btn-primary">
                                                <i class="fa fa-arrow-left"></i> Voltar
                                            </a>
                                            <a class="btn  btn-app btn-primary" onclick="$('#validateSubmitForm').submit();">
                                                <i class="fa fa-edit"></i> Salvar
                                            </a>
                                        </div>

                                    </div>
                                </form>
                                <!-- // Form END -->

                            </div>
                            <!-- // END col-app -->

                        </div>
                        <!-- // END col-app.col-unscrollable -->

                    </div>
                    <!-- // END col-table-row -->

                </div>
                <!-- // END col-table -->

            </div>

            <!-- // END col-separator.box -->
        </div>
    </div>
</div>