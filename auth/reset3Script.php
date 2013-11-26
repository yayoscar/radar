<?php
include_once("../class/auth.class.php");
require_once("../class/class.inputfilter.php");

$auth = new Auth;

$ifilter = new InputFilter();
$_REQUEST = $ifilter->process($_REQUEST);


$key=$_REQUEST["llave2"];
$password=$_REQUEST["password_sha1"];

$reset = $auth->resetPass($key, $password);

$return = array();

switch($reset['code'])
{
	case 0:
		$return['error'] = 1;
		$return['message'] = "Este equipo esta bloqueado por el sistema. Intente de nuevo en 30 minutos.";
		break;
	case 2:
		$return['error'] = 1;
		$return['message'] = "El codigo es incorrecto o ha caducado";
		break;
	case 3:
		$return['error'] = 1;
		$return['message'] = "Codigo de Restablecimiento Eliminado. Su codigo de solicitud concide con uno anterior, solicite uno nuevo";
		break;
	case 4:
		$return['error'] = 1;
		$return['message'] = "El nuevo password coincide con el anterior";
		break;
	case 5:
		$return['error'] = 0;
		$return['message'] = "Password restablecido !";
		break;
	default:
		$return['error'] = 1;
		$return['message'] = "Error en el Sistema";
		break;
}

$return = json_encode($return);

echo $return;

?>