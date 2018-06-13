<?php
Class Integracao_Layout_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao_layout';
    protected $primary_key = 'integracao_layout_id';

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
            'field' => 'tipo',
            'label' => 'Tipo',
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
            'label' => 'descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'formato',
            'label' => 'Formato',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'campo_tipo',
            'label' => 'Tipo de Campo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'tamanho',
            'label' => 'Tamanho',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'obrigatorio',
            'label' => 'Obrigatório',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inicio',
            'label' => 'Início',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inicio',
            'label' => 'Fim',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome_banco',
            'label' => 'Nome no Banco',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'function',
            'label' => 'Function',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'valor_padrao',
            'label' => 'Valor padrão',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'qnt_valor_padrao',
            'label' => 'Quantidade Valor Padrão',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'campo_log',
            'label' => 'ID do LOG',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'insert',
            'label' => 'Será usado no insert',
            'rules' => '',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'integracao_id' => $this->input->post('integracao_id'),
            'tipo' => $this->input->post('tipo'),
            'ordem' => $this->filter_by_integracao($this->input->post('integracao_id'))->get_total(),
            'nome' => $this->input->post('nome'),
            'descricao' => $this->input->post('descricao'),
            'formato' => $this->input->post('formato'),
            'campo_tipo' => $this->input->post('campo_tipo'),
            'tamanho' => $this->input->post('tamanho'),
            'obrigatorio' => $this->input->post('obrigatorio'),
            'inicio' => $this->input->post('inicio'),
            'fim' => $this->input->post('fim'),
            'nome_banco' => $this->input->post('nome_banco'),
            'function' => $this->input->post('function'),
            'valor_padrao' => $this->input->post('valor_padrao'),
            'qnt_valor_padrao' => $this->input->post('qnt_valor_padrao'),
        );
        return $data;
    }

    function filter_by_integracao($integracao_id){

        $this->_database->where('integracao_layout.integracao_id', $integracao_id);

        return $this;
    }

    function filter_by_tipo($tipo){

        $this->_database->where('integracao_layout.tipo', $tipo);

        return $this;
    }

    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    public function filterFromInput($filter = NULL, $data = NULL, $thisTable = true, $or = true){

        if($this->input->get('filter')) {

            $filters = $this->input->get('filter');



            if (isset($filters['nome']) && $filters['nome'] != '') {

                $query = $filters['nome'];
                $this->db->like("{$this->_table}.nome", $query );
            }

            $field = 'tipo';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = $filters[$field];
                $this->db->where("{$this->_table}.{$field}", $query );
            }

            $field = 'campo_tipo';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = $filters[$field];
                $this->db->where("{$this->_table}.{$field}", $query );
            }
          }

        return $this;
    }
}
