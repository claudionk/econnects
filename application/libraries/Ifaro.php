<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ifaro
{

    private $_ci;

    public $client;
    public $token;
    public $token_validade;
    public $produto_parceiro_id;
    public $usuario;
    public $senha;
    public $parametros;
    public $produto_parceiro_servico_id;

    const API_ENDPOINT = "http://ws.ifaro.com.br/APIDados.svc/";
    const API_TOKEN    = "http://ws.ifaro.com.br//Seguranca.svc/API/GetToken/";

    public function __construct($params = array())
    {
        $this->_ci = &get_instance();
        $this->_ci->load->model('produto_parceiro_servico_log_model', 'produto_parceiro_servico_log');

        foreach ($params as $property => $value) {
            $this->$property = $value;
        }

        if ($this->produto_parceiro_id) {
            $this->setToken();
        }
    }

    public function setToken()
    {
        $this->_ci->load->model('produto_parceiro_servico_model', 'produto_parceiro_servico');

        $config = $this->_ci->produto_parceiro_servico
            ->with_servico()
            ->with_servico_tipo()
            ->filter_by_servico_tipo('ifaro_pf')
            ->filter_by_produto_parceiro($this->produto_parceiro_id)
            ->get_all();

        if ($config) {
            $this->produto_parceiro_servico_id = $config[0]["produto_parceiro_servico_id"];
        }

        $Login = base64_encode($config[0]["servico_usuario"]);
        $Senha = base64_encode($config[0]["servico_senha"]);
        $Url   = self::API_TOKEN . "$Login/$Senha";

        $Antes = new DateTime();

        $myCurl = curl_init();
        curl_setopt($myCurl, CURLOPT_URL, $Url);
        curl_setopt($myCurl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($myCurl, CURLOPT_POST, 0);
        curl_setopt($myCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($myCurl, CURLOPT_TIMEOUT, 15);
        curl_setopt($myCurl, CURLOPT_CONNECTTIMEOUT, 15);
        $Response = curl_exec($myCurl);
        curl_close($myCurl);

        $Depois = new DateTime();

        $data_log["produto_parceiro_servico_id"] = $this->produto_parceiro_servico_id;
        $data_log["url"]                         = $Url;
        $data_log["consulta"]                    = "GetToken";
        $data_log["retorno"]                     = $Response;
        $data_log["time_envio"]                  = $Antes->format("H:i:s");
        $data_log["time_retorno"]                = $Depois->format("H:i:s");
        $data_log["parametros"]                  = $Url;
        $data_log["data_log"]                    = $Antes->format("Y-m-d H:i:s");
        $data_log["ip"]                          = (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "");
        $this->_ci->produto_parceiro_servico_log->insLog($this->produto_parceiro_servico_id, $data_log);

        $response = json_decode($Response, true);
        if (isset($response["TipoRetorno"]) && intval($response["TipoRetorno"]) == 0) {
            $this->token = $response["Token"];
            return true;
        } else {
            return false;
        }
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getBasePessoaPF($doc)
    {
        $this->setToken();
        $Token = $this->token;
        $CPF   = $doc;
        $Url   = self::API_ENDPOINT . "ConsultaPessoa/$CPF/$Token";

        $Antes = new DateTime();

        $myCurl = curl_init();
        curl_setopt($myCurl, CURLOPT_URL, $Url);
        curl_setopt($myCurl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($myCurl, CURLOPT_POST, 0);
        curl_setopt($myCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($myCurl, CURLOPT_TIMEOUT, 15);
        curl_setopt($myCurl, CURLOPT_CONNECTTIMEOUT, 15);
        $Response = curl_exec($myCurl);
        curl_close($myCurl);

        $Depois   = new DateTime();
        $response = json_decode($Response, true);

        $data_log["produto_parceiro_servico_id"] = $this->produto_parceiro_servico_id;
        $data_log["url"]                         = $Url;
        $data_log["idConsulta"]                  = isset($response["idConsulta"]) ? $response["idConsulta"] : null;
        $data_log["consulta"]                    = "ConsultaPessoa";
        $data_log["retorno"]                     = $Response;
        $data_log["time_envio"]                  = $Antes->format("H:i:s");
        $data_log["time_retorno"]                = $Depois->format("H:i:s");
        $data_log["parametros"]                  = $Url;
        $data_log["data_log"]                    = $Antes->format("Y-m-d H:i:s");
        $data_log["ip"]                          = (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "");
        $this->_ci->produto_parceiro_servico_log->insLog($this->produto_parceiro_servico_id, $data_log);

        if (isset($response["TipoRetorno"]) && intval($response["TipoRetorno"]) == 0) {
            return $response;
        } else {
            return array();
        }
    }

    public function getLogradouro($Logadouro = '', $LogadouroTipo = '')
    {
        $tipos = [
            "ACAMP" => "ACAMPAMENTO",
            "AC" => "ACESSO",
            "AD" => "ADRO",
            "ERA" => "AEROPORTO",
            "AL" => "ALAMEDA",
            "AT" => "ALTO",
            "A" => "AREA",
            "AE" => "AREA ESPECIAL",
            "ART" => "ARTERIA",
            "ATL" => "ATALHO",
            "AV" => "AVENIDA",
            "AV-CONT" => "AVENIDA CONTORNO",
            "BX" => "BAIXA",
            "BLO" => "BALAO",
            "BAL" => "BALNEARIO",
            "BC" => "BECO",
            "BELV" => "BELVEDERE",
            "BL" => "BLOCO",
            "BSQ" => "BOSQUE",
            "BVD" => "BOULEVARD",
            "BCO" => "BURACO",
            "C" => "CAIS",
            "CALC" => "CALCADA",
            "CAM" => "CAMINHO",
            "CPO" => "CAMPO",
            "CAN" => "CANAL",
            "CHAP" => "CHACARA",
            "CHAP" => "CHAPADAO",
            "CIRC" => "CIRCULAR",
            "COL" => "COLONIA",
            "CMP-VR" => "COMPLEXO VIARIO",
            "COND" => "CONDOMINIO",
            "CJ" => "CONJUNTO",
            "COR" => "CORREDOR",
            "CRG" => "CORREGO",
            "DSC" => "DESCIDA",
            "DSV" => "DESVIO",
            "DT" => "DISTRITO",
            "EVD" => "ELEVADA",
            "ENT-PART" => "ENTRADA PARTICULAR",
            "EQ" => "ENTRE QUADRA",
            "ESC" => "ESCADA",
            "ESP" => "ESPLANADA",
            "ETC" => "ESTACAO",
            "ESTC" => "ESTACIONAMENTO",
            "ETD" => "ESTADIO",
            "ETN" => "ESTANCIA",
            "EST" => "ESTRADA",
            "EST-MUN" => "ESTRADA MUNICIPAL",
            "FAV" => "FAVELA",
            "FAZ" => "FAZENDA",
            "FRA" => "FEIRA",
            "FER" => "FERROVIA",
            "FNT" => "FONTE",
            "FTE" => "FORTE",
            "GAL" => "GALERIA",
            "GJA" => "GRANJA",
            "HAB" => "HABITACIONAL",
            "IA" => "ILHA",
            "JD" => "JARDIM",
            "JDE" => "JARDINETE",
            "LD" => "LADEIRA",
            "LG" => "LAGO",
            "LGA" => "LAGOA",
            "LRG" => "LARGO",
            "LOT" => "LOTEAMENTO",
            "MNA" => "MARINA",
            "MOD" => "MODULO",
            "TEM" => "MONTE",
            "MRO" => "MORRO",
            "NUC" => "NUCLEO",
            "PDA" => "PARADA",
            "PDO" => "PARADOURO",
            "PAR" => "PARALELA",
            "PRQ" => "PARQUE",
            "PSG" => "PASSAGEM",
            "PSC-SUB" => "PASSAGEM SUBTERRANEA",
            "PSA" => "PASSARELA",
            "PAS" => "PASSEIO",
            "PAT" => "PATIO",
            "PNT" => "PONTA",
            "PTE" => "PONTE",
            "PTO" => "PORTO",
            "PC" => "PRACA",
            "PC-ESP" => "PRACA DE ESPORTES",
            "PR" => "PRAIA",
            "PRL" => "PROLONGAMENTO",
            "Q" => "QUADRA",
            "QTA" => "QUINTA",
            "QTAS" => "QUINTA",
            "RAM" => "RAMAL",
            "RMP" => "RAMPA",
            "REC" => "RECANTO",
            "RES" => "RESIDENCIAL",
            "RET" => "RETA",
            "RER" => "RETIRO",
            "RTN" => "RETORNO",
            "ROD-AN" => "RODO ANEL",
            "ROD" => "RODOVIA",
            "RTT" => "ROTATORIA",
            "ROT" => "ROTULA",
            "R" => "RUA",
            "R-LIG" => "RUA DE LIGACAO",
            "R-PED" => "RUA DE PEDESTRE",
            "SRV" => "SERVIDAO",
            "ST" => "SETOR",
            "SIT" => "SITIO",
            "SUB" => "SUBIDA",
            "TER" => "TERMINAL",
            "TV" => "TRAVESSA",
            "TV-PART" => "TRAVESSA PARTICULAR",
            "TRV" => "TRECHO",
            "TRV" => "TREVO",
            "TCH" => "TRINCHEIRA",
            "TUN" => "TUNEL",
            "UNID" => "UNIDADE",
            "VAL" => "VALA",
            "VLE" => "VALE",
            "VRTE" => "VARIANTE",
            "VER" => "VEREDA",
            "V" => "VIA",
            "V-AC" => "VIA DE ACESSO",
            "V-PED" => "VIA DE PEDESTRE",
            "V-EVD" => "VIA ELEVADO",
            "V-EXP" => "VIA EXPRESSA",
            "VD" => "VIADUTO",
            "VLA" => "VIELA",
            "VL" => "VILA",
            "ZIG-ZAG" => "ZIGUE-ZAGUE",
        ];

        $LogadouroTipo = isset($tipos[$LogadouroTipo]) ? $tipos[$LogadouroTipo] .' ' : '';
        return trim($Logadouro).$LogadouroTipo;
    }

}
