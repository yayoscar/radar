<?php

set_time_limit(0);
$idencuesta=$_REQUEST["idencuesta"];
$idencuesta = explode("enc", $idencuesta);
$idencuesta=$idencuesta[1];
$idgeneracion=$_REQUEST["idgeneracion"];

/** PHPExcel_IOFactory */
include '../class/PHPExcel/IOFactory.php';
include '../class/class.radar.php';


$radar=new Radar();


$objPHPexcel=PHPExcel_IOFactory::load('reporte.xlsx');

$objWorksheet=$objPHPexcel->getSheetByName("Reporte");

//$nombreEncuesta=$radar->nombreEncuesta($idencuesta);

/*$objWorksheet->getCell('D1')->setValue($nombreEncuesta);

$preguntas=$radar->listaPreguntas2($idencuesta);
$i=5;
foreach($preguntas as $pregunta){
  $objWorksheet->setCellValueByColumnAndRow($i,5,"ok");
  $i++;
}
$respuestas=$radar->listaRespuestas($idencuesta,$idgeneracion);
$i=6;
foreach($respuestas as $respuesta){
  $objWorksheet->setCellValueByColumnAndRow(1,$i,$respuesta["num_con"]);
  $objWorksheet->setCellValueByColumnAndRow(2,$i,$respuesta["ape_pat"]);
  $objWorksheet->setCellValueByColumnAndRow(3,$i,$respuesta["ape_mat"]);
  $objWorksheet->setCellValueByColumnAndRow(4,$i,$respuesta["nombre"]);
  $i++;
}*/



//$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel2007');
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
// We'll be outputting an excel file
header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

// It will be called file.xls
header("Content-Disposition: attachment; filename='reporte.xlsx'");

// Write file to the browser
$objWriter->save('php://output');


?>