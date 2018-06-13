    <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Apolice extends Api_Controller
{

    /**
     * Apolice constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('apolice_model', 'apolice');
        $this->load->model('apolice_status_model', 'apolice_status');
        $this->load->model('pedido_model', 'pedido');
        $this->load->model("fatura_model", "fatura");
        $this->load->model("fatura_parcela_model", "fatura_parcela");

        $this->load->helper("api_helper");
    }

    /**
     * Retorna uma apolice
     */
    public function get()
    {
        //Cria resposta
        $response = new Response();

        $params = array();

        $params['apolice_id'] = $this->input->get_post('apolice_id');
        $params['num_apolice'] = $this->input->get_post('num_apolice');
        $params['documento'] = $this->input->get_post('documento');
        $params['pedido_id'] = $this->input->get_post('pedido_id');

        if($params['apolice_id'] || $params['num_apolice'] || $params['documento'] || $params['pedido_id'])
        {
            //Verifica se existe pelo ID ou número da apólice

           $pedidos = $this->pedido
                ->with_pedido_status()
                ->with_cotacao_cliente_contato()
                ->with_apolice()
                ->with_fatura()
                ->filterNotCarrinho()
                ->filterAPI($params)
                ->get_all();

           //print_r($pedidos);exit;

            //Se houver apolice
            if($pedidos)
            {

                foreach ($pedidos as $pedido) {
                    //Monta resposta da apólice
                    $apolice = $this->apolice->getApolicePedido($pedido['pedido_id']);
                    $apolice[0]['inadimplente'] = ($this->pedido->isInadimplente($pedido['pedido_id']) === FALSE) ? 0 : 1;


                    $faturas = $this->fatura->filterByPedido($pedido['pedido_id'])
                        ->with_fatura_status()
                        ->with_pedido()
                        ->order_by('data_processamento')
                        ->get_all();


                    foreach ($faturas as $index => $fatura) {
                        $faturas[$index]['parcelas'] = $this->fatura_parcela->with_fatura_status()
                            ->filterByFatura($fatura['fatura_id'])
                            ->order_by('num_parcela')
                            ->get_all();

                    }


                    $resposta[] = array(
                        'apolice' => api_retira_timestamps($apolice),
                        'faturas' => api_retira_timestamps($faturas),
                        'pedido' => api_retira_timestamps($pedido),
                    );
                }

                $response->setDados($resposta);
                $response->setStatus(true);
            }
            else
            {
                $response->setMensagem("Busca não retornou nenhum resultado.");
                $response->setStatus(false);
            }


        }
        else
        {
            $response->setStatus(false);
            $response->setMensagem("Nehum parâmetro foi informado GET ou POST.");
        }

        echo $response->getJSON();
    }




}

