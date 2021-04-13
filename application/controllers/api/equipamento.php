<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Class Apolice
*/
class Equipamento extends CI_Controller {
    public $api_key;
    public $usuario_id;

    public function __construct() {
        parent::__construct();

        header( "Access-Control-Allow-Origin: *" );
        header( "Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS" );
        header( "Access-Control-Allow-Headers: Cache-Control, APIKEY, apikey, Content-Type" );
        header( "Content-Type: application/json");

        $method = $_SERVER["REQUEST_METHOD"];
        if( $method == "OPTIONS" ) {
            die();
        }

        if( isset( $_SERVER["HTTP_APIKEY"] ) ) {
            $this->api_key = $_SERVER["HTTP_APIKEY"];
            $this->load->model( "usuario_webservice_model", "webservice" );

            $webservice = $this->webservice->checkKeyExpiration( $this->api_key );
            if( !sizeof( $webservice ) ) {
                die( json_encode( array( "status" => false, "message" => "APIKEY is invalid" ) ) );
            }
        } else {
            die( json_encode( array( "status" => false, "message" => "APIKEY is missing" ) ) );
        }
        
        $this->usuario_id = $webservice["usuario_id"];
        $this->load->database('default');

        $this->load->model("equipamento_model", "equipamento");
        $this->load->model("equipamentos_elegiveis_model", "equipamentos_elegiveis");
    }

