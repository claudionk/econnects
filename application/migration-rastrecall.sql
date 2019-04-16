ALTER TABLE `integracao`
  ADD COLUMN `tipo_layout` ENUM('LAYOUT','CSV') NULL DEFAULT 'LAYOUT' AFTER `slug_group`,
  ADD COLUMN `layout_separador` VARCHAR(5) NULL DEFAULT ';' AFTER `tipo_layout`;


INSERT INTO `integracao_log_detalhe_erro` (`nome`, `criacao`) VALUES ('Cancelamento em duplicidade', '2018-12-03 21:43:33');INSERT INTO `econnects`.`integracao_log_detalhe_erro` (`nome`, `criacao`) VALUES ('Cancelamento em duplicidade', '2018-12-03 21:43:33');


INSERT INTO `comunicacao_evento` (`comunicacao_tipo_id`, `nome`, `slug`, `descricao`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (2, 'OFERTA SEGURO (COTAÇÃO GERADA)', 'cotacao_gerada', 'OFERTA SEGURO (COTAÇÃO GERADA)', 0, '2019-04-15 19:53:02', 0, '2019-04-15 19:53:02');


INSERT INTO `comunicacao_template` (`comunicacao_tipo_id`, `comunicacao_engine_configuracao_id`, `descricao`, `slug`, `mensagem_titulo`, `mensagem_de`, `mensagem_anexo`, `mensagem`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (2, 3, 'OFERTA SEGURO (COTAÇÃO GERADA', 'cotacao_gerada', 'Oferta Seguro (Cotação Gerada', '-', NULL, 'Sr(a) {nome}, utiliza a seguinte URL para simular seu seguro. {url}', 0, '2019-04-15 19:56:21', 0, '2019-04-15 19:56:21');
