<?php 

$db = "gestionproject";
$servidor = "127.0.0.1";
$usuario = "root";
$clave = "1234";

// Crear conexión con MySQLi
$conexion = new mysqli($servidor, $usuario, $clave, $db);
$conexion->set_charset("utf8");

?>