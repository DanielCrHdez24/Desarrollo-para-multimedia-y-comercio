<?php
// conexion.php

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "tiendavirtual");

// Verifica si hubo error de conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establece el conjunto de caracteres a UTF-8
$conexion->set_charset("utf8");
?>
