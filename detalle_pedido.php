<?php
include './admin/config/sbd.php';


// Obtiene el ID del pedido de la solicitud GET
$idPedido = $_GET['id_pedido'];
$sql_pedido = $con->prepare("SELECT pp.id_pedido, pp.id_producto, p.nombre AS producto_nombre, pp.sku, pp.cantidad, pp.precio
        FROM productos_pedido pp
        JOIN productos p ON pp.id_producto = p.id_producto
        WHERE pp.id_pedido = :id_pedido");
$sql_pedido->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
$sql_pedido->execute();
$detalles = $sql_pedido->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($detalles);
?>