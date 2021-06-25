<?php
Class Parceiro_Relacionamento_Produto_Vigencia_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'parceiro_relacionamento_produto_vigencia';
    protected $primary_key = 'parceiro_relacionamento_produto_vigencia_id';

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

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'parceiro_relacionamento_produto_id' => app_clear_number($this->input->post('parceiro_relacionamento_produto_id')),
            'comissao_data_ini'                  => app_dateonly_mask_to_mysql($this->input->post('comissao_data_ini')),
            'comissao_data_fim'                  => app_dateonly_mask_to_mysql($this->input->post('comissao_data_fim')),
            'repasse_comissao'                   => $this->input->post('repasse_comissao'),
            'repasse_maximo'                     => app_unformat_currency($this->input->post('repasse_maximo')),
            'comissao_tipo'                      => $this->input->post('comissao_tipo'), 
            'comissao'                           => $this->input->post('comissao_tipo') == 1 ? 0 : app_unformat_currency($this->input->post('comissao')), 
            'comissao_indicacao'                 => app_unformat_currency($this->input->post('comissao_indicacao')),
        );
        return $data;
    }

    function filter_by_parceiro_relacionamento_produto_id($parceiro_relacionamento_produto_id){       
        $query = $this->db->query("SELECT parceiro_relacionamento_produto_vigencia_id, 
                                          parceiro_relacionamento_produto_id, 
                                          comissao_data_ini, 
                                          comissao_data_fim, 
                                          repasse_comissao, 
                                          repasse_maximo, 
                                          comissao_tipo, 
                                          comissao, 
                                          comissao_indicacao, 
                                          deletado, 
                                          criacao, 
                                          alteracao_usuario_id, 
                                          alteracao
                                     FROM parceiro_relacionamento_produto_vigencia 
                                    WHERE parceiro_relacionamento_produto_id = {$parceiro_relacionamento_produto_id}
                                      AND deletado = 0
                                    ORDER BY comissao_data_ini DESC
                                    ");
        if($query->num_rows() > 0)
            return $query->result_array();
        return array();
    }    


    function filter_by_parceiro_relacionamento_produto_vigencia_id($parceiro_relacionamento_produto_vigencia_id){       
        $query = $this->db->query("SELECT parceiro_relacionamento_produto_vigencia_id, 
                                          parceiro_relacionamento_produto_id, 
                                          comissao_data_ini, 
                                          comissao_data_fim, 
                                          repasse_comissao, 
                                          repasse_maximo, 
                                          comissao_tipo, 
                                          comissao, 
                                          comissao_indicacao, 
                                          deletado, 
                                          criacao, 
                                          alteracao_usuario_id, 
                                          alteracao
                                     FROM parceiro_relacionamento_produto_vigencia 
                                    WHERE parceiro_relacionamento_produto_vigencia_id = {$parceiro_relacionamento_produto_vigencia_id}
                                      AND deletado = 0
                                    ORDER BY comissao_data_ini DESC");
        if($query->num_rows() > 0)
            return $query->result_array();
        return array();
    }        

    function update_last_row($parceiro_relacionamento_produto_id, $comissao_data_ini){
        $ano= substr($comissao_data_ini, 6);
        $mes= substr($comissao_data_ini, 3,-5);
        $dia= substr($comissao_data_ini, 0,-8);
        $comissao_data_ini = $ano."-".$mes."-".$dia;
        
        $this->db->query("UPDATE parceiro_relacionamento_produto_vigencia 
                             SET comissao_data_fim = DATE_ADD('{$comissao_data_ini}', INTERVAL -1 DAY)
                           WHERE parceiro_relacionamento_produto_id = {$parceiro_relacionamento_produto_id}
                             AND deletado = 0
                             AND comissao_data_fim IS NULL");    
        return true;
    }
}
