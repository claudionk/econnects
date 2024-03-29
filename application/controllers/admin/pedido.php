<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class Pedido extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        //Carrega informações da página
        $this->template->set('page_title', "Pedidos");
        $this->template->set_breadcrumb("Pedidos", base_url("$this->controller_uri/index"));

        //Carrega modelos
        $this->load->model('pedido_model', 'current_model');
    }

    public function index($offset = 0) //Função padrão (load)
    {
        $this->load->model("pedido_status_model", "pedido_status");
        $this->load->model("fatura_status_model", "fatura_status");

        //Carrega bibliotecas
        $this->load->library('pagination');

        //Carrega variáveis de informação para a página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Pedidos");
        $this->template->set_breadcrumb("Pedidos", base_url("$this->controller_uri/index"));
        $data = array();

        //Inicializa tabela
        $config['base_url'] = base_url("$this->controller_uri/index");
        $config['uri_segment'] = 4;
        $config['per_page'] = 10;

        //Carrega dados para a página
        $data['primary_key'] = $this->current_model->primary_key();
        $data['rows'] = $this->current_model
            ->getFileds([
                "pedido.{$data['primary_key']}",
                "pedido.pedido_status_id",
                "pedido.codigo",
                "pedido.criacao",
                "pedido.valor_total",
            ])
            ->with_pedido_status()
            ->with_fatura()
            ->with_cotacao_cliente_contato()
            ->filterPesquisa()
            ->filterNotCarrinho()
            ->limit($config['per_page'], $offset)
            ->order_by('pedido.criacao', 'DESC')
            ->setIndex('pedido', 'idx_deletado_statusid')
            ->get_all(0, 0, false);

        $config['total_rows'] =  count($data['rows']);

        $this->pagination->initialize($config);

        $data['pedido_status_list'] = $this->pedido_status
            ->get_all();

        $data['fatura_status_list'] = $this->fatura_status
            ->get_all();

        $data['inadimplente_list'] = array(
            array('inadimplencia' => 'inadimplente', 'nome' => "Inadimplente"),
        );

        $data["pagination_links"] = $this->pagination->create_links();

        //Carrega template
        $this->template->load("admin/layouts/base", "$this->controller_uri/list", $data );
    }

    public function teste($pedido_id)
    {
        $this->current_model->executa_extorno_upgrade($pedido_id);
    }

    /**
     * Insere um novo cartão
     * @param $id
     */
    public function inserir_cartao($id)
    {
        $this->load->model("pedido_cartao_model", "pedido_cartao");

        //Caso post
        if ($_POST && $id)
        {
            //Valida formulário
            if ($this->pedido_cartao->validate_form())
            {
                $this->pedido_cartao
                    ->where("pedido_id", "=", $id)
                    ->update_all(array(
                        'ativo' => false,
                    ));

                //Insere form
                $update = $this->pedido_cartao->insert_cartao($id, $_POST);

                if ($update)
                {
                    //Caso inserido com sucesso
                    $this->session->set_flashdata('succ_msg', 'Os dados foram salvos corretamente.'); //Mensagem de sucesso
                }
                else
                {
                    //Mensagem de erro
                    $this->session->set_flashdata('fail_msg', 'Não foi possível salvar o Registro.');
                }
            }
        }

        //Redireciona para index
        redirect("$this->controller_uri/view/{$id}");

    }

    public function adicionar_dados_bancarios()
    {
        $result = $this->adicionar_dados_bancarios_core();

        //Caso post
        if($_POST)
        {
        	$this->session->set_flashdata(empty($result['status']) ? 'fail_msg' : 'succ_msg', $result['message']);
        	redirect("$this->controller_uri/view/{$_POST['pedido_id']}");
        }

    }

    public function adicionar_dados_bancarios_core()
    {
        $this->load->model("pedido_dados_bancarios_model", "pedido_dados_bancario");
        $this->load->model("pedido_model", "pedido");

        $return = ['status' => false, 'message' => 'Nenhum dado informado'];

        //Caso post
        if($_POST)
        {
            if(isset($_POST['cpf_cnpj']))
                $_POST['cpf_cnpj'] = app_retorna_numeros($_POST['cpf_cnpj']);

            if($_POST['tipofavorecido'] == 'PF')
            {
                if(!app_validate_cpf($_POST['cpf_cnpj']))
                {
                    //Mensagem de erro
                    $return['message'] = 'CPF - inválido';
                    return $return;
                }
            } 
            if($_POST['tipofavorecido'] == 'PJ'){
                if(!app_validate_cnpj($_POST['cpf_cnpj']))
                {
                    //Mensagem de erro
                    $return['message'] = 'CNPJ - inválido';
                    return $return;
                }
            }

            //Valida formulário
            if( !$this->pedido_dados_bancario->validate_form() )
            {
            	//Mensagem de erro
            	$return['message'] = 'Não foi possível salvar o Registro. Reveja os dados informados';
                return $return;
            }

            // pedido
            $existe = $this->db->query("select pedido_id from pedido_dados_bancarios where pedido_id = ".$_POST['pedido_id']."")->result();

            if(count($existe)>0){
                // Atualiza o campo deletado
                $this->db->query("UPDATE pedido_dados_bancarios SET deletado = 1 WHERE pedido_id = ".$_POST['pedido_id']."");
            }

            // Inserir
            $sql = "INSERT INTO `pedido_dados_bancarios` (`pedido_id`, `conta_pertence`, `tipo_favorecido`, `tipo_conta`, `nome_favorecido`, `cpf_cnpj`, `banco`,`agencia`,`conta`,`digito`,`deletado` ) VALUES ('".$_POST['pedido_id']."','".$_POST['segurado']."','".$_POST['tipofavorecido']."','".$_POST['tipoconta']."','".$_POST['nome']."','".$_POST['cpf_cnpj']."','".$_POST['banco']."','".$_POST['agencia']."','".$_POST['conta']."','".$_POST['digito']."','0');";
            if( !$this->db->query($sql) ){
            	//Mensagem de erro
                $return['message'] = 'Não foi possível salvar o Registro.';
                return $return;
            }

            $arrApolice = [];
            $arrApolice['conta_terceiro'] = $_POST['segurado'];
            $arrApolice['tipo_conta']     = $_POST['tipoconta'];
            $arrApolice['favo_nome']      = $_POST['nome'];
            $arrApolice['favo_doc']       = $_POST['cpf_cnpj'];
            $arrApolice['favo_bco_num']   = $_POST['banco'];
            $arrApolice['favo_bco_ag']    = $_POST['agencia'];
            $arrApolice['favo_bco_cc']    = $_POST['conta'];
            $arrApolice['favo_bco_cc_dg'] = $_POST['digito'];

            $produto_parceiro_cancelamento = $this->pedido->cancelamento( $_POST['pedido_id'], $arrApolice);

            if( isset( $produto_parceiro_cancelamento["result"] ) && $produto_parceiro_cancelamento["result"] == false ) {
                $return['message'] = $produto_parceiro_cancelamento["mensagem"];
            	return $return;
            }

            $return['message'] = 'Apólice cancelada com sucesso.';
            $return['status'] = true;
        	return $return;
        }

        $return['status'] = true; // não é falha quando nao enviar nenhum dado
        return $return;
    }

    /**
     * Visualizar pedido
     * @param $id
     */
    public function view($id, $step = 1)
    {
        $this->load->model('apolice_model', 'apolice');
        $this->load->model('banco_model', 'banco');
        $this->load->model('usuario_acl_permissao_model', 'usuario_acl_permissao');

        //Adicionar Bibliotecas
        $this->load->library('form_validation');

		//Setar variáveis de informação da página
        $this->template->set('page_title_info', '');
        $this->template->set('page_subtitle', "Visualizar pedido");
        $this->template->set_breadcrumb('Editar', base_url("$this->controller_uri/index"));

		//Carrega dados para a página
        $data = array();
        $data['pedido'] = $this->current_model->with_foreign()->get($id);
        $data['produto'] = $this->current_model->getPedidoProdutoParceiro($id);
        $data['produto'] = $data['produto'][0];
    	$data['cancelamento'] = ($this->current_model->isPermiteCancelar($id)) ? '1' : '0';
        $data['cancelamento_permissao'] = !empty($this->usuario_acl_permissao->verify_permission($this->session->userdata('usuario_acl_tipo_id'), 'cancelar', 'pedido'));
        $data['cancelamento_aprovar'] = ($data['pedido']['pedido_status_id'] == 11) ? '1' : '0';
        $data['cancelamento_aprovar_permissao'] = !empty($this->usuario_acl_permissao->verify_permission($this->session->userdata('usuario_acl_tipo_id'), 'cancelar_aprovar', 'pedido'));
        $data['upgrade'] = ($this->current_model->isPermiteUpgrade($id)) ? '1' : '0';
        $data['primary_key'] = $this->current_model->primary_key();
        $data['pedido_id'] = $id;
        $data['new_record'] = '0';
        $data['apolices'] = $this->apolice->getApolicePedido($id);
        $data['apolice_id'] = issetor($data['apolices'][0]['apolice_id'], '');
        $data['produto_parceiro_id'] = $data['produto']['produto_parceiro_id'];
        $data['bancos'] = $this->banco->orderByName()->get_all();
        $data['email'] = issetor($data['apolices'][0]['email'], '');

        // Front a consulta deve ser por 
        if ($this->template->get('layout') == 'front')
        {
	        // 1- login 
	        if(empty($this->session->userdata('logado')))
	        {
	            $this->step_login_cancel([], $id);
	            return;
	        }

	        // 2- calculo do valor de restituicao
	        if ($step == 1)
	        {
	        	$this->load->library('Api');

            	$erros = [];

	            // Passo do calculo da restituição
		        $arrOptions = [
		            "apolice_id" => $data['apolice_id'],
		            "pedido_id"  => $id,
		        ];

		        $r = $this->api->execute(base_url() ."api/apolice/calculoCancelar", 'POST', json_encode($arrOptions), $this->email, null, false);
		        if( empty($r) )
		        {
		         	// TODO: exibir mensagens de erros
		            $erros[] = "Não foi calcular o valor a ser restituído";
		        } else
		        {
			        // pegando o ID da cotação
			        $retorno = convert_objeto_to_array($r);
			        if( empty($retorno->{"status"}) )
			        {
		         		// TODO: exibir mensagens de erros
			            $erros[] = ( !empty($retorno->{"mensagem"}) ) ? $retorno->{"mensagem"} : $r;
			        } else {
			        	if ($_POST)
			        	{
			        		redirect("{$this->controller_uri}/view/$id/2?token={$this->token}&layout={$this->layout}&color={$this->color}");
			        	}

				        $data['inicio_vigencia'] = app_date_mysql_to_mask($retorno->dados[0]->apolices->data_ini_vigencia, 'd/m/Y');
	                    $data['fim_vigencia'] = app_date_mysql_to_mask($retorno->dados[0]->apolices->data_fim_vigencia, 'd/m/Y');
	                    $data['valor_estorno_total'] = app_format_currency($retorno->valor_estorno_total, 2);
	                    $data['dias_utilizados'] = $retorno->dias_utilizados;
			        }
			    }

	            $view = "$this->controller_uri/front/calculo_cancelamento";

	        // 3- termo de cancelamento
	        }elseif ($step == 2)
	        {
	        	$data['solicitacao_desistencia'] = $this->apolice->solicitacao_desistencia($data['apolice_id']);
            	$data["isConfirmaEmail"] = $this->isConfirmaEmail;

            	if ($_POST)
	        	{
	        		// TODO: salvar a data do aceite
	        		redirect("{$this->controller_uri}/view/$id/3?token={$this->token}&layout={$this->layout}&color={$this->color}");
	        	}

			    $view = "$this->controller_uri/front/termo";

	        // 4- informar dados bancarios
	        }elseif ($step == 3)
	        {
	        	if ($_POST)
	        	{
	        		$result = $this->adicionar_dados_bancarios_core();

		        	$this->session->set_flashdata(empty($result['status']) ? 'fail_msg' : 'succ_msg', $result['message']);
		        	
		        	if ( !empty($result['status']) )
		        	{
        				redirect("{$this->controller_uri}/view/$id/4?token={$this->token}&layout={$this->layout}&color={$this->color}");
	        		}

	        		// TODO: preencher os campos quando houver falha ($row)
	        	}

	        	$view = "$this->controller_uri/front/dados_bancarios";

	        // 5- mensagem de sucesso
	        }elseif ($step == 4)
	        {
	        	$view = "$this->controller_uri/front/cancelado";
	        }

	    }else
        {
            $this->load->model("cotacao_model", "cotacao");
            $this->load->model("pedido_transacao_model", "pedido_transacao");
            $this->load->model("pedido_cartao_model", "pedido_cartao");
            $this->load->model('fatura_model', 'fatura');
            $this->load->model('fatura_parcela_model', 'fatura_parcela');
            $this->load->model("produto_parceiro_plano_model", "produto_parceiro_plano");
            $this->load->model("cotacao_seguro_viagem_cobertura_model", "cotacao_seguro_viagem_cobertura");
            $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
            $this->load->model('forma_pagamento_bandeira_model', 'forma_pagamento_bandeira');
            $this->load->model("pedido_cartao_transacao_model", "pedido_cartao_transacao");
            $this->load->model("capitalizacao_model", "capitalizacao");
            $this->load->model('produto_parceiro_cancelamento_model', 'model_producto_parceiro_cancelamento');

            $data['bandeiras'] = $this->forma_pagamento_bandeira->get_all();
            $data['capitalizacoes'] = $this->capitalizacao->get_titulos_pedido($id);
            /*
            $pedido_cartao = $this->pedido_cartao
                ->get_by(array(
                    'pedido_id' => $id
                ));
            //$data['pedido_cartao'] = $this->pedido_cartao->decode_cartao($pedido_cartao);
            */
            $data['cartoes'] = $this->pedido_cartao->get_many_by( array( 'pedido_id' => $id ) ) ;

            foreach ($data['cartoes'] as $index => $cartao)
            {
                $data['cartoes'][$index] = $this->pedido_cartao->decode_cartao($cartao);
                $data['cartoes'][$index]['numero'] = app_format_credit_card($data['cartoes'][$index]['numero']);
                $data['cartoes'][$index]['transacoes'] = $this->pedido_cartao_transacao
                    ->get_many_by(array(
                        'pedido_cartao_id' => $cartao['pedido_cartao_id']
                    ));
            }

            $data['produto_parceiro_configuracao'] = $this->produto_parceiro_configuracao->get_by(array(
                'produto_parceiro_id' => $data['produto']['produto_parceiro_id']
            ));

            //Verifica se registro existe
            if(!$data['pedido'])
            {
                //Mensagem de erro caso registro não exista
                $this->session->set_flashdata('fail_msg', 'Não foi possível encontrar o Registro.');
                //Redireciona para index
                redirect("$this->controller_uri/index");
            }

            if($data['produto']['slug'] == 'seguro_viagem') {
                $data['itens'] = $this->cotacao
                    ->with_cotacao_seguro_viagem()
                    ->with_cotacao_seguro_viagem_plano()
                    ->with_cotacao_seguro_viagem_motivo()
                    ->with_cotacao_seguro_viagem_origem_destino()
                    ->with_cotacao_seguro_viagem_produto()
                    ->with_cotacao_seguro_viagem_moeda()
                    ->filterByID($data['pedido']['cotacao_id'])
                    ->get_all();

                foreach ($data['itens'] as $index => $item) {
                    $data['itens'][$index]['cobertura_adicionais'] = $this->cotacao_seguro_viagem_cobertura->with_cobertura()->get_many_by(array(
                        'cotacao_seguro_viagem_id' => $item['cotacao_seguro_viagem_id']
                    ));
                }
            }elseif ($data['produto']['slug'] == 'equipamento') {
                $data['itens'] = $this->cotacao
                    ->with_cotacao_equipamento()
                    ->with_cotacao_equipamento_produto()
                    ->with_cotacao_equipamento_plano()
                    ->with_cotacao_equipamento_equipamento()
                    ->filterByID($data['pedido']['cotacao_id'])
                    ->get_all();

                foreach ($data['itens'] as $index => $item) {
                    $data['itens'][$index]['cobertura_adicionais'] = array();
                    //   $data['itens'][$index]['cobertura_adicionais'] = $this->cotacao_seguro_viagem_cobertura->with_cobertura()->get_many_by(array(
                    //       'cotacao_seguro_viagem_id' => $item['cotacao_seguro_viagem_id']
                    //   ));
                }
            }elseif ($data['produto']['slug'] == 'generico') {
                $data['itens'] = $this->cotacao
                    ->with_cotacao_generico()
                    ->with_cotacao_generico_produto()
                    ->with_cotacao_generico_plano()
                    ->filterByID($data['pedido']['cotacao_id'])
                    ->get_all();

                foreach ($data['itens'] as $index => $item) {
                    $data['itens'][$index]['cobertura_adicionais'] = array();
                    //   $data['itens'][$index]['cobertura_adicionais'] = $this->cotacao_seguro_viagem_cobertura->with_cobertura()->get_many_by(array(
                    //       'cotacao_seguro_viagem_id' => $item['cotacao_seguro_viagem_id']
                    //   ));
                }
            }

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

            $data['transacoes'] = $this->pedido_transacao
                ->with_foreign()
                ->get_many_by(array(
                    'pedido_id' => $id
                ));

            $data['form_action'] =  base_url("$this->controller_uri/view/{$id}");
            $data['exibe_url_acesso_externo'] = !empty($data['cancelamento']);
            $data['exibe_url_acesso_externo_tipo'] = 'cancelamento';

            if (!empty($data['cancelamento']) )
            {
                $data['url_acesso_externo'] = $this->auth->generate_page_token(
                    ''
                    , $this->urlAcessoExternoAutorizados($data['produto']['slug'], $data['produto']['produto_parceiro_id'])
                    , 'front'
                    , 'cancelamento'
                );
            }

            //Array com dados de config do produto 
            //cancel_via_admin: flag que informa se produto permite cancelamento via admin
            $row = $this->model_producto_parceiro_cancelamento->filter_by_produto_parceiro($data['produto']['produto_parceiro_id'])->get_all();
            $data['product_parceiro_cancelamento'] = $row;

	        $view = "$this->controller_uri/view";
        }

       	//Carrega template
        $this->template->load("admin/layouts/{$this->layout}", $view, $data);
    }

    public function cancelar($pedido_id){
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

    public function cancelar_aprovacao($pedido_id){

        $this->load->model("pedido_transacao_model", "pedido_transacao");

        if($this->current_model->isPermiteCancelar($pedido_id)){
            $this->pedido_transacao->insStatus($pedido_id, 'aprovacao_cancelamento', "AGUARDANDO APROVAÇÃO CANCELAMENTO");
            $this->session->set_flashdata('succ_msg', 'Aguardando Aprovação do cancelamento');
            redirect( base_url("$this->controller_uri/index"));
        }else{
            $this->session->set_flashdata('fail_msg', 'Nao é permitido cancelar esse pedido');
            redirect( base_url("$this->controller_uri/view/{$pedido_id}"));
        }

    }

    public function cancelar_aprovar($pedido_id){

        $this->load->model("pedido_transacao_model", "pedido_transacao");

        $result = $this->current_model->cancelamento($pedido_id);

        if($result['result'] == TRUE) {

            $this->pedido_transacao->insStatus($pedido_id, 'cancelamento_aprovado', "CANCELAMENTO APROVADO");

            $this->session->set_flashdata('succ_msg', $result['mensagem']); //Mensagem de sucesso
            redirect($result['redirect']);

        }
        else
        {
            $msg = (is_array($result['mensagem'])) ? implode("\n", $result['mensagem']) : $result['mensagem'];
            $this->session->set_flashdata('fail_msg', $msg);
            redirect($result['redirect']);
        }

    }

}
