create table _CTA_reprocess_retorno (
	select * from integracao_log where deletado = 0 and integracao_id in(77,78,79) and quantidade_registros > 2
);
SELECT * FROM _CTA_reprocess_retorno r join integracao_log l on r.integracao_log_id = l.integracao_log_id;
update _CTA_reprocess_retorno r join integracao_log l on r.integracao_log_id = l.integracao_log_id set l.deletado = 1;

SELECT ld.* 
FROM _CTA_reprocess_retorno r 
join integracao_log l on r.integracao_log_id = l.integracao_log_id
join integracao_log l2 on replace(l.nome_arquivo, '-RT-', '-EV-') = l2.nome_arquivo
join integracao_log_detalhe ld on l2.integracao_log_id = ld.integracao_log_id
;

update _CTA_reprocess_retorno r 
join integracao_log l on r.integracao_log_id = l.integracao_log_id
join integracao_log l2 on replace(l.nome_arquivo, '-RT-', '-EV-') = l2.nome_arquivo
join integracao_log_detalhe ld on l2.integracao_log_id = ld.integracao_log_id
set ld.integracao_log_status_id = 3, l2.integracao_log_status_id = 3
;




