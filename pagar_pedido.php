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
            pp.precio,
            pp.estado as estado_producto
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

$sql_medotos_pago =  $con->prepare("SELECT mp.id_metodo_pago, mp.tipo, it.id_info_transferencia, it.banco, it.cuenta, it.cbu, it.alias FROM metodos_pago mp LEFT JOIN info_transferencia it ON mp.id_info_transferencia = it.id_info_transferencia;
");
$sql_medotos_pago->execute();
$metodos_pago = $sql_medotos_pago->fetchAll(PDO::FETCH_ASSOC);

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
            max-height: 0;
            /* Por defecto, oculto */
            overflow: hidden;
            transition: max-height 0.5s ease;
            /* Transición suave */
        }

        .fragrance-list.open {
            max-height: 300px;
            /* Altura máxima cuando está abierto (ajusta según el contenido) */
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
                        <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $detalle_pedido["id_usuario"] ?>">
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
                            <?php
                            $subtotal = 0; // Inicializa el subtotal aquí
                            foreach ($pedido as $index => $item):
                                // Verifica si el producto está activo o no
                                $esActivo = $item['estado_producto'] != 0;
                                if ($esActivo) {
                                    // Solo suma al subtotal si el estado es activo
                                    $subtotal += $item['precio'] * $item['cantidad'];
                                }
                            ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 <?php echo $esActivo ? '' : 'text-decoration-line-through'; ?>">
                                            <?php echo htmlspecialchars($item['producto_nombre']); ?>
                                        </h6>
                                        <span class="fragrance-toggle" onclick="toggleFragrances(<?php echo $index; ?>)">
                                            Ver fragancias <i class="bi bi-chevron-down"></i>
                                        </span>
                                    </div>
                                    <ul id="checkout-fragancias-<?php echo $index; ?>" class="fragrance-list">
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
                                <?php foreach ($metodos_pago as $metodo_pago) { ?>
                                    <option
                                        value="<?php echo $metodo_pago['id_metodo_pago']; ?>"
                                        data-banco="<?php echo $metodo_pago['banco']; ?>"
                                        data-cuenta="<?php echo $metodo_pago['cuenta']; ?>"
                                        data-cbu="<?php echo $metodo_pago['cbu']; ?>"
                                        data-alias="<?php echo $metodo_pago['alias']; ?>">
                                        <?php echo $metodo_pago['tipo']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div id="info_container" style="position: relative; overflow: hidden; min-height: 150px;">
                            <div id="info_transferencia" class="p-4" style="display: none;">
                                <h6>Información de Transferencia</h6>
                                <p><strong>Banco:</strong> <span id="banco_transferencia"></span></p>
                                <p><strong>Cuenta:</strong> <span id="cuenta_transferencia"></span></p>
                                <p><strong>CBU:</strong> <span id="cbu_transferencia"></span></p>
                                <p><strong>Alias:</strong> <span id="alias_transferencia"></span></p>

                                <div class="mb-3">
                                    <label for="comprobante_transferencia" class="form-label">Comprobante</label>
                                    <input
                                        type="file"
                                        class="form-control"
                                        name="comprobante_transferencia"
                                        id="comprobante_transferencia"
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
                        <button id="btn_pagar" type="button" class="btn btn-primary-custom w-100" onclick="pagarPedido()">Pagar</button>
                        <button id="btn_mercado_pago" type="button" class="btn btn-primary-custom w-100" style="display: none;" onclick="redirigirMercadoPago()">Pagar con Mercado Pago</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectFormaPago = document.getElementById('forma_pago');
            const infoTransferencia = document.getElementById('info_transferencia');
            const infoMercadoPago = document.getElementById('info_mercado_pago');
            const btnPagar = document.getElementById('btn_pagar');
            const btnMercadoPago = document.getElementById('btn_mercado_pago');

            const bancoTransferencia = document.getElementById('banco_transferencia');
            const cuentaTransferencia = document.getElementById('cuenta_transferencia');
            const cbuTransferencia = document.getElementById('cbu_transferencia');
            const aliasTransferencia = document.getElementById('alias_transferencia');

            // Función para actualizar la información dinámica
            function actualizarInformacion() {
                const selectedOption = selectFormaPago.options[selectFormaPago.selectedIndex];
                const metodoId = selectedOption.value;

                if (metodoId == '1') { // Método de transferencia bancaria
                    infoTransferencia.style.display = 'block';
                    infoMercadoPago.style.display = 'none';
                    btnPagar.style.display = 'block';
                    btnMercadoPago.style.display = 'none';

                    // Actualizar información dinámica de transferencia
                    bancoTransferencia.textContent = selectedOption.getAttribute('data-banco');
                    cuentaTransferencia.textContent = selectedOption.getAttribute('data-cuenta');
                    cbuTransferencia.textContent = selectedOption.getAttribute('data-cbu');
                    aliasTransferencia.textContent = selectedOption.getAttribute('data-alias');
                } else if (metodoId == '2') { // Método de Mercado Pago
                    infoTransferencia.style.display = 'none';
                    infoMercadoPago.style.display = 'block';
                    btnPagar.style.display = 'none';
                    btnMercadoPago.style.display = 'block';
                } else {
                    infoTransferencia.style.display = 'none';
                    infoMercadoPago.style.display = 'none';
                    btnPagar.style.display = 'block';
                    btnMercadoPago.style.display = 'none';
                }
            }

            // Evento para detectar cambios en el selector
            selectFormaPago.addEventListener('change', actualizarInformacion);

            // Mostrar información inicial (primera opción seleccionada por defecto)
            actualizarInformacion();
        });

        function redirigirMercadoPago() {
            alert('Redirigiendo a Mercado Pago...');
        }

        function pagarPedido() {
            const idPedido = document.getElementById('id_pedido').value;
            const idUsuario = document.getElementById('id_usuario').value;
            const metodoPago = document.getElementById('forma_pago').value;
            let nombreComprobante = null;
            if (metodoPago === '1') {
                const comprobanteTransferencia = document.getElementById('comprobante_transferencia').files[0];
                if (!comprobanteTransferencia) {
                    alert('Por favor, suba su comprobante de transferencia.');
                    return;
                }
                nombreComprobante = comprobanteTransferencia.name;
                console.log(comprobanteTransferencia);
            }

            data = new FormData();
            data.append('id_pedido', idPedido);
            data.append('id_usuario', idUsuario);
            data.append('metodo_pago', metodoPago);
            data.append('nombre_comprobante', nombreComprobante);

            fetch('./procesar_pago.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        alert('El pago se realizó correctamente.');
                        window.location.href = './gracias2.php?id_pedido=' + idPedido;
                    } else {
                        alert('Ocurrió un error al procesar el pago.');
                    }
                })

        }

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

            // Seleccionamos el ícono asociado al botón
            const toggleIcon = fragranceList.previousElementSibling.querySelector('i');

            if (toggleIcon) {
                if (fragranceList.classList.contains('open')) {
                    // Ocultamos la lista removiendo la clase "open"
                    fragranceList.classList.remove('open');
                    toggleIcon.classList.remove('bi-chevron-up');
                    toggleIcon.classList.add('bi-chevron-down');
                } else {
                    // Mostramos la lista añadiendo la clase "open"
                    fragranceList.classList.add('open');
                    toggleIcon.classList.remove('bi-chevron-down');
                    toggleIcon.classList.add('bi-chevron-up');
                }
            } else {
                console.warn('No se encontró el icono <i> asociado.');
            }
        }
    </script>
</body>