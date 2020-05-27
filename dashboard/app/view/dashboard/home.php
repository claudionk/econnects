    <?php
        include('app/view/template/template_topo.php'); 
        $data = json_decode( $data, true);
        $data_lock = json_decode( $data_lock, true);
        $data_run_now = json_decode( $data_run_now, true);
        $data_not_exec = json_decode( $data_not_exec, true);
        $data_successful = json_decode( $data_successful, true);
        $data_error = json_decode( $data_error, true);
        $dt_inicio = $data['data']['filter']['dt_inicio'];
        $dt_final = $data['data']['filter']['dt_final'];
        $url_param = str_replace('/', '', $_SERVER['QUERY_STRING']);
    ?>
    <div id="content-wrapper">
        <div class="container-fluid"">
            <form action="home.php" method="get">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="data1">Data Inicial</label>
                        <input type="date" class="form-control" name="dt_inicio" id="dt_inicio" placeholder="dd/mm/aaaa" value="<?php echo $dt_inicio;?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="data2">Data Final</label>
                        <input type="date" class="form-control" name="dt_final" id="dt_final" placeholder="dd/mm/aaaa" value="<?php echo $dt_final;?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="data3">Ambiente</label>
                        <select class="form-control" name="ambiente" id="ambiente">
                            <option <?php if ($ambiente == 'PRODUCAO') echo 'selected';?> value="PRODUCAO">PRODUÇÃO</option>
                            <option <?php if ($ambiente == 'HOMOLOGACAO') echo 'selected';?> value="HOMOLOGACAO">HOMOLOGAÇÃO</option>
                        </select>
                    </div>                    
                    <div class="form-group col-md-1">
                        <div>
                            <label for="data4">&nbsp;</label>
                            <button type="submit" class="form-control btn btn-info">Filtrar</button>
                        </div>
                    </div>
                </div>            
            </form>
        </div>
    <?php
        include('quadros.php');
        include('app/view/template/template_base.php'); 
    ?>