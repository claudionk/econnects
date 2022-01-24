USE sisconnects;
ALTER TABLE `produto_parceiro_cancelamento`
ADD COLUMN `retencao_humano` TINYINT(4) NULL DEFAULT 1 COMMENT 'Transfere para humano para retenção' AFTER `cancel_via_admin`;
