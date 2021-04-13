USE sisconnects;
ALTER TABLE `produto_parceiro_plano_precificacao_itens` 
ADD COLUMN `dt_inicio_vigencia` DATE NULL DEFAULT NULL COMMENT 'Inicio de vigência do plano de precificacao' AFTER `deletado`,
ADD COLUMN `dt_final_vigencia` DATE NULL DEFAULT NULL COMMENT 'Final de vigência do plano de precificacao' AFTER `dt_inicio_vigencia`;
