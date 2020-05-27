        <!-- DataTables Example -->
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