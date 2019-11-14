<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Characterize_Phrases
 *
 * @property Colaborador $current_model
 *
 */
class Produtos_Parceiros_Planos_Precificacao_Itens extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega dados para template
        $this->template->set('page_title', 'Tabela de Preços');
        $this->template->set_breadcrumb('Tabela de Preços', base_url("$this->controller_uri/index"));
        
        //Carrega modelos necessários
        $this->load->model('produto_parceiro_plano_precificacao_itens_model', 'current_model');
    }

    private function dadosProdutoPlano($produto_parceiro_plano_id) {
        $this->load->model('produto_parceiro_plano_model', 'parceiro_plano');
        $parceiro_plano = $this->parceiro_plano->get($produto_parceiro_plano_id);
        //Mensagem de erro
        if (empty($parceiro_plano)) {
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("admin/parceiros/index");
        }

        return $parceiro_plano;
    }
    
    public function index($produto_parceiro_plano_id = 0,  $offset = 0)
    {
        $this->load->library('form_validation');

        //Carrega informações da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Tabela de Preços');
        $this->template->set_breadcrumb('Tabela de Preços', base_url("$this->controller_uri/index/{$produto_parceiro_plano_id}"));
        
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Inicializa paginação
        $config['base_url'] = base_url("$this->controller_uri/index/{$produto_parceiro_plano_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->current_model->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)->get_total();
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        
        //Carrega dados
        $data = array();
        $data['parceiro_plano'] = $this->dadosProdutoPlano($produto_parceiro_plano_id);
        $data['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->filter_by_produto_parceiro_plano($produto_parceiro_plano_id)->get_all();

        //Carrega dados do template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add($produto_parceiro_plano_id = 0)
    {   
        //Carrega models necessários
        $this->load->model('moeda_model', 'moedas');

        //Adiciona bibliotecas necessárias
        $this->load->library('form_validation');

        //Carrega dados para o  template
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Tabela de Preços');
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        $parceiro_plano = $this->dadosProdutoPlano($produto_parceiro_plano_id);

        // tipo de precificação
        if ($parceiro_plano["precificacao_tipo_id"] == $this->config->item("PRECO_POR_LINHA")) {
            $data['descTipo'] = 'Linha';
            $data['descSelect'] = 'categorias';
        } elseif ($parceiro_plano["precificacao_tipo_id"] == $this->config->item("PRECO_POR_EQUIPAMENTO")) {
            $data['descTipo'] = 'Equipamento';
            $data['descSelect'] = 'equipamento_sub_categoria_id';
        }

        $data['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
        $data['precificacao_tipo_id'] = $parceiro_plano["precificacao_tipo_id"];
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/add");

        //Caso post
        if($_POST)
        {
            if ($this->current_model->validate_form())
            {
                $this->current_model->insert_form();
                redirect("$this->controller_uri/index/{$produto_parceiro_plano_id}");
            }
        }
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }

    public function edit($produto_parceiro_plano_id, $id)
    {
        $parceiro_plano = $this->dadosProdutoPlano($produto_parceiro_plano_id);

        //Carrega models necessários
        $this->load->model('moeda_model', 'moedas');

        //Carrega bibliotecas necessesárias
        $this->load->library('form_validation');

        //Seta informações na página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', 'Tabela de Preços');
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

        //Carrega dados para a página
        $data = array();
        // $data['coberturas'] = $this->coberturas->get_all();
        $data['moedas'] = $this->moedas->get_all();
        $data['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;
        $data['precificacao_tipo_id'] = $parceiro_plano["precificacao_tipo_id"];
        $data['row'] =  $this->current_model->get($id); //Carrega Cobertura
        $data['primary_key'] = $this->current_model->primary_key();
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        // tipo de precificação
        if ($parceiro_plano["precificacao_tipo_id"] == $this->config->item("PRECO_POR_LINHA")) {
            $data['descTipo'] = 'Linha';
            $data['descSelect'] = 'categorias';
        } elseif ($parceiro_plano["precificacao_tipo_id"] == $this->config->item("PRECO_POR_EQUIPAMENTO")) {
            $data['descTipo'] = 'Equipamento';
            $data['descSelect'] = 'equipamento_sub_categoria_id';
        }

        //Caso não exista registros
        if(!$data['row'])
        {
            //Mensagem de erro
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            redirect("$this->controller_uri/index/{$produto_parceiro_plano_id}");
        }

        //Caso post
        if($_POST)
        {
            if ($this->current_model->validate_form())
            {
                $this->current_model->update_form();

                $this->session->set_flashdata('succ_msg', 'Os dados cadastrais foram salvos com sucesso.');
                redirect("$this->controller_uri/index/{$produto_parceiro_plano_id}");
            }
        }

        //Carrega assets
        $this->template->js(app_assets_url('modulos/produtos_parceiros_planos_precificacao_itens/js/base.js', 'admin'));

        //Carrega dados para o template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($produto_parceiro_plano_id, $id)
    {
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("$this->controller_uri/index/{$produto_parceiro_plano_id}");
    }

    /**
     * Importa excel
     * @param $produto_parceiro_plano_id
     */
    public function importar_preficicacao($produto_parceiro_plano_id)
    {
        $data = array();
        $data['produto_parceiro_plano_id'] = $produto_parceiro_plano_id;

        //Carrega view de preficiação
        $this->load->view("$this->controller_uri/importar_preficicacao", $data);
    }

    /**
     * Importa excel
     */
    public function importar_excel($produto_parceiro_plano_id)
    {
        //Carrega biblioteca do excel
        $this->load->library('ExcelHelper');

        //Carrega bibliotecas necessesárias


        $excel = new ExcelHelper();

        if($_FILES)
        {
            $arquivo = $this->upload('precificacoes', 'arquivo');

            $rows = $excel->excelLibrary->getArrayRows(UPLOAD_PATH . "precificacoes/{$arquivo}");


            $valido = true;
            $i = 0;
            $dados = array();
            foreach($rows as $row)
            {
                if($i > 0)
                {
                    $data = array(
                        'produto_parceiro_plano_id' => $produto_parceiro_plano_id,
                        'tipo' => $row[0],
                        'unidade_tempo' => $row[1],
                        'inicial' => $row[2],
                        'final' => $row[3],
                        'valor' => $row[4],
                    );

                    if(!$this->current_model->validate($data))
                    {
                        $valido = false;
                    }
                    $dados[] = $data;
                }
                else
                {
                    if($i != 0)
                        $valido = false;
                }
                $i++;
            }

            if($valido)
            {
                $this->current_model->delete_by(array(
                    'produto_parceiro_plano_id' => $produto_parceiro_plano_id
                ));

                $erro = false;

                foreach($dados as $row)
                {
                    if(!$this->current_model->insert($row))
                        $erro = true;
                }

                if(!$erro)
                {
                    $this->session->set_flashdata('succ_msg', 'Importação realizada com sucesso.');
                }
                else
                {
                    $this->session->set_flashdata('fail_msg', 'Não foi possível realizar a importação.');
                }
            }
            else
            {
                $this->session->set_flashdata('fail_msg', 'Arquivo excel inválido.');
            }
            redirect("$this->controller_uri/index/{$produto_parceiro_plano_id}");
        }
    }



    /**
     * Exporta excel
     * @param $produto_parceiro_plano_id
     */
    public function exportar_excel($produto_parceiro_plano_id)
    {
        //Carrega biblioteca do excel
        $this->load->library('ExcelHelper');

        $excel = new ExcelHelper();

        $itens = $this->current_model->get_many_by(array(
            'produto_parceiro_plano_id' => $produto_parceiro_plano_id
        ));

        $excel->setHeader(array(
            array('nome' => 'TIPO', 'tamanho' => 18),
            array('nome' => 'UNIDADE DE TEMPO', 'tamanho' => 22),
            array('nome' => 'INICIAL', 'tamanho' => 15),
            array('nome' => 'FINAL', 'tamanho' => 15),
            array('nome' => 'VALOR', 'tamanho' => 15),
        ));

        if($itens)
        {
            $coluna = 2;
            foreach ($itens as $item)
            {
                $excel->sheet->setCellValue("A{$coluna}", $item['tipo']);
                $excel->sheet->setCellValue("B{$coluna}", $item['unidade_tempo']);
                $excel->sheet->setCellValue("C{$coluna}", $item['inicial']);
                $excel->sheet->setCellValue("D{$coluna}", $item['final']);
                $excel->sheet->setCellValue("E{$coluna}", $item['valor']);

                $coluna ++;
            }
        }

        $excel->generate();
    }

 }

