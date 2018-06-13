<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{
    /**
     * Retorna array de erros
     * @return array|bool
     */
    function error_array()
    {
        if (count($this->_error_array) === 0)
            return FALSE;
        else
            return $this->_error_array;
    }

    /**
     * Roda validação
     * @param string $module
     * @param string $group
     * @return bool
     */
    function run($module = '', $group = '') {
        (is_object($module)) AND $this->CI = &$module;
        return parent::run($group);
    }
    /**
     * Valida se o endereço de e-mail ja esta sendo usado
     * @param $email
     * @return bool
     */
    public function check_email_usuario_exists($email)
    {

        $this->set_message('check_email_usuario_exists', 'O %s ja esta cadastrado.');
        $this->CI->load->model('usuario_model');
        return !$this->CI->usuario_model->check_email_exists($email);
    }

    /**
     * Match one field to another
     *
     * @access	public
     * @param	string
     * @param	field
     * @return	bool
     */
    public function check_password_confirm($str, $field)
    {



        $this->set_message('check_password_confirm', 'As Senhas digitadas não conferem.');
        if ( ! isset($_POST[$field]))
        {
            return FALSE;
        }

        $field = $_POST[$field];
        return ($str !== $field) ? FALSE : TRUE;
    }

    /**
     * Valida se o CPF do usuárioja esta sendo usado
     * @param $email
     * @return bool
     */
    public function check_cpf_usuario_exists($email)
    {

        $this->set_message('check_cpf_usuario_exists', 'O %s ja esta cadastrado.');
        $this->CI->load->model('usuario_model');
        return !$this->CI->usuario_model->check_cpf_exists($email);
    }

    function validate_data($date) 
    {
        $this->set_message('validate_data', 'O Campo %s não é válido');
        if(@checkdate(substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4)))
        {
           return true;
        } 
        else 
        {
            return false;
        }
    }

    function validate_celular($celular)
    {
        $this->set_message('validate_celular', 'O Campo %s não é um telefone celular válido');

        $celular = app_retorna_numeros($celular);

        if($celular)
        {
            if((int) $celular[2] != 9)
            {
                return false;
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    function validate_data_menor_hoje($date)
    {
        $date_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $date= mktime(0, 0, 0, substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4));
        $this->set_message('validate_data_menor_hoje', 'O Campo %s não pode ser menor que hoje');
        if($date < $date_atual)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function valid_vencimento_cartao($date)
    {
        $dt = explode('/', $date);

        if(count($dt) != 2){
            $this->set_message('valid_vencimento_cartao', 'O Campo %s não é válido');
            return false;
        }else {
            if((int)trim($dt[0]) < 1 || (int)trim($dt[0]) > 12){
                $this->set_message('valid_vencimento_cartao', 'O Campo %s não é válido digite (MM/AAAA)');
                return false;
            }elseif(strlen(trim($dt[1])) != 4){
                $this->set_message('valid_vencimento_cartao', 'O Campo %s não é válido digite (MM/AAAA)');
                return false;
            }else {
                $date_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $date = mktime(0, 0, 0, (int)trim($dt[0]), 1, (int)trim($dt[1]));
                $this->set_message('valid_vencimento_cartao', 'Seu Cartão esta vencido');
                if ($date < $date_atual) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }




    //Valida se o cnpj ou cpf são únicos
    public function validate_cnpj_cpf($valor)
    {
        $this->set_message('validate_cnpj_cpf', 'O %s não é válido');
        $this->CI->load->model('cliente_model');
        $valor = app_retorna_numeros($valor);

        if(strlen($valor) == 11)
        {
            return app_validate_cpf($valor);
        }
        else
        {
            return app_validate_cnpj($valor);
        }

    }
    //Valida se o cnpj ou cpf são únicos
    public function valida_cnpj_cpf_unico($valor)
    {
        $this->set_message('valida_cnpj_cpf_unico', 'O %s ja esta cadastrado.');
        $this->CI->load->model('cliente_model');
        if($this->CI->cliente_model->quantidade_cnpj_cpf($valor) == 0)
            return true;
        return false;
    }
    public function check_email_usuario_owner($email)
    {

        $this->set_message('check_email_usuario_owner', 'O %s ja esta cadastrado.');
        $this->CI->load->model('usuario_model');
        //verifica se o email existe
        if($this->CI->usuario_model->check_email_exists($email)){

            //verifica se é o dono do e-mail
            if($this->CI->usuario_model->check_email_owner($email, $this->CI->input->post('usuario_id') )){

                return true;

            }else {

                return false;
            }
        }else {

            return true;
        }

    }

    public function check_cpf_usuario_owner($cpf)
    {

        $this->set_message('check_cpf_usuario_owner', 'O %s ja esta cadastrado.');
        $this->CI->load->model('usuario_model');
        //verifica se o email existe
        if($this->CI->usuario_model->check_cpf_exists($cpf)){

            //verifica se é o dono do e-mail
            if($this->CI->usuario_model->check_cpf_owner($cpf, $this->CI->input->post('usuario_id') )){

                return true;

            }else {

                return false;
            }
        }else {

            return true;
        }

    }

    /**
     * Valida se o endereço de e-mail ja esta sendo usado
     * @param $email
     * @return bool
     */
    public function check_email_agente_exists($email)
    {

        $this->set_message('check_email_agente_exists', 'O %s ja esta cadastrado.');
        $this->CI->load->model('agente_model');
        return !$this->CI->agente_model->check_email_exists($email);
    }

    public function check_email_agente_owner($email)
    {

        $this->set_message('check_email_agente_owner', 'O %s ja esta cadastrado.');
        $this->CI->load->model('agente_model');
        //verifica se o email existe
        if($this->CI->agente_model->check_email_exists($email)){

            //verifica se é o dono do e-mail
            if($this->CI->agente_model->check_email_owner($email, $this->CI->input->post('agente_id') )){

                return true;

            }else {

                return false;
            }
        }else {

            return true;
        }

    }
    /**
     * Valida se possui espaços ou não
     * @param $string
     * @return bool
     */
    function valida_sem_espaco($str)
    {
        $value = strrpos($str, ' ');
        $this->set_message('valida_sem_espaco', 'O %s nao pode possuir espacos.');
        

        if($value == false)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Valida se o endereço de e-mail ja esta sendo usado
     * @param $email
     * @return bool
     */
    public function check_email_lojista_exists($email)
    {

        $this->set_message('check_email_lojista_exists', 'O %s ja esta cadastrado.');
        $this->CI->load->model('lojista_model');
        return !$this->CI->lojista_model->check_email_exists($email);
    }

    public function check_email_lojista_owner($email)
    {

        $this->set_message('check_email_lojista_owner', 'O %s ja esta cadastrado.');
        $this->CI->load->model('lojista_model');
        //verifica se o email existe
        if($this->CI->lojista_model->check_email_exists($email)){

            //verifica se é o dono do e-mail
            if($this->CI->lojista_model->check_email_owner($email, $this->CI->input->post('lojista_id') )){

                return true;

            }else {

                return false;
            }
        }else {

            return true;
        }

    }



    public function validate_cpf($cpf){

        $this->set_message('validate_cpf', 'O número de CPF informado no campo %s é inválido.');
        return app_validate_cpf($cpf);

    }
    public function validate_cnpj($cnpj){
        $this->set_message('validate_cnpj', 'O número do CNPJ informado no campo %s é inválido.');
        return app_validate_cnpj($cnpj);
    }

    public function in_list($str, $list){

        $this->set_message('in_list', 'O valor informado no campo %s não foi encontrado.');
        $list = explode(',', $list);

        if(in_array($str, $list)){

            return true;

        }else {

            return false;
        }

        exit;
    }
    public function validate_mysql_date($string)
    {
        $this->set_message('validate_mysql_date', 'A data informada no campo %s é inválida.');

        if(date_create_from_format("Y-m-d H:i:s", $string) != false)
            return true;
        return false;
    }



    function recaptcha_matches()
    {
        $CI =& get_instance();
        $CI->config->load('recaptcha');

        $public_key = $CI->config->item('recaptcha_public_key');
        $private_key = $CI->config->item('recaptcha_private_key');

        $response_field = $CI->input->post('recaptcha_response_field');
        $challenge_field = $CI->input->post('recaptcha_challenge_field');

        $response = recaptcha_check_answer($private_key,
            $_SERVER['REMOTE_ADDR'],
            $challenge_field,
            $response_field);
        if ($response->is_valid)
        {
            return TRUE;
        }
        else
        {
            $this->recaptcha_error = $response->error;
            $this->set_message('recaptcha_matches', 'A código da imagem %s está incorreto, por favor, tente novamente.');
            return FALSE;
        }
    }

    public function check_parceiro_cnpj_exists($cnpj)
    {
        $cnpj = app_clear_number($cnpj);
        $this->set_message('check_parceiro_cnpj_exists', 'O número de %s  ja esta cadastrado.');
        $this->CI->load->model('parceiro_model');
        return !$this->CI->parceiro_model->check_row_exists('cnpj', $cnpj);
    }

    public function check_parceiro_cnpj_owner($cnpj)
    {
        $cnpj = app_clear_number($cnpj);
        $this->set_message('check_parceiro_cnpj_owner', 'O número de CNPJ ja esta cadastrado.');


        $this->CI->load->model('parceiro_model');

        //verifica se o cnpj existe
        if($this->CI->parceiro_model->check_row_exists('cnpj', $cnpj)){


            $form_parceiro_id = $this->CI->input->post('parceiro_id');

            $parceiro = $this->CI->parceiro_model->get($form_parceiro_id);

            //verifica se é o dono do cnpj
            if( isset($parceiro['cnpj']) && ($parceiro['cnpj'] == $cnpj) ){

                return true;

            }else {

                return false;
            }
        }else {

            return true;
        }

    }


    public function enum($data, $tipos)
    {
        $tipos = explode(",", $tipos);

        if(in_array($data, $tipos))
        {
            return true;
        }
        else
        {
            return false;
        }
        var_dump($data, $tipos);exit;
    }


    public function validate_contato($data){

        $tipo = $this->CI->input->post('contato_tipo_id');

        if($tipo){
            switch ((int)$tipo) {
                case 1:
                    $this->set_message('validate_contato', 'E-mail do campo Contato é inválido');
                    return $this->valid_email($data);
                    break;
                case 2:
                    $this->set_message('validate_contato', 'Celular do campo Contato é inválido ');
                    $result = $this->min_length(app_retorna_numeros($data), 10);
                    if($result){
                        return $this->max_length(app_retorna_numeros($data), 11);
                    }else{
                      return false;
                    }
                    break;
                case 3:
                case 4:
                    $this->set_message('validate_contato', 'Telefone do campo Contato é inválido ');
                    return $this->min_length(app_retorna_numeros($data), 10);
                    break;
                    break;
            }

        }else{
            $this->set_message('validate_contato', 'O Campo tipo de contato é obrigátório');
        }

    }



}