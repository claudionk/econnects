#cria campos classe_css
ALTER TABLE `sisconnects`.`produto_parceiro_campo` 
ADD COLUMN `classe_css` VARCHAR(512) NULL AFTER `alteracao`;

