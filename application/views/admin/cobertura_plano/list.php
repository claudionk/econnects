<div class="layout-app">
  <!-- row -->
  <div class="row row-app">
    <!-- col -->
    <div class="col-md-12">

      <div class="section-header">
        <ol class="breadcrumb">
          <li class="active"><?php echo app_recurso_nome();?> do plano:  <span class="text-danger"><?php echo $parceiro_plano['nome'];?></li>
        </ol>
      </div>

      <!-- col-separator -->
      <div class="col-separator col-separator-first col-unscrollable">
        <div class="card">

          <!-- Widget heading -->
          <div class="card-body">
            <a href="<?php echo base_url("admin/produtos_parceiros_planos/view_by_produto_parceiro/{$parceiro_plano['produto_parceiro_id']}")?>" class="btn  btn-app btn-primary">
              <i class="fa fa-arrow-left"></i> Voltar
            </a>

            <a href="<?php echo base_url("$current_controller_uri/add/{$produto_parceiro_plano_id}")?>" class="btn  btn-app btn-primary">
              <i class="fa  fa-plus"></i> Adicionar
            </a>

            <a onclick='salvarOrdem()' class="btn  btn-app btn-primary">
              <i class="fa  fa-sort-amount-asc"></i> Salvar ordem
            </a>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <?php $this->load->view('admin/partials/messages'); ?>
          </div>
        </div>

        <!-- Widget -->
        <div class="card">

          <div class="card-body">
            <input type="hidden" name="url_ordem" id="url_ordem" value="<?php echo  base_url("$current_controller_uri/set_ordem/{$produto_parceiro_plano_id}"); ?>">
            <table id="tabela-ordem" class="table table-hover">
              <!-- Table heading -->
              <thead>
                <tr>
                  <th width='5%'>Ordem</th>
                  <th width='10%'>Tipo</th>
                  <th width='25%'>Cobertura</th>
                  <th width='25%'>Exibição</th>
                  <th width='10%' style="text-align:center">Taxa/Preço</th>
                  <th width='10%' style="text-align:center">Custo</th>
                  <th class="center" width='20%'>Ações</th>
                </tr>
              </thead>
              <!-- // Table heading END -->

              <!-- Table body -->
              <tbody>

                <!-- Table row -->
                <?php $i = 0; ?>
                <?php $total_valor = 0; ?>
                <?php $total_custo_valor = 0; ?>
                <?php foreach($rows as $row) :?>
                <tr data-id="<?php echo $row[$primary_key];?>" data-ordem="<?php echo $i; ?>">
                  <td><?php echo $row['ordem'];?></td>
                  <td><?php echo $row['cobertura_tipo'];?></td>
                  <td><?php echo $row['cobertura_nome'];?></td>
                  <td>
                    <?php
                    $exibicao = '';

                    if($row['mostrar'] == 'preco') {
                      $total_valor += $row['preco'];
                      $preco = $row['preco'];

                      $exibicao = app_format_currency($row['preco'], FALSE, 3);

                      /*
                                            if ($parceiro_plano['cobertura_valor'] == 'valor') {
                                                $total_valor += $row['preco'];
                                                $preco = $row['preco'];

                                                $exibicao = app_format_currency($row['preco'], FALSE, 3);
                                            }else{
                                                $total_valor += app_calculo_porcentagem($row['porcentagem'], $row['preco']);
                                                $preco = app_calculo_porcentagem($row['porcentagem'], $row['preco']);
                                                $exibicao = app_format_currency(app_calculo_porcentagem($row['porcentagem'], $row['preco']), FALSE, 3);
                                            }*/
                    }elseif($row['mostrar'] == 'importancia_segurada') {
                      $exibicao = "IMPORTÂNCIA SEGURADA";
                      $preco = 0;
                    }else{
                      $exibicao = $row['descricao'];
                      $preco = 0;
                    }
                    echo $exibicao;
                    ?>
                  </td>
                  <td style="text-align:center">
                    <?php
                    	if($row["mostrar"] == "preco" ) {
                          echo "R$" . number_format( $row["preco"], 2, ",", "." );
                        } elseif($row["mostrar"] == "importancia_segurada") {
                          echo number_format( $row["porcentagem"], 2, ",", "." ) . "%";
                        } else {
                          echo "N/A";
                        }
                    ?>
                  </td>
                  <td style="text-align:center"><?php
                    $exibicao = '';
                    if ($row['cobertura_custo'] == 'valor') {
                      $total_custo_valor += $row['custo'];
                      $exibicao = number_format( $row['custo'], 2, ",", "." );
                    }else{
                      $total_custo_valor += app_calculo_porcentagem($row['custo'], $preco);
                      $exibicao = number_format( app_calculo_porcentagem($row['custo'], $preco), 2, ",", "." );
                    }
                    echo $exibicao;
                    ?></td>
                  <td class="center">
                    <a href="<?php echo base_url("{$current_controller_uri}/edit/{$produto_parceiro_plano_id}/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                    <a href="<?php echo base_url("$current_controller_uri/delete/{$produto_parceiro_plano_id}/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                  </td>
                </tr>
                <?php $i++; ?>
                <?php endforeach;?>
                <!-- // Table row END -->

              </tbody>
              <!-- // Table body END -->
              <tfoot>
                <tr><td colspan="7"></td></tr>
                <tr>
                  <th></th>
                  <th style="text-align: right;"><?php echo ($total_valor > 0) ? 'Soma Valores:' : ''?></th>
                  <th><?php echo ($total_valor > 0) ? app_format_currency($total_valor, false, 2) : ''?></th>
                  <th style="text-align: right;"><?php echo ($total_custo_valor > 0) ? 'Soma Custo:' : ''?></th>
                  <th><?php echo ($total_custo_valor > 0) ? app_format_currency($total_custo_valor, false, 2) : ''?></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
            <!-- // Table END -->
          </div>
        </div>
        <!-- // Widget END -->
      </div>
    </div>
  </div>
</div>

