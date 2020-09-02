<?php 
foreach ($campos as $campo) {

  $data_campo = array();
  $data_campo["row"] = $row;
  $data_campo["field_name"] = $campo["nome_banco"];
  $data_campo["field_label"] = isset($campo["label"]) ? $campo["label"] : $campo["nome"];
  $data_campo["list"] = isset($list) ? $list : array();
  $data_campo["tamanho"] = $campo["tamanho"];

  if( strpos( $campo["classes"], "inputmask-date" ) !== false ) {
    $data_campo["class"] = "datepicker " . $campo["classes"];
  } else {
    $data_campo["class"] = $campo["classes"];
  }
  $data_campo["class"] .= ' ' . $campo["nome_banco"];
  $data_campo["class"] .= ' ' . implode(" ",explode("|", $campo["classe_css"]));
  $data_campo["opcoes"] = $campo["opcoes"];
  $this->load->view( "admin/campos_sistema/". $campo["slug"], $data_campo );
  
  /*
  $data_campo = array();
  $data_campo["row"] = $row;
  $data_campo["field_name"] = $campo["campo_nome_banco"];
  $data_campo["field_label"] = isset($campo["label"]) ? $campo["label"] : $campo["campo_nome"];
  $data_campo["list"] = isset($list) ? $list : array();
  $data_campo["tamanho"] = $campo["tamanho"];
  if( strpos( $campo["campo_classes"], "inputmask-date" ) !== false ) {
    $data_campo["class"] = "datepicker " . $campo["campo_classes"];
  } else {
    $data_campo["class"] = $campo["campo_classes"];
  }
  $data_campo["opcoes"] = $campo["campo_opcoes"];
  $this->load->view("admin/campos_sistema/". $campo["campo_slug"], $data_campo);
  */


}
?>

