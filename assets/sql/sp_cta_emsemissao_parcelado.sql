CREATE PROCEDURE `sp_cta_emsemissao_parcelado`(IN _apolice_id INT, IN _apolice_status_id INT, IN _apolice_endosso_id INT)
BEGIN

DROP TEMPORARY TABLE IF EXISTS temp;
DROP TEMPORARY TABLE IF EXISTS temp2;
DROP TEMPORARY TABLE IF EXISTS tmp_up;

CREATE TEMPORARY TABLE temp (
	select 
		x.apolice_id, x.apolice_endosso_id, x.parcela, x.num_parcela, x.cod_cobertura, x.valor_cob,  x.parceiro, x.cd_tipo_comissao, x.pc_comissao, x.cod_corretor, y.dif_liq
        , x.premio_liquido AS premio_liquido1
        , @prm_liq := x.premio_liquido + 
        IF( y.num_parcela <> x.parcela, 0, 
			IF(y.dif_liq >= 0, y.dif_liq,
				IF(y.dif_liq * -1 < x.premio_liquido, y.dif_liq, 
					x.premio_liquido *-1 #zera o valor da cobertura
				)
			)
        ) AS premio_liquido
		, ROUND(@prm_liq * (x.pc_comissao/100), 2) as valor_comissao
	from (
		select 
			apolice_endosso.apolice_id,
            apolice_endosso.apolice_endosso_id,
			cobertura_plano.cod_cobertura,
			apolice_cobertura.cobertura_plano_id,
			apolice_endosso.parcela,
			pedido.num_parcela,
			apolice_endosso.valor,
			apolice_cobertura.valor_config,
			apolice_cobertura.valor AS valor_cob,
            @pr_liq := IF(apolice_endosso.valor = 0, 0, ROUND(apolice_cobertura.valor / pedido.num_parcela, 2) ) AS premio_liquido
            , parceiro.nome as parceiro
            , IF(comissao_gerada.parceiro_tipo_id = 2, 'C', IF(comissao_gerada.parceiro_tipo_id = 3, 'P', IF(comissao_gerada.parceiro_tipo_id = 4, 'R', ''))) AS cd_tipo_comissao
            , ROUND(IF(apolice_endosso.valor = 0, 0, comissao_gerada.comissao), 4) as pc_comissao
			, IFNULL(comissao_gerada.cod_parceiro, parceiro.codigo_corretor) AS cod_corretor

		from pedido
		inner join apolice on apolice.pedido_id = pedido.pedido_id
		inner join apolice_cobertura on apolice.apolice_id = apolice_cobertura.apolice_id
		inner join apolice_generico apolice_equipamento ON apolice_equipamento.apolice_id = apolice.apolice_id
        inner join comissao_gerada ON pedido.pedido_id = comissao_gerada.pedido_id AND comissao_gerada.comissao > 0
        inner join parceiro on comissao_gerada.parceiro_id=parceiro.parceiro_id
		inner join apolice_endosso ON apolice_endosso.apolice_id = apolice.apolice_id AND apolice_endosso.apolice_movimentacao_tipo_id = 1
			AND (apolice_cobertura.cod_cobertura = apolice_endosso.cod_cobertura OR apolice_endosso.cod_cobertura IS NULL)
		INNER JOIN produto_parceiro_plano ppp ON apolice.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
		INNER JOIN produto_parceiro ON ppp.produto_parceiro_id = produto_parceiro.produto_parceiro_id

        #APENAS COBERTURAS (SEM ASSISTENCIAS)
		INNER JOIN cobertura_plano ON apolice_cobertura.cobertura_plano_id = cobertura_plano.cobertura_plano_id AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id

		where 
			pedido.deletado = 0
			and apolice.deletado = 0
			and apolice_cobertura.deletado = 0
			and apolice_endosso.deletado = 0
            and comissao_gerada.deletado = 0
			and cobertura_plano.deletado = 0
            and apolice_endosso.parcela <> 0
			and apolice.apolice_id = _apolice_id
			and apolice_cobertura.valor > 0

			#and (@cod_cobertura = '' or apolice_cobertura.cod_cobertura = @cod_cobertura)
		order by apolice_endosso.sequencial, cobertura_plano.cod_cobertura
	) x
	#PESQUISA POR DIFERENCAS
	LEFT JOIN (
		SELECT
			apolice_cobertura.apolice_id,
			cobertura_plano.cod_cobertura,
			apolice_cobertura.cobertura_plano_id,
			pedido.num_parcela,
			(( apolice_cobertura.valor - ROUND(apolice_cobertura.valor / pedido.num_parcela, 2) * pedido.num_parcela) ) AS dif_liq
		from pedido
		inner join apolice on apolice.pedido_id = pedido.pedido_id
		inner join apolice_cobertura on apolice.apolice_id = apolice_cobertura.apolice_id
		inner join apolice_generico apolice_equipamento ON apolice_equipamento.apolice_id = apolice.apolice_id
		INNER JOIN produto_parceiro_plano ppp ON apolice.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id
		INNER JOIN produto_parceiro ON ppp.produto_parceiro_id = produto_parceiro.produto_parceiro_id

		#APENAS COBERTURAS (SEM ASSISTENCIAS)
		INNER JOIN cobertura_plano ON apolice_cobertura.cobertura_plano_id = cobertura_plano.cobertura_plano_id AND produto_parceiro.parceiro_id = cobertura_plano.parceiro_id

		where 
			pedido.deletado = 0
			and apolice.deletado = 0
			and apolice_cobertura.deletado = 0
			and cobertura_plano.deletado = 0
			and apolice.apolice_id = _apolice_id
			and apolice_cobertura.valor > 0
			#and apolice_cobertura.cod_cobertura = '01795'

		#order by cobertura_plano.cod_cobertura
	) y ON x.apolice_id = y.apolice_id AND x.cobertura_plano_id = y.cobertura_plano_id #AND x.parcela = y.num_parcela AND x.valor_cob = y.m_valor
	
    #where (@cod_cobertura = '' or x.cod_cobertura = @cod_cobertura)
);

