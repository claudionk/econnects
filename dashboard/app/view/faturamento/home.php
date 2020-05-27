    <?php
        include('app/view/template/template_topo.php'); 
        $data_invoicing = json_decode( $data_invoicing, true);
    ?>
    <div id="content-wrapper">
        <div class="container-fluid"">
            <form action="?c=faturamento" method="get">
                <input type="hidden" name="c" id="c" value="faturamento">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="data1">Data de Corte</label>
                        <input type="month" class="form-control" name="dt_corte" id="dt_corte" placeholder="dd/mm/aaaa" value="<?php echo $dt_corte;?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="data3">Oficial?</label>
                        <select class="form-control" name="oficial" id="oficial">
                            <option <?php if ($oficial == 'N') echo 'selected';?> value="N">NÃO</option>
                            <option <?php if ($oficial == 'S') echo 'selected';?> value="S">SIM</option>
                        </select>
                    </div>   
                    <input type="hidden" name="ambiente" id="ambiente" value="PRODUCAO">
                    <div class="form-group col-md-1">
                        <div>
                            <label for="data4">&nbsp;</label>
                            <button type="submit" class="form-control btn btn-info">Gerar</button>
                        </div>
                    </div>
                </div>            
            </form>
        </div>
    <?php
        include('dados_faturamento.php'); 
        include('app/view/template/template_base.php'); 
    ?>