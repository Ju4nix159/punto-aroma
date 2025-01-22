<?php

include './header.php';
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
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Checkout - Punto Aroma</h1>
        <div class="row">

            <div class="col-md-8 mb-4 ">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="mb-0">Información Personal</h5>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <strong><i class="fas fa-user mr-1"></i> Nombre Completo</strong>
                        <p class="text-muted"><?php echo $usuario["nombre"] ?></p>

                        <hr>

                        <strong><i class="fas fa-envelope mr-1"></i> Correo Electrónico</strong>
                        <p class="text-muted"><?php echo $usuario["email"] ?></p>

                        <hr>

                        <strong><i class="fas fa-phone mr-1"></i> Teléfono</strong>
                        <p class="text-muted"><?php echo $usuario["telefono"] ?></p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumen del pedido</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
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
                        </ul>
                        <hr>
                        <!-- Resumen del total -->
                        <?php
                        $subtotal = array_reduce($cart, function ($carry, $item) {
                            foreach ($item['fragancias'] as $fragancia) {
                                $carry += $item['precio'] * $fragancia['cantidad'];
                            }
                            return $carry;
                        }, 0);
                        ?>
                        <div class="justify-content-between mb-3">
                            <label for="" class="form-label">Tipo pago</label>
                            <select
                                class="form-select form-select-lg"
                                name="tipo_pago"
                                id="tipo_pago">
                                <option value="seña" selected>seña(30%)</option>
                                <option value="pago_total">Pago total</option>
                            </select>

                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total</span>
                            <strong id="total" class="text-primary-custom"></strong>
                        </div>
                        <button type="button" class="btn btn-primary-custom w-100" onclick="procesarPago()">Realizar pedido</button>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Información de Domicilio</h5>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="mb-3">
                            <select class="form-select" id="direccionSelect" onchange="mostrarDireccion()">
                                <?php foreach ($domicilios as $domicilio): ?>
                                    <?php if ($domicilio['estado'] == 1): // Solo mostrar domicilios con estado 1 
                                    ?>
                                        <option
                                            value="<?php echo $domicilio['id_domicilio']; ?>"
                                            data-calle="<?php echo htmlspecialchars($domicilio['calle']); ?>"
                                            data-numero="<?php echo htmlspecialchars($domicilio['numero']); ?>"
                                            data-ciudad="<?php echo htmlspecialchars($domicilio['localidad']); ?>"
                                            data-estado="<?php echo htmlspecialchars($domicilio['provincia']); ?>"
                                            data-codigo-postal="<?php echo htmlspecialchars($domicilio['codigo_postal']); ?>"
                                            <?php echo $domicilio['principal'] == 1 ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($domicilio['tipo_domicilio']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="direccionInfo" class="mt-3" style="display: none;">
                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Dirección</strong>
                            <p class="text-muted" id="direccionCalle"></p>

                            <hr>

                            <strong><i class="fas fa-city mr-1"></i> Ciudad</strong>
                            <p class="text-muted" id="direccionCiudad"></p>

                            <hr>

                            <strong><i class="fas fa-map mr-1"></i> Provincia</strong>
                            <p class="text-muted" id="direccionEstado"></p>

                            <hr>

                            <strong><i class="fas fa-mail-bulk mr-1"></i> Código Postal</strong>
                            <p class="text-muted" id="direccionCodigoPostal"></p>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <?php
    include './footer.php';
    ?>

    <script src="carrito.js"></script>
    <script>
        function mostrarDireccion() {
            const select = document.getElementById('direccionSelect');
            const selectedOption = select.options[select.selectedIndex];

            // Extraer datos del atributo data
            const calle = selectedOption.getAttribute('data-calle');
            const numero = selectedOption.getAttribute('data-numero');
            const ciudad = selectedOption.getAttribute('data-ciudad');
            const estado = selectedOption.getAttribute('data-estado');
            const codigoPostal = selectedOption.getAttribute('data-codigo-postal');

            // Actualizar el contenido del DOM
            document.getElementById('direccionCalle').textContent = `${calle || 'No disponible'} ${numero || ''}`;
            document.getElementById('direccionCiudad').textContent = ciudad || 'No disponible';
            document.getElementById('direccionEstado').textContent = estado || 'No disponible';
            document.getElementById('direccionCodigoPostal').textContent = codigoPostal || 'No disponible';

            // Mostrar la sección
            document.getElementById('direccionInfo').style.display = 'block';
        }

        // Llamamos a la función al cargar para inicializar el estado
        document.addEventListener('DOMContentLoaded', mostrarDireccion);

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

        function procesarPago() {
            const idDomicilio = document.getElementById('direccionSelect').value;

            if (!idDomicilio) {
                alert('Por favor, selecciona una dirección.');
                return;
            }

            fetch('procesar_pedido.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        id_domicilio: idDomicilio
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pedido realizado con éxito. ID del pedido: ' + data.id_pedido);
                        cargarCarrito();
                        window.location.href = 'gracias.php?id_pedido=' + data.id_pedido; // Redirigir a una página de agradecimiento
                    } else {
                        alert('Error: ' + (data.error || 'No se pudo realizar el pedido.'));
                    }
                })
                .catch(error => {
                    console.error('Error al procesar el pedido:', error);
                    alert('Ocurrió un error al realizar el pedido.', error);
                });
        };


        document.getElementById('tipo_pago').addEventListener('change', function() {
            const tipoPago = this.value;
            const totalElement = document.getElementById('total');
            const subtotal = <?php echo $subtotal; ?>;
            let total;

            if (tipoPago === 'seña') {
            total = (subtotal) * 0.30;
            } else {
            total = subtotal ;
            }

            totalElement.textContent = '$' + total.toFixed(2);
        });

        // Llamamos a la función al cargar para inicializar el estado
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('tipo_pago').dispatchEvent(new Event('change'));
        });
    </script>
</body>