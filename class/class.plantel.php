<?php
require_once("auth.class.php");
/**
 * Clase para el manejo de planteles
 *
 * @author Oscar Pérez Olan
 */
class Plantel {
  private $mysqli;
	/*
	* Conexion con la BD
	*/
	public function __construct()
	{
		include("config.php");
	
		$this->mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']); 
	}
  
  /*
	* Agrega un nuevo plantel
	* @param string $plantel
	* @param int $numero
	* @param string $admin
	* @return array $return
	*/
  public function agregarPlantel($plantel,$numero,$admin,$idestado,$nombre) 
  {
    $return=array();
    if(!$this->getIdPlantel($plantel,$numero))
    {
      if($idAdmin=$this->getIdAdmin($admin))
      {
        if(!$rol=$this->isAdmin($idAdmin))
        {
          $idplantel=$this->addPlantel($plantel,$numero,$idestado,$idAdmin,$nombre);
          //plantel agregado con exito
          $return["code"]=2;
          $return["plantel"]=$this->listaPlantel($idplantel);
          return $return;  
        }
        else
        {
          if($rol==2){
            //El usuario es el administrador total
            $return["code"]=4;
            return $return;
          }
          else
          {
            $idplantel=$this->addPlantel($plantel,$numero,$idestado,$idAdmin,$nombre);
            //plantel agregado con exito
            $return["code"]=5;
            $return["plantel"]=$this->listaPlantel($idplantel);
            return $return;
          }
        }
      }
      else
      {
        $auth = new Auth();
        $password=sha1(sha1("antofagasta"));
        $auth->register($admin, $password);
        $idAdmin=$this->getIdAdmin($admin);
        $idplantel=$this->addPlantel($plantel,$numero,$idestado,$idAdmin,$nombre);
        //plantel agregado y usuario creado
        $return["code"]=3;
        $return["plantel"]=$this->listaPlantel($idplantel);
        return $return;
      }
      
    }
    else {
      //Plantel ya existente
      $return["code"]=1;
      return $return;
    }
          
  }
  
   public function agregarAdmin($idplantel,$admin) 
  {
    $return=array();
    
      if($idadmin=$this->getIdAdmin($admin))
      {
        if(!$rol=$this->isAdmin($idadmin))
        {
          $idplantel=$this->addAdmin($idplantel,$idadmin);
          //plantel agregado con exito
          $return["code"]=2;
          return $return;  
        }
        else
        {
          if($rol==1){
            //El usuario es el administrador total
            $return["code"]=4;
            return $return;
          }
          else
          {
            $this->addAdmin($idplantel,$idadmin);
            //plantel agregado con exito
            $return["code"]=5;
           
            return $return;
          }
        }
      }
      else
      {
        $auth = new Auth();
        $password=sha1(sha1("prototipos"));
        $auth->register($admin, $password);
        $idadmin=$this->getIdAdmin($admin);
        $return["code"]=3;
        return $return;
      }
      
    
          
  }
  
  /*
	* Obtiene el Id del Plantel
	*/
  private function getIdPlantel($plantel,$numero)
	{
		$query = $this->mysqli->prepare("SELECT id FROM plantel WHERE plantel = ? AND numero= ?");
		$query->bind_param("si", $plantel,$numero);
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
			return $idplantel;
		}
	}
  
  /*
	* Agrega un registro a la tabla plantel,adminplantel
	*/
  private function addPlantel($plantel, $numero, $idestado,$idAdmin,$nombre)
	{
		$query = $this->mysqli->prepare("INSERT INTO plantel (plantel,numero,idestado,nombre) VALUES (?,?,?,?)");
		$query->bind_param("siis", $plantel, $numero, $idestado,$nombre);
		$query->execute();
		$idPlantel = $query->insert_id;
		
    $query = $this->mysqli->prepare("INSERT INTO adminplantel (idplantel, idadmin) VALUES (?, ?)");
		$query->bind_param("ii", $idPlantel, $idAdmin);
		$query->execute();
    
    $rol=$this->isAdmin($idAdmin);
    if($rol!=1)
    {
      $query = $this->mysqli->prepare("UPDATE usuarios set rol=2 where id=?");
      $query->bind_param("i", $idAdmin);
      $query->execute();
    }
    
		$query->close();
    
    return $idPlantel;
    
    
	}
  
  private function addAdmin($idplantel,$idadmin)
	{
		$query = $this->mysqli->prepare("INSERT INTO adminplantel (idplantel, idadmin) VALUES (?, ?)");
		$query->bind_param("ii", $idplantel, $idadmin);
		$query->execute();
    
    $rol=$this->isAdmin($idadmin);
    if($rol!=1)
    {
      $query = $this->mysqli->prepare("UPDATE usuarios set rol=2 where id=?");
      $query->bind_param("i", $idadmin);
      $query->execute();
    }
    
		$query->close();
  }
  
   /*
	* Obtiene el Id del Plantel
	*/
  private function getIdAdmin($admin)
	{
		$query = $this->mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
		$query->bind_param("s", $admin);
		$query->bind_result($idAdmin);
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
			return $idAdmin;
		}
	}
  
  private function isAdmin($idAdmin)
  {
    $sql="SELECT rol FROM usuarios WHERE (rol=1 OR rol=2) AND id=$idAdmin";
    $q=$this->mysqli->query($sql);
		$f=$q->fetch_assoc();
    $count = $q->num_rows;
    $q->close();
		
		if($count == 0)
		{
			return false;
		}
		else
		{
			return $f["rol"];
		}
  }
  
  public function listaEstados()
  {
    $query = $this->mysqli->query("SELECT * FROM estados ORDER BY nombre");
    while($fila=$query->fetch_assoc())
    {
      $fila["nombre"]=  utf8_encode($fila["nombre"]);
      $return[]=$fila;
    }
    $query->close();
        
    return $return;
   }
  
  
  public function listaPlanteles()
  {
    $query = $this->mysqli->query("SELECT * FROM plantel");
    while($fila=$query->fetch_assoc())
    {
      $plantel=$fila["plantel"];
      $id_estado=$fila["idestado"];
      $fila["nombre"]=$this->oracion(utf8_encode($fila["nombre"]));
      $q2=$this->mysqli->query("SELECT nombre FROM estados WHERE id=$id_estado");
      $f2=$q2->fetch_assoc();
      $fila["nom_estado"]=$this->oracion(utf8_encode($f2["nombre"]));
      $fila["localidad"]=$this->oracion(utf8_encode($fila["localidad"]));
      $return[$plantel][]=$fila;
      $q2->close();
      
    }
    $query->close();
     
    return $return;
   }
   
   public function listaPlantel($idplantel)
  {
    $query = $this->mysqli->query("SELECT * FROM plantel where id=$idplantel");
    $fila=$query->fetch_assoc();
    $id_estado=$fila["idestado"];
    $fila["nombre"]=$this->oracion(utf8_encode($fila["nombre"]));
    $q2=$this->mysqli->query("SELECT nombre FROM estados WHERE id=$id_estado");
    $f2=$q2->fetch_assoc();
    $fila["nom_estado"]=$this->oracion(utf8_encode($f2["nombre"]));
    $fila["localidad"]=$this->oracion(utf8_encode($fila["localidad"]));
    
    $q2->close();
    $query->close();
    return $fila;
   }
   
   private function oracion($str){
     $cadena = strtr(strtoupper($str), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú");
     $cadena = ucwords(strtolower($cadena));   
     return $cadena;
   }
   
}
?>