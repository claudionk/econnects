CREATE PROCEDURE `sp_cta_parcemissao_parcelado`(IN _apolice_id INT, IN _apolice_status_id INT, IN _apolice_endosso_id INT)
BEGIN

DROP TEMPORARY TABLE IF EXISTS temp2;
DROP TEMPORARY TABLE IF EXISTS temp;
DROP TEMPORARY TABLE IF EXISTS tmp_up;

CREATE TEMPORARY TABLE temp (
	select 
		x.apolice_id, x.apolice_endosso_id, x.apolice_cobertura_id, x.parcela, x.cod_cobertura, x.valor_cob, x.iof, x.importancia_segurada
        ,x.premio_liquido AS premio_liquido1
        ,x.iof_base
        ,y.dif_liq
        ,y.num_parcela

        , @prm_liq := x.premio_liquido + 
        IF( y.num_parcela <> x.parcela, 0, 
			IF(y.dif_liq >= 0, y.dif_liq,
				IF(y.dif_liq * -1 < x.premio_liquido, y.dif_liq, 
					x.premio_liquido *-1 #zera o valor da cobertura
				)
			)
        ) AS premio_liquido

		, @vlr_iof := ROUND(@prm_liq * (x.iof/100), 2) AS valor_iof
        , x.data_cancelamento
        , x.data_vencimento
        , x.data_inicio_vigencia
        , x.valor_premio_total, x.valor_estorno
        , IF(_apolice_status_id = 2 AND x.valor_premio_total = x.valor_estorno, 1, 0) AS devolucao_integral
        , IF(_apolice_status_id = 2 AND x.data_cancelamento <= x.data_inicio_vigencia, 1, 0) AS antes_vigencia
        , IF(_apolice_status_id = 2 AND x.data_vencimento <= x.data_cancelamento, 1, 0) AS vecto_ant_canc
        , x.ini_vig, x.fim_vig

	from (
		select 
			apolice_endosso.apolice_id,
            apolice_endosso.apolice_endosso_id,
			cobertura_plano.cod_cobertura,
			apolice_cobertura.apolice_cobertura_id,
            apolice_cobertura.cobertura_plano_id,
            apolice_cobertura.data_inicio_vigencia AS ini_vig,
			apolice_cobertura.data_fim_vigencia AS fim_vig,
			apolice_endosso.parcela,
			pedido.num_parcela,
            apolice_equipamento.valor_premio_total,
            apolice_equipamento.valor_estorno,
            apolice_equipamento.data_cancelamento,
			apolice_endosso.valor,
            apolice_endosso.data_inicio_vigencia,
            apolice_endosso.data_vencimento,
            apolice_endosso.cd_movimento_cobranca,
			apolice_cobertura.iof,
			apolice_cobertura.valor_config,
			apolice_cobertura.valor * IF(apolice_cobertura.valor < 0, -1, 1) AS valor_cob,
            apolice_cobertura.importancia_segurada,
			#@pr_liq := ROUND(IF(apolice_endosso.valor = 0, 0, apolice_endosso.valor * (apolice_cobertura.valor_config / 100)), 2) AS premio_liquido
            @pr_liq := IF(apolice_endosso.valor = 0, 0, ROUND(apolice_cobertura.valor / pedido.num_parcela, 2) * IF(apolice_cobertura.valor < 0, -1, 1)) AS premio_liquido,
            apolice_equipamento.pro_labore iof_base
		from pedido
		inner join apolice on apolice.pedido_id = pedido.pedido_id
		inner join apolice_cobertura on apolice.apolice_id = apolice_cobertura.apolice_id
		inner join apolice_generico apolice_equipamento ON apolice_equipamento.apolice_id = apolice.apolice_id
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
			and cobertura_plano.deletado = 0
			and apolice.apolice_id = _apolice_id
			and apolice_cobertura.valor > 0

            #and 1 = IF(_apolice_status_id = 2 AND apolice_equipamento.valor_premio_total <> apolice_equipamento.valor_estorno AND apolice_endosso.data_vencimento <= apolice_equipamento.data_cancelamento, 0, 1) 
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
			#apolice_cobertura.valor * IF(apolice_cobertura.valor < 0, -1, 1) AS valor_cob,
			(( apolice_cobertura.valor - ROUND(apolice_cobertura.valor / pedido.num_parcela, 2) * pedido.num_parcela) ) * IF(apolice_cobertura.valor < 0, -1, 1) AS dif_liq

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
	SET @premio_dif := (SELECT dif_liq + premio_liquido1 FROM temp WHERE num_parcela = parcela AND dif_liq < 0 AND dif_liq * -1 > premio_liquido);

	#Atualiza a ultima parcela da cobertura de maior valor
	UPDATE temp
	SET premio_liquido = round(premio_liquido + @premio_dif, 2)
	WHERE num_parcela = parcela AND cod_cobertura = @max_cod_cobertura;

END IF;


#identifica a diferença do iof com o premio já calculado e distribuido
CREATE TEMPORARY TABLE temp2(
	select apolice_id, sum(valor_iof) valor_iof, iof_base, iof_base - sum(valor_iof) iof_dif
	from temp t
	group by apolice_id, iof_base, num_parcela
	having sum(valor_iof) <> iof_base
);

#Se existe diferença de IOF
IF EXISTS(
	SELECT apolice_id FROM temp2
) THEN

	#Se dá pra tirar apenas da última
	IF EXISTS(
		SELECT t.apolice_id
		FROM temp t
		INNER JOIN temp2 t2 ON t.apolice_id = t2.apolice_id AND t.parcela = t.num_parcela AND cod_cobertura = @max_cod_cobertura
		WHERE t2.iof_dif > 0 OR t.valor_iof > t.valor_iof * IF(t2.iof_dif < 0, -1, 1)
	) THEN

		#Retira a diferença da ultima parcela
		UPDATE temp t
		INNER JOIN temp2 t2 ON t.apolice_id = t2.apolice_id AND t.parcela = t.num_parcela AND cod_cobertura = @max_cod_cobertura
		SET t.valor_iof = t.valor_iof + t2.iof_dif;	

	ELSE

		SET @diff := (
			SELECT t.valor_iof + t2.iof_dif
			FROM temp t
			INNER JOIN temp2 t2 ON t.apolice_id = t2.apolice_id AND t.parcela = t.num_parcela AND cod_cobertura = @max_cod_cobertura
		);
        
        #Retira toda a IOF da maior cobertura
		UPDATE temp t
		INNER JOIN temp2 t2 ON t.apolice_id = t2.apolice_id AND t.parcela = t.num_parcela AND cod_cobertura = @max_cod_cobertura
		SET t.valor_iof = 0;

        #Maior valor de cobertura sem ser a maior (removida totalmente anteriormente)
		SET @max_valor_cob2 := (SELECT max(valor_cob) FROM temp WHERE cod_cobertura <> @max_cod_cobertura);

		#Código da cobertura de maior valor
		SET @max_cod_cobertura2 := (SELECT max(cod_cobertura) FROM temp WHERE cod_cobertura <> @max_cod_cobertura AND valor_cob = @max_valor_cob2);

        #Retira a diferença do IOF incapaz de retirar da prmeira maior cobertura
		UPDATE temp t
		INNER JOIN temp2 t2 ON t.apolice_id = t2.apolice_id AND t.parcela = t.num_parcela AND cod_cobertura = @max_cod_cobertura2
		SET t.valor_iof = t.valor_iof + @diff;

	END IF;

END IF;


#No caso de cancelamento
IF(_apolice_status_id = 2) THEN

	#envia apenas as parcelas de cancelamento
	DELETE t
	FROM temp t
	LEFT JOIN apolice_endosso ae ON ae.apolice_id = t.apolice_id AND ae.apolice_movimentacao_tipo_id = 2 AND ae.parcela = t.parcela AND ae.deletado = 0
		AND (t.cod_cobertura = ae.cod_cobertura OR ae.cod_cobertura IS NULL)
	WHERE ae.apolice_endosso_id IS NULL;

	#PEGA a diferença de cada cobertura
	DROP TEMPORARY TABLE IF EXISTS tmp_up;
	CREATE TEMPORARY TABLE tmp_up (
		select t.apolice_id, t.cod_cobertura, (ac.valor * -1) - SUM(t.premio_liquido) dif, min(parcela) parcela
		from temp t
		join apolice_cobertura ac on t.apolice_id = ac.apolice_id AND t.cod_cobertura = ac.cod_cobertura
		where t.devolucao_integral = 0 AND ac.deletado = 0 AND ac.valor < 0
		group by t.apolice_id, t.cod_cobertura
	);

    #PRIMEIRA PARCELA RECEBE OS VALORES DE COBERTURAS e ZERA O IOF 
	UPDATE tmp_up tu
	INNER JOIN temp t ON tu.apolice_id = t.apolice_id AND tu.parcela = t.parcela AND tu.cod_cobertura = t.cod_cobertura
	SET 
		t.premio_liquido = t.premio_liquido + IF(t.antes_vigencia = 1, 0, tu.dif)
		, t.valor_iof = 0
	WHERE t.devolucao_integral = 0;
    
    #ZERA O IOF DAS PARCELAS VENCIDAS NOS CANCELAMENTOS APÓS X DIAS
	UPDATE temp
	SET valor_iof = 0
	WHERE devolucao_integral = 0 AND vecto_ant_canc = 1;

END IF;


#retorna o resultado em tela
select t.apolice_cobertura_id, t.parcela, t.cod_cobertura, t.premio_liquido, t.valor_iof, t.premio_liquido + t.valor_iof as premio_liquido_total, t.importancia_segurada as nota_fiscal_valor, DATE_FORMAT(t.data_vencimento, '%Y%m%d') AS data_vencimento
, t.ini_vig, t.fim_vig
from temp t
join apolice_endosso ae on t.apolice_id = ae.apolice_id and t.parcela = ae.parcela
	AND (t.cod_cobertura = ae.cod_cobertura OR ae.cod_cobertura IS NULL)
where ae.deletado = 0 and ae.apolice_movimentacao_tipo_id = _apolice_status_id
and (ae.apolice_endosso_id = _apolice_endosso_id OR _apolice_endosso_id IS NULL )
order by t.parcela, t.cod_cobertura;


DROP TEMPORARY TABLE IF EXISTS temp2;
DROP TEMPORARY TABLE IF EXISTS temp;
DROP TEMPORARY TABLE IF EXISTS tmp_up;

END