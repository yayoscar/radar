<?php
require_once("../class/class.radar.php");
require_once("../class/class.inputfilter.php");

$radar= new Radar();
$ifilter = new InputFilter();

$_REQUEST = $ifilter->process($_REQUEST);
$idencuesta=$_REQUEST["idencuesta"];
$idalumno=$_REQUEST["idalumno"];

$return["preguntas"]=$radar->listaPreguntas($idencuesta);
$return["estado"]=$radar->estadoEncuesta($idencuesta,$idalumno);

$return = json_encode($return);
echo $return;


?>