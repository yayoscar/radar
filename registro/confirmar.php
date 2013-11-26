<?php
require_once("../class/class.radar.php");

list($idalumno,$uid)=array_values((array) $_REQUEST);
$radar=new Radar();

$radar->vincularCuenta($idalumno,$uid);



?>