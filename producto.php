<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexión principal
$conexion = new mysqli("localhost", "root", "", "tiendavirtual");
$conexion->set_charset("utf8");
if ($conexion->connect_error) {
    die("La conexión a la base de datos falló: " . $conexion->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
<div class="container-fluid text-center">
    <h1 class="my-4">¡Llévele, barato!</h1>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="producto.php">Mi Tienda</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="contacto.html">Contacto</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">
                            Carrito
                            <?php
                            $cart_item_count = 0;
                            $conexion_cart_count = new mysqli("localhost", "root", "", "tiendavirtual");
                            $conexion_cart_count->set_charset("utf8");
                            if (!$conexion_cart_count->connect_error && isset($_SESSION['user_id'])) {
                                $user_id = $_SESSION['user_id'];
                                $stmt_count = $conexion_cart_count->prepare("SELECT SUM(cantidad) AS total_items FROM carrito WHERE cliente_id = ?");
                                if ($stmt_count) {
                                    $stmt_count->bind_param("i", $user_id);
                                    $stmt_count->execute();
                                    $result_count = $stmt_count->get_result();
                                    $row_count = $result_count->fetch_assoc();
                                    $cart_item_count = $row_count['total_items'] ?? 0;
                                    $stmt_count->close();
                                }
                            }
                            $conexion_cart_count->close();
                            ?>
                            <span class="badge bg-primary rounded-pill"><?php echo $cart_item_count; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                        <?php else: ?>
                            <a class="nav-link" href="login.php">Iniciar Sesión</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="alert alert-info text-center mt-3" role="alert">
            Por favor, <strong>inicie sesión</strong> para agregar productos al carrito y realizar su compra.
        </div>
    <?php endif; ?>

    <!-- FILTRO POR CATEGORÍA -->
    <div class="mb-3">
        <select id="categoriaSelect" class="form-select w-50 mx-auto">
            <option value="todas">Todas las categorías</option>
            <?php
            $categorias = $conexion->query("SELECT id, nombre FROM categorias");
            while ($cat = $categorias->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($cat["nombre"]) . '">' . htmlspecialchars($cat["nombre"]) . '</option>';
            }
            ?>
        </select>
    </div>

    <!-- BÚSQUEDA -->
    <div class="mb-4">
        <input type="text" id="busqueda" class="form-control w-50 mx-auto" placeholder="Buscar por nombre o descripción...">
    </div>

    <!-- CATÁLOGO -->
    <div class="catalogo row justify-content-center g-4">
        <?php
        $sql = "SELECT
                    p.id,
                    p.nombre,
                    p.descripcion,
                    p.precio,
                    p.existencia,
                    p.imagen_url,
                    c.nombre AS nombre_categoria
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id";

        $resultado = $conexion->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo '
                <div class="producto col-12 col-sm-6 col-md-4 col-lg-3 mb-4" data-categoria="' . htmlspecialchars($row["nombre_categoria"]) . '">
                    <img src="' . htmlspecialchars($row["imagen_url"]) . '" alt="' . htmlspecialchars($row["nombre"]) . '" class="img-fluid rounded" />
                    <h2>' . htmlspecialchars($row["nombre"]) . '</h2>
                    <p>' . htmlspecialchars($row["descripcion"]) . '</p>
                    <span class="precio">$' . number_format($row["precio"], 2) . '</span>
                    <span class="existencia">' . htmlspecialchars($row["existencia"]) . ' pzas</span>
                    <span class="categoria badge bg-secondary">' . htmlspecialchars($row["nombre_categoria"]) . '</span>
                    <form action="add_to_cart.php" method="post" class="mt-2">
                        <input type="hidden" name="producto_id" value="' . htmlspecialchars($row["id"]) . '">
                        <button type="submit" class="btn btn-success">Agregar al Carrito</button>
                    </form>
                </div>';
            }
        } else {
            echo '<p class="text-center">No se encontraron productos en el catálogo.</p>';
        }

        $conexion->close();
        ?>
    </div>
</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS DE FILTRO -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputBusqueda = document.getElementById('busqueda');
    const selectCategoria = document.getElementById('categoriaSelect');
    const productos = document.querySelectorAll('.producto');

    function filtrarProductos() {
        const termino = inputBusqueda.value.toLowerCase();
        const categoriaSeleccionada = selectCategoria.value;

        productos.forEach(producto => {
            const nombre = producto.querySelector('h2').textContent.toLowerCase();
            const descripcion = producto.querySelector('p').textContent.toLowerCase();
            const categoria = producto.getAttribute('data-categoria');

            const coincideBusqueda = nombre.includes(termino) || descripcion.includes(termino);
            const coincideCategoria = categoriaSeleccionada === 'todas' || categoria === categoriaSeleccionada;

            producto.style.display = (coincideBusqueda && coincideCategoria) ? '' : 'none';
        });
    }

    inputBusqueda.addEventListener('input', filtrarProductos);
    selectCategoria.addEventListener('change', filtrarProductos);
});
</script>

<!-- CHAT (solo si está logueado) -->
<?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
    <div id="chatbox"></div>
    <form id="chatForm">
        <input type="text" id="usermsg" placeholder="Escribe tu mensaje..." required>
        <button type="submit">Enviar</button>
    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    $(function(){
        function cargarMensajes() {
            $("#chatbox").load("cargar_chat.php");
        }

        $("#chatForm").submit(function(e){
            e.preventDefault();
            $.post("gmensaje.php", {
                mensaje: $("#usermsg").val()
            }, function(){
                $("#usermsg").val("");
                cargarMensajes();
            });
        });

        setInterval(cargarMensajes, 2500);
        cargarMensajes();
    });
    </script>
<?php else: ?>
    <p class="text-center text-muted mt-4">Debes iniciar sesión para usar el chat.</p>
<?php endif; ?>

</body>
</html>
