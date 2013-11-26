<?php
require_once("../class/class.radar.php");


$idgeneracion=$_REQUEST["generacion"];
$idencuesta=$_REQUEST["idencuesta"];
$idencuesta = explode("c", $idencuesta);
$idencuesta=$idencuesta[1];
$radar=new Radar;

$return=array();

$return["code"]=$radar->asignarEncuesta($idencuesta,$idgeneracion);

$return = json_encode($return);
echo $return;	

    
?>