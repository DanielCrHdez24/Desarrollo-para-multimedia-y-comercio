<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Contacto - El Clan del Juego</title>
</head>
<body>
<h2>Contáctanos</h2>
<form action="guardar_contacto.php" method="POST">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>
    <label>Correo:</label><br>
    <input type="email" name="correo" required><br><br>
    <label>Mensaje:</label><br>
    <textarea name="mensaje" rows="5" required></textarea><br><br>
    <input type="submit" value="Enviar">
</form>
</body>
</html>
