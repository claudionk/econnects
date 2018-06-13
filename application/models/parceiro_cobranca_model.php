<?php
Class Parceiro_Cobranca_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'parceiro_cobranca';
    protected $primary_key = 'parceiro_cobranca_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');

    //Dados
    public $validate = array(
        array(
            'field' => 'valor',
            'label' => 'Valor',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'cobranca_item_id',
            'label' => 'Cobrança Item',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'cobranca_item'
        ),
        array(
            'field' => 'cobranca_tipo_id',
            'label' => 'Tipo de cobrança',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'cobranca_tipo'
        ),
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default',
            'foreign' => 'parceiro'
        )
    );


    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  parent::get_form_data($just_check);
        $data['valor'] = (isset($data['valor'])) ? app_unformat_currency($data['valor']) : 0;
        return $data;
    }

    function filter_by_parceiro($parceiro_id){

        $this->_database->where('parceiro_cobranca.parceiro_id', $parceiro_id);

        return $this;
    }


    public function isfilterPesquisa()
    {


        if($this->input->get('parceiro_id') && $this->input->get('data_inicio') && $this->input->get('data_fim')) {
            return TRUE;
        }else{
            return FALSE;
        }
    }


    public function relatorio(){

        $result = array(
            'result' => false,
            'mensgem' => 'Nenhum Filtro Selecionado',
            'rows' => array()
        );

        if($this->isfilterPesquisa()){

            $parceiro_id = $this->input->get('parceiro_id');
            $data_inicio = app_dateonly_mask_to_mysql($this->input->get('data_inicio'));
            $data_fim = app_dateonly_mask_to_mysql($this->input->get('data_fim'));
            $rows = array();

            //Busca Cobranças configuradas
            $itens = $this->current_model->with_foreign()->filter_by_parceiro($parceiro_id)->get_all();
          //  print_r($itens);exit;

            foreach ($itens as $item) {


                $metodo = "processa_{$item['cobranca_item_slug']}_{$item['cobranca_tipo_slug']}";
                if(method_exists($this, $metodo)) {
                    $rows[] = $this->{$metodo}($item, $data_inicio, $data_fim);
                }else{
                    $rows[] = array('item' => "{$item['cobranca_item_descricao']}</br><strong class='text-danger '>RELATÓRIO NÃO IMPLEMENTADO</strong>", 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 0, 'valor' => '00,00', 'valor_total' => '00,00');
                }

            }


            $result['result'] = true;
            $result['rows'] = $rows;

        }


        $valor_total = 0;
        foreach ($result['rows'] as $index => $row) {
            $valor_total += app_unformat_currency($row['valor_total']);
        }
        $result['rows'][] = array('item' => "", 'tipo' => "",  'quantidade' => "", 'valor' =>  "<div class='text-right text-bold'>Total: </div>", 'valor_total' => "<strong class='text-default-dark'>R$ " . app_format_currency($valor_total, false, 2) . "</strong>");

        return $result;

    }

    public function processa_sms_unica($item, $data_inicio, $data_fim){
        return array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 1, 'valor' => 'R$ ' . app_format_currency($item['valor'], false, 2), 'valor_total' => 'R$ ' . app_format_currency($item['valor'], false, 2));
    }

    public function processa_sms_porcentagem($item, $data_inicio, $data_fim){
        return array('item' => "{$item['cobranca_item_descricao']}<br/><strong class='text-danger '>CONFIGURAÇÃO NÃO PERMITIDA PARA ESSE TIPO</strong>", 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => '00,00', 'valor' => '00,00', 'valor_total' => '00,00');
    }

    public function processa_sms_valor($item, $data_inicio, $data_fim){
        $result = array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 0, 'valor' => 'R$ ' . app_format_currency($item['valor'], false, 2), 'valor_total' => '00,00');

        $sql = "
                 select COUNT(*) as total
                from comunicacao
                INNER JOIN produto_parceiro_comunicacao ON produto_parceiro_comunicacao.produto_parceiro_comunicacao_id = comunicacao.produto_parceiro_comunicacao_id
                INNER JOIN comunicacao_evento ON comunicacao_evento.comunicacao_evento_id = produto_parceiro_comunicacao.comunicacao_evento_id
                INNER JOIN comunicacao_tipo ON comunicacao_tipo.comunicacao_tipo_id = comunicacao_evento.comunicacao_tipo_id
                INNER JOIN produto_parceiro ON produto_parceiro.produto_parceiro_id = produto_parceiro_comunicacao.produto_parceiro_id
                WHERE 
                produto_parceiro.parceiro_id = {$item['parceiro_id']}
                AND produto_parceiro_comunicacao.deletado = 0
                AND produto_parceiro.deletado = 0
                AND comunicacao_tipo.slug = 'sms'
                AND comunicacao.criacao > '{$data_inicio} 00:00:00'
                AND comunicacao.criacao < '{$data_fim} 23:59:59'

        ";

        $result_array = $this->_database->query($sql)->result_array();

        $result_array = $result_array[0];
        $result['quantidade'] = $result_array['total'];
        $result['valor_total'] = 'R$ ' . app_format_currency($item['valor'] * $result_array['total'], false, 2);

        return $result;
    }

    public function processa_email_unica($item, $data_inicio, $data_fim){
        return array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 1, 'valor' => 'R$ ' . app_format_currency($item['valor'], false, 2), 'valor_total' => 'R$ ' . app_format_currency($item['valor'], false, 2));
    }

    public function processa_email_porcentagem($item, $data_inicio, $data_fim){
        return array('item' => "{$item['cobranca_item_descricao']}<br/><strong class='text-danger '>CONFIGURAÇÃO NÃO PERMITIDA PARA ESSE TIPO</strong>", 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 0, 'valor' => '00,00', 'valor_total' => '00,00');
    }

    public function processa_email_valor($item, $data_inicio, $data_fim){
        $result = array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 0, 'valor' => 'R$ ' . app_format_currency($item['valor'], false, 2), 'valor_total' => '00,00');

        $sql = "
                 select COUNT(*) as total
                from comunicacao
                INNER JOIN produto_parceiro_comunicacao ON produto_parceiro_comunicacao.produto_parceiro_comunicacao_id = comunicacao.produto_parceiro_comunicacao_id
                INNER JOIN comunicacao_evento ON comunicacao_evento.comunicacao_evento_id = produto_parceiro_comunicacao.comunicacao_evento_id
                INNER JOIN comunicacao_tipo ON comunicacao_tipo.comunicacao_tipo_id = comunicacao_evento.comunicacao_tipo_id
                INNER JOIN produto_parceiro ON produto_parceiro.produto_parceiro_id = produto_parceiro_comunicacao.produto_parceiro_id
                WHERE 
                produto_parceiro.parceiro_id = {$item['parceiro_id']}
                AND produto_parceiro_comunicacao.deletado = 0
                AND produto_parceiro.deletado = 0
                AND comunicacao_tipo.slug = 'email'
                AND comunicacao.criacao > '{$data_inicio} 00:00:00'
                AND comunicacao.criacao < '{$data_fim} 23:59:59'                

        ";

        $result_array = $this->_database->query($sql)->result_array();

        $result_array = $result_array[0];
        $result['quantidade'] = $result_array['total'];
        $result['valor_total'] = 'R$ ' . app_format_currency($item['valor'] * $result_array['total'], false, 2);

        return $result;
    }

    public function processa_cotacao_unica($item, $data_inicio, $data_fim){
        return array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 1, 'valor' => 'R$ ' . app_format_currency($item['valor'], false, 2), 'valor_total' => 'R$ ' . app_format_currency($item['valor'], false, 2));
    }

    public function processa_cotacao_porcentagem($item, $data_inicio, $data_fim){
        $result = array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 1, 'valor' => app_format_currency($item['valor'], false, 2) . ' %', 'valor_total' => '00,00');

        $sql = "
                select SUM(cotacao_seguro_viagem.premio_liquido_total) as valor_total, count(*) as total
                from cotacao
                INNER JOIN cotacao_seguro_viagem ON cotacao_seguro_viagem.cotacao_id = cotacao.cotacao_id
                WHERE 
                cotacao.parceiro_id = 16
                AND cotacao.deletado = 0
                AND cotacao_seguro_viagem.deletado = 0
                AND cotacao.criacao > '{$data_inicio} 00:00:00'
                AND cotacao.criacao < '{$data_fim} 23:59:59'                

        ";

        $result_array = $this->_database->query($sql)->result_array();

        $result_array = $result_array[0];
        $result['quantidade'] = $result_array['total'];
        $result['valor_total'] =  'R$ ' . app_format_currency(( $item['valor'] / 100 ) * $result_array['valor_total'] , false, 2);

        return $result;
    }

    public function processa_cotacao_valor($item, $data_inicio, $data_fim){
        $result = array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 0, 'valor' => 'R$ '. app_format_currency($item['valor'], false, 2), 'valor_total' => '00,00');

        $sql = "
                select COUNT(*) as total
                from cotacao
                WHERE 
                cotacao.parceiro_id = 16
                AND cotacao.deletado = 0
                AND cotacao.criacao > '{$data_inicio} 00:00:00'
                AND cotacao.criacao < '{$data_fim} 23:59:59'                

        ";

        $result_array = $this->_database->query($sql)->result_array();

        $result_array = $result_array[0];
        $result['quantidade'] = $result_array['total'];
        $result['valor_total'] = 'R$ '. app_format_currency($item['valor'] * $result_array['total'], false, 2);

        return $result;
    }


    public function processa_pedido_unica($item, $data_inicio, $data_fim){
        return array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 1, 'valor' => 'R$ ' . app_format_currency($item['valor'], false, 2), 'valor_total' => 'R$ ' . app_format_currency($item['valor'], false, 2));
    }

    public function processa_pedido_porcentagem($item, $data_inicio, $data_fim){
        $result = array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 1, 'valor' => app_format_currency($item['valor'], false, 2) . ' %', 'valor_total' => '00,00');

        $sql = "
                select SUM(pedido.valor_total) as valor_total, count(*) as total
                from cotacao
                INNER JOIN pedido ON pedido.cotacao_id = cotacao.cotacao_id
                WHERE 
                cotacao.parceiro_id = 16
                AND cotacao.deletado = 0
                AND pedido.deletado = 0
                AND pedido.criacao > '{$data_inicio} 00:00:00'
                AND pedido.criacao < '{$data_fim} 23:59:59'                

        ";

        $result_array = $this->_database->query($sql)->result_array();

        $result_array = $result_array[0];
        $result['quantidade'] = $result_array['total'];
        $result['valor_total'] =  'R$ ' . app_format_currency(( $item['valor'] / 100 ) * $result_array['valor_total'] , false, 2);

        return $result;
    }

    public function processa_pedido_valor($item, $data_inicio, $data_fim){
        $result = array('item' => $item['cobranca_item_descricao'], 'tipo' => $item['cobranca_tipo_nome'],  'quantidade' => 0, 'valor' => 'R$ '. app_format_currency($item['valor'], false, 2), 'valor_total' => '00,00');

        $sql = "
                select count(*) as total
                from cotacao
                INNER JOIN pedido ON pedido.cotacao_id = cotacao.cotacao_id
                WHERE 
                cotacao.parceiro_id = 16
                AND cotacao.deletado = 0
                AND pedido.deletado = 0
                AND pedido.criacao > '{$data_inicio} 00:00:00'
                AND pedido.criacao < '{$data_fim} 23:59:59'                

        ";

        $result_array = $this->_database->query($sql)->result_array();

        $result_array = $result_array[0];
        $result['quantidade'] = $result_array['total'];
        $result['valor_total'] = 'R$ '. app_format_currency($item['valor'] * $result_array['total'], false, 2);

        return $result;
    }



    function get_by_id($id)
    {
        return $this->get($id);
    }


}
