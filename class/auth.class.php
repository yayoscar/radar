<?php
require_once("class.phpmailer.php");

class Auth 
{
	private $mysqli;
	
	/*
	* Initiates database connection
	*/
	
	public function __construct()
	{
		include("config.php");
	
		$this->mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
   
	}
	
	/*
	* Logs a user in
	* @param string $username
	* @param string $password (Must be already twice hashed with SHA1 : Ideally client side with JS)
	* @return array $return
	*/
	
	public function fblogin($fbid, $email)
	{
		$return = array();
		
			if($userdata = $this->getUserData($email))
			{
				if($fbid == $userdata['fbid'])
				{
					if($userdata['isactive'] == 1)
					{
						$sessiondata = $this->addNewSession($userdata['uid']);
						$return['session_hash'] = $sessiondata['hash'];
            
            $this->addNewLog($userdata['uid'], "LOGIN_SUCCESS", "Usuario con Sesion Iniciada. Sesion hash : " . $sessiondata['hash']);
						
						return $return;
					}
					else
					{
						//$this->addAttempt($_SERVER['REMOTE_ADDR']); 
					
						$this->addNewLog($userdata['uid'], "LOGIN_FAIL_NONACTIVE", "Cuenta no activada");
					
						return false;
					}
				}
				else
				{
					//$this->addAttempt($_SERVER['REMOTE_ADDR']); 
				
					$this->addNewLog($userdata['uid'], "LOGIN_FAIL_PASSWORD", "Password incorrecto : {$email}");
				
					return false;
				}
			}
			else
			{
				//$this->addAttempt($_SERVER['REMOTE_ADDR']); 
			
				$this->addNewLog("", "LOGIN_FAIL_USERNAME", "Intento de Inicio de sesion con el email : {$email} -> El email no existe en la BD");
			
        return false;
			}
		
	}
  
  /*
	* Logs a user in
	* @param string $username
	* @param string $password (Must be already twice hashed with SHA1 : Ideally client side with JS)
	* @return array $return
	*/
	
