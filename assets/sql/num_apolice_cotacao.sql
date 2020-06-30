ALTER TABLE `sisconnects`.`produto_parceiro_configuracao` 
ADD COLUMN `gera_num_apolice_cotacao` TINYINT(1) NULL DEFAULT '0' AFTER `ir_cotacao_salva`;

ALTER TABLE `sisconnects`.`cotacao` 
ADD COLUMN `numero_apolice` VARCHAR(50) NULL AFTER `cotacao_tipo`,
ADD INDEX `idx_numero_apolice` (`numero_apolice` ASC);

ALTER TABLE `sisconnects`.`cotacao` 
DROP FOREIGN KEY `fk_cotacao_cliente1`;
ALTER TABLE `sisconnects`.`cotacao` 
CHANGE COLUMN `cliente_id` `cliente_id` INT(11) NULL ;
ALTER TABLE `sisconnects`.`cotacao` 
ADD CONSTRAINT `fk_cotacao_cliente1`
  FOREIGN KEY (`cliente_id`)
  REFERENCES `sisconnects`.`cliente` (`cliente_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
