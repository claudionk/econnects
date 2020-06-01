#1 - migrar planos
select * from produto_parceiro_plano where produto_parceiro_plano_id IN(168,169,170,171) and deletado = 0;

#2 - migrar coberturas
select * from cobertura_plano where produto_parceiro_plano_id IN(168,169,170,171) and deletado = 0;

#3- migrar tabela de preço
select * from produto_parceiro_plano_precificacao_itens where produto_parceiro_plano_id IN(168,169,170,171) and deletado = 0;

