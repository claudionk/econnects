    <?php
        include('./app/view/template/template_topo.php'); 
        $data = json_decode( $data, true);    
        $parametro_para_remover = 'c';
        $url_origem = $_SERVER['REQUEST_URI'];
        $url_voltar = preg_replace('~(\?|&)'.$parametro_para_remover.'=[^&]*~', '$1', $url_origem)    
    ?>
    
    <div id="content-wrapper">
        &nbsp;
        &nbsp;
        <a href="..<?php echo $url_voltar;?>" class="btn btn-dark"> &lt;&lt; Voltar</a>        
        <h2>&nbsp;&nbsp;Detalhe do Processo</h2> 
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
                      <th>Id</th>
                      <th>Dt. Criação</th>
                      <th>Tipo</th>
                      <th>Status</th>
                      <th>Qt. Log</th>
                      <th>Opções</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Id</th>
                      <th>Dt. Criação</th>
                      <th>Tipo</th>
                      <th>Status</th>
                      <th>Qt. Log</th>
                      <th>Opções</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <?php 
                      $outhtml = '';
                      foreach ($data['data']['result'] as $row) {
                        $outhtml .= '<tr>';
                        $outhtml .= '<td>' . $row['id'] .'</td>';
                        $outhtml .= '<td>' . $row['dt_criacao'] .'</td>';
                        $outhtml .= '<td>' . $row['tipo'] .'</td>';
                        $outhtml .= '<td>' . $row['status'] .'</td>';
                        $outhtml .= '<td>' . $row['qtde_log'] .'</td>';
                        $outhtml .= '<td>' . 'X | + | ...' .'</td>';
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