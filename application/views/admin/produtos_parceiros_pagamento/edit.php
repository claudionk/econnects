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
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>

            </div>

            <div class="card">

                <!-- Widget heading -->
                <div class="card-body">
                    <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
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
                                    <input type="hidden" name="produto_parceiro_id" value="<?php echo $produto_parceiro_id; ?>"/>
                                    <!-- Widget -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php $this->load->view('admin/partials/validation_errors');?>
                                            <?php $this->load->view('admin/partials/messages'); ?>
                                        </div>

                                    </div>
                                    <div class="card">

                                        <!-- // Widget heading END -->
                                        <!-- Tabs Heading -->
                                        <?php $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>
                                        <!-- // Tabs Heading END -->

                                        <div class="card-body">

                                            <!-- Row -->
                                            <div class="row innerLR">

                                                <div class="relativeWrap">
                                                    <div class="widget widget-tabs widget-tabs-double-2 widget-tabs-responsive">


                                                        <div class="widget-body">
                                                            <div class="tab-content">


                                                                <!-- Tab content -->
                                                                <div id="tabPagamento" class="tab-pane active widget-body-regular">


                                                                    <?php $field_name = 'forma_pagamento_id';?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Forma de pagamento</label>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                                                                <option  value="">Selecione</option>
                                                                                <?php

                                                                                foreach($forma_pagamento as $linha) { ?>
                                                                                    <option  value="<?php echo $linha[$field_name] ?>"
                                                                                        <?php if(isset($row)){if($row[$field_name] == $linha[$field_name]) {echo " selected ";};}; ?> >
                                                                                        <?php echo $linha['nome']; ?>
                                                                                    </option>
                                                                                <?php }  ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <?php $field_name = 'codigo_operadora';?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Código Operadora</label>
                                                                        <div class="col-md-6"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                    <?php $field_name = 'nome_fatura';?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Nome da Fatura</label>
                                                                        <div class="col-md-6"><input maxlength="13" onkeypress="return onlyAlphabets(event,this);" class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                    <?php $field_name = 'parcelamento_maximo';?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Parcelamento máximo</label>
                                                                        <div class="col-md-6"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>
                                                                    <?php $field_name = 'parcelamento_maximo_sem_juros';?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Parcelamento máximo sem juros</label>
                                                                        <div class="col-md-6"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>

                                                                    <?php $field_name = 'juros_parcela';?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Juros Parcela</label>
                                                                        <div class="col-md-6"><input class="form-control inputmask-moeda" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                                                    </div>


                                                                    <?php $field_name = 'ativo';?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Ativo *</label>
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
                                    <div class="card boleto">

                                        <div class="card-head">
                                            <header>CONFIGURAÇÕES BOLETO</header>
                                        </div>

                                        <div class="card-body">
                                            <?php $field_name = 'boleto_banco';?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Código Banco</label>
                                                <div class="col-md-6"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                            </div>
                                            <?php $field_name = 'boleto_cedente_endereco';?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Cedente Endereço</label>
                                                <div class="col-md-6"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                            </div>
                                            <?php $field_name = 'boleto_cedente_nome';?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Cedente Nome</label>
                                                <div class="col-md-6"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                            </div>
                                            <?php $field_name = 'boleto_cedente_cnpj';?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Cedente CNPJ</label>
                                                <div class="col-md-6"><input class="form-control inputmask-cnpj" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                            </div>
                                            <?php $field_name = 'boleto_instrucoes';?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Instruções</label>
                                                <div class="col-md-6"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                            </div>
                                            <?php $field_name = 'boleto_nosso_numero';?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Nosso Número (Sequencia)</label>
                                                <div class="col-md-6"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                            </div>
                                            <?php $field_name = 'boleto_vencimento';?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Vencimento (Dias úteis)</label>
                                                <div class="col-md-6"><input class="form-control" id="<?php echo $field_name;?>" name="<?php echo $field_name;?>" type="text" value="<?php echo isset($row[$field_name]) ? $row[$field_name] : set_value($field_name); ?>" /></div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card">

                                        <!-- Widget heading -->
                                        <div class="card-body">
                                            <a href="<?php echo base_url("{$current_controller_uri}/index/{$produto_parceiro_id}")?>" class="btn  btn-app btn-primary">
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
<script language="Javascript" type="text/javascript">
  function onlyAlphabets(e, t) {
    try {
      if (window.event) {
        var charCode = window.event.keyCode;
      }
      else if (e) {
        var charCode = e.which;
      }
      else { return true; }
      if( (charCode > 47 && charCode < 59) || (charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123))
        return true;
      else
        return false;
    }

    catch (err) {
      alert(err.Description);
    }
  }
</script>


