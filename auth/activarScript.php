<?php
	require_once("../class/auth.class.php");
	require_once("../class/class.inputfilter.php");
	$auth = new Auth;
	
	
	$ifilter = new InputFilter();
	$_REQUEST = $ifilter->process($_REQUEST);
	
	$llaveactiva=$_REQUEST["llave"];
	$activate = $auth->activate($llaveactiva);
	$return = array();
	
	switch($activate['code'])
	{
		case 0:
			$return['error'] = 1;
			$return['message'] = "Este equipo esta bloqueado por el sistema. Intente de nuevo en 30 minutos.";
			break;
		case 2:
			$return['error'] = 1;
			$return['message'] = "C&oacute;digo incorrecto";
			break;
		case 3:
			$return['error'] = 1;
			$return['message'] = "La cuenta ya est&aacute; activa";
			break;
		case 4:
			$return['error'] = 1;
			$return['message'] = "El c&oacute;digo ha caducado, intente reenviarselo de nuevo";
			break;
		case 5:
			$return['error'] = 0;
			$return['message'] = "La cuenta se ha activado, ya puede iniciar sesi&oacute;n";
			break;
		default:
			$return['error'] = 1;
			$return['message'] = "Error en el Sistema";
			break;
	}
	
	$return = json_encode($return);
	
	echo $return;
?>