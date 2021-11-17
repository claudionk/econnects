<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 *
 * @property Localidade_Países $current_model
 *
 */
class Equipamento extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Equipamento");
        $this->template->set_breadcrumb("Equipamento", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('equipamento_model', 'current_model');
    }

    /**
     * Serviço para buscar equipamentos na tela de inicial
     */
    public function service_categorias($categoria_id = 0, $nivel = 1)
    {
        $this->load->model("equipamento_categoria_model", "equipamento_categoria");
        $this->load->model("equipamentos_elegiveis_categoria_model", "equipamentos_elegiveis_categoria");
        $filter             = $this->input->get_post("q");
        $marca_id           = $this->input->get_post("marca_id");
        $categoria_pai_id   = (!empty($categoria_pai_id)) ? $categoria_pai_id : $this->input->get_post("categoria_pai_id");
        $lista_id           = emptyor($lista_id, $this->input->get_post("lista_id"));
        $page               = ($this->input->get_post("page")) ? $this->input->get_post("page") : 1;
        $limit              = 30;

        if($lista_id > 1)
        {
            $catModel = $this->equipamentos_elegiveis_categoria;
            $nameTableCat = 'equipamentos_elegiveis_categoria'; 
        } else {
            $catModel = $this->equipamento_categoria;
            $nameTableCat = 'vw_Equipamentos_Linhas'; 
        }

        //Se houver categoria
        if( !empty($categoria_id) )
        {
            $itens = $catModel
                ->with_foreign()
                ->get($categoria_id);

            $itens['id'] = $categoria_id;
            $json['total_count'] = 1;
            $json['incomplete_results'] = FALSE;
            $json['items'] = $itens;

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));

            return;
        }

        //Retorna tudo
        $data = $catModel;
        $data->limit($limit, $limit*($page-1));

        if($filter)
        {
            $data->_database->or_where('('. $nameTableCat .'.nome LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where($nameTableCat .'.descricao LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $data = $data->with_foreign();

        if (isset($_POST['0'])) {
            $data = $data->whith_multiples_ids($_POST);
        }

        if (!empty($nivel)) {
            if ($nivel == 1) {
                $data = $data->filter_by_nviel(1);
            } else {
                $data = $data->with_sub_categoria($categoria_pai_id, $marca_id);
            }
        }

        if($lista_id)
        {
            $data->_database->join("(SELECT @lista_id:=$lista_id) AS vli", TRUE);
        }

        $data = $data->get_all();

        $total = $catModel;

        if($filter)
        {
              $total->_database->or_where('('. $nameTableCat .'.nome LIKE "%'.$filter.'%"', NULL, FALSE);
              $total->_database->or_where($nameTableCat .'.descricao LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $total = $total->with_foreign();

        if (isset($_POST['0'])) {
            $total = $total->whith_multiples_ids($_POST);
        }

        if (!empty($nivel)) {
            if ($nivel == 1) {
                $total = $total->filter_by_nviel(1);
            } else {
                $total = $total->with_sub_categoria($categoria_pai_id, $marca_id);
            }
        }

        if($lista_id)
        {
            $total->_database->join("(SELECT @lista_id:=$lista_id) AS vli", TRUE);
        }

        $total = $total->get_total("DISTINCT {$nameTableCat}.equipamento_categoria_id");

        foreach ($data as $index => $item)
        {
            $data[$index]['id'] = $item['equipamento_categoria_id'];
        }

        $json['total_count'] = $total;
        $json['incomplete_results'] = FALSE;
        $json['items'] = $data;

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }


    /**
     * Serviço para buscar equipamentos na tela de inicial
     */
    public function service_marcas($marca_id = 0, $categoria_id = 0)
    {
        $this->load->model("equipamento_marca_model", "equipamento_marca");
        $this->load->model("equipamentos_elegiveis_marca_model", "equipamentos_elegiveis_marca");

        $filter       = $this->input->get_post("q");
        $categoria_id = (!empty($categoria_id)) ? $categoria_id : $this->input->get_post("categoria_id");
        $page         = ($this->input->get_post("page")) ? $this->input->get_post("page") : 1;
        $lista_id     = emptyor($lista_id, $this->input->get_post("lista_id"));
        $limit        = 30;

        if($lista_id > 1)
        {
            $MarcaModel = $this->equipamentos_elegiveis_marca;
            $nameTableMarca = 'equipamentos_elegiveis_marca'; 
        } else {
            $MarcaModel = $this->equipamento_marca;
            $nameTableMarca = 'vw_Equipamentos_Marcas'; 
        }

        //Se houver categoria
        if($marca_id > 0)
        {
            $itens = $MarcaModel
                ->with_foreign()
                ->get($marca_id);

            $itens['id'] = $marca_id;
            $json['total_count'] = 1;
            $json['incomplete_results'] = FALSE;
            $json['items'] = $itens;

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));

            return;
        }

        //Retorna tudo
        $total = $MarcaModel;

        if (!empty($categoria_id))
        {
            $total = $total->get_by_categoria($categoria_id);
        }

        if($filter) {
            $total->_database->where('('.$nameTableMarca.'.nome LIKE "%' . $filter . '%"', NULL, FALSE);
            $total->_database->or_where(''.$nameTableMarca.'.descricao LIKE "%' . $filter . '%")', NULL, FALSE);
        }

        $total = $total->with_foreign();

        if (isset($_POST['0'])) {
            $total = $total->whith_multiples_ids($_POST);
        }

        if($lista_id) $total->_database->join("(SELECT @lista_id:=$lista_id) AS vli", TRUE);
        $total = $total->get_total();

        $data = $MarcaModel;
        $data->limit($limit, $limit*($page-1));

        if (!empty($categoria_id))
        {
            $data = $data->get_by_categoria($categoria_id);
        }

        if($filter)
        {
            $data->_database->where('('.$nameTableMarca.'.nome LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where(''.$nameTableMarca.'.descricao LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $data = $data->with_foreign();

        if (isset($_POST['0'])) {
            $data = $data->whith_multiples_ids($_POST);
        }

        if($lista_id) $data->_database->join("(SELECT @lista_id:=$lista_id) AS vli", TRUE);
        $data = $data->get_all();

        foreach ($data as $index => $item)
        {
            $data[$index]['id'] = $item['equipamento_marca_id'];
        }

        $json['total_count'] = $total;
        $json['incomplete_results'] = FALSE;
        $json['items'] = $data;

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }

    /**
     * Serviço para buscar equipamentos na tela de inicial
     */
    public function service($equipamento_id = 0, $marca_id = 0, $categoria_id = 0)
    {
        $this->load->model("equipamentos_elegiveis_model", "equipamentos_elegiveis");

        $json     = array();
        $ali = emptyor($lista_id, $this->input->get_post("lista_id"));
        $lista_id = emptyor($ali, 1);

        if ($lista_id == 1)
        {
            $EqModel = $this->current_model;
            $nameTableEq = 'vw_Equipamentos'; 
        } else{
            $EqModel = $this->equipamentos_elegiveis;
            $nameTableEq = 'equipamentos_elegiveis'; 
        }

        if (empty($equipamento_id)){
            if (isset($_POST['0'])) {

                $data = $EqModel->with_foreign()->whith_multiples_ids($_POST)->get_all();
                foreach ($data as $index => $item) {
                    $data[$index]['id'] = $item['equipamento_id'];
                }

                // $itens['id'] = $equipamento_id;
                $json['total_count'] = count($data);
                $json['incomplete_results'] = FALSE;
                $json['items'] = $data;

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($json));
                return;
            }
        }

        if($equipamento_id > 0){
            $itens = $EqModel->with_foreign()->get($equipamento_id);
            $itens['id'] = $equipamento_id;
            $json['total_count'] = 1;
            $json['incomplete_results'] = FALSE;
            $json['items'] = $itens;
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));
            return;
        }

        $filter             = $this->input->get_post("q");
        $marca_id           = (!empty($marca_id)) ? $marca_id : $this->input->get_post("marca_id");
        $categoria_id       = (!empty($categoria_id)) ? $categoria_id : $this->input->get_post("categoria_id");
        $sub_categoria_id   = $this->input->get_post("sub_categoria_id");
        $limit              = 30;
        $page               = ($this->input->get_post("page")) ? $this->input->get_post("page") : 1;

        //Retorna tudo
        $data = $EqModel;

        $data->limit($limit, $limit*($page-1));

        if (!empty($marca_id))
        {
            $data->_database->where("equipamento_marca_id = {$marca_id}", NULL, FALSE);
        }

        if (!empty($categoria_id))
        {
            $data->_database->where("equipamento_categoria_id = {$categoria_id}", NULL, FALSE);
        }

        if (!empty($sub_categoria_id))
        {
            $data->_database->where("equipamento_sub_categoria_id = {$sub_categoria_id}", NULL, FALSE);
        }

        if($filter)
        {
            $data->_database->where('(ean LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where('nome LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where('descricao LIKE "%'.$filter.'%"', NULL, FALSE);
            $data->_database->or_where('tags LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $data = $data->with_foreign();
        if($lista_id) $data->_database->join("(SELECT @lista_id:=$lista_id) AS vli", TRUE);
        $data = $data->get_all();

        $total = $EqModel;

        if (!empty($marca_id))
        {
            $total->_database->where("equipamento_marca_id = {$marca_id}", NULL, FALSE);
        }

        if (!empty($categoria_id))
        {
            $total->_database->where("equipamento_categoria_id = {$categoria_id}", NULL, FALSE);
        }

        if (!empty($sub_categoria_id))
        {
            $total->_database->where("equipamento_sub_categoria_id = {$sub_categoria_id}", NULL, FALSE);
        }

        if($filter)
        {
            $total->_database->where('(ean LIKE "%'.$filter.'%"', NULL, FALSE);
            $total->_database->or_where('nome LIKE "%'.$filter.'%"', NULL, FALSE);
            $total->_database->or_where('descricao LIKE "%'.$filter.'%"', NULL, FALSE);
            $total->_database->or_where('tags LIKE "%'.$filter.'%")', NULL, FALSE);
        }

        $total = $total->with_foreign();
        if($lista_id) $total->_database->join("(SELECT @lista_id:=$lista_id) AS vli", TRUE);
        $total = $total->get_total();

        foreach ($data as $index => $item) {
            $data[$index]['id'] = $item['equipamento_id'];
        }

        $json['total_count'] = $total;
        $json['incomplete_results'] = FALSE;
        $json['items'] = $data;
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }

    public function index($offset = 0) { 
      	//Função padrão (load)
        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Tipo de Campo");
        $this->template->set_breadcrumb("Tipo de Campo", base_url("$this->controller_uri/index"));



        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model->get_total();
        $config['per_page'] = 10;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model->limit($config['per_page'], $offset)->get_all();
        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function add() //Função que adiciona registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Adicionar Tipo de Campo");
        $this->template->set_breadcrumb('Add', base_url("$this->controller_uri/index"));

        //Carrega dados da página
        $data = array();
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/add");

        //Caso post
        if($_POST)
        {
            //Valida formulário
            if($this->current_model->validate_form())
            {
                //Insere form
                $insert_id = $this->current_model->insert_form();
                if($insert_id)
                {
                    //Caso inserido com sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
                }
                else
                {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }
                //Redireciona para index
                redirect("$this->controller_uri/index");
            }
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public function edit($id) //Função que edita registro
    {
        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Editar Tipo de Campo");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));


        //Carrega dados para a página
        $data = array();
        $data['row'] = $this->current_model->get($id);
        $data['primary_key'] = $this->current_model->primary_key();
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/edit/{$id}");

        //Verifica se registro existe
        if(!$data['row'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

        //Caso post
        if($_POST)
        {
            if($this->current_model->validate_form()) //Valida form
            {
                //Realiza update
                $this->current_model->update_form();

                //Mensagem de sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.');

                //Redireciona para index
                redirect("$this->controller_uri/index");
            }
        }

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data );
    }
    public  function delete($id)
    {
        //Deleta registro
        $this->current_model->delete($id);
        $this->session->set_flashdata('succ_msg', 'Registro excluido corretamente.');
        redirect("{$this->controller_uri}/index");
    }


}

