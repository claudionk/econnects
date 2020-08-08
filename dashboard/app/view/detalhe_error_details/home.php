    <?php
        include('./app/view/template/template_topo.php'); 
        $data_error_det = json_decode( $data_error_det, true);  
    ?>
    
    <div id="content-wrapper">        
        &nbsp;
        &nbsp;
        <button class="btn btn-danger" onclick="self.close()">Fechar</button>
        <h2>&nbsp;&nbsp;Detalhe do Processo Executado com Erro por Mensagem</h2> 
        <div class="container-fluid">
        <!-- tabela -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Dados de Integração</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTableDetalhe" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Nome do Arquivo</th>
                      <th>Status</th>
                      <th>Qtde</th>
                      <th>Tot. Arq.</th>
                      <th>Mensagem</th>
                      <th>Apólices</th>
                      <th>Data da Execução</th>
                      <th>ID Log</th>
                      <th>ID Int.</th>
                      <th>Parceiro</th>
                      <th>Processo</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Nome do Arquivo</th>
                      <th>Status</th>
                      <th>Qtde</th>
                      <th>Tot. Arq.</th>
                      <th>Mensagem</th>
                      <th>Apólices</th>
                      <th>Data da Execução</th>
                      <th>ID Int.</th>
                      <th>ID Log</th>
                      <th>Parceiro</th>
                      <th>Processo</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <?php 
                      $outhtml = '';
                      $bg_color = 'rgba(0, 0, 0, 0.03)';
                      $num_lin = 0;
                      foreach ($data_error_det['data_error_det']['result_error_det'] as $row) {
                        $num_lin ++;
                        if ($num_lin % 2 == 0){
                          $bg_color = 'white';
                        }else{
                          $bg_color = 'rgba(0, 0, 0, 0.03)';
                        }

                        $outhtml .= '<tr style="background-color: '.$bg_color.';">';
                        $outhtml .= '<td>' . $row['nome_arquivo'] .'</td>';
                        $outhtml .= '<td>' . $row['status_processo'] .'</td>';
                        $outhtml .= '<td>' . $row['qtde'] .'</td>';
                        $outhtml .= '<td>' . $row['qtd_total_reg'] .'</td>';
                        $outhtml .= '<td>' . $row['mensagem'] .'</td>';
                        $outhtml .= '<td>'  .$row['apolices'].'</td>';
                        $outhtml .= '<td>' . $row['processamento_inicio'] .'</td>';
                        $outhtml .= '<td>' . $row['integracao_id'] .'</td>';
                        $outhtml .= '<td>' . $row['integracao_log_id'] .'</td>';
                        $outhtml .= '<td>' . $row['parceiro'] .'</td>';
                        $outhtml .= '<td>' . $row['descricao'] .'</td>';
                        // $outhtml .= '</tr>';
                        // $outhtml .= '<tr style="background-color:'.$bg_color.';">';
                        // $outhtml .= '<td colspan="1"> Apólice: </td>';
                        // $outhtml .= '<td colspan="9">';
                        // $outhtml .= '<p>'.$row['apolices'].'</p>';
                        // $outhtml .= '</td>';
                        // $outhtml .= '</tr>';
                      }
                      echo $outhtml;
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>        
        <!-- tabela -->
        &nbsp;
        &nbsp;
        <button class="btn btn-danger" onclick="self.close()">Fechar</button>
        </div>
    </div>

    <?php
        include('./app/view/template/template_base.php'); 
    ?>    