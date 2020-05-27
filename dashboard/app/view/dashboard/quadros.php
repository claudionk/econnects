      <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="#">Dashboard</a>
          </li>
          <li class="breadcrumb-item active">Resumo</li>
        </ol>

        <!-- Icon Cards-->
        <div class="row">
          <!-- Com Lock -->
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-danger o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-life-ring"></i>
                </div>
                <div class="mr-5"><h4><?php echo $data_lock['data_lock']['resume_lock']['qtde_lock']; ?> Com Lock</h4></div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="?c=detalhe_lock<?php echo '&' . $url_param;?>">
                <span class="float-left"><h6>Ver Detalhes</h6></span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
          <!-- Com Log de Erro -->
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-warning o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-comments"></i>
                </div>
                <div class="mr-5"><h4><?php echo $data_error['data_error']['resume_error']['qtde_error']; ?> Com Log Erro</h4></div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="?c=detalhe_error<?php echo '&' . $url_param;?>">
                <span class="float-left"><h6>Ver Detalhes</h6></span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
          <!-- Em Execução -->
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-primary o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-list"></i>
                </div>
                <div class="mr-5"><h4><?php echo $data_run_now['data_run_now']['resume_now']['qtde_now']; ?> Em Execução (Útimos 60 min.)</h4></div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="?c=detalhe_run_now<?php echo '&' . $url_param;?>">
                <span class="float-left"><h6>Ver Detalhes</h6></span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
          <!-- Com Sucesso -->
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-success o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-shopping-cart"></i>
                </div>
                <div class="mr-5"><h4><?php echo $data_successful['data_successful']['resume_successful']['qtde_successful']; ?> Com Sucesso</h4></div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="?c=detalhe_successful<?php echo '&' . $url_param;?>">
                <span class="float-left"><h6>Ver Detalhes</h6></span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- Não executado (+ de 1 dia) -->
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-info o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-comments"></i>
                </div>
                <div class="mr-5"><h4><?php echo $data_not_exec['data_not_exec']['resume_not_exec']['qtde_not_exec']; ?>  Não executado </br>(+ de 1 dia)</h4></div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="?c=detalhe_not_exec<?php echo '&' . $url_param;?>">
                <span class="float-left"><h6>Ver Detalhes</h6></span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
        </div>

        <!-- Area Chart Example-->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-chart-area"></i>
           Itens processados</div>
          <div class="card-body">
            <canvas id="myAreaChart" width="100%" height="30"></canvas>
          </div>
        </div>
        
        <?php
            include('dados_integracao.php'); 
        ?>
      </div>
      <!-- /.container-fluid -->