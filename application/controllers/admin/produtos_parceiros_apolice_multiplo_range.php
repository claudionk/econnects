<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Produtos_parceiros_apolice_multiplo_range extends Admin_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('produto_parceiro_apolice_multiplo_model', 'apolice_multiplo');
        $this->load->model('produto_parceiro_apolice_multiplo_range_model', 'apolice_multiplo_range');
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
    }

    public function index($parceiro_id, $offset = 0)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Produtos");
         $this->template->set('page_title', "Produtos:");

        $this->template->set_breadcrumb("Produtos", base_url("$this->controller_uri/index"));


        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index/{$parceiro_id}");
        $config['uri_segment'] = 5;
        $config['total_rows'] =  $this->apolice_multiplo_range->filter_by_parceiro($parceiro_id)->get_total();
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        $data = [];
        $data['parceiro_id'] = $parceiro_id;
        $data['rows'] = $this->apolice_multiplo_range->limit($config['per_page'], $offset)
            ->filter_by_parceiro($parceiro_id)
            ->get_all();
        $data['primary_key'] = $this->apolice_multiplo_range->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();

        // echo "<pre>";print_r($data['rows']);echo $this->db->last_query();die();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data);
    }

    public function exec($data = [], $parceiro_id, $produto_parceiro_apolice_multiplo_range_id = null)
    {
        // echo "<pre>";var_dump([$data, $parceiro_id, $produto_parceiro_apolice_multiplo_range_id]);die();

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        $parceiro = $this->parceiro->get($parceiro_id);
        //Verifica se registro existe
        if(!$parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Parceiro Inválido.');
            redirect("parceiros/index");
        }

        $prod_plan = [];
        $produtos_parc = [];
        $apolice_multiplo_range = [];
        $result = $this->produto_parceiro->getProdutosByParceiro( $parceiro_id, null, false );

        if ($data['new_record'] == '0') {
            $produtos_parc = $this->apolice_multiplo->get_by_produto_parceiro_apolice_multiplo_range_id($produto_parceiro_apolice_multiplo_range_id)->get_all();
            $apolice_multiplo_range = $this->apolice_multiplo_range->get($produto_parceiro_apolice_multiplo_range_id);

            $data['row'] = $apolice_multiplo_range;
        }

        if( !empty($result) ) {

            foreach ($result as $r) {

                $r['ok'] = false;

                // verifica se tem permissão
                if ($data['new_record'] == '0') {
                    foreach ($produtos_parc as $pp) {
                        if ($pp['produto_parceiro_id'] == $r['produto_parceiro_id']) {
                            $r['ok'] = true;
                            break;
                        }
                    }
                }

                $prod_plan[] = $r;
            }
        }

        $data['page_title'] = 'Planos/Produtos do Parceiro';
        $data['page_subtitle'] = 'Range de Apólice';
        $data['produtos'] = $prod_plan;
        $data['parceiro_id'] = $parceiro_id;
        $data['produto_parceiro_apolice_multiplo_range_id'] = $produto_parceiro_apolice_multiplo_range_id;
        $data['parceiro'] = $parceiro;

        if($_POST)
        {
            $suc = true;

            if ($this->apolice_multiplo_range->validate_form())
            {
                if ($data['new_record'] == '1')
                {
                    $produto_parceiro_apolice_multiplo_range_id = $this->apolice_multiplo_range->insert_form();
                } else {
                    $this->apolice_multiplo_range->update_form();
                }

                if(!$produto_parceiro_apolice_multiplo_range_id)
                {
                    $suc = false;
                } else {
                    $_POST['produto_parceiro_apolice_multiplo_range_id'] = $produto_parceiro_apolice_multiplo_range_id;
                }
            } else {
                $suc = false;
            }

            if($suc)
            {

                // echo "<pre>";print_r($insert_id);die();
                if ($this->apolice_multiplo->validate_form())
                {

                    // remove os produtos e planos
                    $this->apolice_multiplo->removeAll($produto_parceiro_apolice_multiplo_range_id);

                    // remove os planos já que o produto está setado
                    if (array_key_exists('produto_parceiro_id', $_POST)) {
                        foreach ($_POST['produto_parceiro_id'] as $key => $val) {

                            // insere o registro do produto
                            $dados = [
                                'produto_parceiro_id' => $key, 
                                'produto_parceiro_apolice_multiplo_range_id' => $produto_parceiro_apolice_multiplo_range_id,

                            ];
                            if (!$this->apolice_multiplo->insert($dados)) {
                                $suc = false;
                            }
                        }
                    }

                } else {
                    $suc = false;
                }

            }

            if($suc)
            {
                //Caso inserido com sucesso
                $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
            }
            else
            {
                //Mensagem de erro
                $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.'. validation_errors());
            }

            //Redireciona para index
            redirect("$this->controller_uri/index/". $parceiro_id);

        }

        $this->template->load("admin/layouts/base", "$this->controller_uri/edit", $data);
    }

    public function add($parceiro_id, $produto_parceiro_apolice_multiplo_range_id = null)
    {
        $data['new_record'] = '1';
        $this->exec($data, $parceiro_id, $produto_parceiro_apolice_multiplo_range_id);
    }

    public function edit($parceiro_id, $produto_parceiro_apolice_multiplo_range_id = null)
    {
        $data['new_record'] = '0';
        $this->exec($data, $parceiro_id, $produto_parceiro_apolice_multiplo_range_id);
    }

    public function delete($parceiro_id, $produto_parceiro_apolice_multiplo_range_id)
    {
        $this->apolice_multiplo_range->delete_by(array(
            'produto_parceiro_apolice_multiplo_range_id' => $produto_parceiro_apolice_multiplo_range_id,
            'parceiro_id' => $parceiro_id
        ));

        $this->apolice_multiplo->delete_by(array(
            'produto_parceiro_apolice_multiplo_range_id' => $produto_parceiro_apolice_multiplo_range_id,
        ));

        redirect("$this->controller_uri/index/". $parceiro_id);
    }

}
