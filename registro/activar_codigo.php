<?php
require_once("../class/class.radar.php");
require_once("../class/class.inputfilter.php");

$ifilter = new InputFilter();
$codigo=$_REQUEST["codigo"];
$radar=new Radar();

$return=array();

if($idalumno=$radar->buscarCodigo($codigo))
{
  $return["code"]=1;
  $return["info"]=$radar->infoActivarAlumno($idalumno);
}
else
{
  $return["code"]=0;
}

$return = json_encode($return);
echo $return;	
?>