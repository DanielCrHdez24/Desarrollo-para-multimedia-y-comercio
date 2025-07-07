<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexion = new mysqli("localhost", "root", "", "tiendavirtual");
    $conexion->set_charset("utf8");

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT id, nombre, password FROM clientes WHERE email = ?"; // Seleccionamos también el ID
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($password, $usuario['password'])) {
            $_SESSION["usuario"] = $usuario["nombre"];
            $_SESSION["user_id"] = $usuario["id"]; // Almacena el ID del usuario en la sesión
            header("Location: producto.php"); // Redirige al catálogo de productos
            exit();
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "Usuario no encontrado.";
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle" style="font-size: 3rem; color: #0d6efd;"></i>
                    <h2 class="my-3">Iniciar Sesión</h2>

                    <?php if ($mensaje): ?>
                        <div class="alert alert-danger"><?= $mensaje ?></div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <div class="mb-3 text-start">
                            <label for="email" class="form-label">Correo electrónico:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3 text-start">
                            <label for="password" class="form-label">Contraseña:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>

                    <div class="mt-3">
                        <a href="registro.php">¿No tienes cuenta? Regístrate</a><br>
                        <a href="recuperar.php" class="text-secondary">¿Olvidaste tu contraseña?</a>
                    </div>

                    <?php if (isset($_SESSION["usuario"])): ?>
                        <div class="mt-3">
                            <a href="logout.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>