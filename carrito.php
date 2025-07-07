<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "tiendavirtual");
$conexion->set_charset("utf8");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Redirigir si el usuario no ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect_to=carrito.php");
    exit();
}

$cliente_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// Manejar la actualización de cantidades o la eliminación de ítems
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $producto_id = intval($_POST['producto_id']);
        $new_quantity = intval($_POST['cantidad']);

        if ($new_quantity > 0) {
            $stmt = $conexion->prepare("UPDATE carrito SET cantidad = ? WHERE cliente_id = ? AND producto_id = ?");
            $stmt->bind_param("iii", $new_quantity, $cliente_id, $producto_id);
            if ($stmt->execute()) {
                $message = "Cantidad actualizada correctamente.";
                $message_type = "success";
            } else {
                $message = "Error al actualizar la cantidad.";
                $message_type = "danger";
            }
            $stmt->close();
        } else if ($new_quantity == 0) {
            // Si la cantidad es 0, eliminar el ítem del carrito
            $stmt = $conexion->prepare("DELETE FROM carrito WHERE cliente_id = ? AND producto_id = ?");
            $stmt->bind_param("ii", $cliente_id, $producto_id);
            if ($stmt->execute()) {
                $message = "Producto eliminado del carrito.";
                $message_type = "success";
            } else {
                $message = "Error al eliminar el producto.";
                $message_type = "danger";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['remove_item'])) {
        $producto_id = intval($_POST['producto_id_remove']);
        $stmt = $conexion->prepare("DELETE FROM carrito WHERE cliente_id = ? AND producto_id = ?");
        $stmt->bind_param("ii", $cliente_id, $producto_id);
        if ($stmt->execute()) {
            $message = "Producto eliminado del carrito.";
            $message_type = "success";
        } else {
            $message = "Error al eliminar el producto.";
            $message_type = "danger";
        }
        $stmt->close();
    }
}

// Obtener los ítems del carrito para el usuario actual
$cart_items = [];
$total_cart_amount = 0;

$sql_cart = "SELECT
                c.id AS carrito_id,
                p.id AS producto_id,
                p.nombre,
                p.descripcion,
                p.precio,
                p.imagen_url,
                c.cantidad
            FROM carrito c
            INNER JOIN productos p ON c.producto_id = p.id
            WHERE c.cliente_id = ?";
$stmt_cart = $conexion->prepare($sql_cart);
$stmt_cart->bind_param("i", $cliente_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

while ($row = $result_cart->fetch_assoc()) {
    $row['subtotal'] = $row['cantidad'] * $row['precio'];
    $total_cart_amount += $row['subtotal'];
    $cart_items[] = $row;
}
$stmt_cart->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Tu Carrito de Compras</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> text-center" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info text-center" role="alert">
                Tu carrito está vacío. ¡Empieza a agregar productos!
            </div>
            <div class="text-center mt-3">
                <a href="producto.php" class="btn btn-primary">Volver al Catálogo</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            Productos en tu Carrito
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($cart_items as $item): ?>
                                    <li class="list-group-item d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($item['imagen_url']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" style="width: 80px; height: 80px; object-fit: cover; margin-right: 15px;">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($item['nombre']); ?></h5>
                                            <p class="mb-1 text-muted"><?php echo htmlspecialchars($item['descripcion']); ?></p>
                                            <p class="mb-1">Precio unitario: $<?php echo number_format($item['precio'], 2); ?></p>
                                        </div>
                                        <form action="carrito.php" method="post" class="d-flex align-items-center">
                                            <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($item['producto_id']); ?>">
                                            <label for="cantidad_<?php echo $item['producto_id']; ?>" class="me-2">Cantidad:</label>
                                            <input type="number" name="cantidad" id="cantidad_<?php echo $item['producto_id']; ?>" value="<?php echo htmlspecialchars($item['cantidad']); ?>" min="0" class="form-control form-control-sm me-2" style="width: 70px;">
                                            <button type="submit" name="update_quantity" class="btn btn-sm btn-info me-2">Actualizar</button>
                                        </form>
                                        <form action="carrito.php" method="post">
                                            <input type="hidden" name="producto_id_remove" value="<?php echo htmlspecialchars($item['producto_id']); ?>">
                                            <button type="submit" name="remove_item" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                        <div class="ms-3 text-end" style="width: 120px;">
                                            <strong>Subtotal: $<?php echo number_format($item['subtotal'], 2); ?></strong>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="card-footer text-end">
                                <h3>Total a Pagar: $<?php echo number_format($total_cart_amount, 2); ?></h3>
                            </div>
                        </div>
                    </div>

                    <h2 class="mt-5 mb-3 text-center">Datos de Entrega</h2>
                    <div class="card mb-5">
                        <div class="card-body">
                            <form action="process_order.php" method="post">
                                <input type="hidden" name="total_pedido" value="<?php echo $total_cart_amount; ?>">

                                <div class="mb-3">
                                    <label for="domicilio" class="form-label">Domicilio:</label>
                                    <input type="text" class="form-control" id="domicilio" name="domicilio" required>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-md-4">
                                        <label for="cp" class="form-label">Código Postal:</label>
                                        <input type="text" class="form-control" id="cp" name="cp" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="estado" class="form-label">Estado:</label>
                                        <input type="text" class="form-control" id="estado" name="estado" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="municipio" class="form-label">Municipio:</label>
                                        <input type="text" class="form-control" id="municipio" name="municipio" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="nombre_recibe" class="form-label">Nombre de quien recibe:</label>
                                    <input type="text" class="form-control" id="nombre_recibe" name="nombre_recibe" required>
                                </div>
                                <div class="mb-3">
                                    <label for="telefono_contacto" class="form-label">Teléfono de contacto:</label>
                                    <input type="tel" class="form-control" id="telefono_contacto" name="telefono_contacto" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100 btn-lg">Procesar Pago</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Resumen de Compra</div>
                        <div class="card-body">
                            <p>Total de productos: <?php echo count($cart_items); ?></p>
                            <h4>Monto a pagar: $<?php echo number_format($total_cart_amount, 2); ?></h4>
                            <p class="text-muted small">Al procesar el pago, se generará tu pedido.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conexion->close();
?>