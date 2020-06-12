<?php
/*
----------------------------------------------------------------------------------------
TODO: INCLUIR ESTAS DUAS QUERYS
----------------------------------------------------------------------------------------
select l.integracao_id, l.integracao_log_id, l.nome_arquivo, d.integracao_log_detalhe_id, d.integracao_log_status_id, l.processamento_inicio, d.integracao_log_status_id, d.chave, c.msg
from integracao_log l
join integracao_log_detalhe d on l.integracao_log_id = d.integracao_log_id
left join integracao_log_detalhe_campo c on d.integracao_log_detalhe_id = c.integracao_log_detalhe_id and c.deletado = 0
where l.deletado = 0 and d.deletado = 0 and d.chave like '717105700630000005%'
order by l.process;
----------------------------------------------------------------------------------------
TODO: INCLUIR ESTAS DUAS QUERYS
----------------------------------------------------------------------------------------
select x.* from (
	select d.chave, group_concat(distinct c.msg ORDER BY c.msg ASC) msg
	from integracao_log l
	join integracao_log_detalhe d on l.integracao_log_id = d.integracao_log_id
	left join integracao_log_detalhe_campo c on d.integracao_log_detalhe_id = c.integracao_log_detalhe_id and c.deletado = 0
	where l.deletado = 0 and d.deletado = 0 
	and l.nome_arquivo = 'C01.88INSURTECH.EMSCMS-RT-0529-20200519.TXT'
	#and c.msg like '200 - %'
	group by d.chave
) x
where 1;
----------------------------------------------------------------------------------------
TODO: 
----------------------------------------------------------------------------------------
Fazer a validação de processos não executados a mais de 3 dias pela data da prõxima execuçã, comparada com a do dia de hoje.
*/
    function get_all_process($dt_inicio, $dt_final){
        global $conn;
        $query = "   select log_detalhado.id,
                            log_detalhado.dt_criacao,
                            log_detalhado.tipo,
                            log_detalhado.status,
                            count(log_detalhado.integracao_log_id) as qtde_log
                    from (
                    select integracao.integracao_id as id, 
                            DATE_FORMAT(date(integracao.criacao),'%d/%m/%Y') as dt_criacao,
                            date(integracao.criacao) as dt_criacao_original,
                        case when tipo = 'S' then 'SAIDA'
                                when tipo = 'E' then 'ENTRADA' 
                                when tipo = 'R' then 'RESPOSTA'
                            else 'NAO_INFORMADO'    
                            end as tipo,
                            case when status = 'A' then 'AGUARDANDO'
                                when status = 'L' then 'LOCK'
                            else 'NAO_INFORMADO'    
                            end as status,        
                            log.integracao_log_id
                    from sisconnects.integracao
                    left
                    join sisconnects.integracao_log log
                        on integracao.integracao_id = log.integracao_id
                    where integracao.criacao >= '$dt_inicio'
                        and integracao.criacao <= '$dt_final'
                    order by integracao.integracao_id desc
                        ) log_detalhado
                    group by log_detalhado.id,
                            log_detalhado.dt_criacao,
                            log_detalhado.tipo,
                            log_detalhado.status
                    order by log_detalhado.id desc  
                    ";
        //echo '<br>' .  $query;    
        $qtde_saida = 0;
        $qtde_entrada = 0;
        $qtde_resposta = 0;
        try {
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $result = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                if ($row['tipo'] == 'SAIDA'){
                    $qtde_saida ++;
                }elseif($row['tipo'] == 'ENTRADA'){
                    $qtde_entrada ++;
                }elseif($row['tipo'] == 'RESPOSTA'){
                    $qtde_resposta ++;
                }
                $rs['id'] = $row['id'];
                $rs['dt_criacao'] = $row['dt_criacao'];
                $rs['tipo'] = $row['tipo'];
                $rs['status'] = $row['status'];
                $rs['qtde_log'] = $row['qtde_log'];
                $result[] = $rs;
            }
            $stmt = null;
            $filter = array('dt_inicio'     => $dt_inicio,
                            'dt_final'      => $dt_final);
            $resume = array('qtde_saida'    => $qtde_saida,
                            'qtde_entrada'  => $qtde_entrada,
                            'qtde_resposta' => $qtde_resposta);
            return array ('result'  => $result,
                          'filter'  => $filter,
                          'resume'  => $resume);
        }
        catch (PDOException $e) {
            print $e->getMessage();
        }                        
    }

    function get_process_by_filter(){
        return true;
    }
