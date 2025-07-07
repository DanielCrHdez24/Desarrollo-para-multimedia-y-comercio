<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container mt-5 text-center">
        <?php if (isset($_SESSION['order_message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['order_message_type']); ?> mb-4" role="alert">
                <h3><?php echo htmlspecialchars($_SESSION['order_message']); ?></h3>
            </div>
            <?php
            // Limpiar el mensaje de la sesión después de mostrarlo
            unset($_SESSION['order_message']);
            unset($_SESSION['order_message_type']);
            ?>
        <?php else: ?>
            <div class="alert alert-info mb-4" role="alert">
                No hay información de pedido reciente para mostrar.
            </div>
        <?php endif; ?>

        <a href="producto.php" class="btn btn-primary btn-lg">Volver al Catálogo</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>