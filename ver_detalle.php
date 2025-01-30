<?php
include './admin/config/sbd.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_pedido'])) {
    $id_pedido = intval($_POST['id_pedido']);

    // Consulta para el resumen del pedido
    $sql_resumen = $con->prepare("SELECT l.nombre AS sucursal , p.id_pedido, p.total, p.fecha, u.email, iu.nombre AS nombre_usuario, iu.apellido, iu.dni, iu.telefono, ep.nombre AS estado_pedido, ep.descripcion AS estado_pedido_descripcion, d.codigo_postal, d.provincia, d.localidad, d.calle, d.numero FROM pedidos p 
    LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
    LEFT JOIN info_usuarios iu ON u.id_usuario = iu.id_usuario 
    LEFT JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido 
    LEFT JOIN domicilios d ON p.id_domicilio = d.id_domicilio 
    LEFT JOIN locales l ON p.id_local = l.id_local
    WHERE p.id_pedido = :id_pedido;
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
        $detallePedidoHtml .= "<p><strong>Total pedido:</strong> $" . number_format($resumen['total'], 2) . "</p>";
        $detallePedidoHtml .= "<p><strong>Cliente:</strong> " . htmlspecialchars($resumen['nombre_usuario'] . ' ' . $resumen['apellido']) . "</p>";
        $detallePedidoHtml .= "<p><strong>Email:</strong> " . htmlspecialchars($resumen['email']) . "</p>";

        if (!empty($resumen['sucursal'])) {
            $detallePedidoHtml .= "<p><strong>Sucursal:</strong> " . htmlspecialchars($resumen['sucursal']) . "</p>";
        } else {
            $detallePedidoHtml .= "<p><strong>Dirección:</strong> " . htmlspecialchars($resumen['calle'] . ' ' . $resumen['numero'] . ', ' . $resumen['localidad'] . ', ' . $resumen['provincia']) . "</p>";
        }
        $detallePedidoHtml .= "<p><strong>Estado del Pedido:</strong> " . htmlspecialchars($resumen['estado_pedido'] . ' - ' . $resumen['estado_pedido_descripcion']) . "</p>";
    } else {
        $detallePedidoHtml .= "<p>No se encontró información del resumen del pedido.</p>";
    }

    // Consulta para los detalles de los productos
    $sql_detalle = $con->prepare("SELECT p.nombre AS producto, v.nombre_variante AS variante, pp.precio, pp.estado AS estado_producto, pp.cantidad
FROM productos_pedido pp
JOIN productos p ON pp.id_producto = p.id_producto
JOIN variantes v ON pp.sku = v.sku
JOIN estados_productos ep ON v.id_estado_producto = ep.id_estado_producto
WHERE pp.id_pedido = :id_pedido");
    $sql_detalle->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $sql_detalle->execute();
    $detalles = $sql_detalle->fetchAll(PDO::FETCH_ASSOC);

    // Construir el HTML de los detalles de los productos
    if (!empty($detalles)) {
        $detallePedidoHtml .= "<h4>Detalles de los Productos</h4>";
        $detallePedidoHtml .= "<table class='table'>";
        $detallePedidoHtml .= "<thead>
        <tr>
        <th>Producto</th>
        <th>Variante</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Estado</th>
        </tr>
        </thead>
        <tbody>";

        foreach ($detalles as $row) {
            // Si el estado es 0, agrega una clase para tachar la fila
            $class = ($row['estado_producto'] == 0) ? "class='text-decoration-line-through'" : "";

            $detallePedidoHtml .= "<tr $class>";
            $detallePedidoHtml .= "<td>" . $row['producto'] . "</td>";
            $detallePedidoHtml .= "<td>" . $row['variante'] . "</td>";
            $detallePedidoHtml .= "<td>" . intval($row['cantidad']) . "</td>";
            $detallePedidoHtml .= "<td>" . number_format($row['precio'], 2) . "</td>";
            $detallePedidoHtml .= "<td>" . $row['estado_producto']."</td>";
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
