ALTER TABLE `sisconnects`.`capitalizacao` 
CHANGE COLUMN `data_inicio` `data_inicio` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `data_fim` `data_fim` DATE NULL DEFAULT NULL 
ADD COLUMN `codigo_interno` VARCHAR(10) NULL AFTER `data_fim`;


/*
#Atualizar o campo codigo_interno e nome conforme descrito abaixo
Cód Marco Regulatório Sul América	Código Icatu	Nome do Produto	Nome do Estipulante
5559	8976	PROTECAO PREMIADA	NOVO MUNDO
5725	9334	SEG RESIDENCIAL QQ	QUERO QUERO
5637	9335	SEG RESIDENCIAL QQ	QUERO QUERO
5547	8968	SEGUROS POMPEIA	LOJAS POMPÉIA
5532	9309	SEG PROT MACAVI - S	MACAVI
5548	9311	SEGURO RESID RENAULT	RENAULT
*/

ALTER TABLE `sisconnects`.`capitalizacao_serie` 
CHANGE COLUMN `serie_aberta` `serie_aberta` TINYINT(4) NULL DEFAULT '0' AFTER `data_fim`,
ADD COLUMN `num_serie` TINYINT NULL AFTER `serie_aberta`,
ADD COLUMN `solicita_range` VARCHAR(45) NULL DEFAULT 0 COMMENT '0 - Nao solicita\n1 - Solicitar\n2 - Solicitado - Ag Retorno' AFTER `ativo`;

#Atualiza a Série para 1 de todas as operações vigentes
update capitalizacao_serie set num_serie = 1 where deletado = 0;
