CREATE PROCEDURE `sp_cta_parcemissao_unico`(IN _apolice_id INT, IN _apolice_status_id INT, IN _apolice_endosso_id INT)
BEGIN

select *, premio_liquido + valor_iof as premio_liquido_total
from (

select 
apolice_cobertura.apolice_cobertura_id
, cobertura_plano.cod_cobertura
, ROUND(apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1), 2) AS premio_liquido

#se o IOF é menor que 0.01, joga o valor na maior
, IF(_apolice_status_id = 2 AND apolice_aux.valor_premio_total <> apolice_aux.valor_estorno, 0, IF(
		TRUNCATE(IF(rp.regra_preco_id IS NOT NULL, (apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1)) * IFNULL(pprp.parametros,0) / 100, IF(apolice_cobertura.iof > 0, (apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1)) * apolice_cobertura.iof / 100, 0) ),2)

		#add a diferenca do IOF total à cobertura de +valor
		+ IF( menor.apolice_cobertura_id = apolice_cobertura.apolice_cobertura_id, menor.valor-menor.valor_t, 0) = 0,

			TRUNCATE(IF( menor.apolice_cobertura_id = apolice_cobertura.apolice_cobertura_id, IF( TRUNCATE(menor.valor, 2) = 0, 0.01, menor.valor), 0), 2)
		,
			TRUNCATE(IF(rp.regra_preco_id IS NOT NULL, (apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1)) * IFNULL(pprp.parametros,0) / 100, IF(apolice_cobertura.iof > 0, (apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1)) * apolice_cobertura.iof / 100, 0) ),2)

			#add a diferenca do IOF total à cobertura de +valor
			+ IF( menor.apolice_cobertura_id = apolice_cobertura.apolice_cobertura_id, menor.valor-menor.valor_t, 0)
)) AS valor_iof

, IF(apolice_aux.slug_table = 'generico', apolice_cobertura.importancia_segurada, apolice_aux.nota_fiscal_valor) nota_fiscal_valor
, apolice_cobertura.data_inicio_vigencia AS ini_vig
, apolice_cobertura.data_fim_vigencia AS fim_vig

FROM pedido
INNER JOIN apolice on apolice.pedido_id = pedido.pedido_id
INNER JOIN produto_parceiro_plano ON apolice.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id
INNER JOIN produto_parceiro ON produto_parceiro_plano.produto_parceiro_id = produto_parceiro.produto_parceiro_id
INNER JOIN parceiro ON produto_parceiro.parceiro_id = parceiro.parceiro_id

LEFT JOIN produto_parceiro_regra_preco pprp ON produto_parceiro_plano.produto_parceiro_id = pprp.produto_parceiro_id AND pprp.deletado = 0
LEFT JOIN regra_preco rp on pprp.regra_preco_id = rp.regra_preco_id AND rp.slug = 'iof' 

INNER JOIN apolice_cobertura on apolice.apolice_id = apolice_cobertura.apolice_id

#APENAS COBERTURAS (SEM ASSISTENCIAS)
INNER JOIN cobertura_plano ON apolice_cobertura.cobertura_plano_id = cobertura_plano.cobertura_plano_id AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id
INNER JOIN apolice_endosso ON apolice.apolice_id = apolice_endosso.apolice_id AND apolice_endosso.apolice_movimentacao_tipo_id = _apolice_status_id
	AND (apolice_endosso.cod_cobertura IS NULL OR apolice_cobertura.cod_cobertura = apolice_endosso.cod_cobertura)
INNER JOIN (
	select apolice_id, valor_premio_net, pro_labore, valor_premio_total, valor_estorno, nota_fiscal_valor, 'equipamento' slug_table from apolice_equipamento where apolice_id = _apolice_id
	union 
	select apolice_id, valor_premio_net, pro_labore, valor_premio_total, valor_estorno, nota_fiscal_valor, 'generico' slug_table from apolice_generico where apolice_id = _apolice_id
) apolice_aux ON apolice_aux.apolice_id = apolice.apolice_id

