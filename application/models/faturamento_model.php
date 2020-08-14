<?php

Class Faturamento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'fatura_parceiro_lote';
    protected $primary_key = 'fatura_parceiro_lote_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();
    
    function get_all_lote(){
        $lote = $this->db->query("SELECT p.nome_fantasia AS parceiro,
                                         fpl.fatura_parceiro_lote_id,
                                         DATE_FORMAT(fpl.data_corte, '%Y-%m') AS data_corte_orig,
                                         DATE_FORMAT(fpl.data_corte, '%m/%Y') AS data_corte,
                                         fpl.data_corte AS data_corte_report,
                                         CASE WHEN fpl.gera_oficial = 'N' THEN 'NÃO' 
                                              ELSE 'SIM'
                                          END AS gera_oficial,
                                         DATE_FORMAT(fpl.data_processamento, '%d/%m/%Y %H:%i:%s') AS data_processamento
                                    FROM fatura_parceiro_lote fpl
                                    JOIN parceiro p ON fpl.parceiro_id = p.parceiro_id
                                     AND fpl.deletado = 0
                                   ORDER BY fpl.data_corte desc,
                                            p.nome_fantasia asc" )->result_array();
        return $lote;
    }    

}