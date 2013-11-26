<?php
	require_once("../class/auth.class.php");
  require_once("../class/class.plantel.php");
	require_once("../class/class.inputfilter.php");
	
	$classPlantel = new Plantel;
	$ifilter = new InputFilter();
	$_REQUEST = $ifilter->process($_REQUEST);

	$plantel = $_REQUEST['plantel'];
	$numero = $_REQUEST['numero'];
  $admin = $_REQUEST["admin"];
  $estado = $_REQUEST["estado"];
  $nombre= utf8_decode($_REQUEST["nombre"]);

	$addPlantel = $classPlantel->agregarPlantel($plantel,$numero,$admin,$estado,$nombre);

	$return = array();

	switch($addPlantel['code'])
	{
		case 1:
			$return['error'] = 1;
			$return['message'] = "El plantel ya ha sido agregado.";
      break;
		case 2:
			$return['error'] = 0;
			$return['message'] = "El plantel se agrego correctamente";
      $return['plantel'] = $addPlantel["plantel"];
			break;
		case 3:
			$return['error'] = 0;
			$return['message'] = "El plantel se agrego correctamente, y el correo se agrego a la BD con el password <strong>antofagasta</strong>, debera ser activado posteriormente";
      $return['plantel'] = $addPlantel["plantel"];
			break;
    case 4:
			$return['error'] = 1;
			$return['message'] = "El Email se encuentra vinculado a otro plantel";
			break;
    case 5:
			$return['error'] = 0;
			$return['message'] = "El plantel se agrego correctamente";
      $return['plantel'] = $addPlantel["plantel"];
      break;
		default:
			$return['error'] = 1;
			$return['message'] = "Error en el Sistema";
			break;
	}

	$return = json_encode($return);

	echo $return;
?>
