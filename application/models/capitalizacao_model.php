<?php
Class Capitalizacao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'capitalizacao';
    protected $primary_key = 'capitalizacao_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'descricao');


    //Dados
    public $validate = array(
        array(
            'field' => 'capitalizacao_tipo_id',
            'label' => 'Tipo de Capitalização',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'Descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_inicio',
            'label' => 'Início Distribuição',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
        array(
            'field' => 'data_fim',
            'label' => 'Fim Distribuição',
            'rules' => 'required|validate_data',
            'groups' => 'default'
        ),
        array(
            'field' => 'titulo_randomico',
            'label' => 'Título randômico',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'qtde_titulos_por_compra',
            'label' => 'Quantidade de Títulos por compra',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor_minimo_participacao',
            'label' => 'Valor Mínimo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor_custo_titulo',
            'label' => 'Custo do título',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor_sorteio',
            'label' => 'Valor Sorteio',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'ativo',
            'label' => 'Ativo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'serie',
            'label' => 'Série',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'capitalizacao_sorteio_id',
            'label' => 'Tipo de sorteio',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'qnt_sorteio',
            'label' => 'Quantidade Sorteios',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'num_remessa',
            'label' => 'Número Seqüencial Remessa',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'capitalizacao_tipo_id' => $this->input->post('capitalizacao_tipo_id'),
            'capitalizacao_sorteio_id' => $this->input->post('capitalizacao_sorteio_id'),
            'qnt_sorteio' => $this->input->post('qnt_sorteio'),
            'nome' => $this->input->post('nome'),
            'descricao' => $this->input->post('descricao'),
            'data_inicio' => app_dateonly_mask_to_mysql($this->input->post('data_inicio')),
            'data_fim' => app_dateonly_mask_to_mysql($this->input->post('data_fim')),
            'titulo_randomico' => $this->input->post('titulo_randomico'),
            'qtde_titulos_por_compra' => $this->input->post('qtde_titulos_por_compra'),
            'valor_minimo_participacao' => app_unformat_currency($this->input->post('valor_minimo_participacao')),
            'valor_custo_titulo' => app_unformat_currency($this->input->post('valor_custo_titulo')),
            'valor_sorteio' => app_unformat_currency($this->input->post('valor_sorteio')),
            'num_remessa' => $this->input->post('num_remessa'),
            'ativo' => $this->input->post('ativo'),
            'serie' => $this->input->post('serie'),

        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    //Agrega tipo de capitalização
    function with_capitalizacao_tipo($fields = array('nome'))
    {
        $this->with_simple_relation('capitalizacao_tipo', 'capitalizacao_tipo_', 'capitalizacao_tipo_id', $fields );
        return $this;
    }

    function getTituloNaoUtilizado($capitalizacao_id){


        $date = date('Y-m-d H:i:s');
        $sql = "
                SELECT *
                from capitalizacao
                inner JOIN capitalizacao_serie ON capitalizacao.capitalizacao_id = capitalizacao_serie.capitalizacao_id
                inner JOIN capitalizacao_serie_titulo ON capitalizacao_serie.capitalizacao_serie_id = capitalizacao_serie_titulo.capitalizacao_serie_id
                where 
                capitalizacao.capitalizacao_id = {$capitalizacao_id}
                and capitalizacao_serie.ativo = 1
                and capitalizacao_serie.deletado = 0
                and capitalizacao_serie_titulo.utilizado = 0
                and capitalizacao_serie_titulo.ativo = 1 
                and capitalizacao_serie.data_inicio < '{$date}'                       
                and capitalizacao_serie.data_fim > '{$date}'                       
                LIMIT 1;
        
        ";


        return $this->_database->query($sql)->result_array();

    }


    function get_titulos_pedido($pedido_id){
        $sql = "
                SELECT *
                from capitalizacao
                inner JOIN capitalizacao_serie ON capitalizacao.capitalizacao_id = capitalizacao_serie.capitalizacao_id
                inner JOIN capitalizacao_serie_titulo ON capitalizacao_serie.capitalizacao_serie_id = capitalizacao_serie_titulo.capitalizacao_serie_id
                where 
                capitalizacao_serie.ativo = 1
                and capitalizacao_serie.deletado = 0
                  and capitalizacao_serie_titulo.ativo = 1 
                and capitalizacao_serie_titulo.pedido_id = {$pedido_id}
                ORDER BY capitalizacao_serie_titulo.data_compra 
        
        ";


        return $this->_database->query($sql)->result_array();
    }
}
