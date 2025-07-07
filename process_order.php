<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "tiendavirtual");
$conexion->set_charset("utf8");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Redirigir si el usuario no ha iniciado sesión o no hay datos POST
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$cliente_id = $_SESSION['user_id'];

// Obtener los datos de entrega del formulario POST
$domicilio = $_POST['domicilio'] ?? '';
$cp = $_POST['cp'] ?? '';
$estado = $_POST['estado'] ?? '';
$municipio = $_POST['municipio'] ?? '';
$nombre_recibe = $_POST['nombre_recibe'] ?? '';
$telefono_contacto = $_POST['telefono_contacto'] ?? '';
$total_pedido = $_POST['total_pedido'] ?? 0;

// Validación básica de los datos de entrada
if (empty($domicilio) || empty($cp) || empty($estado) || empty($municipio) || empty($nombre_recibe) || empty($telefono_contacto) || $total_pedido <= 0) {
    // Manejar el error, por ejemplo, redirigir al carrito con un mensaje de error
    $_SESSION['order_message'] = "Por favor, complete todos los datos de entrega y asegúrese de que el total sea válido.";
    $_SESSION['order_message_type'] = "danger";
    header("Location: carrito.php");
    exit();
}

// Iniciar una transacción para asegurar la integridad de los datos
$conexion->begin_transaction();

try {
    // 6. Insertar en la tabla 'pedido' (Maestro de Pedidos)
    $stmt_pedido = $conexion->prepare(
        "INSERT INTO pedido (cliente_id, fecha_pedido, domicilio, codigo_postal, estado, municipio, nombre_recibe, telefono_contacto, total)
        VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt_pedido->bind_param(
        "issssssd",
        $cliente_id,
        $domicilio,
        $cp,
        $estado,
        $municipio,
        $nombre_recibe,
        $telefono_contacto,
        $total_pedido
    );
    $stmt_pedido->execute();
    $pedido_id = $conexion->insert_id; // Obtener el ID del pedido recién creado
    $stmt_pedido->close();

    // Obtener los ítems del 'carrito' para el usuario actual
    $stmt_carrito_items = $conexion->prepare(
        "SELECT producto_id, cantidad FROM carrito WHERE cliente_id = ?"
    );
    $stmt_carrito_items->bind_param("i", $cliente_id);
    $stmt_carrito_items->execute();
    $result_carrito_items = $stmt_carrito_items->get_result();

    $productos_en_carrito = [];
    while ($row = $result_carrito_items->fetch_assoc()) {
        $productos_en_carrito[] = $row;
    }
    $stmt_carrito_items->close();

    // Insertar cada ítem del carrito en 'detallepedido' (Detalle de Pedidos)
    $stmt_detalle = $conexion->prepare(
        "INSERT INTO detallepedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
        VALUES (?, ?, ?, ?, ?)"
    );

    foreach ($productos_en_carrito as $item) {
        // Obtener el precio actual del producto al momento del pedido
        $stmt_product_price = $conexion->prepare("SELECT precio FROM productos WHERE id = ?");
        $stmt_product_price->bind_param("i", $item['producto_id']);
        $stmt_product_price->execute();
        $result_product_price = $stmt_product_price->get_result();
        $product_data = $result_product_price->fetch_assoc();
        $precio_unitario = $product_data['precio'];
        $stmt_product_price->close();

        $subtotal_item = $item['cantidad'] * $precio_unitario;

        $stmt_detalle->bind_param(
            "iiidd",
            $pedido_id,
            $item['producto_id'],
            $item['cantidad'],
            $precio_unitario,
            $subtotal_item
        );
        $stmt_detalle->execute();
    }
    $stmt_detalle->close();

    // Limpiar la tabla 'carrito' para el usuario actual
    $stmt_clear_cart = $conexion->prepare("DELETE FROM carrito WHERE cliente_id = ?");
    $stmt_clear_cart->bind_param("i", $cliente_id);
    $stmt_clear_cart->execute();
    $stmt_clear_cart->close();

    // Confirmar la transacción
    $conexion->commit();

    // Pedido procesado exitosamente
    $_SESSION['order_message'] = "¡Tu pedido #" . $pedido_id . " ha sido procesado exitosamente! Recibirás una confirmación por correo. ¡Gracias por tu compra!";
    $_SESSION['order_message_type'] = "success";
    header("Location: order_confirmation.php"); // Redirigir a una página de confirmación
    exit();

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conexion->rollback();
    error_log("Error al procesar el pedido: " . $e->getMessage());
    $_SESSION['order_message'] = "Hubo un error al procesar tu pedido. Por favor, inténtalo de nuevo. " . $e->getMessage();
    $_SESSION['order_message_type'] = "danger";
    header("Location: carrito.php"); // Redirigir de nuevo al carrito con error
    exit();
} finally {
    $conexion->close();
}
?>