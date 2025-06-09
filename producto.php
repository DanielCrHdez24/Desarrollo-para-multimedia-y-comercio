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

    <!-- Conexión -->
    <?php
    $conexion = new mysqli("localhost", "root", "", "tiendavirtual");
    $conexion->set_charset("utf8");
    ?>

    <!-- Menú de categorías -->
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

    <!-- Barra de búsqueda -->
    <div class="mb-4">
        <input type="text" id="busqueda" class="form-control w-50 mx-auto" placeholder="Buscar por nombre o descripción...">
    </div>

    <!-- Contenedor de productos -->
    <div class="catalogo row justify-content-center g-4">
        <?php
        $sql = "SELECT 
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
                </div>';
            }
        } else {
            echo '<p class="text-center">No se encontraron productos en el catálogo.</p>';
        }

        $conexion->close();
        ?>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

</body>
</html>
