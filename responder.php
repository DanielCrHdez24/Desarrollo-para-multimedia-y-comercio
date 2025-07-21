<?php
include("conexion.php");

$idcliente = $_POST["idcliente"];
$mensaje = $_POST["mensaje"];
$tipo = 2; // Admin

$query = "INSERT INTO chat (fecha, idcliente, tipo, mensaje) VALUES (NOW(), $idcliente, $tipo, '$mensaje')";
mysqli_query($conexion, $query);

header("Location: admin.php");
?>
