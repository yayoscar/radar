<?php
require_once("../class/class.radar.php");

$radar=new Radar;

$idencuesta=$_REQUEST["idencuesta"];
$idgeneracion=$_REQUEST["idgeneracion"];

$return=array();

$return=$radar->cargarCharts($idencuesta,$idgeneracion);
$return = json_encode($return);
echo $return;	

?>