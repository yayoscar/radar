<?php
	require_once("../class/class.grupo.php");
	require_once("../class/class.inputfilter.php");
	
	$classGrupo= new Grupo();
	$ifilter = new InputFilter();
	$datos = $ifilter->process($_REQUEST);
  
  
  
  $addAlumno = $classGrupo->agregarAlumno($datos);

	$return = array();

	switch($addAlumno['code'])
	{
		case 1:
			$return['error'] = 1;
			$return['message'] = "El Alumno tiene coincidencia con otro alumno de la BD";
      break;
		case 2:
			$return['error'] = 0;
			$return['message'] = "El Alumno se agrego correctamente";
      $return["alumno"]=$addAlumno["alumno"];
			break;
		default:
			$return['error'] = 1;
			$return['message'] = "Error en el Sistema";
			break;
	}

	$return = json_encode($return);

	echo $return;
?>
