<?php

set_time_limit(0);

/** PHPExcel_IOFactory */
include '../class/PHPExcel/IOFactory.php';





$objOrigen=PHPExcel_IOFactory::load('Curri2013.xls');

$objOrigensheet=$objOrigen->getActiveSheet();


$objDestino=PHPExcel_IOFactory::load('curri.xlsx');
$objDestinosheet=$objDestino->getActiveSheet();
$i = 0;
foreach ($objOrigen->getWorksheetIterator() as $objOrigensheet) { 


	//$objOrigensheet=$objOrigen->getSheet($i);


	$aux=$objOrigensheet->getCell('K4')->getValue();
	$aux2=$objDestinosheet->getCell('B9')->getValue();
	$aux2.=" ".$aux;
	$objDestinosheet->setCellValue("B9",  $aux2);

	//nombre
	$apepat=$aux=$objOrigensheet->getCell('A7')->getValue();
	$objDestinosheet->setCellValue("B11",  $aux);
	$apemat=$aux=$objOrigensheet->getCell('E7')->getValue();
	$objDestinosheet->setCellValue("F11",  $aux);
	$nombre=$aux=$objOrigensheet->getCell('I7')->getValue();
	$objDestinosheet->setCellValue("I11",  $aux);

	//formacion academica
	$aux=$objOrigensheet->getCell('A13')->getValue();
	$objDestinosheet->setCellValue("B15",  $aux);
	$objDestinosheet->setCellValue("E15","LICENCIATURA");
	$aux=$objOrigensheet->getCell('A15')->getValue();
	if(strlen($aux)>1)$objDestinosheet->setCellValue("E16","ESPECIALIDAD");
	$objDestinosheet->setCellValue("B16",  $aux);
	$aux=$objOrigensheet->getCell('A17')->getValue();
	if(strlen($aux)>1) $objDestinosheet->setCellValue("E17","MAESTRIA");
	$objDestinosheet->setCellValue("B17",  $aux);
	$aux=$objOrigensheet->getCell('A19')->getValue();
	if(strlen($aux)>1) $objDestinosheet->setCellValue("E17","DOCTORADO");
	$objDestinosheet->setCellValue("B18",  $aux);

	$aux=$objOrigensheet->getCell('F13')->getValue();
	$objDestinosheet->setCellValue("H15",  $aux);
	$aux=$objOrigensheet->getCell('F15')->getValue();
	$objDestinosheet->setCellValue("H16",  $aux);
	$aux=$objOrigensheet->getCell('F17')->getValue();
	$objDestinosheet->setCellValue("H17",  $aux);
	$aux=$objOrigensheet->getCell('F19')->getValue();
	$objDestinosheet->setCellValue("H18",  $aux);

	$aux=$objOrigensheet->getCell('J13')->getValue();
	$objDestinosheet->setCellValue("J15",  $aux);
	$aux=$objOrigensheet->getCell('J15')->getValue();
	$objDestinosheet->setCellValue("J16",  $aux);
	$aux=$objOrigensheet->getCell('J17')->getValue();
	$objDestinosheet->setCellValue("J17",  $aux);
	$aux=$objOrigensheet->getCell('J19')->getValue();
	$objDestinosheet->setCellValue("J18",  $aux);

	//programas de certificacion
	$aux=$objOrigensheet->getCell('A23')->getValue();
	$objDestinosheet->setCellValue("B24",  $aux);
	$aux=$objOrigensheet->getCell('A24')->getValue();
	$objDestinosheet->setCellValue("B25",  $aux);
	$aux=$objOrigensheet->getCell('F23')->getValue();
	$objDestinosheet->setCellValue("G24",  $aux);
	$aux=$objOrigensheet->getCell('F24')->getValue();
	$objDestinosheet->setCellValue("G25",  $aux);
	$aux=$objOrigensheet->getCell('J23')->getValue();
	$objDestinosheet->setCellValue("K24",  $aux);
	$aux=$objOrigensheet->getCell('J24')->getValue();
	$objDestinosheet->setCellValue("K25",  $aux);


	//formacion continua
	$aux=$objOrigensheet->getCell('A28')->getValue();
	$objDestinosheet->setCellValue("B30",  $aux);
	$aux=$objOrigensheet->getCell('A29')->getValue();
	$objDestinosheet->setCellValue("B31",  $aux);
	$aux=$objOrigensheet->getCell('A30')->getValue();
	$objDestinosheet->setCellValue("B32",  $aux);
	$aux=$objOrigensheet->getCell('A31')->getValue();
	$objDestinosheet->setCellValue("B33",  $aux);

	$aux=$objOrigensheet->getCell('F28')->getValue();
	$objDestinosheet->setCellValue("G30",  $aux);
	$aux=$objOrigensheet->getCell('F29')->getValue();
	$objDestinosheet->setCellValue("G31",  $aux);
	$aux=$objOrigensheet->getCell('F30')->getValue();
	$objDestinosheet->setCellValue("G32",  $aux);
	$aux=$objOrigensheet->getCell('F31')->getValue();
	$objDestinosheet->setCellValue("G33",  $aux);

	$aux=$objOrigensheet->getCell('J28')->getValue();
	$objDestinosheet->setCellValue("J30",  $aux);
	$aux=$objOrigensheet->getCell('J29')->getValue();
	$objDestinosheet->setCellValue("J31",  $aux);
	$aux=$objOrigensheet->getCell('J30')->getValue();
	$objDestinosheet->setCellValue("J32",  $aux);
	$aux=$objOrigensheet->getCell('J31')->getValue();
	$objDestinosheet->setCellValue("J33",  $aux);

	//CARRERA ACADEMICA
	$aux=$objOrigensheet->getCell('A37')->getValue();
	$objDestinosheet->setCellValue("B38",  $aux);
	$aux=$objOrigensheet->getCell('A38')->getValue();
	$objDestinosheet->setCellValue("B39",  $aux);
	$aux=$objOrigensheet->getCell('A39')->getValue();
	$objDestinosheet->setCellValue("B40",  $aux);
	$aux=$objOrigensheet->getCell('A40')->getValue();
	$objDestinosheet->setCellValue("B41",  $aux);

	$aux=$objOrigensheet->getCell('F37')->getValue();
	$objDestinosheet->setCellValue("G38",  $aux);
	$aux=$objOrigensheet->getCell('F38')->getValue();
	$objDestinosheet->setCellValue("G39",  $aux);
	$aux=$objOrigensheet->getCell('F39')->getValue();
	$objDestinosheet->setCellValue("G40",  $aux);
	$aux=$objOrigensheet->getCell('F40')->getValue();
	$objDestinosheet->setCellValue("G41",  $aux);

	$aux=$objOrigensheet->getCell('J37')->getValue();
	$objDestinosheet->setCellValue("J38",  $aux);
	$aux=$objOrigensheet->getCell('J38')->getValue();
	$objDestinosheet->setCellValue("J39",  $aux);
	$aux=$objOrigensheet->getCell('J39')->getValue();
	$objDestinosheet->setCellValue("J40",  $aux);
	$aux=$objOrigensheet->getCell('J40')->getValue();
	$objDestinosheet->setCellValue("J41",  $aux);

	$aux=$objOrigensheet->getCell('K37')->getValue();
	$objDestinosheet->setCellValue("K38",  $aux);
	$aux=$objOrigensheet->getCell('K38')->getValue();
	$objDestinosheet->setCellValue("K39",  $aux);
	$aux=$objOrigensheet->getCell('K39')->getValue();
	$objDestinosheet->setCellValue("K40",  $aux);
	$aux=$objOrigensheet->getCell('K40')->getValue();
	$objDestinosheet->setCellValue("K41",  $aux);

	//OTRAS ACTIVIDADES
	$aux=$objOrigensheet->getCell('A46')->getValue();
	$objDestinosheet->setCellValue("B45",  $aux);
	$aux=$objOrigensheet->getCell('A47')->getValue();
	$objDestinosheet->setCellValue("B46",  $aux);
	$aux=$objOrigensheet->getCell('A48')->getValue();
	$objDestinosheet->setCellValue("B47",  $aux);
	$aux=$objOrigensheet->getCell('A49')->getValue();
	$objDestinosheet->setCellValue("B48",  $aux);

	$aux=$objOrigensheet->getCell('F46')->getValue();
	$objDestinosheet->setCellValue("G45",  $aux);
	$aux=$objOrigensheet->getCell('F47')->getValue();
	$objDestinosheet->setCellValue("G46",  $aux);
	$aux=$objOrigensheet->getCell('F48')->getValue();
	$objDestinosheet->setCellValue("G47",  $aux);
	$aux=$objOrigensheet->getCell('F49')->getValue();
	$objDestinosheet->setCellValue("G48",  $aux);

	$aux=$objOrigensheet->getCell('J46')->getValue();
	$objDestinosheet->setCellValue("J45",  $aux);
	$aux=$objOrigensheet->getCell('J47')->getValue();
	$objDestinosheet->setCellValue("J46",  $aux);
	$aux=$objOrigensheet->getCell('J48')->getValue();
	$objDestinosheet->setCellValue("J47",  $aux);
	$aux=$objOrigensheet->getCell('J49')->getValue();
	$objDestinosheet->setCellValue("J48",  $aux);

	//ASIGANTURAS
	$aux=$objOrigensheet->getCell('A59')->getValue();
	$objDestinosheet->setCellValue("B57",  $aux);
	$aux=$objOrigensheet->getCell('A60')->getValue();
	$objDestinosheet->setCellValue("B58",  $aux);
	$aux=$objOrigensheet->getCell('A61')->getValue();
	$objDestinosheet->setCellValue("B59",  $aux);
	$aux=$objOrigensheet->getCell('A62')->getValue();
	$objDestinosheet->setCellValue("B60",  $aux);
	$aux=$objOrigensheet->getCell('A63')->getValue();
	$objDestinosheet->setCellValue("B61",  $aux);
	$aux=$objOrigensheet->getCell('A64')->getValue();
	$objDestinosheet->setCellValue("B62",  $aux);
	$aux=$objOrigensheet->getCell('A65')->getValue();
	$objDestinosheet->setCellValue("B63",  $aux);
	$aux=$objOrigensheet->getCell('A66')->getValue();
	$objDestinosheet->setCellValue("B64",  $aux);

	$aux=$objOrigensheet->getCell('E59')->getValue();
	$objDestinosheet->setCellValue("F57",  $aux);
	$aux=$objOrigensheet->getCell('E60')->getValue();
	$objDestinosheet->setCellValue("F58",  $aux);
	$aux=$objOrigensheet->getCell('E61')->getValue();
	$objDestinosheet->setCellValue("F59",  $aux);
	$aux=$objOrigensheet->getCell('E62')->getValue();
	$objDestinosheet->setCellValue("F60",  $aux);
	$aux=$objOrigensheet->getCell('E63')->getValue();
	$objDestinosheet->setCellValue("F61",  $aux);
	$aux=$objOrigensheet->getCell('E64')->getValue();
	$objDestinosheet->setCellValue("F62",  $aux);
	$aux=$objOrigensheet->getCell('E65')->getValue();
	$objDestinosheet->setCellValue("F63",  $aux);
	$aux=$objOrigensheet->getCell('E66')->getValue();
	$objDestinosheet->setCellValue("F64",  $aux);

	$aux=$objOrigensheet->getCell('F59')->getValue();
	$objDestinosheet->setCellValue("G57",  $aux);
	$aux=$objOrigensheet->getCell('F60')->getValue();
	$objDestinosheet->setCellValue("G58",  $aux);
	$aux=$objOrigensheet->getCell('F61')->getValue();
	$objDestinosheet->setCellValue("G59",  $aux);
	$aux=$objOrigensheet->getCell('F62')->getValue();
	$objDestinosheet->setCellValue("G60",  $aux);
	$aux=$objOrigensheet->getCell('F63')->getValue();
	$objDestinosheet->setCellValue("G61",  $aux);
	$aux=$objOrigensheet->getCell('F64')->getValue();
	$objDestinosheet->setCellValue("G62",  $aux);
	$aux=$objOrigensheet->getCell('F65')->getValue();
	$objDestinosheet->setCellValue("G63",  $aux);
	$aux=$objOrigensheet->getCell('F66')->getValue();
	$objDestinosheet->setCellValue("G64",  $aux);

	$aux=$objOrigensheet->getCell('G59')->getValue();
	$objDestinosheet->setCellValue("H57",  $aux);
	$aux=$objOrigensheet->getCell('G60')->getValue();
	$objDestinosheet->setCellValue("H58",  $aux);
	$aux=$objOrigensheet->getCell('G61')->getValue();
	$objDestinosheet->setCellValue("H59",  $aux);
	$aux=$objOrigensheet->getCell('G62')->getValue();
	$objDestinosheet->setCellValue("H60",  $aux);
	$aux=$objOrigensheet->getCell('G63')->getValue();
	$objDestinosheet->setCellValue("H61",  $aux);
	$aux=$objOrigensheet->getCell('G64')->getValue();
	$objDestinosheet->setCellValue("H62",  $aux);
	$aux=$objOrigensheet->getCell('G65')->getValue();
	$objDestinosheet->setCellValue("H63",  $aux);
	$aux=$objOrigensheet->getCell('G66')->getValue();
	$objDestinosheet->setCellValue("H64",  $aux);

	$aux=$objOrigensheet->getCell('I59')->getValue();
	$objDestinosheet->setCellValue("I57",  $aux);
	$aux=$objOrigensheet->getCell('I60')->getValue();
	$objDestinosheet->setCellValue("I58",  $aux);
	$aux=$objOrigensheet->getCell('I61')->getValue();
	$objDestinosheet->setCellValue("I59",  $aux);
	$aux=$objOrigensheet->getCell('I62')->getValue();
	$objDestinosheet->setCellValue("I60",  $aux);
	$aux=$objOrigensheet->getCell('I63')->getValue();
	$objDestinosheet->setCellValue("I61",  $aux);
	$aux=$objOrigensheet->getCell('I64')->getValue();
	$objDestinosheet->setCellValue("I62",  $aux);
	$aux=$objOrigensheet->getCell('II5')->getValue();
	$objDestinosheet->setCellValue("I63",  $aux);
	$aux=$objOrigensheet->getCell('I66')->getValue();
	$objDestinosheet->setCellValue("I64",  $aux);

	$aux=$objOrigensheet->getCell('J59')->getValue();
	$objDestinosheet->setCellValue("J57",  $aux);
	$aux=$objOrigensheet->getCell('J60')->getValue();
	$objDestinosheet->setCellValue("J58",  $aux);
	$aux=$objOrigensheet->getCell('J61')->getValue();
	$objDestinosheet->setCellValue("J59",  $aux);
	$aux=$objOrigensheet->getCell('J62')->getValue();
	$objDestinosheet->setCellValue("J60",  $aux);
	$aux=$objOrigensheet->getCell('J63')->getValue();
	$objDestinosheet->setCellValue("J61",  $aux);
	$aux=$objOrigensheet->getCell('J64')->getValue();
	$objDestinosheet->setCellValue("J62",  $aux);
	$aux=$objOrigensheet->getCell('JI5')->getValue();
	$objDestinosheet->setCellValue("J63",  $aux);
	$aux=$objOrigensheet->getCell('J66')->getValue();
	$objDestinosheet->setCellValue("J64",  $aux);


	$objWriter = PHPExcel_IOFactory::createWriter($objDestino, 'Excel2007');
	/*
	// We'll be outputting an excel file
	header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

	// It will be called file.xls
	header('Content-Disposition: attachment; filename="'.$plantel.'_alumnos.xlsx"');/*

	// Write file to the browser*/
	$nombrefull=$apepat." ".$apemat." ".$nombre;
	$objWriter->save("$nombrefull.xlsx");

}

?>