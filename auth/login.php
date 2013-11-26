<?php
require_once("../class/class.inputfilter.php");
require_once("../class/auth.class.php");

$auth = new Auth;

$ifilter = new InputFilter();
$_REQUEST = $ifilter->process($_REQUEST);

$username=$_REQUEST["email-login"];
$password=$_REQUEST["password-sha1-login"];

$login = $auth->login($username, $password);

$return = array();

switch($login['code'])
{
	case 0:
		$return['error'] = 1;
		$return['message'] = "Este equipo esta suspendido. Intente de nuevo en 30 minutos.";
		break;
	case 2:
		$return['error'] = 1;
		$return['message'] = "Email / Password es incorrecto";
		break;
	case 3:
		$return['error'] = 1;
		$return['message'] = "La cuenta aun no est&aacute; activada";
		break;
	case 4:
		$return['error'] = 0;
		$return['message'] = "Sesion Iniciada, espere un momento...";
		$return['session_hash'] = $login['session_hash'];
		break;
	default:
		$return['error'] = 1;
		$return['message'] = "System error encountered";
		break;
}

$return = json_encode($return);

echo $return;

?>