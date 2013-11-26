<?php
/**
 * Clase para el manejo de grupos
 *
 * @author Oscar Pérez Olan
 */
class Grupo {
  private $mysqli;
	/*
	* Conexion con la BD
	*/
	public function __construct()
	{
		include("config.php");
	
		$this->mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']); 
	}
  
  public function getInfoPlantel($idplantel)
  {
    $q=$this->mysqli->query("SELECT id,plantel,numero,nombre FROM plantel WHERE id=$idplantel");
    $f=$q->fetch_assoc();
    $q->close();
    return $f;
    
  }
  
  public function getPlantel($idgrupo)
  {
    $q=$this->mysqli->query("SELECT idplantel FROM grupos WHERE id=$idgrupo");
    $f=$q->fetch_assoc();
    $q->close();
    $id_plantel=$f["idplantel"];
    $q=$this->mysqli->query("SELECT id,plantel,numero,nombre FROM plantel WHERE id=$id_plantel");
    $f=$q->fetch_assoc();
    $q->close();
    return $f;
    
  }
  
  public function getGrupo($idgrupo)
  {
    $return=array();
    $q=$this->mysqli->query("SELECT idespecialidad,idgeneracion,grupo,turno FROM grupos WHERE id=$idgrupo");
    list($idespecialidad,$idgeneracion,$return["grupo"],$return["turno"])=$q->fetch_row();
    $q->close();
    
    $q=$this->mysqli->query("SELECT nombre_corto FROM especialidad WHERE id=$idespecialidad");
    list($return["especialidad"])=$q->fetch_row();
    $return["especialidad"]=$this->oracion(utf8_encode($return["especialidad"]));
    $q->close();
    
    $q=$this->mysqli->query("SELECT abrev FROM generacion WHERE id=$idgeneracion");
    list($return["generacion"])=$q->fetch_row();
    $q->close();
    
    $return["turno"] = ($return["turno"]==1) ? "TM" : "TV";
    return $return;
    
  }
  
  public function listaEspecialidad()
  {
    $query = $this->mysqli->query("SELECT id,clave,nombre_corto as texto FROM especialidad ORDER BY nombre");
    while($fila=$query->fetch_assoc())
    {
      
      $fila["texto"]= "[".$fila["clave"]."] ".$this->oracion(utf8_encode($fila["texto"]));
      $return[]=$fila;
    }
    $query->close();
        
    return $return;
   }
   
   public function listaGeneracion()
  {
    $query = $this->mysqli->query("SELECT id,generacion as texto FROM generacion ORDER BY generacion");
    while($fila=$query->fetch_assoc())
    {
      $return[]=$fila;
    }
    $query->close();
        
    return $return;
   }
  
  
  public function agregarGrupo($datos) 
  {
    $return=array();
    if(!$this->repetido($datos))
    {
      //plantel agregado con exito
      $idgrupo=$this->addGrupo($datos);
      $return["idgrupo"]=$idgrupo;   
      $return["code"]=2;
      $return["grupo"]=$this->listaGrupo($idgrupo);
      return $return;  
    }
    else {
      //Grupo ya existente
      $return["code"]=1;
      return $return;
    }
          
  }
  