#caso o IOF seja menor que 0.01, soma as comissoes e identifica a de maior valor
LEFT JOIN (
	SELECT apolice_id, max(apolice_cobertura_id) as apolice_cobertura_id, max(sequencial) sequencial, valor, valor_t
	FROM (
		SELECT apolice.apolice_id, ac.apolice_cobertura_id, x.sequencial, x.regra_preco_id, x.status_
			, IF( ROUND(IF(x.regra_preco_id IS NOT NULL, x.valor_por_cob, x.valor), 2) = 0, 0.01, IF(x.regra_preco_id IS NOT NULL, x.valor_por_cob, x.valor)) as valor
			, IF( ROUND(x.valor_t, 2) = 0, 0.01, x.valor_t) as valor_t
		FROM apolice_cobertura ac
		JOIN (
			SELECT apolice.apolice_id, max(apolice_endosso.sequencial) sequencial, rp.regra_preco_id, IF(apolice_cobertura.valor > 0, 1, 2) status_,
				round(sum(IF(apolice_endosso.valor = 0, 0, IF(rp.regra_preco_id IS NOT NULL,  

					(apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1)) * IFNULL(pprp.parametros,0) / 100
					,
					IF(apolice_cobertura.iof > 0, (apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1)) * apolice_cobertura.iof / 100, 0)
					)
				)), 2) as valor
				, round(sum(TRUNCATE(IF(apolice_endosso.valor = 0, 0, IF(rp.regra_preco_id IS NOT NULL,  

					(apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1)) * IFNULL(pprp.parametros,0) / 100
					,
					IF(apolice_cobertura.iof > 0, (apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1)) * apolice_cobertura.iof / 100, 0)
					))
				,2)), 2) as valor_t
				, round(sum(
					IF(rp.regra_preco_id IS NOT NULL, 
						#IF(apolice_endosso.valor = 0, 0, IFNULL(apolice_aux.pro_labore,0)) * IF(_apolice_status_id = 1, 1, -1)

                        IF(apolice_endosso.valor = 0, 0, 
							apolice_aux.pro_labore / apolice_aux.valor_premio_net * (apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1))
                        )

						, 
						(TRUNCATE(
							IF(apolice_endosso.valor = 0,
							0
							, 
							IF(apolice_cobertura.iof > 0, (apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1)) * apolice_cobertura.iof / 100, 0)
						),2))
					)), 2) as valor_por_cob
				, max(IF(cobertura_plano.cobertura_plano_id IS NOT NULL, apolice_cobertura.valor, 0)) c 
				, min(IF(cobertura_plano.cobertura_plano_id IS NOT NULL, apolice_cobertura.valor, 0)) d 

			FROM pedido
			INNER JOIN apolice ON apolice.pedido_id = pedido.pedido_id
			INNER JOIN apolice_cobertura ON apolice.apolice_id = apolice_cobertura.apolice_id
			INNER JOIN produto_parceiro_plano ppp ON apolice.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
			INNER JOIN produto_parceiro ON ppp.produto_parceiro_id = produto_parceiro.produto_parceiro_id
			INNER JOIN apolice_endosso ON apolice_endosso.apolice_id = apolice.apolice_id AND apolice_endosso.apolice_movimentacao_tipo_id = 1
				AND (apolice_endosso.cod_cobertura IS NULL OR apolice_cobertura.cod_cobertura = apolice_endosso.cod_cobertura)
			LEFT JOIN produto_parceiro_regra_preco pprp ON ppp.produto_parceiro_id = pprp.produto_parceiro_id AND pprp.deletado = 0
			LEFT JOIN regra_preco rp on pprp.regra_preco_id = rp.regra_preco_id AND rp.slug = 'iof' 
			INNER JOIN (
				select apolice_id, valor_premio_net, pro_labore, valor_premio_total, valor_estorno, nota_fiscal_valor, 'equipamento' slug_table from apolice_equipamento where apolice_id = _apolice_id
				union 
				select apolice_id, valor_premio_net, pro_labore, valor_premio_total, valor_estorno, nota_fiscal_valor, 'generico' slug_table from apolice_generico where apolice_id = _apolice_id
            ) apolice_aux ON apolice_aux.apolice_id = apolice.apolice_id

			#APENAS COBERTURAS (SEM ASSISTENCIAS)
			INNER JOIN cobertura_plano ON apolice_cobertura.cobertura_plano_id = cobertura_plano.cobertura_plano_id AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id

			WHERE apolice.apolice_id = _apolice_id
				AND pedido.deletado = 0
				AND apolice.deletado = 0
				AND apolice_cobertura.deletado = 0
                AND apolice_endosso.deletado = 0
			GROUP BY apolice.apolice_id, IF(apolice_cobertura.valor > 0, 1, 2)

		) x ON x.apolice_id = ac.apolice_id 
			AND IF(_apolice_status_id = 1, x.c, x.d) = ac.valor
		INNER JOIN apolice ON apolice.apolice_id = ac.apolice_id
		WHERE x.status_ = _apolice_status_id

	) z  GROUP BY apolice_id
) AS menor ON apolice.apolice_id = menor.apolice_id

where 
pedido.deletado = 0
and apolice.deletado = 0
and apolice_cobertura.deletado = 0
and cobertura_plano.deletado = 0
AND apolice_endosso.deletado = 0
and apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1) > 0
and apolice.apolice_id = _apolice_id
AND (apolice_endosso.apolice_endosso_id = _apolice_endosso_id OR _apolice_endosso_id IS NULL)

) as y;


END