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

$sql_domicilios = $con->prepare("SELECT d.*, ud.tipo_domicilio, ud.principal
FROM domicilios d
    JOIN usuario_domicilios ud ON d.id_domicilio = ud.id_domicilio
    JOIN usuarios i ON ud.id_usuario = i.id_usuario
WHERE i.id_usuario = :id_usuario");
$sql_domicilios->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$sql_domicilios->execute();
$domicilios = $sql_domicilios->fetchAll(PDO::FETCH_ASSOC);


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
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Dirección de envío</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <select class="form-select" id="direccionSelect" onchange="mostrarDireccion()">
                                <?php foreach ($domicilios as $domicilio): ?>
                                    <option value="<?php echo $domicilio['id_domicilio']; ?>"
                                        data-detalle="<?php echo htmlspecialchars($domicilio['calle'] . ', ' . $domicilio['localidad'] . ', ' . $domicilio['provincia'] . ', CP ' . $domicilio['codigo_postal']); ?>"
                                        <?php echo $domicilio['principal'] == 1 ? 'selected' : ''; ?>>
                                        <?php echo $domicilio['tipo_domicilio']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="direccionInfo" class="mt-3" style="display: none;">
                            <h6 class="mb-2">Información de la dirección seleccionada:</h6>
                            <p id="direccionDetalle"></p>
                        </div>

                        <div id="direccionInfo" class="mt-3" style="display: none;">
                            <h6 class="mb-2">Información de la dirección seleccionada:</h6>
                            <p id="direccionDetalle"></p>
                        </div>
                        <form id="nuevaDireccionForm" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="calle" class="form-label">Calle</label>
                                    <input type="text" class="form-control" id="calle" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="numero" class="form-label">Número</label>
                                    <input type="text" class="form-control" id="numero" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="ciudad" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="provincia" class="form-label">Provincia</label>
                                    <input type="text" class="form-control" id="provincia" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="codigoPostal" class="form-label">Código Postal</label>
                                <input type="text" class="form-control" id="codigoPostal" required>
                            </div>
                        </form>
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
                        <button type="button" class="btn btn-primary-custom w-100" onclick="procesarPago()">Realizar pedido</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="carrito.js"></script>
    <script>
        function mostrarDireccion() {
            const select = document.getElementById('direccionSelect');
            const info = document.getElementById('direccionInfo');
            const detalle = document.getElementById('direccionDetalle');

            // Ocultamos inicialmente el contenedor de información
            info.style.display = 'none';

            // Verificamos si hay una selección válida
            if (select.value !== '') {
                const selectedOption = select.options[select.selectedIndex];
                const direccionDetalle = selectedOption.getAttribute('data-detalle');

                // Mostramos la información de la dirección seleccionada
                if (direccionDetalle) {
                    detalle.textContent = direccionDetalle;
                    info.style.display = 'block';
                }
            }
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
                        window.location.href = 'gracias.php?id_pedido='+data.id_pedido; // Redirigir a una página de agradecimiento
                    } else {
                        alert('Error: ' + (data.error || 'No se pudo realizar el pedido.'));
                    }
                })
                .catch(error => {
                    console.error('Error al procesar el pedido:', error);
                    alert('Ocurrió un error al realizar el pedido.', error);
                });
        };
    </script>
</body>