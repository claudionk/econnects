<?php
Class Recorrencia_Model extends MY_Model
{
  //Dados da tabela e chave primária
  protected $_table = 'recorrencia';
  protected $primary_key = 'recorrencia_id';

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

  );


  public function insertRecorrencia( $data ) {

    $recorrencia = $this->get_many_by( array('pedido_id' => $data["pedido_id"] ) ) ;

    if($recorrencia){
      return;
    }
    else{
       $this->insert($data, TRUE);
    }
  }

}



