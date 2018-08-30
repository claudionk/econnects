<?php
if($_POST)
    $row = $_POST;
?>
<div class="layout-app" xmlns="http://www.w3.org/1999/html">
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


                                        <?php  $this->load->view('admin/produtos_parceiros_configuracao/tab_configuracao');?>

                                        <div class="card-body tab-content">

                                            <!-- Row -->
                                            <div class="row innerLR">

                                                <div class="relativeWrap">
                                                    <div class="widget widget-tabs widget-tabs-double-2 widget-tabs-responsive">

                                                        <!-- Tabs Heading -->

                                                         <!-- // Tabs Heading END -->

                                                        <section>
                                                            <div class="section-body">
                                                                <div class="">
                                                                    <!-- BEGIN FIXED TIMELINE -->
                                                                    <ul class="timeline collapse-lg timeline-hairline">
                                                                        <li class="timeline-inverted">
                                                                            <div class="timeline-circ circ-xl style-primary"><span class="glyphicon glyphicon-leaf"></span></div>
                                                                            <div class="timeline-entry">
                                                                                <div class="card style-default-bright">
                                                                                    <div class="card-body small-padding">
                                                                                        <span class="text-xl text-primary">Regras de Negócios</span><br>
                                                                                        <ul class="timeline collapse-lg timeline-hairline">
                                                                                            <li class="timeline-inverted">
                                                                                                <div class="timeline-circ style-primary"></div>
                                                                                                <div class="timeline-entry">
                                                                                                    <div class="card style-default-bright">
                                                                                                        <div class="card-body small-padding">
                                                                                                            <span class="text-lg text-primary">Comissões</span><br>
                                                                                                            <?php $this->load->view('admin/produtos_parceiros_resumo/comissao', array('row' => $row));?>

                                                                                                        </div><!--end .card-body -->
                                                                                                    </div><!--end .card -->
                                                                                                </div><!--end .timeline-entry -->
                                                                                            </li>
                                                                                            <li class="timeline-inverted">
                                                                                                <div class="timeline-circ style-primary"></div>
                                                                                                <div class="timeline-entry">
                                                                                                    <div class="card style-default-bright">
                                                                                                        <div class="card-body small-padding">
                                                                                                            <span class="text-lg text-primary">Acréscimo no Prêmio</span><br>
                                                                                                            <?php
                                                                                                            $this->load->view('admin/produtos_parceiros_resumo/regra_preco', array('row' => $row));?>

                                                                                                        </div><!--end .card-body -->
                                                                                                    </div><!--end .card -->
                                                                                                </div><!--end .timeline-entry -->
                                                                                            </li>
                                                                                            <li class="timeline-inverted">
                                                                                                <div class="timeline-circ style-primary"></div>
                                                                                                <div class="timeline-entry">
                                                                                                    <div class="card style-default-bright">
                                                                                                        <div class="card-body small-padding">
                                                                                                            <span class="text-lg text-primary">Desconto</span><br>
                                                                                                            <?php
                                                                                                            $this->load->view('admin/produtos_parceiros_resumo/desconto_comissao', array('row' => $row));?>
                                                                                                            
                                                                                                        </div><!--end .card-body -->
                                                                                                    </div><!--end .card -->
                                                                                                </div><!--end .timeline-entry -->
                                                                                            </li>
                                                                                            <li class="timeline-inverted">
                                                                                                <div class="timeline-circ style-primary"></div>
                                                                                                <div class="timeline-entry">
                                                                                                    <div class="card style-default-bright">
                                                                                                        <div class="card-body small-padding">
                                                                                                            <span
                                                                                                                class="text-lg text-primary">Cancelamento</span><br>
                                                                                                            <?php $this->load->view('admin/produtos_parceiros_resumo/cancelamento', array('row' => $row));?>
                                                                                                        </div><!--end .card-body -->
                                                                                                    </div><!--end .card -->
                                                                                                </div><!--end .timeline-entry -->
                                                                                            </li>
                                                                                            <li class="timeline-inverted">
                                                                                                <div class="timeline-circ style-primary"></div>
                                                                                                <div class="timeline-entry">
                                                                                                    <div class="card style-default-bright">
                                                                                                        <div class="card-body small-padding">
                                                                                                            <span class="text-lg text-primary">Geral</span><br>
                                                                                                            <?php $this->load->view('admin/produtos_parceiros_resumo/geral', array('row' => $row));?>
                                                                                                        </div><!--end .card-body -->
                                                                                                    </div><!--end .card -->
                                                                                                </div><!--end .timeline-entry -->
                                                                                            </li>
                                                                                        </ul>



                                                                                    </div><!--end .card-body -->
                                                                                </div><!--end .card -->
                                                                            </div><!--end .timeline-entry -->
                                                                        </li>
                                                                    </ul>
                                                                    <!-- END FIXED TIMELINE -->

                                                                </div><!--end .container -->
                                                            </div><!--end .section-body -->
                                                        </section>

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
