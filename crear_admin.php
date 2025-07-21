<?php
include("conexion.php");

$nombre = "Admin";
$correo = "admin@clandeljuego.com";
$contrasena = password_hash("admin123", PASSWORD_DEFAULT);

$sql = "INSERT INTO administrador (nombre, correo, contrasena) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sss", $nombre, $correo, $contrasena);

if ($stmt->execute()) {
    echo "Administrador creado.";
} else {
    echo "Error: " . $stmt->error;
}
?>
