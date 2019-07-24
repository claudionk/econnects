<?php
Class Produto_Parceiro_Servico_Log_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_servico_log';
    protected $primary_key = 'produto_parceiro_servico_log_id';

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

    //Dados
    public $validate = array(
    );

    function get_by_id($id)
    {
        return $this->get($id);
    }


    public function insLog($produto_parceiro_servico_id, $params){

        $data = array();

        $data['produto_parceiro_servico_id'] = $produto_parceiro_servico_id;
        $data['url'] = $params['url'];
        $data['idConsulta'] = issetor($params['idConsulta'], null);
        $data['consulta'] = $params['consulta'];
        $data['retorno'] = $params['retorno'];
        $data['time_envio'] = $params['time_envio'];
        $data['time_retorno'] = $params['time_retorno'];
        $data['parametros'] = $params['parametros'];
        $data['data_log'] = date('Y-m-d H:i:s');
        $data['ip'] = $params['ip'];

        $this->insert($data, TRUE);

    }

    function filter_by_idConsulta($idConsulta){
        $this->_database->where('idConsulta', $idConsulta);
        return $this;
    }

    function filter_by_produto_parceiro_servico_id($produto_parceiro_servico_id){
        $this->_database->where('produto_parceiro_servico_id', $produto_parceiro_servico_id);
        return $this;
    }

    function filter_by_consulta($consulta){
        $this->_database->where('consulta', $consulta);
        return $this;
    }

}
