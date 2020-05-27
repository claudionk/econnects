    <?php
        include('./app/view/template/template_topo.php'); 
        $data_run_now = json_decode( $data_run_now, true);  
        $parametro_para_remover = 'c';
        $url_origem = $_SERVER['REQUEST_URI'];
        $url_voltar = preg_replace('~(\?|&)'.$parametro_para_remover.'=[^&]*~', '$1', $url_origem);
    ?>
    
    <div id="content-wrapper">
        &nbsp;
        &nbsp;
        <a href="..<?php echo $url_voltar;?>" class="btn btn-dark"> &lt;&lt; Voltar</a>        
        <h2>&nbsp;&nbsp;Detalhe do Processo em Execução (Últimos 60 Min.)</h2> 
        <div class="container-fluid">
        <!-- tabela -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Dados de Integração</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Parceiro</th>
                      <th>Processo</th>
                      <th>Status</th>
                      <th>Arquivo</th>
                      <th>Início</th>
                      <th>Fim</th>
                      <th>Reg. Proc.</th>
                      <th>Reg. Total</th>
                      <th>ID Int</th>
                      <th>ID Log</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Parceiro</th>
                      <th>Processo</th>
                      <th>Status</th>
                      <th>Arquivo</th>
                      <th>Início</th>
                      <th>Fim</th>
                      <th>Reg. Proc.</th>
                      <th>Reg. Total</th>
                      <th>ID Int</th>
                      <th>ID Log</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <?php 
                      $outhtml = '';
                      foreach ($data_run_now['data_run_now']['result_now'] as $row) {
                        $outhtml .= '<tr>';
                        $outhtml .= '<td>' . $row['parceiro'] .'</td>';
                        $outhtml .= '<td>' . $row['descricao'] .'</td>';
                        $outhtml .= '<td>' . $row['status_atual'] .'</td>';
                        $outhtml .= '<td>' . $row['nome_arquivo'] .'</td>';
                        $outhtml .= '<td>' . $row['processamento_inicio'] .'</td>';
                        $outhtml .= '<td>' . $row['processamento_fim'] .'</td>';
                        $outhtml .= '<td>' . $row['quantidade_processado'] .' ('. $row['percentual'] .')</td>';
                        $outhtml .= '<td>' . $row['quantidade_registros'] .'</td>';
                        $outhtml .= '<td>' . $row['integracao_id'] .'</td>';
                        $outhtml .= '<td>' . $row['integracao_log_id'] .'</td>';
                        $outhtml .= '</tr>';
                      }
                      echo $outhtml;
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>        
        <!-- tabela -->

        </div>
    </div>

    <?php
        include('./app/view/template/template_base.php'); 
    ?>    