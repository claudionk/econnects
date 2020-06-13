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
    protected $time = -5;

    public function __construct()
    {
        parent::__construct();
    }

    public function run($time = null){
        if ( !empty($time) )
            $this->time = $time; // minutes

        // cria um arquivo para evitar rodar 2x o mesmo script
        $fileFull = "/var/log/httpd/cta_movimentacao.block";

        if(file_exists($fileFull)){
            // pega a data de hoje e a data da criacao do arquivo
            $now = new DateTime(date('Y-m-d H:i:s'));
            $fileModified = new DateTime(date ("Y-m-d H:i:s", filemtime($fileFull)));

            // se já fazem 4h que foi criado deverá remover o arquivo
            $diff = $now->diff($fileModified);
            if ($diff->h >= 4){
                unlink($fileFull);
            } else {
                return false;
            }
        }

        error_log(date('d-m-Y H:i:s'), 3, $fileFull);

        $this->integracao_realizada_parc();
        $this->integracao_realizada_cli();
        $this->novas_apolices();

        // remove o arquivo
        unlink($fileFull);

    }

    private function structure(){

        $sqlBegin = " 
        replace cta_movimentacao( 
            pedido_id
            , apolice_id
            , cotacao_id
            , cliente_id
            , num_apolice
            , apolice_movimentacao_tipo_id
            , apolice_endosso_id
            , CTA_nao_processado
            , CTA_Ag_Retorno
            , CTA_Retorno_ok
            , CTA_Retorno_erro
            , Erro
            , ultimo_envio_cliente
            , ultimo_envio_parcela
            , ultimo_envio_comissao
            , CTA_arquivo_ok
        )
        SELECT 
            pedido_id 
            , apolice_id 
            , cotacao_id 
            , cliente_id
            , num_apolice 
            , apolice_movimentacao_tipo_id 
            , apolice_endosso_id
            , date_format(IF(CTA_Enviado IS NULL AND CTA_Retorno_ok IS NULL AND NOT (CTA_Retorno_ok IS NULL AND IFNULL(CTA_Retorno, 1) >= IFNULL(CTA_Enviado, 1)), now(), NULL), '%Y-%m-%d %H:00:00') as CTA_nao_processado
            , date_format(IF(CTA_Enviado IS NOT NULL AND CTA_Retorno_ok IS NULL , if( IFNULL(CTA_Enviado,1) > IFNULL(CTA_Retorno, 1), CTA_Enviado, NULL) , NULL), '%Y-%m-%d %H:00:00') as CTA_Ag_Retorno
            , date_format(IF(CTA_Retorno_ok IS NOT NULL, CTA_Retorno_ok, NULL), '%Y-%m-%d %H:00:00')  as CTA_Retorno_ok
            , date_format(IF(CTA_Retorno_ok IS NULL, if( IFNULL(CTA_Retorno, 1) >= IFNULL(CTA_Enviado, 1), CTA_Retorno , NULL), NULL), '%Y-%m-%d %H:00:00') as CTA_Retorno_erro
            , IFNULL(IF(IF(CTA_Retorno_ok IS NULL, if( IFNULL(CTA_Retorno, 1) >= IFNULL(CTA_Enviado, 1), CTA_Retorno , NULL), NULL)  IS NOT NULL, Erro, ''), '') as Erro
            , ultimo_envio_cliente
            , ultimo_envio_parcela
            , ultimo_envio_comissao
            , CTA_arquivo_ok
        FROM (
            SELECT 
                  ctaEmissao(chave_emi, 0) as CTA_Enviado
                , ctaEmissaoSucesso(chave_emi) as CTA_Retorno_ok
                , ctaEmissao(chave_emi, 5) as CTA_Retorno
                , ctaEmissaoErro(chave_emi) as Erro
                , ctaEmissaoSucesso_arquivo(chave_emi) as CTA_arquivo_ok
                , num_apolice, pedido_id, apolice_id, cotacao_id, cliente_id
                , apolice_movimentacao_tipo_id, apolice_endosso_id
                , (
                    SELECT DATE(MAX(l.criacao) )
                    FROM integracao_log l
                    INNER JOIN integracao_log_detalhe d ON l.integracao_log_id=d.integracao_log_id
                    INNER JOIN integracao i ON l.integracao_id=i.integracao_id
                    WHERE l.deletado = 0 AND d.deletado = 0 AND i.deletado = 0 
                    AND i.slug_group = 'cliente'
                    AND d.chave = CAST(cliente_id AS CHAR)
                ) AS ultimo_envio_cliente
                , (
                    SELECT DATE(MAX(l.criacao))
                    FROM integracao_log l
                    INNER JOIN integracao_log_detalhe d ON l.integracao_log_id=d.integracao_log_id
                    INNER JOIN integracao i ON l.integracao_id=i.integracao_id
                    WHERE l.deletado = 0 AND d.deletado = 0 AND i.deletado = 0 
                    AND i.slug_group = 'parc-emissao'
                    AND d.chave = chave_emi
                ) AS ultimo_envio_parcela
                , (
                    SELECT DATE(MAX(l.criacao))
                    FROM integracao_log l
                    INNER JOIN integracao_log_detalhe d ON l.integracao_log_id=d.integracao_log_id
                    INNER JOIN integracao i ON l.integracao_id=i.integracao_id
                    WHERE l.deletado = 0 AND d.deletado = 0 AND i.deletado = 0 
                    AND i.slug_group = 'ems-emissao'
                    AND d.chave = chave_emi
                ) AS ultimo_envio_comissao
            FROM ( ";

        $sqlEnd = "
            ) AS x
        ) AS y ";

        return [
            'begin' => $sqlBegin, 
            'end' => $sqlEnd
        ];
    }

    public function integracao_realizada_parc(){
        $q = $this->structure();

        $sql = $q['begin'] . " 
            SELECT distinct
                c.cliente_id
                , concat(a.num_apolice, '|', ae.sequencial) as chave_emi
                , am.apolice_movimentacao_tipo_id
                , a.num_apolice
                , a.pedido_id
                , a.apolice_id
                , p.cotacao_id
                , ae.apolice_endosso_id
            FROM 
                apolice a 
                INNER JOIN pedido p on a.pedido_id = p.pedido_id
                INNER JOIN cotacao c on p.cotacao_id = c.cotacao_id
                INNER JOIN apolice_movimentacao am on a.apolice_id = am.apolice_id
                INNER JOIN apolice_movimentacao_tipo amt on am.apolice_movimentacao_tipo_id = amt.apolice_movimentacao_tipo_id
                INNER JOIN apolice_endosso ae on ae.apolice_id = a.apolice_id and ae.apolice_movimentacao_tipo_id = am.apolice_movimentacao_tipo_id
            WHERE 
                a.deletado = 0 
                AND p.deletado = 0 
                AND c.deletado = 0
                AND am.deletado = 0
                AND ae.deletado = 0
                AND concat(a.num_apolice, '|', ae.sequencial) IN(
                    SELECT ild.chave
                    FROM integracao i
                    INNER JOIN integracao_log il on i.integracao_id = il.integracao_id
                    INNER JOIN integracao_log_detalhe ild on ild.integracao_log_id = il.integracao_log_id
                    WHERE il.deletado = 0 AND ild.deletado = 0 and i.slug_group in ('parc-emissao','ems-emissao')
                    AND date_add( now(), interval {$this->time} minute ) < IFNULL(ild.alteracao,ild.criacao)
                )
        ". $q['end'];

        if ( $this->_database->query( $sql ) ){
            echo "OK Parcelas\n";
            return true;
        }
        else
        {
            echo "Fail Parcelas\n";
            return false;
        }
    }

    public function integracao_realizada_cli(){
        $q = $this->structure();

        $sql = $q['begin'] . " 
            SELECT distinct
                c.cliente_id
                , concat(a.num_apolice, '|', ae.sequencial) as chave_emi
                , am.apolice_movimentacao_tipo_id
                , a.num_apolice
                , a.pedido_id
                , a.apolice_id
                , p.cotacao_id
                , ae.apolice_endosso_id
            FROM 
                apolice a 
                INNER JOIN pedido p on a.pedido_id = p.pedido_id
                INNER JOIN cotacao c on p.cotacao_id = c.cotacao_id
                INNER JOIN apolice_movimentacao am on a.apolice_id = am.apolice_id
                INNER JOIN apolice_movimentacao_tipo amt on am.apolice_movimentacao_tipo_id = amt.apolice_movimentacao_tipo_id    
                INNER JOIN apolice_endosso ae on ae.apolice_id = a.apolice_id and ae.apolice_movimentacao_tipo_id = am.apolice_movimentacao_tipo_id
                , integracao i        
                , integracao_log il 
                , integracao_log_detalhe ild
            WHERE 
                a.deletado = 0 
                AND p.deletado = 0 
                AND c.deletado = 0
                and i.integracao_id = il.integracao_id
                and ild.integracao_log_id = il.integracao_log_id
                and i.slug_group = 'cliente'
                and c.cliente_id = ild.chave
                and date_add( now(), interval {$this->time} minute ) < IFNULL(ild.alteracao,ild.criacao)
        ". $q['end'];

        if ( $this->_database->query( $sql ) ){
            echo "OK Clientes\n";
            return true;
        }
        else
        {
            echo "Fail Clientes\n";
            return false;
        }
    }

    public function novas_apolices(){
        $q = $this->structure();

        $sql = $q['begin'] . " 
            SELECT DISTINCT
                c.cliente_id
                , concat(a.num_apolice, '|', ae.sequencial) as chave_emi
                , am.apolice_movimentacao_tipo_id
                , a.num_apolice
                , a.pedido_id
                , a.apolice_id
                , p.cotacao_id
                , ae.apolice_endosso_id
            FROM 
                apolice a 
                INNER JOIN pedido p on a.pedido_id = p.pedido_id
                INNER JOIN cotacao c on p.cotacao_id = c.cotacao_id
                INNER JOIN apolice_movimentacao am on a.apolice_id = am.apolice_id
                INNER JOIN apolice_movimentacao_tipo amt on am.apolice_movimentacao_tipo_id = amt.apolice_movimentacao_tipo_id    
                INNER JOIN apolice_endosso ae on ae.apolice_id = a.apolice_id and ae.apolice_movimentacao_tipo_id = am.apolice_movimentacao_tipo_id
                LEFT JOIN cta_movimentacao cta on ae.apolice_id = cta.apolice_id and ae.apolice_movimentacao_tipo_id = cta.apolice_movimentacao_tipo_id and ae.apolice_endosso_id = cta.apolice_endosso_id
            WHERE 
                a.deletado = 0 
                AND p.deletado = 0 
                AND c.deletado = 0
                AND cta.apolice_id IS NULL
        ". $q['end'];

        if ( $this->_database->query( $sql ) ){
            echo "OK Novas Apólices\n";
            return true;
        }
        else
        {
            echo "Fail Novas Apólices\n";
            return false;
        }
    }

}

