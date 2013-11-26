<?php
require_once("../class/class.plantel.php");

$plantel=new Plantel();

$return=array();

$return=$plantel->listaPlanteles();

$return = json_encode($return);
echo $return;	

?>