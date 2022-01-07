<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Localidade_Países
 *
 * @property Localidade_Países $current_model
 *
 */
class TesteTivit extends CI_Controller {


    private $integracoesProducao = [
        "columns" => [
            "integracao_id", "parceiro_id", "cod_tpa", "tipo", "integracao_comunicacao_id", "periodicidade_unidade", "periodicidade", "periodicidade_hora", "proxima_execucao", "ultima_execucao", "nome", "slug", "descricao", "script_sql", "parametros", "campo_chave", "ambiente", "host", "porta", "usuario", "senha", "diretorio", "envia_vazio", "habilitado", "status", "before_execute", "after_execute", "before_detail", "after_detail", "sequencia", "deletado", "criacao", "alteracao_usuario_id", "alteracao", "slug_group", "tipo_layout", "layout_separador", "limita_extensao", "integracaocol", "salva_log_vazio", "after_run", "privatekey_filename"
        ],
        "rows" => [
            ['125', '72', NULL, 'E', '1', 'I', '1', '01:00:00', '2022-01-04 09:31:00', '2022-01-04 09:30:03', 'NOVO MUNDO - VOUCHER - RETORNO', 'nm-voucher-retorno', 'NOVO MUNDO - VOUCHER - RETORNO', NULL, '0', 'id_exp|voucher|id_status', 'P', 'ftp.generali.com.br', '21', 'p-gbs-sis', 'G3n3r4l1@#2020', '/NOVOMUNDO/SINISTRO/input/', '0', '1', 'A', '', '', 'app_integracao_csv_retorno_novomundo', '', '0', '0', '2019-09-23 14:07:10', '0', '2022-01-04 09:30:03', NULL, 'CSV', ';', '1', NULL, '1', NULL, NULL]
        ]
    ];

    public function __construct() {
        parent::__construct();       
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 
        $this->load->model("integracao_model", "integracao");
    }

    public function testeIntegracao($integracao_id){
        $integracao = $this->integracao->get_by_id($integracao_id);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

        //$integracao = $this->getIntegracao($integracao_id);
        $aFile = $this->integracao->getFile($integracao, null);        
        var_dump($aFile);
    }

    private function getIntegracao($integracao_id){
        foreach($this->integracoesProducao["rows"] as $i => $row){
            if($row[0] == $integracao_id){
                return $this->createIntegracao($i);
            }
        }
    }

    private function createIntegracao($index){
        $output = array();
        foreach($this->integracoesProducao["columns"] as $i => $column){
            $output[$column] = $this->integracoesProducao["rows"][$index][$i];
        }
        return $output;
    }

    public function showTMP($dirPath){
        $dirPath = "/".str_replace("---", "/", $dirPath);
        var_dump($dirPath);
        print_r(scandir($dirPath));
    }

}