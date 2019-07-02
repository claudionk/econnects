<div ng-controller="Enquete">

    <!-- Seção do topo -->
    <div class="section-header">
        <div class="row">
            <div class="col-md-6">
                <ul class="actions">
                    <li>
                        <a href="<?php echo base_url("{$controller_url}/dashboard/{$enquete['enquete_id']}")?>" class="btn ink-reaction btn-floating-action btn-sm btn-primary">
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

    <!-- Conteudo -->
    <div class="card">

        <form class="form" id="validateSubmitForm" validate="true" model_validate="<?php echo $model_name ?>" method="post" autocomplete="off" enctype="multipart/form-data">

            <!-- Título -->
            <div class="card-head style-gray-dark">
                <header>Dashboard da Enquete - <?php echo $enquete['nome'] ?></header>
            </div>

            <!-- Conteúdo -->
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12">

                        <?php if (isset($exp)) { ?>
                        <h4>Informações do Segurado</h4>

                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <td>Descrição</td>
                                    <td>Dado</td>
                                </tr>
                            </thead>

                            <tr>
                                <td>Nome</td>
                                <td><?php echo $exp['seg_nome'] ?></td>
                            </tr>

                            <tr>
                                <td>Sexo</td>
                                <td><?php echo $exp['seg_sexo'] == "F" ? "Feminino" : "Masculino" ?></td>
                            </tr>

                            <tr>
                                <td>Data de Abertura</td>
                                <td><?php echo app_date_mysql_to_mask($exp['data_abe']) ?></td>
                            </tr>

                            <tr>
                                <td>Data de Fechamento</td>
                                <td><?php echo app_date_mysql_to_mask($exp['data_fech']) ?></td>
                            </tr>
                        </table>

                        <?php } ?>

                        <h4>Informações da Enquete</h4>


                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <td>Descrição</td>
                                <td>Dado</td>
                            </tr>
                            </thead>

                            <tr>
                                <td>Data da Enquete Enviada</td>
                                <td><?php echo app_date_mysql_to_mask($enquete_resposta['data_enviada']) ?></td>
                            </tr>

                            <tr>
                                <td>Data da Enquete Respondida</td>
                                <td><?php echo app_date_mysql_to_mask($enquete_resposta['data_respondido']) ?></td>
                            </tr>

                            <tr>
                                <td>Respondida?</td>
                                <td><?php echo strtoupper($enquete_resposta['respondido']) ?></td>
                            </tr>
                        </table>



                        <h4>Respostas</h4>

                        <table class="table table-condensed">

                            <thead>
                                <tr>
                                    <td>Pergunta</td>
                                    <td>Respondida?</td>
                                    <td>Resposta</td>
                                </tr>
                            </thead>

                            <?php if($enquete_resposta_pergunta) { ?>

                                <?php foreach($enquete_resposta_pergunta as $rp) {  ?>

                                    <tr>
                                        <td><?php echo $rp['enquete_pergunta_pergunta'] ?></td>
                                        <td><?php echo $rp['respondida'] ? "Sim" : "Não" ?></td>
                                        <td><?php echo $rp['resposta'] ?></td>
                                    </tr>
                                <?php } ?>

                            <?php } ?>

                        </table>
                    </div>
                </div>

            </div>

        </form>
    </div>
</div>


<script>
    var enquete_id = <?php echo $enquete_id ?>;
</script>