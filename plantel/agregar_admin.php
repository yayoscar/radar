<?php
	require_once("../class/auth.class.php");
  require_once("../class/class.plantel.php");
	require_once("../class/class.inputfilter.php");
	
	$classPlantel = new Plantel;
	$ifilter = new InputFilter();
	$_REQUEST = $ifilter->process($_REQUEST);

	$idplantel = $_REQUEST['idplantel'];
  $idplantel = explode("plantel", $idplantel);
  $idplantel=$idplantel[1];
	$admin = $_REQUEST["admin"];
  
	$addPlantel = $classPlantel->agregarAdmin($idplantel,$admin);

	$return = array();

	switch($addPlantel['code'])
	{
		case 2:
			$return['error'] = 0;
			$return['message'] = "El Administrador se agrego correctamente";
      break;
		case 3:
			$return['error'] = 0;
			$return['message'] = "El administrador se agrego a la BD con el password <strong>prototipos</strong>, debera ser activado posteriormente";
      
			break;
    case 4:
			$return['error'] = 1;
			$return['message'] = "El Email es del Administrador General";
			break;
    case 5:
			$return['error'] = 0;
			$return['message'] = "El Administrador se agrego correctamente";
      
      break;
		default:
			$return['error'] = 1;
			$return['message'] = "Error en el Sistema";
			break;
	}

	$return = json_encode($return);

	echo $return;
?>
