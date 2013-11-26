<?php
require_once("../class/class.grupo.php");

$idgrupo=$_REQUEST["idgrupo"];
$grupo=new Grupo();


$return=array();

$return=$grupo->listaAlumnos($idgrupo);

$return = json_encode($return);
echo $return;	

?>