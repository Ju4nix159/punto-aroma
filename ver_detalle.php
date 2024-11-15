<?php
include './admin/config/sbd.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_pedido'])) {
    $id_pedido = intval($_POST['id_pedido']);

    $sql_detalle = $con->prepare("SELECT id_producto, cantidad, precio FROM productos_pedido WHERE id_pedido = :id_pedido");
    $sql_detalle->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $sql_detalle->execute();
    $detalles = $sql_detalle->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($detalles)) {
        $detallePedidoHtml = "<table class='table'>";
        $detallePedidoHtml .= "<thead><tr><th>Ítem</th><th>Cantidad</th><th>Precio</th></tr></thead><tbody>";

        foreach ($detalles as $row) {
            $detallePedidoHtml .= "<tr>";
            $detallePedidoHtml .= "<td>" . htmlspecialchars($row['id_producto']) . "</td>";
            $detallePedidoHtml .= "<td>" . intval($row['cantidad']) . "</td>";
            $detallePedidoHtml .= "<td>" . number_format($row['precio'], 2) . "</td>";
            $detallePedidoHtml .= "</tr>";
        }

        $detallePedidoHtml .= "</tbody></table>";
    } else {
        $detallePedidoHtml = "<p>No se encontraron detalles para este pedido.</p>";
    }
    echo $detallePedidoHtml; // Respuesta
} else {
    echo "<p>Error: ID de pedido inválido.</p>";
}
?>
