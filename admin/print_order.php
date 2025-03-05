<?php
include './config/sbd.php';

// Get order ID from URL
$id_pedido = $_GET['id_pedido'];

// Fetch order details, user information, and address
$sql = $con->prepare("
    SELECT 
        p.id_pedido,
        p.total,
        p.fecha,
        p.envio,
        p.id_domicilio,
        p.id_local,
        P.estado_seña,
        u.email,
        -- Delivery address information
        d.calle,
        d.numero,
        d.codigo_postal,
        d.provincia,
        d.localidad,
        d.barrio,
        d.informacion_adicional,
        d.piso,
        d.departamento,
        -- Local pickup information
        l.nombre AS local_nombre,
        -- User information
        iu.nombre,
        iu.apellido,
        iu.dni,
        iu.fecha_nacimiento,
        iu.telefono
    FROM pedidos p
    JOIN usuarios u ON p.id_usuario = u.id_usuario
    JOIN info_usuarios iu ON p.id_usuario = iu.id_usuario 
    LEFT JOIN domicilios d ON p.id_domicilio = d.id_domicilio
    LEFT JOIN locales l ON p.id_local = l.id_local
    WHERE p.id_pedido = :id_pedido;
");

$sql->bindParam(':id_pedido', $id_pedido);
$sql->execute();
$order = $sql->fetch(PDO::FETCH_ASSOC);

// Fetch order items
$sql_items = $con->prepare("
    SELECT v.aroma, pp.id_pedido, pp.sku, p.nombre AS producto_nombre, pp.cantidad, pp.precio, pp.estado
    FROM productos_pedido pp
        JOIN productos p ON pp.id_producto = p.id_producto
        JOIN variantes v ON pp.sku = v.sku
    WHERE pp.id_pedido = :id_pedido;
");

$sql_items->bindParam(':id_pedido', $id_pedido);
$sql_items->execute();
$items = $sql_items->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden #<?php echo $id_pedido ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }

        .info-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            gap: 40px;
        }

        .customer-info,
        .address-info {
            flex: 1;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .info-box {
            margin-bottom: 20px;
        }

        .info-box h2 {
            color: #333;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .info-box p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .shipping-row {
            background-color: #f5f5f5;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
            font-size: 1.2em;
            padding: 10px;
            background-color: #f2f2f2;
            border-radius: 5px;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }

            .customer-info,
            .address-info {
                background-color: transparent;
                padding: 0;
            }

            .info-box {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Orden de Pedido #<?php echo $id_pedido ?></h1>
        <p>Fecha: <?php echo $order['fecha'] ?></p>
    </div>

    <div class="info-container">
        <div class="customer-info info-box">
            <h2>Información del Cliente</h2>
            <p><strong>Nombre:</strong> <?php echo $order['nombre'] . ' ' . $order['apellido'] ?></p>
            <p><strong>Email:</strong> <?php echo $order['email'] ?></p>
            <p><strong>Teléfono:</strong> <?php echo $order['telefono'] ?></p>
            <p><strong>DNI:</strong> <?php echo $order['dni'] ?></p>
        </div>

        <div class="address-info info-box">
            <h2><?php echo $order['id_domicilio'] ? 'Dirección de Entrega' : 'Retiro en Local' ?></h2>

            <?php if ($order['id_domicilio']): ?>
                <!-- Delivery Address -->
                <p><strong>Calle:</strong> <?php echo $order['calle'] . ' ' . $order['numero'] ?></p>
                <p><strong>Provincia:</strong> <?php echo $order['provincia'] ?></p>
                <p><strong>Localidad:</strong> <?php echo $order['localidad'] ?></p>
                <p><strong>Barrio:</strong> <?php echo $order['barrio'] ?></p>
                <p><strong>CP:</strong> <?php echo $order['codigo_postal'] ?></p>
                <?php if (!empty($order['piso'])): ?>
                    <p><strong>Piso:</strong> <?php echo $order['piso'] ?></p>
                <?php endif; ?>
                <?php if (!empty($order['departamento'])): ?>
                    <p><strong>Departamento:</strong> <?php echo $order['departamento'] ?></p>
                <?php endif; ?>
            <?php else: ?>
                <!-- Local Pickup -->
                <p><strong>Local:</strong> <?php echo $order['local_nombre'] ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="order-details">
        <h2>Detalles del Pedido</h2>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Aroma</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['producto_nombre'] ?></td>
                        <td><?php echo $item['aroma'] ?></td>
                        <td><?php echo $item['cantidad'] ?></td>
                        <td>$<?php echo number_format($item['precio'], 2) ?></td>
                        <td>$<?php echo number_format($item['cantidad'] * $item['precio'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="shipping-row">
                    <td colspan="4" style="text-align: right;"><strong>Costo de Envío</strong></td>
                    <td>$<?php echo number_format($order['envio'] ?? 0, 2) ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;"><strong>Total Final</strong></td>
                    <td>$<?php echo number_format($order['total'] + ($order['envio'] ?? 0), 2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>