<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contrasena = $_POST["contrasena"];

    $stmt = $conexion->prepare("SELECT id, contrasena FROM administrador WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashGuardado);
        $stmt->fetch();

        if (password_verify($contrasena, $hashGuardado)) {
            $_SESSION["admin_loggedin"] = true;
            $_SESSION["admin_id"] = $id;
            header("location: admin.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Correo no encontrado.";
    }
}
?>

<!-- Formulario simple -->
<form method="POST">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <label>Correo:</label><input type="email" name="correo" required><br>
    <label>Contraseña:</label><input type="password" name="contrasena" required><br>
    <button type="submit">Iniciar sesión</button>
</form>
<?php if (isset($error)) echo "<p>$error</p>"; ?>
