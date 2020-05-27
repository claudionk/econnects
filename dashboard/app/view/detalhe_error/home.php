    <?php
        include('./app/view/template/template_topo.php'); 
        $data_error = json_decode( $data_error, true);  
        $parametro_para_remover = 'c';
        $url_origem = $_SERVER['REQUEST_URI'];
        $url_voltar = preg_replace('~(\?|&)'.$parametro_para_remover.'=[^&]*~', '$1', $url_origem);
    ?>
    
    <div id="content-wrapper">
        &nbsp;
        &nbsp;
        <a href="..<?php echo $url_voltar;?>" class="btn btn-dark"> &lt;&lt; Voltar</a>        
        <h2>&nbsp;&nbsp;Detalhe do Processo Executado com Erro</h2> 
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
                      <th>Nome do Arquivo</th>
                      <th>Status</th>
                      <th>Data da Execução</th>
                      <th>ID da Integração</th>
                      <th>Processos</th>
                      <th>Arquivos</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Parceiro</th>
                      <th>Processo</th>
                      <th>Nome do Arquivo</th>
                      <th>Status</th>
                      <th>Data da Execução</th>
                      <th>ID da Integração</th>
                      <th>Processos</th>
                      <th>Arquivos</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <?php 
                      $outhtml = '';
                      foreach ($data_error['data_error']['result_error'] as $row) {
                        $caminho_prd_arq_1 = (@fopen($row['caminho_prd_arq_1'],'r')) ? ' <a title="ServPrd40" href="'.$row['caminho_prd_arq_1'].'" target="_blank"><i class="fas fa-file-alt fa-2x" style="color:green"></i></a>' : '<i class="fas fa-file-alt fa-2x" style="color:darkgrey"></i>' ;
                        //$caminho_prd_arq_2 = (@fopen($row['caminho_prd_arq_2'],'r')) ? ' <a title="ServPrd41" href="'.$row['caminho_prd_arq_2'].'" target="_blank"><i class="fas fa-file-alt fa-2x" style="color:green"></i></a>' : '<i class="fas fa-file-alt fa-2x" style="color:darkgrey"></i>' ;
                        $caminho_prd_arq_3 = (@fopen($row['caminho_prd_arq_3'],'r')) ? ' <a title="ServPrd42" href="'.$row['caminho_prd_arq_3'].'" target="_blank"><i class="fas fa-file-alt fa-2x" style="color:green"></i></a>' : '<i class="fas fa-file-alt fa-2x" style="color:darkgrey"></i>' ;
                        $outhtml .= '<tr>';
                        $outhtml .= '<td>' . $row['parceiro'] .'</td>';
                        $outhtml .= '<td>' . $row['descricao'] .'</td>';
                        $outhtml .= '<td>' . $row['nome_arquivo'] .'</td>';
                        $outhtml .= '<td>' . $row['status_processo'] .'</td>';
                        $outhtml .= '<td>' . $row['processamento_inicio'] .'</td>';
                        $outhtml .= '<td>' . $row['integracao_id'] .'</td>';
                        $outhtml .= '<td> <a title="Ver detalhes" href="?c=detalhe_error_details&ambiente=' . $ambiente . '&data_inicio=' . $row['proces_inicio_orig']. '&file_name=' .$row['nome_arquivo'].'" target="_blank"><i class="fas fa-list fa-2x" style="color:lightslategray"></i></a>' . 
                                        ' <a title="Reprocessar" href="#"><i class="fas fa-sync-alt fa-2x" style="color:orange"></i></a>' . 
                                    '</td>';
                        if ($ambiente == 'PRODUCAO'){
                          $outhtml .= '<td> ' . $caminho_prd_arq_1 . ' ' .
                                                //$caminho_prd_arq_2 . ' ' .
                                                $caminho_prd_arq_3 . ' ' .
                                      '</td>';
                        }else{
                          $outhtml .= '<td> <a title="ServHomol" href="'.$row['caminho_hml_arq_1'].'" target="_blank"><i class="fas fa-file-alt fa-2x" style="color:darkslategrey"></i></a>' . '</td>';
                        }
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