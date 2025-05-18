<?php
// Verifica si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoge los datos del formulario
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Aquí puedes procesar los datos recibidos, como enviar un correo electrónico, guardar en una base de datos, etc.

    // Ejemplo básico de impresión de datos recibidos
    echo "<h2>Datos recibidos:</h2>";
    echo "<p>Correo electrónico: $email</p>";
    echo "<p>Número de teléfono: $telefono</p>";

    // No es recomendable imprimir la contraseña en producción
    // echo "<p>Contraseña: $password</p>";
}
?>
