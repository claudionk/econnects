<?php
class Produto_Parceiro_Plano_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'produto_parceiro_plano';
    protected $primary_key = 'produto_parceiro_plano_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'descricao');

    //Dados
    public $validate = array(
        array(
            'field'  => 'nome',
            'label'  => 'Nome',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'descricao',
            'label'  => 'Descrição',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'slug_plano',
            'label'  => 'Slug',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'codigo_operadora',
            'label'  => 'Código Operadora',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'   => 'produto_parceiro_id',
            'label'   => 'Produto / Parceiro',
            'rules'   => 'required',
            'groups'  => 'default',
            'foreign' => 'produto_parceiro',
        ),
        array(
            'field'   => 'moeda_id',
            'label'   => 'Moeda',
            'rules'   => 'required',
            'groups'  => 'default',
            'foreign' => 'moeda',
        ),
        array(
            'field'  => 'precificacao_tipo_id',
            'label'  => 'Tipo de Precificação',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'unidade_tempo',
            'label'  => 'Unidade de Tempo',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'limite_vigencia',
            'label'  => 'Limite de Vigência',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'possui_limite_tempo',
            'label'  => 'Possui limite de Tempo de Uso',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'unidade_limite_tempo',
            'label'  => 'Unidade de Tempo de Uso',
            // 'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'limite_tempo',
            'label'  => 'Limite de Tempo de Uso',
            // 'rules' => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'passivel_upgrade',
            'label'  => 'Passível de upgrade',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'ordem',
            'label'  => 'Ordem',
            'rules'  => '',
            'groups' => 'default',
        ),
        array(
            'field'  => 'qtd_min_vida',
            'label'  => 'Mínimo de Vidas',
            'rules'  => '',
            'groups' => 'default',
        ),
        array(
            'field'  => 'idade_minima',
            'label'  => 'Idade Mínima',
            'rules'  => '',
            'groups' => 'default',
        ),
        array(
            'field'  => 'idade_maxima',
            'label'  => 'Idade Máxima',
            'rules'  => '',
            'groups' => 'default',
        ),
    );

    /**
     * Busca planos com esta destino
     * @param $localidade_id
     * @return $this
     */
    public function with_destino($localidade_id)
    {
        $this->_database->join("produto_parceiro_plano_destino", "produto_parceiro_plano_destino.produto_parceiro_plano_id = {$this->_table}.{$this->primary_key}");
        $this->_database->where("produto_parceiro_plano_destino.localidade_id = {$localidade_id}");
        $this->_database->where("produto_parceiro_plano_destino.deletado = 0");
        return $this;
    }

    public function with_faixa_salarial($faixa_salarial_id)
    {
        $this->_database->join("produto_parceiro_plano_faixa_salarial", "produto_parceiro_plano_faixa_salarial.produto_parceiro_plano_id = {$this->_table}.{$this->primary_key}");
        $this->_database->where("produto_parceiro_plano_faixa_salarial.faixa_salarial_id = {$faixa_salarial_id}");
        $this->_database->where("produto_parceiro_plano_faixa_salarial.deletado = 0");
        return $this;
    }

    /**
     * Busca planos com esta destino
     * @param $localidade_id
     * @return $this
     */
    public function with_origem($localidade_id)
    {
        $this->_database->join("produto_parceiro_plano_origem", "produto_parceiro_plano_origem.produto_parceiro_plano_id = {$this->_table}.{$this->primary_key}");
        $this->_database->where("produto_parceiro_plano_origem.localidade_id = {$localidade_id}");
        $this->_database->where("produto_parceiro_plano_origem.deletado = 0");
        return $this;
    }

    public function with_precificacao_tipo()
    {
        $this->_database->select("precificacao_tipo.nome as precificacao_tipo");
        $this->_database->join("precificacao_tipo", "precificacao_tipo.precificacao_tipo_id = {$this->_table}.precificacao_tipo_id");
        return $this;
    }

    public function with_moeda()
    {
        $this->_database->select("moeda.nome as moeda");
        $this->_database->join("moeda", "moeda.moeda_id = {$this->_table}.moeda_id");
        return $this;
    }

    public function get_by_id($id)
    {
        return $this->get($id);
    }

    public function with_produto_parceiro()
    {
        $this->with_simple_relation('produto_parceiro', 'produto_parceiro_', 'produto_parceiro_id', array('nome'));
        return $this;
    }

    public function with_produto_parceiro_configuracao($produto_parceiro_plano_id)
    {
        $this->with_simple_relation('produto_parceiro_configuracao', '', 'produto_parceiro_id', array('apolice_vigencia', 'apolice_vigencia_regra', 'pagamento_tipo', 'pagamento_periodicidade_unidade'));
        $this->_database->where("{$this->_table}.{$this->primary_key}", $produto_parceiro_plano_id);

        return $this;
    }

    public function wtih_plano_habilitado($parceiro_id)
    {
        // $this->load->model('produto_parceiro_model', 'produto_parceiro');
        // $result = $this->produto_parceiro->getPlanosHabilitados($parceiro_id);

        $subQuery = "
            SELECT h.produto_parceiro_plano_id FROM (
                SELECT parceiro_id, produto_parceiro_plano_id FROM parceiro_plano where deletado = 0
                UNION
                SELECT parceiro_produto.parceiro_id, produto_parceiro_plano.produto_parceiro_plano_id
                FROM parceiro_produto
                INNER JOIN produto_parceiro_plano ON produto_parceiro_plano.produto_parceiro_id = parceiro_produto.produto_parceiro_id
                WHERE parceiro_produto.deletado = 0 AND produto_parceiro_plano.deletado = 0
            ) AS h
            INNER JOIN produto_parceiro_plano ON h.produto_parceiro_plano_id = produto_parceiro_plano.produto_parceiro_plano_id
            WHERE h.parceiro_id = $parceiro_id
            AND produto_parceiro_plano.deletado = 0";

        $this->_database->join("($subQuery) t", "{$this->_table}.produto_parceiro_plano_id = t.produto_parceiro_plano_id");
        return $this;
    }

    public function with_produto()
    {
        $this->_database->join("produto", "produto_parceiro.produto_id = produto.produto_id");
        $this->_database->where("produto.deletado = 0");
        return $this;
    }

    public function filter_by_produto_parceiro($produto_parceiro_id)
    {
        $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        return $this;
    }

    public function filter_by_slug($slug)
    {
        $this->_database->where('slug_plano', $slug);
        return $this;
    }

    public function get_qtd_min_vida($produto_parceiro_id, $produto_parceiro_plano_id)
    {
        if (!is_null($produto_parceiro_id)) {
            $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        }
        if (!is_null($produto_parceiro_plano_id)) {
            $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        }
        $this->_database->where("{$this->_table}.qtd_min_vida >", 0);
        $result = $this->get_all();

        if ( !empty($result) )
        {
            return $result[0]['qtd_min_vida'];
        }
        return 1;
    }

    public function coreSelectPlanosProdutoParceiro($produto_parceiro_id, $produto_parceiro_plano_id = null)
    {
        $this->_database->select("{$this->_table}.produto_parceiro_plano_id");
        $this->_database->select("{$this->_table}.produto_parceiro_id");
        $this->_database->select("{$this->_table}.nome");
        $this->_database->select("{$this->_table}.slug_plano");
        $this->_database->select("{$this->_table}.descricao");
        $this->_database->select("{$this->_table}.codigo_operadora");
        $this->_database->select("{$this->_table}.limite_vigencia");
        $this->_database->select("{$this->_table}.possui_limite_tempo");
        $this->_database->select("{$this->_table}.limite_tempo");
        $this->_database->select("{$this->_table}.unidade_limite_tempo");
        $this->_database->select("{$this->_table}.unidade_tempo as limite_vigencia_unidade ");
        if (!is_null($produto_parceiro_id)) {
            $this->_database->where("{$this->_table}.produto_parceiro_id", $produto_parceiro_id);
        }
        if (!is_null($produto_parceiro_plano_id)) {
            $this->_database->where("{$this->_table}.produto_parceiro_plano_id", $produto_parceiro_plano_id);
        }
        return $this;
    }

    public function coreSelectPlanosProduto()
    {
        $this->_database->select("{$this->_table}.*");
        $this->_database->select("produto.produto_id");
        $this->_database->select("produto.produto_ramo_id");
        $this->_database->select("produto.nome");
        $this->_database->select("produto.slug");
        $this->_database->join('produto_parceiro', "{$this->_table}.produto_parceiro_id = produto_parceiro.produto_parceiro_id");
        $this->_database->join('produto', "produto.produto_id = produto_parceiro.produto_id");

        return $this;
    }

    /**
     * Faz O Calculo da Vigência
     * @param $produto_parceiro_plano
     * @param $data_base
     */

    public function getInicioFimVigencia($produto_parceiro_plano_id, $data_base_rec = null, $cotacao_salva = null, $coberturas = [])
    {
        $produto_parceiro_plano = $this->get($produto_parceiro_plano_id);

        $config = $this->with_produto_parceiro_configuracao($produto_parceiro_plano_id)->get_all();
        if ($config) {
            $config = $config[0];
        }

        $apolice_vigencia_regra = false;
        $data_adesao = null;
        $date_fim_vig = null;
        $result = [];

        // Validação para que o retorno seja sempre por cobertura
        if ( empty($coberturas) )
        {
            $coberturas[] = [ 'data_base_rec' => $data_base_rec];
        }

        /*
        Definição da Data Base
        INI VIG - NF - RESULT
        TRUE    -  TRUE     - INI
        TRUE    -  FALSE    - INI
        FALSE   -  TRUE     - NF
        FALSE   -  FALSE    - HJ

        Definição da Data de Adesão *** enviou data de adesão - manter sempre ela ***
        INI VIG - NF - RESULT
        TRUE    -  TRUE     - NF
        TRUE    -  FALSE    - INI
        FALSE   -  TRUE     - NF
        FALSE   -  FALSE    - HJ 
        */

        foreach ($coberturas as $cob)
        {
            $data_inicio_vigencia  = emptyor($cob['data_inicio_vigencia'],  emptyor($cotacao_salva['data_inicio_vigencia'], null) );
            $data_fim_vigencia     = emptyor($cob['data_fim_vigencia'],     emptyor($cotacao_salva['data_fim_vigencia'],    null) );
            $nota_fiscal_data      = emptyor($cob['nota_fiscal_data'],      emptyor($cotacao_salva['nota_fiscal_data'],     null) );
            $data_ad               = emptyor($cob['data_adesao'],           emptyor($cotacao_salva['data_adesao'],          null) );
            $data_base             = $data_base_rec;

            // se nao foi informada a data base. Se foi, tem que ser o q informou
            if (empty($data_base))
            {
                // trecho para manter o que foi enviado
                if ( !empty($data_inicio_vigencia) && $data_inicio_vigencia != "0000-00-00")
                {
                    $data_base = $data_adesao = $data_inicio_vigencia;
                    $apolice_vigencia_regra = false;
                } else {
                    $apolice_vigencia_regra = true;
                }

                if ( !empty($nota_fiscal_data) && $nota_fiscal_data != "0000-00-00")
                {
                    $data_adesao = $nota_fiscal_data;

                    // se nao definiu com o inicio da vignecia, que é prioridade
                    if (empty($data_base))
                    {
                        $data_base = $nota_fiscal_data;
                    }
                }

                if ( !empty($data_fim_vigencia) && $data_fim_vigencia != "0000-00-00")
                {
                    $date_fim_vig = $data_fim_vigencia;
                }

                if ($config)
                {
                    switch ($config["apolice_vigencia"])
                    {
                        case "S": //Data de Criação
                            $data_base = $data_adesao = date("Y-m-d");
                            $apolice_vigencia_regra = true;
                            break;

                        case "N": //Data da Nota Fiscal
                            $apolice_vigencia_regra = true;

                            if ( !empty($nota_fiscal_data) && $nota_fiscal_data != "0000-00-00" ) {
                                $data_base = $data_adesao = $nota_fiscal_data;
                            }
                            break;

                        case "E": //Especifica (Somente via API)
                            // já tratando antes de validar a configuração
                            break;
                    }

                    // se depois de todas as validações, ainda não conseguiu definir a data base
                    // seta neste ponto para nao dar problema na regra de vigência
                    if ( empty($data_base) )
                    {
                        $data_base = date("Y-m-d");
                    }

                    if ($apolice_vigencia_regra)
                    {
                        switch ($config["apolice_vigencia_regra"])
                        {
                            case 'M':
                                $d1 = new DateTime($data_base);
                                $d1->add(new DateInterval('P1D')); // Início de Vigência: A partir das 24h do dia em que o produto foi adquirido
                                $data_base = $d1->format('Y-m-d');
                                break;
                            default:
                                break;
                        }
                    }
                }

                // se depois de todas as validações, ainda não conseguiu definir a data base
                if ( empty($data_base) )
                {
                    $data_base = date("Y-m-d");
                }

            }

            // se depois de todas as validações, ainda não conseguiu definir a data de adesão
            if ( empty($data_adesao) )
            {
                $data_adesao = $data_base;
            }

            // a adesão deve ser sempre o que foi enviado
            if (!empty($data_ad) && $data_ad != "0000-00-00") {
                $data_adesao = $data_ad;
            }

            $data_base = explode('-', $data_base);

            if (($produto_parceiro_plano['unidade_tempo'] == 'MES')) {
                $date_inicio = date('Y-m-d', mktime(0, 0, 0, $data_base[1] + $produto_parceiro_plano['inicio_vigencia'], $data_base[2], $data_base[0]));
                $data_base2  = explode('-', $date_inicio);
                $date_fim    = date('Y-m-d', mktime(0, 0, 0, $data_base2[1] + $produto_parceiro_plano['limite_vigencia'], $data_base2[2], $data_base2[0]));
            } elseif ($produto_parceiro_plano['unidade_tempo'] == 'MES_A') {

                // Adiciona os meses no INICIO da vigência
                $d1 = new DateTime($data_base[2]."-".$data_base[1]."-".$data_base[0]);
                $ini = (int)$produto_parceiro_plano['inicio_vigencia'];
                $d1->add(new DateInterval("P{$ini}M"));
                $date_inicio = $d1->format('Y-m-d');
                $m = $d1->format('m');

                // Adiciona os meses na FIM da vigência
                $fim = (int)$produto_parceiro_plano['limite_vigencia'];
                $date_fim = $d2 = $d1;
                $d2->add(new DateInterval("P{$fim}M"));
                $rem = 0;

                // valida FEVEREIRO, onde o PHP add os meses com visão de dias
                if ($d2->format('m') - $m != $fim) {
                    // volta para o último dia do mês
                    $rem = $d2->format('d');
                    $d2 = $d2->sub(new DateInterval("P{$rem}D"));
                } else {
                    // retira um dia (-1 dia)
                    $date_fim = $d2->sub(new DateInterval("P1D"));
                }

                $date_fim = $date_fim->format('Y-m-d');

            } elseif ($produto_parceiro_plano['unidade_tempo'] == 'ANO') {
                $date_inicio = date('Y-m-d', mktime(0, 0, 0, $data_base[1], $data_base[2], $data_base[0] + $produto_parceiro_plano['inicio_vigencia']));
                $data_base2  = explode('-', $date_inicio);
                $date_fim    = date('Y-m-d', mktime(0, 0, 0, $data_base2[1], $data_base2[2], $data_base2[0] + $produto_parceiro_plano['limite_vigencia']));
            } else {
                $date_inicio = date('Y-m-d', mktime(0, 0, 0, $data_base[1], $data_base[2] + $produto_parceiro_plano['inicio_vigencia'], $data_base[0]));
                $data_base2  = explode('-', $date_inicio);
                $date_fim    = date('Y-m-d', mktime(0, 0, 0, $data_base2[1], $data_base2[2] + $produto_parceiro_plano['limite_vigencia'], $data_base2[0]));
            }

            // caso tenha uma data fim específica
            if ( !empty($date_fim_vig) )
            {
                $date_fim = $date_fim_vig;
            }

            // gera o resultado de cada cobertura
            $result[] = [
                'inicio_vigencia' => $date_inicio,
                'fim_vigencia'    => $date_fim,
                'dias'            => app_date_get_diff_mysql($date_inicio, $date_fim, 'D'),
                'data_adesao'     => $data_adesao,
            ];

        }

        return array(
            'inicio_vigencia' => $result[0]['inicio_vigencia'],
            'fim_vigencia'    => $result[0]['fim_vigencia'],
            'dias'            => $result[0]['dias'],
            'data_adesao'     => $result[0]['data_adesao'],
            'coberturas'      => $result,    
        );
    }

    public function getDatasCapa($produto_parceiro_plano_id, $data_base, $data_vencimento, $is_controle_endosso_pelo_cliente = false)
    {

        $date_inicio = $data_base;

        $config = $this->with_produto_parceiro_configuracao($produto_parceiro_plano_id)->get_all();
        if ($config) {
            $config = $config[0];

            if ($config['pagamento_tipo'] == 'RECORRENTE' || $is_controle_endosso_pelo_cliente) {

                $data_base = explode('-', $data_base);
                $d1 = new DateTime($data_base[2]."-".$data_base[1]."-".$data_base[0]);
                $date_inicio = $d1->format('Y-m-d');
                $fim = 1;
                $date_fim = $d2 = $d1;

                $data_vencimento = explode('-', $data_vencimento);
                $dvcto = new DateTime($data_vencimento[2]."-".$data_vencimento[1]."-".$data_vencimento[0]);

                if ($is_controle_endosso_pelo_cliente)
                {
                    $config["pagamento_periodicidade_unidade"] = 'MES';
                }

                switch ($config["pagamento_periodicidade_unidade"])
                {
                    case "DIA": //
                        if ($is_controle_endosso_pelo_cliente == 1)
                        {
                            $d2->add(new DateInterval("P{$fim}D"));
                        }

                        $dvcto->add(new DateInterval("P1D"));

                        break;
                    case "MES":

                        if ($is_controle_endosso_pelo_cliente == 1) 
                        {
                            // Adiciona os meses no INICIO da vigência
                            $m = $d1->format('m');

                            // Adiciona os meses na FIM da vigência
                            $d2->add(new DateInterval("P{$fim}M"));

                            // valida FEVEREIRO, onde o PHP add os meses com visão de dias
                            if ($d2->format('m') - $m != $fim) {
                                // volta para o último dia do mês
                                $rem = $d2->format('d');
                                $d2 = $d2->sub(new DateInterval("P{$rem}D"));
                            } else {
                                // retira um dia (-1 dia)
                                $date_fim = $d2->sub(new DateInterval("P1D"));
                            }
                        }

                        // Adiciona os meses no vencimento
                        $dvcto->add(new DateInterval("P1M"));

                        break;
                    case "ANO":
                        if ($is_controle_endosso_pelo_cliente == 1) 
                        {
                            $d2->add(new DateInterval("P{$fim}Y"));
                        }

                        $dvcto->add(new DateInterval("P1Y"));

                        break;
                }

                $date_fim = $date_fim->format('Y-m-d');
                $data_vencimento = $dvcto->format('Y-m-d');
            }
        }

        return array(
            'inicio_vigencia' => $date_inicio,
            'fim_vigencia'    => $date_fim,
            'data_vencimento' => $data_vencimento,
            'dias'            => app_date_get_diff_mysql($date_inicio, $date_fim, 'D'),
        );
    }

    public function verifica_tempo_limite_de_uso($produto_parceiro_id, $produto_parceiro_plano_id, $data)
    {

        $result = $this->coreSelectPlanosProdutoParceiro($produto_parceiro_id, $produto_parceiro_plano_id)->get_all();

        if (!empty($result)) {
            $result = $result[0];

            if (!empty($result['possui_limite_tempo'])) {
                $desc = '';
                switch ($result['unidade_limite_tempo']) {
                    case 'DIA':
                        $base = 'D';
                        $desc = 'Dia(s)';
                        break;
                    case 'MES':
                        $base = 'M';
                        $desc = 'Mes(es)';
                        break;
                    case 'ANO':
                        $base = 'Y';
                        $desc = 'Ano(s)';
                        break;
                }

                $d = app_date_get_diff($data, date('Y-m-d'), $base);

                if ($d > $result['limite_tempo']) {
                    return "O plano {$result['nome']} requer que o Equipamento tenha um prazo máximo de uso de {$result['limite_tempo']} {$desc}";
                }

            }

        }

        return null;
    }

}
