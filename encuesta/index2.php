<?php
include("../class/class.radar.php");
include("../class/config.php");

$radar = new Radar;
$texto="He completado la encuesta en radar|dgeti";
$radar->fbPublicar($texto);

?>