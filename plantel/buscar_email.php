<?php
include("../class/config.php");

//Conectar con la DB Enlace
$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);

$term = $_REQUEST['term'];

$sql = "SELECT id,email from usuarios WHERE email LIKE '%".$term."%' order by email";
$r=$mysqli->query($sql);

while($fila=$r->fetch_assoc())
{
		$row['value']=$fila['email'];
		$row['id']=(int)$fila['id'];
		$row_set[] = $row;
}
$mysqli->close();
echo json_encode($row_set);//format the array into json data
?>