<?php
include './config/sbd.php';

$id_pedido = $_GET['id_pedido'];

$sql = $con->prepare("
    SELECT sku, estado 
    FROM productos_pedido 
    WHERE id_pedido = :id_pedido;
");
$sql->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql->execute();

$detalles = $sql->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'detalles' => $detalles]);
