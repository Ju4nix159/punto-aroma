<?php

include './header.php';
include './admin/config/sbd.php';

$id_pedido = $_GET['id_pedido'];

$sql_obetener_pedido = $con->prepare("SELECT 
            p.id_pedido, 
            ep.nombre AS estado_pedido, 
            pp.id_producto, 
            pr.nombre AS producto_nombre, 
            pp.sku, 
            pp.cantidad, 
            pp.precio
        FROM 
            pedidos p
            JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido
            JOIN productos_pedido pp ON p.id_pedido = pp.id_pedido
            JOIN productos pr ON pp.id_producto = pr.id_producto
        WHERE 
            p.id_pedido = :id_pedido;");
$sql_obetener_pedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_obetener_pedido->execute();
$pedido = $sql_obetener_pedido->fetchAll(PDO::FETCH_ASSOC);

$sq_detalle_pedido = $con->prepare(" SELECT 
                p.id_pedido, 
                p.total, 
                p.fecha, 
                u.id_usuario, 
                u.email, 
                iu.nombre AS nombre_usuario, 
                iu.apellido, 
                iu.dni, 
                iu.fecha_nacimiento, 
                iu.telefono, 
                ep.nombre AS estado_pedido, 
                ep.descripcion AS estado_pedido_descripcion, 
                d.codigo_postal, 
                d.provincia, 
                d.localidad, 
                d.barrio, 
                d.calle, 
                d.numero
            FROM 
                pedidos p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                JOIN info_usuarios iu ON u.id_usuario = iu.id_usuario
                JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido
                JOIN domicilios d ON p.id_domicilio = d.id_domicilio
            WHERE 
                p.id_pedido = :id_pedido;");
$sq_detalle_pedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sq_detalle_pedido->execute();
$detalle_pedido = $sq_detalle_pedido->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Punto Aroma</title>
    <style>
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(131, 175, 55, 0.25);
        }

        .card-header {
            background-color: var(--secondary-color);
            color: white;
        }

        .fragrance-toggle {
            cursor: pointer;
            user-select: none;
        }

        .fragrance-toggle:hover {
            text-decoration: underline;
        }

        .fragrance-list {
            margin-top: 0.5rem;
            padding-left: 1rem;
        }

        #info_container {
            transition: height 0.3s ease;
            /* Animación suave */
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Pagar pedido: <?php echo $id_pedido ?></h1>
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">resumen del pedido</h5>
                    </div>
                    <div class="card-body">
                        <h4>Resumen del Pedido</h4>
                        <input type="hidden" name="id_pedido" id="id_pedido" value="<?php echo $detalle_pedido["id_pedido"] ?>">
                        <p><strong>ID Pedido:</strong> <?php echo htmlspecialchars($detalle_pedido['id_pedido']); ?></p>
                        <p><strong>Fecha:</strong> <?php echo htmlspecialchars($detalle_pedido['fecha']); ?></p>
                        <p><strong>Total:</strong> $<?php echo number_format($detalle_pedido['total'], 2); ?></p>
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($detalle_pedido['nombre_usuario'] . ' ' . $detalle_pedido['apellido']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($detalle_pedido['email']); ?></p>
                        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($detalle_pedido['calle'] . ' ' . $detalle_pedido['numero'] . ', ' . $detalle_pedido['localidad'] . ', ' . $detalle_pedido['provincia']); ?></p>
                        <p><strong>Estado del Pedido:</strong> <?php echo htmlspecialchars($detalle_pedido['estado_pedido'] . ' - ' . $detalle_pedido['estado_pedido_descripcion']); ?></p>



                    </div>
                </div>

            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumen del pedido</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($pedido as $index => $item): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['producto_nombre']); ?></h6>
                                        <span class="fragrance-toggle" onclick="toggleFragrances(<?php echo $index; ?>)">
                                            Ver fragancias <i class="bi bi-chevron-down"></i>
                                        </span>
                                    </div>
                                    <ul id="checkout-fragancias-<?php echo $index; ?>" class="fragrance-list" style="display: none;">
                                        <li>
                                            SKU: <?php echo htmlspecialchars($item['sku']); ?>
                                        </li>
                                        <li>
                                            Cantidad: <?php echo htmlspecialchars($item['cantidad']); ?>
                                        </li>
                                        <li>
                                            Precio: $<?php echo number_format($item['precio'], 2); ?>
                                        </li>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <hr>
                        <!-- Resumen del total -->
                        <?php
                        $subtotal = array_reduce($pedido, function ($carry, $item) {
                            $carry += $item['precio'] * $item['cantidad'];
                            return $carry;
                        }, 0);
                        $envio = 5.00;
                        $total = $subtotal + $envio;
                        ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Envío</span>
                            <strong>$<?php echo number_format($envio, 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total</span>
                            <strong class="text-primary-custom">$<?php echo number_format($total, 2); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">forma de pago</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <label for="forma_pago">Forma de pago</label>
                            <select class="form-control" name="forma_pago" id="forma_pago">
                                <option value="transferencia">Transferencia</option>
                                <option value="mercadoPago">Mercado Pago</option>
                            </select>
                        </div>

                        <!-- Contenedor de contenido dinámico -->
                        <div id="info_container" style="position: relative; overflow: hidden; min-height: 150px;">
                            <div id="info_transferencia" class="p-4">
                                <h6>Información de Transferencia</h6>
                                <p><strong>Banco:</strong> Banco Ejemplo</p>
                                <p><strong>Cuenta:</strong> 1234567890</p>
                                <p><strong>CBU:</strong> 1234567890123456789012</p>
                                <p><strong>Alias:</strong> alias.ejemplo</p>

                                <div class="mb-3">
                                    <label for="comprobante_trasnferencia" class="form-label">comprobante</label>
                                    <input
                                        type="file"
                                        class="form-control"
                                        name="comprobante_trasnferencia"
                                        id="comprobante_trasnferencia"
                                        placeholder="Comprobante de transferencia"
                                        aria-describedby="help_id" />
                                    <div id="help_id" class="form-text">Suba su comprobante de transferencia</div>
                                </div>
                            </div>

                            <div id="info_mercado_pago" class="p-4" style="display: none;">
                                <h6>Información de Mercado Pago</h6>
                                <p>Será redirigido a Mercado Pago para completar su pago.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="btn_pagar" type="button" class="btn btn-primary-custom w-100" onclick="pagarPedido()">pagar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('forma_pago').addEventListener('change', function() {
            const infoTransferencia = document.getElementById('info_transferencia');
            const infoMercadoPago = document.getElementById('info_mercado_pago');
            const container = document.getElementById('info_container');
            const btnPagar = document.getElementById('btn_pagar');

            // Cambiar visibilidad del contenido
            if (this.value === 'transferencia') {
                infoTransferencia.style.display = 'block';
                infoMercadoPago.style.display = 'none';
                btnPagar.style.display = 'block';
            } else if (this.value === 'mercadoPago') {
                infoTransferencia.style.display = 'none';
                btnPagar.style.display = 'none';
                infoMercadoPago.style.display = 'block';
            }

            // Ajustar altura del contenedor dinámicamente
            var activeContent = this.value === 'transferencia' ? infoTransferencia : infoMercadoPago;
            container.style.height = activeContent.offsetHeight + 'px';
        });

        // Inicializar altura para que coincida con el contenido inicial
        window.addEventListener('DOMContentLoaded', function() {
            var container = document.getElementById('info_container');
            var infoTransferencia = document.getElementById('info_transferencia');
            container.style.height = infoTransferencia.offsetHeight + 'px';
        });


        function toggleFragrances(productId) {
            // Usamos el ID único generado con "checkout-"
            const fragranceList = document.getElementById(`checkout-fragancias-${productId}`);

            // Verificar si el elemento existe
            if (!fragranceList) {
                console.error(`No se encontró el elemento con ID checkout-fragancias-${productId}`);
                return;
            }

            const toggleIcon = fragranceList.previousElementSibling.querySelector('i');

            if (toggleIcon) {
                if (fragranceList.style.display === 'none') {
                    fragranceList.style.display = 'block';
                    toggleIcon.classList.remove('bi-chevron-down');
                    toggleIcon.classList.add('bi-chevron-up');
                } else {
                    fragranceList.style.display = 'none';
                    toggleIcon.classList.remove('bi-chevron-up');
                    toggleIcon.classList.add('bi-chevron-down');
                }
            } else {
                console.warn('No se encontró el icono <i> asociado.');
            }
        }

        function pagarPedido() {
            const formaPago = document.getElementById('forma_pago').value;
            const comprobanteTransferencia = document.getElementById('comprobante_trasnferencia').files[0];
            const idPedido = document.getElementById('id_pedido').value;

            if (formaPago === 'transferencia' && !comprobanteTransferencia) {
                alert('Por favor, suba su comprobante de transferencia.');
                return;
            }
            data = new FormData();
            data.append('id_pedido', idPedido);
            data.append('forma_pago', formaPago);
            data.append('comprobante_trasnferencia', comprobanteTransferencia);

            fetch('procesar_pago.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('El pago se realizó con éxito.');
                        window.location.href = './panel_usuario.php';
                    } else {
                        alert('Ocurrió un error al procesar el pago.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al procesar el pago.');
                });


        }
    </script>
</body>