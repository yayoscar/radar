<?php
  require_once("../class/class.plantel.php");
  
  $grupo=new Plantel();
  
 $return=array();
  
 $return=$grupo->listaEstados();
  
 $return = json_encode($return);
 echo $return;
  
?>