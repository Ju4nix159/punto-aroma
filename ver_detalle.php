<?php
include './admin/config/sbd.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_pedido'])) {
    $id_pedido = intval($_POST['id_pedido']);

    // Consulta para el resumen del pedido
    $sql_resumen = $con->prepare("SELECT p.id_pedido, p.total, p.fecha, u.email, iu.nombre AS nombre_usuario, iu.apellido, iu.dni, iu.telefono, ep.nombre AS estado_pedido, ep.descripcion AS estado_pedido_descripcion, d.codigo_postal, d.provincia, d.localidad, d.calle, d.numero, fp.tipo AS forma_pago FROM pedidos p LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario LEFT JOIN info_usuarios iu ON u.id_usuario = iu.id_usuario LEFT JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido LEFT JOIN domicilios d ON p.id_domicilio = d.id_domicilio LEFT JOIN metodos_pago fp ON p.id_metodo_pago = fp.id_metodo_pago WHERE p.id_pedido = :id_pedido;
    ");
    $sql_resumen->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $sql_resumen->execute();
    $resumen = $sql_resumen->fetch(PDO::FETCH_ASSOC);

    // Construir el HTML del resumen del pedido
    $detallePedidoHtml = "";
    if ($resumen) {
        $detallePedidoHtml .= "<h4>Resumen del Pedido</h4>";
        $detallePedidoHtml .= "<p><strong>ID Pedido:</strong> " . htmlspecialchars($resumen['id_pedido']) . "</p>";
        $detallePedidoHtml .= "<p><strong>Fecha:</strong> " . htmlspecialchars($resumen['fecha']) . "</p>";
        $detallePedidoHtml .= "<p><strong>Total:</strong> $" . number_format($resumen['total'], 2) . "</p>";
        $detallePedidoHtml .= "<p><strong>Cliente:</strong> " . htmlspecialchars($resumen['nombre_usuario'] . ' ' . $resumen['apellido']) . "</p>";
        $detallePedidoHtml .= "<p><strong>Email:</strong> " . htmlspecialchars($resumen['email']) . "</p>";
        $detallePedidoHtml .= "<p><strong>Dirección:</strong> " . htmlspecialchars($resumen['calle'] . ' ' . $resumen['numero'] . ', ' . $resumen['localidad'] . ', ' . $resumen['provincia']) . "</p>";
        $detallePedidoHtml .= "<p><strong>Forma de Pago:</strong> " . htmlspecialchars($resumen['forma_pago']) . "</p>";
        $detallePedidoHtml .= "<p><strong>Estado del Pedido:</strong> " . htmlspecialchars($resumen['estado_pedido'] . ' - ' . $resumen['estado_pedido_descripcion']) . "</p>";
    } else {
        $detallePedidoHtml .= "<p>No se encontró información del resumen del pedido.</p>";
    }

    // Consulta para los detalles de los productos
    $sql_detalle = $con->prepare("SELECT id_producto, cantidad, precio, estado FROM productos_pedido WHERE id_pedido = :id_pedido");
    $sql_detalle->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $sql_detalle->execute();
    $detalles = $sql_detalle->fetchAll(PDO::FETCH_ASSOC);

    // Construir el HTML de los detalles de los productos
    if (!empty($detalles)) {
        $detallePedidoHtml .= "<h4>Detalles de los Productos</h4>";
        $detallePedidoHtml .= "<table class='table'>";
        $detallePedidoHtml .= "<thead><tr><th>Ítem</th><th>Cantidad</th><th>Precio</th><th>Estado</th></tr></thead><tbody>";

        foreach ($detalles as $row) {
            // Si el estado es 0, agrega una clase para tachar la fila
            $class = ($row['estado'] == 0) ? "class='text-decoration-line-through'" : "";

            $detallePedidoHtml .= "<tr $class>";
            $detallePedidoHtml .= "<td>" . htmlspecialchars($row['id_producto']) . "</td>";
            $detallePedidoHtml .= "<td>" . intval($row['cantidad']) . "</td>";
            $detallePedidoHtml .= "<td>" . number_format($row['precio'], 2) . "</td>";
            $detallePedidoHtml .= "<td>" . (($row['estado'] == 1) ? "Disponible" : "Eliminado") . "</td>";
            $detallePedidoHtml .= "</tr>";
        }

        $detallePedidoHtml .= "</tbody></table>";
    } else {
        $detallePedidoHtml .= "<p>No se encontraron detalles de los productos para este pedido.</p>";
    }

    echo $detallePedidoHtml; // Enviar respuesta
} else {
    echo "<p>Error: ID de pedido inválido.</p>";
}
