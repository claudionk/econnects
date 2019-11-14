<?php
Class Capitalizacao_Sorteio_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'capitalizacao_sorteio';
    protected $primary_key = 'capitalizacao_sorteio_id';

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
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug')

        );
        return $data;
    }

    function get_by_id($id)
    {
        return $this->get($id);
    }

    public function getDadosCapitalizacao($pedido_id)
    {
        $this->_database
            ->select(" capitalizacao.capitalizacao_id, capitalizacao_sorteio.slug as slug_sorteio ")
            ->select(" IFNULL( IFNULL(apolice_equipamento.data_adesao, apolice_generico.data_adesao), apolice_seguro_viagem.data_adesao ) as data_adesao ", FALSE)
            ->select(" IFNULL( IFNULL(apolice_equipamento.data_ini_vigencia, apolice_generico.data_ini_vigencia), apolice_seguro_viagem.data_ini_vigencia ) as data_ini_vigencia ", FALSE)
            ->select(" IFNULL( IFNULL(apolice_equipamento.data_fim_vigencia, apolice_generico.data_fim_vigencia), apolice_seguro_viagem.data_fim_vigencia ) as data_fim_vigencia ", FALSE)
            ->join("capitalizacao", "capitalizacao.capitalizacao_sorteio_id = capitalizacao_sorteio.capitalizacao_sorteio_id AND capitalizacao.deletado = 0", 'inner')
            ->join("capitalizacao_serie", "capitalizacao.capitalizacao_id = capitalizacao_serie.capitalizacao_id AND capitalizacao_serie.deletado = 0", 'inner')
            ->join("capitalizacao_serie_titulo", "capitalizacao_serie.capitalizacao_serie_id = capitalizacao_serie_titulo.capitalizacao_serie_id AND capitalizacao_serie_titulo.deletado = 0", 'inner')
            ->join("apolice", "apolice.pedido_id = capitalizacao_serie_titulo.pedido_id AND apolice.deletado = 0", 'inner')
            ->join("apolice_equipamento", "apolice.apolice_id = apolice_equipamento.apolice_id AND apolice_equipamento.deletado = 0", 'left')
            ->join("apolice_generico", "apolice.apolice_id = apolice_generico.apolice_id AND apolice_generico.deletado = 0", 'left')
            ->join("apolice_seguro_viagem", "apolice.apolice_id = apolice_seguro_viagem.apolice_id AND apolice_seguro_viagem.deletado = 0", 'left')
            ->where("apolice.pedido_id", $pedido_id);
        return $this->get_all();
    }

    public function defineDataSorteio($pedido_id, $formato = 'Y-m-d', $data_sorteio = null)
    {
        $dados = $this->getDadosCapitalizacao($pedido_id);

        if ( empty($dados) ) {
            return null;
        }

        $data = null;
        $dados = $dados[0];
        $data_compra        = issetor($dados["data_adesao"], date('Y-m-d'));
        $data_ini_vigencia  = issetor($dados["data_ini_vigencia"], date('Y-m-d'));
        $data_fim_vigencia  = issetor($dados["data_fim_vigencia"], date('Y-m-d'));


        // Tipo de Sorteio
        switch ($dados['slug_sorteio']) {

            case 'ultimo_sabado_mes_2_compra':

                // Caso tenha enviado a última data do sorteio
                if ( !empty($data_sorteio) )
                {
                    // deve add o próximo mês
                    $d1 = new DateTime($data_sorteio);
                    $d1->add(new DateInterval('P1M')); // add 1 month

                } else {

                    $d1 = new DateTime($data_compra);
                    $d1->add(new DateInterval('P2M')); // add 2 month

                }

                $data = $this->calculaDataSorteio($d1, 'ultimo_sabado', $formato);
                break;

            case 'sabado_compra':
                #%w = Sunday=0 and Saturday=6
                $d1 = new DateTime($data_compra);
                $data = $this->calculaDataSorteio($d1, 'sabado_seguinte', $formato);
                break;

             case 'sabado_inicio_vigência':
                #%w = Sunday=0 and Saturday=6
                $d1 = new DateTime($data_ini_vigencia);
                $data = $this->calculaDataSorteio($d1, 'sabado_seguinte', $formato);
                break;

             case 'sabado_fim_vigencia':
                #%w = Sunday=0 and Saturday=6
                $d1 = new DateTime($data_fim_vigencia);
                $data = $this->calculaDataSorteio($d1, 'sabado_seguinte', $formato);
                break;
        }

        return $data;

    }

    private function calculaDataSorteio($d1, $tipo, $formato)
    {
        #%w = Sunday=0 and Saturday=6
        // último Sábado do mês
        if ($tipo == 'ultimo_sabado') {

            $d1->add(new DateInterval('P1M')); // add 1 month para encontrar o último sabado
            $d2 = $d1->format('d'); // dia do mês para ser retirado
            $d1->sub(new DateInterval("P{$d2}D")); // -1 dia (último dia do mês)

            // se não for um sábado
            if ($d1->format('w') != 6)
            {
                $d2 = $d1->format('w') + 1; // dia da semana do último dia do mês
                $d1->sub(new DateInterval("P{$d2}D")); // -1 dia (último dia do mês)
            }

        } elseif ($tipo == 'sabado_seguinte') {

            $d2 = 6 - $d1->format('w'); // diferença de dias para chegar ao sábado
            $d1->add(new DateInterval("P{$d2}D"));

        } else {
            return null;
        }

        return $d1->format($formato);
    }

}
