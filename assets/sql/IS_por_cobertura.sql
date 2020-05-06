
ALTER TABLE `sisconnects`.`cobertura_plano` 
ADD COLUMN `importancia_segurada` DECIMAL(10,2) NULL AFTER `porcentagem_a`;

ALTER TABLE `sisconnects`.`apolice_cobertura` 
ADD COLUMN `importancia_segurada` DECIMAL(10,2) NULL AFTER `iof`;

select pp.produto_parceiro_id, pp.nome, ppp.produto_parceiro_plano_id, ppp.nome, cp.*
from produto p
join produto_parceiro pp on p.produto_id = pp.produto_id and pp.deletado = 0
join produto_parceiro_plano ppp on pp.produto_parceiro_id = ppp.produto_parceiro_id and ppp.deletado = 0
join cobertura_plano cp on ppp.produto_parceiro_plano_id = cp.produto_parceiro_plano_id and cp.deletado = 0 and cp.parceiro_id = pp.parceiro_id 
where p.slug = 'generico' and p.deletado = 0 and pp.parceiro_id = 32
and cp.importancia_segurada is null
#and pp.produto_parceiro_id = 90
;


update produto p
join produto_parceiro pp on p.produto_id = pp.produto_id and pp.deletado = 0
join produto_parceiro_plano ppp on pp.produto_parceiro_id = ppp.produto_parceiro_id and ppp.deletado = 0
join cobertura_plano cp on ppp.produto_parceiro_plano_id = cp.produto_parceiro_plano_id and cp.deletado = 0 and cp.parceiro_id = pp.parceiro_id 
set cp.importancia_segurada = cp.preco
where p.slug = 'generico' and p.deletado = 0
and cp.importancia_segurada is null
and pp.produto_parceiro_id = 90
;



select pp.produto_parceiro_id, pp.nome, ppp.produto_parceiro_plano_id, ppp.nome, cp.importancia_segurada, ac.importancia_segurada
from produto p
join produto_parceiro pp on p.produto_id = pp.produto_id and pp.deletado = 0
join produto_parceiro_plano ppp on pp.produto_parceiro_id = ppp.produto_parceiro_id and ppp.deletado = 0
join cobertura_plano cp on ppp.produto_parceiro_plano_id = cp.produto_parceiro_plano_id and cp.deletado = 0 and cp.parceiro_id = pp.parceiro_id 
join apolice a on ppp.produto_parceiro_plano_id = a.produto_parceiro_plano_id and a.deletado = 0
join apolice_cobertura ac on a.apolice_id = ac.apolice_id and ac.deletado = 0 and cp.cobertura_plano_id = ac.cobertura_plano_id
where p.slug = 'generico' and p.deletado = 0 and pp.parceiro_id = 32
and ac.importancia_segurada is null
#and pp.produto_parceiro_id = 72
limit 10
;


update produto p
join produto_parceiro pp on p.produto_id = pp.produto_id and pp.deletado = 0
join produto_parceiro_plano ppp on pp.produto_parceiro_id = ppp.produto_parceiro_id and ppp.deletado = 0
join cobertura_plano cp on ppp.produto_parceiro_plano_id = cp.produto_parceiro_plano_id and cp.deletado = 0 and cp.parceiro_id = pp.parceiro_id 
join apolice a on ppp.produto_parceiro_plano_id = a.produto_parceiro_plano_id and a.deletado = 0
join apolice_cobertura ac on a.apolice_id = ac.apolice_id and ac.deletado = 0 and cp.cobertura_plano_id = ac.cobertura_plano_id
set ac.importancia_segurada = cp.importancia_segurada
where p.slug = 'generico' and p.deletado = 0 and pp.parceiro_id = 32
and ac.importancia_segurada is null
and pp.produto_parceiro_id = 93
;
