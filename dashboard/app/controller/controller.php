<?php   
    require('./app/model/process_model.php'); 

    function dashboard(){
        $dt_inicio = (isset($_GET['dt_inicio'])) ? $_GET['dt_inicio'] : date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
        $dt_final = (isset($_GET['dt_final'])) ? $_GET['dt_final'] : date('Y-m-d');  
        $ambiente = (isset($_GET['ambiente']) && $_GET['ambiente'] <> '') ? $_GET['ambiente'] : 'PRODUCAO';

        $data = get_all_process($dt_inicio, $dt_final);    
        $data = json_encode(array('data' => $data));

        $data_lock = get_lock_process($dt_inicio, $dt_final);    
        $data_lock = json_encode(array('data_lock' => $data_lock));

        $data_run_now = get_run_now_process();    
        $data_run_now = json_encode(array('data_run_now' => $data_run_now)); 

        $data_not_exec = get_not_exec_process();    
        $data_not_exec = json_encode(array('data_not_exec' => $data_not_exec)); 

        $data_successful = get_successful_process($dt_inicio, $dt_final);    
        $data_successful = json_encode(array('data_successful' => $data_successful)); 
        
        $data_error = get_error_process($dt_inicio, $dt_final);
        $data_error = json_encode(array('data_error' => $data_error));        

        include('./app/view/dashboard/home.php');    
    }

    function detalhe(){
        $dt_inicio = (isset($_GET['dt_inicio'])) ? $_GET['dt_inicio'] : date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
        $dt_final = (isset($_GET['dt_final'])) ? $_GET['dt_final'] : date('Y-m-d');  
        $ambiente = (isset($_GET['ambiente']) && $_GET['ambiente'] <> '') ? $_GET['ambiente'] : 'PRODUCAO';        

        $data = get_all_process($dt_inicio, $dt_final);        
        $data = json_encode(array ('data' => $data));
        include('./app/view/detalhe/home.php');    
    }

    function detalhe_lock(){
        $dt_inicio = (isset($_GET['dt_inicio'])) ? $_GET['dt_inicio'] : date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
        $dt_final = (isset($_GET['dt_final'])) ? $_GET['dt_final'] : date('Y-m-d');  
        $ambiente = (isset($_GET['ambiente']) && $_GET['ambiente'] <> '') ? $_GET['ambiente'] : 'PRODUCAO';                
  
        $data_lock = get_lock_process($dt_inicio, $dt_final);    
        $data_lock = json_encode(array('data_lock' => $data_lock));  
        include('./app/view/detalhe_lock/home.php');    
    }

    function detalhe_run_now(){
        $ambiente = (isset($_GET['ambiente']) && $_GET['ambiente'] <> '') ? $_GET['ambiente'] : 'PRODUCAO';                
  
        $data_run_now = get_run_now_process();    
        $data_run_now = json_encode(array('data_run_now' => $data_run_now));  
        include('./app/view/detalhe_run_now/home.php');    
    }

    function detalhe_not_exec(){
        $ambiente = (isset($_GET['ambiente']) && $_GET['ambiente'] <> '') ? $_GET['ambiente'] : 'PRODUCAO';                
  
        $data_not_exec = get_not_exec_process();    
        $data_not_exec = json_encode(array('data_not_exec' => $data_not_exec));  
        include('./app/view/detalhe_not_exec/home.php');    
    }    

    function detalhe_successful(){
        $dt_inicio = (isset($_GET['dt_inicio'])) ? $_GET['dt_inicio'] : date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
        $dt_final = (isset($_GET['dt_final'])) ? $_GET['dt_final'] : date('Y-m-d');  
        $ambiente = (isset($_GET['ambiente']) && $_GET['ambiente'] <> '') ? $_GET['ambiente'] : 'PRODUCAO';                
  
        $data_successful = get_successful_process($dt_inicio, $dt_final);    
        $data_successful = json_encode(array('data_successful' => $data_successful)); 
        include('./app/view/detalhe_successful/home.php');    
    }      

    function detalhe_error(){
        $dt_inicio = (isset($_GET['dt_inicio'])) ? $_GET['dt_inicio'] : date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
        $dt_final = (isset($_GET['dt_final'])) ? $_GET['dt_final'] : date('Y-m-d');  
        $ambiente = (isset($_GET['ambiente']) && $_GET['ambiente'] <> '') ? $_GET['ambiente'] : 'PRODUCAO';                
  
        $data_error = get_error_process($dt_inicio, $dt_final);    
        $data_error = json_encode(array('data_error' => $data_error));        
        include('./app/view/detalhe_error/home.php');    
    }          

    function detalhe_error_details(){
        $dt_inicio = (isset($_GET['dt_inicio'])) ? $_GET['dt_inicio'] : date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
        $dt_final = (isset($_GET['dt_final'])) ? $_GET['dt_final'] : date('Y-m-d');  
        $ambiente = (isset($_GET['ambiente']) && $_GET['ambiente'] <> '') ? $_GET['ambiente'] : 'PRODUCAO';                
        $file_name = (isset($_GET['file_name'])) ? $_GET['file_name'] : '';  
  
        $data_error_det = get_error_process_details($dt_inicio, $dt_final, $file_name);    
        $data_error_det = json_encode(array('data_error_det' => $data_error_det));        
        include('./app/view/detalhe_error_details/home.php');    
    }    

    function faturamento(){        
        $dt_corte = (isset($_GET['dt_corte'])) ? $_GET['dt_corte'] : date('Y-m', strtotime(date('Y-m'). ' - 1 month'));
        $oficial = (isset($_GET['oficial'])) ? $_GET['oficial'] : 'N';  
        $ambiente = 'PRODUCAO';

        $data_invoicing = get_all_invoicing();    
        $data_invoicing = json_encode(array('data_invoicing' => $data_invoicing));

        include('./app/view/faturamento/home.php');    
    }  

    function run_invoicing(){
        $dt_corte = (isset($_GET['dt_corte'])) ? $_GET['dt_corte'] : date('Y-m', strtotime(date('Y-m'). ' - 1 month'));
        $oficial = (isset($_GET['oficial'])) ? $_GET['oficial'] : 'N';  
        $ambiente = 'PRODUCAO';

        $data_invoicing_process = invoicing_process(date("Y-m-t", strtotime($dt_corte)), $oficial);  
        if ($data_invoicing_process['status'] == 'OK'){
            echo '<script>alert("'.$data_invoicing_process['message'].'")</script>';   
        } else{
            echo '<script>alert("Falha no processamento")</script>';   
        }

        faturamento();
    }

    function faturamento_report(){        
        $dt_corte   = (isset($_GET['dt_corte'])) ? $_GET['dt_corte'] : date('Y-m', strtotime(date('Y-m'). ' - 1 month'));
        $oficial    = (isset($_GET['oficial'])) ? $_GET['oficial'] : 'N';  
        $ambiente   = 'PRODUCAO';
        $tipo_rel   = (isset($_GET['tipo_rel'])) ? $_GET['tipo_rel'] : 'GERA_RESUMO';  
        $id_lote    = (isset($_GET['id_lote'])) ? $_GET['id_lote'] : '0';  
        exportCSV(get_invoicing_report($dt_corte, $oficial, $tipo_rel, $id_lote),$tipo_rel .'_LOTE_'.$id_lote);
    }    

    function exportCSVOld($rows = [], $nomeArq) {
        header('Content-Type: text/html; charset=utf-8');
        header("Pragma: no-cache");
        header("Cache: no-cahce");
        $file = dirname(__FILE__) . "/temp/";
        $filename = $file . $nomeArq."_".date("Y-m-d_H-i-s",time()).".csv";
        $fp = fopen($filename, "w");
        $header = false;
        foreach ($rows as $row){
            if (empty($header)){
                $header = array_keys($row);
                fputcsv($fp, $header,';');
                $header = array_flip($header);
            }
            fputcsv($fp, array_merge($header, $row),';');
        }
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Length: ' . filesize($filename));
        header('Content-Disposition: attachment; filename=' . basename($filename));
        readfile($filename);
        unlink($filename);
        return; 
    }

    function exportCSV($rows = [], $nomeArq) {
        header('Content-Type: text/html; charset=utf-8');
        header("Pragma: no-cache");
        header("Cache: no-cahce");
        $file = dirname(__FILE__) . "/temp/";
        $filename = $file . $nomeArq."_".date("Y-m-d_H-i-s",time()).".csv";
        $fp = fopen($filename, "w");
        $header = false;
        $linhaheader = '';
        $countheader = 0;
        $linha = '';
        // Cria o Header
        foreach ($rows as $row){
            $headers = array_keys($row);
            $countheader = count($headers);
            if ($countheader > 0 ){
                foreach ($headers as $header) {
                    $linhaheader .= $header . ";";
                }
                break;
            }
        }
        fwrite($fp, $linhaheader."\n");       

        // Cria as Linhas
        foreach ($rows as $row) {
            $columns = array_values($row);
            foreach ($columns as $column) {
                $linha .= $column . ";";
            }            
            fwrite($fp, $linha."\n"); 
            $linha = '';           
        }
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Length: ' . filesize($filename));
        header('Content-Disposition: attachment; filename=' . basename($filename));
        readfile($filename);
        unlink($filename);
        return; 
    }
    
?>