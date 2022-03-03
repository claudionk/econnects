USE sisconnects;
/*Backup da TAbela Original*/
CREATE TABLE bkp_produto_parceiro_termo
SELECT * FROM produto_parceiro_termo;
/*Script de alteração da estrutura*/
ALTER TABLE produto_parceiro_termo 
ADD COLUMN termo_data_ini TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00' AFTER termo,
ADD COLUMN termo_data_fim TIMESTAMP NULL AFTER termo_data_ini,
ADD UNIQUE INDEX uk_produto_parceiro_termo (produto_parceiro_id ASC, slug ASC, termo_data_ini ASC);
;