        <!-- DataTables Example -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-table"></i>
            Faturamento Gerado</div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="dataTableDetalhe" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>Parceiro</th>
                    <th>ID do Lote</th>
                    <th>Data de Corte</th>
                    <th>Oficial?</th>
                    <th>Data da Execução</th>
                    <th>Download Rel.</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th>Parceiro</th>
                    <th>ID do Lote</th>
                    <th>Data de Corte</th>
                    <th>Oficial?</th>
                    <th>Data da Execução</th>
                    <th>Download Rel.</th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php 
                    $outhtml = '';
                    foreach ($data_invoicing['data_invoicing']['result_invoicing'] as $row) {
                      $outhtml .= '<tr>';
                      $outhtml .= '<td>' . $row['parceiro'] .'</td>';
                      $outhtml .= '<td>' . $row['fatura_parceiro_lote_id'] .'</td>';
                      $outhtml .= '<td>' . $row['data_corte'] .'</td>';
                      $outhtml .= '<td>' . $row['gera_oficial'] .'</td>';
                      $outhtml .= '<td>' . $row['data_processamento'] .'</td>';
                      $outhtml .= '<td>   <a title="Rel. Analítico" href="?c=faturamento_report&dt_corte='.$row['data_corte_report'].'&oficial='.substr($row['gera_oficial'],0,1). '&tipo_rel=GERA_ANALITICO&id_lote='.$row['fatura_parceiro_lote_id'].'"><i class="fas fa-file-download fa-2x" style="color:darkgrey"></i></a>' .
                                        ' <a title="Rel. Resumo" href="?c=faturamento_report&dt_corte='.$row['data_corte_report'].'&oficial='.substr($row['gera_oficial'],0,1). '&tipo_rel=GERA_RESUMO&id_lote='.$row['fatura_parceiro_lote_id'].'"><i class="fas fa-file-download fa-2x" style="color:dimgray"></i></a>' .
                                        ' <a title="Saldo Acumulado" href="?c=faturamento_report&dt_corte='.$row['data_corte_report'].'&oficial='.substr($row['gera_oficial'],0,1). '&tipo_rel=GERA_SALDO_ACUMULADO&id_lote='.$row['fatura_parceiro_lote_id'].'"><i class="fas fa-file-download fa-2x" style="color:darkslategrey"></i></a>' .
                                  '</td>';
                      $outhtml .= '</tr>';
                    }
                    echo $outhtml;
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>