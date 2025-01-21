<?php
include './config/sbd.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_pedido = $data['id_pedido'];
$cambios = $data['cambios'];
$costo_envio = $data['envio'];

$sql_costo_envio = $con->prepare("
    UPDATE pedidos 
    SET envio = :costo_envio
    WHERE id_pedido = :id_pedido;
");
$sql_costo_envio->bindParam(':costo_envio', $costo_envio, PDO::PARAM_INT);
$sql_costo_envio->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_costo_envio->execute();


foreach ($cambios as $detalle) {
    $sku = $detalle['sku'];
    $estado = $detalle['estado'];

    $sql = $con->prepare("
        UPDATE productos_pedido 
        SET estado = :estado 
        WHERE id_pedido = :id_pedido AND sku = :sku;
    ");
    $sql->bindParam(':estado', $estado, PDO::PARAM_INT);
    $sql->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $sql->bindParam(':sku', $sku, PDO::PARAM_STR);
    $sql->execute();
}

echo json_encode(['success' => true]);
