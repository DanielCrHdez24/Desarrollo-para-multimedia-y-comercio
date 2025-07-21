<?php
session_start();
$page = isset($_GET['page']) ? $_GET['page'] : 'inicio';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>El Clan del Juego</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
    <!-- Cabecera con navegación -->
    <header>
        <nav>
            <ul>
                <li><a href="index.php?page=inicio">Inicio</a></li>
                <li><a href="index.php?page=catalogo-venta">Nuestra Merch</a></li>

                <?php if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true): ?>
                    <li><a href="admin.php">Panel Admin</a></li>
                    <li><a href="logout_admin.php">Cerrar Sesión</a></li>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <li><a href="carrito.php">Carrito</a></li>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="index.php?page=inicio-sesion">Iniciar Sesión</a></li>
                    <li><a href="index.php?page=registro">Registrarse</a></li>
                <?php endif; ?>

                <li><a href="index.php?page=contacto">Contacto</a></li>
                <li><a href="login_admin.php">Administrador</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        switch ($page) {
            case 'inicio':
                include 'inicio.html';
                break;
            case 'catalogo-venta':
                include 'producto.php';
                break;
            case 'contacto':
                include 'contacto.php';
                break;
            case 'inicio-sesion':
                include 'login.php';
                break;
            case 'registro':
                include 'registro.php';
                break;
            default:
                include 'inicio.html';
                break;
        }
        ?>
    </main>

    <footer>
        <p>&copy; 2025 El Clan del Juego. Todos los derechos reservados.</p>
        <p>Síguenos en:
            <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer">Facebook</a> |
            <a href="https://x.com" target="_blank" rel="noopener noreferrer">Twitter/X</a> |
            <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer">Instagram</a>
        </p>
    </footer>
</body>

</html>
