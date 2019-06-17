<?php
class Cliente_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'cliente';
    protected $primary_key = 'cliente_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';
    protected $salt            = '174mJuR18mS0lhgKL2J0CETRlN252x';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('razao_nome', 'nome_fantasia', 'endereco', 'bairro', 'complemento');

    //Dados
    public $validate = array(
        array(
            'field'  => 'cnpj_cpf',
            'label'  => 'CNPJ / CPF',
            'rules'  => 'trim|required|min_length[11]|max_lenght[14]|validate_cnpj_cpf',
            'groups' => 'default',
        ),
        array(
            'field'   => 'colaborador_id',
            'label'   => 'Responsável Cadastro',
            'rules'   => 'required',
            'groups'  => 'default',
            'foreign' => 'colaborador',
        ),
        array(
            'field'  => 'colaborador_comercial_id',
            'label'  => 'Responsável Comercial',
            'rules'  => 'required|integer',
            'groups' => 'default',
        ),
        array(
            'field'   => 'cliente_evolucao_status_id',
            'label'   => 'Status',
            'rules'   => 'required|integer',
            'groups'  => 'default',
            'foreign' => 'cliente_evolucao_status',
        ),
        array(
            'field'  => 'grupo_empresarial_id',
            'label'  => 'Grupo Empresarial',
            'rules'  => 'integer',
            'groups' => 'default',
        ),
        array(
            'field'  => 'localidade_cidade_id',
            'label'  => 'Cidade',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'titular',
            'label'  => 'Titular',
            'rules'  => '',
            'groups' => 'default',
        ),
        array(
            'field'  => 'tipo_cliente',
            'label'  => 'Tipo do cliente',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'endereco',
            'label'  => 'Endereço',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'bairro',
            'label'  => 'Bairro',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'complemento',
            'label'  => 'Complemento',
            'rules'  => '',
            'groups' => 'default',
        ),
        array(
            'field'  => 'numero',
            'label'  => 'Número',
            'rules'  => '',
            'groups' => 'default',
        ),
        array(
            'field'  => 'codigo',
            'label'  => 'Código',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'ie_rg',
            'label'  => 'Inscrição Estadual / RG',
            'rules'  => 'exact_lenght[9]',
            'groups' => 'default',
        ),
        array(
            'field'  => 'razao_nome',
            'label'  => 'Razão Social / Nome',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'nome_fantasia',
            'label'  => 'Nome Fantasia',
            'rules'  => '',
            'groups' => 'default',
        ),
        array(
            'field'  => 'cep',
            'label'  => 'CEP',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'site',
            'label'  => 'Site',
            'rules'  => '',
            'groups' => 'default',
        ),
        array(
            'field'  => 'pabx',
            'label'  => 'PABX',
            'rules'  => '',
            'groups' => 'default',
        ),
        array(
            'field'  => 'data_nascimento',
            'label'  => 'Data de nascimento / fundação',
            'rules'  => '',
            'groups' => 'default',
        ),
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data = array(
            'cnpj_cpf'                   => app_retorna_numeros($this->input->post('cnpj_cpf')),
            'codigo'                     => $this->input->post('codigo'),
            'colaborador_id'             => $this->input->post('colaborador_id'),
            'colaborador_comercial_id'   => $this->input->post('colaborador_comercial_id'),
            'grupo_empresarial_id'       => $this->input->post('grupo_empresarial_id'),
            'localidade_cidade_id'       => $this->input->post('localidade_cidade_id'),
            'cliente_evolucao_status_id' => $this->input->post('cliente_evolucao_status_id'),
            'titular'                    => app_retorna_numeros($this->input->post('titular')),
            'tipo_cliente'               => $this->input->post('tipo_cliente'),
            'endereco'                   => $this->input->post('endereco'),
            'numero'                     => $this->input->post('numero'),
            'ie_rg'                      => app_retorna_numeros($this->input->post('ie_rg')),
            'razao_nome'                 => $this->input->post('razao_nome'),
            'nome_fantasia'              => $this->input->post('nome_fantasia'),
            'cep'                        => $this->input->post('cep'),
            'site'                       => $this->input->post('site'),
            'data_nascimento'            => app_dateonly_mask_to_mysql($this->input->post('data_nascimento')),
            'complemento'                => $this->input->post('complemento'),
            'bairro'                     => $this->input->post('bairro'),
            'pabx'                       => app_retorna_numeros($this->input->post('pabx')),
        );

        if ($this->input->post('novoRegistro') == 1) {
            $data['evolucao_status_id'] = $this->input->post('evolucao_status_id');
        }

        return $data;
    }

    //Retorna quantidade de CPNJ's e CPF's que existem com este valor
    public function quantidade_cnpj_cpf($valor, $id)
    {
        $this->_database->select($this->_table . '.cnpj_cpf');
        $this->_database->where($this->_table . '.cnpj_cpf', $valor);
        $this->_database->where($this->_table . '.deletado', 0);

        if ($id) {
            $this->db->where($this->_table . '.' . $this->primary_key, $id);
        }

        $query = $this->db->get();
        return $query->num_rows();
    }
    //Retorna por slug
    public function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }

    //Agrega relação simples com evolução status
    public function with_cliente_evolucao_status($fields = array('descricao'))
    {
        $this->with_simple_relation('cliente_evolucao_status', 'cliente_evolucao_status_', 'cliente_evolucao_status_id', $fields);
        return $this;
    }

    //Executa filtro
    public function filterFromInput($filter = null, $data = null, $thisTable = true, $or = true)
    {
        $data = array_keys($this->get_form_data());

        foreach ($data as $field) {
            if ((isset($filter[$field])) && (!empty($filter[$field]))) {
                $this->db->like("{$this->_table}.{$field}", $filter[$field]);
            }
        }
        return $this;
    }

    //Retorna por CPF/CNPJ
    public function filterByCPFCNPJ($valor)
    {
        $this->_database->where($this->_table . '.cnpj_cpf', $valor);
        $this->_database->where($this->_table . '.deletado', 0);
        return $this;
    }

    public function get_cliente($documento, $produto_parceiro_id)
    {

        $this->load->model('cliente_contato_model', 'cliente_contato');
        $this->load->model('produto_parceiro_servico_model', 'produto_parceiro_servico');

        $cliente                    = array();
        $cliente['data_nascimento'] = '';
        $cliente['razao_nome']      = '';
        $cliente['email']           = '';
        $cliente['telefone']        = '';
        $cliente['ie_rg']           = '';
        $cliente['quantidade']      = 0;
        $cliente['cliente_id']      = 0;

        $documento = app_retorna_numeros($documento);
        if (empty($documento)) {
            return $cliente;
        }

        if ($this->produto_parceiro_servico->isUnitfour_getCliente($produto_parceiro_id)) {

            $unitfour = $this->produto_parceiro_servico->unitfour_getCliente($documento, $produto_parceiro_id, "unitfour_pf");

            if ($unitfour) {

                $cliente['data_nascimento'] = $unitfour['data_nascimento'];
                $cliente['razao_nome']      = $unitfour['nome'];
                $cliente['quantidade']      = 1;
                $cliente['cliente_id']      = $unitfour['cliente_id'];
                if (count($unitfour['contato']) > 0) {

                    foreach ($unitfour['contato'] as $contato) {
                        //print_r($contato);exit;
                        $cliente['email']    = ($contato['contato_tipo_id'] == 1 && empty($cliente['email'])) ? $contato['contato'] : $cliente['email'];
                        $cliente['telefone'] = ($contato['contato_tipo_id'] == 2 && empty($cliente['telefone'])) ? $contato['contato'] : $cliente['telefone'];
                    }
                }

                if ($unitfour['cliente_id']) {
                    $cliente = $this->get($unitfour['cliente_id']);

                    $contatos            = $this->cliente_contato->with_contato()->order_by('cliente_contato.criacao', 'desc')->get_by_cliente($cliente['cliente_id']);
                    $cliente['email']    = '';
                    $cliente['telefone'] = '';
                    if (count($contatos) > 0) {

                        foreach ($contatos as $contato) {
                            $cliente['email']    = ($contato['contato_tipo_id'] == 1 && empty($cliente['email'])) ? $contato['contato'] : $cliente['email'];
                            $cliente['telefone'] = ($contato['contato_tipo_id'] == 2 && empty($cliente['telefone'])) ? $contato['contato'] : $cliente['telefone'];
                        }

                        //$cliente['email']  = $contato[0]['email'];
                        //$cliente['telefone']  = $contato[0]['celular'];
                    }

                    $cliente['quantidade'] = 1;
                }

            } else {
                $cliente = $this->filterByCPFCNPJ(app_retorna_numeros($documento))

                    ->get_all();

                if (count($cliente) > 0) {
                    $cliente = $cliente[0];

                    $contatos            = $this->cliente_contato->with_contato()->get_by_cliente($cliente['cliente_id']);
                    $cliente['email']    = '';
                    $cliente['telefone'] = '';
                    if (count($contatos) > 0) {

                        foreach ($contatos as $contato) {
                            //print_r($contato);exit;
                            $cliente['email']    = ($contato['contato_tipo_id'] == 1 && empty($cliente['email'])) ? $contato['contato'] : $cliente['email'];
                            $cliente['telefone'] = ($contato['contato_tipo_id'] == 2 && empty($cliente['telefone'])) ? $contato['contato'] : $cliente['telefone'];
                        }

                        //$cliente['email']  = $contato[0]['email'];
                        //$cliente['telefone']  = $contato[0]['celular'];
                    }

                    $cliente['quantidade'] = 1;
                }

            }

        } elseif ($this->produto_parceiro_servico->isIfaro_getCliente($produto_parceiro_id)) {
            $ifaro = $this->produto_parceiro_servico->Ifaro_getCliente($documento, $produto_parceiro_id, "ifaro_pf");

            if ($ifaro) {
                $cliente["data_nascimento"] = $ifaro["data_nascimento"];
                $cliente["razao_nome"]      = $ifaro["nome"];
                $cliente["sexo"]            = $ifaro["sexo"];
                $cliente["quantidade"]      = 1;
                $cliente["cliente_id"]      = $ifaro["cliente_id"];

                if (count($ifaro["contato"]) > 0) {
                    $contacts = $this->getRankingContato($ifaro["contato"]);
                    $cliente["email"]    = (empty($cliente["email"])) ? $contacts["email"] : $cliente["email"];
                    $cliente["telefone"] = (empty($cliente["telefone"])) ? $contacts["celular"] : $cliente["telefone"];

                }
                if ($ifaro["cliente_id"]) {
                    $cliente             = $this->get($ifaro["cliente_id"]);
                    $contatos            = $this->cliente_contato->with_contato()->order_by("cliente_contato.criacao", "desc")->get_by_cliente($cliente["cliente_id"]);
                    $cliente["email"]    = "";
                    $cliente["telefone"] = "";
                    $cliente["quantidade"] = 1;

                    if (count($contatos) > 0) {
                        $contacts = $this->getRankingContato($contatos);
                        $cliente["email"]    = (empty($cliente["email"])) ? $contacts["email"] : $cliente["email"];
                        $cliente["telefone"] = (empty($cliente["telefone"])) ? $contacts["celular"] : $cliente["telefone"];
                    }

                }
            } else {
                $cliente = $this->filterByCPFCNPJ(app_retorna_numeros($documento))->get_all();
                if (count($cliente) > 0) {
                    $cliente             = $cliente[0];
                    $contatos            = $this->cliente_contato->with_contato()->get_by_cliente($cliente["cliente_id"]);
                    $cliente["email"]    = "";
                    $cliente["telefone"] = "";
                    $cliente["quantidade"] = 1;

                    if (count($contatos) > 0) {
                        $contacts = $this->getRankingContato($contatos);
                        $cliente["email"]    = (empty($cliente["email"])) ? $contacts["email"] : $cliente["email"];
                        $cliente["telefone"] = (empty($cliente["telefone"])) ? $contacts["celular"] : $cliente["telefone"];
                    }
                }
            }
        } else {

            $cliente = $this->filterByCPFCNPJ(app_retorna_numeros($documento))->get_all();

            if (count($cliente) > 0) {
                $cliente = $cliente[0];

                $contatos            = $this->cliente_contato->with_contato()->get_by_cliente($cliente['cliente_id']);
                $cliente['email']    = '';
                $cliente['telefone'] = '';
                $cliente['quantidade'] = 1;

                if (count($contatos) > 0) {
                    $contacts = $this->getRankingContato($contatos);
                    $cliente["email"]    = (empty($cliente["email"])) ? $contacts["email"] : $cliente["email"];
                    $cliente["telefone"] = (empty($cliente["telefone"])) ? $contacts["celular"] : $cliente["telefone"];
                }

            }

        }

        return $cliente;

    }

    public function cliente_insert_update($data)
    {

        //print_r($data);exit;
        $this->load->model('cliente_contato_model', 'cliente_contato');
        $this->load->model('cliente_codigo_model', 'cliente_codigo');
        $this->load->model('localidade_cidade_model', 'localidade_cidade');

        if (!isset($data['DADOS_CADASTRAIS'])) {
            return 0;
        }

        //verifica se o cliente existe
        $documento = isset($data['DADOS_CADASTRAIS'][0]['CPF']) ? app_retorna_numeros($data['DADOS_CADASTRAIS'][0]['CPF']) : app_retorna_numeros($data['DADOS_CADASTRAIS']['CPF']);
        $cliente   = $this->filterByCPFCNPJ($documento)
            ->get_all();

        if (count($cliente) == 0) {
            //insere novo cliente
            $data_cliente                               = array();
            $data_cliente['tipo_cliente']               = (app_verifica_cpf_cnpj(app_retorna_numeros($data['DADOS_CADASTRAIS']['CPF'])) == 'CNPJ') ? 'CO' : 'CF';
            $data_cliente['cnpj_cpf']                   = app_retorna_numeros($data['DADOS_CADASTRAIS']['CPF']);
            $data_cliente['codigo']                     = $this->cliente_codigo->get_codigo_cliente_formatado($data_cliente['tipo_cliente']);
            $data_cliente['colaborador_id']             = 1;
            $data_cliente['colaborador_comercial_id']   = 1;
            $data_cliente['titular']                    = 1;
            $data_cliente['razao_nome']                 = $data['DADOS_CADASTRAIS']['NOME'];
            $data_cliente['sexo']                       = $data['DADOS_CADASTRAIS']['SEXO'];
            $data_cliente['data_nascimento']            = app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);
            $data_cliente['cliente_evolucao_status_id'] = 6; //Salva como prospect
            $data_cliente['grupo_empresarial_id']       = 0;

            if (isset($data['ENDERECOS'])) {
                $item = $this->getRanking($data['ENDERECOS']);

                $data_cliente['cep']         = (isset($item['CEP']) && !is_array($item['CEP'])) ? app_format_cep($item['CEP']) : '';
                $data_cliente['endereco']    = (isset($item['LOGRADOURO']) && !is_array($item['LOGRADOURO'])) ? $item['LOGRADOURO'] : '';
                $data_cliente['bairro']      = (isset($item['BAIRRO']) && !is_array($item['BAIRRO'])) ? $item['BAIRRO'] : '';
                $data_cliente['numero']      = (isset($item['NUMERO']) && !is_array($item['NUMERO'])) ? app_retorna_numeros($item['NUMERO']) : '';
                $data_cliente['complemento'] = (isset($item['COMPLEMENTO']) && !is_array($item['COMPLEMENTO'])) ? $item['COMPLEMENTO'] : '';
                if ((isset($item['CIDADE']) && !is_array($item['CIDADE']))) {
                    $localidade_cidade = $this->localidade_cidade->get_by_nome($item['CIDADE']);
                    if ($localidade_cidade) {
                        $data_cliente['localidade_cidade_id'] = $localidade_cidade['localidade_cidade_id'];
                    }
                }

            }

            $cliente_id = $this->insert($data_cliente, true);

            $data_contato                                            = array();
            $data_contato['cliente_id']                              = $cliente_id;
            $data_contato['cliente_contato_nivel_relacionamento_id'] = 3;
            $data_contato['decisor']                                 = 1;
            $data_contato['nome']                                    = $data['DADOS_CADASTRAIS']['NOME'];
            $data_contato['data_nascimento']                         = app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);

            if (isset($data['TELEFONES'])) {
                foreach ($data['TELEFONES'] as $telefone) {
                    $data_contato['contato']         = app_retorna_numeros($telefone['TELEFONE']);
                    $data_contato['contato_tipo_id'] = (app_validate_mobile_phone(app_format_telefone_unitfour(app_retorna_numeros($telefone['TELEFONE'])))) ? 2 : 3;
                    $this->cliente_contato->insert_contato($data_contato);
                }
            }

            if (isset($data['EMAILS'])) {
                foreach ($data['EMAILS'] as $email) {
                    if (isset($email['EMAIL'])) {
                        $data_contato['contato']         = $email['EMAIL'];
                        $data_contato['contato_tipo_id'] = 1;
                        $this->cliente_contato->insert_contato($data_contato);
                    }
                }
            }

        } else {
            //
            $cliente_id = $cliente[0]['cliente_id'];

            $data_cliente                    = array();
            $data_cliente['cnpj_cpf']        = app_retorna_numeros($data['DADOS_CADASTRAIS']['CPF']);
            $data_cliente['razao_nome']      = $data['DADOS_CADASTRAIS']['NOME'];
            $data_cliente['data_nascimento'] = app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);

            if (isset($data['ENDERECOS'])) {
                $item = $this->getRanking($data['ENDERECOS']);

                $data_cliente['cep']         = (isset($item['CEP']) && !is_array($item['CEP'])) ? app_format_cep($item['CEP']) : '';
                $data_cliente['endereco']    = (isset($item['LOGRADOURO']) && !is_array($item['LOGRADOURO'])) ? $item['LOGRADOURO'] : '';
                $data_cliente['bairro']      = (isset($item['BAIRRO']) && !is_array($item['BAIRRO'])) ? $item['BAIRRO'] : '';
                $data_cliente['numero']      = (isset($item['NUMERO']) && !is_array($item['NUMERO'])) ? app_retorna_numeros($item['NUMERO']) : '';
                $data_cliente['complemento'] = (isset($item['COMPLEMENTO']) && !is_array($item['COMPLEMENTO'])) ? $item['COMPLEMENTO'] : '';
                if ((isset($item['CIDADE']) && !is_array($item['CIDADE']))) {
                    $localidade_cidade = $this->localidade_cidade->get_by_nome($item['CIDADE']);
                    if ($localidade_cidade) {
                        $data_cliente['localidade_cidade_id'] = $localidade_cidade['localidade_cidade_id'];
                    }
                }

            }

            $this->update($cliente_id, $data_cliente, true);

            $data_contato                                            = array();
            $data_contato['cliente_id']                              = $cliente_id;
            $data_contato['cliente_contato_nivel_relacionamento_id'] = 3;
            $data_contato['decisor']                                 = 1;
            $data_contato['nome']                                    = $data['DADOS_CADASTRAIS']['NOME'];
            $data_contato['data_nascimento']                         = app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);

            if (isset($data['TELEFONES'])) {
                foreach ($data['TELEFONES'] as $telefone) {
                    $telefone['TELEFONE'] = isset($telefone['TELEFONE']) ? $telefone['TELEFONE'] : $telefone;
                    if (!empty($telefone['TELEFONE'])) {
                        $data_contato['contato']         = app_retorna_numeros($telefone['TELEFONE']);
                        $data_contato['contato_tipo_id'] = (app_validate_mobile_phone(app_format_telefone_unitfour(app_retorna_numeros($telefone['TELEFONE'])))) ? 2 : 3;
                        $this->cliente_contato->insert_not_exist_contato($data_contato);
                    }
                }
            }

            if (isset($data['EMAILS'])) {
                foreach ($data['EMAILS'] as $email) {
                    if (isset($email['EMAIL'])) {
                        $data_contato['contato']         = $email['EMAIL'];
                        $data_contato['contato_tipo_id'] = 1;
                        $this->cliente_contato->insert_not_exist_contato($data_contato);
                    }
                }
            }

        }

        return $cliente_id;
    }

    public function getRanking($dados = [])
    {
        $rank=$rankAux=1000;
        $index=0;

        $result = [];

        foreach ($dados as $data) {

            if ( empty($data["RANKING"]) ) {
                if ($rank == 1000) {
                    $rank = $index;
                }
            } elseif ($data["RANKING"] <= $rankAux) {
                $rank = $index;
                $rankAux = $data["RANKING"];
            }

            $index++;
        }

        if ($index > 0)
        {
            if (!empty($dados[$rank]))
                $result = $dados[$rank];
        }

        return $result;
    }

    private function getRankingContato($contatos = [])
    {
        $rankTel=$rankCel=$rankEmail=1000;
        $rankTelAux=$rankCelAux=$rankEmailAux=1000;
        $index=0;

        $result = [
            'email' => '',
            'celular' => '',
            'telefone' => '',
        ];

        foreach ($contatos as $contato) {
            switch ($contato["contato_tipo_id"]) {
                // Telefone Residencial
                case 3:
                    if ( empty($contato["ranking"]) ) {
                        if ($rankTel == 1000) {
                            $rankTel = $index;
                        }
                    } elseif ($contato["ranking"] <= $rankTelAux) {
                        $rankTel = $index;
                        $rankTelAux = $contato["ranking"];
                    }
                    break;

                // Celular
                case 2:
                    if ( empty($contato["ranking"]) ) {
                        if ($rankCel == 1000) {
                            $rankCel = $index;
                        }
                    } elseif ($contato["ranking"] <= $rankCelAux) {
                        $rankCel = $index;
                        $rankCelAux = $contato["ranking"];
                    }
                    break;

                // Email
                case 1:
                    if ( empty($contato["ranking"]) ) {
                        if ($rankEmail == 1000) {
                            $rankEmail = $index;
                        }
                    } elseif ($contato["ranking"] <= $rankEmailAux) {
                        $rankEmail = $index;
                        $rankEmailAux = $contato["ranking"];
                    }
                    break;
            }
            $index++;
        }

        if ($index > 0)
        {
            if (!empty($contatos[$rankEmail]["contato"]))
                $result['email'] = $contatos[$rankEmail]["contato"];

            if (!empty($contatos[$rankCel]["contato"]))
                $result['celular'] = $contatos[$rankCel]["contato"];

            if (!empty($contatos[$rankTel]["contato"]))
                $result['telefone'] = $contatos[$rankTel]["contato"];
        }

        return $result;
    }

    /**
     * @param array $data dados da cotação
     * @return mixed retorna os dados do Cliente
     *
     */

    public function cotacao_insert_update($data = array())
    {

        $this->load->model('cliente_contato_model', 'cliente_contato');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');

        //verifica se o cliente existe
        $cliente = $this->filterByCPFCNPJ(app_retorna_numeros($data['cnpj_cpf']))
            ->get_all();

        if (isset($data['produto_parceiro_id']) && empty($data['parceiro_id'])) {
            $produto = $this->produto_parceiro->get($data['produto_parceiro_id']);
            if (isset($produto['parceiro_id'])) {
                $data['parceiro_id'] = $produto['parceiro_id'];
            }
        }

        if (count($cliente) == 0) {

            //insere novo cliente
            $data_cliente                             = array();
            $data_cliente['tipo_cliente']             = (app_verifica_cpf_cnpj(app_retorna_numeros($data['cnpj_cpf'])) == 'CNPJ') ? 'CO' : 'CF';
            $data_cliente['cnpj_cpf']                 = app_retorna_numeros($data['cnpj_cpf']);
            $data_cliente['ie_rg']                    = issetor($data['rg'], '');
            $data_cliente['codigo']                   = $this->cliente_codigo->get_codigo_cliente_formatado($data_cliente['tipo_cliente']);
            $data_cliente['colaborador_id']           = 1;
            $data_cliente['colaborador_comercial_id'] = 1;
            $data_cliente['titular']                  = 1;
            $data_cliente['razao_nome']               = $data['nome'];
            if (isset($data['data_nascimento'])) {
                $data_cliente['data_nascimento'] = app_dateonly_mask_to_mysql($data['data_nascimento']);
            }
            if (!empty($data['rg_data_expedicao'])) {
                $data_cliente['rg_data_expedicao'] = app_dateonly_mask_to_mysql($data['rg_data_expedicao']);
            } else {
                $data_cliente['rg_data_expedicao'] = null;
            }
            if (isset($data['sexo'])) {
                $data_cliente['sexo'] = $data['sexo'];
            }
            if (isset($data['estado_civil'])) {
                $data_cliente['estado_civil'] = $data['estado_civil'];
            }
            if (isset($data['rg_orgao_expedidor'])) {
                $data_cliente['rg_orgao_expedidor'] = $data['rg_orgao_expedidor'];
            }
            if (isset($data['rg_uf'])) {
                $data_cliente['rg_uf'] = $data['rg_uf'];
            }

            if (isset($data['password'])) {
                $data_cliente['password'] = MD5($this->salt . $data['password']);
            }

            $data_cliente['cliente_evolucao_status_id'] = 6; //salva como prospect
            $data_cliente['grupo_empresarial_id']       = 0;
            $data_cliente['parceiro_id']                = $data['parceiro_id'];

            $cliente_id = $this->insert($data_cliente, true);
            $cliente    = $this->get($cliente_id);

            //Inseri Contato de cliente E-mail
            $data_contato                                            = array();
            $data_contato['cliente_id']                              = $cliente_id;
            $data_contato['cliente_contato_nivel_relacionamento_id'] = 3;
            $data_contato['decisor']                                 = 1;
            $data_contato['nome']                                    = $data['nome'];
            $data_contato['contato']                                 = $data['email'];
            $data_contato['contato_tipo_id']                         = 1;

            $this->cliente_contato->insert_contato($data_contato);

            //Celular
            $data_contato['contato']         = app_retorna_numeros($data['telefone']);
            $data_contato['contato_tipo_id'] = 2;
            $this->cliente_contato->insert_contato($data_contato);

        } else {
            //
            $cliente = $cliente[0];
            $this->atualizar($cliente['cliente_id'], $data);
        }

        return $cliente;
    }

    public function atualizar($cliente_id, $data = array())
    {

        if (!$cliente_id) {
            return false;
        }

        $data_cliente = array();
        if (isset($data['rg'])) {
            $data_cliente['ie_rg'] = $data['rg'];
        }
        if (isset($data['nome'])) {
            $data_cliente['razao_nome'] = $data['nome'];
        }
        if (isset($data['data_nascimento'])) {
            $data_cliente['data_nascimento'] = app_dateonly_mask_to_mysql($data['data_nascimento']);
        }
        if (isset($data['data_nascimento'])) {
            $data_cliente['data_nascimento'] = app_dateonly_mask_to_mysql($data['data_nascimento']);
        }
        if (isset($data['rg_data_expedicao'])) {
            $data_cliente['rg_data_expedicao'] = app_dateonly_mask_to_mysql($data['rg_data_expedicao']);
        }
        if (isset($data['sexo'])) {
            $data_cliente['sexo'] = $data['sexo'];
        }
        if (isset($data['estado_civil'])) {
            $data_cliente['estado_civil'] = $data['estado_civil'];
        }
        if (isset($data['rg_orgao_expedidor'])) {
            $data_cliente['rg_orgao_expedidor'] = $data['rg_orgao_expedidor'];
        }
        if (isset($data['rg_uf'])) {
            $data_cliente['rg_uf'] = $data['rg_uf'];
        }

        if (isset($data['rg_uf'])) {
            $data_cliente['rg_uf'] = $data['rg_uf'];
        }
        if (isset($data['parceiro_id'])) {
            $data_cliente['parceiro_id'] = $data['parceiro_id'];
        }

        if (isset($data['password'])) {
            $data_cliente['password'] = MD5($this->salt . $data['password']);
        }
        if ($data_cliente) {
            $this->update($cliente_id, $data_cliente, true);
        }

        if(!isset($data['nome'])){return;}
        if(!isset($data['email'])){return;}
        if(!isset($data['telefone'])){return;}

        if (($data['nome']) || ($data['email']) || ($data['telefone'])) {
            //$this->cliente_contato->delete_by(array('cliente_id' => $cliente_id));

            $data_contato                                            = array();
            $data_contato['cliente_id']                              = $cliente_id;
            $data_contato['cliente_contato_nivel_relacionamento_id'] = 3;
            $data_contato['decisor']                                 = 1;
            $data_contato['nome']                                    = issetor($data['nome'], '');
            $data_contato['contato']                                 = issetor($data['email'], '');
            $data_contato['contato_tipo_id']                         = 1;

            if (isset($data['email'])) {
                $this->cliente_contato->delete_by(array('cliente_id' => $cliente_id));
                $this->cliente_contato->insert_not_exist_contato($data_contato);
            }

            if ($data['telefone']) {
                $data_contato['contato']         = app_retorna_numeros($data['telefone']);
                $data_contato['contato_tipo_id'] = 2;
                $this->cliente_contato->insert_not_exist_contato($data_contato);
            }
        }

    }

    /**
     * Efetua login no sistema
     * @param $login
     * @param $password
     * @return bool
     */
    public function login($login, $password)
    {
        $this->load->model('usuario_acl_recurso_model', 'recursos');

        $this->_database->select($this->_table . '.*');
        $this->_database->from($this->_table);
        $this->_database->where($this->_table . '.cnpj_cpf', $login);
        $this->_database->where($this->_table . '.password', MD5($this->salt . $password));
        $this->_database->where($this->_table . '.deletado', 0);
        $this->_database->limit(1);

        $query = $this->_database->get();
        if ($query->num_rows() == 1) {
            $result                       = $query->result_array();
            $usuario                      = $result[0];
            $usuario['cliente_id']        = $usuario['cliente_id'];
            $usuario['nome']              = $usuario['razao_nome'];
            $usuario['cpf']               = $usuario['cnpj_cpf'];
            $usuario['parceiro_id']       = $usuario['parceiro_id'];
            $usuario['cliente_is_logged'] = true;
            $usuario['upload_url']        = base_url('assets/uploads/media') . '/';
            $usuario['recursos']          = array();

            /*
             * deleta os dados da sessão antiga
             */
            $this->session->sess_destroy();
            $this->session->set_userdata($usuario);

            return $usuario['cliente_id'];
        } else {
            return false;
        }
    }

    public function logout()
    {

        $this->session->sess_destroy();

    }

    public function get_all($limit = 0, $offset = 0, $processa = true)
    {
        if ($processa) {
            $parceiro_id = $this->session->userdata('parceiro_id');

            $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
        }
        return parent::get_all($limit, $offset);
    }

    public function get_total($processa = true)
    {
        if ($processa) {
            //Efetua join com cotação
            //$this->_database->join("parceiro as cotacao_filtro","cotacao_filtro.cotacao_id = {$this->_table}.cotacao_id");

            $this->processa_parceiros_permitidos("{$this->_table}.parceiro_id");
        }
        return parent::get_total(); // TODO: Change the autogenerated stub
    }
}
