<?php

include "./header.php";

include './admin/config/sbd.php';

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
                        <strong>Total</strong>
                        <strong id="totalPrice"></strong>
                    </div>
                </div>

                <div id="step2" class="checkout_step-content d-none">
                    <h2><i class="bi bi-person"></i> Información de facturación</h2>
                    <form id="billingForm">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" placeholder="Ingrese su nombre" value="<?php echo $usuario["nombre"] ?>" required>
                            </div>
                            <div class="col">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" placeholder="Ingrese su apellido" value="<?php echo $usuario["apellido"] ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="dni" class="form-label">DNI</label>
                                <input type="text" class="form-control" id="dni" placeholder="Ingrese su DNI" value="<?php echo $usuario["dni"] ?>" required>
                            </div>
                            <div class="col">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone" placeholder="Ingrese su número de teléfono" value="<?php echo $usuario["telefono"] ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" placeholder="Ingrese su mail" value="<?php echo $usuario["email"] ?>" required>
                        </div>
                        <hr>
                        <h2><i class="bi bi-signpost"></i> Información de domicilios</h2>


                        <div class="row mb-3">
                            <div class="col">
                                <label for="province" class="form-label">Provincia</label>
                                <input type="text" class="form-control" id="province" placeholder="Ingrese su provincia" required>
                            </div>
                            <div class="col">
                                <label for="locality" class="form-label">Localidad</label>
                                <input type="text" class="form-control" id="locality" placeholder="Ingrese su localidad" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="street" class="form-label">calle</label>
                                <input type="text" class="form-control" id="street" placeholder="Calle Principal" required>
                            </div>
                            <div class="col">
                                <label for="number" class="form-label">Número</label>
                                <input type="text" class="form-control" id="number" placeholder="123" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="floor" class="form-label">Piso</label>
                                <input type="text" class="form-control" id="floor" placeholder="Ej. 2" required>
                            </div>
                            <div class="col">
                                <label for="department" class="form-label">Departamento</label>
                                <input type="text" class="form-control" id="department" placeholder="Ej. B" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="postalCode" class="form-label">Código postal</label>
                            <input type="text" class="form-control" id="postalCode" placeholder="28001" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="additionalInfo" class="form-label">Información adicional</label>
                            <textarea class="form-control" id="additionalInfo" rows="3" placeholder="Ingrese información adicional (opcional)"></textarea>
                        </div>
                    </form>


                </div>

                <div id="step3" class="checkout_step-content d-none">
                    <h2><i class="bi bi-credit-card"></i> Método de pago</h2>
                    <form id="paymentForm">
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
                                <button type="button" class="btn btn-primary">Pagar con Mercado Pago</button>
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
                    </form>
                </div>
            </div>
            <div class="card-footer">
                <button id="prevBtn" class="btn btn-outline-custom" disabled>Anterior</button>
                <button id="nextBtn" class="btn btn-primary-custom float-end">Siguiente</button>
            </div>
        </div>
    </div>

    <script src="checkout.js"></script>
</body>
<script>

</script>

</html>