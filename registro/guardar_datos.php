<?php
require_once("../class/class.radar.php");
require_once("../class/class.inputfilter.php");

$ifilter = new InputFilter();
$datos = $ifilter->process($_REQUEST);
$radar=new Radar();

$data=$radar->guardarDatosAlumno($datos);

?>