<?php
Class Integracao_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'integracao';
    protected $primary_key = 'integracao_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome', 'descricao');

    private $data_template_script = array();


    //Dados
    public $validate = array(
        array(
            'field' => 'parceiro_id',
            'label' => 'Parceiro',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'tipo',
            'label' => 'Tipo de Dados   ',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'integracao_comunicacao_id',
            'label' => 'Tipo de Comunicação',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'periodicidade_unidade',
            'label' => 'Unidade Periodicidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'periodicidade',
            'label' => 'Periodicidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'periodicidade_hora',
            'label' => 'Hora Periodicidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'nome',
            'label' => 'Nome',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'descricao',
            'label' => 'descrição',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'script_sql',
            'label' => 'SQL',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'campo_chave',
            'label' => 'Campo Chave (Registros)',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'ambiente',
            'label' => 'Ambiente',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'host',
            'label' => 'Host',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'porta',
            'label' => 'Porta',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'usuario',
            'label' => 'usuario',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'senha',
            'label' => 'senha',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'diretorio',
            'label' => 'Diretório',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'habilitado',
            'label' => 'Habilitado',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'before_execute',
            'label' => 'Antes de Executar',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'after_execute',
            'label' => 'Depois de Executar',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'before_detail',
            'label' => 'Antes de Executar Detalhe',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'after_detail',
            'label' => 'Depois de Executar Detalhe',
            'rules' => '',
            'groups' => 'default'
        ),
        array(
            'field' => 'sequencia',
            'label' => 'Numero de sequencia',
            'rules' => '',
            'groups' => 'default'
        )
    );

    public function __construct()
    {
        parent::__construct();

        $this->load->library('parser');

        $this->data_template_script = array(
            'data_ini_mes_anterior' => date('Y-m-d', mktime(0, 0, 0, date('m')-1, 1, date('Y'))),
            'data_fim_mes_anterior' => date('Y-m-t', mktime(0, 0, 0, date('m')-1, 1, date('Y'))),
            'data_ini_mes' => date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y'))),
            'data_fim_mes' => date('Y-m-t', mktime(0, 0, 0, date('m'), 1, date('Y'))),

        );


    }

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'parceiro_id' => $this->input->post('parceiro_id'),
            'tipo' => $this->input->post('tipo'),
            'integracao_comunicacao_id' => $this->input->post('integracao_comunicacao_id'),
            'periodicidade_unidade' => $this->input->post('periodicidade_unidade'),
            'periodicidade' => $this->input->post('periodicidade'),
            'periodicidade_hora' => $this->input->post('periodicidade_hora'),
            'proxima_execucao' => $this->get_proxima_execucao( $this->input->post('integracao_id')),
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'descricao' => $this->input->post('descricao'),
            'script_sql' => $this->input->post('script_sql'),
            'parametros' => $this->input->post('parametros'),
            'campo_chave' => $this->input->post('campo_chave'),
            'ambiente' => $this->input->post('ambiente'),
            'host' => $this->input->post('host'),
            'porta' => $this->input->post('porta'),
            'usuario' => $this->input->post('usuario'),
            'senha' => $this->input->post('senha'),
            'diretorio' => $this->input->post('diretorio'),
            'habilitado' => $this->input->post('habilitado'),
            'before_execute' => $this->input->post('before_execute'),
            'after_execute' => $this->input->post('after_execute'),
            'before_detail' => $this->input->post('before_detail'),
            'after_detail' => $this->input->post('after_detail'),
        );
        return $data;
    }

    public function get_proxima_execucao($integracao_id = 0){
        if($integracao_id == 0){
            return null;

        }else{
            $integracao = $this->get($integracao_id);
            if($integracao){
                switch ($integracao['periodicidade_unidade']) {
                    case 'I' :
                        $date = date('Y-m-d H:i:s', mktime(date('h'), date('i') + $integracao['periodicidade'], 0, date('m'), date('d'), date('Y')));
                        break;
                    case 'H' :
                        $date = date('Y-m-d H:i:s', mktime(date('h') + $integracao['periodicidade'], date('i'), 0, date('m'), date('d'), date('Y')));
                        break;
                    case 'D' :
                        $date = date("Y-m-d {$integracao['periodicidade_hora']}", mktime(date('h') + $integracao['periodicidade'], date('i'), 0, date('m'), date('d') + $integracao['periodicidade'], date('Y')));
                        break;
                    case 'M' :
                        $date = date("Y-m-d {$integracao['periodicidade_hora']}", mktime(date('h') + $integracao['periodicidade'], date('i'), 0, date('m') + $integracao['periodicidade'], date('d'), date('Y')));
                        break;
                    case 'Y' :
                        $date = date("Y-m-d {$integracao['periodicidade_hora']}", mktime(date('h') + $integracao['periodicidade'], date('i'), 0, date('m'), date('d'), date('Y') + $integracao['periodicidade']));
                        break;
                    case 'C' :
                        $date = date("Y-m-d h-i-s");
                        break;
                }

                return $date;

            }else{
                return '0000-00-00 00:00:00';
            }
        }

    }

    public function get_by_id($id)
    {
        return $this->get_by($this->primary_key, $id);
    }


    public function run(){

        $integracoes = $this->filter_by_rotina_pronta()->order_by('proxima_execucao')->limit(1)->get_all();

        foreach ($integracoes as $integracao) {

            if($integracao['tipo'] == 'S'){
                $this->run_s($integracao['integracao_id']);
            }elseif($integracao['tipo'] == 'R'){
                $this->run_r($integracao['integracao_id']);
            }elseif($integracao['tipo'] == 'E'){
                $this->run_r($integracao['integracao_id']);
            }

        }


    }


    public function run_r($integracao_id){

        $this->load->model('integracao_log_model', 'integracao_log');
        $this->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');
        $this->load->model('integracao_layout_model', 'integracao_layout');


        $this->_database->select('integracao.*');
        $this->_database->where("integracao.integracao_id", $integracao_id);
        $this->_database->where("integracao.status", 'A');
        $this->_database->where("integracao.deletado", 0);
        $this->_database->where("integracao.habilitado", 1);
        $this->_database->where("integracao.proxima_execucao <= ", date('Y-m-d H:m:s'));
        $result = $this->get_all();


        if($result){
            $result = $result[0];
            $dados_integracao = array();
            $dados_integracao['status'] = 'L';
            $this->update($result['integracao_id'], $dados_integracao, TRUE);


            //execute before execute
            if((!empty($result['before_execute'])) && (function_exists($result['before_execute']))){
                call_user_func($result['before_execute'], null, array('item' => $result, 'registro' => array(), 'log' => array(), 'valor' => null));
            }


            $layout_filename = $this->integracao_layout->filter_by_integracao($result['integracao_id'])
                ->filter_by_tipo('F')
                ->order_by('ordem')
                ->get_all();


            $file = (isset($layout_filename[0]['valor_padrao'])) ? $layout_filename[0]['valor_padrao'] : '';
            $result_file = $this->getFile($result, $file);


            if(!empty($result_file['file'])){
                $this->processFileIntegracao($result, $result_file['file']);
            }



            $dados_integracao = array();
            $dados_integracao['proxima_execucao'] = $this->get_proxima_execucao($result['integracao_id']);
            $dados_integracao['ultima_execucao'] = date('Y-m-d H:i:s');
            $dados_integracao['status'] = 'A';
            $this->update($result['integracao_id'], $dados_integracao, TRUE);

            //execute before execute
            if((!empty($result['after_execute'])) && (function_exists($result['after_execute']))){
                call_user_func($result['after_execute'], null, array('item' => $result, 'registro' => $result_file, 'log' => array(), 'valor' => null));
            }

        }



    }

    public function run_s($integracao_id){

        $this->load->model('integracao_log_model', 'integracao_log');
        $this->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');


        $this->_database->select('integracao.*');
        $this->_database->where("integracao.integracao_id", $integracao_id);
        $this->_database->where("integracao.status", 'A');
        $this->_database->where("integracao.deletado", 0);
        $this->_database->where("integracao.habilitado", 1);
        $this->_database->where("integracao.proxima_execucao <= ", date('Y-m-d H:m:s'));
        $result = $this->get_all();

        if($result){
            $result = $result[0];
            $dados_integracao = array();
            $dados_integracao['status'] = 'L';
            $this->update($result['integracao_id'], $dados_integracao, TRUE);

            $result_file = $this->createFileIntegracao($result);

            $this->sendFile($result, $result_file['file']);

            $dados_log = array();
            $dados_log['processamento_fim'] = date('Y-m-d H:i:s');
            $dados_log['nome_arquivo'] = basename($result_file['file']);
            $dados_log['integracao_log_status_id'] = 3;

            $this->integracao_log->update($result_file['integracao_log_id'], $dados_log, TRUE);
            $this->integracao_log_detalhe->update_by(
                array('integracao_log_id' =>$result_file['integracao_log_id']),array(
                    'integracao_log_status_id' => 3
                )
            );

            $dados_integracao = array();
            $dados_integracao['proxima_execucao'] = $this->get_proxima_execucao($result['integracao_id']);
            $dados_integracao['ultima_execucao'] = date('Y-m-d H:i:s');
            $dados_integracao['status'] = 'A';
            $this->update($result['integracao_id'], $dados_integracao, TRUE);

        }

    }

    private function sendFile($integracao = array(), $file){
        try{

            switch ($integracao['integracao_comunicacao_id']){

                case 1:
                    $this->sendFileFTP($integracao, $file);
                    break;

                case 2:

                    break;
                case 3:

                    break;
            }

        }catch (Exception $e) {

        }

    }


    private function getFile($integracao = array(), $file){
        try{

            switch ($integracao['integracao_comunicacao_id']){

                case 1:
                    return $this->getFileFTP($integracao, $file);
                    break;

                case 2:

                    break;
                case 3:

                    break;
            }

        }catch (Exception $e) {

        }

    }


    private function getFileFTP($integracao = array(), $file){

        $this->load->model('integracao_log_model', 'integracao_log');

        $this->load->library('ftp');


        $config['hostname'] = $integracao['host'];
        $config['username'] = $integracao['usuario'];
        $config['password'] = $integracao['senha'];
        $config['port'] = $integracao['porta'];
        $config['debug']	= TRUE;


        //$filename = basename($file);
        $filename = "{$file}*";

        $this->ftp->connect($config);

        $list = $this->ftp->list_files("{$integracao['diretorio']}{$filename}");



        $result = array(
            'file' => ''
        );
        $file_processar = '';
        if($list) {
            foreach ($list as $index => $item) {
                $total = $this->integracao_log
                    ->filter_by_integracao($integracao['integracao_id'])
                    ->filter_by_file(basename($item))
                    ->get_total();

                if ((int)$total == 0) {
                    $file_processar = $item;
                    break;
                }
            }
        }


        if(!empty($file_processar)){
            $diretorio = app_assets_dir('integracao', 'uploads') . "{$integracao['integracao_id']}/{$integracao['tipo']}";
            if(!file_exists($diretorio)){
                mkdir($diretorio, 0777, true);
            }
            $fileget = basename($file_processar);
            if($this->ftp->download($file_processar, "{$diretorio}/{$fileget}", 'binary')){
                $result = array(
                    'file' => "{$diretorio}/{$fileget}"
                );
            }

        }
        $this->ftp->close();
        return $result;
    }

    private function sendFileFTP($integracao = array(), $file){

        $this->load->library('ftp');



        $config['hostname'] = $integracao['host'];
        $config['username'] = $integracao['usuario'];
        $config['password'] = $integracao['senha'];
        $config['port'] = $integracao['porta'];
        $config['debug']	= TRUE;
        $filename = basename($file);
        $this->ftp->connect($config);
        $this->ftp->upload($file, "{$integracao['diretorio']}{$filename}", 'binary', 0777);
        $this->ftp->close();
    }

    private function createFileIntegracao($integracao = array()){

        $this->load->model('integracao_log_model', 'integracao_log');
        $this->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');
        $this->load->model('integracao_layout_model', 'integracao_layout');


        $diretorio = app_assets_dir('integracao', 'uploads') . "{$integracao['integracao_id']}/{$integracao['tipo']}";



        $this->data_template_script['integracao_id'] = $integracao['integracao_id'];

        $integracao['script_sql'] = $this->parser->parse_string($integracao['script_sql'], $this->data_template_script, TRUE);
        //exit($integracao['script_sql']);
        $registros = $this->_database->query($integracao['script_sql'])->result_array();


        $integracao_log =  $this->integracao_log->insLog($integracao['integracao_id'], count($registros));

        //busca layout
        $layout_header = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
                                                 ->filter_by_tipo('H')
                                                 ->order_by('ordem')
                                                 ->get_all();

        $layout_detail = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D')
            ->order_by('ordem')
            ->get_all();


        $layout_detail2 = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D2')
            ->order_by('ordem')
            ->get_all();

        $layout_detail3 = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D3')
            ->order_by('ordem')
            ->get_all();

        $layout_detail4 = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D4')
            ->order_by('ordem')
            ->get_all();

        $layout_detail5 = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D5')
            ->order_by('ordem')
            ->get_all();

        $layout_detail6 = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D6')
            ->order_by('ordem')
            ->get_all();

        $layout_detail7 = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D7')
            ->order_by('ordem')
            ->get_all();

        $layout_detail8 = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D8')
            ->order_by('ordem')
            ->get_all();

        $layout_detail9 = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D9')
            ->order_by('ordem')
            ->get_all();

        $layout_trailler = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('T')
            ->order_by('ordem')
            ->get_all();

        $layout_filename = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('F')
            ->order_by('ordem')
            ->get_all();


        //header
        $linhas[] = $this->getLinha($layout_header, $registros, $integracao_log);



        //detail
        foreach ($registros as $registro) {
            $linha = $this->getLinha($layout_detail, $registro, $integracao_log);
            $linha = mb_strtoupper($linha, 'UTF-8');
            $linha = app_remove_especial_caracteres($linha);
            $linhas[] = $linha;


            if($layout_detail2){
                $linha = $this->getLinha($layout_detail2, $registro, $integracao_log);
                $linha = mb_strtoupper($linha, 'UTF-8');
                $linha = app_remove_especial_caracteres($linha);
                $linhas[] = $linha;
            }

            if($layout_detail3){
                $linha = $this->getLinha($layout_detail3, $registro, $integracao_log);
                $linha = mb_strtoupper($linha, 'UTF-8');
                $linha = app_remove_especial_caracteres($linha);
                $linhas[] = $linha;
            }

            if($layout_detail4){
                $linha = $this->getLinha($layout_detail3, $registro, $integracao_log);
                $linha = mb_strtoupper($linha, 'UTF-8');
                $linha = app_remove_especial_caracteres($linha);
                $linhas[] = $linha;
            }

            if($layout_detail5){
                $linha = $this->getLinha($layout_detail3, $registro, $integracao_log);
                $linha = mb_strtoupper($linha, 'UTF-8');
                $linha = app_remove_especial_caracteres($linha);
                $linhas[] = $linha;
            }

            if($layout_detail6){
                $linha = $this->getLinha($layout_detail3, $registro, $integracao_log);
                $linha = mb_strtoupper($linha, 'UTF-8');
                $linha = app_remove_especial_caracteres($linha);
                $linhas[] = $linha;
            }

            if($layout_detail7){
                $linha = $this->getLinha($layout_detail3, $registro, $integracao_log);
                $linha = mb_strtoupper($linha, 'UTF-8');
                $linha = app_remove_especial_caracteres($linha);
                $linhas[] = $linha;
            }

            if($layout_detail8){
                $linha = $this->getLinha($layout_detail3, $registro, $integracao_log);
                $linha = mb_strtoupper($linha, 'UTF-8');
                $linha = app_remove_especial_caracteres($linha);
                $linhas[] = $linha;
            }

            if($layout_detail9){
                $linha = $this->getLinha($layout_detail3, $registro, $integracao_log);
                $linha = mb_strtoupper($linha, 'UTF-8');
                $linha = app_remove_especial_caracteres($linha);
                $linhas[] = $linha;
            }
            $this->integracao_log_detalhe->insLogDetalhe($integracao_log['integracao_log_id'], count($linhas), $registro[$integracao['campo_chave']]);

        }

        //trailler
        $linhas[] = $this->getLinha($layout_trailler, $registros, $integracao_log);


        if(!file_exists($diretorio)){
            mkdir($diretorio, 0777, true);
        }



        $filename = $this->getLinha($layout_filename, $registros, $integracao_log);


        //$filename = 'Intercontinental_Remessa_'. date('dmY');
        file_put_contents("{$diretorio}/{$filename}", implode("\n", $linhas));


        return array('file' => "{$diretorio}/{$filename}", 'integracao_log_id' => $integracao_log['integracao_log_id']);

    }

    private function processFileIntegracao($integracao = array(), $file){

        $this->load->model('integracao_log_model', 'integracao_log');
        $this->load->model('integracao_log_detalhe_model', 'integracao_log_detalhe');
        $this->load->model('integracao_layout_model', 'integracao_layout');


        $diretorio = app_assets_dir('integracao', 'uploads') . "{$integracao['integracao_id']}/{$integracao['tipo']}";

        //busca layout
        $layout_header = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('H')
            ->order_by('ordem')
            ->get_all();

        $layout_detail = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('D')
            ->order_by('ordem')
            ->get_all();

        $layout_trailler = $this->integracao_layout->filter_by_integracao($integracao['integracao_id'])
            ->filter_by_tipo('T')
            ->order_by('ordem')
            ->get_all();




        $fh = fopen($file, 'r');


        $integracao_log =  $this->integracao_log->insLog($integracao['integracao_id'], count(file($file)), basename($file));

        $header = array();
        $detail = array();
        $trailler = array();
        $num_registro = 0;

        while (!feof($fh)) #INICIO DO WHILE NO ARQUIVO
        {
            $linhas = str_replace("'"," ",fgets($fh, 4096));

            //header
            if(substr($linhas,($layout_header[0]['inicio'])-1,$layout_header[0]['tamanho']) == $layout_header[0]['valor_padrao']){
                foreach ($layout_header as $idxh => $item_h) {
                    $header[] = array(
                        'layout' => $item_h,
                        'valor' => substr($linhas,($item_h['inicio'])-1,$item_h['tamanho']),
                        'linha' => $linhas,
                    );
                }
            }elseif(substr($linhas,($layout_detail[0]['inicio'])-1,$layout_detail[0]['tamanho']) == $layout_detail[0]['valor_padrao']){
                $sub_detail = array();
                foreach ($layout_detail as $idxd => $item_d) {
                    $sub_detail[] = array(
                        'layout' => $item_d,
                        'valor' => substr($linhas,($item_d['inicio'])-1,$item_d['tamanho']),
                        'linha' => $linhas,
                    );
                }
                $detail[] = $sub_detail;
                $num_registro++;
            }elseif(substr($linhas,($layout_trailler[0]['inicio'])-1,$layout_trailler[0]['tamanho']) == $layout_trailler[0]['valor_padrao']){
                foreach ($layout_trailler as $idxt => $item_t) {
                    $trailler[] = array(
                        'layout' => $item_t,
                        'valor' => substr($linhas,($item_t['inicio'])-1,$item_t['tamanho']),
                        'linha' => $linhas,
                    );
                }
            }




        }
        $this->data_template_script['integracao_id'] = $integracao['integracao_id'];
        $integracao['script_sql']  = $this->parser->parse_string($integracao['script_sql'], $this->data_template_script, TRUE);
        $sql = $integracao['script_sql']; 

        $data = array();

        $id_log = 0;
        $num_linha = 0;
        foreach ($detail as  $rows) {
            $data_row = array();
            foreach ($rows as $index => $row) {
                if ($row['layout']['insert'] == 1) {
                    if(function_exists($row['layout']['function'])){

                        $row['valor'] = call_user_func($row['layout']['function'], $row['layout']['formato'], array('item' => array(), 'registro' => array(), 'log' => array(), 'valor' => $row['valor']));
                    }
                    $data_row[] = $row['valor'];
                }
                if ($row['layout']['campo_log'] == 1) {
                    $id_log = $row['valor'];
                    if(function_exists($row['layout']['function'])){

                        $id_log = call_user_func($row['layout']['function'], $row['layout']['formato'], array('item' => array(), 'registro' => array(), 'log' => array(), 'valor' => $row['valor']));
                    }
                }
            }
            $data[] = $data_row;

           $num_linha++;

        }



        $num_linha = 1;
        foreach ($data as $index => $datum) {
            //execute before detail
            if((!empty($result['before_detail'])) && (function_exists($result['before_detail']))){
                call_user_func($result['before_detail'], null, array('item' => $detail, 'registro' => $datum, 'log' => $integracao_log, 'valor' => null));
            }

            $this->_database->query($sql, $datum);
            $ultimo_id = $this->_database->insert_id();
            $this->integracao_log_detalhe->insLogDetalhe($integracao_log['integracao_log_id'], $num_linha, $ultimo_id);
            $num_linha++;

            //execute before detail
            if((!empty($result['after_detail'])) && (function_exists($result['after_detail']))){
                call_user_func($result['after_detail'], null, array('item' => $detail, 'registro' => $datum, 'log' => $integracao_log, 'valor' => $ultimo_id));
            }
        }


        $dados_log = array();
        $dados_log['processamento_fim'] = date('Y-m-d H:i:s');
        $dados_log['integracao_log_status_id'] = 4;

        $this->integracao_log->update($integracao_log['integracao_log_id'], $dados_log, TRUE);
        $this->integracao_log_detalhe->update_by(
            array('integracao_log_id' =>$integracao_log['integracao_log_id']),array(
                'integracao_log_status_id' => 4
            )
        );


        if($id_log){
            $dados_log = array();
            $dados_log['integracao_log_status_id'] = 4;

            $this->integracao_log->update($id_log, $dados_log, TRUE);
            $this->integracao_log_detalhe->update_by(
                array('integracao_log_id' =>$id_log),array(
                    'integracao_log_status_id' => 4
                )
            );
        }



    }


    private function getLinha($layout, $registro = array(), $log = array()){

        $result = '';

        foreach ($layout as $index => $item) {

            //print_r($registro);
            $pre_result = '';
            if(strlen($item['valor_padrao']) > 0 && $item['qnt_valor_padrao'] > 0){
                $pre_result .= mb_str_pad('', $item['qnt_valor_padrao'], $item['valor_padrao'], $item['str_pad']);
            }elseif (!empty($item['function'])){
                if(function_exists($item['function'])){
                    $pre_result .= mb_str_pad(call_user_func($item['function'], $item['formato'], array('item' => $item, 'registro' => $registro, 'log' => $log)), $item['tamanho'], $item['valor_padrao'], $item['str_pad']);
                }
            }elseif (!empty($item['nome_banco'])){
                if(isset($registro[$item['nome_banco']])){
                    $registro[$item['nome_banco']] = app_remove_especial_caracteres($registro[$item['nome_banco']]);
                    $pre_result .= mb_str_pad($registro[$item['nome_banco']], $item['tamanho'], isempty($item['valor_padrao'],' '), $item['str_pad']);
                }elseif(isset($log[$item['nome_banco']])){
                    $pre_result .= mb_str_pad($log[$item['nome_banco']], $item['tamanho'], isempty($item['valor_padrao'],' '), $item['str_pad']);
                }else{
                    $pre_result .= mb_str_pad('', $item['tamanho'], isempty($item['valor_padrao'],' '), $item['str_pad']);
                }
            }

            $result .= mb_substr($pre_result,0,$item['tamanho']);
        }
        return $result;

    }

    function filter_by_rotina_pronta(){

        $this->_database->where('integracao.periodicidade_unidade <>', 'C');
        $this->_database->where('integracao.habilitado', 1);
        $this->_database->where('integracao.status', 'A');
        $this->_database->where('integracao.proxima_execucao > ', date('Y-m-d H:i:s'));

        return $this;
    }

}
