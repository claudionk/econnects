ALTER TABLE `sisconnects`.`apolice_cobertura` 
ADD COLUMN `data_inicio_vigencia` DATE NULL DEFAULT NULL AFTER `cod_sucursal`,
ADD COLUMN `data_fim_vigencia` DATE NULL DEFAULT NULL AFTER `data_ini_vigencia`;

ALTER TABLE `sisconnects`.`cotacao_cobertura` 
ADD COLUMN `data_inicio_vigencia` DATE NULL DEFAULT NULL AFTER `iof`,
ADD COLUMN `data_fim_vigencia` DATE NULL DEFAULT NULL AFTER `data_ini_vigencia`;
