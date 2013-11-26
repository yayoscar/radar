<?php
<<<<<<< HEAD
$mysqli = new mysqli('cbtis72.edu.mx', 'cbtised0_admin', 'atlante3', 'cbtised0_radar');
=======
$mysqli = new mysqli('localhost', 'cbtised0_admin', 'atlante3', 'cbtised0_radar');
>>>>>>> 09433c88f4fee77b865e4115791bafb36db74cbf

/*
 * Esta es la forma OO "oficial" de hacerlo,
 * AUNQUE $connect_error estaba averiado hasta PHP 5.2.9 y 5.3.0.
 */
if ($mysqli->connect_error) {
    die('Error de Conexión (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

/*
 * Use esto en lugar de $connect_error si necesita asegurarse
 * de la compatibilidad con versiones de PHP anteriores a 5.2.9 y 5.3.0.
 */
if (mysqli_connect_error()) {
    die('Error de Conexión (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}

echo 'Éxito con git y con deploy automatico	... ' . $mysqli->host_info . "\n";

$mysqli->close();
?>