<?php
// Inicia la sesión
session_start();

// Verifica qué página se está solicitando
$page = isset($_GET['page']) ? $_GET['page'] : 'inicio';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Clan del Juego</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <!-- Cabecera con navegación -->
    <header>
        <nav>
            <ul>
                <li><a href="index.php?page=inicio">Inicio</a></li>
                <li><a href="index.php?page=catalogo-venta">Nuestra Merch</a></li>
                <li><a href="index.php?page=contacto">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        // Verificar si el usuario está logueado
        if (isset($_SESSION['usuario_nombre'])) {
            echo "¡Bienvenido @" . $_SESSION['usuario_nombre'] . "!";
        } else {
            echo "¡Bienvenido visitante! Inicia sesión o regístrate.";
        }

        // Cargar el contenido de la página solicitada
        switch ($page) {
            case 'inicio':
                include 'inicio.html';
                break;
            case 'catalogo-venta':
                include 'producto.php';
                break;
            case 'contacto':
                include 'contacto.html';
                break;
            default:
                include 'inicio.html'; // Página predeterminada si no se especifica ninguna
                break;
        }
        ?>
    </main>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2025 El Clan del Juego. Todos los derechos reservados.</p>
        <p>Síguenos en:
            <a href="https://www.facebook.com" target="_blank">Facebook</a> |
            <a href="https://x.com" target="_blank">Twitter/X</a> |
            <a href="https://www.instagram.com" target="_blank">Instagram</a>
        </p>
    </footer>

</body>

</html>