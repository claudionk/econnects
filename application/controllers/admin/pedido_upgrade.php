<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Pedido_Upgrade extends Admin_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        //Carrega informações da página
        $this->template->set('page_title', "Upgrade");
        $this->template->set_breadcrumb("Upgrade", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('pedido_model', 'current_model');
    }
    
    public function index($offset = 0) //Função padrão (load)
    {
        //Carrega bibliotecas
        $this->load->library('pagination');
        
        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Upgrade");
        $this->template->set_breadcrumb("Pedidos", base_url("$this->controller_uri/index"));

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['total_rows'] =  $this->current_model
            ->with_pedido_status()
            ->with_cotacao_cliente_contato()
            ->filter_by_upgrade()
            ->filterPesquisa()
            ->get_total();
        $config['per_page'] = 10;

        $this->pagination->initialize($config);

        //Carrega dados para a página
        $data = array();
        $data['rows'] = $this->current_model
            ->with_pedido_status()
            ->with_cotacao_cliente_contato()
            ->filter_by_upgrade()
            ->filterPesquisa()
            ->limit($config['per_page'], $offset)
            ->order_by('pedido.criacao', 'desc')
            ->get_all();


        $data['primary_key'] = $this->current_model->primary_key();
        $data["pagination_links"] = $this->pagination->create_links();
        
        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    /**
     * Upgrade
     * @param $pedido_id
     */
    public function upgrade($pedido_id)
    {
        $this->load->model("cotacao_model", "cotacao");
        $this->load->model("cotacao_equipamento_model", "cotacao_equipamento");
        $this->load->model("cotacao_seguro_viagem_model", "cotacao_seguro_viagem");
        $this->load->model("cotacao_seguro_viagem_pessoa_model", "cotacao_seguro_viagem_pessoa");
        $this->load->model("produto_parceiro_plano_model", "produto_parceiro_plano");

        if(!$this->current_model->isPermiteUpgrade($pedido_id)){
            $this->session->set_flashdata('fail_msg', 'Não é possível fazer o upgrade deste plano.');
            redirect(admin_url("pedido_upgrade/view/{$pedido_id}"));
        }

        //Retorna dados atuais
        $pedido = $this->current_model->get($pedido_id);
        $cotacao = $this->cotacao->with_cotacao_produto_parceiro_plano()->get($pedido['cotacao_id']);

        $parceiro_plano = $this->produto_parceiro_plano->get($cotacao['produto_parceiro_plano_id']);

        $habilitado = true;
        if($cotacao['produto_slug'] == 'seguro_viagem'){
            $cotacao_seguro_viagem = $this->cotacao_seguro_viagem->get_by(array('cotacao_id' => $cotacao['cotacao_id']));
            $cotacao_seguro_viagem_pessoas = $this->cotacao_seguro_viagem_pessoa->get_many_by(array(
                'cotacao_seguro_viagem_id' => $cotacao_seguro_viagem['cotacao_seguro_viagem_id']
            ));
            if($pedido['pedido_status_id'] != 3 || $cotacao['cotacao_upgrade_id'] || !$parceiro_plano['passivel_upgrade']
                || $cotacao_seguro_viagem['desconto_condicional'] > 0 ){
                $habilitado = false;
            }

        }elseif ($cotacao['produto_slug'] == 'equipamento'){
            $cotacao_equipamento = $this->cotacao_equipamento->get_by(array('cotacao_id' => $cotacao['cotacao_id']));
            if($pedido['pedido_status_id'] != 3 || $cotacao['cotacao_upgrade_id'] || !$parceiro_plano['passivel_upgrade']
                || $cotacao_equipamento['desconto_condicional'] > 0 ){
                $habilitado = false;
            }
        }




        //Cria cópia da nova cotação
        $nova_cotacao = $cotacao;
        unset($nova_cotacao['cotacao_id']);
        unset($nova_cotacao['produto_slug']);
        unset($nova_cotacao['produto_parceiro_plano_id']);
        $nova_cotacao['cotacao_upgrade_id'] = $cotacao['cotacao_id'];
        $nova_cotacao['cotacao_status_id'] = 1;

        if($habilitado)
        {

            //delete todos antes
            //delete_by
            $this->cotacao->delete_by(array('cotacao_upgrade_id' => $cotacao['cotacao_id']));

            if($cotacao_id = $this->cotacao->insert($nova_cotacao)){
                if($cotacao['produto_slug'] == 'seguro_viagem') {
                    $nova_cotacao_seguro_viagem = $cotacao_seguro_viagem;
                    unset($nova_cotacao_seguro_viagem['cotacao_seguro_viagem_id']);
                    $nova_cotacao_seguro_viagem['cotacao_id'] = $cotacao_id;

                    if ($this->cotacao_seguro_viagem->insert($nova_cotacao_seguro_viagem, true)) {
                        redirect(admin_url("venda/seguro_viagem/{$nova_cotacao_seguro_viagem['produto_parceiro_id']}/2/{$cotacao_id}"));
                    }
                }elseif ($cotacao['produto_slug'] == 'equipamento'){
                    $nova_cotacao_equipamento = $cotacao_equipamento;
                    unset($nova_cotacao_equipamento['cotacao_equipamento_id']);
                    $nova_cotacao_equipamento['cotacao_id'] = $cotacao_id;
                    if ($this->cotacao_equipamento->insert($nova_cotacao_equipamento, true)) {
                        redirect(admin_url("venda_equipamento/equipamento/{$nova_cotacao_equipamento['produto_parceiro_id']}/2/{$cotacao_id}"));
                    }
                }
            }
        }
        $this->session->set_flashdata('fail_msg', 'Não é possível fazer o upgrade deste plano.');
        redirect(admin_url("pedido_upgrade/view/{$pedido_id}"));
    }


    /**
     * Visualizar pedido
     * @param $id
     */
    public function view($id)
    {
        $this->load->model("cotacao_model", "cotacao");
        $this->load->model("pedido_transacao_model", "pedido_transacao");
        $this->load->model('fatura_model', 'fatura');
        $this->load->model('fatura_parcela_model', 'fatura_parcela');
        $this->load->model('apolice_model', 'apolice');
        $this->load->model("produto_parceiro_plano_model", "produto_parceiro_plano");

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

        //Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Visualizar pedido");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));
        
        //Carrega dados para a página
        $data = array();
        $data['pedido'] = $this->current_model->with_foreign()->get($id);

        //Verifica se registro existe
        if(!$data['pedido'])
        {
            //Mensagem de erro caso registro não exista
            $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
            //Redireciona para index
            redirect("$this->controller_uri/index");
        }

        //$data['cotacao'] = $this->cotacao->with_foreign()->get($data['pedido']['cotacao_id']);
        $data['itens'] = $this->cotacao
                                ->with_cotacao_seguro_viagem()
                                ->with_cotacao_seguro_viagem_plano()
                                ->with_cotacao_seguro_viagem_motivo()
                                ->with_cotacao_seguro_viagem_origem_destino()
                                ->with_cotacao_seguro_viagem_produto()
                                ->with_cotacao_seguro_viagem_moeda()
                                ->filterByID($data['pedido']['cotacao_id'])
                                ->get_all();


        $data['faturas'] = $this->fatura->filterByPedido($id)
                                        ->with_fatura_status()
                                        ->with_pedido()
                                        ->order_by('data_processamento')
                                        ->get_all();


        foreach ($data['faturas'] as $index => $fatura) {
            $data['faturas'][$index]['parcelas'] = $this->fatura_parcela->with_fatura_status()
                ->filterByFatura($fatura['fatura_id'])
                ->order_by('num_parcela')
                ->get_all();

        }


        $data['apolices'] = $this->apolice->getApolicePedido($id);
    //    print_r($data['fatura'] );exit;

        $data['transacoes'] = $this->pedido_transacao
            ->with_foreign()
            ->get_many_by(array(
            'pedido_id' => $id
        ));


        $data['cancelamento'] = ($this->current_model->isPermiteCancelar($id)) ? '1' : '0';
        $data['upgrade'] = ($this->current_model->isPermiteUpgrade($id)) ? '1' : '0';
        $data['primary_key'] = $this->current_model->primary_key();
        $data['pedido_id'] = $id;
        $data['new_record'] = '0';
        $data['form_action'] =  base_url("$this->controller_uri/view/{$id}");


        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/view", $data );
    }



    public  function cancelar($pedido_id){

        $result = $this->current_model->cancelamento($pedido_id);


        if($result['result'] == TRUE)                {

            $this->session->set_flashdata('succ_msg', $result['mensagem']); //Mensagem de sucesso
            redirect($result['redirect']);

        }
        else
        {

            $this->session->set_flashdata('fail_msg', $result['mensagem']);
            redirect($result['redirect']);
        }


    }


}