    public function index() {
        if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
            $GET = $_GET;
        } else {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        if( !isset( $GET["ean"] ) ) {
            die( json_encode( array( "status" => false, "message" => "Campo EAN é obrigatório" ) ) );
        }

        $lista_id = isempty($GET["lista_id"], 1);

        if ($lista_id == 1)
        {
            $EquipModel = $this->equipamento;
        } else {
            $EquipModel = $this->equipamentos_elegiveis;
        }

        $ean = $GET["ean"];

        $Equipamento = $EquipModel
            ->filterByEAN($ean)
            ->whith_linhas()
            ->get_all();

        if( sizeof( $Equipamento ) ) {
            $Equipamento = $Equipamento[0];

            die( json_encode( array( 
                "status" => true, 
                "equipamento_id" => $Equipamento["equipamento_id"],
                "ean" => $Equipamento["ean"],
                "nome" => $Equipamento["nome"],
                "categoria" => $Equipamento["categoria"],
                "equipamento_marca_id" => $Equipamento["equipamento_marca_id"],
                "equipamento_categoria_id" => $Equipamento["equipamento_categoria_id"],
                "equipamento_sub_categoria_id" => $Equipamento["equipamento_sub_categoria_id"]
            ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        } else {          	
        	if( !isset( $GET["ret"] ) ) { /*rcarpi - gambiarra para nao gerar erro no header ao consumit o metodo*/
            	die( json_encode( array( "status" => false, "message" => "Não foram localizados equipamentos com esse EAN ($ean)" ) ) );
            }
        }    
    }

    public function categorias()
    {
        if( $_SERVER["REQUEST_METHOD"] === "GET" )
        {
            $GET = $_GET;
        } else {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        $this->load->model("equipamento_categoria_model", "equipamento_categoria");
        $this->load->model("equipamentos_elegiveis_categoria_model", "equipamentos_elegiveis_categoria");

        $lista_id = isempty($GET["lista_id"], 1);
        if ($lista_id == 1)
        {
            $EqCatModel = $this->equipamento_categoria;
        } else {
            $EqCatModel = $this->equipamentos_elegiveis_categoria;
        }

        $Categorias = $EqCatModel->filter_by_nviel(1)->order_by('nome')->get_all();
        die( json_encode( $Categorias, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function equipamentos() {
        if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
            $GET = $_GET;
        } else {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        $categoria_pai_id   = isempty($GET["equipamento_categoria_id"], 0);
        $marca_id           = isempty($GET["equipamento_marca_id"], 0);
        $lista_id           = isempty($GET["lista_id"], 1);

        $this->load->model("equipamento_categoria_model", "equipamento_categoria");
        $this->load->model("equipamentos_elegiveis_categoria_model", "equipamentos_elegiveis_categoria");

        if ($lista_id == 1)
        {
            $EqCatModel = $this->equipamento_categoria;
        } else {
            $EqCatModel = $this->equipamentos_elegiveis_categoria;
        }

        $Categorias = $EqCatModel->with_sub_categoria($categoria_pai_id, $marca_id)->order_by('nome')->get_all();
        die( json_encode( $Categorias, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function marcas() {
        if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
            $GET = $_GET;
        } else {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        $this->load->model("equipamento_marca_model", "equipamento_marca");
        $this->load->model("equipamentos_elegiveis_marca_model", "equipamentos_elegiveis_marca");

        $categoria_id = isempty($GET["equipamento_categoria_id"], 0);
        $lista_id     = isempty($GET["lista_id"], 1);

        if ($lista_id == 1)
        {
            $Marcas = $this->equipamento_marca;
        } else {
            $Marcas = $this->equipamentos_elegiveis_marca;
        }

        if (!empty($categoria_id))
        {
            $Marcas = $Marcas->get_by_categoria($categoria_id);
        }

        $Marcas = $Marcas->get_all();
        die( json_encode( $Marcas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function modeloMarca() {
        if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
            $GET = $_GET;
        } else {
            die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
        }

        $categoria_id = isempty($GET["equipamento_categoria_id"], null);
        $marca_id     = isempty($GET["equipamento_marca_id"], null);
        $lista_id     = isempty($GET["lista_id"], 1);

        if ($lista_id == 1)
        {
            $modelInstance = $this->equipamento;
        } else {
            $modelInstance = $this->equipamentos_elegiveis;
        }

        $result = $modelInstance->get_equipamentos($categoria_id, $marca_id);

        die( json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    }

    public function modelo($payload = null)
    {
        $returnDie = false;

        // caso tenha informado os dados na entrada
        if ( empty($payload) )
        {
            if( $_SERVER["REQUEST_METHOD"] !== "POST" ) {
                ob_clean();
                die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
            }

            $payload = json_decode( file_get_contents( "php://input" ) );
            $returnDie = true;
        }

        if (empty($payload)) {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "Nenhum dado enviado" ) ) );
        }

        if (empty($payload->modelo)) {
            ob_clean();
            die( json_encode( array( "status" => false, "message" => "O campo Modelo é obrigatório" ) ) );
        }

        //Faz o MATCH para consulta do Equipamento
        $indiceMax = 20;
        $modelo = $payload->modelo;
        $marca = emptyor($payload->marca, null);
        $categoria = emptyor($payload->categoria, null);
        $lista_id     = emptyor($payload->lista_id, 1);
        $resulDefault = array( "status" => false, "message" => "Não foram localizados equipamentos com o modelo informado ({$modelo})" );

        if ($lista_id == 1)
        {
            $EquipModel = $this->equipamento;
        } else {
            $EquipModel = $this->equipamentos_elegiveis;
        }

        if (!empty($payload->marca)) {
            $marca = $payload->marca;
            if (strpos($modelo, $marca) === FALSE) {
                $modelo = $marca." ".$modelo;
            }
        } else {
            $marca = null;
        }

        $qtdeRegistros = ( isset($payload->quantidade) && (int)$payload->quantidade > 0) ? $payload->quantidade : 10;
        $result = $EquipModel->match($lista_id, $modelo, $marca, $qtdeRegistros, $categoria);

        //se encontrou algum parecido
        if (empty($result))
        {
            if (!empty($marca))
            {
                $payload->marca = null; // remove a marca
                $result = $this->modelo($payload); //processa sem a marca
                die( json_encode( $result ) );
            } 

            if ( $returnDie ) {
                die( json_encode( $resulDefault ) );
            } else {
                return $resulDefault;
            }
        }

        $retorno = [];
        foreach ($result as $EANenriquecido)
        {
            //se o indice e maior do que o minimo estipulado de 30%
            if($EANenriquecido->indice / $indiceMax > 0.3) {
                $retorno[] = [
                    "equipamento_id" => $EANenriquecido->equipamento_id,
                    "ean" => $EANenriquecido->ean,
                    "nome" => $EANenriquecido->nome,
                    "categoria" => $EANenriquecido->categoria,
                    "equipamento_marca_id" => $EANenriquecido->equipamento_marca_id,
                    "equipamento_categoria_id" => $EANenriquecido->equipamento_categoria_id,
                    "equipamento_sub_categoria_id" => $EANenriquecido->equipamento_sub_categoria_id,
                    "indice" => $EANenriquecido->indice / $indiceMax
                ];
            }
        }

        if (empty($retorno))
        {
            if (!empty($marca))
            {
                $payload->marca = null; // remove a marca
                $retorno = $this->modelo($payload); //processa sem a marca
                die( json_encode( $retorno ) );
            } else {
                if ( $returnDie ) {
                    die( json_encode( $resulDefault ) );
                } else {
                    return $resulDefault;
                }
            }
        }

        // mesmo encontrando faz pesquisa sem marca para enriquecer o resultado
        if (!empty($marca))
        {
            $payload->marca = null; // remove a marca
            $ret = $this->modelo($payload); //processa sem a marca

            if ( !empty($ret['dados']) )
            {
                if ( !empty($retorno) )
                {
                    // junta as 2 pesquisas
                    $retorno = array_merge($ret['dados'], $retorno);

                    // prioriza a pesquisa mais real
                    usort($retorno, function ($item1, $item2) {
                        if ($item1['indice'] == $item2['indice']) return 0;
                        return $item1['indice'] > $item2['indice'] ? -1 : 1;
                    });

                } else {
                    $retorno = $ret['dados'];
                }
            }
        }

        $retorno = array( "status" => true, "modelo_informado" => $modelo, "dados" => $retorno);

        if ( $returnDie )
        {
            die( json_encode( $retorno , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        } else {
            return $retorno;
        }

    }

}


