<?php
include_once("../class/auth.class.php");
require_once("../class/class.inputfilter.php");

$auth = new Auth;

$ifilter = new InputFilter();
$_REQUEST = $ifilter->process($_REQUEST);

$llave=$_REQUEST["llave"];

$key = $auth->isResetValid($llave);

$return = array();

switch($key['code'])
{
	case 0:
		$return['error'] = 1;
		$return['message'] = "Este equipo esta bloqueado por el sistema. Intente de nuevo en 30 minutos.";
		break;
	case 2:
		$return['error'] = 1;
		$return['message'] = "C&oacute;digo de restablecimiento incorrecto";
		break;
	case 3:
		$return['error'] = 1;
		$return['message'] = "El C&oacute;digo de restablecimiento ha caducado";
		break;
	case 4:
		$return['error'] = 0;
		$return['message'] = "C&oacute;digo de restablecimiento v&aacute;lido. Espere un momento...";
		break;
	default:
		$return['error'] = 1;
		$return['message'] = "Error en el Sistema";
		break;
}

$return = json_encode($return);

echo $return;

?>