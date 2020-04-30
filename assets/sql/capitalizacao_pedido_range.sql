ALTER TABLE `sisconnects`.`capitalizacao` 
CHANGE COLUMN `data_inicio` `data_inicio` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `data_fim` `data_fim` DATE NULL DEFAULT NULL 
ADD COLUMN `codigo_interno` VARCHAR(10) NULL AFTER `data_fim`,
ADD COLUMN `responsavel_num_sorte_distribuicao` TINYINT(4) NULL DEFAULT '0' COMMENT '0 - Sistema\n1 - Parceiro' AFTER `responsavel_num_sorte`;


/*
#Atualizar o campo codigo_interno e nome conforme descrito abaixo
Cód Marco Regulatório Sul América	Código Icatu	Nome do Produto	Nome do Estipulante
5559	8976	PROTECAO PREMIADA	NOVO MUNDO
5725	9334	SEG RESIDENCIAL QQ	QUERO QUERO
5637	9335	SEG RESIDENCIAL QQ	QUERO QUERO
5547	8968	SEGUROS POMPEIA	LOJAS POMPÉIA
5532	9309	SEG PROT MACAVI - S	MACAVI
5548	9311	SEGURO RESID RENAULT	RENAULT

#Popular o campo responsavel_num_sorte_distribuicao definindo os casos em que o parceiro envia o número da sorte
#Apenas RCI e Uniqq são entregues pelo sistema
*/

ALTER TABLE `sisconnects`.`produto_parceiro_plano` 
ADD COLUMN `capitalizacao_id` INT NULL AFTER `qtd_min_vida`,
CHANGE COLUMN `deletado` `deletado` TINYINT(4) NULL DEFAULT '0' AFTER `capitalizacao_id`,
CHANGE COLUMN `criacao` `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `deletado`,
CHANGE COLUMN `criacao_usuario_id` `criacao_usuario_id` INT(11) NULL DEFAULT '0' AFTER `criacao`,
CHANGE COLUMN `alteracao` `alteracao` TIMESTAMP NULL DEFAULT NULL AFTER `criacao_usuario_id`,
CHANGE COLUMN `alteracao_usuario_id` `alteracao_usuario_id` INT(11) NULL DEFAULT '0' AFTER `alteracao`,
CHANGE COLUMN `unidade_limite_tempo` `unidade_limite_tempo` ENUM('', 'DIA', 'MES', 'ANO') NULL DEFAULT NULL;

ALTER TABLE `sisconnects`.`capitalizacao_serie` 
CHANGE COLUMN `serie_aberta` `serie_aberta` TINYINT(4) NULL DEFAULT '0' AFTER `data_fim`,
ADD COLUMN `num_serie` TINYINT NULL AFTER `serie_aberta`,
ADD COLUMN `solicita_range` VARCHAR(45) NULL DEFAULT 0 COMMENT '0 - Nao solicita\n1 - Solicitar\n2 - Solicitado - Ag Retorno' AFTER `ativo`;

#Atualiza a Série para 1 de todas as operações vigentes
update capitalizacao_serie set num_serie = 1 where deletado = 0;

#cria um novo tipo
INSERT INTO `sisconnects`.`capitalizacao_tipo` (`capitalizacao_tipo_id`, `nome`, `slug`, `deletado`, `criacao`, `alteracao_usuario_id`) VALUES (4, 'ICATU SEGURSOS S/A', 'icatu', '0', '2020-04-24 10:38:25', '0');

#alterar o tipo de capitalizacao para Icatu
UPDATE `sisconnects`.`capitalizacao` SET `capitalizacao_tipo_id`='4' WHERE `capitalizacao_id`= ?;


##INSTALACAO DO SSH2 NA APLICAÇÃO
/*
php -m | grep ssh2
sudo yum install autoconf
phpize
sudo pecl install -f ssh2
echo extension=ssh2.so > /etc/php.d/ssh2.ini
sudo service httpd restart
php -m | grep ssh2

*/

#migrar as integrações de pedido e retorno de pedido
select * from integracao where integracao_id IN(102, 218) and deletado = 0;
select * from integracao_detalhe where integracao_id IN(102, 218) and deletado = 0;
select * from integracao_layout where integracao_id IN(102, 218) and deletado = 0;

#ATRIBUIR A CAPITALIZAÇÃO AOS PLANOS DA QUERO QUERO
select * from produto_parceiro_plano where produto_parceiro_id = 90 AND deletado = 0;


ALTER TABLE `sisconnects`.`cotacao_equipamento` 
ADD COLUMN `num_proposta_capitalizacao` VARCHAR(50) NULL DEFAULT NULL AFTER `numero_sorte`;

ALTER TABLE `sisconnects`.`cotacao_generico` 
ADD COLUMN `num_proposta_capitalizacao` VARCHAR(50) NULL DEFAULT NULL AFTER `numero_sorte`;

ALTER TABLE `sisconnects`.`cotacao_seguro_viagem` 
ADD COLUMN `num_proposta_capitalizacao` VARCHAR(50) NULL DEFAULT NULL AFTER `numero_sorte`;

ALTER TABLE `sisconnects`.`integracao` 
CHANGE COLUMN `cod_tpa` `cod_tpa` VARCHAR(4) NULL DEFAULT NULL ;

#INFORMAR O COD DO PRODUTO NO CAMPO COD_TPA DO RESULTADO ABAIXO
#select * from integracao where slug_group = 'sulacap-ativacao' and deletado = 0;

ALTER TABLE `sisconnects`.`integracao_log_detalhe_api` 
CHANGE COLUMN `retorno_amigavel` `retorno_amigavel` TEXT NULL DEFAULT NULL ;
