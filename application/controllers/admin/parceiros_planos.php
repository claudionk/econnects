<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parceiros_planos extends Admin_Controller {

    /**
     * @var Acl_Acao
     */
    protected $current_model;

    function __construct()
    {
        parent::__construct();
        $this->load->model('produto_parceiro_model', 'produto_parceiro');
        $this->load->model( "produto_parceiro_plano_model", "produto_parceiro_plano" );
        $this->load->model( "parceiro_plano_model", "parceiro_plano" );
        $this->load->model( "parceiro_produto_model", "parceiro_produto" );
    }

    public function edit($id)
    {
        $this->template->js(app_assets_url('modulos/parceiros_planos/base.js', 'admin'));

        $parceiro = $this->parceiro->get($id);
        //Verifica se registro existe
        if(!$parceiro)
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Parceiro Inválido.');
            redirect("parceiros/index");
        }

        $prod_plan = [];
        $result = $this->produto_parceiro->getProdutosByParceiro( $id, null, false );
        $planos_parc = $this->parceiro_plano->get_by_parceiro($id)->get_all();
        $produtos_parc = $this->parceiro_produto->get_by_parceiro($id)->get_all();

        if( !empty($result) ) {

            foreach ($result as $r) {

                $r['ok'] = false;

                // verifica se tem permissão
                foreach ($produtos_parc as $pp) {
                    if ($pp['produto_parceiro_id'] == $r['produto_parceiro_id']) {
                        $r['ok'] = true;
                        break;
                    }
                }

                $planosProd = $this->produto_parceiro_plano->coreSelectPlanosProdutoParceiro($r['produto_parceiro_id'])->get_all_select();
                $plansProdutos = [];
                foreach ($planosProd as $key => $value) {

                    $value['ok'] = false;

                    // verifica se tem permissão
                    foreach ($planos_parc as $pp) {
                        if ($pp['produto_parceiro_plano_id'] == $value['produto_parceiro_plano_id']) {
                            $value['ok'] = true;
                            break;
                        }
                    }

                    $plansProdutos[$key] = $value;
                }

                $r['planos'] = $plansProdutos;
                $prod_plan[] = $r;
            }
        }

        $data = array();
        $data['page_title'] = 'Planos/Produtos do Parceiro';
        $data['produtos'] = $prod_plan;
        $data['parceiro_id'] = $id;
        $data['parceiro'] = $parceiro;

        if($_POST)
        {
            $suc = true;

            // remove os produtos e planos
            $this->parceiro_plano->removeAll($id);
            $this->parceiro_produto->removeAll($id);

            // remove os planos já que o produto está setado
            if (array_key_exists('produto', $_POST)) {
                foreach ($_POST['produto'] as $key => $val) {
                    if (isset($_POST['plano'][$key]))
                        unset($_POST['plano'][$key]);

                    // insere o registro do produto
                    $dados = [
                        'parceiro_id' => $id,
                        'produto_parceiro_id' => $key
                    ];
                    if (!$this->parceiro_produto->insert($dados))
                        $suc = false;
                }
            }

            if (array_key_exists('plano', $_POST)) {
                foreach ($_POST['plano'] as $p) {
                    foreach ($p as $key => $val) {

                        // insere os planos
                        $dados = [
                            'parceiro_id' => $id,
                            'produto_parceiro_plano_id' => $key
                        ];
                        if (!$this->parceiro_plano->insert($dados))
                            $suc = false;

                    }
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
                $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
            }
            //Redireciona para index
            redirect("admin/parceiros_planos/edit/". $id);

        }

        $this->template->load('admin/layouts/base', 'admin/parceiros_planos/edit', $data );
    }


}
