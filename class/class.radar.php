<?php
require_once("class.phpmailer.php");

class Radar 
{
	private $mysqli;
  private $base_url;
	
	public function __construct()
	{
		include("config.php");
	
		$this->mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']); 
    $this->base_url = $auth_conf['base_url'];
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
  
  
	public function logout($hash)
	{
		if(strlen($hash) != 32) { return false; }
		
		$this->deleteSession($hash);
		
		setcookie("auth_session", $hash, time() - 3600);
    		
		return true;
	}
	
	private function deleteExistingSessions($uid)
	{
		$query = $this->mysqli->prepare("DELETE FROM sesiones WHERE uid = ?");
		$query->bind_param("i", $uid);
		$query->execute();
		$query->close();
		
		return true;
	}
	
	private function deleteSession($hash)
	{
		$query = $this->mysqli->prepare("DELETE FROM sesiones WHERE hash = ?");
		$query->bind_param("s", $hash);
		$query->execute();
		$query->close();
		
		return true;
	}
	
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
	
	private function updateSessionIp($sid, $ip)
	{
		$query = $this->mysqli->prepare("UPDATE sesiones SET ip = ? WHERE id = ?");
		$query->bind_param("si", $ip, $sid);
		$query->execute();
		$query->close();
		
		return true;
	}
	
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
	
  public function buscarCodigo($codigo)
  {
    $q=$this->mysqli->query("SELECT idalumno FROM codigos WHERE codigo='$codigo'");
    $f=$q->fetch_assoc();
    $count = $q->num_rows;
		$q->close();
		if($count == 0)
		{
			return false;
		}
		else
		{
			$idalumno=$f["idalumno"];
      return $idalumno;
		}
    
  }
  
   public function infoActivarAlumno($idalumno)
  {
    $q=$this->mysqli->query("SELECT num_con,nombre,apepat,apemat FROM alumnos WHERE id=$idalumno");
    $f=$q->fetch_assoc();
    $q->close();
    $return["idalumno"]=$idalumno;
    $return["num_con"]=$f["num_con"];
    $return["nombre"]=$f["apepat"]." ".$f["apemat"]." ".$f["nombre"];
    $return["nombre"]=$this->oracion(utf8_encode($return["nombre"]));
    
    $q=$this->mysqli->query("SELECT * FROM alumno_grupo WHERE idalumno=$idalumno");
    $f=$q->fetch_assoc();
    list($idalumno,$idgeneracion,$idestado,$idplantel,$idespecialidad,$idgrupo,$turno)=array_values((array) $f);
    $q->close();
    
    $q=$this->mysqli->query("SELECT concat(plantel,' ',numero) as nplantel FROM plantel WHERE id=$idplantel");
    $f=$q->fetch_assoc();
    $return["plantel"]=$f["nplantel"];
    $q->close();
    
    $q=$this->mysqli->query("SELECT generacion FROM generacion WHERE id=$idgeneracion");
    $f=$q->fetch_assoc();
    $return["generacion"]=$f["generacion"];
    $q->close();
    
     $q=$this->mysqli->query("SELECT nombre FROM especialidad WHERE id=$idespecialidad");
    $f=$q->fetch_assoc();
    $especialidad=$this->oracion(utf8_encode($f["nombre"]));
    $q->close();
    
    $q=$this->mysqli->query("SELECT grupo FROM grupos WHERE id=$idgrupo");
    $f=$q->fetch_assoc();
    $grupo=$f["grupo"];
    $q->close();
    
    $turno= ($turno==1) ? "TM" : "TV";
    
    $return["grupo"]=$especialidad." ".$grupo." ".$turno;
    
              
    return $return;
  }
  
  public function vincularCuenta($idalumno,$uid){
    $sql="UPDATE usuarios set idalumno=$idalumno WHERE id=$uid";
    $this->mysqli->query($sql);
    $sql="UPDATE alumnos set activo=3 WHERE id=$idalumno";
    $this->mysqli->query($sql);
    $sql="DELETE from codigos WHERE idalumno=$idalumno";
    $this->mysqli->query($sql);
    
    $texto="He completado mi registro en radar|dgeti, que esperas? &uacute;nete";
    $this->fbPublicar($texto); 
    
    $this->asignarAlumnoEncuestas($idalumno);
  }
  
  public function getRol($uid)
  {
    $sql="SELECT rol FROM usuarios WHERE id=$uid";
    $q=$this->mysqli->query($sql);
		$f=$q->fetch_assoc();
    $q->close();
		return $f["rol"];
	}
  
  public function getEncuesta($idencuesta)
  {
    $sql="SELECT nombre FROM encuestas WHERE id=$idencuesta";
    $q=$this->mysqli->query($sql);
		$f=$q->fetch_assoc();
    $q->close();
		return $this->oracion(utf8_encode($f["nombre"]));
	}
  
  public function listaPreguntas($idencuesta){
    $return=array();
    $sql="SELECT * FROM preguntas WHERE idencuesta=$idencuesta ORDER by orden";
    if($q=$this->mysqli->query($sql))
    {
      while($pregunta=$q->fetch_assoc()){
        $idpregunta=$pregunta["id"];
        $pregunta["pregunta"]=utf8_encode($pregunta["pregunta"]);
        unset($pregunta["idencuesta"]);
        $sql="SELECT * FROM opciones WHERE idpregunta=$idpregunta ORDER by orden";
        $q2=$this->mysqli->query($sql);
        while($opcion=$q2->fetch_assoc()){
          unset($opcion["idpregunta"]);
          $opcion["opcion"]=utf8_encode($opcion["opcion"]);
          $pregunta["opcion"][]=$opcion;
        }
        $q2->close();
        $return[]=$pregunta;
      }
      $q->close();
      return $return;
    }
    else
    {
      return false;
    }
  }
  
  public function getIdAlumno($uid){
    $sql="SELECT idalumno FROM usuarios WHERE id=$uid";
    $q=$this->mysqli->query($sql);
		$f=$q->fetch_assoc();
    $q->close();
		return $f["idalumno"];
  }
  
  public function getIdsAlumno($idalumno){
    $sql="SELECT idgeneracion,idestado,idplantel,idespecialidad,idgrupo,turno FROM alumno_grupo WHERE idalumno=$idalumno";
    $q=$this->mysqli->query($sql);
		$f=$q->fetch_assoc();
    $q->close();
		return $f;
  }
  
  public function enviarEncuesta($encuesta)
  {
   $idalumno=$encuesta["idalumno"];
   $idencuesta=$encuesta["idencuesta"];
   $idpregunta=$encuesta["idpregunta"];
   $respuesta=$encuesta["respuesta"];
   
   $max=$encuesta["max"];
   $next=$encuesta["next"];
   list($idgeneracion,$idestado,$idplantel,$idespecialidad,$idgrupo,$turno)=array_values((array) $this->getIdsAlumno($idalumno));
   
   unset($encuesta["idalumno"]);
   unset($encuesta["idencuesta"]);
   
   if (is_array($respuesta)){
      foreach($respuesta as $item){
        $sql="INSERT INTO respuestas values ($idgeneracion,$idestado,$idplantel,$idespecialidad,$idgrupo,$turno,$idalumno,$idencuesta,$idpregunta,$item)";
               
        if(!$q=$this->mysqli->query($sql))
         {
           return false;
         }
      }
    } else {
      if(is_numeric($respuesta)){
        $sql="INSERT INTO respuestas values ($idgeneracion,$idestado,$idplantel,$idespecialidad,$idgrupo,$turno,$idalumno,$idencuesta,$idpregunta,$respuesta)";
      } else {
        $sql="INSERT INTO respuestas_texto values ($idgeneracion,$idestado,$idplantel,$idespecialidad,$idgrupo,$turno,$idalumno,$idencuesta,$idpregunta,'$respuesta')";
      }
      
      if(!$q=$this->mysqli->query($sql))
     {
      
       return false;
     }
    
   }
   $this->actualizaEncuesta($idencuesta,$idalumno,$next,$max);
   return true;
  }
  
  //Actualiza el estado de la respuesta del alumno en caso de quedar inconclusa
  private function actualizaEncuesta($idencuesta,$idalumno,$next,$max) {
    if($this->estadoEncuesta($idencuesta,$idalumno)){
      if($next>$max) { //ultima pregunta respondida
         $this->actualizaEstadoEncuesta($idalumno,$idencuesta,0);
         $encuesta=$this->nombreEncuesta($idencuesta);
         $texto="He completado la encuesta $encuesta en radar|dgeti";
         $this->fbPublicar($texto);
      }
      else {
        $this->actualizaEstadoEncuesta($idalumno,$idencuesta,$next);
      }
    }
    else {
      $this->nuevoEstadoEncuesta($idalumno,$idencuesta,$next);
    }
  }
  
  private function nuevoEstadoEncuesta($idalumno,$idencuesta,$next){
    $fecha=date("Y-m-d h:i:s");
    $sql="INSERT INTO alumno_encuesta (idalumno,idencuesta,estado,fecha) values($idalumno,$idencuesta,$next,'$fecha')";
    $q=$this->mysqli->query($sql);
    
  }


  
  //Regresa el ultimo estado del usuario en la escuesta
  public function estadoEncuesta($idencuesta,$idalumno){
    $sql="SELECT estado FROM alumno_encuesta WHERE idalumno=$idalumno AND idencuesta=$idencuesta";
    $q=$this->mysqli->query($sql);
		$count = $q->num_rows;
    $f=$q->fetch_assoc();
    $q->close();
    if($count == 0)
		{
			return false;
		}
		else
		{
			return $f["estado"];
		}
  }
  
  private function actualizaEstadoEncuesta($idalumno,$idencuesta,$estado){
    $fecha=date("Y-m-d h:i:s");
    $sql="UPDATE alumno_encuesta set estado=$estado,fecha='$fecha' WHERE idalumno=$idalumno AND idencuesta=$idencuesta";
    $q=$this->mysqli->query($sql);
  }
  
  public function nombreEncuesta($idencuesta){
    $sql="SELECT nombre FROM encuestas where id=$idencuesta";
    $query = $this->mysqli->query($sql);
    $f=$query->fetch_assoc();
    $query->close();
        
    return $f["nombre"];
  }
  
  public function listaPreguntas2($idencuesta){
    $sql="SELECT pregunta FROM preguntas WHERE idencuesta=$idencuesta ORDER by orden";
    $query = $this->mysqli->query($sql);
    while($f=$query->fetch_assoc()){
      $return[]=$f;
    }
    $query->close();
        
    return $return;
  }
  
  public function listaEncuestas()
  {
    $sql="SELECT * FROM encuestas ORDER BY nombre";
    $query = $this->mysqli->query($sql);
    while($f=$query->fetch_assoc()){
      $return[]=$f;
    }
    $query->close();
        
    return $return;
   }
  
   public function asignarEncuesta($idencuesta,$idgeneracion){
     if(!$this->generacionEnEncuesta($idencuesta,$idgeneracion)){
       $this->asignarEncuestaGeneracion($idencuesta,$idgeneracion);
       return 2;
     } else {
       return 1; //Encuesta ya asgna previamente a generacion
     }
     
   }
   
   private function generacionEnEncuesta($idencuesta,$idgeneracion) {
    $sql="SELECT idencuesta FROM encuesta_generacion WHERE idencuesta=$idencuesta AND idgeneracion=$idgeneracion";
    $q=$this->mysqli->query($sql);
		$count = $q->num_rows;
    $f=$q->fetch_assoc();
    $q->close();
    if($count == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
   }

   private function asignarEncuestaGeneracion($idencuesta,$idgeneracion) {
     $sql="INSERT INTO encuesta_generacion values($idencuesta,$idgeneracion)";
     $this->mysqli->query($sql);
     $this->asignarEncuestaAlumnos($idencuesta,$idgeneracion);
     
   }
   
   private function asignarEncuestaAlumnos($idencuesta,$idgeneracion) {
     $sql="SELECT id FROM alumnos WHERE ACTIVO=3 AND id IN (SELECT idalumno FROM alumno_grupo WHERE idgeneracion=$idgeneracion AND idalumno NOT IN (SELECT idalumno FROM alumno_encuesta WHERE idencuesta=$idencuesta))";
     $query = $this->mysqli->query($sql);
     while($f=$query->fetch_assoc()){
        $idalumno=$f["id"];
        $this->nuevoEstadoEncuesta($idalumno,$idencuesta,1);
        
        $mensaje="Pedimos tu ayuda para contestar la siguiente encuesta";
        $link="encuesta/index.php?idalumno=$idalumno&idencuesta=encuesta$idencuesta";
        $this->fbNotificar($idalumno,$mensaje,$link);
     }
   }
   
   private function asignarAlumnoEncuestas($idalumno) {
     $alumno= $this->getIdsAlumno($idalumno);
     $idgeneracion=$alumno["idgeneracion"];
     
     $sql="SELECT idencuesta FROM encuesta_generacion WHERE idgeneracion=$idgeneracion AND idencuesta NOT IN (SELECT idencuesta FROM alumno_encuesta WHERE idalumno=$idalumno)";
     
     $query = $this->mysqli->query($sql);
     while($f=$query->fetch_assoc()){
        $idencuesta=$f["idencuesta"];
        $this->nuevoEstadoEncuesta($idalumno,$idencuesta,1);
        
        $mensaje="Pedimos tu ayuda para contestar la siguiente encuesta";
        $link="encuesta/index.php?idalumno=$idalumno&idencuesta=encuesta$idencuesta";
        $this->fbNotificar($idalumno,$mensaje,$link);
     }
   }
   
  public function guardarDatosAlumno($datos) {
    list($telfijo,$telmovil,$direccion,$fecha_nac,$idalumno)=array_values((array) $datos);
    $fecha_nac = date("Y-m-d", strtotime($fecha_nac));
    $sql="INSERT INTO alumno_datos VALUES ($idalumno,'$telfijo','$telmovil','$direccion','$fecha_nac')";
    $this->mysqli->query($sql);
    
  }
   
   public function listaSolicitudes($idalumno)
  {
    $query = $this->mysqli->query("SELECT * FROM alumno_encuesta WHERE idalumno=$idalumno and estado<>0");
    while($fila=$query->fetch_assoc())
    {
      $aux["idencuesta"]=$idencuesta=$fila["idencuesta"];
      $aux["encuesta"]=$this->getEncuesta($idencuesta);
      $aux["ago"]=$this->ago($fila["fecha"]);
      $return[]=$aux;
      
    }
    $query->close();
        
    return $return;
   }
  
  
   
   private function agregarMensaje($idalumno,$mensaje,$tipo){
     $sql="INSERT INTO mensajes values($idalumno,$tipo,\"$mensaje\")";
     $this->mysqli->query($sql);
   }
   
   public function fbNotificar($idalumno,$mensaje,$link){
     $sql="SELECT fbid FROM usuarios WHERE idalumno=$idalumno";
     $q=$this->mysqli->query($sql); 
     $f=$q->fetch_assoc();
     $fbid=$f["fbid"];
     if($fbid!=0){
       
       require_once("../inc/facebook.php");
        include("config.php");

        $facebook = new Facebook(array(
          'appId'  => $appId,
          'secret' => $appSecret,
          'cookie' => true
        ));

        $facebook->api("/$fbid/notifications", "POST",
        array(
          'access_token' => $facebook->getAppId().'|'.$facebook->getApiSecret(),
          'href' => $link,
          'template' => $mensaje,
        )
        );
       
     }
   }
   
   public function listaRespuestas($idencuesta,$idgeneracion){
     $sql="SELECT idalumno FROM alumno_encuesta where estado=0 AND idencuesta IN (SELECT idencuesta FROM encuesta_generacion WHERE idgeneracion=$idgeneracion and idencuesta=$idencuesta)";
     $q=$this->mysqli->query($sql);
     while($f=$q->fetch_assoc()){
      $return[]=$f;
    }
    $q->close();
        
    return $return;
   }
   
   private function listaAlumno($idalumno)
  {
    $q=$this->mysqli->query("SELECT * FROM alumnos WHERE id=$idalumno");
    $return=$q->fetch_assoc();
    $q->close();

    
    $return["ape_pat"]=$this->oracion(utf8_encode($return["apepat"]));
    $return["ape_mat"]=$this->oracion(utf8_encode($return["apemat"]));
    $return["nombre"]=$this->oracion(utf8_encode($return["nombre"]));
    
    
    
    return $return;
   }


   public function cargarCharts($idencuesta,$idgeneracion){
    
     $return=array();
      if($this->encuestaRespondida($idencuesta,$idgeneracion))
      {
       $sql="SELECT id,pregunta from preguntas WHERE idencuesta=$idencuesta ORDER by orden";
       $q=$this->mysqli->query($sql);
       $c=0;
       while($pregunta=$q->fetch_assoc())
       {
         $idpregunta=$pregunta["id"];
         $sql="SELECT id,opcion FROM opciones WHERE idpregunta=$idpregunta order by orden";
         $q2=$this->mysqli->query($sql);
         $return[$c]["id"]=$pregunta["id"];
         $return[$c]["pregunta"]=utf8_encode($pregunta["pregunta"]);
         while($opciones=$q2->fetch_assoc()){
           $idopcion=$opciones["id"];
           unset($opciones["id"]);
           $opcion=$opciones["opcion"];
           $sql="SELECT count(idopcion) as total FROM respuestas WHERE idgeneracion=$idgeneracion AND idopcion=$idopcion";
           $q3=$this->mysqli->query($sql);
           $f=$q3->fetch_assoc();
           $total=$f["total"];
           $chart["title"]=utf8_encode($opcion);
           $chart["value"]=$total;
           $return[$c]["chart"][]=$chart;
           $q3->close();
           //print_r($chart);
         }
         $c++;
         $q2->close();
       }
       $q->close();
     }
     else
     {
       $return["code"]=1;
     }
     
     return $return;     
   }
   
   
   private function encuestaRespondida($idencuesta,$idgeneracion) {
    $sql="SELECT idencuesta FROM alumno_encuesta WHERE idalumno IN (SELECT idalumno FROM alumno_grupo WHERE idgeneracion=$idgeneracion)";
    $q=$this->mysqli->query($sql);
		$count = $q->num_rows;
    $q->close();
    if($count == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
   }
   
  public function fbPublicar($texto){
    require_once("../inc/facebook.php");
    include("config.php");
    
    $facebook = new Facebook(array(
      'appId'  => $appId,
      'secret' => $appSecret,
      'cookie' => true
    ));
    
    if($facebook->getUser()){
      $ret = $facebook->api("/me/feed", 'post', array(
        //'message' => "He completado mi registro en radar|dgeti, que esperas unete tambien", 
        'message' => $texto, 
        'link'    => $this->base_url,
        'picture' => 'http://fbcdn-photos-a.akamaihd.net/photos-ak-snc7/v43/145/142197315929821/app_115_142197315929821_1881202694.png',
        'name'    => 'radar-dgeti',
        'description'=> 'Sistema de Seguimiento de Egresados de la DGETI'
      )
    );
  }
    
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
  
  public function listaMensajes($idalumno){
    $sql="SELECT * from mensajes where idalumno=$idalumno";
    $q=$this->mysqli->query($sql);
    while($mensaje=$q->fetch_assoc()){
      $men=$mensaje["mensaje"];
      echo "<div class='alert alert-info'>$men</div>";
    }
  }
  
 
  
  private function oracion($str){
     $cadena = strtr(strtoupper($str), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú");
     $cadena = ucwords(strtolower($cadena));   
     return $cadena;
   }
  
  private function ago($dt)
  {
      $dt = date_parse($dt);
      $now = date_parse(date("Y-m-d H:i:s"));

      $prefix = "Hace ";
      $suffix = "";

      if ($now['year'] != $dt['year']) return $prefix . $this->pluralize($now['year'] - $dt['year'], "año") . $suffix;
      if ($now['month'] != $dt['month']) return $prefix . $this->pluralize($now['month'] - $dt['month'], "mes") . $suffix;
      if ($now['day'] != $dt['day']) return $prefix . $this->pluralize($now['day'] - $dt['day'], "dia") . $suffix;
      if ($now['hour'] != $dt['hour']) return $prefix . $this->pluralize($now['hour'] - $dt['hour'], "hora") . $suffix;
      if ($now['minute'] != $dt['minute']) return $prefix . $this->pluralize($now['minute'] - $dt['minute'], "minuto") . $suffix;
      if ($now['second'] != $dt['second']) return $prefix . $this->pluralize($now['second'] - $dt['second'], "segundo") . $suffix;
          return "Ahora";
  }

  private function pluralize($count, $text) 
  { 
      return $count . (($count == 1) ? (" $text") : (" ${text}s"));
  }
   
   
}

?>
