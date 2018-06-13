<?php
/**
 * Created by PhpStorm.
 * User: Carlos
 * Date: 13/05/14
 * Time: 17:48
 */
Class App_mail {

    protected $config;
    protected $CI;
    protected $template = false;
    protected $data;
    protected $subject;
    protected $from;
    protected $from_name = array();
    protected $to = array();
    protected $cc = array();
    protected $bcc = array();




    public function __construct( $config = array() )
    {
        $this->config = $config;

        $this->CI = &get_instance();

        /*$config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.mandrillapp.com',
            'smtp_port' => 587,
            'smtp_user' => 'carlos@zazz.com.br',
            'smtp_pass' => 'cwKv2r7T9FC6EoFHSORR6Q',
            'mailtype'  => 'html'

        );
        $config = array();
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "ssl://smtp.gmail.com";
        $config['smtp_port'] = "465";
        $config['smtp_user'] = "carlos@zazz.com.br";
        $config['smtp_pass'] = "zazz1q2w3e";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";*/

        $config = array();
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "smtp.topride.com.br";
        $config['smtp_port'] = "587";
        $config['smtp_user'] = "contato@topride.com.br";
        $config['smtp_pass'] = "senha20031982";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";


        $this->CI->load->library('email', $config);


    }


    public function get($name)
    {
        if($this->is_set($name)){
            return $this->data[$name];
        }

        return null;
    }

    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function set_from($email, $name = '')
    {
        $this->from = $email;
        $this->from_name = $name;

    }

    public function set_subject($subject)
    {
        $this->subject = $subject;


    }

    public function add_to($email)
    {

        $this->to[] = $email;

    }
    public function add_cc($email)
    {

        $this->cc[] = $email;

    }
    public function add_bcc($email)
    {

        $this->bcc[] = $email;

    }
    public function is_set($name)
    {
        return isset($this->data[$name]);
    }

    public function set_template($view)
    {
        $this->template = $view;

        return $this;
    }

    public function clear(){

        $this->data = array();
        $this->template = false;
        $this->subject = '';
        $this->to = array();
        $this->CI->email->clear();

        return $this;

    }

    public function send(){

        $message = $this->CI->load->view($this->template, $this->data, true);


        $this->CI->email->from($this->from, $this->from_name);
        $this->CI->email->to($this->to);
        $this->CI->email->cc($this->cc);
        $this->CI->email->bcc($this->bcc);

        $this->CI->email->subject($this->subject);
        $this->CI->email->message($message);


        return $this->CI->email->send();
    }

    public function get_debug(){

        return $this->CI->email->print_debugger();
    }


}