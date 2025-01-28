<?php

include "./header.php";

include './admin/config/sbd.php';
require "mp.php";

if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('No hay una sesión iniciada');</script>";
    var_dump($_SESSION);
    exit;
} else {
    $id_usuario = $_SESSION['usuario'];
}

$sql_domicilios = $con->prepare("SELECT d.*, ud.tipo_domicilio, ud.principal,ud.estado
FROM domicilios d
    JOIN usuario_domicilios ud ON d.id_domicilio = ud.id_domicilio
    JOIN usuarios i ON ud.id_usuario = i.id_usuario
WHERE i.id_usuario = :id_usuario");
$sql_domicilios->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$sql_domicilios->execute();
$domicilios = $sql_domicilios->fetchAll(PDO::FETCH_ASSOC);

$sql_informacio_usuario = $con->prepare("SELECT 
                    u.id_usuario, 
                    u.email, 
                    iu.nombre, 
                    iu.apellido, 
                    iu.dni, 
                    iu.fecha_nacimiento, 
                    iu.telefono, 
                    s.nombre AS sexo, 
                    eu.nombre AS estado_usuario
                FROM 
                    usuarios u
                    JOIN info_usuarios iu ON u.id_usuario = iu.id_usuario
                    JOIN sexos s ON iu.id_sexo = s.id_sexo
                    JOIN estados_usuarios eu ON u.id_estado_usuario = eu.id_estado_usuario
                WHERE 
                    u.id_usuario = :id_usuario;");
$sql_informacio_usuario->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$sql_informacio_usuario->execute();
$usuario = $sql_informacio_usuario->fetch(PDO::FETCH_ASSOC);


$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
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
                <p class="card-text">Complete su pedido en 3 sencillos pasos</p>
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
                    <?php foreach ($cart as $index => $item): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?php echo htmlspecialchars($item['nombre']); ?></h6>
                                <span class="fragrance-toggle" onclick="toggleFragrances(<?php echo $index; ?>)">
                                    Ver fragancias <i class="bi bi-chevron-down"></i>
                                </span>
                            </div>
                            <ul id="checkout-fragancias-<?php echo $index; ?>" class="fragrance-list" style="display: none;">

                                <?php foreach ($item['fragancias'] as $fragancia): ?>
                                    <li>
                                        <?php echo htmlspecialchars($fragancia['aroma']); ?>
                                        (<?php echo $fragancia['cantidad']; ?>) - $<?php echo number_format($item['precio'] * $fragancia['cantidad'], 2); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <?php
                        $total = 0;
                        foreach ($cart as $item) {
                            foreach ($item['fragancias'] as $fragancia) {
                                $total += $item['precio'] * $fragancia['cantidad'];
                            }
                        }
                        ?>
                        <strong>Total</strong>
                        <strong id="totalPrice">$<?php echo number_format($total, 2); ?></strong>
                    </div>
                </div>

                <div id="step2" class="checkout_step-content d-none">
                    <h2><i class="bi bi-person"></i> Información de facturación</h2>
                    <form id="billingForm">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese su nombre" value="<?php echo $usuario["nombre"] ?>" required>
                            </div>
                            <div class="col">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese su apellido" value="<?php echo $usuario["apellido"] ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="dni" class="form-label">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese su DNI" value="<?php echo $usuario["dni"] ?>" required>
                            </div>
                            <div class="col">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Ingrese su número de teléfono" value="<?php echo $usuario["telefono"] ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su mail" value="<?php echo $usuario["email"] ?>" required>
                        </div>
                        <hr>
                        <div class="container mt-4">
                            <!-- Selección del método de entrega -->
                            <h4 class="mb-3">Seleccione el método de entrega</h4>
                            <div class="d-flex justify-content-center gap-3 mb-4">
                                <button type="button" id="btn-pickup" class="btn btn-outline-custom p-3 w-50" onclick="selectDeliveryMethod('pickup', event)">
                                    <i class="bi bi-shop d-block mb-2"></i> Retirar en sucursal
                                </button>
                                <button type="button" id="btn-delivery" class="btn btn-outline-custom p-3 w-50" onclick="selectDeliveryMethod('delivery', event)">
                                    <i class="bi bi-geo-alt d-block mb-2"></i> Envío a domicilio
                                </button>
                            </div>

                            <!-- Selección de sucursales -->
                            <div id="pickupOptions" class="d-none">
                                <h5 class="mb-3">Seleccione la sucursal</h5>
                                <div class="d-flex justify-content-center gap-3">
                                    <div class="card border-dark pickup-card" onclick="selectPickupPoint(this)" style="width: 18rem;">
                                        <div class="card-body">
                                            <h5 class="card-title">Sucursal Centro</h5>
                                            <p class="card-text">Av. Principal 123<br>Horario: 9:00 - 18:00</p>
                                        </div>
                                    </div>
                                    <div class="card border-dark pickup-card" onclick="selectPickupPoint(this)" style="width: 18rem;">
                                        <div class="card-body">
                                            <h5 class="card-title">Sucursal Norte</h5>
                                            <p class="card-text">Calle Norte 456<br>Horario: 9:00 - 18:00</p>
                                        </div>
                                    </div>
                                    <div class="card border-dark pickup-card" onclick="selectPickupPoint(this)" style="width: 18rem;">
                                        <div class="card-body">
                                            <h5 class="card-title">Sucursal Sur</h5>
                                            <p class="card-text">Av. Sur 789<br>Horario: 9:00 - 18:00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulario de dirección -->
                            <div id="deliveryForm" class="d-none mt-4">
                                <h5 class="mb-3">Información de domicilios</h5>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="province" class="form-label">Provincia</label>
                                        <input type="text" class="form-control" id="province" name="province" placeholder="Ingrese su provincia" required>
                                    </div>
                                    <div class="col">
                                        <label for="locality" class="form-label">Localidad</label>
                                        <input type="text" class="form-control" id="locality" name="locality" placeholder="Ingrese su localidad" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="street" class="form-label">Calle</label>
                                        <input type="text" class="form-control" id="street" name="street" placeholder="Calle Principal" required>
                                    </div>
                                    <div class="col">
                                        <label for="number" class="form-label">Número</label>
                                        <input type="text" class="form-control" id="number" name="number" placeholder="123" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="floor" class="form-label">Piso</label>
                                        <input type="text" class="form-control" id="floor" name="floor" placeholder="Ej. 2" required>
                                    </div>
                                    <div class="col">
                                        <label for="department" class="form-label">Departamento</label>
                                        <input type="text" class="form-control" id="department" name="department" placeholder="Ej. B" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="postalCode" class="form-label">Código postal</label>
                                    <input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="28001" required>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label for="additionalInfo" class="form-label">Información adicional</label>
                                    <textarea class="form-control" id="additionalInfo" name="additionalInfo" rows="3" placeholder="Ingrese información adicional (opcional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>


                </div>

                <div id="step3" class="checkout_step-content d-none">
                    <h2><i class="bi bi-credit-card"></i> Método de pago</h2>
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
                                <input type="file" class="form-control" id="comprobanteTransferencia">
                            </div>
                        </div>

                        <!-- Mercado Pago -->
                        <div id="mercadoPagoFields" class="payment-fields d-none">
                            <div class="text-center mb-3">
                                <div id="wallet_container"></div>
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

    <script src="checkout.js"></script>
    <script>
    
    </script>
</body>


</html>