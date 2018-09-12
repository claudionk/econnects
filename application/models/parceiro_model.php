<?php
Class Parceiro_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'parceiro';
    protected $primary_key = 'parceiro_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'nome_fantasia', 'endereco', 'bairro', 'complemento');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'nome',
            'label' => 'Razão Social',
            'rules' => 'required',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'nome_fantasia',
            'label' => 'Nome Fantasia',
            'rules' => 'required',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'matriz_id',
            'label' => 'Matriz',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'cnpj',
            'label' => 'CNPJ',
            'rules' => 'required|validate_cnpj|check_parceiro_cnpj_exists',
            'groups' => 'add'
        ),
        array(
            'field' => 'codigo_susep',
            'label' => 'Código Susep',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'codigo_sucursal',
            'label' => 'Código Sucursal',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'codigo_corretor',
            'label' => 'Código Corretor',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'theme',
            'label' => 'Tema',
            'rules' => 'required',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'cnpj',
            'label' => 'CNPJ',
            'rules' => 'required|validate_cnpj|check_parceiro_cnpj_owner',
            'groups' => 'edit'
        ),
        array(
            'field' => 'parceiro_status_id',
            'label' => 'Status',
            'rules' => 'required',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'cep',
            'label' => 'Cep',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'endereco',
            'label' => 'Endereço',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'numero',
            'label' => 'Número',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'bairro',
            'label' => 'Bairro',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'localidade_cidade_id',
            'label' => 'Cidade',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'logo',
            'label' => 'Logo',
            'rules' => '',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'parceiro_tipo_id',
            'label' => 'Tipo',
            'rules' => 'required',
            'groups' => 'default, add, edit'
        ),
        array(
            'field' => 'termo_aceite_usuario',
            'label' => 'Termo Usuário',
            'rules' => 'required',
            'groups' => 'default, add, edit'
        )

    );

    //Get dados
    public function get_form_data($just_check = false)
    {

        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'nome_fantasia' => $this->input->post('nome_fantasia'),
            'cnpj' => app_clear_number($this->input->post('cnpj')),
            'parceiro_status_id' => $this->input->post('parceiro_status_id'),
            'parceiro_tipo_id' => $this->input->post('parceiro_tipo_id'),
            'codigo_susep' => $this->input->post('codigo_susep'),
            'codigo_sucursal' => $this->input->post('codigo_sucursal'),
            'codigo_corretor' => $this->input->post('codigo_corretor'),
            'matriz_id' => $this->input->post("matriz_id"),

            //aparencia
            'theme' => $this->input->post('theme'),
            'slug' => $this->input->post('slug'),
            'apelido' => $this->input->post('apelido'),
            'logo' => $this->input->post('logo'),

            //Endereço
            'cep' => app_clear_number($this->input->post('cep')),
            'endereco' => $this->input->post('endereco'),
            'numero' => app_clear_number($this->input->post('numero')),
            'complemento' => $this->input->post('complemento'),
            'bairro' => $this->input->post('bairro'),
            'localidade_estado_id' => $this->input->post('localidade_estado_id'),
            'localidade_cidade_id' => $this->input->post('localidade_cidade_id'),

            //Extranet
            'extranet_url' => $this->input->post('extranet_url'),
            'extranet_codigo_acesso' => $this->input->post('extranet_codigo_acesso'),
            'extranet_senha' => $this->input->post('extranet_senha'),

            //Termo Aceite Usuário
            'termo_aceite_usuario' => $this->input->post('termo_aceite_usuario'),


            'api_host' => $this->input->post('api_host'),
            'api_key' => $this->input->post('api_key'),
            'api_senha' => $this->input->post('api_senha'),




        );


        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }

    public function filter_by_cnpj($cnpj){

        $this->_database->where('cnpj',$cnpj );

        return $this;
    }

    public function filter_matriz($parceiro_id){

        $this->_database->where('parceiro_id !=', $parceiro_id );

        return $this;
    }

    public function filter_by_tipo($parceiro_tipo_id){

        $this->_database->where('parceiro_tipo_id', $parceiro_tipo_id );

        return $this;
    }

    public function filter_by_not_parceiro($parceiro_id){

        $this->_database->where('parceiro_id <> ', $parceiro_id );

        return $this;
    }

    public function with_parceiro_tipo(){

        return $this->with_simple_relation('parceiro_tipo', 'parceiro_tipo_', 'parceiro_tipo_id', array('nome'), 'left');
    }

    public function filterFromInput($filter = NULL, $data = NULL, $thisTable = true, $or = true){

        if($this->input->get('filter')) {

            $filters = $this->input->get('filter');



            if (isset($filters['nome']) && $filters['nome'] != '') {

                $query = $filters['nome'];
                $this->db->like("{$this->_table}.nome", $query );
            }

            $field = 'cnpj';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = app_clear_number($filters[$field]);
                $this->db->like("{$this->_table}.{$field}", $query );
            }

            $field = 'nome_fantasia';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = $filters[$field];
                $this->db->like("{$this->_table}.{$field}", $query );
            }
            $field = 'apelido';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = $filters[$field];
                $this->db->like("{$this->_table}.{$field}", $query );
            }
            $field = 'codigo_susep';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = $filters[$field];
                $this->db->like("{$this->_table}.{$field}", $query );
            }
            $field = 'codigo_sucursal';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = $filters[$field];
                $this->db->like("{$this->_table}.{$field}", $query );
            }
            $field = 'codigo_corretor';
            if (isset($filters[$field]) && $filters[$field] != '') {

                $query = $filters[$field];
                $this->db->like("{$this->_table}.{$field}", $query );
            }
        }

        return $this;
    }

    /**
     * Retorna todos
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
  /*
    public function get_all($limit = 0, $offset = 0, $processa = true) {
        if($processa)
        {
            $parceiro_id = $this->session->userdata('parceiro_id');

            $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
        }
        return parent::get_all($limit, $offset);
    }
    
    */
  
    /**
     * Retorna todos
     * @return mixed
     */

    public function get_total($processa = true) {
        if($processa)
        {
            //Efetua join com cotação
            //$this->_database->join("parceiro as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

            $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
        }
        return parent::get_total(); // TODO: Change the autogenerated stub
    }

}





