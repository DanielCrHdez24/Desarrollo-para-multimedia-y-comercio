<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "tiendavirtual");
$conexion->set_charset("utf8");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// 1. Validar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Si no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: login.php?redirect_to=" . urlencode($_SERVER['HTTP_REFERER'])); // O a producto.php
    exit();
}

$cliente_id = $_SESSION['user_id'];
$producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;

if ($producto_id > 0) {
    // 2. Validar que el registro del producto seleccionado no exista,
    // en caso de que exista, incrementar la cantidad en una unidad.
    $stmt = $conexion->prepare("SELECT cantidad FROM carrito WHERE cliente_id = ? AND producto_id = ?");
    $stmt->bind_param("ii", $cliente_id, $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El producto ya existe en el carrito, incrementar la cantidad
        $row = $result->fetch_assoc();
        $new_quantity = $row['cantidad'] + 1;
        $update_stmt = $conexion->prepare("UPDATE carrito SET cantidad = ? WHERE cliente_id = ? AND producto_id = ?");
        $update_stmt->bind_param("iii", $new_quantity, $cliente_id, $producto_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // El producto no existe en el carrito, insertarlo
        $insert_stmt = $conexion->prepare("INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (?, ?, 1)");
        $insert_stmt->bind_param("ii", $cliente_id, $producto_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    $stmt->close();
    // Redirigir de vuelta a la página del catálogo (o al carrito si lo deseas)
    header("Location: producto.php?status=agregado");
    exit();
} else {
    // ID de producto inválido
    header("Location: producto.php?status=error&message=ID de producto inválido");
    exit();
}

$conexion->close();
?>