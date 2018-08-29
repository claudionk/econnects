<?php
Class Produto_Parceiro_Campo_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_campo';
    protected $primary_key = 'produto_parceiro_campo_id';

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
        array(
            'field' => 'produto_parceiro_id',
            'label' => 'Produto parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'campo_tipo_id',
            'label' => 'Tipo de Campo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'campo_id',
            'label' => 'Campo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'label',
            'label' => 'Label',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'opcoes',
            'label' => 'Opções',
            'groups' => 'default'
        ),
        array(
            'field' => 'validacoes',
            'label' => 'Validações',
            'groups' => 'default'
        ),
        array(
            'field' => 'ordem',
            'label' => 'Ordem',
            'groups' => 'default'
        ),
        array(
            'field' => 'tamanho',
            'label' => 'Tamanho',
            'groups' => 'default'
        ),

    );

    public function get_form_data($just_check = false)
    {
        $data = parent::get_form_data($just_check);

        if(isset($data['validacoes']) && is_array($data['validacoes']))
        {
            $data['validacoes'] = implode("|", $data['validacoes']);
        }

        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    function with_campo(){
        $this->with_simple_relation('campo', 'campo_', 'campo_id',
                array(
                       'nome', 'slug', 'nome_banco', 'nome_banco_equipamento', 'nome_banco_generico',
                      'nome_banco_viagem',  'opcoes', 'classes', 'function_salvar', 'function_exibir'));
        return $this;
    }

    function with_campo_tipo(){
        $this->_database->select('campo_tipo.nome, campo_tipo.slug');
        $this->_database->join('campo_tipo', "{$this->_table}.campo_tipo_id = campo_tipo.campo_tipo_id", 'inner');
        return $this;
    }

    function with_campo_classe(){
        $this->_database->select('campo_classe.nome as classe_nome, campo_classe.slug classe_slug');
        $this->_database->join('campo_classe', 'campo.campo_classe_id = campo_classe.campo_classe_id', 'inner');
        return $this;
    }

    function  filter_by_produto_parceiro($produto_parceiro_id){

        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);

        return $this;
    }

    function  filter_by_campo_tipo($campo_tipo_id){

        $this->_database->where("campo_tipo.campo_tipo_id", $campo_tipo_id);

        return $this;
    }

    function  filter_by_campo_tipo_slug($slug){

        $this->_database->where("campo_tipo.slug", $slug);

        return $this;
    }
    function  filter_by_campo_slug($slug){

        $this->_database->where("campo.slug", $slug);

        return $this;
    }

    function coreSelecCampoProdutoParceiro($produto_parceiro_id, $campo_tipo_id){
        $this->_database->select("{$this->_table}.campo_id");
        $this->_database->select("{$this->_table}.label");
        $this->_database->select("{$this->_table}.tamanho");
        $this->_database->select("{$this->_table}.opcoes");
        $this->_database->select("{$this->_table}.validacoes");
        $this->_database->select("campo.nome");
        $this->_database->select("campo.nome_banco");
        $this->_database->select("campo.slug");
        $this->_database->join('campo', "campo.campo_id = {$this->_table}.campo_id", 'inner');
        $this->_database->join('campo_tipo', "campo_tipo.campo_tipo_id = {$this->_table}.campo_tipo_id", 'inner');

        $this->_database->where("campo_tipo.campo_tipo_id", $campo_tipo_id);
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->order_by("{$this->_table}.ordem", 'ASC');
        return $this;
    }

    function buscaCampoUsado($campo_id){
        // $this->_database->select("count(1) c");

        // $this->_database->select("campo.nome");
        // $this->_database->select("produto_parceiro.nome");
        // $this->_database->select("parceiro.nome_fantasia");
        // $this->_database->select("campo_tipo.nome");

        $this->_database->join('produto_parceiro', "produto_parceiro.produto_parceiro_id = {$this->_table}.produto_parceiro_id", 'inner');
        $this->_database->join('parceiro', "parceiro.parceiro_id = produto_parceiro.parceiro_id", 'inner');
        $this->_database->join('campo', "campo.campo_id = {$this->_table}.campo_id", 'inner');
        $this->_database->join('campo_tipo', "campo_tipo.campo_tipo_id = {$this->_table}.campo_tipo_id", 'inner');

        $this->_database->where("campo.campo_id", $campo_id);
        $this->_database->where("{$this->_table}.deletado", 0);
        return $this;
    }

    /**
     * @description Seta os dados do post
     * @param $produto_parceiro_id
     * @param $slug
     * @param $plano
     * @param $dados
     * @return mixed
     */
    public function setDadosCampos($produto_parceiro_id, $produto, $slug, $plano, &$dados){

        $campos = $this->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug($slug)
            ->order_by("ordem", "asc")
            ->get_all();



        if($campos){
            foreach ($campos as $index => $campo) {
                if($this->input->post("plano_{$plano}_{$campo['campo_nome_banco']}")){

                    $value = $this->input->post("plano_{$plano}_{$campo['campo_nome_banco']}");
                    if(($campo['campo_function_salvar']) && (function_exists($campo['campo_function_salvar']))){
                        $value = call_user_func($campo['campo_function_salvar'], $value);
                    }
                    if(!empty($campo["campo_nome_banco_{$produto}"])) {
                        $dados[$campo["campo_nome_banco_{$produto}"]] = $value;
                    }
                }
                
            }
        }
        return $dados;
    }

    /**
     * @param $produto_parceiro_id
     * @param $slug
     * @param $plano
     * @return array
     */
    public function setValidacoesCamposPlano($produto_parceiro_id, $slug, $planos){
        $campos = $this->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug($slug)
            ->order_by("ordem", "asc")
            ->get_all();

        $validacoes = array();
        $planos = explode(';', $planos);

        if(($campos) && ($planos)){
            foreach ($planos as $idx => $plano) {
                foreach ($campos as $index => $campo) {
                    $validacao = $campo['validacoes'];
                    if(strpos($campo['validacoes'], 'matches') !== FALSE){
                        $validacao = str_replace('matches[password]', '', $validacao);
                        $validacao = str_replace('||', '|', $validacao);
                    }
                    $validacoes[] = array(
                        'field' => "plano_{$plano}_{$campo['campo_nome_banco']}",
                        'label' => "{$campo['campo_nome']}",
                        'rules' => $validacao,
                        'groups' => $slug
                    );
                    if(($campo['campo_slug'] == 'password') && (strpos($campo['validacoes'], 'matches')!== FALSE)){
                        $validacoes[] = array(
                            'field' => "plano_{$plano}_{$campo['campo_nome_banco']}_confirm",
                            'label' => "{$campo['campo_nome']}",
                            'rules' => "trim|required|check_password_confirm[plano_{$plano}_{$campo['campo_nome_banco']}]",
                            'groups' => $slug
                        );
                    }
                }
            }
        }
        return $validacoes;

    }

    /**
     * @param $produto_parceiro_id
     * @param $slug
     * @return array
     */
    public function setValidacoesCampos($produto_parceiro_id, $slug){
        $campos = $this->with_campo()
            ->with_campo_tipo()
            ->filter_by_produto_parceiro($produto_parceiro_id)
            ->filter_by_campo_tipo_slug($slug)
            ->order_by("ordem", "asc")
            ->get_all();

        $validacoes = array();

        //print_r($campos);exit;
        if($campos){
            foreach ($campos as $index => $campo) {

                $validacao = $campo['validacoes'];
                if(strpos($campo['validacoes'], 'matches') !== FALSE){
                    $validacao = str_replace('matches[password]', '', $validacao);
                    $validacao = str_replace('||', '|', $validacao);
                }
                $validacoes[] = array(
                    'field' => "{$campo['campo_nome_banco']}",
                    'label' => "{$campo['campo_nome']}",
                    'rules' => $validacao,
                    'groups' => $slug
                );
                if(($campo['campo_slug'] == 'password') && (strpos($campo['validacoes'], 'matches')!== FALSE)){
                    $validacoes[] = array(
                        'field' => "{$campo['campo_nome_banco']}_confirm",
                        'label' => "{$campo['campo_nome']}",
                        'rules' => "trim|required|check_password_confirm[{$campo['campo_nome_banco']}]",
                        'groups' => $slug
                    );
                }
            }
        }
        return $validacoes;

    }




}

