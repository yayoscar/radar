<?php
include("class/class.radar.php");
$radar=new Radar;
$hash = $_COOKIE['auth_session'];
$hash2 = $_COOKIE['PHPSESSID'];
$radar->logout($hash);
setcookie("PHPSESSID", $hash2, time() - 3600);
session_start();
session_destroy();

header("Location: index.php");

exit();

?>