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
        $detallePedidoHtml .= "<div class='row g-4 mb-4'>
            <div class='col-md-6'>
                <h6 class='info-label mb-3'>Información del Pedido</h6>
                <div class='row g-2'>
                    <div class='col-6'><span class='fw-medium'>ID Pedido:</span></div>
                    <div class='col-6'>" . htmlspecialchars($resumen['id_pedido']) . "</div>
                    <div class='col-6'><span class='fw-medium'>Fecha:</span></div>
                    <div class='col-6'>" . htmlspecialchars($resumen['fecha']) . "</div>
                    <div class='col-6'><span class='fw-medium'>Total:</span></div>
                    <div class='col-6'>$" . number_format($resumen['total'], 2) . "</div>
                </div>
            </div>
            <div class='col-md-6'>
                <h6 class='info-label mb-3'>Información del Cliente</h6>
                <div class='mb-2'><span class='fw-medium'>Cliente: </span>" . htmlspecialchars($resumen['nombre_usuario'] . ' ' . $resumen['apellido']) . "</div>
                <div class='mb-2'><span class='fw-medium'>Email: </span>" . htmlspecialchars($resumen['email']) . "</div>
                <div class='mb-2'><span class='fw-medium'>Dirección: </span>";
        if (!empty($resumen['sucursal'])) {
            $detallePedidoHtml .= htmlspecialchars($resumen['sucursal']);
        } else {
            $detallePedidoHtml .= htmlspecialchars($resumen['calle'] . ' ' . $resumen['numero'] . ', ' . $resumen['localidad'] . ', ' . $resumen['provincia']);
        }
        $detallePedidoHtml .= "</div>
            </div>
        </div>";

        // Consulta para obtener los pagos del pedido
        $sql_pagos = $con->prepare("SELECT * FROM pagos WHERE id_pedido = :id_pedido");
        $sql_pagos->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $sql_pagos->execute();
        $pagos = $sql_pagos->fetchAll(PDO::FETCH_ASSOC);

        // Calcular el total pagado y la seña
        $totalPagado = 0;
        $seniaPagada = 0;

        foreach ($pagos as $pago) {
            if ($pago['comprobante'] !== null) {
            $totalPagado += $pago['monto'];
            if (strpos(strtolower($pago['descripcion']), 'seña') !== false) {
                $seniaPagada += $pago['monto'];
            }
            }
        }

        // Calcular el saldo pendiente
        $saldoPendiente = $resumen['total'] - $totalPagado;

        // Mostrar la información de pago
        $detallePedidoHtml .= "<div class='payment-info mb-4'>
            <h6 class='info-label mb-3'>Información de Pago</h6>
            <div class='row g-2'>
                <div class='col-6 col-md-3'>
                    <span class='fw-medium'>Total:</span>
                </div>
                <div class='col-6 col-md-3'>
                    $" . number_format($resumen['total'], 2) . "
                </div>
                <div class='col-6 col-md-3'>
                    <span class='fw-medium'>Seña pagada:</span>
                </div>
                <div class='col-6 col-md-3'>
                    $" . number_format($seniaPagada, 2) . "
                </div>
                <div class='col-6 col-md-3'>
                    <span class='fw-medium'>Saldo pendiente:</span>
                </div>
                <div class='col-6 col-md-3'>
                    <span class='text-danger fw-bold'>$" . number_format($saldoPendiente, 2) . "</span>
                </div>
            </div>
        </div>";

        // Consulta para los detalles de los productos
        $sql_detalle = $con->prepare("SELECT p.nombre AS producto, pp.precio, pp.estado AS estado_producto, pp.cantidad, 
        COALESCE(v.aroma, v.color, v.titulo) AS nombre
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
            $detallePedidoHtml .= "<div class='mb-4'>
                <h6 class='info-label mb-3'>Detalles de los Productos</h6>
                <div class='table-responsive'>
                    <table class='table'>
                        <thead>
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
                $class = ($row['estado_producto'] == 0) ? "class='text-decoration-line-through'" : "";
                $detallePedidoHtml .= "<tr $class>
                    <td>" . $row['producto'] . "</td>
                    <td>" . $row['nombre'] . "</td>
                    <td>" . intval($row['cantidad']) . "</td>
                    <td>$" . number_format($row['precio'], 2) . "</td>
                    <td><span class='badge badge-disponible'>" . ($row['estado_producto'] == 1 ? 'Disponible' : 'No disponible') . "</span></td>
                </tr>";
            }
            $detallePedidoHtml .= "</tbody></table></div></div>";
        } else {
            $detallePedidoHtml .= "<p>No se encontraron detalles de los productos para este pedido.</p>";
        }

        // Mostrar el total final (saldo pendiente)
        $detallePedidoHtml .= "<div class='d-flex justify-content-end border-top pt-3'>
            <div class='text-end'>
                <span class='fw-medium me-3'>Total a pagar:</span>
                <span class='total-amount'>$" . number_format($saldoPendiente, 2) . "</span>
            </div>
        </div>";
    } else {
        $detallePedidoHtml .= "<p>No se encontró información del resumen del pedido.</p>";
    }

    echo $detallePedidoHtml; // Enviar respuesta
} else {
    echo "<p>Error: ID de pedido inválido.</p>";
}
