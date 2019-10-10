<?php
Class Parceiro_Relacionamento_Produto_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'parceiro_relacionamento_produto';
    protected $primary_key = 'parceiro_relacionamento_produto_id';

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

    protected $soma;
    protected $somatoria;

    //Dados
    public $validate = array(
        array(
            'field' => 'produto_parceiro_id',
            'label' => 'Produto',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'parceiro_tipo_id',
            'label' => 'Tipo de Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'pai_id',
            'label' => 'Hierarquia',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'repasse_comissao',
            'label' => 'Repasse Comissão',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'repasse_maximo',
            'label' => 'Repasse Máximo',
            'rules' => 'required|callback_check_repasse_maximo',
            'groups' => 'default'
        ),
        array( 
            'field' => 'comissao_tipo', 
            'label' => 'Tipo de Comissão', 
            'rules' => 'required', 
            'groups' => 'default' 
        ), 
        array(
            'field' => 'comissao',
            'label' => 'Comissão',
            'rules' => 'required|callback_check_markup_relacionamento',
            'groups' => 'default'
        ),
        array(
            'field' => 'comissao_indicacao',
            'label' => 'Comissão indicação',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'desconto_data_ini',
            'label' => 'Data de início',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'desconto_data_fim',
            'label' => 'Data final',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'desconto_valor',
            'label' => 'Desconto Valor',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'desconto_habilitado',
            'label' => 'Desconto Habilitado',
            'rules' => 'callback_check_desconto_habilitado',
            'groups' => 'default'
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_id'   => app_clear_number($this->input->post('produto_parceiro_id')),
            'parceiro_id'           => app_clear_number($this->input->post('parceiro_id')),
            'pai_id'                =>  (!empty($this->input->post('pai_id'))) ? app_clear_number($this->input->post('pai_id')) : 0,
            'repasse_comissao'      => $this->input->post('repasse_comissao'),
            'repasse_maximo'        => app_unformat_currency($this->input->post('repasse_maximo')),
            'comissao_tipo'         => $this->input->post('comissao_tipo'), 
            'comissao'              => $this->input->post('comissao_tipo') == 1 ? 0 : app_unformat_currency($this->input->post('comissao')), 
            'comissao_indicacao'    => app_unformat_currency($this->input->post('comissao_indicacao')),
            'desconto_data_ini'     => app_dateonly_mask_to_mysql($this->input->post('desconto_data_ini')),
            'desconto_data_fim'     => app_dateonly_mask_to_mysql($this->input->post('desconto_data_fim')),
            'desconto_valor'        => app_unformat_currency($this->input->post('desconto_valor')),
            'desconto_habilitado'   => $this->input->post('desconto_habilitado'),
            'parceiro_tipo_id'      => isempty($this->input->post('parceiro_tipo_id'), null),
        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    public function with_produto_parceiro(){
        return $this->with_simple_relation_foreign('produto_parceiro', 'produto_parceiro_', 'produto_parceiro_id', 'produto_parceiro_id', array('nome'), 'inner');
    }

    public function with_parceiro(){
        return $this->with_simple_relation_foreign('parceiro', 'parceiro_', 'parceiro_id', 'parceiro_id', array('nome','cnpj','codigo_susep','codigo_corretor'), 'inner');
    }

    public function with_parceiro_tipo(){
        $this->_database->select("parceiro_tipo.nome as parceiro_tipo, parceiro_tipo.codigo_interno, parceiro_tipo.parceiro_tipo_id", "left");
        $this->_database->join("parceiro_tipo", "IFNULL({$this->_table}.parceiro_tipo_id,parceiro.parceiro_tipo_id) = parceiro_tipo.parceiro_tipo_id", "left");
        return $this;
    }

    public function get_comissao($produto_parceiro_id, $parceiro_id, $comissao = 0){

        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        $this->_database->where('parceiro_id', $parceiro_id);

        $rows = $this->get_all();

        if($rows){
            $rows = $rows[0];
            $rows['comissao'] = $this->define_comissao_rep($produto_parceiro_id, $rows['comissao'], $rows['comissao_tipo'], $comissao);
            return $rows;
        }else{
            return array();
        }

    }

    /**
     * Retorna a comissão do corretor configurada
     * @param int produto_parceiro_id
     * @return float
     */
    public function get_comissao_corretor($produto_parceiro_id){

        $this->_database->join("produto_parceiro", "{$this->_table}.produto_parceiro_id = produto_parceiro.produto_parceiro_id", "join");
        $this->_database->join("parceiro", "{$this->_table}.parceiro_id = parceiro.parceiro_id", "join");
        $this->_database->join("parceiro_tipo", "parceiro.parceiro_tipo_id = parceiro_tipo.parceiro_tipo_id", "join");
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        $this->_database->where("parceiro_tipo.codigo_interno", "corretora");
        $rows = $this->get_all();

        $comissao_corretor = 0;
        if($rows){
            $rows = $rows[0];
            $comissao_corretor = $rows['comissao'];
        }

        return $comissao_corretor;
    }

    public function get_desconto($produto_parceiro_id, $parceiro_id){

        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        $this->_database->where('parceiro_id', $parceiro_id);
        $this->_database->where('desconto_habilitado', 1);
        $this->_database->where('desconto_data_ini <', date('Y-m-d H-i-s'));
        $this->_database->where('desconto_data_fim >', date('Y-m-d H-i-s'));

        $rows = $this->get_all();

        if($rows){
            return $rows[0];
        }else{
            return array();
        }

    }

    public function is_desconto_produto_habilitado($produto_parceiro_id){

        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        $this->_database->where('desconto_habilitado', 1);

        $rows = $this->get_all();

        if($rows){
            return TRUE;
        }else{
            return FALSE;
        }

    }

    public function get_comissao_markup($produto_parceiro_id, $parceiro_id, $comissao_cotacao = 0){

        $this->_database->where("produto_parceiro_id", $produto_parceiro_id);
        $this->_database->where("parceiro_id", $parceiro_id);
        $rows = $this->get_all();

        if($rows){
            $row = $rows[0];
            $soma = $this->define_comissao_rep($produto_parceiro_id, $row['comissao'], $row['comissao_tipo'], $comissao_cotacao);
            while( intval( $row["pai_id"] ) != 0 ) {
                $linha = $this->get( $row["pai_id"] );
                if( $linha ) {
                    $row = $linha;
                    $soma += $this->define_comissao_rep($produto_parceiro_id, $row['comissao'], $row['comissao_tipo'], $comissao_cotacao);
                } else {
                    $row["pai_id"] = 0;
                }
            }
            return $soma;

        } else {
            return 0;
        }

    }

    public function define_comissao_rep($produto_parceiro_id, $comissao, $comissao_tipo = 0, $comissao_cot = 0)
    {
        // Se a comissão for variável
        if ($comissao_tipo != 0) {
            // se precisar retirar a comissão do corretor
            $comissao = $comissao_cot - (($comissao_tipo == 1) ? $this->get_comissao_corretor($produto_parceiro_id) : 0);
        }

        return $comissao;
    }

    public function get_todas_comissoes($produto_parceiro_id, $parceiro_relacionamento_produto_id = 0, $parceiro_id = 0){
        $this->soma = 0;
        $this->somatoria = [];
        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        $this->_database->where("comissao_tipo", 0);

        $rows = $this->get_all(0, 0, true, 'ASC');

        if(!empty($rows)){
            $oArray = [];
            $parceiro_id_pai = 0;

            foreach ($rows as $row) {
                // echo "id = ".$row["parceiro_relacionamento_produto_id"] ." - ". $row['parceiro_id'] ." - ". $row['pai_id'] ."<br>";

                if ( $row['pai_id'] == 0 ){

                    $parceiro_id_pai = $row['parceiro_id'];
                    $oArray = [
                        $parceiro_id_pai => [
                            "comissao" => $row['comissao']
                        ]
                    ];

                } else {

                    $result = $this
                        ->filter_by_pai($row["parceiro_relacionamento_produto_id"])
                        ->filter_by_produto_parceiro($produto_parceiro_id)
                        ->get_all();

                    if ( !empty($result) ) {
                        $oArray[$parceiro_id_pai][$row['parceiro_id']] = [
                            'comissao' => $row['comissao'],
                        ];

                        foreach ($result as $r) {
                            $oArray[$parceiro_id_pai][$row['parceiro_id']][$r['parceiro_id']] = [
                                "comissao" => $r['comissao']
                            ];
                        }
                    }

                }

            }

            $this->analisaComissoes($oArray, $parceiro_id);
        }

        return $this->soma;
    }

    private function analisaComissoes($oArray, $parceiro_id = 0, $nivel = 0, &$break = false){
        if (empty($oArray)) return 0;

        foreach ($oArray as $key => $value) {

            $this->soma = (isset($this->somatoria[$nivel-1])) ? $this->somatoria[$nivel-1] : 0;

            if (!empty($parceiro_id) && $parceiro_id == $key) {
                $break = true;
                return $this->soma;
            }

            // echo "<b>KEY:{$key} - NIVEL: {$nivel} </b> <br>";
            $this->soma += $value['comissao'];
            $this->somatoria[$nivel] = $this->soma;
            // echo $this->soma . "<br>";

            unset($value['comissao']);

            if (!empty($value) && is_array($value) ){
                $this->analisaComissoes($value, $parceiro_id, $nivel+1, $break);
                if ($break === TRUE) {
                    return $this->soma;
                }
            }
        }

        return $this->maiorValor();
    }

    /**
     * Retorna os ids de todos permitidos
     * @param array retorna os parceiros habilitados
     * @return mixed
     */
    public function get_parceiros_permitidos($produto_parceiro_id, $parceiro_id){
        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        $this->_database->where('parceiro_id', $parceiro_id);

        $rows = $this->get_all(0, 0, true, 'ASC');
        $oArray = [];

        if(!empty($rows)){
            $oArray = $this->getSetParc($oArray, $rows);
        }

        return $oArray;
    }

    private function getSetParc(&$oArray, $rows){
        if ( !empty($rows) ) {
            foreach ($rows as $row) {
                $oArray[] = $row['parceiro_id'];

                $result = $this->filter_by_pai($row["parceiro_relacionamento_produto_id"])->filter_by_produto_parceiro($row["produto_parceiro_id"])->get_all(0,0,false);

                if (!empty($result))
                    return $this->getSetParc($oArray, $result);
            }
        }

        return $oArray;
    }

    private function maiorValor(){
        if (empty($this->somatoria)) return 0;

        foreach ($this->somatoria as $val) {
            if ($val > $this->soma)
                $this->soma = $val;
        }
        return $this->soma;
    }

    function filter_by_pai($pai_id){
        $this->_database->where('pai_id', $pai_id);
        return $this;
    }

    function filter_by_comissao_tipo($comissao_tipo){
        $this->_database->where('comissao_tipo', $comissao_tipo);
        return $this;
    }

    function filter_by_produto_parceiro($produto_parceiro_id){
        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        return $this;
    }

    function filter_by_parceiro_tipo($parceiro_tipo_id){
        $this->_database->where('parceiro.parceiro_tipo_id', $parceiro_tipo_id);
        return $this;
    }


    function filter_by_parceiro($parceiro_id){
        $this->_database->where('parceiro_id', $parceiro_id);
        return $this;
    }

    public function getRelacionamentoProduto($produto_parceiro_id = 0, $pai_id = 0, &$arr){

        $this->load->model('parceiro_model', 'parceiro');
        $relacionamentos = $this->filter_by_pai($pai_id)->filter_by_produto_parceiro($produto_parceiro_id)->get_all();

        if($relacionamentos){
            foreach ($relacionamentos as $relacionamento) {
                $relacionamento['itens'] = array();
                $relacionamento['parceiro'] = $this->parceiro->get($relacionamento['parceiro_id']);
                $this->getRelacionamentoProduto($produto_parceiro_id, $relacionamento['parceiro_relacionamento_produto_id'], $relacionamento['itens']);
                $arr[] = $relacionamento;
            }
        }

    }

    public function get_all($limit = 0, $offset = 0, $processa = true, $order_by = null) {
        if($processa) {
            $parceiro_id = $this->session->userdata('parceiro_id');
            $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
        }
        if (!empty($order_by)) {
            $this->order_by("{$this->_table}.produto_parceiro_id, {$this->_table}.pai_id", $order_by);
        }

        return parent::get_all($limit, $offset);
    }

    public function get_total($processa = true) {
        if($processa) {
            //Efetua join com cotação
            //$this->_database->join("parceiro as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");
            $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
        }
        return parent::get_total(); // TODO: Change the autogenerated stub
    }

}