// TODO: Implementar esta rotina
    function get_process_by_id($integracao_id){
        return true;
    }    

    function get_lock_process($dt_inicio, $dt_final){
        global $conn;
        $query = "SELECT DISTINCT 
                         p.nome_fantasia AS parceiro,
                         i.descricao,
                         CASE WHEN i.status = 'L' THEN 'LOCK'
                              ELSE i.status
                          END AS status_atual,
                         DATE_FORMAT(i.ultima_execucao,'%d/%m/%Y %H:%i:%s') as ultima_execucao,
                         i.integracao_id,
                         CONCAT('http://econnects-h.jelastic.saveincloud.net//assets/uploads/integracao/',i.integracao_id, '/', i.tipo, '/', IFNULL(il.nome_arquivo,'SEM_ARQUIVO')) AS caminho_hml_arq_1,
                         CONCAT('http://node23640-econnects-prod.jelastic.saveincloud.net/assets/uploads/integracao/',i.integracao_id, '/', i.tipo, '/', IFNULL(il.nome_arquivo,'SEM_ARQUIVO')) AS caminho_prd_arq_1,
                         CONCAT('http://node23641-econnects-prod.jelastic.saveincloud.net/assets/uploads/integracao/',i.integracao_id, '/', i.tipo, '/', IFNULL(il.nome_arquivo,'SEM_ARQUIVO')) AS caminho_prd_arq_2,
                         CONCAT('http://node23642-econnects-prod.jelastic.saveincloud.net/assets/uploads/integracao/',i.integracao_id, '/', i.tipo, '/', IFNULL(il.nome_arquivo,'SEM_ARQUIVO')) AS caminho_prd_arq_3,
                         DATE(il.processamento_inicio) as proces_inicio_orig,
                         DATE_FORMAT(il.processamento_inicio,'%d/%m/%Y %H:%i:%s') as processamento_inicio,
                         il.nome_arquivo,
                         il.quantidade_registros,
                         (select count(1) from integracao_log_detalhe where integracao_log_id = il.integracao_log_id) as quantidade_processado,
                         CONCAT(ROUND((((select count(1) from integracao_log_detalhe where integracao_log_id = il.integracao_log_id) / il.quantidade_registros) * 100),2),'%') AS percentual                         
                    FROM integracao i
                    JOIN parceiro p ON i.parceiro_id = p.parceiro_id
                     AND i.status <> 'A'
                    LEFT JOIN integracao_log il ON  i.integracao_id = il.integracao_id
                     AND il.deletado = 0
                     AND DATE(il.processamento_inicio) BETWEEN '$dt_inicio' AND '$dt_final'
                     AND il.processamento_fim IS NULL
                   ORDER BY p.nome_fantasia,
                            i.descricao,
                            i.ultima_execucao
                 ";
        try {
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $result_lock = [];
            $qtde_lock = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $qtde_lock ++;
                $rs_lock['parceiro']              = $row['parceiro'];
                $rs_lock['descricao']             = $row['descricao']; 
                $rs_lock['status_atual']          = $row['status_atual'];
                $rs_lock['ultima_execucao']       = $row['ultima_execucao'];
                $rs_lock['integracao_id']         = $row['integracao_id'];
                $rs_lock['caminho_prd_arq_3']     = $row['caminho_prd_arq_3'];
                $rs_lock['caminho_hml_arq_1']     = $row['caminho_hml_arq_1'];
                $rs_lock['caminho_prd_arq_1']     = $row['caminho_prd_arq_1'];
                $rs_lock['caminho_prd_arq_2']     = $row['caminho_prd_arq_2'];
                $rs_lock['proces_inicio_orig']    = $row['proces_inicio_orig'];
                $rs_lock['nome_arquivo']          = $row['nome_arquivo'];
                $rs_lock['quantidade_registros']  = $row['quantidade_registros'];
                $rs_lock['quantidade_processado'] = $row['quantidade_processado'];
                $rs_lock['percentual']            = $row['percentual'];
                $result_lock[] = $rs_lock;
            }
            $stmt = null;
            $resume_lock = array('qtde_lock' => $qtde_lock);
            return array ('result_lock' => $result_lock,
                          'resume_lock' => $resume_lock);            
        }
        catch (PDOException $e) {
            print $e->getMessage();
        }                                    
    }

    function get_run_now_process(){
        global $conn;
        $query = "   SELECT p.nome_fantasia AS parceiro,
                            i.descricao,
                            CASE WHEN il.processamento_fim IS NULL THEN 'EM EXECUCAO'
                                ELSE 'FINALIZADO'
                            END AS status_atual,
                            il.nome_arquivo,
                            i.integracao_id,
                            il.integracao_log_id,
                            DATE_FORMAT(il.processamento_inicio,'%d/%m/%Y %H:%i:%s') AS processamento_inicio,
                            DATE_FORMAT(il.processamento_fim,'%d/%m/%Y %H:%i:%s') AS processamento_fim,
                            il.quantidade_registros,
                            COUNT(*) AS quantidade_processado, 
                            CASE WHEN il.processamento_fim IS NULL THEN CONCAT(ROUND(((count(*) / il.quantidade_registros) * 100),2),'%') 
                                 ELSE '100%'
                             END AS percentual
                       FROM integracao i
                       JOIN parceiro p ON i.parceiro_id = p.parceiro_id
                       JOIN integracao_log il ON i.integracao_id = il.integracao_id
                        AND il.deletado = 0
                        AND il.processamento_inicio BETWEEN (NOW() - INTERVAL 60 MINUTE) AND (NOW())
                       JOIN integracao_log_detalhe ild ON il.integracao_log_id = ild.integracao_log_id
                        AND ild.deletado = 0
                      GROUP BY p.nome_fantasia,
                               i.descricao,
                               status_atual,
                               il.nome_arquivo,
                               i.integracao_id,
                               il.integracao_log_id,
                               processamento_inicio,
                               processamento_fim,
                               il.quantidade_registros
                      ORDER BY status_atual, 
                               il.processamento_inicio,
                               p.nome_fantasia,
                               i.descricao,
                               i.ultima_execucao
                 ";
        try {
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $result_now = [];
            $qtde_now = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $qtde_now ++;
                $rs_now['parceiro']                 = $row['parceiro']; // preg_replace( '/[^[:print:]\r\n]/', '?',$row['parceiro']);
                $rs_now['descricao']                = $row['descricao']; 
                $rs_now['status_atual']             = $row['status_atual'];
                $rs_now['nome_arquivo']             = $row['nome_arquivo'];
                $rs_now['integracao_id']            = $row['integracao_id'];
                $rs_now['integracao_log_id']        = $row['integracao_log_id'];
                $rs_now['processamento_inicio']     = $row['processamento_inicio'];
                $rs_now['processamento_fim']        = $row['processamento_fim'];
                $rs_now['quantidade_registros']     = $row['quantidade_registros'];
                $rs_now['quantidade_processado']    = $row['quantidade_processado'];
                $rs_now['percentual']               = $row['percentual'];
                $result_now[] = $rs_now;
            }
            $stmt = null;
            $resume_now = array('qtde_now' => $qtde_now);
            return array ('result_now' => $result_now,
                          'resume_now' => $resume_now);            
        }
        catch (PDOException $e) {
            print $e->getMessage();
        }                                    
    }

    function get_not_exec_process(){
        global $conn;
        $query = "   SELECT p.nome_fantasia AS parceiro,
                            i.descricao,
                            i.status AS status_atual,
                            DATE_FORMAT(i.ultima_execucao,'%d/%m/%Y %H:%i:%s') AS ultima_execucao,
                            DATEDIFF(NOW(),i.ultima_execucao) AS qtde_dias,
                            i.periodicidade_unidade,
                            i.integracao_id
                       FROM integracao i
                       JOIN parceiro p ON i.parceiro_id = p.parceiro_id
                        AND i.deletado = 0
                        AND i.ultima_execucao IS NOT NULL
                        AND DATEDIFF(NOW(),i.ultima_execucao) > 1
                        AND i.slug_group NOT IN ('sulacap-ativacao','ret-pedido-num-cap', 'baixa_cobrca')
                      ORDER BY p.nome_fantasia ASC,
                               i.descricao ASC,
                               i.ultima_execucao ASC
                 ";
        try {
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $result_not_exec = [];
            $qtde_not_exec = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $qtde_not_exec ++;
                $rs_not_exec['parceiro']                = $row['parceiro'];
                $rs_not_exec['descricao']               = $row['descricao']; 
                $rs_not_exec['status_atual']            = $row['status_atual'];
                $rs_not_exec['ultima_execucao']         = $row['ultima_execucao'];
                $rs_not_exec['qtde_dias']               = $row['qtde_dias'];
                $rs_not_exec['periodicidade_unidade']   = $row['periodicidade_unidade'];
                $rs_not_exec['integracao_id']           = $row['integracao_id'];
                $result_not_exec[] = $rs_not_exec;
            }
            $stmt = null;
            $resume_not_exec = array('qtde_not_exec' => $qtde_not_exec);
            return array ('result_not_exec' => $result_not_exec,
                          'resume_not_exec' => $resume_not_exec);            
        }
        catch (PDOException $e) {
            print $e->getMessage();
        }                                    
    }  

    function get_successful_process($dt_inicio, $dt_final){
        global $conn;
        $query = "SELECT p.nome_fantasia AS parceiro,
                         i.descricao,
                         DATE_FORMAT(il.processamento_inicio,'%d/%m/%Y %H:%i:%s') as processamento_inicio,
                         il.nome_arquivo,
                         CASE WHEN il.integracao_log_status_id = 4 THEN 'Processado com Sucesso'                   
                             WHEN il.integracao_log_status_id = 7 THEN 'Processado com Sucesso ant.'
                         END AS status_processo,
                         i.integracao_id
                    FROM integracao i
                    JOIN parceiro p ON i.parceiro_id = p.parceiro_id
                    JOIN integracao_log il ON i.integracao_id = il.integracao_id
                     AND il.deletado = 0
                     AND DATE(il.processamento_inicio) BETWEEN '$dt_inicio' AND '$dt_final'
                     AND il.integracao_log_status_id IN (4,7)
                   ORDER BY p.nome_fantasia,
                            il.nome_arquivo,
                            i.ultima_execucao ASC
                 ";
        try {
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $result_successful = [];
            $qtde_successful = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $qtde_successful ++;
                $rs_successful['parceiro']             = $row['parceiro'];
                $rs_successful['descricao']            = $row['descricao']; 
                $rs_successful['processamento_inicio'] = $row['processamento_inicio'];
                $rs_successful['nome_arquivo']         = $row['nome_arquivo'];
                $rs_successful['status_processo']      = $row['status_processo'];
                $rs_successful['integracao_id']        = $row['integracao_id'];
                $result_successful[] = $rs_successful;
            }
            $stmt = null;
            $resume_successful = array('qtde_successful' => $qtde_successful);
            return array ('result_successful' => $result_successful,
                          'resume_successful' => $resume_successful);            
        }
        catch (PDOException $e) {
            print $e->getMessage();
        }                                    
    }  

    function get_error_process($dt_inicio, $dt_final){
        global $conn;
        $query = "SELECT p.nome_fantasia AS parceiro,
                         i.descricao,
                         DATE_FORMAT(il.processamento_inicio,'%d/%m/%Y %H:%i:%s') as processamento_inicio,
                         il.nome_arquivo,
                         CASE WHEN il.integracao_log_status_id = 5 THEN 'Processado com Erro'
                         END AS status_processo,
                         i.integracao_id,
                         CONCAT('http://econnects-h.jelastic.saveincloud.net//assets/uploads/integracao/',i.integracao_id, '/', i.tipo, '/', il.nome_arquivo) AS caminho_hml_arq_1,
                         CONCAT('http://node23640-econnects-prod.jelastic.saveincloud.net/assets/uploads/integracao/',i.integracao_id, '/', i.tipo, '/', il.nome_arquivo) AS caminho_prd_arq_1,
                         CONCAT('http://node23641-econnects-prod.jelastic.saveincloud.net/assets/uploads/integracao/',i.integracao_id, '/', i.tipo, '/', il.nome_arquivo) AS caminho_prd_arq_2,
                         CONCAT('http://node23642-econnects-prod.jelastic.saveincloud.net/assets/uploads/integracao/',i.integracao_id, '/', i.tipo, '/', il.nome_arquivo) AS caminho_prd_arq_3,
                         DATE(il.processamento_inicio) as proces_inicio_orig
                    FROM integracao i
                    JOIN parceiro p ON i.parceiro_id = p.parceiro_id
                    JOIN integracao_log il ON i.integracao_id = il.integracao_id
                     AND il.deletado = 0
                     AND DATE(il.processamento_inicio) BETWEEN '$dt_inicio' AND '$dt_final'
                     AND il.integracao_log_status_id = 5
                   ORDER BY p.nome_fantasia,
                            il.nome_arquivo,
                            i.ultima_execucao ASC
                 ";
        try {
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $result_error = [];
            $qtde_error = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $qtde_error ++;
                $rs_error['parceiro']             = $row['parceiro'];
                $rs_error['descricao']            = $row['descricao']; 
                $rs_error['processamento_inicio'] = $row['processamento_inicio'];
                $rs_error['nome_arquivo']         = $row['nome_arquivo'];
                $rs_error['status_processo']      = $row['status_processo'];
                $rs_error['integracao_id']        = $row['integracao_id'];
                $rs_error['caminho_hml_arq_1']    = $row['caminho_hml_arq_1'];
                $rs_error['caminho_prd_arq_1']    = $row['caminho_prd_arq_1'];
                $rs_error['caminho_prd_arq_2']    = $row['caminho_prd_arq_2'];
                $rs_error['caminho_prd_arq_3']    = $row['caminho_prd_arq_3'];
                $rs_error['proces_inicio_orig']   = $row['proces_inicio_orig'];
                $result_error[] = $rs_error;
            }
            $stmt = null;
            $resume_error = array('qtde_error' => $qtde_error);
            return array ('result_error' => $result_error,
                          'resume_error' => $resume_error);            
        }
        catch (PDOException $e) {
            print $e->getMessage();
        }                                    
    }  

    function get_error_process_details($dt_inicio, $dt_final, $file_name){
        global $conn;
        $file_name_ret = str_replace('-EV-','-RT-',$file_name);
        $query = "   SELECT reg_proc.nome_arquivo,
                            CASE
                                WHEN reg_proc.integracao_log_status_id = 2 THEN 'Em Processamento'
                                WHEN reg_proc.integracao_log_status_id = 3 THEN 'Sem Retorno'
                                WHEN reg_proc.integracao_log_status_id = 4 THEN 'Processado com Sucesso'
                                WHEN reg_proc.integracao_log_status_id = 5 THEN 'Processado com Erro'
                                WHEN reg_proc.integracao_log_status_id = 7 THEN 'Processado com Sucesso ant.'
                            END AS status_processo,
                            count(*) AS qtde,
                            reg_proc.quantidade_registros - 2 AS qtd_total_reg,
                            CASE
                                WHEN reg_proc.integracao_log_status_id = 2 THEN concat(ifnull(reg_proc.msg, 'Cod: SID2 - Processando...'))
                                WHEN reg_proc.integracao_log_status_id = 3 THEN concat(ifnull(reg_proc.msg, 'Cod: SID3 - Processamento pendente'))
                                WHEN reg_proc.integracao_log_status_id = 4 THEN concat(ifnull(reg_proc.msg, 'Cod: SID4 - Processado com sucesso'))
                                WHEN reg_proc.integracao_log_status_id = 5 THEN concat(ifnull(reg_proc.msg, 'Cod: SID5 - Processado com erro'))
                                WHEN reg_proc.integracao_log_status_id = 7 THEN concat(ifnull(reg_proc.msg, 'Cod: SID7 - Já processado anteriormente'))
                                ELSE reg_proc.integracao_log_status_id
                            END AS mensagem,
                            CASE
                                WHEN reg_proc.integracao_log_status_id <> 4 THEN group_concat(concat('''', reg_proc.chave, '''') ORDER BY chave separator ', ')
                                ELSE ''
                            END AS apolices,
                            DATE_FORMAT(reg_proc.processamento_inicio, '%d/%m/%Y %H:%i:%s') AS processamento_inicio,
                            DATE_FORMAT(reg_proc.processamento_fim, '%d/%m/%Y %H:%i:%s') AS processamento_fim,
                            integracao_id,
                            integracao_log_id,
                            nome_fantasia AS parceiro,
                            nome AS descricao
                    FROM
                    (SELECT l.quantidade_registros,
                            l.integracao_log_id,
                            d.integracao_log_status_id,
                            d.chave,
                            CASE WHEN SUBSTR((CASE WHEN (c.msg IS NULL OR c.msg = '') THEN d.retorno ELSE c.msg END)
                                            ,1,4) = 'Cod:' 
                                 THEN (CASE WHEN (c.msg IS NULL OR c.msg = '') THEN d.retorno ELSE c.msg END)
                                 ELSE CONCAT('Cod: ',(CASE WHEN (c.msg IS NULL OR c.msg = '') THEN d.retorno ELSE c.msg END))
                             END AS msg, 
                            l.nome_arquivo,
                            l.processamento_inicio,
                            l.processamento_fim,
                            l.integracao_id,
                            i.nome,
                            i.parceiro_id,
                            i.slug,
                            i.slug_group,
                            i.status,
                            i.proxima_execucao,
                            i.ultima_execucao,
                            p.nome_fantasia
                        FROM integracao_log l
                        JOIN integracao_log_detalhe d ON l.integracao_log_id = d.integracao_log_id
                        AND l.deletado = 0
                        AND d.deletado = 0
                        JOIN integracao i ON l.integracao_id = i.integracao_id
                        JOIN parceiro p ON i.parceiro_id = p.parceiro_id
                        LEFT JOIN integracao_log_detalhe_campo c ON d.integracao_log_detalhe_id = c.integracao_log_detalhe_id
                        AND c.deletado = 0
                        WHERE date(l.processamento_inicio) >= '$dt_inicio'
                        AND l.nome_arquivo IN ('$file_name','$file_name_ret') 
                    ) AS reg_proc
                    GROUP BY nome_arquivo,
                             mensagem
                    ORDER BY integracao_log_id desc,
                             nome_arquivo,
                             status,
                             mensagem
                 ";
        try {
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $result_error_det = [];
            $qtde_error_det = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $qtde_error_det ++;
                $rs_error_det['parceiro']             = $row['parceiro'];
                $rs_error_det['descricao']            = $row['descricao']; 
                $rs_error_det['processamento_inicio'] = $row['processamento_inicio'];
                $rs_error_det['processamento_fim']    = $row['processamento_fim'];
                $rs_error_det['nome_arquivo']         = $row['nome_arquivo'];
                $rs_error_det['status_processo']      = $row['status_processo'];
                $rs_error_det['integracao_id']        = $row['integracao_id'];
                $rs_error_det['integracao_log_id']    = $row['integracao_log_id'];
                $rs_error_det['qtde']                 = $row['qtde'];
                $rs_error_det['qtd_total_reg']        = $row['qtd_total_reg'];
                $rs_error_det['mensagem']             = $row['mensagem'];
                $rs_error_det['apolices']             = $row['apolices'];
                $result_error_det[] = $rs_error_det;
            }
            $stmt = null;
            $resume_error_det = array('qtde_error_det' => $qtde_error_det);
            return array ('result_error_det' => $result_error_det,
                          'resume_error_det' => $resume_error_det);            
        }
        catch (PDOException $e) {
            print $e->getMessage();
        }                                    
    } 

    function get_all_invoicing(){
        global $conn;
        $query = "   SELECT p.nome_fantasia AS parceiro,
                            fpl.fatura_parceiro_lote_id,
                            DATE_FORMAT(fpl.data_corte, '%Y-%m') AS data_corte_orig,
                            DATE_FORMAT(fpl.data_corte, '%m/%Y') AS data_corte,
                            fpl.data_corte AS data_corte_report,
                            CASE WHEN fpl.gera_oficial = 'N' THEN 'NÃO' 
                                 ELSE 'SIM'
                             END AS gera_oficial,
                            DATE_FORMAT(fpl.data_processamento, '%d/%m/%Y %H:%i:%s') AS data_processamento
                    FROM fatura_parceiro_lote fpl
                    JOIN parceiro p ON fpl.parceiro_id = p.parceiro_id
                        AND fpl.deletado = 0
                    ORDER BY fpl.data_corte desc,
                            p.nome_fantasia asc;
                 ";
        try {
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $result_invoicing = [];
            $qtde_invoicing = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $qtde_invoicing ++;
                $rs_invoicing['parceiro']                   = $row['parceiro'];
                $rs_invoicing['fatura_parceiro_lote_id']    = $row['fatura_parceiro_lote_id']; 
                $rs_invoicing['data_corte_orig']            = $row['data_corte_orig']; 
                $rs_invoicing['data_corte']                 = $row['data_corte'];
                $rs_invoicing['data_corte_report']          = $row['data_corte_report'];
                $rs_invoicing['gera_oficial']               = $row['gera_oficial'];
                $rs_invoicing['data_processamento']         = $row['data_processamento'];
                $result_invoicing[] = $rs_invoicing;
            }
            $stmt = null;
            $resume_invoicing = array('qtde_invoicing' => $qtde_invoicing);
            return array ('result_invoicing' => $result_invoicing,
                          'resume_invoicing' => $resume_invoicing);            
        }
        catch (PDOException $e) {
            print $e->getMessage();
        }                                    
    }

    function get_invoicing_report($dt_corte, $oficial, $tipo_rel, $id_lote){
        global $conn;
        //CALL sp_gera_faturamento_relatorio('GERA_ANALITICO'      , 15, '2020-04-30', NULL);
        //CALL sp_gera_faturamento_relatorio('GERA_RESUMO'         , 15, '2020-04-30', NULL);
        //CALL sp_gera_faturamento_relatorio('GERA_SALDO_ACUMULADO', 15, '2020-04-30', NULL);
        $query = "CALL sp_gera_faturamento_relatorio('$tipo_rel', $id_lote, '$dt_corte', NULL)";
        try {
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $result_invoicing_report = [];
            $qtde_invoicing_report = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $qtde_invoicing_report ++;
                $rs_invoicing_report['NF']                         = $row['NF'];
                $rs_invoicing_report['Período de Referência']      = $row['Período de Referência']; 
                $rs_invoicing_report['Cliente / Seguradora']       = $row['Cliente / Seguradora']; 
                $rs_invoicing_report['Representante de Seguros']   = $row['Representante de Seguros'];
                $rs_invoicing_report['Produto']                    = $row['Produto'];
                $rs_invoicing_report['Tipo de Transação']          = $row['Tipo de Transação'];
                $rs_invoicing_report['Valor']                      = $row['Valor'];
                $rs_invoicing_report['Quantidade Emissão']         = $row['Quantidade Emissão'];
                $rs_invoicing_report['Quantidade CTA']             = $row['Quantidade CTA'];
                $rs_invoicing_report['Valor Total CTA']            = $row['Valor Total CTA'];
                $rs_invoicing_report['Valor Total Emissão']        = $row['Valor Total Emissão'];
                $rs_invoicing_report['Valor Parametrizado']        = $row['Valor Parametrizado'];
                $result_invoicing_report[] = $rs_invoicing_report;
            }
            $stmt = null;
            $resume_invoicing_report = array('qtde_invoicing_report' => $qtde_invoicing_report);
            return array ('result_invoicing_report' => $result_invoicing_report,
                          'resume_invoicing_report' => $resume_invoicing_report);            
        }
        catch (PDOException $e) {
            print $e->getMessage();
        }                                    
    }
    
?>