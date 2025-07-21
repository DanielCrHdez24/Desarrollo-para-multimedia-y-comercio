<?php
session_start();

// Verifica si el administrador ha iniciado sesión
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
</head>
<body>
    <h1>Bienvenido, Administrador</h1>
    <p><a href="logout_admin.php">Cerrar sesión</a></p>
    <!-- Aquí va el contenido exclusivo del administrador -->
</body>
</html>
