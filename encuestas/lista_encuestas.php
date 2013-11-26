<?php
require_once("../class/class.radar.php");

$radar=new Radar;

$return=array();

$return=$radar->listaEncuestas();
$return = json_encode($return);
echo $return;	

?>