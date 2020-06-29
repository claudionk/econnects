ALTER TABLE `sisconnects`.`produto_parceiro_configuracao` 
ADD COLUMN `gera_num_apolice_cotacao` TINYINT(1) NULL DEFAULT '0' AFTER `ir_cotacao_salva`;

ALTER TABLE `sisconnects`.`cotacao` 
ADD COLUMN `numero_apolice` VARCHAR(50) NULL AFTER `cotacao_tipo`;

ALTER TABLE `sisconnects`.`cotacao` 
ADD INDEX `idx_numero_apolice` (`numero_apolice` ASC);
