<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["user_id"])) {  // verifica la variable correcta
    exit("No hay sesión activa");
}

$idcliente = $_SESSION["user_id"];


$idcliente = intval($idcliente);

$query = "SELECT * FROM chat WHERE idcliente = $idcliente ORDER BY fecha ASC";
$result = mysqli_query($conexion, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $tipo = $row['tipo'];
    $mensaje = htmlspecialchars($row['mensaje']);
    $fecha = $row['fecha'];

    if ($tipo == 1) {
        echo "<p><strong>Tú:</strong> $mensaje <br><small>$fecha</small></p>";
    } else {
        echo "<p><strong>Admin:</strong> $mensaje <br><small>$fecha</small></p>";
    }
}
?>
