<?php
include("conexion.php");

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$mensaje = $_POST['mensaje'];

$query = "INSERT INTO contacto (nombre, correo, mensaje, fecha) VALUES ('$nombre', '$correo', '$mensaje', NOW())";
if (mysqli_query($conexion, $query)) {
    echo "<script>alert('Tu mensaje ha sido guardado exitosamente'); window.location='contacto.php';</script>";
} else {
    echo "Error: " . mysqli_error($conexion);
}
?>