  private function repetido($datos)
	{
		$query = $this->mysqli->prepare("SELECT id FROM grupos WHERE idplantel=? AND grupo=? AND idespecialidad=? AND idgeneracion=? AND turno=?");
		$query->bind_param("isiii", $datos["plantel"],$datos["grupo"],$datos["especialidad"],$datos["generacion"],$datos["turno"]);
		$query->bind_result($idplantel);
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
			return true;
		}
	}
  

  private function addGrupo($datos)
	{
		list($idplantel,$especialidad,$grupo,$turno,$generacion)=array_values((array) $datos);
    $query = $this->mysqli->prepare("INSERT INTO grupos (idplantel,idespecialidad,idgeneracion,grupo,turno) VALUES (?,?,?,?,?)");
		$query->bind_param("iiisi", $idplantel,$especialidad,$generacion,$grupo,$turno);
		$query->execute();
		$idgrupo = $query->insert_id;
		
    $query->close();
    
    return $idgrupo;
  }
  
  public function listaGrupos($plantel)
  {
    $query = $this->mysqli->query("SELECT * FROM grupos WHERE idplantel=$plantel");
    while($fila=$query->fetch_assoc())
    {
      $aux["id"]=$fila["id"];
      
      $especialidad=$fila["idespecialidad"];
      
      $q2=$this->mysqli->query("SELECT * FROM especialidad WHERE id=$especialidad");
      $f2=$q2->fetch_assoc();
      $q2->close();
      $aux["especialidad"]=$this->oracion(utf8_encode($f2["nombre"]));
      
      
      $generacion=$fila["idgeneracion"];
      $q2=$this->mysqli->query("SELECT * FROM generacion WHERE id=$generacion");
      $f2=$q2->fetch_assoc();
      $q2->close();
      $aux["generacion"]=$f2["generacion"];
      
      $aux["grupo"]=$fila["grupo"];
      
      $aux["turno"] = ($fila["turno"]==1) ? "Matutino" : "Vespertino";
      
      $return[]=$aux;
      
    }
    $query->close();
        
    return $return;
   }
  
   private function listaGruposId($idgrupo)
  {
    $query = $this->mysqli->query("SELECT idplantel,idespecialidad,idgeneracion,turno FROM grupos WHERE id=$idgrupo");
    $fila=$query->fetch_assoc();
    $query->close();
    return $fila;
   }
   
 public function agregarAlumno($datos) 
  {
    $return=array();
    if(!$this->alumnoRepetido($datos))
    {
      //Agregar alumno a la BD
      $idalumno=$this->addAlumno($datos);
      $this->addAlumnoGrupo($idalumno,$datos["grupo"]);
      $return["idalumno"]=$idalumno;   
      $return["code"]=2;
      $return["alumno"]=$this->listaAlumno($idalumno);
      $this->insertarCodigo($idalumno);
      return $return;  
    }
    else {
      //Alumno ya existente
      $return["code"]=1;
      return $return;
    }
  }
  
  private function alumnoRepetido($datos)
  {
    $query = $this->mysqli->prepare("SELECT num_con FROM alumnos WHERE num_con=? OR curp=?");
		$query->bind_param("ss", $datos["numcon"],$datos["curp"]);
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
			return true;
		}
  }
  
  private function addAlumno($datos)
	{
		$activo=1;
    list($idgrupo,$num_con,$nombre,$apepat,$apemat,$curp)=array_values((array) $datos);
    $nombre=utf8_decode($nombre);
    $apepat=utf8_decode($apepat);
    $apemat=utf8_decode($apemat);
    $curp=utf8_decode($curp);
    $query = $this->mysqli->prepare("INSERT INTO alumnos (num_con,nombre,apepat,apemat,curp,activo) VALUES (?,?,?,?,?,?)");
		$query->bind_param("sssssi", $num_con,$nombre,$apepat,$apemat,$curp,$activo);
		$query->execute();
		$idalumno = $query->insert_id;
		
    $query->close();
    
    return $idalumno;
  }
  
  private function addAlumnoGrupo($idalumno,$idgrupo)
	{
		$grupo=$this->listaGruposId($idgrupo);
    $idestado=$this->getEstado($grupo["idplantel"]);
    $query = $this->mysqli->prepare("INSERT INTO alumno_grupo (idalumno,idplantel,idestado,idgeneracion,idespecialidad,idgrupo,turno) VALUES (?,?,?,?,?,?,?)");
		$query->bind_param("iiiiiii", $idalumno,$grupo["idplantel"],$idestado,$grupo["idgeneracion"],$grupo["idespecialidad"],$idgrupo,$grupo["turno"]);
		$query->execute();
		
    $query->close();
    
   }
   
   private function getEstado($idplantel){
     $sql="SELECT idestado FROM plantel WHERE id=$idplantel";
     $q=$this->mysqli->query($sql);
     $f=$q->fetch_assoc();
     return $f["idestado"];
   }
   
   private function insertarCodigo($idalumno)
   {
     /*do {
      $codigo=$this->ae_gen_password();
     } while ($this->buscarCodigo($codigo)!=false);*/
     $alumno=$this->listaAlumno($idalumno);
     $codigo=$alumno["curp"];
     $query =$this->mysqli->query("INSERT INTO codigos (idalumno,codigo) values ($idalumno,'$codigo')");
   }
  
  private function ae_gen_password($silabas=1, $use_prefix = true)
  {
    // Definimos la función a menos de que esta exista
    if (!function_exists('ae_arr'))
    {
      // Esta función devuleve un elemento aleatorio
      function ae_arr(&$arr)
      {
        return $arr[rand(0, sizeof($arr)-1)];
      }
    }
    // Prefijos
    $prefix = array('', 'tavo', 'yayo', 'dogor', 'm',
      'cho', 'dansy', 'chu', 'sa', 'sim',
      'zini', 'alex', 'cbtis', 'aula', 'dgeti',
      'radar', 'tera', 'carri', 'cruz', 'monch');
    // Sufijos
    $suffix = array('on', 'ion', 'ancia', 'sion', 'ia',
     'dor', 'tor', 'sor', 'cion', 'acia');
    // Sonidos
    $vowels = array('a', 'o', 'e', 'i', 'u', 'ia', 'eo');
    // Consonantes
    $consonants = array('r', 't', 'p', 's', 'd', 'f', 'g', 'h', 'j',
      'k', 'l', 'z', 'c', 'v', 'b', 'n', 'm', 'qu');
    $password = $use_prefix?ae_arr($prefix):'';
    $password_suffix = ae_arr($suffix);
    for($i=0; $i<$silabas; $i++)
    {
      // Selecciona una consonante al azar
      $doubles = array('c', 'l', 'r');
      $c = ae_arr($consonants);
      if (in_array($c, $doubles)&&($i!=0)) {
        if (rand(0, 4) == 1) // 20% de probabiidad
          $c .= $c;
      }
      $password .= $c;
      //
      // Seleccionamos un sonido al azar
      $password .= ae_arr($vowels);
      if ($i == $silabas - 1) // Si el sufijo empieza con vocal
        if (in_array($password_suffix[0], $vowels)) // Añadimos una consonante
          $password .= ae_arr($consonants);
    }
    // Seleccionamos un sufijo aleatorio
    $password .= $password_suffix;
    return $password;
  }
   
   
  public function listaAlumnos($idgrupo)
  {
    $query = $this->mysqli->query("SELECT idalumno FROM alumno_grupo WHERE idgrupo=$idgrupo");
    while($fila=$query->fetch_assoc())
    {
      $idalumno=$fila["idalumno"];
      $aux=$this->listaAlumno($idalumno);
      $return[]=$aux;
      
    }
    $query->close();
        
    return $return;
   }
   
   private function listaAlumno($idalumno)
  {
    $q=$this->mysqli->query("SELECT * FROM alumnos WHERE id=$idalumno");
    $f=$q->fetch_assoc();
    $q->close();

    $return["id"]=$idalumno;
    $return["num_con"]=$f["num_con"];
    $return["nombre"]=$this->oracion(utf8_encode($f["apepat"]." ".$f["apemat"]." ".$f["nombre"]));;
    $return["curp"]=$f["curp"];
    $return["activo"]=$f["activo"];
    
    return $return;
   }
   
   private function oracion($str){
     $cadena = strtr(strtoupper($str), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú");
     $cadena = ucwords(strtolower($cadena));   
     return $cadena;
   }
   
  public function getEspecialidades()
  {
    $query = $this->mysqli->query("SELECT id,clave,nombre_corto FROM especialidad order by nombre");
    while($fila=$query->fetch_assoc())
    {
      $fila["nombre_corto"]=$this->oracion(utf8_encode($fila["nombre_corto"]));
      $return[]=$fila;
    }
    $query->close();
        
    return $return;
   }
   
   public function listaGrupo($idgrupo)
  {
    $query = $this->mysqli->query("SELECT * FROM grupos where id=$idgrupo");
    $fila=$query->fetch_assoc();
   
    $id_generacion=$fila["idgeneracion"];
    $q2=$this->mysqli->query("SELECT generacion FROM generacion WHERE id=$id_generacion");
    $f2=$q2->fetch_assoc();
    $aux["generacion"]=$f2["generacion"];
   
    $id_especialidad=$fila["idespecialidad"];
    $q2=$this->mysqli->query("SELECT nombre FROM especialidad WHERE id=$id_especialidad");
    $f2=$q2->fetch_assoc();
    
    $aux["especialidad"]=$this->oracion(utf8_encode($f2["nombre"]));
    
    $aux["grupo"]=$fila["grupo"];
      
    $aux["turno"] = ($fila["turno"]==1) ? "Matutino" : "Vespertino";
      
     
    
    $q2->close();
    $query->close();
    return $aux;
   }
   
   public function getIdGeneracion($generacion){
     $query = $this->mysqli->query("SELECT id FROM generacion where generacion='$generacion'");
     $count = $query->num_rows;
     if($count==0){
       return false;
     }
     $fila=$query->fetch_assoc();
     return $fila["id"];
   }
   
   private function buscarCodigo($codigo)
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
   
   
}
?>