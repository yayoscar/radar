<?php
require_once("../class/class.grupo.php");

$plantel=$_REQUEST["plantel"];
$grupo=new Grupo();


$return=array();

$return=$grupo->listaGrupos($plantel);

$return = json_encode($return);
echo $return;	

?>