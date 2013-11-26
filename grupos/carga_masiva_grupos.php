<?php
error_reporting(E_ALL);
set_time_limit(0);

$archivo=$_REQUEST["archivo"];
$id_plantel=$_REQUEST["id_plantel"];

/** PHPExcel_IOFactory */
include '../class/PHPExcel/IOFactory.php';
include '../class/class.grupo.php';
include("../class/auth.class.php");

$grupo = new Grupo;
$auth = new Auth;
$hash = $_COOKIE['auth_session'];
$uid = $auth->getUid($hash);

 function mayus($str){
   $str=utf8_decode($str);
   $cadena = strtr(strtoupper($str),"àáâãäåæçèéêëìíîïðñòóôõöøùüú","ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ");
   return $cadena; 
 }

$inputFileName = "server/php/files/$archivo";

$inputFileType = 'Excel2007';
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objReader->setReadDataOnly(true);
//$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
if(!$objPHPExcel = $objReader->load($inputFileName)){
  echo "no2";
  exit();
}

$objWorksheet = $objPHPExcel->getSheetByName('Grupos');

$highestRow = $objWorksheet->getHighestRow();


$id_plantel_plantilla=$objWorksheet->getCellByColumnAndRow(3,3)->getValue();
//$plantel=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();

if($id_plantel!=$id_plantel_plantilla){
  $result["code"]=0;
  $result["error"]="<strong>Error!</strong> La plantilla no corresponde al plantel seleccionado";
  $result=json_encode($result);
  echo $result;
  exit();
}

$i=0;
$max=$highestRow-5;
$return["error"]="";
$correcto=0;
for ($row = 6; $row <= $highestRow; ++$row) {
  ++$i;
  $generacion=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
  $especialidad=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
  $grupo2=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
  $turno=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();   
  
  $especialidad = explode(" -", $especialidad);
  $id_especialidad=$especialidad[1];
  $turno=($turno=="Matutino")?1:2;
  
  $generacion=$grupo->getIdGeneracion($generacion);
  if(!$generacion){
    $return["error"].="<p><strong>Error! Fila $row:</strong> Generaci&oacute;n Invalida</p>";
  }
  else{

    $aux["plantel"]=$id_plantel;
    $aux["especialidad"]=$id_especialidad;
    $aux["grupo"]=$grupo2;
    $aux["turno"]=$turno;
    $aux["generacion"]=$generacion;

    $data=$grupo->agregarGrupo($aux);

    if($data["code"]==1){
      $return["error"].="<p><span class='text-error'><i class='icon2-exclamation-sign'></i> Intenta agregar un grupo en la fila <strong>$row</strong> que ya esta en Sistema</span></p>";
    }else {
      $correcto++;
    }

    
    //sleep(2);
  }
  $myFile = "progreso$uid.json";
  $fh = fopen($myFile, 'w') or die("can't open file");
  $return["max"]=$max;
  $return["item"]=$i;
  $return["correcto"]=$correcto;
  $return2=json_encode($return);
  fwrite($fh,$return2);
  fclose($fh);
} 

unlink($inputFileName);




/*
$ban=0;
foreach ($objWorksheet->getRowIterator() as $row) {
	if(!$ban){
		$ban=1;
	}
	else
	{
		$clave=strtoupper($especialidad["A"]);
		$nombre=$nombre_corto=mayus($especialidad["B"]);
		$observacion=mayus($especialidad["C"]);
			
		$sql="insert into especialidad (clave,nombre,nombre_corto,observacion) values (
				'$clave','$nombre','$nombre_corto','$observacion')";
				
		
		$mysqli->query($sql);

	}
}
*/
/*
$return["max"]=$i;
$return["id_plantel"]=$id_plantel;
$return = json_encode($return);
 //echo $return;*/
?>
