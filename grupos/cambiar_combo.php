<?php
  require_once("../class/class.grupo.php");
  
  $grupo=new Grupo();
  
  $combo=$_REQUEST["combo"];
  
  $return=array();
  
  if($combo=="especialidad")
  {
    $return=$grupo->listaEspecialidad();
  }
  elseif($combo=="generacion")
  {
    $return=$grupo->listaGeneracion();
  }
  
  $return = json_encode($return);
  echo $return;
  
?>