ALTER TABLE `sisconnects`.`apolice_cobertura` 
ADD COLUMN `data_inicio_vigencia` DATE NULL DEFAULT NULL AFTER `cod_sucursal`,
ADD COLUMN `data_fim_vigencia` DATE NULL DEFAULT NULL AFTER `data_ini_vigencia`;

ALTER TABLE `sisconnects`.`cotacao_cobertura` 
ADD COLUMN `data_inicio_vigencia` DATE NULL DEFAULT NULL AFTER `iof`,
ADD COLUMN `data_fim_vigencia` DATE NULL DEFAULT NULL AFTER `data_ini_vigencia`;

ALTER TABLE `sisconnects`.`apolice_endosso` 
ADD COLUMN `cod_cobertura` VARCHAR(15) NULL AFTER `data_vencimento`;

#CREATE PROCEDURE sp_cta_parcemissao_unico
#CREATE PROCEDURE sp_cta_parcemissao_parcelado

create table integracao_detalhe_bkp_sql (
	select distinct i.integracao_id, i.nome, i.cod_tpa, IF(pc.pagamento_tipo = 'RECORRENTE' OR pc.endosso_controle_cliente = 1, 'MENSAL', IF(pc.endosso_controle_cliente = 2, 'PARCELADO', 'UNICO')) cobranca, d.script_sql, i.slug_group, now() criacao
	from integracao i
	join produto_parceiro pp on i.cod_tpa = pp.cod_tpa and pp.parceiro_id = 32
	join produto_parceiro_configuracao pc on pp.produto_parceiro_id = pc.produto_parceiro_id
	join integracao_detalhe d on i.integracao_id = d.integracao_id and d.tipo = 'D'
	where i.slug_group IN( 'parc-emissao', 'ems-emissao' ) and i.deletado = 0 and pp.deletado = 0 and pc.deletado = 0
	order by cobranca desc, i.cod_tpa
);

#query unico
update integracao i
join produto_parceiro pp on i.cod_tpa = pp.cod_tpa and pp.parceiro_id = 32
join produto_parceiro_configuracao pc on pp.produto_parceiro_id = pc.produto_parceiro_id
join integracao_detalhe d on i.integracao_id = d.integracao_id and d.tipo = 'D'
set d.script_sql = 'call sp_cta_parcemissao_unico({apolice_id}, {apolice_status_id}, {apolice_endosso_id});'
where i.slug_group = 'parc-emissao' and i.deletado = 0 and pp.deletado = 0 and pc.deletado = 0
AND i.cod_tpa in( '031',  '032',  '053',  '054',  '055',  '057',  '071',  '010', '011', '015', '020', '021', '067', '012', '045', '007', '029', '048' )
;

#query parcelado
update integracao i
join produto_parceiro pp on i.cod_tpa = pp.cod_tpa and pp.parceiro_id = 32
join produto_parceiro_configuracao pc on pp.produto_parceiro_id = pc.produto_parceiro_id
join integracao_detalhe d on i.integracao_id = d.integracao_id and d.tipo = 'D'
set d.script_sql = 'call sp_cta_parcemissao_parcelado({apolice_id}, {apolice_status_id}, {apolice_endosso_id});'
where i.slug_group = 'parc-emissao' and i.deletado = 0 and pp.deletado = 0 and pc.deletado = 0
AND i.cod_tpa in( '022',  '026' )
;

update apolice_cobertura set valor_config= valor_config*100 where valor < 0 and deletado = 0;

/*
#QUERY PARA ALTERAR...
1 - o valor_config da apolice_cobertura
2 - código de movimento cobranca do endosso
3 - sequencial do endoss
4 - vigência do endosso

#Na integracao_detalhe, observar...
1 - com restituição o IOF é zerado na primeira parcela
2 - unir endosso x cobertura através da cobertura

*/