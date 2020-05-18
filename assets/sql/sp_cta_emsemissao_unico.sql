CREATE PROCEDURE `sp_cta_emsemissao_unico`(IN _apolice_id INT, IN _apolice_status_id INT, IN _apolice_endosso_id INT)
BEGIN

SELECT x.cod_cobertura, ROUND(x.premio_liquido + IFNULL(y.dif_liq,0), 2) AS premio_liquido, x.parceiro, x.cd_tipo_comissao, IF(ROUND(x.valor_comissao + IFNULL(y.dif_comissao,0), 2) = 0 AND y.apolice_id = x.apolice_id, 0.01, ROUND(x.valor_comissao + IFNULL(y.dif_comissao,0), 2)) AS valor_comissao, x.pc_comissao, x.cod_corretor#, y.*
FROM (
	select 

	apolice.apolice_id
    , apolice_endosso.parcela
    , apolice_cobertura.valor AS valor_cob
	, cobertura_plano.cod_cobertura
	, ROUND(apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1), 2) AS premio_liquido
	, parceiro.nome as parceiro
	, IF(IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id) = 2, 'C', IF(IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id) = 3, 'P', IF(IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id) = 4, 'R', ''))) AS cd_tipo_comissao
	, ROUND(comissao_gerada.comissao / 100 * ROUND(apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1), 2), 2) AS valor_comissao
	, ROUND(comissao_gerada.comissao, 4) as pc_comissao
	, IFNULL(comissao_gerada.cod_parceiro, parceiro.codigo_corretor) AS cod_corretor

	from pedido
	inner join apolice on apolice.pedido_id = pedido.pedido_id
	inner join apolice_cobertura on apolice.apolice_id = apolice_cobertura.apolice_id
	inner join comissao_gerada ON pedido.pedido_id = comissao_gerada.pedido_id #AND comissao_gerada.comissao > 0
	inner join parceiro on comissao_gerada.parceiro_id=parceiro.parceiro_id
	inner join apolice_endosso ON apolice_endosso.apolice_id = apolice.apolice_id AND apolice_endosso.apolice_movimentacao_tipo_id = _apolice_status_id
		AND (apolice_endosso.cod_cobertura IS NULL OR apolice_cobertura.cod_cobertura = apolice_endosso.cod_cobertura)
	INNER JOIN produto_parceiro_plano ppp ON apolice.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
	INNER JOIN produto_parceiro ON ppp.produto_parceiro_id = produto_parceiro.produto_parceiro_id
    INNER JOIN parceiro_tipo ON parceiro_tipo.parceiro_tipo_id = IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id)

	#APENAS COBERTURAS (SEM ASSISTENCIAS)
	INNER JOIN cobertura_plano ON apolice_cobertura.cobertura_plano_id = cobertura_plano.cobertura_plano_id AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id

	where 
	pedido.deletado = 0
	and apolice.deletado = 0
	and cobertura_plano.deletado = 0
	and apolice_cobertura.deletado = 0
	and comissao_gerada.deletado = 0
    and apolice_endosso.deletado = 0
	and apolice.apolice_id = _apolice_id
    and parceiro_tipo.codigo_interno <> 'seguradora'
	and apolice_cobertura.valor * (IF(_apolice_status_id = 1, 1, -1)) > 0
	and (apolice_endosso.apolice_endosso_id = _apolice_endosso_id OR _apolice_endosso_id IS NULL)
) x

