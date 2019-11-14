<div ng-controller="Enquete">

    <!-- Seção do topo -->
    <div class="section-header">
        <div class="row">
            <div class="col-md-6">
                <ul class="actions">
                    <li>
                        <a href="<?php echo base_url("{$controller_url}/index")?>" class="btn ink-reaction btn-floating-action btn-sm btn-primary">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </li>
                    <li><h2><?php echo $titulo;?></h2></li>
                </ul>

            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>

    <!-- Mensagens -->
    <?php $this->load->view('admin/partials/messages'); ?>


    <div class="panel-group" id="accordion6">

        <div class="card panel">

            <div class="card-head style-gray-bright" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-1" aria-expanded="true">
                <header><i class="fa fa-users"></i> Dashboard da enquete</header>

                <div class="tools">
                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                </div>
            </div>

            <div id="accordion1-1" class="" aria-expanded="false" style="height: 0px;">
                <div class="card-body">

                    <div class="row">

                        <div class="col-md-3">
                            <div class="alert alert-callout alert-success no-margin">
                                <h1 class="pull-right text-default"><i class="md md-timer"></i></h1>
                                <strong class="text-xl"><?php echo $total ?></strong><br>
                                <span>Total de respostas</span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="alert alert-callout alert-success no-margin">
                                <h1 class="pull-right text-success"><i class="md md-check"></i></h1>
                                <strong class="text-xl"><?php echo $respondidos_total ?></strong><br>
                                <span>Completados</span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="alert alert-callout alert-info no-margin">
                                <h1 class="pull-right text-info"><i class="md md-wal"></i></h1>
                                <strong class="text-xl"><?php echo $respondidos_parcial ?></strong><br>
                                <span>Parcialmente respondidos</span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="alert alert-callout alert-danger no-margin">
                                <h1 class="pull-right text-danger"><i class="md md-close"></i></h1>
                                <strong class="text-xl"><?php echo $respondidos_nao ?></strong><br>
                                <span>Não respondidos</span>
                            </div>
                        </div>

                    </div>

                    <div class="row" style="margin-top: 15px;">

                        <div class="col-md-12">
                            <div class="col-md-4" ng-repeat="pergunta in perguntas" id="pergunta_{{$index}}" style="height:350px">

                                <h4>{{pergunta[0].pergunta}}</h4>

                                <div class="grafico" style="width:100%; height: 250px; float:left; margin-left: auto; margin-right:auto;"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card panel">

            <div class="card-head style-primary-dark" data-toggle="collapse" data-parent="#accordion6" data-target="#accordion6-1" aria-expanded="true">
                <header><i class="fa fa-search"></i> Listagem de enviados</header>

                <div class="tools">
                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                </div>
            </div>

            <div id="accordion6-1" class="" aria-expanded="false" style="height: 0px;">
                <div class="card-body">


                    <div class="row">
                        <div class="col-md-12">
                            <a class="btn btn-primary" href="<?php echo admin_url('Relatorio/gerar_enquete/' . $enquete_id) ?>">
                                <i class="fa fa-download"></i> Gerar relatório
                            </a>
                        </div>
                        <br>
                        <br>
                    </div>

                    <div class="row">

                        <div class="col-md-12">

                            <div id="grid_respostas"></div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>






</div>


<script>
    var enquete_id = <?php echo $enquete_id ?>;

    var perguntas = <?php echo json_encode($relacionamento_pergunta_resposta) ?>;
</script>