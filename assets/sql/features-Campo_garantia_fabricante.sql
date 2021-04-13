ALTER TABLE `sisconnects`.`cotacao_equipamento`
ADD COLUMN `garantia_fabricante` INT(11) NULL AFTER `cliente_id`;

ALTER TABLE `sisconnects`.`cotacao_generico`
ADD COLUMN `garantia_fabricante` INT(11) NULL AFTER `mei`;

ALTER TABLE `sisconnects`.`cotacao_seguro_viagem`
ADD COLUMN `garantia_fabricante` INT(11) NULL AFTER `cliente_id`;

ALTER TABLE `sisconnects`.`apolice_equipamento`
ADD COLUMN `garantia_fabricante` INT(11) NULL AFTER `numero_sorte`;

ALTER TABLE `sisconnects`.`apolice_generico`
ADD COLUMN `garantia_fabricante` INT(11) NULL AFTER `numero_sorte`;

ALTER TABLE `sisconnects`.`apolice_seguro_viagem`
ADD COLUMN `garantia_fabricante` INT(11) NULL AFTER `numero_sorte`;
