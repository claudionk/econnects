ALTER TABLE `integracao`
  ADD COLUMN `tipo_layout` ENUM('LAYOUT','CSV') NULL DEFAULT 'LAYOUT' AFTER `slug_group`,
  ADD COLUMN `layout_separador` VARCHAR(5) NULL DEFAULT ';' AFTER `tipo_layout`;


INSERT INTO `integracao_log_detalhe_erro` (`integracao_log_detalhe_erro_id`, `nome`, `tipo`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (18, 'Número do Celular inválido', 'E', 0, '2018-12-03 21:43:33', 0, NULL);


INSERT INTO `comunicacao_evento` (`comunicacao_tipo_id`, `nome`, `slug`, `descricao`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (2, 'OFERTA SEGURO (COTAÇÃO GERADA)', 'cotacao_gerada', 'OFERTA SEGURO (COTAÇÃO GERADA)', 0, '2019-04-15 19:53:02', 0, '2019-04-15 19:53:02');


INSERT INTO `comunicacao_template` (`comunicacao_tipo_id`, `comunicacao_engine_configuracao_id`, `descricao`, `slug`, `mensagem_titulo`, `mensagem_de`, `mensagem_anexo`, `mensagem`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (2, 3, 'OFERTA SEGURO (COTAÇÃO GERADA', 'cotacao_gerada', 'Oferta Seguro (Cotação Gerada', '-', NULL, 'Sr(a) {nome}, utiliza a seguinte URL para simular seu seguro. {url}', 0, '2019-04-15 19:56:21', 0, '2019-04-15 19:56:21');


INSERT INTO `integracao` (`integracao_id`, `parceiro_id`, `tipo`, `integracao_comunicacao_id`, `periodicidade_unidade`, `periodicidade`, `periodicidade_hora`, `proxima_execucao`, `ultima_execucao`, `nome`, `slug`, `descricao`, `script_sql`, `parametros`, `campo_chave`, `ambiente`, `host`, `porta`, `usuario`, `senha`, `diretorio`, `envia_vazio`, `habilitado`, `status`, `before_execute`, `after_execute`, `before_detail`, `after_detail`, `sequencia`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`, `slug_group`, `tipo_layout`, `layout_separador`) VALUES (97, 32, 'R', 1, 'D', 1, '01:00:00', '2019-04-16 01:00:00', '2019-04-15 22:19:19', 'RASTRECALL - OFERTA DE SEGUROS SMS', 'rastrecall-sms', 'RASTRECALL - OFERTA DE SEGUROS SMS', NULL, '0', 'cotacao_id', 'H', 'ftp.zazz.com.br', 21, 'zazz', 'vruReLsZFrWDWQhhCBsV', '/RASTRECALL/SMS/', 0, 1, 'A', '', '', 'app_integracao_rastrecall_sms', '', 0, 0, '2018-08-23 20:02:44', 0, '2019-04-15 22:19:19', 'ems-emissao', 'CSV', ';');


INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 68, 'F', 0, 'NOMENCLATURA DO ARQUIVO:', 'NOMENCLATURA DO ARQUIVO:', '', 'C', 34, 1, 0, 0, 1, 30, NULL, '', 'teste-arquivo.csv', 0, 0, 0, '2018-08-19 22:15:07', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 66, 'D', 0, 'NOME_FANTASIA\r\n', 'Nome Estipulante', '', 'C', 100, 0, 0, 0, 1, 99, '', '', '', 0, 0, 0, '2018-09-01 17:01:40', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 66, 'D', 1, 'NOTA_FISCAL\r\n', 'NUMERO DA NOTA FISCAL\r\n', '', NULL, 10, 0, 1, 1, 725, 734, 'nota_fiscal_numero', '', '0', 0, 0, 0, '2018-02-15 20:04:04', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 66, 'D', 2, 'SERIE\r\n', 'SERIE DA NOTA FISCAL\r\n', '', NULL, 1, 0, 0, 0, 725, 734, 'nota_fiscal_serie', '', '0', 0, 0, 0, '2018-02-15 20:04:04', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 66, 'D', 3, 'DATA_EMISSAO\r\n', 'Data de referência referente a emissão da apólice ou endosso AAAAMMDD', '', 'C', 8, 1, 0, 1, 119, 126, 'nota_fiscal_data', '', ' ', 8, 0, 0, '2018-08-23 21:31:07', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 66, 'D', 4, 'NOME_CLIENTE\r\n', 'Nome Cliente', '', 'C', 100, 0, 0, 1, 1, 99, 'nome', '', '', 0, 0, 0, '2018-09-01 17:01:40', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 66, 'D', 5, 'CPF_CNPJ\r\n', 'CPF="11111111111111","CNPJ=11111111111111"\r\n', '', NULL, 14, 0, 1, 1, 95, 108, 'cnpj_cpf', '', '0', 14, 0, 0, '2018-02-14 15:10:21', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 66, 'D', 6, 'RG_IE', 'RG ou IE', '', 'C', 20, 0, 0, 0, 440, 447, '', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 66, 'D', 7, 'DATA_NASC_CLIENTE\r\n', 'Data de nascimento do Beneficiario DD/mm/YYYY', 'd/m/Y', 'D', 10, 0, 0, 1, 440, 447, 'data_nascimento', '', ' ', 10, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 8, 'CEP\r\n', 'CEP do beneficiario', '', 'C', 10, 1, 0, 1, 410, 419, 'endereco_cep', '', ' ', 10, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 9, 'ENDERECO\r\n', 'Endereco', '', 'C', 100, 1, 0, 1, 410, 419, 'endereco_logradouro', '', ' ', 100, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 10, 'NUMERO\r\n', 'Endereco Numero', '', 'C', 20, 1, 0, 1, 410, 419, 'endereco_numero', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 11, 'COMPLEMENTO\r\n', 'Complemento', '', 'C', 20, 1, 0, 1, 410, 419, 'endereco_complemento', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 12, 'BAIRRO\r\n', 'Bairro', '', 'C', 100, 1, 0, 1, 410, 419, 'endereco_bairro', '', ' ', 100, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 13, 'CIDADE\r\n', 'Cidade', '', 'C', 100, 1, 0, 1, 410, 419, 'endereco_cidade', '', ' ', 100, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 14, 'UF', 'UF', '', 'C', 2, 1, 0, 1, 410, 419, 'endereco_estado', '', ' ', 2, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 15, 'FONE_CLIENTE\r\n', 'Telefone', '', 'C', 20, 0, 0, 0, 410, 419, '', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 16, 'CELULAR_CLIENTE\r\n', 'Celular', '', 'C', 20, 1, 0, 1, 410, 419, 'telefone', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 17, 'EMAIL_CLIENTE\r\n', 'Email', '', 'C', 100, 0, 0, 1, 410, 419, 'email', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 18, 'DESCRICAO\r\n', 'modelo', '', 'C', 100, 0, 0, 1, 410, 419, 'equipamento', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 19, 'CATEGORIA\r\n', 'Categoria', '', 'C', 100, 0, 0, 1, 410, 419, 'equipamento_categoria', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 20, 'MARCA\r\n', 'Marca', '', 'C', 100, 0, 0, 1, 410, 419, 'equipamento_marca', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 21, 'CODEBAR\r\n', 'CODEBAR', '', 'C', 100, 0, 0, 1, 410, 419, 'equipamento_codigo_barra', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 22, 'IMEI\r\n', 'IMEI', '', 'C', 100, 0, 0, 1, 410, 419, 'equipamento_imei', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');
INSERT INTO `integracao_layout` (`integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `nome_banco`, `function`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (97, 357, 'D', 23, 'PRECO_VENDA_LIQ\r\n', 'VALOR NF', '', 'C', 100, 0, 0, 1, 410, 419, 'nota_fiscal_valor', '', ' ', 20, 0, 0, '2018-10-18 14:23:12', 0, '0000-00-00 00:00:00');


-- 13;06;2019

ALTER TABLE `produto_parceiro_configuracao`
  ADD COLUMN `ir_cotacao_salva` TINYINT(1) NULL DEFAULT '0' AFTER `endosso_controle_cliente`;

ALTER TABLE `comunicacao`
  ADD COLUMN `cotacao_id` INT NULL DEFAULT '0' AFTER `chave`;


CREATE TABLE `comunicacao_agendamento` (
  `comunicacao_agendamento_id` INT(11) NOT NULL AUTO_INCREMENT,
  `produto_parceiro_comunicacao_id` INT(11) NOT NULL,
  `comunicacao_status_id` INT(11) NOT NULL,
  `mensagem_from` VARCHAR(100) NULL DEFAULT NULL,
  `mensagem_from_name` VARCHAR(100) NULL DEFAULT NULL,
  `mensagem_to` VARCHAR(100) NULL DEFAULT NULL,
  `mensagem_to_name` VARCHAR(100) NULL DEFAULT NULL,
  `mensagem_anexo` VARCHAR(512) NULL DEFAULT NULL,
  `mensagem` TEXT NULL,
  `data_enviar` TIMESTAMP NULL DEFAULT NULL,
  `data_envio` TIMESTAMP NULL DEFAULT NULL,
  `retorno` VARCHAR(512) NULL DEFAULT NULL,
  `retorno_codigo` VARCHAR(100) NULL DEFAULT NULL,
  `tabela` VARCHAR(100) NULL DEFAULT NULL,
  `campo` VARCHAR(100) NULL DEFAULT NULL,
  `chave` VARCHAR(100) NULL DEFAULT NULL,
  `cotacao_id` INT(11) NULL DEFAULT '0',
  `deletado` TINYINT(4) NULL DEFAULT '0',
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`comunicacao_agendamento_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
  AUTO_INCREMENT=1
;

CREATE TABLE `comunicacao_track` (
  `comunicacao_track_id` INT(11) NOT NULL AUTO_INCREMENT,
  `comunicacao_id` INT(11) NOT NULL,
  `data_hora` TIMESTAMP NULL DEFAULT NULL,
  `deletado` TINYINT(4) NULL DEFAULT '0',
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`comunicacao_track_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
;


CREATE TABLE `comunicacao_automatico` (
  `comunicacao_automatico_id` INT(11) NOT NULL AUTO_INCREMENT,
  `proxima_execucao` TIMESTAMP NULL DEFAULT NULL,
  `melhor_horario` VARCHAR(5) NULL DEFAULT '16:00',
  `quantidade` INT(11) NULL DEFAULT '200',
  `somente_dia_util` TINYINT(4) NULL DEFAULT '0',
  `deletado` TINYINT(4) NULL DEFAULT '0',
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`comunicacao_automatico_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
;


INSERT INTO `comunicacao_automatico` (`comunicacao_automatico_id`, `proxima_execucao`, `melhor_horario`, `quantidade`, `somente_dia_util`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (1, '2019-06-17 09:57:00', '09:57', 200, 1, 0, '2019-06-14 19:33:32', 0, '2019-06-14 17:40:07');

-- 24/06/2019

CREATE TABLE `enquete` (
  `enquete_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(64) NULL DEFAULT NULL,
  `titulo` VARCHAR(128) NULL DEFAULT NULL,
  `texto_inicial` MEDIUMTEXT NULL,
  `texto_final` MEDIUMTEXT NULL,
  `data_corte` DATETIME NULL DEFAULT NULL,
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  `deletado` TINYINT(4) NULL DEFAULT '0',
  PRIMARY KEY (`enquete_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
  AUTO_INCREMENT=1
;


CREATE TABLE `enquete_configuracao` (
  `enquete_configuracao_id` INT(11) NOT NULL AUTO_INCREMENT,
  `enquete_id` INT(11) NOT NULL DEFAULT '0',
  `envio_tipo` ENUM('sms','email') NULL DEFAULT NULL,
  `envio_mensagem` TEXT NULL,
  `ativo` TINYINT(4) NULL DEFAULT '0',
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  `deletado` TINYINT(4) NULL DEFAULT '0',
  PRIMARY KEY (`enquete_configuracao_id`),
  INDEX `FK_enquete_configuracao_enquete` (`enquete_id`),
  CONSTRAINT `FK_enquete_configuracao_enquete` FOREIGN KEY (`enquete_id`) REFERENCES `enquete` (`enquete_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
  AUTO_INCREMENT=1
;


CREATE TABLE `enquete_gatilho` (
  `enquete_gatilho_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(64) NULL DEFAULT NULL,
  `parametro` VARCHAR(512) NULL DEFAULT NULL,
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  `deletado` TINYINT(4) NULL DEFAULT '0',
  PRIMARY KEY (`enquete_gatilho_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
  AUTO_INCREMENT=1
;


CREATE TABLE `enquete_gatilho_configuracao` (
  `enquete_gatilho_configuracao_id` INT(11) NOT NULL AUTO_INCREMENT,
  `enquete_gatilho_id` INT(11) NOT NULL DEFAULT '0',
  `enquete_configuracao_id` INT(11) NOT NULL DEFAULT '0',
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  `deletado` TINYINT(4) NULL DEFAULT '0',
  PRIMARY KEY (`enquete_gatilho_configuracao_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
  AUTO_INCREMENT=1
;


CREATE TABLE `enquete_pergunta` (
  `enquete_pergunta_id` INT(11) NOT NULL AUTO_INCREMENT,
  `enquete_id` INT(11) NULL DEFAULT NULL,
  `tipo` ENUM('texto','select','multiselect','sim_nao','zero_a_dez') NULL DEFAULT NULL,
  `pergunta` VARCHAR(256) NULL DEFAULT NULL,
  `opcoes` VARCHAR(2048) NULL DEFAULT NULL,
  `ordem` INT(11) NULL DEFAULT NULL,
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  `deletado` TINYINT(4) NULL DEFAULT '0',
  PRIMARY KEY (`enquete_pergunta_id`),
  INDEX `FK_enquete_pergunta_enquete` (`enquete_id`),
  CONSTRAINT `FK_enquete_pergunta_enquete` FOREIGN KEY (`enquete_id`) REFERENCES `enquete` (`enquete_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
  AUTO_INCREMENT=1
;

CREATE TABLE `enquete_resposta` (
  `enquete_resposta_id` INT(11) NOT NULL AUTO_INCREMENT,
  `enquete_id` INT(11) NOT NULL DEFAULT '0',
  `enquete_configuracao_id` INT(11) NOT NULL DEFAULT '0',
  `apolice_id` INT(11) NULL DEFAULT NULL,
  `id_resposta_api` VARCHAR(255) NULL DEFAULT NULL,
  `tentativas_envio` INT(11) NULL DEFAULT '0',
  `respondido` ENUM('nao','parcial','total','erro') NULL DEFAULT NULL,
  `data_enviada` DATETIME NULL DEFAULT NULL,
  `data_respondido` DATETIME NULL DEFAULT NULL,
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  `deletado` TINYINT(4) NULL DEFAULT '0',
  `locked` TINYINT(1) NULL DEFAULT '0',
  `ultimo_erro` VARCHAR(100) NULL DEFAULT '0',
  PRIMARY KEY (`enquete_resposta_id`),
  INDEX `apolice_id` (`apolice_id`),
  INDEX `FK_enquete_resposta_enquete` (`enquete_id`),
  CONSTRAINT `FK_enquete_resposta_enquete` FOREIGN KEY (`enquete_id`) REFERENCES `enquete` (`enquete_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
  AUTO_INCREMENT=1
;

CREATE TABLE `enquete_resposta_pergunta` (
  `enquete_resposta_pergunta_id` INT(11) NOT NULL AUTO_INCREMENT,
  `enquete_resposta_id` INT(11) NOT NULL DEFAULT '0',
  `enquete_pergunta_id` INT(11) NOT NULL DEFAULT '0',
  `resposta` VARCHAR(256) NULL DEFAULT '0',
  `respondida` TINYINT(1) NULL DEFAULT '0',
  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `alteracao_usuario_id` INT(11) NULL DEFAULT '0',
  `alteracao` TIMESTAMP NULL DEFAULT NULL,
  `deletado` TINYINT(4) NULL DEFAULT '0',
  PRIMARY KEY (`enquete_resposta_pergunta_id`),
  INDEX `FK_enquete_resposta_pergunta_enquete_resposta` (`enquete_resposta_id`),
  INDEX `FK_enquete_resposta_pergunta_enquete_pergunta` (`enquete_pergunta_id`),
  CONSTRAINT `FK_enquete_resposta_pergunta_enquete_pergunta` FOREIGN KEY (`enquete_pergunta_id`) REFERENCES `enquete_pergunta` (`enquete_pergunta_id`),
  CONSTRAINT `FK_enquete_resposta_pergunta_enquete_resposta` FOREIGN KEY (`enquete_resposta_id`) REFERENCES `enquete_resposta` (`enquete_resposta_id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
  AUTO_INCREMENT=1
;


ALTER TABLE `produto_parceiro_configuracao`
  ADD COLUMN `enquete_id` INT NULL DEFAULT '0' AFTER `ir_cotacao_salva`;