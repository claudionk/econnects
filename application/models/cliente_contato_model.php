<?php
Class Cliente_Contato_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'cliente_contato';
    protected $primary_key = 'cliente_contato_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';
    
    //Dados
    public $validate = array(
        array(
            'field' => 'cliente_id',
            'label' => 'Cliente',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'cliente_contato_cargo_id',
            'label' => 'Cargo',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'cliente_contato_departamento_id',
            'label' => 'Departamento',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'cliente_contato_nivel_relacionamento_id',
            'label' => 'Nível de Relacionamento',
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
            'field' => 'decisor',
            'label' => 'Decisor',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'contato_tipo_id',
            'label' => 'Tipo de Contato',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'contato',
            'label' => 'Contato',
            'rules' => 'required|validate_contato',
            'groups' => 'default'
        )
        
       
    );

    //Retorna por Id
    function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    //Retorna por Cliente
    function filter_by_cliente($id)
    {
        $this->db->where($this->_table. '.cliente_id', $id);
        $this->db->where($this->_table. '.deletado', 0);
        return $this;
    }


    //Retorna por Cliente
    function get_by_cliente($id)
    {
        $this->db->select($this->_table. '.*');
        $this->db->from($this->_table);
        $this->db->where($this->_table. '.cliente_id', $id);
        $this->db->where($this->_table. '.deletado', 0);

        $query = $this->db->get();

        if ($query->num_rows() > 0) 
        {
            $data = $query->result_array();
            return $data;
        }
        else
        {
            return array();
        }
    }

    //Agrega colaborador
    function with_contato_cargo($fields = array('nome'))
    {
        $this->with_simple_relation('cliente_contato_cargo', 'cliente_contato_cargo_', 'cliente_contato_cargo_id', $fields );
        return $this;
    }

    //Agrega contato
    function with_contato($fields = array('contato.nome', 'contato.contato', 'contato.contato_tipo_id', 'contato_tipo.nome as contato_tipo'))
    {


        $this->_database->select($fields);
        $this->_database->join('contato', 'contato.contato_id = cliente_contato.contato_id');
        $this->_database->join('contato_tipo', 'contato_tipo.contato_tipo_id = contato.contato_tipo_id');
        return $this;
    }

    //Agrega contato unicos
    function select_contato_unico()
    {

        $this->_database->select("(SELECT contato FROM contato WHERE contato.contato_id = cliente_contato.contato_id AND contato.deletado = 0 AND contato.contato_tipo_id = 1 LIMIT 1) AS email");
        $this->_database->select("(SELECT contato FROM contato WHERE contato.contato_id = cliente_contato.contato_id AND contato.deletado = 0 AND contato.contato_tipo_id = 2 LIMIT 1)  AS celular");
        $this->_database->select("(SELECT contato FROM contato WHERE contato.contato_id = cliente_contato.contato_id AND contato.deletado = 0 AND contato.contato_tipo_id = 3 LIMIT 1) AS telefone");
        $this->_database->select("(SELECT contato FROM contato WHERE contato.contato_id = cliente_contato.contato_id AND contato.deletado = 0 LIMIT 1) AS nome");
        return $this;
    }

    //Agrega colaborador
    function with_contato_departamento($fields = array('nome'))
    {
        $this->with_simple_relation('cliente_contato_departamento', 'cliente_contato_departamento_', 'cliente_contato_departamento_id', $fields );
        return $this;
    }

    function insert_contato($data){

        $this->load->model('contato_model', 'contato');

        $data_contato = array();
        $data_contato['contato_tipo_id'] = $data['contato_tipo_id'];
        $data_contato['nome'] = $data['nome'];
        $data_contato['contato'] = ($data['contato_tipo_id'] != 1) ? app_retorna_numeros($data['contato']) : mb_strtolower($data['contato'], 'UTF-8');
        $data_contato['melhor_horario'] = isset( $data['melhor_horario']) ? $data['melhor_horario'] : 'Q';

        $contato_id = $this->contato->insert($data_contato, TRUE);

        $data_cliente_contato = array();
        $data_cliente_contato['contato_id'] = $contato_id;
        $data_cliente_contato['cliente_id'] = $data['cliente_id'];
        $data_cliente_contato['cliente_contato_nivel_relacionamento_id'] = (int)$data['cliente_contato_nivel_relacionamento_id'];
        $data_cliente_contato['cliente_contato_cargo_id'] = (isset($data['cliente_contato_cargo_id'])) ? (int)$data['cliente_contato_cargo_id'] : 0;
        $data_cliente_contato['cliente_contato_departamento_id'] = (isset($data['cliente_contato_departamento_id'])) ? (int)$data['cliente_contato_departamento_id'] : 0;
        $data_cliente_contato['decisor'] = (int)$data['decisor'];

        return $this->insert($data_cliente_contato, TRUE);
    }

    function insert_not_exist_contato($data){

        $this->load->model('contato_model', 'contato');

        $contato = $this->with_contato()->get_many_by(array(
            'contato.contato_tipo_id' =>  $data['contato_tipo_id'],
            'contato.contato' =>  ($data['contato_tipo_id'] != 1) ? app_retorna_numeros($data['contato']) :  mb_strtolower($data['contato'], 'UTF-8'),
            'cliente_contato.cliente_id' => $data['cliente_id']
        ));

        if(count($contato) == 0) {
            $data_contato = array();
            $data_contato['contato_tipo_id'] = $data['contato_tipo_id'];
            $data_contato['nome'] = $data['nome'];
            $data_contato['cliente_terceiro'] = isset( $data['cliente_terceiro']) ? $data['cliente_terceiro'] : 0;
            $data_contato['melhor_horario'] = isset( $data['melhor_horario']) ? $data['melhor_horario'] : 'Q';
            $data_contato['contato'] = ($data['contato_tipo_id'] != 1) ? app_retorna_numeros($data['contato']) : mb_strtolower($data['contato'], 'UTF-8');


            $contato_id = $this->contato->insert($data_contato, TRUE);

            $data_cliente_contato = array();
            $data_cliente_contato['contato_id'] = $contato_id;
            $data_cliente_contato['cliente_id'] = $data['cliente_id'];
            $data_cliente_contato['cliente_contato_nivel_relacionamento_id'] = $data['cliente_contato_nivel_relacionamento_id'];
            $data_cliente_contato['cliente_contato_cargo_id'] = isset($data['cliente_contato_cargo_id']) ? $data['cliente_contato_cargo_id'] : 0;
            $data_cliente_contato['cliente_contato_departamento_id'] = isset($data['cliente_contato_departamento_id']) ? $data['cliente_contato_departamento_id'] : 0 ;
            $data_cliente_contato['decisor'] = isset($data['decisor']) ? $data['decisor'] :0  ;

            return $this->insert($data_cliente_contato, TRUE);
        }else{
            return FALSE;
        }
    }

    function update_contato($data){

        $this->load->model('contato_model', 'contato');

        $data_contato = array();
        $data_contato['contato_tipo_id'] = $data['contato_tipo_id'];
        $data_contato['nome'] = $data['nome'];
        $data_contato['contato'] = ($data['contato_tipo_id'] != 1) ? app_retorna_numeros($data['contato']) : $data['contato'];

        $this->contato->update( $data['contato_id'], $data_contato, TRUE);

        $data_cliente_contato = array();
        $data_cliente_contato['cliente_contato_nivel_relacionamento_id'] = (int)$data['cliente_contato_nivel_relacionamento_id'];
        $data_cliente_contato['cliente_contato_cargo_id'] = (int)$data['cliente_contato_cargo_id'];
        $data_cliente_contato['cliente_contato_departamento_id'] = (int)$data['cliente_contato_departamento_id'];
        $data_cliente_contato['decisor'] = (int)$data['decisor'];

        return $this->update($data['cliente_contato_id'], $data_cliente_contato, TRUE);
    }

    public function melhorHorario( $search = null, $typeSearch = 'slug', $return = null )
    {
        $oRet = array(
            ['slug' => 'M', 'nome' => 'MANHÃ'],
            ['slug' => 'T', 'nome' => 'TARDE'],
            ['slug' => 'N', 'nome' => 'NOITE'],
            ['slug' => 'C', 'nome' => 'COMERCIAL'],
            ['slug' => 'Q', 'nome' => 'QUALQUER HORARIO'],
        );

        if (!empty($search)) {
            $results = $this->search($oRet, $typeSearch, $search);
            if (!empty($results)) {
                if ( !empty($return) ) {
                    $oRet = $results[$return];
                } else {
                    $oRet = $results;
                }
            } else {
                if (!empty($results)) {
                    $oRet = $oRet[ count($oRet)-1 ][$return];
                } else {
                    $oRet = $oRet[ count($oRet)-1 ]['slug'];
                }
            }

        }

        return $oRet;
    }
}
