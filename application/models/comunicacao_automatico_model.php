<?php
Class Comunicacao_automatico_model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'comunicacao_automatico';
    protected $primary_key = 'comunicacao_automatico_id';
    protected $enable_log = FALSE;
    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');
    
    //Dados
    public $validate = array(
        array(
            'field' => 'proxima_execucao',
            'label' => 'Próxima Execucao',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'melhor_horario',
            'label' => 'Melhor Horário',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'quantidade',
            'label' => 'Quantidade',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'somente_dia_util',
            'label' => 'Somente Dia útil',
            'rules' => 'required',
            'groups' => 'default'
        ),
    );

    public function update_track(){

        $this->load->model('comunicacao_track_model', 'comunicacao_track');


        $sql = "SELECT TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(data_hora))),'%H:%i') as melhor_horario FROM comunicacao_track";
        $row = $this->_database->query($sql)->result_array();
        $row = $row[0];

        //print_r($row);exit;

        $data = array();
        $data['melhor_horario'] = $row['melhor_horario'];

        $this->update(1, $data, TRUE);





    }


    public function enviarMensagem(){

        $this->load->model('comunicacao_agendamento_model', 'comunicacao_agendamento');
        $this->load->model('comunicacao_model', 'comunicacao_model');

        $row = $this->where('proxima_execucao', '<=', date('Y-m-d H:i:s'))->get(1);


        if(!$row){
            exit('Sem Execucao');
        }

        $mensagens = $this->comunicacao_agendamento
            ->limit($row['quantidade'])
            ->get_many_by(array(
                'comunicacao_status_id' => 1
            ));

        foreach ($mensagens as $mensagem)
        {
            $comunicacao_agendamento_id = $mensagem['comunicacao_agendamento_id'];
            unset($mensagem['comunicacao_agendamento_id']);

            $this->comunicacao_model->insert($mensagem, TRUE);


            $this->comunicacao_agendamento->update($comunicacao_agendamento_id, ['comunicacao_status_id' => 2], TRUE);
        }


        if($row['somente_dia_util']){
            $proxima_execucao = $this->proximoDiaUtil(date('Y-m-d', mktime(0,0,0, date('m'),date('d')+1,date('Y'))));
        }else{
            $proxima_execucao = date('Y-m-d', mktime(0,0,0, date('m'),date('d')+1,date('Y')));
        }


        $this->update(1, ['proxima_execucao' => "{$proxima_execucao} {$row['melhor_horario']}:00"], TRUE);

    }




    public function proximoDiaUtil($agora) {
        $timestamp = strtotime($agora);

        $dia = date('N', $timestamp);
        if ($dia >= 6) {
            $timestamp_final = $timestamp + ((8 - $dia) * 3600 * 24);
        } else {
            $timestamp_final = $timestamp;
        }
        return date('Y-m-d', $timestamp_final);
    }

}
