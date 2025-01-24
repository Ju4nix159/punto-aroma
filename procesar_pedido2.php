<?php
include './admin/config/sbd.php';
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['cart'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Sesión no válida o carrito vacío.']);
    exit;
}

$id_usuario = $_SESSION['usuario'];
$cart = $_SESSION['cart'];
$id_domicilio = $_POST['id_domicilio'];

if (!$id_domicilio) {
    http_response_code(400);
    echo json_encode(['error' => 'Debe seleccionar un domicilio.']);
    exit;
}

try {
    $con->beginTransaction();

    // Insertar en la tabla "pedidos"
    $total = array_reduce($cart, function ($carry, $item) {
        foreach ($item['fragancias'] as $fragancia) {
            $carry += $item['precio'] * $fragancia['cantidad'];
        }
        return $carry;
    }, 0);

    $sql_pedido = $con->prepare("
        INSERT INTO pedidos (id_usuario, id_estado_pedido, total, fecha, id_domicilio)
        VALUES (:id_usuario, 1, :total, NOW(), :id_domicilio)
    ");
    $sql_pedido->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $sql_pedido->bindParam(':total', $total, PDO::PARAM_STR);
    $sql_pedido->bindParam(':id_domicilio', $id_domicilio, PDO::PARAM_INT);
    $sql_pedido->execute();
    $id_pedido = $con->lastInsertId();

    // Insertar en la tabla "productos_pedidos"
    $sql_producto_pedido = $con->prepare("
        INSERT INTO productos_pedido (id_pedido, id_producto, sku, cantidad, precio)
        VALUES (:id_pedido, :id_producto, :sku, :cantidad, :precio)
    ");

    foreach ($cart as $item) {
        foreach ($item['fragancias'] as $fragancia) {
            $sql_producto_pedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $sql_producto_pedido->bindParam(':id_producto', $item['id'], PDO::PARAM_INT);
            $sql_producto_pedido->bindParam(':sku', $fragancia['sku'], PDO::PARAM_STR);
            $sql_producto_pedido->bindParam(':cantidad', $fragancia['cantidad'], PDO::PARAM_INT);
            $sql_producto_pedido->bindParam(':precio', $item['precio'], PDO::PARAM_STR);
            $sql_producto_pedido->execute();
        }
    }

    $con->commit();
    unset($_SESSION['cart']);
    echo json_encode(['success' => true, 'id_pedido' => $id_pedido]);
} catch (Exception $e) {
    $con->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Error al procesar el pedido: ' . $e->getMessage()]);
}
