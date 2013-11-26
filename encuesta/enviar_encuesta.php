<?php
require_once("../class/class.radar.php");
require_once("../class/class.inputfilter.php");

$radar= new Radar();
$ifilter = new InputFilter();
$encuesta = $ifilter->process($_REQUEST);

$return=array();

if($radar->enviarEncuesta($encuesta)){
  $return["code"]=1;
}
else{
  $return["code"]=0;
}

$return = json_encode($return);
echo $return;
?>