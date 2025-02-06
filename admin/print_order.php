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
        u.email,
        u.nombre,
        u.apellido,
        u.telefono,
        d.calle,
        d.numero,
        d.colonia,
        d.ciudad,
        d.estado,
        d.codigo_postal
    FROM pedidos p
    JOIN usuarios u ON p.id_usuario = u.id_usuario
    JOIN domicilios d ON u.id_usuario = d.id_usuario
    WHERE p.id_pedido = :id_pedido
");

$sql->bindParam(':id_pedido', $id_pedido);
$sql->execute();
$order = $sql->fetch(PDO::FETCH_ASSOC);

// Fetch order items
$sql_items = $con->prepare("
    SELECT 
        dp.cantidad,
        dp.precio_unitario,
        dp.subtotal,
        p.nombre as producto_nombre
    FROM detalles_pedido dp
    JOIN productos p ON dp.id_producto = p.id_producto
    WHERE dp.id_pedido = :id_pedido
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
        }
        .order-info {
            margin-bottom: 20px;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orden de Pedido #<?php echo $id_pedido ?></h1>
        <p>Fecha: <?php echo $order['fecha'] ?></p>
    </div>

    <div class="customer-info">
        <h2>Información del Cliente</h2>
        <p>Nombre: <?php echo $order['nombre'] . ' ' . $order['apellido'] ?></p>
        <p>Email: <?php echo $order['email'] ?></p>
        <p>Teléfono: <?php echo $order['telefono'] ?></p>
    </div>

    <div class="address-info">
        <h2>Dirección de Entrega</h2>
        <p>
            <?php echo $order['calle'] . ' ' . $order['numero'] ?><br>
            <?php echo $order['colonia'] ?><br>
            <?php echo $order['ciudad'] . ', ' . $order['estado'] ?><br>
            CP: <?php echo $order['codigo_postal'] ?>
        </p>
    </div>

    <div class="order-details">
        <h2>Detalles del Pedido</h2>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo $item['producto_nombre'] ?></td>
                    <td><?php echo $item['cantidad'] ?></td>
                    <td>$<?php echo number_format($item['precio_unitario'], 2) ?></td>
                    <td>$<?php echo number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total">
            Total: $<?php echo number_format($order['total'], 2) ?>
        </div>
    </div>
</body>
</html>