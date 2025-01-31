<?php

include "./header.php";

include './admin/config/sbd.php';
require "mp_total.php";

if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('No hay una sesión iniciada');</script>";
    var_dump($_SESSION);
    exit;
} else {
    $id_usuario = $_SESSION['usuario'];
}

$sql_informacio_usuario = $con->prepare("SELECT 
                    u.id_usuario, 
                    u.email, 
                    iu.nombre, 
                    iu.apellido, 
                    iu.dni, 
                    iu.fecha_nacimiento, 
                    iu.telefono, 
                    eu.nombre AS estado_usuario
                FROM 
                    usuarios u
                    JOIN info_usuarios iu ON u.id_usuario = iu.id_usuario
                    JOIN estados_usuarios eu ON u.id_estado_usuario = eu.id_estado_usuario
                WHERE 
                    u.id_usuario = :id_usuario;");
$sql_informacio_usuario->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$sql_informacio_usuario->execute();
$usuario = $sql_informacio_usuario->fetch(PDO::FETCH_ASSOC);

$id_pedido = $_GET['id_pedido'];

$sql_producto_pedido = $con->prepare("SELECT v.aroma, pp.id_pedido, pp.sku, p.nombre AS producto_nombre, pp.cantidad, pp.precio, pp.estado
FROM productos_pedido pp
    JOIN productos p ON pp.id_producto = p.id_producto
    JOIN variantes v ON pp.sku = v.sku
WHERE pp.id_pedido = :id_pedido;");
$sql_producto_pedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_producto_pedido->execute();
$productos = $sql_producto_pedido->fetchAll(PDO::FETCH_ASSOC);

$sql_envio = $con->prepare("SELECT p.id_pedido, p.envio AS costo_envio, d.*
FROM pedidos p
JOIN domicilios d ON p.id_domicilio = d.id_domicilio
WHERE p.id_pedido = :id_pedido");
$sql_envio->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_envio->execute();
$info_pedido = $sql_envio->fetch(PDO::FETCH_ASSOC);






?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="styles.css" rel="stylesheet">
    <style>
        /* Estilo para la tarjeta seleccionada */
        .pickup-card.selected {
            background-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(0, 128, 0, 0.5);
            /* Efecto visual */
        }

        .is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Finalizar compra</h1>
                <p class="card-text">pago total del pedido</p>
            </div>

            <div class="card-body">
                <div class="checkout_steps mb-4">
                    <div class="checkout_step active" data-step="1">
                        <div class="checkout_step-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <span class="checkout_step-text">Resumen</span>
                    </div>
                    <div class="checkout_step-arrow">&rarr;</div>
                    <div class="checkout_step" data-step="2">
                        <div class="checkout_step-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        <span class="checkout_step-text">Facturación</span>
                    </div>
                    <div class="checkout_step-arrow">&rarr;</div>
                    <div class="checkout_step" data-step="3">
                        <div class="checkout_step-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <span class="checkout_step-text">Pago</span>
                    </div>
                </div>

                <div id="step1" class="checkout_step-content">
                    <h2><i class="bi bi-box-seam"></i> Resumen de productos</h2>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Fragancia</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <?php

                            $total = 0;
                            foreach ($productos as $producto) {
                                $isUnavailable = $producto['estado'] == 0;
                                $subtotal = $producto["cantidad"] * $producto["precio"];
                                if (!$isUnavailable) {
                                    $total += $subtotal;
                                }
                            ?>
                                <tr class="<?php echo $isUnavailable ? 'text-muted' : ''; ?>">
                                    <input type="hidden" name="id_pedido" id="id_pedido" value="<?php echo $id_pedido; ?>">
                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                        <?php echo $producto["producto_nombre"]; ?>
                                    </td>
                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                        <?php echo $producto["aroma"]; ?>
                                    </td>
                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                        <?php echo $producto["cantidad"]; ?>
                                    </td>
                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                        $<?php echo number_format($producto["precio"], 2); ?>
                                    </td>
                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                        $<?php echo number_format($subtotal, 2); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <!-- Costo de Envío -->
                            <tr>
                                <td>Envío</td>
                                <td colspan="2"></td>
                                <td>$<?php echo number_format($info_pedido['costo_envio'], 2); ?></td>
                                <td>$<?php echo number_format($info_pedido['costo_envio'], 2); ?></td>
                            </tr>
                            <!-- Total -->
                            <tr>
                                <td><strong>Total</strong></td>
                                <td colspan="2"></td>
                                <td colspan="2"><strong>$<?php echo number_format($total + $info_pedido['costo_envio'], 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>

                </div>

                <div id="step2" class="checkout_step-content d-none">
                    <h2><i class="bi bi-person"></i> Información de facturación</h2>
                    <div id="billingForm">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input disabled type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese su nombre" value="<?php echo $usuario["nombre"] ?>" required>
                            </div>
                            <div class="col">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input disabled type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese su apellido" value="<?php echo $usuario["apellido"] ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="dni" class="form-label">DNI</label>
                                <input disabled type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese su DNI" value="<?php echo $usuario["dni"] ?>" required>
                            </div>
                            <div class="col">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input disabled type="tel" class="form-control" id="phone" name="phone" placeholder="Ingrese su número de teléfono" value="<?php echo $usuario["telefono"] ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input disabled type="email" class="form-control" id="email" name="email" placeholder="Ingrese su mail" value="<?php echo $usuario["email"] ?>" required>
                            </div>
                            <div class="col">
                                <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
                                <input disabled type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" value="<?php echo $usuario["fecha_nacimiento"] ?>" required>
                            </div>
                        </div>
                        <hr>
                        <div class="container mt-4">
                            <!-- Selección del método de entrega -->
                            <div id="deliveryForm" class="mt-4">
                                <h4 class="mb-3">Información de domicilios</h4>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="province" class="form-label">Provincia</label>
                                        <input disabled value="<?php echo $info_pedido["provincia"]?>" type="text" class="form-control" id="province" name="province" placeholder="Ingrese su provincia" required>
                                    </div>
                                    <div class="col">
                                        <label for="locality" class="form-label">Localidad</label>
                                        <input disabled value="<?php echo $info_pedido["localidad"]?>" type="text" class="form-control" id="locality" name="locality" placeholder="Ingrese su localidad" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="street" class="form-label">Calle</label>
                                        <input disabled value="<?php echo $info_pedido["calle"]?>" type="text" class="form-control" id="street" name="street" placeholder="Calle Principal" required>
                                    </div>
                                    <div class="col">
                                        <label for="number" class="form-label">Número</label>
                                        <input disabled value="<?php echo $info_pedido["numero"]?>" type="text" class="form-control" id="number" name="number" placeholder="123" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="floor" class="form-label">Piso</label>
                                        <input disabled value="<?php echo !empty($info_pedido["piso"]) ? $info_pedido["piso"] : 'No aplica'; ?>" type="text" class="form-control" id="floor" name="floor" placeholder="Ej. 2" required>
                                    </div>
                                    <div class="col">
                                        <label for="department" class="form-label">Departamento</label>
                                        <input disabled value="<?php echo !empty($info_pedido["departamento"]) ? $info_pedido["departamento"] : 'No aplica'; ?>" type="text" class="form-control" id="department" name="department" placeholder="Ej. B" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="postalCode" class="form-label">Código postal</label>
                                    <input disabled value="<?php echo $info_pedido["codigo_postal"]?>" type="text" class="form-control" id="postalCode" name="postalCode" placeholder="28001" required>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label for="additionalInfo" class="form-label">Información adicional</label>
                                    <textarea disabled value="<?php echo $info_pedido["informacion_adicional"]?>" class="form-control" id="additionalInfo" name="additionalInfo" rows="3" placeholder="Ingrese información adicional (opcional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="step3" class="checkout_step-content d-none">
                    <h2 class="d-flex align-items-center">
                        <i class="bi bi-credit-card me-2"></i> Método de pago
                    </h2>
                    <div class="mt-3">
                        <p class="bg-success text-white p-2 rounded">
                            El precio a pagar es el 70% restante del total del pedido.
                        </p>
                    </div>
                    <div class="mt-3">
                        <p class="fw-bold">
                            Total a pagar: <span id="montoAPagar" class="text-success"><strong id="totalPrice">$<?php echo number_format(($total + $info_pedido["costo_envio"]), 2); ?></strong></span>
                            <input type="hidden" name="monto" id="monto" value="<?php echo number_format(($total + $info_pedido["costo_envio"]), 2); ?>">
                        </p>
                    </div>
                    <div id="paymentForm">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="transferencia" value="transferencia" checked>
                                <label class="form-check-label" for="transferencia">
                                    Transferencia bancaria
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="mercadoPago" value="mercadopago">
                                <label class="form-check-label" for="mercadoPago">
                                    Mercado Pago
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="pagoEnLocal" value="pagoenlocal">
                                <label class="form-check-label" for="pagoEnLocal">
                                    Pago en local
                                </label>
                            </div>
                        </div>

                        <!-- Transferencia Bancaria -->
                        <div id="transferenciaFields" class="payment-fields">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Cuenta Bancaria 1</h5>
                                            <p class="card-text">Banco: Banco Ejemplo<br>Cuenta: 123456789<br>CBU: 000000310000123456789</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Cuenta Bancaria 2</h5>
                                            <p class="card-text">Banco: Otro Banco<br>Cuenta: 987654321<br>CBU: 000000320000987654321</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Cuenta Bancaria 3</h5>
                                            <p class="card-text">Banco: Banco Más<br>Cuenta: 555555555<br>CBU: 000000330000555555555</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comprobanteTransferencia" class="form-label">Subir comprobante</label>
                                <input type="file" class="form-control" id="comprobanteTransferencia" accept="image/*,.pdf">
                                <small class="text-muted">Formatos permitidos: JPG, PNG, GIF, PDF. Tamaño máximo: 5MB.</small>
                                <small class="text-danger" id="fileError" style="display: none;"></small>
                            </div>

                        </div>

                        <!-- Mercado Pago -->
                        <div id="mercadoPagoFields" class="payment-fields d-none">
                            <div class="text-center mb-3">
                                <div id="wallet_container">

                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comprobanteMercadoPago" class="form-label">Subir comprobante</label>
                                <input type="file" class="form-control" id="comprobanteMercadoPago">
                            </div>
                        </div>

                        <!-- Pago en Local -->
                        <div id="pagoEnLocalFields" class="payment-fields d-none">
                            <p>Puede realizar el pago en nuestra oficina local.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button id="prevBtn" class="btn btn-outline-custom" disabled>Anterior</button>
                <button id="nextBtn" class="btn btn-primary-custom float-end">Siguiente</button>
            </div>
        </div>
    </div>

    <script src="pago_total.js"></script>
    <script>
        document.getElementById('comprobanteTransferencia').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const maxSizeMB = 5; // Límite de tamaño en MB
            const maxSizeBytes = maxSizeMB * 1024 * 1024; // Convertir a bytes
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

            const errorElement = document.getElementById('fileError');
            errorElement.style.display = 'none';
            errorElement.textContent = '';

            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    errorElement.textContent = 'Solo se permiten archivos de imagen (JPG, PNG, GIF) o PDF.';
                    errorElement.style.display = 'block';
                    event.target.value = ''; // Resetear input
                    return;
                }

                if (file.size > maxSizeBytes) {
                    errorElement.textContent = `El archivo no debe superar los ${maxSizeMB}MB.`;
                    errorElement.style.display = 'block';
                    event.target.value = ''; // Resetear input
                    return;
                }
            }
        });
    </script>
</body>


</html>