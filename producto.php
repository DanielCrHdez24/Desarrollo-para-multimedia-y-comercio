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

    <div class="catalogo row justify-content-center g-4">
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "tiendavirtual";

        $conexion = new mysqli($servername, $username, $password, $dbname);

        if ($conexion->connect_error) {
            die("Conexión fallida: " . $conexion->connect_error);
        }

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
                <div class="producto col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <img src="' . htmlspecialchars($row["imagen_url"]) . '" alt="' . htmlspecialchars($row["nombre"]) . '" class="img-fluid rounded" />
                    <h2>' . htmlspecialchars($row["nombre"]) . '</h2>
                    <p>' . htmlspecialchars($row["descripcion"]) . '</p>
                    <span class="precio">$' . number_format($row["precio"], 2) . '</span>
                    <span class="existencia">' . htmlspecialchars($row["existencia"]) . ' pzas</span>
                    <span class="categoria">' . htmlspecialchars($row["nombre_categoria"]) . '</span>
                </div>';
            }
        } else {
            echo '<p class="text-center">No se encontraron productos en el catálogo.</p>';
        }

        $conexion->close();
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
