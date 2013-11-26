<?php

set_time_limit(0);
$id_plantel=$_REQUEST["id_plantel"];
$plantel=$_REQUEST["plantel"];

/** PHPExcel_IOFactory */
include '../class/PHPExcel/IOFactory.php';
include '../class/class.grupo.php';


$grupo=new Grupo();


$especialidades=$grupo->getEspecialidades();


$objPHPexcel=PHPExcel_IOFactory::load('plantilla_grupos.xlsx');

$objWorksheet=$objPHPexcel->getSheetByName("config");
$i=0;
foreach($especialidades as $especialidad){
  $i++;
  $celda=$especialidad["nombre_corto"]." [".$especialidad["clave"]."] -".$especialidad["id"];
  $objWorksheet->setCellValue("A$i", $celda);  
  
}


$objWorksheet = $objPHPexcel->getSheetByName("Grupos");

$objWorksheet->getCell('D1')->setValue("Plantel: $plantel");
$objWorksheet->getCell('id_plantel')->setValue($id_plantel);

$objValidation=$objWorksheet->getCell('C6') ->getDataValidation();
$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
$objValidation->setAllowBlank(false);
$objValidation->setShowInputMessage(true);
$objValidation->setShowErrorMessage(true);
$objValidation->setShowDropDown(true);
$objValidation->setErrorTitle('Error');
$objValidation->setError('Elija una especialidad de la lista');
$objValidation->setPromptTitle('Elija una Especialidad');
//$objValidation->setPrompt('Please pick a value from the drop-down list.');
$validar='config!$A1:$A$'.$i;
$objValidation->setFormula1($validar);

$objValidationGen=$objWorksheet->getCell('B6') ->getDataValidation();
$objValidationGen->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
$objValidationGen->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
$objValidationGen->setAllowBlank(false);
$objValidationGen->setShowInputMessage(true);
$objValidationGen->setShowErrorMessage(true);
$objValidationGen->setShowDropDown(true);
$objValidationGen->setErrorTitle('Error');
$objValidationGen->setError('Elija una Generación de la lista');
$objValidationGen->setPromptTitle('Elija una Generación');
//$objValidation->setPrompt('Please pick a value from the drop-down list.');
//$validar='config!$B1:$A$'.$i;
$objValidationGen->setFormula1('config!$B1:$B$1');

$objValidationTurno=$objWorksheet->getCell('E6') ->getDataValidation();
$objValidationTurno->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
$objValidationTurno->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
$objValidationTurno->setAllowBlank(false);
$objValidationTurno->setShowInputMessage(true);
$objValidationTurno->setShowErrorMessage(true);
$objValidationTurno->setShowDropDown(true);
$objValidationTurno->setErrorTitle('Error');
$objValidationTurno->setError('Elija un Turno de la lista');
$objValidationTurno->setPromptTitle('Elija un Turno');
//$objValidation->setPrompt('Please pick a value from the drop-down list.');
//$validar='config!$B1:$A$'.$i;
$objValidationTurno->setFormula1('config!$C1:$C$2');

for($x=7;$x<=57;$x++){
  $objWorksheet->getCell("B$x")->setDataValidation(clone $objValidationGen);
  $objWorksheet->getCell("C$x")->setDataValidation(clone $objValidation);
  $objWorksheet->getCell("E$x")->setDataValidation(clone $objValidationTurno);
}


$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel2007');

// We'll be outputting an excel file
header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

// It will be called file.xls
header('Content-Disposition: attachment; filename="'.$plantel.'_grupos.xlsx"');

// Write file to the browser
$objWriter->save('php://output');



?>