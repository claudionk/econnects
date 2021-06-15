USE sisconnects;
CREATE TABLE parceiro_relacionamento_produto_vigencia (
  parceiro_relacionamento_produto_vigencia_id int(11) NOT NULL AUTO_INCREMENT,
  parceiro_relacionamento_produto_id int(11) NOT NULL,
  comissao_data_ini timestamp NOT NULL,
  comissao_data_fim timestamp NULL DEFAULT NULL,
  repasse_comissao tinyint(4) DEFAULT '0',
  repasse_maximo decimal(15,3) DEFAULT NULL,
  comissao_tipo tinyint(4) DEFAULT '0',
  comissao decimal(15,3) DEFAULT NULL,
  comissao_indicacao decimal(15,3) DEFAULT NULL,
  deletado tinyint(4) DEFAULT '0',
  criacao timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  alteracao_usuario_id int(11) DEFAULT '0',
  alteracao timestamp NULL DEFAULT NULL,
  PRIMARY KEY (parceiro_relacionamento_produto_vigencia_id),
  KEY idx_comissao_data_ini (comissao_data_ini),
  KEY idx_comissao_data_fim (comissao_data_fim),
  CONSTRAINT fk_parceiro_relacionamento_produto_vigencia FOREIGN KEY (parceiro_relacionamento_produto_id) REFERENCES parceiro_relacionamento_produto (parceiro_relacionamento_produto_id) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE parceiro_relacionamento_produto_vigencia 
ADD UNIQUE INDEX uk_parceiro_relacionamento_produto_vigencia (comissao_data_ini ASC, parceiro_relacionamento_produto_id ASC);
;

INSERT INTO parceiro_relacionamento_produto_vigencia
SELECT NULL, 
parceiro_relacionamento_produto_id, 
'2000-01-01' AS comissao_data_ini, 
NULL AS comissao_data_fim, 
repasse_comissao, 
repasse_maximo, 
comissao_tipo, 
comissao, 
comissao_indicacao, 
deletado, 
criacao, 
alteracao_usuario_id, 
alteracao
FROM sisconnects.parceiro_relacionamento_produto;