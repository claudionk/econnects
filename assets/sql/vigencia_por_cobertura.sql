ALTER TABLE `sisconnects`.`apolice_cobertura` 
ADD COLUMN `data_inicio_vigencia` DATE NULL DEFAULT NULL AFTER `cod_sucursal`,
ADD COLUMN `data_fim_vigencia` DATE NULL DEFAULT NULL AFTER `data_ini_vigencia`;

ALTER TABLE `sisconnects`.`cotacao_cobertura` 
ADD COLUMN `data_inicio_vigencia` DATE NULL DEFAULT NULL AFTER `iof`,
ADD COLUMN `data_fim_vigencia` DATE NULL DEFAULT NULL AFTER `data_ini_vigencia`;

ALTER TABLE `sisconnects`.`apolice_endosso` 
ADD COLUMN `cod_cobertura` VARCHAR(15) NULL AFTER `data_vencimento`;

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