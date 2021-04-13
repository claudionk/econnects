#add tabela parceiro_id
ALTER TABLE `sisconnects`.`usuario_acl_tipo` 
ADD COLUMN `parceiro_id` INT(11) NULL AFTER `usuario_acl_tipo_id`;

#Coloca Id generali para todos os grupos atuais
UPDATE sisconnects.usuario_acl_tipo SET parceiro_id = 32;

#Id 0 para acesso externo
UPDATE `sisconnects`.`usuario_acl_tipo` SET `parceiro_id` = '0' WHERE (`usuario_acl_tipo_id` = '3');

