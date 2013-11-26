<?php
error_reporting(E_ALL);
set_time_limit(0);

$archivo=$_REQUEST["archivo"];
$id_plantel=$_REQUEST["id_plantel"];

/** PHPExcel_IOFactory */
include '../class/PHPExcel/IOFactory.php';
include '../class/class.grupo.php';
include ("../class/auth.class.php");

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

$objWorksheet = $objPHPExcel->getSheetByName('Alumnos');

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
  
 
  $textgrupo=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
  $auxgrupo = explode(" id-", $textgrupo);
  $aux['grupo']=$auxgrupo[1];
  $aux['numcon']=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
  $aux['nombre']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
  $aux['apepat']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
  $aux['apemat']=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
  $aux['curp']=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();   
     
  
      
  $data=$grupo->agregarAlumno($aux);
 
  if($data["code"]==1){
    $return["error"].="<p><span class='text-error'><i class='icon2-exclamation-sign'></i> Intenta agregar un Alumno en la fila <strong>$row</strong> que ya esta en Sistema</span></p>";
  }else {
    $correcto++;
  }

  //sleep(2);
  
  $myFile = "progresoalumnos$uid.json";
  $fh = fopen($myFile,'w') or die("can't open file");
  $return["max"]=$max;
  $return["item"]=$i;
  $return["correcto"]=$correcto;
  $return2=json_encode($return);
  fwrite($fh,$return2);
  fclose($fh);
} 

unlink($inputFileName);





?>
