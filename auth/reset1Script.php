<?php
include_once("../class/auth.class.php");
require_once("../class/class.inputfilter.php");

$auth = new Auth;

$ifilter = new InputFilter();
$_REQUEST = $ifilter->process($_REQUEST);
$email=$_REQUEST["email"];

$email = $auth->requestReset($email);

$return = array();

switch($email['code'])
{
	case 0:
		$return['error'] = 1;
		$return['message'] = "Este equipo esta bloqueado por el sistema. Intente de nuevo en 30 minutos.";
		break;
	case 2:
		$return['error'] = 1;
		$return['message'] = "El Email es incorrecto";
		break;
	case 3:
		$return['error'] = 1;
		$return['message'] = "Una solictud para restablecer password se env&iacute;o en las &uacute;ltimas 24 horas";
		break;
	case 4:
		$return['error'] = 0;
		$return['message'] = "La solicitud para restablecer su password se envio al email " . $email['email'];
		break;
	default:
		$return['error'] = 1;
		$return['message'] = "Errir del Sistema";
		break;
}

$return = json_encode($return);

echo $return;

?>