#Maior valor de cobertura
SET @max_valor_cob := (SELECT max(valor_cob) FROM temp);

#Código da cobertura de maior valor
SET @max_cod_cobertura := (SELECT max(cod_cobertura) FROM temp WHERE valor_cob = @max_valor_cob);


#**** 
#**** CORRIGE O PREMIO LIQUIDO ****
#**** 

#**** RETIRAR O VALOR RESTANTE DA COBERTURA D MAIOR VALOR ****
IF EXISTS (
	select cod_cobertura from temp 
	#ultima parcela / a diferença deve ser subtraida / a diferença é maior q o valor disponivel para retirar
	where num_parcela = parcela AND dif_liq < 0 AND dif_liq * -1 > premio_liquido
	order by parcela, cod_cobertura
) THEN

	#diferença da cobertura negativa
	SET @premio_dif := (SELECT dif_liq + premio_liquido1 FROM temp WHERE num_parcela = parcela AND dif_liq < 0 AND dif_liq * -1 > premio_liquido limit 1);

	#Atualiza a ultima parcela da cobertura de maior valor
	UPDATE temp
	SET premio_liquido = round(premio_liquido + @premio_dif, 2)
		, valor_comissao = ROUND(round(premio_liquido + @premio_dif, 2) * (pc_comissao/100), 2)
	WHERE num_parcela = parcela AND cod_cobertura = @max_cod_cobertura;

END IF;


#No caso de cancelamento
IF(_apolice_status_id = 2) THEN

	#envia apenas as parcelas de cancelamento
	DELETE t
	FROM temp t
	LEFT JOIN apolice_endosso ae ON ae.apolice_id = t.apolice_id AND ae.apolice_movimentacao_tipo_id = 2 AND ae.parcela = t.parcela
		AND (t.cod_cobertura = ae.cod_cobertura OR ae.cod_cobertura IS NULL)
	WHERE ae.apolice_endosso_id IS NULL;

	#PEGA a diferença de cada cobertura
	CREATE TEMPORARY TABLE tmp_up (
		select x.apolice_id, x.cod_cobertura, (ac.valor * -1) - x.premio_liquido dif, x.parcela
		from (
			select t.apolice_id, t.cod_cobertura, t.cd_tipo_comissao, MIN(t.parcela) parcela, SUM(t.premio_liquido) premio_liquido
			from temp t
			group by t.apolice_id, t.cod_cobertura, t.cd_tipo_comissao
		) x
		join apolice_cobertura ac on x.apolice_id = ac.apolice_id AND x.cod_cobertura = ac.cod_cobertura
		where ac.deletado = 0 AND ac.valor < 0
		group by x.apolice_id, x.cod_cobertura
	);

    #PRIMEIRA PARCELA RECEBE OS VALORES DE COBERTURAS e ZERA O IOF 
	UPDATE tmp_up tu
	INNER JOIN temp t ON tu.apolice_id = t.apolice_id AND tu.parcela = t.parcela AND tu.cod_cobertura = t.cod_cobertura
	SET t.premio_liquido = t.premio_liquido + tu.dif, t.valor_comissao = ROUND((t.premio_liquido + tu.dif) * (t.pc_comissao/100), 2)
    ;

END IF;


#identifica a diferença da comissao por tipo e cobertura
CREATE TEMPORARY TABLE temp2(
	select apolice_id, parcela, cd_tipo_comissao, ROUND(SUM(premio_liquido) * (pc_comissao/100), 2) - SUM(valor_comissao) comissao_dif
	from temp
	group by apolice_id, parcela, cd_tipo_comissao, pc_comissao
	having ROUND(SUM(premio_liquido) * (pc_comissao/100), 2) <> SUM(valor_comissao)
);


#identifica qual a maior parcela do registro com maior valor sw premio_liquido
update temp t 
inner join temp2 t2 on t.apolice_id = t2.apolice_id and t.parcela = t2.parcela and t.cd_tipo_comissao = t2.cd_tipo_comissao and t.cod_cobertura = @max_cod_cobertura
SET t.valor_comissao = t.valor_comissao + t2.comissao_dif;


#retorna o resultado em tela
select t.parcela, t.cod_cobertura, t.premio_liquido, t.parceiro, t.cd_tipo_comissao, t.valor_comissao, t.pc_comissao, t.cod_corretor
from temp t
join apolice_endosso ae on t.apolice_id = ae.apolice_id and t.parcela = ae.parcela
	AND (t.cod_cobertura = ae.cod_cobertura OR ae.cod_cobertura IS NULL)
where ae.deletado = 0 and ae.apolice_movimentacao_tipo_id = _apolice_status_id
and (ae.apolice_endosso_id = _apolice_endosso_id OR _apolice_endosso_id IS NULL )
order by t.parcela, t.cd_tipo_comissao, t.cod_cobertura, t.parceiro;

DROP TEMPORARY TABLE IF EXISTS temp;
DROP TEMPORARY TABLE IF EXISTS temp2;
DROP TEMPORARY TABLE IF EXISTS tmp_up;

END