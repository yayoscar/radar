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

	$email = $_REQUEST['email'];
	$password = $_REQUEST['password'];

	$register = $auth->register($email, $password);

	$return = array();

	switch($register['code'])
	{
		case 0:
			$return['error'] = 1;
			$return['message'] = "Este equipo esta suspendido. Intente de nuevo en 30 minutos.";
			break;
		case 1:
			$return['error'] = 1;
			$return['message'] = "Username / Password is invalid";
			break;
		case 2:
			$return['error'] = 1;
			$return['message'] = "El correo ya esta registrado";
			break;
		case 4:
			$return['error'] = 0;
			$return['message'] = "Se ha envio una activaci&oacute;n a " . $register['email']." por favor consulte su correo";
			break;
		default:
			$return['error'] = 1;
			$return['message'] = "Error en el Sistema";
			break;
	}

	$return = json_encode($return);

	echo $return;
?>