#PESQUISA POR DIFERENCAS
LEFT JOIN (
	SELECT z.apolice_id, z.num_parcela, z.cd_tipo_comissao, z.cod_corretor, z.dif_comissao, z.dif_liq, MAX(ac.valor) m_valor, MIN(ac.valor) min_valor
	FROM (
		SELECT *, ROUND(premio_liquido * (pc_comissao/100), 2) - valor_comissao AS dif_comissao, SUM(premio_liquido) - valor_premio_net AS dif_liq
		FROM (
			select 

			apolice.apolice_id
			, pedido.num_parcela
            , SUM(ROUND(apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1), 2)) AS premio_liquido
            , apolice_aux.valor_premio_net
			, SUM(ROUND(comissao_gerada.comissao / 100 * (ROUND(apolice_cobertura.valor * IF(_apolice_status_id = 1, 1, -1), 2) ), 2) ) AS valor_comissao
            , IF(IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id) = 2, 'C', IF(IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id) = 3, 'P', IF(IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id) = 4, 'R', ''))) AS cd_tipo_comissao
			, ROUND(comissao_gerada.comissao, 4) as pc_comissao
			, IFNULL(comissao_gerada.cod_parceiro, parceiro.codigo_corretor) AS cod_corretor

			from pedido
			inner join apolice on apolice.pedido_id = pedido.pedido_id
			inner join apolice_cobertura on apolice.apolice_id = apolice_cobertura.apolice_id
			INNER JOIN (
				select apolice_id, valor_premio_net, pro_labore, valor_premio_total, valor_estorno, nota_fiscal_valor, 'equipamento' slug_table from apolice_equipamento where apolice_id = _apolice_id
				union 
				select apolice_id, valor_premio_net, pro_labore, valor_premio_total, valor_estorno, nota_fiscal_valor, 'generico' slug_table from apolice_generico where apolice_id = _apolice_id
            ) apolice_aux ON apolice_aux.apolice_id = apolice.apolice_id
			inner join comissao_gerada ON pedido.pedido_id = comissao_gerada.pedido_id #AND comissao_gerada.comissao > 0
			inner join parceiro on comissao_gerada.parceiro_id=parceiro.parceiro_id
			inner join apolice_endosso ON apolice_endosso.apolice_id = apolice.apolice_id AND apolice_endosso.apolice_movimentacao_tipo_id = _apolice_status_id
				AND (apolice_endosso.cod_cobertura IS NULL OR apolice_cobertura.cod_cobertura = apolice_endosso.cod_cobertura)
			INNER JOIN produto_parceiro_plano ppp ON apolice.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
			INNER JOIN produto_parceiro ON ppp.produto_parceiro_id = produto_parceiro.produto_parceiro_id
            INNER JOIN parceiro_tipo ON parceiro_tipo.parceiro_tipo_id = IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id)

			#APENAS COBERTURAS (SEM ASSISTENCIAS)
			INNER JOIN cobertura_plano ON apolice_cobertura.cobertura_plano_id = cobertura_plano.cobertura_plano_id AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id

			where 
			pedido.deletado = 0
			and apolice.deletado = 0
			and cobertura_plano.deletado = 0
			and apolice_cobertura.deletado = 0
			and apolice_endosso.deletado = 0
			and comissao_gerada.deletado = 0
			and apolice.apolice_id = _apolice_id
            and parceiro_tipo.codigo_interno <> 'seguradora'
			and apolice_cobertura.valor * (IF(_apolice_status_id = 1, 1, -1)) > 0

			GROUP BY apolice.apolice_id, pedido.num_parcela, apolice_aux.valor_premio_net, IF(IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id) = 2, 'C', IF(IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id) = 3, 'P', IF(IFNULL(comissao_gerada.parceiro_tipo_id, parceiro.parceiro_tipo_id) = 4, 'R', '')))
				, ROUND(comissao_gerada.comissao, 4), IFNULL(comissao_gerada.cod_parceiro, parceiro.codigo_corretor)
            
		) x
        GROUP BY apolice_id, num_parcela, valor_premio_net, cd_tipo_comissao, pc_comissao, cod_corretor
	) z
	INNER JOIN apolice a ON z.apolice_id = a.apolice_id
	INNER JOIN apolice_cobertura ac ON a.apolice_id = ac.apolice_id
	INNER JOIN produto_parceiro_plano ppp ON a.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
	INNER JOIN produto_parceiro ON ppp.produto_parceiro_id = produto_parceiro.produto_parceiro_id

	#APENAS COBERTURAS (SEM ASSISTENCIAS)
	INNER JOIN cobertura_plano ON ac.cobertura_plano_id = cobertura_plano.cobertura_plano_id AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id

	WHERE ac.deletado = 0 AND cobertura_plano.deletado = 0
	GROUP BY z.apolice_id, z.num_parcela, z.cd_tipo_comissao, z.cod_corretor, z.dif_liq, z.dif_comissao
) y ON x.apolice_id = y.apolice_id AND x.parcela = y.num_parcela AND y.cd_tipo_comissao = x.cd_tipo_comissao AND y.cod_corretor = x.cod_corretor AND x.valor_cob = IF(_apolice_status_id = 1, y.m_valor, y.min_valor)
;


END