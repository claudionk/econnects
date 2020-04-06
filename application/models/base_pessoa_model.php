<?php
class Base_Pessoa_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'base_pessoa';
    protected $primary_key = 'base_pessoa_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');

    //Dados
    public $validate = array(
    );

    public function get_by_id($id)
    {
        return $this->get($id);
    }

    public function getByDoc($documento, $produto_parceiro_id, $info_service)
    {

        $this->load->model('base_pessoa_contato_model', 'base_pessoa_contato');
        $this->load->model('base_pessoa_empresa_model', 'base_pessoa_empresa');
        $this->load->model('base_pessoa_endereco_model', 'base_pessoa_endereco');
        $this->load->model('base_pessoa_endereco_model', 'base_pessoa_endereco');

        $documento = app_retorna_numeros($documento);
        $meses = BASE_TEMPO_NOVA_PESQUISA;

        $sql = "
            SELECT
            base_pessoa.*,
            IF(CURRENT_TIMESTAMP < DATE_ADD(base_pessoa.ultima_atualizacao, INTERVAL {$meses} MONTH), 1, 0) as atualizado
            FROM
            base_pessoa
            WHERE
            base_pessoa.deletado = 0
            and base_pessoa.documento = '{$documento}'
        ";

        $result = $this->_database->query($sql)->result_array();
        if ($result) {
            $result = $result[0];

            if ($result['atualizado'] == 0) {
                //faz a atualização do cliente.
                $documento = str_pad($documento, 11, '0', STR_PAD_LEFT);

                $this->updateCliente($documento, $result['base_pessoa_id'], $produto_parceiro_id, $info_service);
                $result = $this->_database->query($sql)->result_array();
                if ($result)
                    $result = $result[0];
            }

        } else {
            $this->updateCliente($documento, 0, $produto_parceiro_id, $info_service);
            $result = $this->_database->query($sql)->result_array();
            if ($result)
                $result = $result[0];
        }

        if ($result) {
            $result['contato']  = $this->base_pessoa_contato->with_contato_tipo()->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
            $result['empresa']  = $this->base_pessoa_empresa->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
            $result['endereco'] = $this->base_pessoa_endereco->filter_by_base_pessoa($result['base_pessoa_id'])->order_by('ranking')->get_all();
        } else {
            $result = array();
        }

        return $result;

    }

    public function updateCliente($documento, $base_pessoa_id = 0, $produto_parceiro_id, $info_service)
    {

        $this->load->model('base_pessoa_contato_model', 'base_pessoa_contato');
        $this->load->model('base_pessoa_empresa_model', 'base_pessoa_empresa');
        $this->load->model('base_pessoa_endereco_model', 'base_pessoa_endereco');

        if ($info_service == "unitfour_pf") {
            $this->load->library("Unitfour", array('produto_parceiro_id' => $produto_parceiro_id));
            $result = $this->unitfour->getBasePessoaPF(app_retorna_numeros($documento));
        }

        if ($info_service == "ifaro_pf") {
            $this->load->library("Ifaro", array('produto_parceiro_id' => $produto_parceiro_id));
            $ifaro = $this->ifaro->getBasePessoaPF(app_retorna_numeros($documento));
            if (!empty($ifaro)) {
                $DataNascimento             = date_create_from_format("d/m/Y", $ifaro["DataNascimento"]);
                $result["DADOS_CADASTRAIS"] = array("CPF" => $ifaro["CPF"],
                    "NOME"                                    => $ifaro["Nome"],
                    "NOME_ULTIMO"                             => trim(strrchr($ifaro["Nome"], " ")),
                    "SEXO"                                    => $ifaro["Sexo"],
                    "NOME_MAE"                                => $ifaro["Mae"],
                    "DATANASC"                                => date_format($DataNascimento, "Y-m-d"),
                    "IDADE"                                   => $ifaro["Idade"],
                    "SIGNO"                                   => $ifaro["Signo"],
                    "RG"                                      => $ifaro["RG"],
                    "SITUACAO_RECEITA"                        => "REGULAR");

                $iTelefones = $ifaro["Telefones"];
                $Telefones  = [];
                foreach ($iTelefones as $row) {
                    $Telefones[] = array("TELEFONE" => "(" . trim($row["DD"]) . ") " . $row["Numero"],
                        "RANKING"                       => ($row["Tipo"] == "TELEFONE MÓVEL" ? 90 : $row["Ranking"]));
                }
                $result["TELEFONES"] = $Telefones;

                $iEmails = $ifaro["Emails"];
                $Emails  = [];
                foreach ($iEmails as $row) {
                    $Emails[] = array("EMAIL" => trim($row["EmailEndereco"]), "RANKING" => $row["Ranking"]);
                }
                $result["EMAILS"] = $Emails;

                $iEnderecos = $ifaro["Enderecos"];
                $Enderecos  = [];
                foreach ($iEnderecos as $row) {
                    $Enderecos[] = array("LOGRADOURO" => $this->ifaro->getLogradouro($row["Logadouro"], $row["LogadouroTipo"]),
                        "NUMERO"                          => trim($row["Numero"]),
                        "COMPLEMENTO"                     => trim($row["Complemento"]),
                        "BAIRRO"                          => trim($row["Bairro"]),
                        "CIDADE"                          => trim($row["Cidade"]),
                        "UF"                              => trim($row["UF"]),
                        "CEP"                             => trim($row["CEP"]),
                        "RANKING"                         => $row["Ranking"]);
                }
                $result["ENDERECOS"] = $Enderecos;
            }
        }

        if (isset($result) && isset($result["DADOS_CADASTRAIS"])) {
            if ($base_pessoa_id > 0) {
                $this->base_pessoa_contato->delete_by(array("base_pessoa_id" => $base_pessoa_id));
                $this->base_pessoa_empresa->delete_by(array("base_pessoa_id" => $base_pessoa_id));
                $this->base_pessoa_endereco->delete_by(array("base_pessoa_id" => $base_pessoa_id));
                $this->update_base_pessoa($base_pessoa_id, $result, $produto_parceiro_id);
            } else {
                $this->update_base_pessoa($base_pessoa_id, $result, $produto_parceiro_id);
            }
        } else {
            return array();
        }
    }

    public function update_base_pessoa($base_pessoa_id, $data, $produto_parceiro_id)
    {
        $this->load->model('cliente_model', 'cliente');
        $this->load->model('base_pessoa_contato_model', 'base_pessoa_contato');
        $this->load->model('base_pessoa_empresa_model', 'base_pessoa_empresa');
        $this->load->model('base_pessoa_endereco_model', 'base_pessoa_endereco');

        if ($base_pessoa_id > 0) {
            $pessoa = $this->get($base_pessoa_id);
        } else {
            $pessoa = array('quantidade_atualziacao' => 0);
        }

        $cliente_id                            = $this->cliente->cliente_insert_update($data, $produto_parceiro_id);
        $data_pessoa                           = array();
        $data_pessoa['cliente_id']             = $cliente_id;
        $data_pessoa['documento']              = isset($data['DADOS_CADASTRAIS'][0]['CPF']) ? $data['DADOS_CADASTRAIS'][0]['CPF'] : $data['DADOS_CADASTRAIS']['CPF'];
        $data_pessoa['nome']                   = isset($data['DADOS_CADASTRAIS'][0]['NOME']) ? $data['DADOS_CADASTRAIS'][0]['NOME'] : $data['DADOS_CADASTRAIS']['NOME'];
        $data_pessoa['sobrenome']              = isset($data['DADOS_CADASTRAIS'][0]['NOME_ULTIMO']) ? $data['DADOS_CADASTRAIS'][0]['NOME_ULTIMO'] : $data['DADOS_CADASTRAIS']['NOME_ULTIMO'];
        $data_pessoa['sexo']                   = isset($data['DADOS_CADASTRAIS'][0]['SEXO']) ? $data['DADOS_CADASTRAIS'][0]['SEXO'] : $data['DADOS_CADASTRAIS']['SEXO'];
        $data_pessoa['nome_mae']               = isset($data['DADOS_CADASTRAIS'][0]['NOME_MAE']) ? $data['DADOS_CADASTRAIS'][0]['NOME_MAE'] : $data['DADOS_CADASTRAIS']['NOME_MAE'];
        $data_pessoa['data_nascimento']        = isset($data['DADOS_CADASTRAIS'][0]['DATANASC']) ? app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS'][0]['DATANASC']) : app_dateonly_mask_to_mysql($data['DADOS_CADASTRAIS']['DATANASC']);
        $data_pessoa['signo']                  = isset($data['DADOS_CADASTRAIS'][0]['SIGNO']) ? $data['DADOS_CADASTRAIS'][0]['SIGNO'] : $data['DADOS_CADASTRAIS']['SIGNO'];
        $data_pessoa['situacao_receita']       = isset($data['DADOS_CADASTRAIS'][0]['SITUACAO_RECEITA']) ? $data['DADOS_CADASTRAIS'][0]['SITUACAO_RECEITA'] : $data['DADOS_CADASTRAIS']['SITUACAO_RECEITA'];
        $data_pessoa['ultima_atualizacao']     = date('Y-m-d H:i:s');
        $data_pessoa['quantidade_atualziacao'] = ((int) $pessoa['quantidade_atualziacao']) + 1;

        if ($base_pessoa_id > 0) {
            $this->update($base_pessoa_id, $data_pessoa, true);

        } else {
            $base_pessoa_id = $this->insert($data_pessoa, true);
        }

        //telefones;
        if (isset($data['TELEFONES'])) {
            foreach ($data['TELEFONES'] as $telefone) {
                $telefone['TELEFONE'] = isset($telefone['TELEFONE']) ? $telefone['TELEFONE'] : $telefone;
                if (!empty($telefone['TELEFONE'])) {
                    $data_contato                    = array();
                    $data_contato['base_pessoa_id']  = $base_pessoa_id;
                    $data_contato['nome']            = $data['DADOS_CADASTRAIS']['NOME'];
                    $data_contato['ranking']         = isset($telefone['RANKING']) ? $telefone['RANKING'] : 1;
                    $data_contato['contato']         = app_retorna_numeros($telefone['TELEFONE']);
                    $data_contato['contato_tipo_id'] = (app_validate_mobile_phone(app_format_telefone_unitfour(app_retorna_numeros($telefone['TELEFONE'])))) ? 2 : 3;
                    $this->base_pessoa_contato->insert($data_contato, true);
                }

            }
        }

        if (isset($data['EMAILS'])) {
            foreach ($data['EMAILS'] as $email) {
                if (isset($email['EMAIL'])) {
                    $data_contato                    = array();
                    $data_contato['base_pessoa_id']  = $base_pessoa_id;
                    $data_contato['nome']            = $data['DADOS_CADASTRAIS']['NOME'];
                    $data_contato['ranking']         = isset($email['RANKING']) ? $email['RANKING'] : 1;
                    $data_contato['contato']         = mb_strtolower($email['EMAIL'], 'UTF-8');
                    $data_contato['contato_tipo_id'] = 1;
                    $this->base_pessoa_contato->insert($data_contato, true);
                }
            }
        }

        if (isset($data['ENDERECOS'])) {
            foreach ($data['ENDERECOS'] as $item) {
                $data_endereco                         = array();
                $data_endereco['base_pessoa_id']       = $base_pessoa_id;
                $data_endereco['ranking']              = (isset($item['RANKING']) && !is_array($item['RANKING'])) ? $item['RANKING'] : 0;
                $data_endereco['endereco_cep']         = (isset($item['CEP']) && !is_array($item['CEP'])) ? app_format_cep($item['CEP']) : '';
                $data_endereco['endereco']             = (isset($item['LOGRADOURO']) && !is_array($item['LOGRADOURO'])) ? $item['LOGRADOURO'] : '';
                $data_endereco['endereco_bairro']      = (isset($item['BAIRRO']) && !is_array($item['BAIRRO'])) ? $item['BAIRRO'] : '';
                $data_endereco['endereco_numero']      = (isset($item['NUMERO']) && !is_array($item['NUMERO'])) ? app_retorna_numeros($item['NUMERO']) : '';
                $data_endereco['endereco_complemento'] = (isset($item['COMPLEMENTO']) && !is_array($item['COMPLEMENTO'])) ? $item['COMPLEMENTO'] : '';
                $data_endereco['endereco_cidade']      = (isset($item['CIDADE']) && !is_array($item['CIDADE'])) ? $item['CIDADE'] : '';
                $data_endereco['endereco_uf']          = (isset($item['UF']) && !is_array($item['UF'])) ? $item['UF'] : '';
                $this->base_pessoa_endereco->insert($data_endereco, true);
            }
        }

        if (isset($data['PARTICIPACAO_EMPRESA'])) {
            foreach ($data['PARTICIPACAO_EMPRESA'] as $item) {
                $data_empresa                   = array();
                $data_empresa['base_pessoa_id'] = $base_pessoa_id;
                $data_empresa['ranking']        = (isset($item['RANKING']) && !is_array($item['RANKING'])) ? $item['RANKING'] : 0;
                $data_empresa['nome']           = (isset($item['NOME']) && !is_array($item['NOME'])) ? $item['NOME'] : '';
                $data_empresa['documento']      = (isset($item['DOCUMENTO']) && !is_array($item['DOCUMENTO'])) ? $item['DOCUMENTO'] : '';
                $data_empresa['participacao']   = (isset($item['PCT_PARTICIPACAO']) && !is_array($item['PCT_PARTICIPACAO'])) ? $item['PCT_PARTICIPACAO'] : '';
                $data_empresa['data_entrada']   = (isset($item['DATA_ENTRADA']) && !is_array($item['DATA_ENTRADA'])) ? app_dateonly_mask_to_mysql($item['DATA_ENTRADA']) : '';
                $this->base_pessoa_empresa->insert($data_empresa, true);
            }
        }

    }

}
