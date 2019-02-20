<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

Class Cta_Movimentacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cta_movimentacao';
    protected $primary_key = 'cta_movimentacao_id';

    //Configurações
    protected $return_type = 'array';

    public function __construct()
    {
        parent::__construct();
    }

    public function run(){

        $sql = " 
        replace cta_movimentacao( 
            pedido_id
            , apolice_id
            , cotacao_id
            , num_apolice
            , apolice_movimentacao_tipo_id
            , CTA_Ag_Retorno
            , CTA_Retorno_ok
            , CTA_Retorno_erro
            , Erro
        )
        SELECT 
            pedido_id 
            , apolice_id 
            , cotacao_id 
            , num_apolice 
            , apolice_movimentacao_tipo_id 
            , IF(CTA_Enviado IS NOT NULL AND CTA_Retorno_ok IS NULL AND CTA_Retorno IS NULL, CTA_Enviado, NULL)  as CTA_Ag_Retorno
            , IF(CTA_Enviado IS NOT NULL, CTA_Retorno_ok, NULL)  as CTA_Retorno_ok
            , IF(CTA_Retorno_ok IS NULL, CTA_Retorno, NULL)  as CTA_Retorno_erro
            , IFNULL(IF(IF(CTA_Retorno_ok IS NULL, CTA_Retorno, NULL) IS NOT NULL, Erro, ''), '')  as Erro
        FROM (
            SELECT 
                maxDate( ctaEmissao(chave_emi, 0), ctaCliente(cliente_id, 0), 1 ) as CTA_Enviado
                , maxDate( ctaEmissao(chave_emi, 4), ctaCliente(cliente_id, 4), 1 ) as CTA_Retorno_ok
                , maxDate( ctaEmissao(chave_emi, 5), ctaCliente(cliente_id, 5), 0 ) as CTA_Retorno
                , ctaEmissaoErro(chave_emi) as Erro
                , num_apolice, pedido_id, apolice_id, cotacao_id
                , apolice_movimentacao_tipo_id
            FROM (
                ( 
                    SELECT distinct
                        c.cliente_id
                        , concat(a.num_apolice, '|', LPAD(am.apolice_movimentacao_tipo_id, 2, '0')) as chave_emi
                        , am.apolice_movimentacao_tipo_id
                        , a.num_apolice
                       , a.pedido_id
                       , a.apolice_id
                       , p.cotacao_id
                    FROM 
                        apolice a 
                        JOIN pedido p on a.pedido_id = p.pedido_id
                        JOIN cotacao c on p.cotacao_id = c.cotacao_id
                        JOIN apolice_movimentacao am on a.apolice_id = am.apolice_id
                        JOIN apolice_movimentacao_tipo amt on am.apolice_movimentacao_tipo_id = amt.apolice_movimentacao_tipo_id    
                        , integracao i        
                        , integracao_log il 
                        , integracao_log_detalhe ild
                    WHERE 
                        a.deletado = 0 
                        AND p.deletado = 0 
                        AND c.deletado = 0
                        and i.integracao_id = il.integracao_id
                        and ild.integracao_log_id = il.integracao_log_id
                        and i.slug_group in ('parc-emissao','ems-emissao')
                        and concat(a.num_apolice, '|', LPAD(am.apolice_movimentacao_tipo_id, 2, '0')) = ild.chave
                        and date_add( now(), interval -3 minute ) < IFNULL(ild.alteracao,ild.criacao)
                )
                union
                (
                SELECT distinct
                    c.cliente_id
                    , concat(a.num_apolice, '|', LPAD(am.apolice_movimentacao_tipo_id, 2, '0')) as chave_emi
                    , am.apolice_movimentacao_tipo_id
                    , a.num_apolice
                   , a.pedido_id
                   , a.apolice_id
                   , p.cotacao_id
                FROM 
                    apolice a 
                    JOIN pedido p on a.pedido_id = p.pedido_id
                    JOIN cotacao c on p.cotacao_id = c.cotacao_id
                    JOIN apolice_movimentacao am on a.apolice_id = am.apolice_id
                    JOIN apolice_movimentacao_tipo amt on am.apolice_movimentacao_tipo_id = amt.apolice_movimentacao_tipo_id    
                WHERE 
                    a.deletado = 0 
                    AND p.deletado = 0 
                    AND c.deletado = 0
                    and date_add( now(), interval -3 minute ) < am.criacao 
                )
            ) AS x
        ) AS y ";

        if ( $this->_database->query( $sql ) ){
            echo "Success!";
        }
        else
        {
            echo "Query failed!";
        }
    }

}
