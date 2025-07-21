<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica si hay sesión activa
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Redirige si no ha iniciado sesión
    header("Location: login.php");
    exit;
}

// OPCIONAL: Verificación de rol (ajusta el valor según tu base de datos)
// Supongamos que el rol de administrador es 1
if (isset($solo_admin) && $solo_admin === true && $_SESSION["idRol"] != 1) {
    echo "<p>No tienes permiso para acceder a esta sección.</p>";
    exit;
}
?>
