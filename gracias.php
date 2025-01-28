<?php 
    include './header.php';
    include './admin/config/sbd.php';

    $id_pedido = isset($_GET['id_pedido']) ? $_GET['id_pedido'] : null;
    $sql_pedido = $con->prepare("SELECT p.total, ep.nombre AS estado_nombre, ep.descripcion AS estado_descripcion
FROM pedidos p
JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido
WHERE p.id_pedido = :id_pedido;");
    $sql_pedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $sql_pedido->execute();
    $pedido = $sql_pedido->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gracias por tu compra - Punto Aroma</title>
    <style>
        .text-primary-custom {
            color: var(--primary-color);
        }
        .text-secondary-custom {
            color: var(--secondary-color);
        }
        .thank-you-icon {
            font-size: 5rem;
            color: var(--primary-color);
        }
        main {
            flex: 1 0 auto;
        }
        footer {
            margin-top: auto;
        }
    </style>
</head>
<body>
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <i class="bi bi-check-circle thank-you-icon mb-4"></i>
                <h1 class="mb-4">¡Gracias por tu compra!</h1>
                <p class="lead mb-4">Tu pedido ha sido procesado exitosamente. Hemos enviado un correo electrónico con los detalles de tu pedido.</p>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Resumen de tu pedido</h5>
                        <p class="card-text">Número de pedido: <strong><?php echo $id_pedido?></strong></p>
                        <p class="card-text">Total: <strong><?php echo $pedido["total"]?></strong></p>
                        <p class="card-text">Fecha estimada de confirmacion del pedido: <strong>24 a 48 horas</strong></p>
                        <p class="card-text">Estado del pedido: <strong><?php echo $pedido["estado_nombre"]?></strong></p>
                        <p class="card-text">Descripción: <strong><?php echo $pedido["estado_descripcion"]?></strong></p>
                    </div>
                </div>
                <a href="panelUsuario.php" class="btn btn-primary-custom btn-lg">Ver estado del pedido</a>
                <p class="mt-4">
                    <a href="catalogo.php" class="text-secondary-custom">Volver al catalogo</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p>&copy; 2023 Punto Aroma. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-secondary-custom me-3">Política de privacidad</a>
                    <a href="#" class="text-secondary-custom">Términos y condiciones</a>
                </div>
            </div>
        </div>
    </footer>

</body>