	public function login($username, $password)
	{
		$return = array();
		
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			$return['code'] = 0;
			return $return;
		}
		else
		{
			
			$plainpass = $password;
			$password = $this->getHash($password);
			
			if($userdata = $this->getUserData($username))
			{
				if($password == $userdata['password'])
				{
					if($userdata['isactive'] == 1)
					{
						$sessiondata = $this->addNewSession($userdata['uid']);

						$return['code'] = 4;
						$return['session_hash'] = $sessiondata['hash'];
						
						$this->addNewLog($userdata['uid'], "LOGIN_SUCCESS", "Usuario con Sesion Iniciada. Sesion hash : " . $sessiondata['hash']);
						
						return $return;
					}
					else
					{
						$this->addAttempt($_SERVER['REMOTE_ADDR']); 
					
						$this->addNewLog($userdata['uid'], "LOGIN_FAIL_NONACTIVE", "Cuenta no activada");
					
						$return['code'] = 3;
						
						return $return;
					}
				}
				else
				{
					$this->addAttempt($_SERVER['REMOTE_ADDR']); 
				
					$this->addNewLog($userdata['uid'], "LOGIN_FAIL_PASSWORD", "Password incorrecto : {$plainpass}");
				
					$return['code'] = 2;
					
					return $return;
				}
			}
			else
			{
				$this->addAttempt($_SERVER['REMOTE_ADDR']); 
			
				$this->addNewLog("", "LOGIN_FAIL_USERNAME", "Intento de Inicio de sesion con el email : {$username} -> El email no existe en la BD");
			
				$return['code'] = 2;
				
				return $return;
			}
		}
	}
	
	/*
	* Creates a new user, adds them to database
	* @param string $email
	* @param string $username
	* @param string $password (Must be already twice hashed with SHA1 : Ideally client side with JS)
	* @return array $return
	*/
	
	public function register($email, $password)
	{
		$return = array();
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			$return['code'] = 0;
			return $return;
		}
		else
		{
			$password = $this->getHash($password);
			
			if(!$this->isEmailTaken($email))
			{
				$uid = $this->addUser($email, $password);
				
				$this->addNewLog($uid, "REGISTER_SUCCESS", "Cuenta creda con exito, correo de activacion enviado.");
				
				$return['code'] = 4;
				$return['email'] = $email;
				return $return;
				
			}
			else
			{
				$this->addAttempt($_SERVER['REMOTE_ADDR']); 
			
				$this->addNewLog("", "REGISTER_FAIL_EMAIL", "El usuario intento registrar el email : {$email} -> el email ya esta en uso");
			
				$return['code'] = 2;
				return $return;
			}
			
		}
	}
	
	/*
	* Activates a user's account
	* @param string $activekey
	* @return array $return
	*/
	
	public function activate($activekey)
	{
		$return = array();
		
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			$return['code'] = 0;
			return $return;
		}
		else
		{
			$query = $this->mysqli->prepare("SELECT uid, fechaexpiracion FROM activaciones WHERE llaveactiva = ?");
			$query->bind_param("s", $activekey);
			$query->bind_result($uid, $expiredate);
			$query->execute();
			$query->store_result();
			$count = $query->num_rows;
			$query->fetch();
			$query->close();
			
			if($count == 0)
			{
				$this->addAttempt($_SERVER['REMOTE_ADDR']); 
			
				$this->addNewLog("", "ACTIVATE_FAIL_ACTIVEKEY", "El usuario intento activar una cuenta con la llave: {$activekey} -> La llave de activacion no se encuentra en la base de datos");
				
				$return['code'] = 2;
				return $return;
			}
			else
			{
				if(!$this->isUserActivated($uid))
				{
					$expiredate = strtotime($expiredate);
					$currentdate = strtotime(date("Y-m-d H:i:s"));
				
					if($currentdate < $expiredate)
					{
						$isactive = 1;
					
						$query = $this->mysqli->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
						$query->bind_param("ii", $isactive, $uid);
						$query->execute();
						$query->close();
						
						$this->deleteUserActivations($uid);
						
						$this->addNewLog($uid, "ACTIVATE_SUCCESS", "Cuenta Activada -> activo : 1");
						
						$return['code'] = 5;
						return $return;
					}
					else
					{
						$this->addAttempt($_SERVER['REMOTE_ADDR']); 
					
						$this->addNewLog($uid, "ACTIVATE_FAIL_EXPIRED", "El usuario intento activar una cuenta con la llave: {$activekey} -> La llave ha caducado");
					
						$this->deleteUserActivations($uid);
					
						$return['code'] = 4;
						return $return;
					}
				}
				else
				{
					$this->addAttempt($_SERVER['REMOTE_ADDR']); 
				
					$this->deleteUserActivations($uid);
				
					$this->addNewLog($uid, "ACTIVATE_FAIL_ALREADYACTIVE", "User attempted to activate an account with the key : {$activekey} -> Account already active. Set activekey : 0");
				
					$return['code'] = 3;
					return $return;
				}
			}
			
		}			
	}
	
	/*
	* Creates a reset key for an email address and sends email
	* @param string $email
	* @return array $return
	*/
	
	public function requestReset($email)
	{
		$return = array();
		
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			$return['code'] = 0;
			return $return;
		}
		else
		{
			$query = $this->mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
			$query->bind_param("s", $email);
			$query->bind_result($uid);
			$query->execute();
			$query->store_result();
			$count = $query->num_rows;
			$query->fetch();
			$query->close();
			
			if($count == 0)
			{
				$this->addAttempt($_SERVER['REMOTE_ADDR']); 
			
				$this->addNewLog("", "REQUESTRESET_FAIL_EMAIL", "El usuario intento reinicar el password del email : {$email} -> El Emailno existe en la BD");
				
				$return['code'] = 2;
				return $return;
			}
			else
			{
				if($this->addReset($uid, $email))
				{
					$this->addNewLog($uid, "REQUESTRESET_SUCCESS", "Se envio una solicitud de restablecimiento de password al email : {$email}");
				
					$return['code'] = 4;
					$return['email'] = $email;

					return $return;
				}
				else
				{
					$this->addAttempt($_SERVER['REMOTE_ADDR']); 
				
					$this->addNewLog($uid, "REQUESTRESET_FAIL_EXIST", "El usuario inetento restablecer el password del email: {$email} -> Una solictud de restablecimiento ya existe.");
				
					$return['code'] = 3;
					return $return;
				}
			}
			
		}
	}
		
	/*
	* Logs out the session, identified by hash
	* @param string $hash
	* @return boolean
	*/
	
	public function logout($hash)
	{
		if(strlen($hash) != 32) { return false; }
		
		$this->deleteSession($hash);
		
		setcookie("auth_session", $hash, time() - 3600);
		
		return true;
	}
	
	/*
	* Hashes string using multiple hashing methods, for enhanced security
	* @param string $string
	* @return string $enc
	*/
	
	public function getHash($string)
	{
		include("config.php");
	
		// If you can use the following line :
		// $enc = hash_pbkdf2("SHA512", base64_encode(str_rot13(hash("SHA512", str_rot13($auth_conf['salt_1'] . $string . $auth_conf['salt_2'])))), $auth_conf['salt_3'], 50000, 128);
		// If the above line spits out errors, use the following line :
		$enc = hash("SHA512", base64_encode(str_rot13(hash("SHA512", str_rot13($auth_conf['salt_1'] . $string . $auth_conf['salt_2'])))));
		return $enc;
	}
	
	/*
	* Gets user data for a given username and returns an array
	* @param string $username
	* @return array $data
	*/
	
	private function getUserData($username)
	{
		$data = array();
	
		$data['username'] = $username;
	
		$query = $this->mysqli->prepare("SELECT id, fbid, password, email, activo FROM usuarios WHERE email = ?");
		$query->bind_param("s", $username);
		$query->bind_result($data['uid'], $data['fbid'], $data['password'], $data['email'], $data['isactive']);
		$query->execute();
		$query->store_result();
		$count = $query->num_rows;
		$query->fetch();
		$query->close();
		
		if($count == 0)
		{
			return false;
		}
		else
		{
			return $data;
		}
	}
	
	/*
	* Creates a session for a specified user id
	* @param int $uid
	* @return array $data
	*/
	
	private function addNewSession($uid)
	{
		$data = array();
	
		$data['hash'] = md5(microtime());
		
		$agent = $_SERVER['HTTP_USER_AGENT'];
		
		$this->deleteExistingSessions($uid);
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$data['expire'] = date("Y-m-d H:i:s", strtotime("+1 month"));
		
		$query = $this->mysqli->prepare("INSERT INTO sesiones (uid, hash, fechaexpiracion, ip, agente) VALUES (?, ?, ?, ?, ?)");
		$query->bind_param("issss", $uid, $data['hash'], $data['expire'], $ip, $agent);
		$query->execute();
		$query->close();
		
		return $data;
	}
	
	/*
	* Removes all existing sessions for a given UID
	* @param int $uid
	* @return boolean
	*/
	
	private function deleteExistingSessions($uid)
	{
		$query = $this->mysqli->prepare("DELETE FROM sesiones WHERE uid = ?");
		$query->bind_param("i", $uid);
		$query->execute();
		$query->close();
		
		return true;
	}
	
	/*
	* Removes a session based on hash
	* @param string $hash
	* @return boolean
	*/
	
	private function deleteSession($hash)
	{
		$query = $this->mysqli->prepare("DELETE FROM sesiones WHERE hash = ?");
		$query->bind_param("s", $hash);
		$query->execute();
		$query->close();
		
		return true;
	}
	
	/*
	* Returns email based on session uid
	* @param string $uid
	* @return string $email
	*/
	
	public function getEmail($uid)
	{
    $query = $this->mysqli->prepare("SELECT email FROM usuarios WHERE id = ?");
    $query->bind_param("i", $uid);
    $query->bind_result($email);
    $query->execute();
    $query->store_result();
    $count = $query->num_rows;
    $query->fetch();
    $query->close();

    if($count == 0)
    {
      return false;
    }
    else
    {
      return $email;
    }
		
	}
  
  /*
	* Returns uid based on session hash
	* @param string $hash
	* @return string $uid
	*/
	
	public function getUid($hash)
	{
		$query = $this->mysqli->prepare("SELECT uid FROM sesiones WHERE hash = ?");
		$query->bind_param("s", $hash);
		$query->bind_result($uid);
		$query->execute();
		$query->store_result();
		$count = $query->num_rows;
		$query->fetch();
		$query->close();
		
		if($count == 0)
		{
			return false;
		}
		else
		{
			return $uid;
		}
	}
	
	/*
	* Function to add data to log table
	* @param string $uid
	* @param string $action
	* @param string $info
	* @param return boolean
	*/
	
	private function addNewLog($uid = 'UNKNOWN', $action, $info)
	{
		if(strlen($uid) == 0) { $uid = "UNKNOWN"; }
		elseif(strlen($action) == 0) { return false; }
		elseif(strlen($action) > 100) { return false; }
		elseif(strlen($info) == 0) { return false; }
		elseif(strlen($info) > 1000) { return false; }
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		
			$query = $this->mysqli->prepare("INSERT INTO log (uid, accion, info, ip) VALUES (?, ?, ?, ?)");
			$query->bind_param("ssss", $uid, $action, $info, $ip);
			$query->execute();
			$query->close();
			
			return true;
		}
	}
	
	/*
	* Function to check if a session is valid
	* @param string $hash
	* @return boolean
	*/
	
	public function checkSession($hash)
	{
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			return false;
		}
		else
		{
			if(strlen($hash) != 32) { setcookie("auth_session", $hash, time() - 3600); return false; }
		
			
			$query = $this->mysqli->prepare("SELECT id, uid, fechaexpiracion, ip, agente FROM sesiones WHERE hash = ?");
			$query->bind_param("s", $hash);
			$query->bind_result($sid, $uid, $expiredate, $db_ip, $db_agent);
			$query->execute();
			$query->store_result();
			$count = $query->num_rows;
			$query->fetch();
			$query->close();
			
			if($count == 0)
			{		
				setcookie("auth_session", $hash, time() - 3600);
				
				$this->addNewLog($uid, "CHECKSESSION_FAIL_NOEXIST", "Hash ({$hash}) no existe en la BD -> Cookie eliminada");
				
				return false;
			}
			else
			{
				if($_SERVER['REMOTE_ADDR'] != $db_ip)
				{
					if($_SERVER['HTTP_USER_AGENT'] != $db_agent)
					{
						$this->deleteExistingSessions($uid);
					
						setcookie("auth_session", $hash, time() - 3600);
					
						$this->addNewLog($uid, "CHECKSESSION_FAIL_DIFF", "IP y Agente diferentes ( DB : {$db_ip} / Actual : " . $_SERVER['REMOTE_ADDR'] . " ) -> UID sesion eliminada, cookie eliminada");
					
						return false;
					}
					else
					{
						$expiredate = strtotime($expiredate);
						$currentdate = strtotime(date("Y-m-d H:i:s"));
					
						if($currentdate > $expiredate)
						{			
							$this->deleteExistingSessions($uid);
						
							setcookie("auth_session", $hash, time() - 3600);
						
							$this->addNewLog($uid, "CHECKSESSION_FAIL_EXPIRE", "Sesion caduca ( Fecha de expiracion : {$db_expiredate} ) -> UID sesion eliminada, cookie eliminada");
						
							return false;
						}
						else 
						{
							$this->updateSessionIp($sid, $_SERVER['REMOTE_ADDR']);
						
							return true;
						}
					}
				}
				else 
				{
					$expiredate = strtotime($expiredate);
					$currentdate = strtotime(date("Y-m-d H:i:s"));
					
					if($currentdate > $expiredate)
					{			
						$this->deleteExistingSessions($uid);
						
						setcookie("auth_session", $hash, time() - 3600);
						
						$this->addNewLog($uid, "AUTH_CHECKSESSION_FAIL_EXPIRE", "Sesion caduca ( Fecha de expiracion : {$db_expiredate} ) -> UID sesion eliminada, cookie eliminada");
						
						return false;
					}
					else 
					{			
						return true;
					}
				}
			}
		}
	}
	
	/*
	* Updates the IP of a session (used if IP has changed, but agent has remained unchanged)
	* @param int $sid
	* @param string $ip
	* @return boolean
	*/
	
	private function updateSessionIp($sid, $ip)
	{
		$query = $this->mysqli->prepare("UPDATE sesiones SET ip = ? WHERE id = ?");
		$query->bind_param("si", $ip, $sid);
		$query->execute();
		$query->close();
		
		return true;
	}
	
	/*
	* Checks if an email is already in use
	* @param string $email
	* @return boolean
	*/
	
	private function isEmailTaken($email)
	{
		/*$query = $this->mysqli->prepare("SELECT * FROM usuarios WHERE email = ?");
		$query->bind_param("s", $email);
		$query->execute();
		$query->store_result();*/
    $q=$this->mysqli->query("SELECT * FROM usuarios WHERE email = '$email'");
    
    $count = $q->num_rows;
		
		//$q->close();
		
		if($count == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
  /*
	* Adds a new user Facebook to database
	* @param string $email
	* @param bigint $fbid
	* @return int $uid
	*/
	
	public function addFacebook($fbid, $email)
	{
		$email = htmlentities($email);
    if($this->isEmailTaken($email)){
      $query = $this->mysqli->prepare("UPDATE usuarios set fbid=$fbid,activo=1 where email='$email'");
      $query->execute();
      $query->close();
    }
    else{
      $query = $this->mysqli->prepare("INSERT INTO usuarios (fbid, email, activo) VALUES ($fbid,'$email',1)");
      $query->execute();
      $uid = $query->insert_id;
      $query->close();
		
      return $uid;
    }
	
		
	}
  
	/*
	* Adds a new user to database
	* @param string $email
	* @param string $username
	* @param string $password
	* @return int $uid
	*/
	
	private function addUser($email, $password)
	{
		$email = htmlentities($email);
	
		$query = $this->mysqli->prepare("INSERT INTO usuarios (password, email) VALUES (?, ?)");
		$query->bind_param("ss", $password, $email);
		$query->execute();
    
    $uid = $query->insert_id;
		
		
		$this->addActivation($uid, $email);
		
		return $uid;
	}
	
	/*
	* Creates an activation entry and sends email to user
	* @param int $uid
	* @param string $email
	* @return boolean
	*/
	
	private function addActivation($uid, $email)
	{
		include("config.php");
	
		//$activekey = $this->getRandomKey(20);
    $activekey = "prototipos";
				
		if($this->isUserActivated($uid))
		{
			return false;
		}
		else
		{
			$query = $this->mysqli->prepare("SELECT fechaexpiracion FROM activaciones WHERE uid = ?");
			$query->bind_param("i", $uid);
			$query->bind_result($expiredate);
			$query->execute();
			$query->store_result();
			$count = $query->num_rows;
			$query->fetch();
			$query->close();
			
			if($count > 0)
			{
				$expiredate = strtotime($expiredate);
				$currentdate = strtotime(date("Y-m-d H:i:s"));
				
				if($currentdate < $expiredate)
				{
					return false;
				}
				else
				{
					$this->deleteUserActivations($uid);
				}
			}
			
			$expiredate = date("Y-m-d H:i:s", strtotime("+1 day"));
			
			$query = $this->mysqli->prepare("INSERT INTO activaciones (uid, llaveactiva, fechaexpiracion) VALUES (?, ?, ?)");
			$query->bind_param("iss", $uid, $activekey, $expiredate);
			$query->execute();
			$query->close();
		
			$mail = new PHPMailer();
 
			//Luego tenemos que iniciar la validaci�n por SMTP:
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->Host = "mail.cbtis72.edu.mx"; // SMTP a utilizar. Por ej. smtp.elserver.com
			$mail->Username = "no-reply@cbtis72.edu.mx"; // Correo completo a utilizar
			$mail->Password = "23dct0245m"; // Contrase�a
			$mail->Port = 26; // Puerto a utilizar
			
			//Con estas pocas l�neas iniciamos una conexi�n con el SMTP. Lo que ahora deber�amos hacer, es configurar el mensaje a enviar, el //From, etc.
			$mail->From = "no-reply@cbtis72.edu.mx"; // Desde donde enviamos (Para mostrar)
			$mail->FromName = "radar|dgeti";
			
			//Estas dos l�neas, cumplir�an la funci�n de encabezado (En mail() usado de esta forma: �From: Nombre <correo@dominio.com>�) de //correo.
			$mail->AddAddress($email); // Esta es la direcci�n a donde enviamos
			$mail->IsHTML(true); // El correo se env�a como HTML
			$mail->Subject = "Enlace de Activacion de Cuenta"; // Este es el titulo del email.
			
			
			$email_body=file_get_contents("email_body.html");
			$email_body=html_entity_decode($email_body);
			
			$match=array("{llave}","{url}");
			$replace=array($activekey,$auth_conf['base_url']);
			$body = str_replace($match,$replace, $email_body);
			
			$mail->AltBody    = "Para ver este correo correctamente, utilice un cliente de correo compatible con HTML"; // optional, comment out and test
			
			$mail->MsgHTML($body);
			
			$exito = $mail->Send(); // Env�a el correo.
		
			/*$mail_body = str_replace("{key}", $activekey, $auth_conf['activation_email']['body']);
						
			@mail($email, $auth_conf['activation_email']['subj'], $mail_body, $auth_conf['activation_email']['head']);*/
								
			return true;
		}
	}
	
	/*
	* Deletes all activation entries for a user
	* @param int $uid
	* @return boolean
	*/
	
	private function deleteUserActivations($uid)
	{
		$query = $this->mysqli->prepare("DELETE FROM activaciones WHERE uid = ?");
		$query->bind_param("i", $uid);
		$query->execute();
		$query->close();
		
		return true;
	}
	
	/*
	* Checks if a user account is activated based on uid
	* @param int $uid
	* @return boolean
	*/
	
	private function isUserActivated($uid)
	{
		$query = $this->mysqli->prepare("SELECT activo FROM usuarios WHERE id = ?");
		$query->bind_param("i", $uid);
		$query->bind_result($isactive);
		$query->execute();
		$query->store_result();
    
		$count = $query->num_rows;
		
		
		if($count == 0)
		{
			return false;
		}
		else
		{
			if($isactive == 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	/*
	* Creates a reset entry and sends email to user
	* @param int $uid
	* @param string $email
	* @return boolean
	*/
	
	private function addReset($uid, $email)
	{
		include("config.php");
			
		$resetkey = $this->getRandomKey(20);	
		
		$query = $this->mysqli->prepare("SELECT fechaexpiracion FROM restablecer WHERE uid = ?");
		$query->bind_param("i", $uid);
		$query->bind_result($expiredate);
		$query->execute();
		$query->store_result();
		$count = $query->num_rows;
		$query->fetch();
		$query->close();
		
		$mail = new PHPMailer();
 
		//Luego tenemos que iniciar la validaci�n por SMTP:
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = "mail.cbtis72.edu.mx"; // SMTP a utilizar. Por ej. smtp.elserver.com
		$mail->Username = "no-reply@cbtis72.edu.mx"; // Correo completo a utilizar
		$mail->Password = "23dct0245m"; // Contrase�a
		$mail->Port = 26; // Puerto a utilizar
		
		//Con estas pocas l�neas iniciamos una conexi�n con el SMTP. Lo que ahora deber�amos hacer, es configurar el mensaje a enviar, el //From, etc.
		$mail->From = "no-reply@cbtis72.edu.mx"; // Desde donde enviamos (Para mostrar)
		$mail->FromName = "radar|dgeti";
		
		//Estas dos l�neas, cumplir�an la funci�n de encabezado (En mail() usado de esta forma: �From: Nombre <correo@dominio.com>�) de //correo.
		$mail->AddAddress($email); // Esta es la direcci�n a donde enviamos
		$mail->IsHTML(true); // El correo se env�a como HTML
		$mail->Subject = "Enlace de Restablecimiento de password"; // Este es el titulo del email.

		
		if($count == 0)
		{
			$expiredate = date("Y-m-d H:i:s", strtotime("+1 day"));
		
			$query = $this->mysqli->prepare("INSERT INTO restablecer (uid, llaverestablecer, fechaexpiracion) VALUES (?, ?, ?)");
			$query->bind_param("iss", $uid, $resetkey, $expiredate);
			$query->execute();
			$query->close();
			
			$reset_body=file_get_contents("reset_body.html");
			$reset_body=html_entity_decode($reset_body);
			
			$match=array("{llave}","{url}");
			$replace=array($resetkey,$auth_conf['base_url']);
			$body = str_replace($match,$replace, $reset_body);
			
			$mail->AltBody    = "Para ver este correo correctamente, utilice un cliente de correo compatible con HTML"; // optional, comment out and test
			
			$mail->MsgHTML($body);
			
			$exito = $mail->Send(); // Env�a el correo.
				
			return true;
		}
		else
		{
			$expiredate = strtotime($expiredate);
			$currentdate = strtotime(date("Y-m-d H:i:s"));
				
			if($currentdate < $expiredate)
			{		
				return false;
			}
			else
			{
				$this->deleteUserResets($uid);
			}
			
			$expiredate = date("Y-m-d H:i:s", strtotime("+1 day"));
			
			$query = $this->mysqli->prepare("INSERT INTO resets (uid, llaverestablecer, fechaexpiracion) VALUES (?, ?, ?)");
			$query->bind_param("iss", $uid, $resetkey, $expiredate);
			$query->execute();
			$query->close();
			
			$reset_body=file_get_contents("reset_body.html");
			$reset_body=html_entity_decode($reset_body);
			
			$match=array("{llave}","{url}");
			$replace=array($resetkey,$auth_conf['base_url']);
			$body = str_replace($match,$replace, $reset_body);
			
			$mail->AltBody    = "Para ver este correo correctamente, utilice un cliente de correo compatible con HTML"; // optional, comment out and test
			
			$mail->MsgHTML($body);
			
			$exito = $mail->Send(); // Env�a el correo.

			
			return true;
		}
	}
	
	/*
	* Deletes all reset entries for a user
	* @param int $uid
	* @return boolean
	*/
	
	private function deleteUserResets($uid)
	{
		$query = $this->mysqli->prepare("DELETE FROM restablecer WHERE uid = ?");
		$query->bind_param("i", $uid);
		$query->execute();
		$query->close();
		
		return true;
	}
	
	/*
	* Checks if a reset key is valid
	* @param string $key
	* @return array $return
	*/
	
	public function isResetValid($key)
	{
		$return = array();
		
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			$return['code'] = 0;
			return $return;
		}
		else
		{
			$query = $this->mysqli->prepare("SELECT uid, fechaexpiracion FROM restablecer WHERE llaverestablecer = ?");
			$query->bind_param("s", $key);
			$query->bind_result($uid, $expiredate);
			$query->execute();
			$query->store_result();
			$count = $query->num_rows;
			$query->fetch();
			$query->close();
			
			if($count == 0)
			{
				$this->addAttempt($_SERVER['REMOTE_ADDR']); 
			
				$return['code'] = 2;
				return $return;
			}
			else
			{
				$expiredate = strtotime($expiredate);
				$currentdate = strtotime(date("Y-m-d H:i:s"));
			
				if($currentdate > $expiredate)
				{
					$this->addAttempt($_SERVER['REMOTE_ADDR']); 
				
					$this->deleteUserResets($uid);
				
					$return['code'] = 3;
					return $return;
				}
				else
				{
					$return['code'] = 4;
					$return['uid'] = $uid;
					return $return;
				}
			}
		}
	}
	
	/*
	* After verifying key validity, changes user's password
	* @param string $key
	* @param string $password (Must be already twice hashed with SHA1 : Ideally client side with JS)
	* @return array $return
	*/
	
	public function resetPass($key, $password)
	{
		$return = array();
		
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			$return['code'] = 0;
			return $return;
		}
		else
		{
			$data = $this->isResetValid($key);
			
			if($data['code'] = 4)
			{
				$password = $this->getHash($password);
			
				$query = $this->mysqli->prepare("SELECT password FROM usuarios WHERE id = ?");
				$query->bind_param("i", $data['uid']);
				$query->bind_result($db_password);
				$query->execute();
				$query->store_result();
				$count = $query->num_rows;
				$query->fetch();
				$query->close();
				
				if($count == 0)
				{
					$this->addAttempt($_SERVER['REMOTE_ADDR']); 
				
					$this->deleteUserResets($data['uid']);
					
					$this->addNewLog($data['uid'], "RESETPASS_FAIL_UID", "El usuario intento restablecer un password con el codigo : {$key} -> El usuario no existe !");
					
					$return['code'] = 3;
					return $return;
				}
				else
				{
					if($db_password == $password)
					{
						$this->addAttempt($_SERVER['REMOTE_ADDR']); 
					
						$this->addNewLog($data['uid'], "RESETPASS_FAIL_SAMEPASS", "El usuario intento restablecer un password con el codigo : {$key} -> El nuevo password coincide con el anterior !");
					
						$this->deleteUserResets($data['uid']);
					
						$return['code'] = 4;
						return $return;
					}
					else
					{
						$query = $this->mysqli->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
						$query->bind_param("si", $password, $data['uid']);
						$query->execute();
						$query->close();
						
						$this->addNewLog($data['uid'], "RESETPASS_SUCCESS", "El usuario intento restablecer un password con el codigo : {$key} -> Password restablecido, codigo eliminado !");
						
						$this->deleteUserResets($data['uid']);
						
						$return['code'] = 5;
						return $return;
					}
				}
			}
			else
			{
				$this->addNewLog($data['uid'], "RESETPASS_FAIL_KEY", "El usuario intento restablecer un password con el codigo : {$key} -> Codigo ivalido / incorrecto / caducado !");
			
				$return['code'] = 2;
				return $return;
			}
		}
	}
	
	/*
	* Recreates activation email for a given email and sends
	* @param string $email
	* @return array $return
	*/
	
	public function resendActivation($email)
	{
		$return = array();
		
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			$return['code'] = 0;
			return $return;
		}
		else
		{
			$query = $this->mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
			$query->bind_param("s", $email);
			$query->bind_result($uid);
			$query->execute();
			$query->store_result();
			$count = $query->num_rows;
			$query->fetch();
			$query->close();
			
			if($count == 0)
			{
				$this->addAttempt($_SERVER['REMOTE_ADDR']); 
			
				$this->addNewLog("", "RESENDACTIVATION_FAIL_EMAIL", "El usuario intento reenviarse el codigo de activacion : {$email} -> El Email no existe en la BD");
			
				$return['code'] = 2;
				return $return;
			}
			else
			{
				if($this->isUserActivated($uid))
				{
					$this->addAttempt($_SERVER['REMOTE_ADDR']); 
				
					$this->addNewLog($uid, "RESENDACTIVATION_FAIL_ACTIVATED", "El usuario intento reenviarse el codigo de activacion : {$email} -> La cuenta ya esta activada !");
				
					$return['code'] = 3;
					return $return;
				}
				else
				{
					if($this->addActivation($uid, $email))
					{
						$this->addNewLog($uid, "RESENDACTIVATION_SUCCESS", "Activation email was resent to the email : {$email}");
					
						$return['code'] = 5;
						return $return;
					}
					else
					{
						$this->addAttempt($_SERVER['REMOTE_ADDR']); 
					
						$this->addNewLog($uid, "RESENDACTIVATION_FAIL_EXIST", "El usuario intento reenviarse el codigo de activacion : {$email} -> El codigo de activacion ya se envio anteriormente. Necesita esperar 24 horas para que expire !");
						
						$return['code'] = 4;
						return $return;
					}
				}
			}
		}
	}
	
	/*
	* Gets UID from Session hash
	* @param string $hash
	* @return int $uid
	*/
	
	public function sessionUID($hash)
	{
		if(strlen($hash) != 32) { return false; }
		else
		{
			$query = $this->mysqli->prepare("SELECT uid FROM sessions WHERE hash = ?");
			$query->bind_param("s", $hash);
			$query->bind_result($uid);
			$query->execute();
			$query->store_result();
			$count = $query->num_rows;
			$query->fetch();
			$query->close();
			
			if($count == 0)
			{
				return false;
			}
			else
			{
				return $uid;
			}
		}
	}
	
	/*
	* Changes a user's password
	* @param int $uid
	* @param string $currpass
	* @param string $newpass
	* @return array $return
	*/
	
	public function changePassword($uid, $currpass, $newpass)
	{
		$return = array();
	
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			$return['code'] = 0;
			return $return;
		}
		else
		{
			if(strlen($currpass) != 40) { $return['code'] = 1; $this->addAttempt($_SERVER['REMOTE_ADDR']); return $return; }
			elseif(strlen($newpass) != 40) { $return['code'] = 1; $this->addAttempt($_SERVER['REMOTE_ADDR']); return $return; }
			else
			{
				$currpass = $this->getHash($currpass);
				$newpass = $this->getHash($newpass);
			
				$query = $this->mysqli->prepare("SELECT password FROM users WHERE id = ?");
				$query->bind_param("i", $uid);
				$query->bind_result($db_currpass);
				$query->execute();
				$query->store_result();
				$count = $query->num_rows;
				$query->fetch();
				$query->close();
				
				if($count == 0)
				{
					$this->addAttempt($_SERVER['REMOTE_ADDR']); 
				
					$this->addNewLog($uid, "CHANGEPASS_FAIL_UID", "User attempted to change password for the UID : {$uid} -> UID doesn't exist !");
				
					$return['code'] = 2;
					return $return;
				}
				else
				{
					if($currpass != $newpass)
					{
						if($currpass == $db_currpass)
						{
							$query = $this->mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
							$query->bind_param("si", $newpass, $uid);
							$query->execute();
							$query->close();
							
							$this->addNewLog($uid, "CHANGEPASS_SUCCESS", "User changed the password for the UID : {$uid}");
							
							$return['code'] = 5;
							return $return;
						}
						else
						{
							$this->addAttempt($_SERVER['REMOTE_ADDR']); 
						
							$this->addNewLog($uid, "CHANGEPASS_FAIL_PASSWRONG", "User attempted to change password for the UID : {$uid} -> Current password incorrect !");
						
							$return['code'] = 4;
							return $return;
						}
					}
					else
					{
						$this->addAttempt($_SERVER['REMOTE_ADDR']);
					
						$this->addNewLog($uid, "CHANGEPASS_FAIL_PASSMATCH", "User attempted to change password for the UID : {$uid} -> New password matches current password !");
					
						$return['code'] = 3;
						return $return;
					}
				}
			}
		}
	}
	
	
	/*
	* Changes a user's email
	* @param int $uid
	* @param string $currpass
	* @param string $newpass
	* @return array $return
	*/
	
	public function changeEmail($uid, $email, $password)
	{
		$return = array();
		
		if($this->isBlocked($_SERVER['REMOTE_ADDR']))
		{
			$return['code'] = 0;
			return $return;
		}
		else
		{
			if(strlen($email) == 0) { $return['code'] = 1; $this->addAttempt($_SERVER['REMOTE_ADDR']); return $return; }
			elseif(strlen($email) > 100) { $return['code'] = 1; $this->addAttempt($_SERVER['REMOTE_ADDR']); return $return; }
			elseif(strlen($email) < 3) { $return['code'] = 1; $this->addAttempt($_SERVER['REMOTE_ADDR']); return $return; }
			elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) { $return['code'] = 1; $this->addAttempt($_SERVER['REMOTE_ADDR']); return $return; }
			elseif(strlen($password) != 40) { $return['code'] = 1; $this->addAttempt($_SERVER['REMOTE_ADDR']); return $return; }
			else
			{	
				$password = $this->getHash($password);
			
				$query = $this->mysqli->prepare("SELECT password, email FROM users WHERE id = ?");
				$query->bind_param("i", $uid);
				$query->bind_result($db_password, $db_email);
				$query->execute();
				$query->store_result();
				$count = $query->num_rows;
				$query->fetch();
				$query->close();
				
				if($count == 0)
				{
					$this->addAttempt($_SERVER['REMOTE_ADDR']); 
				
					$this->addNewLog($uid, "CHANGEEMAIL_FAIL_UID", "User attempted to change email for the UID : {$uid} -> UID doesn't exist !");
				
					$return['code'] = 2;
					return $return;
				}
				else
				{
					if($password == $db_password)
					{
						if($email == $db_email)
						{
							$this->addAttempt($_SERVER['REMOTE_ADDR']); 
						
							$this->addNewLog($uid, "CHANGEEMAIL_FAIL_EMAILMATCH", "User attempted to change email for the UID : {$uid} -> New Email address matches current email !");
						
							$return['code'] = 4;
							return $return;
						}
						else
						{
							$query = $this->mysqli->prepare("UPDATE users SET email = ? WHERE id = ?");
							$query->bind_param("si", $email, $uid);
							$query->execute();
							$query->close();
							
							$this->addNewLog($uid, "CHANGEEMAIL_SUCCESS", "User changed email address for UID : {$uid}");
							
							$return['code'] = 5;
							return $return;
						}					
					}
					else
					{
						$this->addAttempt($_SERVER['REMOTE_ADDR']); 
					
						$this->addNewLog($uid, "CHANGEEMAIL_FAIL_PASS", "User attempted to change email for the UID : {$uid} -> Password is incorrect !");
					
						$return['code'] = 3;
						return $return;
					}
				}
			}
		}
	}
	
	/*
	* Informs if a user is locked out
	* @param string $ip
	* @return boolean
	*/
	
	public function isBlocked($ip)
	{
		$query = $this->mysqli->prepare("SELECT contador, fechaexpiracion FROM intentos WHERE ip = ?");
		$query->bind_param("s", $ip);
		$query->bind_result($attcount, $expiredate);
		$query->execute();
		$query->store_result();
		$count = $query->num_rows;
		$query->fetch();
		$query->close();
		
		if($count == 0)
		{
			return false;
		}
		else
		{
			if($attcount == 5)
			{
				$expiredate = strtotime($expiredate);
				$currentdate = strtotime(date("Y-m-d H:i:s"));
			
				if($currentdate < $expiredate)
				{
					return true;
				}
				else
				{
					$this->deleteAttempts($ip);
					return false;
				}
			}
			else
			{
				$expiredate = strtotime($expiredate);
				$currentdate = strtotime(date("Y-m-d H:i:s"));
			
				if($currentdate < $expiredate)
				{
					return false;
				}
				else
				{
					$this->deleteAttempts($ip);
					return false;
				}
			
				return false;
			}
		}
    
	}
	
	/*
	* Deletes all attempts for a given IP from database
	* @param string $ip
	* @return boolean
	*/
	
	private function deleteAttempts($ip)
	{
		$query = $this->mysqli->prepare("DELETE FROM intentos WHERE ip = ?");
		$query->bind_param("s", $ip);
		$query->execute();
		$query->close();
	
		return true;
	}
	
	/*
	* Adds an attempt to database for given IP
	* @param string $ip
	* @return boolean
	*/
	
	private function addAttempt($ip)
	{
		$query = $this->mysqli->prepare("SELECT contador FROM intentos WHERE ip = ?");
		$query->bind_param("s", $ip);
		$query->bind_result($attempt_count);
		$query->execute();
		$query->store_result();
		$count = $query->num_rows;
		$query->fetch();
		$query->close();
		
		if($count == 0)
		{		
			$attempt_expiredate = date("Y-m-d H:i:s", strtotime("+30 minutes"));
			$attempt_count = 1;
			
			$query = $this->mysqli->prepare("INSERT INTO intentos (ip, contador, fechaexpiracion) VALUES (?, ?, ?)");
			$query->bind_param("sis", $ip, $attempt_count, $attempt_expiredate);
			$query->execute();
			$query->close();
			
			return true;
		}
		else 
		{
			// IP Already exists in attempts table, add 1 to current count
			
			$attempt_expiredate = date("Y-m-d H:i:s", strtotime("+30 minutes"));
			$attempt_count = $attempt_count + 1;
			
			$query = $this->mysqli->prepare("UPDATE intentos SET contador=?, fechaexpiracion=? WHERE ip=?");
			$query->bind_param("iss", $attempt_count, $attempt_expiredate, $ip);
			$query->execute();
			$query->close();
			
			return true;
		}
	}
	
	/*
	* Returns a random string, length can be modified
	* @param int $length
	* @return string $key
	*/
	
	public function getRandomKey($length = 20)
	{
		$chars = "_" . "A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6" . "_" . "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6" . "_";
		$key = "";
		
		for($i = 0; $i < $length; $i++)
		{
			$key .= $chars{mt_rand(0, strlen($chars) - 1)};
		}
		
		return $key;
	}
  
  public function isActivo($uid)
  {
    $sql="SELECT idalumno FROM usuarios WHERE id=$uid";
    $q=$this->mysqli->query($sql);
		$f=$q->fetch_assoc();
    $idalumno=$f["idalumno"];
    $q->close();
		if($idalumno == 0)
		{
			return false;
		}
		else
		{
			return $idalumno;
		}
  }
  
  public function getRol($uid)
  {
    $sql="SELECT rol FROM usuarios WHERE id=$uid";
    $q=$this->mysqli->query($sql);
		$f=$q->fetch_assoc();
    $q->close();
		return $f["rol"];
	}
  
  public function menu($email,$rol,$activo){ 
    $inicio=$planteles=$grupos=$encuestas="";
    switch($activo)
    {
      case 'inicio': $inicio="active";break;
      case 'planteles': $planteles="active";break;
      case 'grupos': $grupos="active";break;
      case 'encuestas': $encuestas="active";break;
    }
    
    echo "<div class='navbar navbar-inverse nav navbar-fixed-top'><div class='navbar-inner'><div class='container'><a class='logo-radar' href='#'><img src='../img/logo.png'/></a><div class='nav-collapse collapse'><ul class='nav'><li class='divider-vertical'></li><li class='$inicio'><a href='../home/'>Inicio</a></li>";
    if ($rol==1) 
      echo "<li class='$planteles'><a href='../plantel/'>Planteles</a></li>";
    if ($rol==2)
      echo "<li class='$grupos'><a href='../grupos/'>Grupos</a></li>";
    if($rol==1)
      echo "<li class='$encuestas'><a href='../encuestas/'>Encuestas</a></li>";
    echo "</ul><div class='pull-right'><ul class='nav pull-right'><li class='dropdown'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>$email<b class='caret'></b></a><ul class='dropdown-menu'><li><a href='#'><i class='icon-cog'></i> Perfil</a></li><li class='divider'></li><li><a href='../logout.php'><i class='icon-off'></i> Salir</a></li></ul></li></ul></div></div></div></div></div> ";
  }
  
}

?>
