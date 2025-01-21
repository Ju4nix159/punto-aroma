<?php
include './config/sbd.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_pedido = $data['id_pedido'];
$productos = $data['productos'];
$nuevo_estado = $data['nuevo_estado'];
$costo_envio = $data['envio'];

try {
    $con->beginTransaction();

    // Actualizar el estado de cada producto en el pedido
    $sqlProducto = $con->prepare("
        UPDATE productos_pedido 
        SET estado = :estado 
        WHERE id_pedido = :id_pedido AND sku = :sku;
    ");
    foreach ($productos as $producto) {
        $sqlProducto->bindParam(':estado', $producto['estado'], PDO::PARAM_INT);
        $sqlProducto->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $sqlProducto->bindParam(':sku', $producto['sku'], PDO::PARAM_STR);
        $sqlProducto->execute();
    }

    // Actualizar el estado del pedido
    $sqlPedido = $con->prepare("
        UPDATE pedidos 
        SET id_estado_pedido = (
            SELECT id_estado_pedido 
            FROM estados_pedidos 
            WHERE nombre = :nuevo_estado
        ),
        envio = :costo_envio
        WHERE id_pedido = :id_pedido;
    ");
    $sqlPedido->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_STR);
    $sqlPedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $sqlPedido->bindParam(':costo_envio', $costo_envio, PDO::PARAM_INT);
    $sqlPedido->execute();

    $con->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
