<?php
class Produto_Parceiro_Cancelamento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table = 'produto_parceiro_cancelamento';
    protected $primary_key = 'produto_parceiro_cancelamento_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array();

    //Dados
    public $validate = array(
        array(
            'field' => 'calculo_tipo',
            'label' => 'Tipo de cálculo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seg_antes_hab',
            'label' => 'Cancelamento habilitado antes do início da Vigência',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seg_antes_dias',
            'label' => 'Quantidade maxima de dias antes de iniciar a vigência para o cancelamento',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seg_antes_calculo',
            'label' => 'Modulo de cálculo para multa de cancelamento',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seg_antes_valor',
            'label' => 'Valor em porcentagem ou monetário',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seg_depois_hab',
            'label' => 'Cancelamento habilitado depois do início da Vigência',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seg_depois_dias',
            'label' => 'Quantidade maxima de dias depois de iniciar a vigência para o cancelamento',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seg_depois_dias_carencia',
            'label' => 'Carência de dias p/ utilização do cálculo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seg_depois_calculo',
            'label' => 'Modulo de cálculo para multa de cancelamento depois do cancelamento',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'seg_depois_valor',
            'label' => 'Valor em porcentagem ou monetário depois do cancelamento',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inad_hab',
            'label' => 'Habilitado o cancelamento para inadimplência',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inad_max_dias',
            'label' => 'Máximo de dias de atraso para o auto-cancelamento',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inad_max_parcela',
            'label' => 'Máximo de parcelas em atraso para o auto-cancelamento',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inad_reativacao_hab',
            'label' => 'Reativação do seguro por Inadimplencia',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inad_reativacao_max_dias',
            'label' => 'Máximo de dias cancelado que permite a reativação',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inad_reativacao_max_parcela',
            'label' => 'Máximo de parcelas em atraso para permitir a reativação',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inad_reativacao_calculo',
            'label' => 'modulo de cálculo para multa de reativação ',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'inad_reativacao_valor',
            'label' => 'Valor em porcentagem ou monetário da multa para reativação',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'indenizacao_hab',
            'label' => 'Indenização',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'cancel_via_admin',
            'label' => 'cancelar via admin',
            'rules' => 'required',
            'groups' => 'default'
        )
    );

    //Get dados
    public function get_form_data($just_check = false)
    {
        //Dados
        $data =  array(
            'produto_parceiro_id' => $this->input->post('produto_parceiro_id'),
            'calculo_tipo' => $this->input->post('calculo_tipo'),
            'seg_antes_hab' => $this->input->post('seg_antes_hab'),
            'seg_antes_dias' => $this->input->post('seg_antes_dias'),
            'seg_antes_calculo' => $this->input->post('seg_antes_calculo'),
            'seg_antes_valor' => app_unformat_currency($this->input->post('seg_antes_valor')),
            'seg_depois_hab' => $this->input->post('seg_depois_hab'),
            'seg_depois_dias' => $this->input->post('seg_depois_dias'),
            'seg_depois_dias_carencia' => $this->input->post('seg_depois_dias_carencia'),
            'seg_depois_calculo' => $this->input->post('seg_depois_calculo'),
            'seg_depois_valor' => app_unformat_currency($this->input->post('seg_depois_valor')),
            'inad_hab' => $this->input->post('inad_hab'),
            'inad_max_dias' => $this->input->post('inad_max_dias'),
            'inad_max_parcela' => $this->input->post('inad_max_parcela'),
            'inad_reativacao_hab' => $this->input->post('inad_reativacao_hab'),
            'inad_reativacao_max_dias' => $this->input->post('inad_reativacao_max_dias'),
            'inad_reativacao_max_parcela' => $this->input->post('inad_reativacao_max_parcela'),
            'inad_reativacao_calculo' => $this->input->post('inad_reativacao_calculo'),
            'inad_reativacao_valor' => app_unformat_currency($this->input->post('inad_reativacao_valor')),
            'indenizacao_hab' => $this->input->post('indenizacao_hab'),
            'cancel_via_admin' => $this->input->post('cancel_via_admin'),
        );
        return $data;
    }
    function get_by_id($id)
    {
        return $this->get($id);
    }


    function  filter_by_produto_parceiro($produto_parceiro_id)
    {
        $this->_database->where('produto_parceiro_id', $produto_parceiro_id);
        return $this;
    }
}
