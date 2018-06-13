<?php
Class Log_Evento_Model extends MY_Model
{


    protected $_table = 'log_evento';
    protected $primary_key = 'log_evento_id';

    protected $return_type = 'array';
    protected $soft_delete = true;

    protected $soft_delete_key = 'deletado';

    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    /**
     * Desabilita o log nesse model para não gerar
     * um loop infinito
     * @var bool
     */
    protected $enable_log = FAlSE;

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();


    protected $_exclude_fields = array(
        'alteracao',
        'criacao',
        'alteracao_usuario_id',
        'deletado'
    );



    public function log($class, $tipo_evento, $conteudo){

        if(is_object($class) ){

            $class = strtolower(get_class($class));
        }

        $data  = array(
            'model' => $class,
            'tipo_evento' => $tipo_evento,
            'conteudo' => $conteudo,
            'ip' => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'cron',
            'controller' => $this->router->fetch_class(),
            'acao' => $this->router->fetch_method(),
            'criacao' => date('Y-m-d H:i:s'),
            'usuario_id' => $this->session->userdata('usuario_id'),
        );

        $this->insert($data);

    }

    /**
     * @param $model MY_Model
     * @param $old_data array
     * @param $new_data array
     */
    public function log_alterar($model, $old_data, $new_data){

        $resumo = '<table border="0" cellpadding="1" cellspacing="2" align="left" class="log_table_resumo">';

        if(is_object($model) && ($model instanceof MY_Model) ){

            $id_key = $model->primary_key();
            $id_value = $old_data[$id_key];

            $resumo .= '
                <tr class="linha_0">
                    <td>Chave primária:</td>
                    <td> ' . $id_key . ' </td>
                    <td> ' . $id_value . ' </td>
                </tr>
                <tr><td colspan="3">&nbsp;</td></tr>
                ';


        }

        $resumo .= '<tr><td>Campos</td><td>Antes</td><td>Depois</td></tr>';

        $c = 1;
        foreach ($new_data as $key => $value) {

            if(in_array($key, $this->_exclude_fields)){
                continue;
            }

            if ($old_data[$key] != $value) {
                $resumo .= '
                <tr class="linha_'.$c.'">
                    <td>' . $key . '</td>
                    <td>' . $old_data[$key] . '</td>
                    <td>' . $value . '</td>
                </tr>';
                $c++;
            }
        }
        if ($c == 1) {
            $resumo = '<table border="0" cellpadding="0" cellspacing="0" align="center">';
            $resumo .= '<tr class="tabela01_th"><td>Nenhum campo foi Alterado</td></tr>';
        }

        $resumo .= '</table>';

        $this->log($model, 'alterar', $resumo);
    }
    /**
     * @param $model MY_Model
     * @param $id_value int
     * @param $new_data array
     */
    public function log_inserir($model, $id_value,  $new_data){

        $resumo = '<table border="0" cellpadding="1" cellspacing="2" align="left" class="log_table_resumo">';

        if(is_object($model) && ($model instanceof MY_Model)){

            $id_key = $model->primary_key();

            $resumo .= '
                <tr class="linha_0">
                    <td>Chave primária:</td>
                    <td> ' . $id_value . ' ( '.$id_key. ' )</td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                ';

        }


        $resumo .= '<tr><td>Campos</td><td>Conteudo</td></tr>';

        $c = 1;
        foreach ($new_data as $key => $value) {

            if(in_array($key, $this->_exclude_fields)){
                continue;
            }


                $resumo .= '
                <tr class="linha_'.$c.'">
                    <td>' . $key . '</td>
                    <td>' . $value . '</td>
                </tr>';
                $c++;

        }

        $resumo .= '</table>';

        $this->log($model, 'inserir', $resumo);
    }

    /**
     * @param $model MY_Model
     * @param $id_value int
     * @param $new_data array
     */
    public function log_excluir($model, $id_value,  $new_data){

        $resumo = '<table border="0" cellpadding="1" cellspacing="2" align="left" class="log_table_resumo">';

        if(is_object($model) && ($model instanceof MY_Model)){

            $id_key = $model->primary_key();



            $resumo .= '
                <tr class="linha_0">
                    <td>Chave primária:</td>
                    <td> ' . $id_value . ' ( '.$id_key. ' )</td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                ';
            unset($new_data[$id_key]);
        }

        $resumo .= '<tr><td>Campos</td><td>Conteudo</td></tr>';

        $c = 1;
        foreach ($new_data as $key => $value) {

            if(in_array($key, $this->_exclude_fields)){
                continue;
            }

            $resumo .= '
                <tr class="linha_'.$c.'">
                    <td>' . $key . '</td>
                    <td>' . $value . '</td>
                </tr>';
            $c++;

        }

        $resumo .= '</table>';

        $this->log($model, 'excluir', $resumo);
    }

    public function with_colaborador()
    {

        $this->_database->select('colaborador.nome');
        $this->_database->join('colaborador', 'usuario.colaborador_id = colaborador.colaborador_id', 'inner');
        return $this;
    }
    public function get_all($limit = 0, $offset = 0)
    {
        $this->_database->select($this->_table.'.*, colaborador.nome');
        $this->_database->from($this->_table);
        $this->_database->where($this->_table.'.deletado', 0);
        $this->_database->join('usuario', 'usuario.usuario_id = '.$this->_table.'.usuario_id', 'inner');
        $this->_database->join('colaborador', 'usuario.colaborador_id = colaborador.colaborador_id', 'inner');
        $this->_database->order_by($this->_table.'.criacao', 'DESC');

        $query = $this->_database->get();

        if($query->num_rows() > 0)
            return $query->result_array();
        return array();
    }

    public function with_usuario($fields = array('colaborador_id'))
    {
        $this->with_simple_relation('usuario', '', 'usuario_id', $fields, 'inner');
        return $this;
    }

}
