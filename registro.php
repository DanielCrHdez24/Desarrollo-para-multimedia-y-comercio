<?php
$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conexion = new mysqli("localhost", "root", "", "tiendavirtual");
    $conexion->set_charset("utf8");

    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $email = $conexion->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    if ($password !== $confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
        $tipoMensaje = "danger";
    } else {
        $existe = $conexion->query("SELECT id FROM clientes WHERE email = '$email'");
        if ($existe && $existe->num_rows > 0) {
            $mensaje = "Este email ya está registrado.";
            $tipoMensaje = "warning";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insertar = $conexion->query("INSERT INTO clientes (nombre, email, password) VALUES ('$nombre', '$email', '$hash')");
            if ($insertar) {
                $mensaje = "Registro exitoso. Ya puedes iniciar sesión.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "Error al registrar.";
                $tipoMensaje = "danger";
            }
        }
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Registro de cliente</h3>

                    <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-<?= htmlspecialchars($tipoMensaje) ?> text-center">
                            <?= htmlspecialchars($mensaje) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirmar" class="form-label">Confirmar contraseña</label>
                            <input type="password" name="confirmar" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php">¿Ya tienes cuenta? Inicia sesión aquí</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
