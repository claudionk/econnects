<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Relatorios
 */
class Relatorios extends Admin_Controller
{
    /**
     * Relatório de vendas
     */
    public function index()
    {

        //Carrega React e Orb (relatórios)
        $this->loadLibraries();
        $this->template->js(app_assets_url("modulos/relatorios/vendas/venda.js", "admin"));

        //Dados para template
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data);
    }


    public function mapa_repasse_dinamico()
    {

        //Carrega React e Orb (relatórios)
        $this->loadLibraries();
        $this->template->js(app_assets_url("modulos/relatorios/vendas/mapa_repasse.js", "admin"));

        //Dados para template
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data);
    }

    public function vendas1()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 01 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Desc. do Produto',
            'Seguro Contratado',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'), 
                        $row['segurado'], 
                        $row['documento'], 
                        $row['plano_nome'], 
                        $row['nome_produto_parceiro'], 
                        app_format_currency($row['nota_fiscal_valor'], true), 
                        app_format_currency($row['premio_liquido_total'], true), 
                        $row['num_apolice'], 
                    ];
                }
                $this->exportExcel($data['columns'], $rows);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas1", $data);
    }

    public function vendas4()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 04 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Seguro Contratado',
            'Desc. do Produto',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
            'Varejista',
            'CNPJ Varejista',
            'UF Varejista',
            'Vendedor',
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'), 
                        $row['segurado'], 
                        $row['documento'], 
                        $row['plano_nome'], 
                        $row['nome_produto_parceiro'], 
                        app_format_currency($row['nota_fiscal_valor'], true), 
                        app_format_currency($row['premio_liquido_total'], true), 
                        $row['num_apolice'], 
                        $row['nome_fantasia'], 
                        $row['cnpj'], 
                        $row['UF'], 
                        $row['vendedor'], 
                    ];
                }
                $this->exportExcel($data['columns'], $rows);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas4", $data);
    }

    public function vendas5()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['title'] = 'Relatório 05 de Vendas';
        $data['columns'] = [
            'Data da Venda',
            'Cliente',
            'Documento',
            'Seguro Contratado',
            'Desc. do Produto',
            'Importancia Segurada',
            'Valor do Prêmio',
            'Num. Apólice',
            'Varejista',
            'CNPJ Varejista',
            'UF Varejista',
            'Valor a Receber',
        ];

        if ($_POST) {
            $result = $this->getRelatorio(FALSE);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {

                $rows = [];
                foreach ($data['result'] as $row) {
                    $rows[] = [
                        app_date_mysql_to_mask($row['status_data'], 'd/m/Y'), 
                        $row['segurado'], 
                        $row['documento'], 
                        $row['plano_nome'], 
                        $row['nome_produto_parceiro'], 
                        app_format_currency($row['nota_fiscal_valor'], true), 
                        app_format_currency($row['premio_liquido_total'], true), 
                        $row['num_apolice'], 
                        $row['nome_fantasia'], 
                        $row['cnpj'], 
                        $row['UF'], 
                        app_format_currency($row['comissao_parceiro'], true), 
                    ];
                }
                $this->exportExcel($data['columns'], $rows);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/vendas5", $data);
    }

    public function _mapa_repasse_lasa()
    {
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['action'] = $this->uri->segment(3);
        $data['src'] = $this->controller_uri;
        $data['title'] = 'Relatório de Mapa de Repasse';
        $data['layout'] = 'mapa_analitico';
        $data['columns'] = [
            'Operacao',
            'Grupo',
            'Data da Venda',
            'Inicio Vigencia',
            'Fim Vigencia',
            'Num Bilhete',
            'Segurado',
            'Documento',
            'Equipamento',
            'Marca',
            'Modelo',
            'IMEI',
            'Produto',
            'Importancia Segurada',
            'Num Endosso',
            'Vigencia Parcela',
            'Parcela',
            'Status Parcela',
            'Data Cancelamento',
            'Valor Parcela',
            'Premio Bruto Roubo Furto',
            'Premio Liquido Roubo Furto',
            'Premio Bruto Quebra',
            'Premio Liquido Quebra',
            'Pro Labore',
            'Comissao Corretagem',

        ];

        if ($_POST) {
            if (!empty($this->input->get_post('layout'))) {
                $data['layout'] = $this->input->get_post('layout');
            }
            $result = $this->getMapaRepasse(FALSE, $data['layout']);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {
                $this->exportExcelMapaRepasse($data['columns'], $data['result']);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim');
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "{$this->controller_uri}/{$data['action']}" , $data);
    }

    public function mapa_repasse()
    {     
        //Dados para template
        $data = array();
        $data['data_inicio'] = date("d/m/Y",strtotime("-1 month"));
        $data['data_fim'] = date("d/m/Y");
        $data['action'] = $this->uri->segment(3);
        $data['src'] = $this->controller_uri;
        $data['title'] = 'Relatório de Mapa de Repasse';
        $data['layout'] = 'mapa_analitico';
        $data['columns'] = [
            'Plano',
            'Representante',
            'Cobertura',
            'Tipo Movimento (Emissão ou Cancelamento',
            'Data do Movimento',
            'Inicio Vigencia',
            'Fim Vigencia',
            'Num Bilhete',
            'Nome',
            'CPF',
            'Equipamento',
            'Marca',
            'Modelo',
            'IMEI',
            'Produto',
            'Importancia Segurada',
            'Forma Pagto',
            'Num Endosso',
            'Mês Parcela',
            'Parcela',
            'Status Parcela',
            'Data processamento Cliente/SIS',
            'Data Cancelamento',
            'Valor Parcela',
            'Premio Bruto Roubo Furto',
            'Premio Liquido Roubo Furto',
            'Premio Bruto Quebra',
            'Premio Liquido Quebra',
            'Comissao Representante',
            'Comissao Corretagem',

        ];
        // Bandeira utilizada para exibir os resultados
        $data['flag'] = FALSE;
        if ($_POST) {

            $data['title'] = "Relatório de Mapa de Repasse - ( ".$_POST['nomerepresentante']." )";

            $data['id_parceiro'] = $_POST['representante'];
            $data['slug'] = $_POST['slug'];

            if (!empty($this->input->get_post('layout'))) {
                $data['layout'] = $this->input->get_post('layout');
                $data['flag'] = TRUE;
            }
            $result = $this->getMapaRepasse(FALSE, $data['layout'], $data['id_parceiro'], $data['slug']);
            $data['result'] = $result['data'];

            if (!empty($_POST['btnExcel'])) {
                $this->exportExcelMapaRepasse($data['columns'], $data['result']);
            }

            //Dados via GET
            $data['data_inicio'] = $this->input->get_post('data_inicio');
            $data['data_fim'] = $this->input->get_post('data_fim'); 

            $data['id_parceiro'] = $_POST['representante'];
            $data['slug'] = $_POST['slug'];

        }

        $data['combo'] = $this->getParceiro();


        //Carrega template
        $this->template->load("admin/layouts/base", "{$this->controller_uri}/{$data['action']}" , $data);
    }


    /* Retorno os dados para combo */
      public function getParceiro()
      {

        $this->load->model('pedido_model', 'pedido');
        return $this->pedido->getRepresentantes();
      }

    /**
     * Retorna resultado
     */
    public function getRelatorio ($ajax = TRUE)
    {
        $this->load->model("pedido_model", "pedido");

        $resultado = array();
        $resultado['status'] = false;
        // $pedidos = $this->pedido;

        //Dados via GET
        $data_inicio = $this->input->get_post('data_inicio');
        $data_fim = $this->input->get_post('data_fim');

        $resultado['data'] = $this->pedido->extrairRelatorioVendas($data_inicio, $data_fim);
        $resultado['status'] = true;

        if ($ajax)
            echo json_encode($resultado);
        else
            return $resultado;
    }

    /**
     * Retorna resultado
     */
    public function getMapaRepasse ($ajax = TRUE, $layout, $parceiro, $slug)
    {
        $this->load->model("pedido_model", "pedido");

        $resultado = array();
        $resultado['status'] = false;

        //Dados via GET
        $data_inicio = $this->input->get_post('data_inicio');
        $data_fim = $this->input->get_post('data_fim');

        if ($layout=='mapa_analitico') {

            $resultado['data'] = $this->pedido->extrairRelatorioMapaRepasseAnalitico($data_inicio = null, $data_fim = null, $parceiro, $slug);
        } else {

            // 'Sinténtico'

            $resultado['data'] = $this->preparaMapaRepasse($this->pedido->extrairRelatorioMapaRepasseSintetico($data_inicio, $data_fim, $parceiro, $slug));
        }
        $resultado['status'] = true;

        if ($ajax)
            echo json_encode($resultado);
        else
            return $resultado;
    }

    private function preparaMapaRepasse($result)
    {
               

        if (empty($result)) {
            return [];
        }

        $planos = [];

        foreach ($result as $k => $v) {
            $planos[$k] = $v['planos'];
        }


        $ret = [];
        $V_quantidade = 0;
        $V_IOF = 0;
        $V_PL = 0;
        $V_PB = 0;
        $V_pro_labore = 0;
        $V_valor_comissao = 0;

        $C_quantidade = 0;
        $C_IOF = 0;
        $C_PL = 0;
        $C_PB = 0;
        $C_pro_labore = 0;
        $C_valor_comissao = 0;

        $T_quantidade = 0;
        $T_IOF = 0;
        $T_PL = 0;
        $T_PB = 0;
        $T_pro_labore = 0;
        $T_valor_comissao = 0;

        foreach ($planos as $key => $desc) { 
            $find = false;
            foreach ($result as $row) { 
                if ($row['planos'] == $desc) {
                    $row['desc'] = $desc;
                    $ret[] = $row;

                    $V_quantidade += $row['V_quantidade'];
                    $V_IOF += $row['V_IOF'];
                    $V_PL += $row['V_PL'];
                    $V_PB += $row['V_PB'];
                    $V_pro_labore += $row['V_pro_labore'];
                    $V_valor_comissao += $row['V_valor_comissao'];

                    $C_quantidade += $row['C_quantidade'];
                    $C_IOF += $row['C_IOF'];
                    $C_PL += $row['C_PL'];
                    $C_PB += $row['C_PB'];
                    $C_pro_labore += $row['C_pro_labore'];
                    $C_valor_comissao += $row['C_valor_comissao'];

                    $T_quantidade += $V_quantidade + $C_quantidade;
                    $T_IOF += $V_IOF + $C_IOF;
                    $T_PL += $V_PL + $C_PL;
                    $T_PB += $V_PB + $C_PB;
                    $T_pro_labore += $V_pro_labore + $C_pro_labore;
                    $T_valor_comissao += $V_valor_comissao + $C_valor_comissao;
                    $find = true;
                    break;
                }
            }

            if (!$find) {
                $ret[] = [
                    'desc' => $desc,
                    'V_quantidade' => 0,
                    'V_IOF' => 0,
                    'V_PL' => 0,
                    'V_PB' => 0,
                    'V_pro_labore' => 0,
                    'V_valor_comissao' => 0,

                    'C_quantidade' => 0,
                    'C_IOF' => 0,
                    'C_PL' => 0,
                    'C_PB' => 0,
                    'C_pro_labore' => 0,
                    'C_valor_comissao' => 0,

                    'T_quantidade' => 0,
                    'T_IOF' => 0,
                    'T_PL' => 0,
                    'T_PB' => 0,
                    'T_pro_labore' => 0,
                    'T_valor_comissao' => 0,
                ];
            }
        }

        $ret[] = [
            'desc' => 'TOTAL',
            'V_quantidade' => $V_quantidade,
            'V_IOF' => $V_IOF,
            'V_PL' => $V_PL,
            'V_PB' => $V_PB,
            'V_pro_labore' => $V_pro_labore,
            'V_valor_comissao' => $V_valor_comissao,
            'C_quantidade' => $C_quantidade,
            'C_IOF' => $C_IOF,
            'C_PL' => $C_PL,
            'C_PB' => $C_PB,
            'C_pro_labore' => $C_pro_labore,
            'C_valor_comissao' => $C_valor_comissao,
            'T_quantidade' => $T_quantidade,
            'T_IOF' => $T_IOF,
            'T_PL' => $T_PL,
            'T_PB' => $T_PB,
            'T_pro_labore' => $T_pro_labore,
            'T_valor_comissao' => $T_valor_comissao,
        ];

        return $ret;
    }

    private function _preparaMapaRepasse($result)
    {
        if (empty($result)) {
            return [];
        }

        $tpas = [
            '007' => 'NOVOS',
            '010' => 'USADOS'
        ];

        $ret = [];
        $V_quantidade_RF = 0;
        $V_IOF_RF = 0;
        $V_PL_RF = 0;
        $V_PB_RF = 0;
        $V_pro_labore_RF = 0;
        $V_valor_comissao_RF = 0;
        $V_quantidade_QA = 0;
        $V_PB_QA = 0;
        $V_IOF_QA = 0;
        $V_PL_QA = 0;
        $V_pro_labore_QA = 0;
        $V_valor_comissao_QA = 0;

        $C_quantidade_RF = 0;
        $C_IOF_RF = 0;
        $C_PL_RF = 0;
        $C_PB_RF = 0;
        $C_pro_labore_RF = 0;
        $C_valor_comissao_RF = 0;
        $C_quantidade_QA = 0;
        $C_PB_QA = 0;
        $C_IOF_QA = 0;
        $C_PL_QA = 0;
        $C_pro_labore_QA = 0;
        $C_valor_comissao_QA = 0;

        $T_quantidade_RF = 0;
        $T_IOF_RF = 0;
        $T_PL_RF = 0;
        $T_PB_RF = 0;
        $T_pro_labore_RF = 0;
        $T_valor_comissao_RF = 0;
        $T_quantidade_QA = 0;
        $T_PB_QA = 0;
        $T_IOF_QA = 0;
        $T_PL_QA = 0;
        $T_pro_labore_QA = 0;
        $T_valor_comissao_QA = 0;

        foreach ($tpas as $tpa => $desc) { 
            $find = false;
            foreach ($result as $row) { 
                if ($row['cod_tpa'] == $tpa) {
                    $row['desc'] = $desc;
                    $ret[] = $row;

                    $V_quantidade_RF += $row['V_quantidade_RF'];
                    $V_IOF_RF += $row['V_IOF_RF'];
                    $V_PL_RF += $row['V_PL_RF'];
                    $V_PB_RF += $row['V_PB_RF'];
                    $V_pro_labore_RF += $row['V_pro_labore_RF'];
                    $V_valor_comissao_RF += $row['V_valor_comissao_RF'];
                    $V_quantidade_QA += $row['V_quantidade_QA'];
                    $V_PB_QA += $row['V_PB_QA'];
                    $V_IOF_QA += $row['V_IOF_QA'];
                    $V_PL_QA += $row['V_PL_QA'];
                    $V_pro_labore_QA += $row['V_pro_labore_QA'];
                    $V_valor_comissao_QA += $row['V_valor_comissao_QA'];

                    $C_quantidade_RF += $row['C_quantidade_RF'];
                    $C_IOF_RF += $row['C_IOF_RF'];
                    $C_PL_RF += $row['C_PL_RF'];
                    $C_PB_RF += $row['C_PB_RF'];
                    $C_pro_labore_RF += $row['C_pro_labore_RF'];
                    $C_valor_comissao_RF += $row['C_valor_comissao_RF'];
                    $C_quantidade_QA += $row['C_quantidade_QA'];
                    $C_PB_QA += $row['C_PB_QA'];
                    $C_IOF_QA += $row['C_IOF_QA'];
                    $C_PL_QA += $row['C_PL_QA'];
                    $C_pro_labore_QA += $row['C_pro_labore_QA'];
                    $C_valor_comissao_QA += $row['C_valor_comissao_QA'];

                    $T_quantidade_RF += $V_quantidade_RF + $C_quantidade_RF;
                    $T_IOF_RF += $V_IOF_RF + $C_IOF_RF;
                    $T_PL_RF += $V_PL_RF + $C_PL_RF;
                    $T_PB_RF += $V_PB_RF + $C_PB_RF;
                    $T_pro_labore_RF += $V_pro_labore_RF + $C_pro_labore_RF;
                    $T_valor_comissao_RF += $V_valor_comissao_RF + $C_valor_comissao_RF;
                    $T_quantidade_QA += $V_quantidade_QA + $C_quantidade_QA;
                    $T_PB_QA += $V_PB_QA + $C_PB_QA;
                    $T_IOF_QA += $V_IOF_QA + $C_IOF_QA;
                    $T_PL_QA += $V_PL_QA + $C_PL_QA;
                    $T_pro_labore_QA += $V_pro_labore_QA + $C_pro_labore_QA;
                    $T_valor_comissao_QA += $V_valor_comissao_QA + $C_valor_comissao_QA;
                    $find = true;
                    break;
                }
            }

            if (!$find) {
                $ret[] = [
                    'desc' => $desc,
                    'V_quantidade_RF' => 0,
                    'V_IOF_RF' => 0,
                    'V_PL_RF' => 0,
                    'V_PB_RF' => 0,
                    'V_pro_labore_RF' => 0,
                    'V_valor_comissao_RF' => 0,
                    'V_quantidade_QA' => 0,
                    'V_PB_QA' => 0,
                    'V_IOF_QA' => 0,
                    'V_PL_QA' => 0,
                    'V_pro_labore_QA' => 0,
                    'V_valor_comissao_QA' => 0,

                    'C_quantidade_RF' => 0,
                    'C_IOF_RF' => 0,
                    'C_PL_RF' => 0,
                    'C_PB_RF' => 0,
                    'C_pro_labore_RF' => 0,
                    'C_valor_comissao_RF' => 0,
                    'C_quantidade_QA' => 0,
                    'C_PB_QA' => 0,
                    'C_IOF_QA' => 0,
                    'C_PL_QA' => 0,
                    'C_pro_labore_QA' => 0,
                    'C_valor_comissao_QA' => 0,

                    'T_quantidade_RF' => 0,
                    'T_IOF_RF' => 0,
                    'T_PL_RF' => 0,
                    'T_PB_RF' => 0,
                    'T_pro_labore_RF' => 0,
                    'T_valor_comissao_RF' => 0,
                    'T_quantidade_QA' => 0,
                    'T_PB_QA' => 0,
                    'T_IOF_QA' => 0,
                    'T_PL_QA' => 0,
                    'T_pro_labore_QA' => 0,
                    'T_valor_comissao_QA' => 0,
                ];
            }
        }

        $ret[] = [
            'desc' => 'TOTAL',
            'V_quantidade_RF' => $V_quantidade_RF,
            'V_IOF_RF' => $V_IOF_RF,
            'V_PL_RF' => $V_PL_RF,
            'V_PB_RF' => $V_PB_RF,
            'V_pro_labore_RF' => $V_pro_labore_RF,
            'V_valor_comissao_RF' => $V_valor_comissao_RF,
            'V_quantidade_QA' => $V_quantidade_QA,
            'V_PB_QA' => $V_PB_QA,
            'V_IOF_QA' => $V_IOF_QA,
            'V_PL_QA' => $V_PL_QA,
            'V_pro_labore_QA' => $V_pro_labore_QA,
            'V_valor_comissao_QA' => $V_valor_comissao_QA,
            'C_quantidade_RF' => $C_quantidade_RF,
            'C_IOF_RF' => $C_IOF_RF,
            'C_PL_RF' => $C_PL_RF,
            'C_PB_RF' => $C_PB_RF,
            'C_pro_labore_RF' => $C_pro_labore_RF,
            'C_valor_comissao_RF' => $C_valor_comissao_RF,
            'C_quantidade_QA' => $C_quantidade_QA,
            'C_PB_QA' => $C_PB_QA,
            'C_IOF_QA' => $C_IOF_QA,
            'C_PL_QA' => $C_PL_QA,
            'C_pro_labore_QA' => $C_pro_labore_QA,
            'C_valor_comissao_QA' => $C_valor_comissao_QA,
            'T_quantidade_RF' => $T_quantidade_RF,
            'T_IOF_RF' => $T_IOF_RF,
            'T_PL_RF' => $T_PL_RF,
            'T_PB_RF' => $T_PB_RF,
            'T_pro_labore_RF' => $T_pro_labore_RF,
            'T_valor_comissao_RF' => $T_valor_comissao_RF,
            'T_quantidade_QA' => $T_quantidade_QA,
            'T_PB_QA' => $T_PB_QA,
            'T_IOF_QA' => $T_IOF_QA,
            'T_PL_QA' => $T_PL_QA,
            'T_pro_labore_QA' => $T_pro_labore_QA,
            'T_valor_comissao_QA' => $T_valor_comissao_QA,
        ];

        return $ret;
    }

    public function exportExcel($columns, $rows = []) {
        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $letters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $contC = 0;
        $contR = 1;

        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        // Cria as colunas
        foreach ($columns as $column) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$contC] . $contR, $column);
            $contC++;
        }

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:'. $letters[count($columns)-1].'1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9')
                )
            )
        );

        // Cria as Linhas
        foreach ($rows as $row) {
            $contR++;
            $contC = 0;

            foreach ($columns as $column) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$contC] . $contR, $row[$contC]);
                $contC++;
            }
            
        }

        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // $objWriter->save(str_replace('.php', '.xlsx', app_assets_dir('temp', 'uploads'). basename(__FILE__)));

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="relatorio.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function exportExcelMapaRepasse($columns, $rows = []) {
        $this->load->library('Excel');
        $objPHPExcel = new PHPExcel();

        $letters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $contC = 0;
        $contR = 1;

        $styleCenter = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleCenterVertic = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleRight = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
        );
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:K2')->applyFromArray($styleCenter);
        
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C1:E1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','VENDAS');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F1:H1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','CANCELAMENTOS');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('I1:K1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','TOTAL');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2','Roubo ou Furto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2','Quebra Acidental');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2','TOTAL');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2','Roubo ou Furto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2','Quebra Acidental');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2','TOTAL');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2','Roubo ou Furto');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2','Quebra Acidental');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2','TOTAL');

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:K1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9')
                )
            )
        );

        $contR = 3;
        // Cria as Linhas
        foreach ($rows as $row) {
            $contRFim = $contR + 5;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':H'.$contR)->applyFromArray($styleCenter);

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$contR.':A'.$contRFim)->getStyle('A'.$contR.':A'.$contR)->applyFromArray($styleCenterVertic);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$contR)->getAlignment()->setTextRotation(90);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'. $contR,$row['desc']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Quantidade de Registros');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, $row['V_quantidade_RF']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, $row['V_quantidade_QA']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, $row['V_quantidade_RF'] + $row['V_quantidade_QA']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'. $contR, $row['C_quantidade_RF']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'. $contR, $row['C_quantidade_QA']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'. $contR, $row['C_quantidade_RF'] + $row['C_quantidade_QA']);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'. $contR, $row['V_quantidade_RF'] + $row['C_quantidade_RF']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'. $contR, $row['V_quantidade_QA'] + $row['C_quantidade_QA']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'. $contR, $row['V_quantidade_RF'] + $row['C_quantidade_RF'] + $row['V_quantidade_QA'] + $row['C_quantidade_QA']);
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Prêmio Bruto');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['V_PB_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['V_PB_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['V_PB_RF'] + $row['V_PB_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'. $contR, app_format_currency($row['C_PB_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'. $contR, app_format_currency($row['C_PB_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'. $contR, app_format_currency($row['C_PB_RF'] + $row['C_PB_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'. $contR, app_format_currency($row['V_PB_RF'] + $row['C_PB_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'. $contR, app_format_currency($row['V_PB_QA'] + $row['C_PB_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'. $contR, app_format_currency($row['V_PB_RF'] + $row['C_PB_RF'] + $row['V_PB_QA'] + $row['C_PB_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'IOF');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['V_IOF_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['V_IOF_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['V_IOF_RF'] + $row['V_IOF_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'. $contR, app_format_currency($row['C_IOF_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'. $contR, app_format_currency($row['C_IOF_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'. $contR, app_format_currency($row['C_IOF_RF'] + $row['C_IOF_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'. $contR, app_format_currency($row['V_IOF_RF'] + $row['C_IOF_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'. $contR, app_format_currency($row['V_IOF_QA'] + $row['C_IOF_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'. $contR, app_format_currency($row['V_IOF_RF'] + $row['C_IOF_RF'] + $row['V_IOF_QA'] + $row['C_IOF_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Prêmio Líquido');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['V_PL_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['V_PL_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['V_PL_RF'] + $row['V_PL_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'. $contR, app_format_currency($row['C_PL_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'. $contR, app_format_currency($row['C_PL_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'. $contR, app_format_currency($row['C_PL_RF'] + $row['C_PL_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'. $contR, app_format_currency($row['V_PL_RF'] + $row['C_PL_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'. $contR, app_format_currency($row['V_PL_QA'] + $row['C_PL_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'. $contR, app_format_currency($row['V_PL_RF'] + $row['C_PL_RF'] + $row['V_PL_QA'] + $row['C_PL_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Pró-labore LASA');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['V_pro_labore_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['V_pro_labore_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['V_pro_labore_RF'] + $row['V_pro_labore_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'. $contR, app_format_currency($row['C_pro_labore_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'. $contR, app_format_currency($row['C_pro_labore_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'. $contR, app_format_currency($row['C_pro_labore_RF'] + $row['C_pro_labore_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'. $contR, app_format_currency($row['V_pro_labore_RF'] + $row['C_pro_labore_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'. $contR, app_format_currency($row['V_pro_labore_QA'] + $row['C_pro_labore_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'. $contR, app_format_currency($row['V_pro_labore_RF'] + $row['C_pro_labore_RF'] + $row['V_pro_labore_QA'] + $row['C_pro_labore_QA'], true));
            $contR++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$contR.':E'.$contR)->applyFromArray($styleRight);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'. $contR, 'Comissão de Corretagem');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'. $contR, app_format_currency($row['V_valor_comissao_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'. $contR, app_format_currency($row['V_valor_comissao_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'. $contR, app_format_currency($row['V_valor_comissao_RF'] + $row['V_valor_comissao_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'. $contR, app_format_currency($row['C_valor_comissao_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'. $contR, app_format_currency($row['C_valor_comissao_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'. $contR, app_format_currency($row['C_valor_comissao_RF'] + $row['C_valor_comissao_QA'], true));

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'. $contR, app_format_currency($row['V_valor_comissao_RF'] + $row['C_valor_comissao_RF'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'. $contR, app_format_currency($row['V_valor_comissao_QA'] + $row['C_valor_comissao_QA'], true));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'. $contR, app_format_currency($row['V_valor_comissao_RF'] + $row['C_valor_comissao_RF'] + $row['V_valor_comissao_QA'] + $row['C_valor_comissao_QA'], true));
            $contR++;
        }

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="relatorio.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    private function loadLibraries() {
        //Carrega React e Orb (relatórios)
        $this->template->js(app_assets_url("core/js/react.js", "admin"));
        $this->template->js(app_assets_url("core/js/orb.min.js", "admin"));
        $this->template->js(app_assets_url("modulos/relatorios/vendas/core.js", "admin"));
        $this->template->js(app_assets_url("template/js/libs/toastr/toastr.js", "admin"));

        $this->template->css(app_assets_url("template/css/{$this->_theme}/libs/toastr/toastr.css", "admin"));
        $this->template->css(app_assets_url("core/css/orb.min.css", "admin"));
    }
}
