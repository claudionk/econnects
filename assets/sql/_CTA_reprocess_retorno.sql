create table _CTA_reprocess_retorno (
	select * from integracao_log where deletado = 0 and integracao_id in(77,78,79) and quantidade_registros > 2
);
SELECT * FROM _CTA_reprocess_retorno r join integracao_log l on r.integracao_log_id = l.integracao_log_id;
update _CTA_reprocess_retorno r join integracao_log l on r.integracao_log_id = l.integracao_log_id set l.deletado = 1;
