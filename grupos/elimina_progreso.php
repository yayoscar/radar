<?php
error_reporting(E_ALL);
set_time_limit(0);

$archivo=$_REQUEST["archivo"];

unlink($archivo);

?>
