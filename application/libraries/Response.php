<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Response
{
    private $mensagem = "Não foi possível efetuar a consulta.";
    private $dados = array();
    private $status = false;
    private $error;

    /**
     * @return string
     */
    public function getMensagem()
    {
        return $this->mensagem;
    }

    /**
     * @param string $mensagem
     */
    public function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;
    }

    /**
     * @param array $data
     */
    public function setDados($data)
    {
        $this->dados = $data;
    }

    /**
     * @param array $data
     */
    public function setError($data)
    {
        $this->error = $data;
    }

    /**
     * Codifica para UTF-8
     * @return array
     */
    public function getError()
    {
        if(!$this->error)
            return array();
        return $this->error;
    }

    /**
     * Codifica para UTF-8
     * @return array
     */
    public function getDados()
    {
        return $this->dados;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Retorna JSON
     * @return string
     */
    public function getJSON()
    {
        $data = array();
        $data['status'] = $this->isStatus();

        if($data['status'])
        {
            $data['dados'] = $this->getDados();
        }
        else
        {
            $data['erros'] = $this->getError();
            $data['mensagem'] = $this->getMensagem();
        }

        return json_encode($data);
    }

}

