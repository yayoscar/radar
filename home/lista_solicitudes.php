<?php
require_once("../class/class.radar.php");

$idalumno=$_REQUEST["idalumno"];
$radar=new Radar();


$return=array();

$return=$radar->listaSolicitudes($idalumno);

$return = json_encode($return);
echo $return;	

?>