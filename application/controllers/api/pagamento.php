<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Pagamento
 */
class Pagamento extends CI_Controller
{
    public $api_key;
    public $usuario_id;

    public function __construct()
    {
        parent::__construct();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
        header("Access-Control-Allow-Headers: Cache-Control, APIKEY, apikey, Content-Type");
        header("Content-Type: application/json");

        $method = $_SERVER["REQUEST_METHOD"];
        if ($method == "OPTIONS") {
            die();
        }

        if (isset($_SERVER["HTTP_APIKEY"])) {
            $this->api_key = $_SERVER["HTTP_APIKEY"];
            $this->load->model("usuario_webservice_model", "webservice");

            $webservice = $this->webservice->checkKeyExpiration($this->api_key);
            if (!sizeof($webservice)) {
                die(json_encode(array("status" => false, "message" => "APIKEY is invalid")));
            }
        } else {
            die(json_encode(array("status" => false, "message" => "APIKEY is missing")));
        }
        $this->usuario_id = $webservice["usuario_id"];
        $this->load->database('default');

        $this->load->model("produto_parceiro_model", "current_model");
    }

    public function forma_pagamento_cotacao()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $GET = $_GET;
        } else {
            die(json_encode(array("status" => false, "message" => "Invalid HTTP method")));
        }

        if (!isset($GET["cotacao_id"])) {
            die(json_encode(array("status" => false, "message" => "Campo cotacao_id é obrigatório")));
        }
        $cotacao_id = $GET["cotacao_id"];

        $this->load->model('cotacao_model', 'cotacao');
        $this->load->model('cotacao_generico_model', 'cotacao_generico');
        $this->load->model('cotacao_seguro_viagem_model', 'cotacao_seguro_viagem');
        $this->load->model('cotacao_equipamento_model', 'cotacao_equipamento');
        $this->load->model('forma_pagamento_tipo_model', 'forma_pagamento_tipo');
        $this->load->model('forma_pagamento_bandeira_model', 'forma_pagamento_bandeira');
        $this->load->model('forma_pagamento_model', 'forma_pagamento');
        $this->load->model('produto_parceiro_pagamento_model', 'produto_pagamento');
        $this->load->model('produto_parceiro_configuracao_model', 'produto_parceiro_configuracao');
        $this->load->model('pedido_model', 'pedido');

        $result  = array();
        $cotacao = $this->cotacao->get_by_id($cotacao_id);
        if (empty($cotacao)) {
            die(json_encode(array("status" => false, "message" => "Não foi encontrada a cotacao_id $cotacao_id")));
        }

        $produto_parceiro_id = $cotacao["produto_parceiro_id"];
        $produto             = $this->current_model->with_produto()->get($produto_parceiro_id);

        if ($produto["produto_slug"] == "equipamento") {
            $cotacao_itens = $this->cotacao_equipamento->get_by(array("cotacao_id" => $cotacao_id));
        } else if ($produto["produto_slug"] == "generico" || $produto["produto_slug"] == "seguro_saude") {
            $cotacao_itens = $this->cotacao_generico->get_by(array("cotacao_id" => $cotacao_id));
        }

        $produto_parceiro_plano_id = $cotacao_itens["produto_parceiro_plano_id"];

        $cotacao = $this->cotacao->get_cotacao_produto($cotacao_id);
        switch ($produto["produto_slug"]) {
            case 'seguro_viagem':
                $valor_total = $this->cotacao_seguro_viagem->getValorTotal($cotacao_id);
                break;
            case 'equipamento':
                $valor_total = $this->cotacao_equipamento->getValorTotal($cotacao_id);
                break;
            case 'generico':
            case 'seguro_saude':
                $valor_total = $this->cotacao_generico->getValorTotal($cotacao_id);
                break;
        }

        $forma_pagamento = array();
        $tipo_pagamento  = $this->forma_pagamento_tipo->get_all();

        //Para cada tipo de pagamento
        foreach ($tipo_pagamento as $index => $tipo) {
            $forma = $this->produto_pagamento->with_forma_pagamento()
                ->filter_by_produto_parceiro($produto_parceiro_id)
                ->filter_by_forma_pagamento_tipo($tipo['forma_pagamento_tipo_id'])
                ->filter_by_ativo()
                ->get_all();

            $bandeiras = $this->forma_pagamento_bandeira
                ->get_many_by(array(
                    'forma_pagamento_tipo_id' => $tipo['forma_pagamento_tipo_id'],
                ));

            if (count($forma) > 0) {

                foreach ($forma as $index => $item) {
                    $parcelamento = array();
                    for ($i = 1; $i <= $item['parcelamento_maximo']; $i++) {
                        if ($i <= $item['parcelamento_maximo_sem_juros']) {
                            $parcelamento[$i] = array("Parcelas" => $i, "Valor" => round($valor_total / $i, 2), "Descricao" => "{$i} X " . app_format_currency(round($valor_total / $i)) . " sem juros");
                        } else {
                            //$valor = (( $item['juros_parcela'] / 100 ) * ($valor_total/$i)) + ($valor_total/$i);
                            $valor            = ($valor_total / (1 - ($item['juros_parcela'] / 100))) / $i;
                            $parcelamento[$i] = array("Parcelas" => $i, "Valor" => $valor, "Descricao" => "{$i} X " . app_format_currency($valor) . " com juros (" . app_format_currency($item['juros_parcela']) . "%)");
                        }
                    }
                    $forma[$index]['parcelamento'] = $parcelamento;
                }

                $forma_pagamento[] = array("tipo" => array(
                    "forma_pagamento_tipo_id"       => $tipo["forma_pagamento_tipo_id"],
                    "forma_pagamento_integracao_id" => $tipo["forma_pagamento_integracao_id"],
                    "nome"                          => $tipo["nome"],
                    "slug"                          => $tipo["slug"]),
                    "pagamento"                       => $forma,
                    "bandeiras"                       => $bandeiras);
            }

        }
        if ($forma_pagamento) {
            die(json_encode($forma_pagamento, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            die(json_encode("Não existem formas de pagamento configuradas para a cotacao_id $cotacao_id"));
        }
    }

    public function formas()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $GET = $_GET;
        } else {
            die(json_encode(array("status" => false, "message" => "Invalid HTTP method")));
        }

        $result = $this->db->query("SELECT forma_pagamento_id, forma_pagamento_tipo_id, nome, slug, aceita_parcelamento FROM forma_pagamento WHERE deletado=0 ORDER BY nome")->result_array();
        die(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function campos()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $GET = $_GET;
        } else {
            die(json_encode(array("status" => false, "message" => "Invalid HTTP method")));
        }

        if (!isset($GET["forma_pagamento_id"])) {
            die(json_encode(array("status" => false, "message" => "Campo forma_pagamento_id é obrigatório")));
        }

        $forma_pagamento_id = $GET["forma_pagamento_id"];

        $result = $this->db->query("SELECT forma_pagamento_id, forma_pagamento_tipo_id, nome, slug, aceita_parcelamento FROM forma_pagamento WHERE deletado=0 AND forma_pagamento_id=$forma_pagamento_id ORDER BY nome")->result_array();

        if (sizeof($result) == 0) {
            die(json_encode(array("status" => false, "message" => "Campo forma_pagamento_id informado é inválido")));
        }

        $result                  = $result[0];
        $forma_pagamento_tipo_id = $result["forma_pagamento_tipo_id"];
        $Campos                  = array();

        switch ($forma_pagamento_tipo_id) {
            case $this->config->item("FORMA_PAGAMENTO_CARTAO_CREDITO"):
                $Campos = array(
                    "MerchantOrderId" => "",
                    "Customer"        => array(
                        "Name"       => "",
                        "Email"      => "",
                        "PhoneNumer" => "",
                        "Identity"   => "",
                    ),
                    "Payment"         => array(
                        "Type"           => "CreditCard",
                        "Amount"         => 0,
                        "Installments"   => 1,
                        "Capture"        => "true|false",
                        "SoftDescriptor" => "",
                        "Webhook"        => "",
                        "CreditCard"     => array(
                            "CardNumber"     => "",
                            "Holder"         => "",
                            "ExpirationDate" => "",
                            "SecurityCode"   => "",
                            "Brand"          => "",
                            "SaveCard"       => "true|false",
                        ),
                    ),
                );
                break;

            case $this->config->item("FORMA_PAGAMENTO_CARTAO_DEBITO"):
                $Campos = array(
                    "MerchantOrderId" => "",
                    "Customer"        => array(
                        "Name"       => "",
                        "Email"      => "",
                        "PhoneNumer" => "",
                        "Identity"   => "",
                    ),
                    "Payment"         => array(
                        "Type"           => "DebitCard",
                        "Amount"         => 0,
                        "Installments"   => 1,
                        "Capture"        => "true|false",
                        "SoftDescriptor" => "",
                        "Webhook"        => "",
                        "returnUrl"      => "",
                        "DebitCard"      => array(
                            "CardNumber"     => "",
                            "Holder"         => "",
                            "ExpirationDate" => "",
                            "SecurityCode"   => "",
                            "Brand"          => "",
                            "SaveCard"       => "true|false",
                        ),
                    ),
                );
                break;

            case $this->config->item("FORMA_PAGAMENTO_BOLETO"):
                $Campos = array(
                    "MerchantOrderId" => "",
                    "Customer"        => array(
                        "Name"         => "",
                        "Email"        => "",
                        "Identity"     => "",
                        "IdentityType" => "CPF|CNPJ",
                        "Address"      => array(
                            "Street"     => "",
                            "Number"     => "",
                            "Complement" => "",
                            "District"   => "",
                            "ZipCode"    => "",
                            "City"       => "",
                            "State"      => "",
                        ),
                    ),
                    "Payment"         => array(
                        "Type"           => "Boleto",
                        "Amount"         => 0,
                        "BoletoNumber"   => "",
                        "ExpirationDate" => "",
                        "Identification" => "",
                        "Instructions"   => "",
                    ),
                );
                break;

            case $this->config->item("FORMA_PAGAMENTO_TRANSF_BRADESCO"):
                $Campos = array(
                    "MerchantOrderId" => "",
                    "Payment"         => array(
                        "Type"     => "EletronicTransfer",
                        "Provider" => "BRADESCO",
                        "Amount"   => 0,
                        "Webhook"  => "",
                    ),
                );
                break;

            case $this->config->item("FORMA_PAGAMENTO_TRANSF_BB"):
                $Campos = array(
                    "MerchantOrderId" => "",
                    "Payment"         => array(
                        "Type"     => "EletronicTransfer",
                        "Provider" => "BB",
                        "Amount"   => 0,
                        "Webhook"  => "",
                    ),
                );
                break;

            case $this->config->item("FORMA_PAGAMENTO_CHECKOUT_PAGMAX"):
                $Campos = array(
                    "Customer"     => array(
                        "Name"           => "",
                        "Identification" => "",
                        "Email"          => "",
                        "PhoneNumber"    => "",
                    ),
                    "Sale"         => array(
                        "Reference" => "",
                        "Amount"    => 0,
                    ),
                    "Transaction"  => array(
                        "MerchantOrderID"        => "",
                        "MerchantSoftDescriptor" => "",
                    ),
                    "Acceleration" => array(
                        "NPS" => array(
                            "Enabled"    => "true|false",
                            "CampaignID" => "",
                        ),
                        "CAP" => array(
                            "Enabled" => "true|false",
                            "SerieID" => 0,
                        ),
                    ),
                    "Notification" => array(
                        "Enabled" => "true|false",
                        "Method"  => "SMS|EMAIL",
                    ),
                );
                break;

            case $this->config->item("FORMA_PAGAMENTO_FATURADO"):
            case $this->config->item("FORMA_PAGAMENTO_TERCEIROS"):
                $Campos = array();
                break;

            default:
                $Campos = array();
                break;
        }

        die(json_encode(
            array(
                "forma_pagamento_id"      => $result["forma_pagamento_id"],
                "forma_pagamento_tipo_id" => $result["forma_pagamento_tipo_id"],
                "nome"                    => $result["nome"],
                "aceita_parcelamento"     => $result["aceita_parcelamento"],
                "slug"                    => $result["slug"],
                "campos"                  => $Campos),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function pagar()
    {
        //$produto_parceiro_id = null, $cotacao_id = null, $pedido_id = 0, $forma_pagamento_tipo_id = null
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $POST = json_decode(file_get_contents("php://input"), true);
        } else {
            die(json_encode(array("status" => false, "message" => "Invalid HTTP method")));
        }

        if (!isset($POST["cotacao_id"])) {
            die(json_encode(array("status" => false, "message" => "Campo cotacao_id é obrigatório")));
        }
        $cotacao_id = $POST["cotacao_id"];

        if (!isset($POST["produto_parceiro_id"])) {
            die(json_encode(array("status" => false, "message" => "Campo produto_parceiro_id é obrigatório")));
        }
        $produto_parceiro_id = $POST["produto_parceiro_id"];

        if (!isset($POST["forma_pagamento_id"])) {
            die(json_encode(array("status" => false, "message" => "Campo forma_pagamento_id é obrigatório")));
        }
        $forma_pagamento_id = $POST["forma_pagamento_id"];

        if (!isset($POST["produto_parceiro_pagamento_id"])) {
            die(json_encode(array("status" => false, "message" => "Campo produto_parceiro_pagamento_id é obrigatório")));
        }
        $produto_parceiro_pagamento_id = $POST["produto_parceiro_pagamento_id"];

        $forma_pagamento = $this->db->query("SELECT forma_pagamento_id, forma_pagamento_tipo_id, nome, slug, aceita_parcelamento FROM forma_pagamento WHERE deletado=0 AND forma_pagamento_id=$forma_pagamento_id ORDER BY nome")->result_array();

        if (sizeof($forma_pagamento) == 0) {
            die(json_encode(array("status" => false, "message" => "Campo forma_pagamento_id informado é inválido")));
        }

        $forma_pagamento         = $forma_pagamento[0];
        $forma_pagamento_tipo_id = $forma_pagamento["forma_pagamento_tipo_id"];

        if (!isset($POST["campos"])) {
            //die( json_encode( array( "status" => false, "message" => "Campo campos é obrigatório" ) ) );
            $Campos = [];
        } else {
            $Campos = $POST["campos"];
        }

        if (!isset($POST["pedido_id"])) {
            $pedido_id = 0;
        }

        $this->load->model("cotacao_model", "cotacao");
        $this->load->model("cotacao_generico_model", "cotacao_generico");
        $this->load->model("cotacao_seguro_viagem_model", "cotacao_seguro_viagem");
        $this->load->model("cotacao_equipamento_model", "cotacao_equipamento");
        $this->load->model("forma_pagamento_tipo_model", "forma_pagamento_tipo");
        $this->load->model("forma_pagamento_bandeira_model", "forma_pagamento_bandeira");
        $this->load->model("forma_pagamento_model", "forma_pagamento");
        $this->load->model("produto_parceiro_pagamento_model", "parceiro_pagamento");
        $this->load->model("produto_parceiro_configuracao_model", "produto_parceiro_configuracao");
        $this->load->model("pedido_model", "pedido");

        $cotacao = $this->cotacao->get_cotacao_produto($cotacao_id);
        if (empty($cotacao)) {
            die(json_encode(array("status" => false, "message" => "Não foi encontrada a cotacao_id $cotacao_id")));
        }

        switch ($cotacao["produto_slug"]) {
            case "seguro_viagem":
                $valor_total = $this->cotacao_seguro_viagem->getValorTotal($cotacao_id);
                break;
            case "equipamento":
                $valor_total = $this->cotacao_equipamento->getValorTotal($cotacao_id);
                if (!$this->db->query("SELECT cotacao_equipamento_id FROM cotacao_equipamento WHERE cotacao_id=$cotacao_id AND step=4")->result_array()) {
                    die(json_encode(array("status" => false, "message" => "Não é possível efetuar o pagamento. É necessário confirmar a contratação da apólice antes de efetuar seu pagamento.")));
                }
                break;
            default:
                $valor_total = $this->cotacao_generico->getValorTotal($cotacao_id);
                break;
        }

        //die( json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

        $erros           = [];
        $nome_cartao     = ""; //$scope.dadosPagamento.Holder,
        $bandeira_cartao = ""; //$scope.dadosPagamento.Brand,
        $numero          = ""; //$scope.dadosPagamento.CardNumber,
        $validade        = ""; //$scope.dadosPagamento.ExpirationDate,
        $codigo          = ""; //$scope.dadosPagamento.SecurityCode,
        $num_parcela     = ""; //$scope.dadosPagamento.Installments,

        switch ($forma_pagamento_tipo_id) {
            case $this->config->item("FORMA_PAGAMENTO_CARTAO_CREDITO"):
                $Matriz = array(
                    "MerchantOrderId" => "",
                    "Customer"        => array(
                        "Name"       => "",
                        "Email"      => "",
                        "PhoneNumer" => "",
                        "Identity"   => "",
                    ),
                    "Payment"         => array(
                        "Type"           => "CreditCard",
                        "Amount"         => 0,
                        "Installments"   => 1,
                        "Capture"        => "true|false",
                        "SoftDescriptor" => "",
                        "Webhook"        => "",
                        "CreditCard"     => array(
                            "CardNumber"     => "",
                            "Holder"         => "",
                            "ExpirationDate" => "",
                            "SecurityCode"   => "",
                            "Brand"          => "",
                            "SaveCard"       => "true|false",
                        ),
                    ),
                );
                if (!isset($Campos["MerchantOrderId"])) {
                    $erros[] = "MerchantOrderId";
                }

                if (isset($Campos["Payment"])) {
                    $comparison = array_diff_key($Matriz["Payment"], $Campos["Payment"]);
                    if ($comparison) {
                        $erros["Payment"] = $comparison;
                    } else {
                        $num_parcela = $Campos["Payment"]["Installments"];
                    }

                    if (isset($Campos["Payment"]["CreditCard"])) {
                        $comparison = array_diff_key($Matriz["Payment"]["CreditCard"], $Campos["Payment"]["CreditCard"]);
                        if ($comparison) {
                            $erros["Payment"]["CreditCard"] = $comparison;
                        } else {
                            $nome_cartao     = $Campos["Payment"]["CreditCard"]["Holder"];
                            $bandeira_cartao = $Campos["Payment"]["CreditCard"]["Brand"];
                            $numero          = $Campos["Payment"]["CreditCard"]["CardNumber"];
                            $validade        = $Campos["Payment"]["CreditCard"]["ExpirationDate"];
                            $codigo          = $Campos["Payment"]["CreditCard"]["SecurityCode"];
                        }
                    } else {
                        $erros["Payment"]["CreditCard"] = $Matriz["Payment"]["CreditCard"];
                    }
                } else {
                    $erros["Payment"] = $Matriz["Payment"];
                }
                break;

            case $this->config->item("FORMA_PAGAMENTO_CARTAO_DEBITO"):
                $Matriz = array(
                    "MerchantOrderId" => "",
                    "Customer"        => array(
                        "Name"       => "",
                        "Email"      => "",
                        "PhoneNumer" => "",
                        "Identity"   => "",
                    ),
                    "Payment"         => array(
                        "Type"           => "DebitCard",
                        "Amount"         => 0,
                        "Installments"   => 1,
                        "Capture"        => "true|false",
                        "SoftDescriptor" => "",
                        "Webhook"        => "",
                        "DebitCard"      => array(
                            "CardNumber"     => "",
                            "Holder"         => "",
                            "ExpirationDate" => "",
                            "SecurityCode"   => "",
                            "Brand"          => "",
                            "SaveCard"       => "true|false",
                        ),
                    ),
                );
                if (!isset($Campos["MerchantOrderId"])) {
                    $erros[] = "MerchantOrderId";
                }

                if (isset($Campos["Payment"])) {
                    $comparison = array_diff_key($Matriz["Payment"], $Campos["Payment"]);
                    if ($comparison) {
                        $erros["Payment"] = $comparison;
                    } else {
                        $num_parcela = $Campos["Payment"]["Installments"];
                    }

                    if (isset($Campos["Payment"]["DebitCard"])) {
                        $comparison = array_diff_key($Matriz["Payment"]["DebitCard"], $Campos["Payment"]["DebitCard"]);
                        if ($comparison) {
                            $erros["Payment"]["DebitCard"] = $comparison;
                        } else {
                            $nome_cartao     = $Campos["Payment"]["DebitCard"]["Holder"];
                            $bandeira_cartao = $Campos["Payment"]["DebitCard"]["Brand"];
                            $numero          = $Campos["Payment"]["DebitCard"]["CardNumber"];
                            $validade        = $Campos["Payment"]["DebitCard"]["ExpirationDate"];
                            $codigo          = $Campos["Payment"]["DebitCard"]["SecurityCode"];
                        }
                    } else {
                        $erros["Payment"]["DebitCard"] = $Matriz["Payment"]["DebitCard"];
                    }
                } else {
                    $erros["Payment"] = $Matriz["Payment"];
                }
                break;

            case $this->config->item("FORMA_PAGAMENTO_BOLETO"):
                $Matriz = array(
                    "MerchantOrderId" => "",
                    "Customer"        => array(
                        "Name"         => "",
                        "Email"        => "",
                        "Identity"     => "",
                        "IdentityType" => "CPF|CNPJ",
                        "Address"      => array(
                            "Street"     => "",
                            "Number"     => "",
                            "Complement" => "",
                            "District"   => "",
                            "ZipCode"    => "",
                            "City"       => "",
                            "State"      => "",
                        ),
                    ),
                    "Payment"         => array(
                        "Type"           => "Boleto",
                        "Amount"         => 0,
                        "BoletoNumber"   => "",
                        "ExpirationDate" => "",
                        "Identification" => "",
                        "Instructions"   => "",
                    ),
                );
                if (!isset($Campos["MerchantOrderId"])) {
                    $erros[] = "MerchantOrderId";
                }

                if (isset($Campos["Customer"])) {
                    $comparison = array_diff_key($Matriz["Customer"], $Campos["Customer"]);
                    if ($comparison) {
                        $erros["Customer"] = $comparison;
                    }
                    if (isset($Campos["Customer"]["Address"])) {
                        $comparison = array_diff_key($Matriz["Customer"]["Address"], $Campos["Customer"]["Address"]);
                        if ($comparison) {
                            $erros["Customer"]["Address"] = $comparison;
                        }
                    } else {
                        $erros["Customer"]["Address"] = $Matriz["Customer"]["Address"];
                    }
                } else {
                    $erros["Customer"] = $Matriz["Customer"];
                }

                if (isset($Campos["Payment"])) {
                    $comparison = array_diff_key($Matriz["Payment"], $Campos["Payment"]);
                    if ($comparison) {
                        $erros["Payment"] = $comparison;
                    } else {
                        $num_parcela = 1;
                    }
                } else {
                    $erros["Payment"] = $Matriz["Payment"];
                }
                break;

            case $this->config->item("FORMA_PAGAMENTO_TRANSF_BRADESCO"):
                $Matriz = array(
                    "MerchantOrderId" => "",
                    "Payment"         => array(
                        "Type"     => "EletronicTransfer",
                        "Provider" => "BRADESCO",
                        "Amount"   => 0,
                        "Webhook"  => "",
                    ),
                );
                if (!isset($Campos["MerchantOrderId"])) {
                    $erros[] = "MerchantOrderId";
                }

                if (isset($Campos["Payment"])) {
                    $comparison = array_diff_key($Matriz["Payment"], $Campos["Payment"]);
                    if ($comparison) {
                        $erros["Payment"] = $comparison;
                    } else {
                        $num_parcela = 1;
                    }
                } else {
                    $erros["Payment"] = $Matriz["Payment"];
                }
                break;

            case $this->config->item("FORMA_PAGAMENTO_TRANSF_BB"):
                $Matriz = array(
                    "MerchantOrderId" => "",
                    "Payment"         => array(
                        "Type"     => "EletronicTransfer",
                        "Provider" => "BB",
                        "Amount"   => 0,
                        "Webhook"  => "",
                    ),
                );
                if (!isset($Campos["MerchantOrderId"])) {
                    $erros[] = "MerchantOrderId";
                }

                if (isset($Campos["Payment"])) {
                    $comparison = array_diff_key($Matriz["Payment"], $Campos["Payment"]);
                    if ($comparison) {
                        $erros["Payment"] = $comparison;
                    } else {
                        $num_parcela = 1;
                    }
                } else {
                    $erros["Payment"] = $Matriz["Payment"];
                }
                break;

            case $this->config->item("FORMA_PAGAMENTO_CHECKOUT_PAGMAX"):
                $Matriz = array(
                    "Customer"     => array(
                        "Name"           => "",
                        "Identification" => "",
                        "Email"          => "",
                        "PhoneNumber"    => "",
                    ),
                    "Sale"         => array(
                        "Reference" => "",
                        "Amount"    => 0,
                    ),
                    "Transaction"  => array(
                        "MerchantOrderID"        => "",
                        "MerchantSoftDescriptor" => "",
                    ),
                    "Acceleration" => array(
                        "NPS" => array(
                            "Enabled"    => "true|false",
                            "CampaignID" => "",
                        ),
                        "CAP" => array(
                            "Enabled" => "true|false",
                            "SerieID" => 0,
                        ),
                    ),
                    "Notification" => array(
                        "Enabled" => "true|false",
                        "Method"  => "SMS|EMAIL",
                    ),
                );

                if (isset($Campos["Customer"])) {
                    $comparison = array_diff_key($Matriz["Customer"], $Campos["Customer"]);
                    if ($comparison) {
                        $erros["Customer"] = $comparison;
                    }
                } else {
                    $erros["Customer"] = $Matriz["Customer"];
                }

                if (isset($Campos["Sale"])) {
                    $comparison = array_diff_key($Matriz["Sale"], $Campos["Sale"]);
                    if ($comparison) {
                        $erros["Sale"] = $comparison;
                    }
                } else {
                    $erros["Sale"] = $Matriz["Sale"];
                }

                if (isset($Campos["Transaction"])) {
                    $comparison = array_diff_key($Matriz["Transaction"], $Campos["Transaction"]);
                    if ($comparison) {
                        $erros["Transaction"] = $comparison;
                    }
                } else {
                    $erros["Transaction"] = $Matriz["Transaction"];
                }

                if (isset($Campos["Notification"])) {
                    $comparison = array_diff_key($Matriz["Notification"], $Campos["Notification"]);
                    if ($comparison) {
                        $erros["Notification"] = $comparison;
                    }
                } else {
                    $erros["Notification"] = $Matriz["Notification"];
                }
                break;

            case $this->config->item("FORMA_PAGAMENTO_FATURADO"):
            case $this->config->item("FORMA_PAGAMENTO_TERCEIROS"):
                $Matriz = array();
                break;

            default:
                $Campos = array();
                break;
        }

        if (sizeof($erros)) {
            die(
                json_encode(
                    array(
                        "status"              => false,
                        "cotacao_id"          => $cotacao_id,
                        "produto_parceiro_id" => $produto_parceiro_id,
                        "forma_pagamento_id"  => $forma_pagamento_id,
                        "nome"                => $forma_pagamento["nome"],
                        "erros"               => $erros,
                    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                )
            );
        } else {

            $data                                  = array();
            $data["cotacao_id"]                    = $cotacao_id;
            $data["pedido_id"]                     = $pedido_id;
            $data["produto_slug"]                  = $cotacao["produto_slug"];
            $data["produto_parceiro_configuracao"] = $this->produto_parceiro_configuracao->get_by(array("produto_parceiro_id" => $produto_parceiro_id));
            $data["produto_parceiro_id"]           = $produto_parceiro_id;

            if ($forma_pagamento_tipo_id == $this->config->item("FORMA_PAGAMENTO_CARTAO_CREDITO") || $forma_pagamento_tipo_id == $this->config->item("FORMA_PAGAMENTO_CARTAO_DEBITO")) {
                $pedido_data                            = array();
                $pedido_data["cotacao_id"]              = $cotacao_id;
                $pedido_data["produto_parceiro_id"]     = $produto_parceiro_id;
                $pedido_data["pedido_id"]               = $pedido_id;
                $pedido_data["forma_pagamento_id"]      = $forma_pagamento_id;
                $pedido_data["forma_pagamento_tipo_id"] = $forma_pagamento_tipo_id;
                $pedido_data["nome_cartao"]             = $nome_cartao;
                $pedido_data["num_parcela"]             = $num_parcela;
                $pedido_data["bandeira_cartao"]         = $bandeira_cartao;
                $pedido_data["numero"]                  = $numero;
                $pedido_data["validade"]                = $validade;
                $pedido_data["codigo"]                  = $codigo;
                $pedido_data["bandeira"]                = $produto_parceiro_pagamento_id;

                // valida recorrência
                $Campos = $this->parceiro_pagamento->getRecurrent($forma_pagamento_tipo_id, $produto_parceiro_pagamento_id, $Campos);
            }

            if ($forma_pagamento_tipo_id == $this->config->item("FORMA_PAGAMENTO_BOLETO")) {
                $pedido_data                            = array();
                $pedido_data["cotacao_id"]              = $cotacao_id;
                $pedido_data["produto_parceiro_id"]     = $produto_parceiro_id;
                $pedido_data["pedido_id"]               = $pedido_id;
                $pedido_data["forma_pagamento_id"]      = $forma_pagamento_id;
                $pedido_data["forma_pagamento_tipo_id"] = $forma_pagamento_tipo_id;
                $pedido_data["sacado_nome"]             = $Campos["Customer"]["Name"];
                $pedido_data["sacado_documento"]        = $Campos["Customer"]["Identity"];
                $pedido_data["sacado_endereco"]         = $Campos["Customer"]["Address"]["Street"];
                $pedido_data["sacado_endereco_num"]     = $Campos["Customer"]["Address"]["Number"];
                $pedido_data["sacado_endereco_comp"]    = $Campos["Customer"]["Address"]["Complement"];
                $pedido_data["sacado_endereco_cep"]     = $Campos["Customer"]["Address"]["ZipCode"];
                $pedido_data["sacado_endereco_bairro"]  = $Campos["Customer"]["Address"]["District"];
                $pedido_data["sacado_endereco_cidade"]  = $Campos["Customer"]["Address"]["City"];
                $pedido_data["sacado_endereco_uf"]      = $Campos["Customer"]["Address"]["State"];
                $pedido_data["sacado_endereco_pais"]    = "BRA";
                $pedido_data["banco"]                   = "PAGMAX";
                $pedido_data["nosso_numero"]            = $Campos["Payment"]["BoletoNumber"];
                $pedido_data["emissao"]                 = date("Y-m-d H:i:s");
                $pedido_data["vencimento"]              = $Campos["Payment"]["ExpirationDate"];
                $pedido_data["valor"]                   = $Campos["Payment"]["Amount"];
                $pedido_data["instrucoes"]              = $Campos["Payment"]["Instructions"];
                $pedido_data["num_parcela"]             = $num_parcela;
                $pedido_data["bandeira"]                = $produto_parceiro_pagamento_id;
                $Campos["Payment"]["Address"]           = "Alameda Rio Negro, 500 - 6° andar - Alphaville - Barueri - São Paulo - CEP 06454-000";
                $Campos["Payment"]["Provider"]          = "Simulado";
                $Campos["Payment"]["Identification"]    = "08.267.567/0001-30";
                $Campos["Payment"]["Instructions"]      = "Aceitar somente ate a data de vencimento, apos essa data juros de 1% dia.";
            }
            //die( json_encode( $pedido_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

            if ($forma_pagamento_tipo_id == $this->config->item("FORMA_PAGAMENTO_FATURADO") || $forma_pagamento_tipo_id == $this->config->item("FORMA_PAGAMENTO_TERCEIROS")) {
                $pedido_data                            = array();
                $pedido_data["cotacao_id"]              = $cotacao_id;
                $pedido_data["produto_parceiro_id"]     = $produto_parceiro_id;
                $pedido_data["pedido_id"]               = $pedido_id;
                $pedido_data["forma_pagamento_id"]      = $forma_pagamento_id;
                $pedido_data["forma_pagamento_tipo_id"] = $forma_pagamento_tipo_id;
                $pedido_data["num_parcela"]             = 1;
                $pedido_data["bandeira"]                = $produto_parceiro_pagamento_id;
            }

            if ($pedido_id == 0 || $pedido_id == "") {
                $result = $this->db->query("SELECT * FROM pedido WHERE cotacao_id=$cotacao_id")->result_array();
                if (sizeof($result) > 0) {
                    die(
                        json_encode(
                            array(
                                "status"              => false,
                                "cotacao_id"          => $cotacao_id,
                                "produto_parceiro_id" => $produto_parceiro_id,
                                "forma_pagamento_id"  => $forma_pagamento_id,
                                "nome"                => $forma_pagamento["nome"],
                                "erros"               => "Pedido já existente para essa cotação",
                            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                        )
                    );
                }
                $pedido_id = $this->pedido->insertPedido($pedido_data);
                if (is_array($pedido_id)) {
                    die(
                        json_encode(
                            array(
                                "status"              => false,
                                "cotacao_id"          => $cotacao_id,
                                "produto_parceiro_id" => $produto_parceiro_id,
                                "forma_pagamento_id"  => $forma_pagamento_id,
                                "nome"                => $forma_pagamento["nome"],
                                "erros"               => $pedido_id["erros"],
                            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                        )
                    );
                }
            } else {
                $this->pedido->updatePedido($pedido_id, $pedido_data);
            }

            $pedido = $this->db->query("SELECT * FROM pedido WHERE cotacao_id=$cotacao_id AND deletado=0")->result_array();
            if ($pedido) {
                $pedido_id = $pedido[0]["pedido_id"];
            } else {
                die(
                    json_encode(
                        array(
                            "success"             => false,
                            "cotacao_id"          => $dados["cotacao_id"],
                            "produto_parceiro_id" => $dados["produto_parceiro_id"],
                            "forma_pagamento_id"  => $dados["forma_pagamento_id"],
                            "erros"               => "Falha na criação do pedido",
                        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    )
                );
            }

            if ($pedido_id && $forma_pagamento_tipo_id == $this->config->item("FORMA_PAGAMENTO_FATURADO") || $forma_pagamento_tipo_id == $this->config->item("FORMA_PAGAMENTO_TERCEIROS")) {
                $status = $this->pedido->mudaStatus($pedido_id, "pagamento_confirmado");
                $this->load->model('apolice_model', 'apolice');
                $this->apolice->insertApolice($pedido_id);

                $apolice = $this->apolice->get_by("pedido_id", $pedido_id);
                $apolice = $this->db->query("SELECT * FROM apolice WHERE pedido_id=$pedido_id AND deletado=0")->result_array();
                if (sizeof($apolice)) {
                    $apolice = $apolice[0];
                }
                if ($apolice) {
                    $result = array(
                        "status"   => true,
                        "mensagem" => "Pedido confirmado",
                        "dados"    => array("pedido_id" => $pedido_id, "apolice_id" => $apolice["apolice_id"], "num_apolice" => $apolice["num_apolice"]),
                    );
                } else {
                    $result = array(
                        "status"              => false,
                        "cotacao_id"          => $cotacao_id,
                        "produto_parceiro_id" => $produto_parceiro_id,
                        "forma_pagamento_id"  => $forma_pagamento_id,
                        "nome"                => $forma_pagamento["nome"],
                        "erros"               => "Não foi possível criar a apólice",
                    );
                    die(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }

                die(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }

            $faturas = $this->fatura->filterByPedido($pedido_id)->get_all();
            if ($faturas) {
                $faturas        = $faturas[0];
                $fatura_id      = $faturas["fatura_id"];
                $fatura_parcela = $this->fatura_parcela->filterByFatura($fatura_id)->get_all();
                if ($fatura_parcela) {
                    $fatura_parcela = $fatura_parcela[0];
                    $fatura_parcela["fatura_parcela_id"];
                }
                if ($forma_pagamento_tipo_id == $this->config->item("FORMA_PAGAMENTO_CHECKOUT_PAGMAX")) {
                    if ($Campos["Transaction"]["MerchantOrderID"] == "") {
                        $Campos["Transaction"]["MerchantOrderID"] = $fatura_parcela["fatura_parcela_id"];
                        $Campos["Sale"]["Amount"]                 = $faturas["valor_total"];
                    }
                } else {
                    if ($Campos["MerchantOrderId"] == "") {
                        $Campos["MerchantOrderId"] = $fatura_parcela["fatura_parcela_id"];
                    }
                    $Campos["Payment"]["Installments"] = $faturas["num_parcela"];
                    $Campos["Payment"]["Amount"]       = $faturas["valor_total"];
                }
            }

            $this->load->library("Pagmax360");
            $Pagmax360              = new Pagmax360();
            $Pagmax360->merchantId  = $this->config->item("Pagmax360_merchantId");
            $Pagmax360->merchantKey = $this->config->item("Pagmax360_merchantKey");
            $Pagmax360->Environment = $this->config->item("Pagmax360_Environment");

            $Json = json_encode($Campos);

            $Response = $Pagmax360->createTransaction($Pagmax360->merchantId, $Pagmax360->merchantKey, $Json, $Pagmax360->Environment, $pedido_id);
            $Response = json_decode($Response, true);

            $result = [];
            //die( json_encode( $Response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
            if (isset($Response["Payment"]) && isset($Response["Payment"]["Status"]) && isset($Response["Payment"]["Status"]["Code"])) {
                if ($Response["Payment"]["Status"]["Code"] == "0") {
                    $status = $this->pedido->mudaStatus($pedido_id, "criado");
                    $result = array(
                        "status"   => true,
                        "mensagem" => "Transação iniciada",
                        "dados"    => array("pedido_id" => $pedido_id),
                        "pagmax"   => $Response,
                    );
                    if ($Campos["Payment"]["Type"] == "DebitCard" && isset($Response["Payment"]["AuthenticationUrl"])) {
                        $result["link_de_autenticacao"] = $Response["Payment"]["AuthenticationUrl"];
                    }
                    if ($Campos["Payment"]["Type"] == "Boleto" && isset($Response["Payment"]["Url"])) {
                        $result["link_do_boleto"] = $Response["Payment"]["Url"];
                    }
                    if ($Campos["Payment"]["Type"] == "EletronicTransfer" && isset($Response["Payment"]["Url"])) {
                        $result["link_de_autenticacao"] = $Response["Payment"]["Url"];
                    }
                }
                if ($Response["Payment"]["Status"]["Code"] == "1") {
                    $status = $this->pedido->mudaStatus($pedido_id, "aguardando_pagamento");
                    $result = array(
                        "status"   => true,
                        "mensagem" => "Aguardando pagamento",
                        "dados"    => array("pedido_id" => $pedido_id),
                        "pagmax"   => $Response,
                    );
                    if ($Campos["Payment"]["Type"] == "DebitCard" && isset($Response["Payment"]["AuthenticationUrl"])) {
                        $result["link_de_autenticacao"] = $Response["Payment"]["AuthenticationUrl"];
                    }
                    if ($Campos["Payment"]["Type"] == "Boleto" && isset($Response["Payment"]["Url"])) {
                        $result["link_do_boleto"] = $Response["Payment"]["Url"];
                    }
                    if ($Campos["Payment"]["Type"] == "EletronicTransfer" && isset($Response["Payment"]["Url"])) {
                        $result["link_de_autenticacao"] = $Response["Payment"]["Url"];
                    }
                }
                if ($Response["Payment"]["Status"]["Code"] == "2") {
                    $status = $this->pedido->mudaStatus($pedido_id, "pagamento_confirmado");
                    $result = array(
                        "status"   => true,
                        "mensagem" => "Pagamento efetuado com sucesso",
                        "dados"    => array("pedido_id" => $pedido_id),
                        "pagmax"   => $Response,
                    );
                }
                if ($Response["Payment"]["Status"]["Code"] == "3") {
                    $status = $this->pedido->mudaStatus($pedido_id, "pagamento_negado");
                    $result = array(
                        "status"   => false,
                        "mensagem" => "Autorização negada (" . $Response["Payment"]["ReturnMessage"] . ")",
                        "dados"    => array("pedido_id" => $pedido_id),
                        "pagmax"   => $Response,
                    );
                }
            } else {
                if (sizeof($Response) == 0 || is_null($Response) || is_null(json_encode($Response))) {
                    $result = array(
                        "status"   => false,
                        "mensagem" => "Falha na transacao (timeout com emissor)",
                        "dados"    => array("pedido_id" => $pedido_id),
                    );
                } else {
                    if (isset($Response["error"]) && isset($Response["error"]["Code"])) {
                        $result = array(
                            "status"   => false,
                            "mensagem" => issetor( $Response["error"]["Message"], "Falha na transacao") ." (Erro " . $Response["error"]["Code"] . ")",
                            "dados"    => array("pedido_id" => $pedido_id),
                        );
                    } else {
                        if ( isset($Response[0]["Code"]) && isset($Response[0]["Message"]) ) {
                            $result = array(
                                "status"   => false,
                                "mensagem" => $Response[0]["Message"] ." (Code " . $Response[0]["Code"] . ")",
                                "dados"    => array("pedido_id" => $pedido_id),
                            );
                        } else {
                            $result = array(
                                "status"   => false,
                                "mensagem" => "Falha de comunicação (Erro 0)",
                                "dados"    => array("pedido_id" => $pedido_id),
                            );
                        }
                    }
                }
            }

            die(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        }

    }

}
