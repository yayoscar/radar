<?php
	require_once("../class/class.grupo.php");
	require_once("../class/class.inputfilter.php");
	
	$classGrupo= new Grupo();
	$ifilter = new InputFilter();
	$_REQUEST = $ifilter->process($_REQUEST);
  
 $datos["plantel"]=$_REQUEST["plantel"];
 $datos["especialidad"]=$_REQUEST["especialidad"];
 $datos["grupo"]=$_REQUEST["grupo"];
 $datos["turno"]=$_REQUEST["turno"];
 $datos["generacion"]=$_REQUEST["generacion"];
  
  $addGrupo = $classGrupo->agregarGrupo($datos);

	$return = array();

	switch($addGrupo['code'])
	{
		case 1:
			$return['error'] = 1;
			$return['message'] = "El Grupo ya ha sido agregado.";
      $return["idgrupo"] = $addGrupo["id"];
			break;
		case 2:
			$return['error'] = 0;
			$return['message'] = "El Grupo se agrego correctamente";
      $return["idgrupo"] =$addGrupo["idgrupo"];
      $return["grupo"]=$addGrupo["grupo"];
			break;
		default:
			$return['error'] = 1;
			$return['message'] = "Error en el Sistema";
			break;
	}

	$return = json_encode($return);

	echo $return;
?>
