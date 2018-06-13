<?php
Class Usuario_Acl_Recurso_Model extends MY_Model
{

    protected $_table = 'usuario_acl_recurso';
    protected $primary_key = 'usuario_acl_recurso_id';

    protected $return_type = 'array';
    protected $soft_delete = TRUE;

    protected $soft_delete_key = 'deletado';

    protected $update_at_key = 'alteracao';
    protected $create_at_key = 'criacao';


    public $validate = array(
        array(
            'field' => 'pai_id',
            'label' => 'Principal (Pai)',
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
            'field' => 'externo',
            'label' => 'Acesso Externo',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'target',
            'label' => 'Destino (target)',
            'rules' => 'required',
            'groups' => 'default'
        ),
        array(
            'field' => 'exibir_menu',
            'label' => 'Exibir no Menu',
            'rules' => 'required',
            'groups' => 'default'
        )

    );

    public function get_form_data($just_check = false){

        $data =  array(
            'pai_id' => $this->input->post('pai_id'),
            'nome' => $this->input->post('nome'),
            'slug' => $this->input->post('slug'),
            'controller' => $this->input->post('controller'),
            'acao' => $this->input->post('acao'),
            'parametros' => $this->input->post('parametros'),
            'url' => $this->input->post('url'),
            'externo' => $this->input->post('externo'),
            'target' => $this->input->post('target'),
            'exibir_menu' => $this->input->post('exibir_menu'),
        );


        return $data;
    }


    public function getRecursosSelect($pai_id = 0, $espacos = 0, &$arr){

        if($pai_id == 0){
            $arr = array(0 => "Principal (Raiz)");
            $espaco2 = '&nbsp;';
        }else{
            $espaco2 = '&nbsp;';
            for($s = 0; $s < $espacos;$s++){
                $espaco2 .= '--';
            }
            $espaco2 .= '>&nbsp;';
        }


        $acesso = $this->filter_by_pai($pai_id)->order_by('ordem')->get_all();

        if($acesso){
            foreach ($acesso as $linha) {
                $arr[$linha['usuario_acl_recurso_id']] = $espaco2. $linha['nome'];
                $this->getRecursosSelect($linha['usuario_acl_recurso_id'], $espacos +1, $arr);
            }
        }


    }

    /**
     * Retorna recursos por usuário
     * @param int $usuario_id
     * @param int $pai_id
     * @param $arr
     * @param int $exibir_menu
     */
    public function getRecursosUsuario($usuario_id = 0, $pai_id = 0, &$arr, $exibir_menu = 1 )
    {
        $this->load->model("usuario_acl_permissao_model", "usuario_acl_permissao");
        $this->load->model("usuario_model", "usuario");

        $usuario = $this->usuario->get($usuario_id);


        $acesso = $this
            ->filter_by_pai($pai_id)
            ->filter_by_menu($exibir_menu)
            ->order_by('ordem')
            ->get_all();


        if($acesso)
        {
            foreach ($acesso as $linha)
            {
                $permissao = $this->usuario_acl_permissao->get_by(array(
                    'usuario_acl_recurso_id' => $linha['usuario_acl_recurso_id'],
                    'usuario_acl_acao_id' => 1,
                    'usuario_acl_tipo_id' => $usuario['usuario_acl_tipo_id']
                ));

                if($permissao)
                {
                    $linha['itens'] = array();
                    $linha['controllers'] = array();
                    $this->getRecursosUsuario($usuario_id, $linha['usuario_acl_recurso_id'], $linha['itens'], $exibir_menu);
                    $this->getRecursosController($usuario_id, $linha['usuario_acl_recurso_id'], $pai_id, $linha['controllers']);
                    $arr[] = $linha;
                }

            }
        }


    }

    public function getRecursosController($usuario_id = 0, $usuario_acl_recurso_id = 0, $pai_id = 0, &$controller ){

        if($usuario_acl_recurso_id == 0) {
            $acesso = $this->filter_by_pai($pai_id)->order_by('ordem')->get_all();
        }else{
            $acesso = $this->filter_by_id($usuario_acl_recurso_id)->filter_by_pai($pai_id)->order_by('ordem')->get_all();
        }

        if($acesso){
            foreach ($acesso as $linha) {

                if((!empty($linha['controller'])) && !in_array($linha['controller'], $controller)){
                    $controller[] = $linha['controller'];
                }
                $this->getRecursosController($usuario_id, 0, $linha['usuario_acl_recurso_id'], $controller);
            }
        }


    }



    function  filter_by_pai($pai_id){

        $this->_database->where('pai_id', $pai_id);

        return $this;
    }

    function  filter_by_menu($exibir){

        $this->_database->where('exibir_menu', $exibir);

        return $this;
    }



    function  filter_by_id($usuario_acl_recurso_id){

        $this->_database->where('usuario_acl_recurso_id', $usuario_acl_recurso_id);

        return $this;
    }


    /**
     * Retorna de forma recursiva
     */
    public function retornarRecursivamente($id_permissao = 0)
    {
        $data = $this
            ->order_by('ordem')
            ->get_many_by(array('pai_id' => 0));


        $ret = array();
        foreach($data as $permissao)
        {
            $ret[] = $this->retornaFilhosArray($permissao, $id_permissao);
        }

        return $ret;
    }

    /**
     * Retorna array dos filhos de forma recursiva
     * @param $data
     * @return mixed
     */
    private function retornaFilhosArray(&$data, $id_permissao = 0)
    {
        $this->load->model("usuario_acl_recurso_acao_model","usuario_acl_recurso_acao");
        $this->load->model("usuario_acl_permissao_model","usuario_acl_permissao");


        $data['filhos'] = $this
            ->order_by('ordem')
            ->get_many_by(array('pai_id' => $data[$this->primary_key]));

        $data['acoes'] = $this->usuario_acl_recurso_acao
            ->with_foreign()
            ->get_many_by(array('usuario_acl_recurso.usuario_acl_recurso_id' => $data[$this->primary_key]));


        $i = 0;
        foreach($data['filhos'] as $permissao)
        {
            $data['filhos'][$i] = $this->retornaFilhosArray($permissao, $id_permissao);
            $i++;
        }

        $i = 0;
        foreach($data['acoes'] as $acao)
        {

            $permi = $this->usuario_acl_permissao->get_by(array(
                'usuario_acl_tipo_id' => $id_permissao,
                'usuario_acl_acao_id' => $acao['usuario_acl_acao_id'],
                'usuario_acl_recurso_id' => $data[$this->primary_key]
            ));

            $data['acoes'][$i]['permitido'] = false;
            if($permi)
                $data['acoes'][$i]['permitido'] = true;

            $i++;
        }

        return $data;
    }
}
