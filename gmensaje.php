<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["user_id"])) {
    exit("No hay sesión activa");
}

$idcliente = $_SESSION["user_id"];
$mensaje = $_POST["mensaje"];
$tipo = 1; // Cliente

// Para evitar inyección, usa prepared statement
$stmt = $conexion->prepare("INSERT INTO chat (fecha, idcliente, tipo, mensaje) VALUES (NOW(), ?, ?, ?)");
$stmt->bind_param("iis", $idcliente, $tipo, $mensaje);
$stmt->execute();
$stmt->close();
?>
