# Altera o valor retornado do BD para determinar PF / PJ
UPDATE `sisconnects`.`integracao_layout` SET `nome_banco`='tipo_doc', `valor_padrao`= ' ' where integracao_id IN(113,116) and tipo = 'D' and deletado = 0 and nome = 'TIPO';

# ADD no script SQL da tabela integracao
#select * from integracao where parceiro_id in(72,76) and slug_group = 'cliente'; #113 e 116
IF(cliente.tipo_cliente='CF', 'F', 'J') as tipo_doc,