

    <div class="section-header">
        <ol class="breadcrumb">
            <li class="active"><?php echo app_recurso_nome();?></li>
            <li class="active"><?php echo $page_subtitle;?></li>
        </ol>
    </div>

    <div class="card">
        <div class="card-head">
            <header>Total de mensagens enviadas por engine</header>
        </div>

        <!-- Widget heading -->
        <div class="card-body">

            <div id="comunicacao_por_engine">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <header>Mensagens enviadas por parceiro</header>
        </div>

        <!-- Widget heading -->
        <div class="card-body">

            <div id="comunicacao_por_parceiro">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <header>Mensagens enviadas por dia (Referente a <?php echo date('m/Y') ?>)</header>
        </div>
        
        <!-- Widget heading -->
        <div class="card-body">

            <div id="comunicacao_por_dia">
            </div>
        </div>
    </div>





