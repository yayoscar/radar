<?php
require_once("../class/auth.class.php");
require_once("../class/class.inputfilter.php");
$auth = new Auth;
if(isset($_COOKIE['auth_session']))
{
	$hash = $_COOKIE['auth_session'];
	if($auth->checkSession($hash))
	{
		$loggedin = 1;
	}
	else
	{
		$loggedin = 0;
	}
}
else
{
	$loggedin = 0;
}

if($loggedin == 1) { exit(); }

$ifilter = new InputFilter();
$_REQUEST = $ifilter->process($_REQUEST);
$email=$_REQUEST["email"];

$activate = $auth->resendActivation($email);
$return = array();

switch($activate['code'])
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
		$return['message'] = "La cuenta ya est&aacute; activa";
		break;
	case 4:
		$return['error'] = 1;
		$return['message'] = "Solicitud de activación ya existe, consulte su correo o spere a que la solicitud expire en 24 horas";
		break;
	case 5:
		$return['error'] = 0;
		$return['message'] = "Activaci&oacute;n reenviada";
		break;
	default:
		$return['error'] = 1;
		$return['message'] = "Error en el Sistema";
		break;
}

$return = json_encode($return);

echo $return;

?>