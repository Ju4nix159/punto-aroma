<?php

include './header.php';
include './config/sbd.php';
include './aside.php';
include './footer.php';
$id_pedido = $_GET['id_pedido'];

$sql_productos = $con->prepare("SELECT v.aroma, pp.id_pedido, pp.sku, p.nombre AS producto_nombre, pp.cantidad, pp.precio, pp.estado
    FROM productos_pedido pp
        JOIN productos p ON pp.id_producto = p.id_producto
        JOIN variantes v ON pp.sku = v.sku
    WHERE pp.id_pedido = :id_pedido;");
$sql_productos->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_productos->execute();
$detalles = $sql_productos->fetchAll(PDO::FETCH_ASSOC);

$sql_informacion_pedido = $con->prepare("SELECT p.estado_seña, p.envio, p.id_pedido, p.total, p.fecha, u.email, iu.nombre AS nombre_usuario, iu.apellido, iu.dni, iu.telefono, ep.nombre AS estado_pedido, ep.descripcion AS estado_pedido_descripcion, d.codigo_postal, d.provincia, d.localidad, d.calle, d.numero,d.barrio, l.nombre AS nombre_local FROM pedidos p 
LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
LEFT JOIN info_usuarios iu ON u.id_usuario = iu.id_usuario 
LEFT JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido 
LEFT JOIN domicilios d ON p.id_domicilio = d.id_domicilio 
LEFT JOIN locales l ON p.id_local = l.id_local 
WHERE p.id_pedido = :id_pedido;");
$sql_informacion_pedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_informacion_pedido->execute();
$pedido = $sql_informacion_pedido->fetch(PDO::FETCH_ASSOC);

$sql_estados_pedidos = $con->prepare("SELECT * FROM estados_pedidos;");
$sql_estados_pedidos->execute();
$estados_pedidos = $sql_estados_pedidos->fetchAll(PDO::FETCH_ASSOC);

$sql_pagos = $con->prepare("SELECT p.id_pago, p.id_pedido, p.id_metodo_pago, mp.nombre_metodo_pago, p.comprobante, p.monto, p.fecha, p.descripcion
FROM pagos p
JOIN metodos_pago mp ON p.id_metodo_pago = mp.id_metodo_pago
WHERE p.id_pedido = :id_pedido;");
$sql_pagos->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_pagos->execute();
$pagos = $sql_pagos->fetchAll(PDO::FETCH_ASSOC);

// Ordenar el array $detalles por el campo "producto_nombre"
usort($detalles, function ($a, $b) {
    return strcmp($a["producto_nombre"], $b["producto_nombre"]);
});

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <title>resuemn del pedido</title>
    <style>
        .botones-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .botones-container button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .comprobantes-container {
            margin-top: 20px;
        }
    </style>
</head>
</head>

<body>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Resumen del Pedido</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="./admin.php">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="./pedidos.php">Pedidos</a></li>
                                <li class="breadcrumb-item active">Resumen del Pedido</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Pedido: <?php echo $id_pedido ?></h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>Información del Cliente</h4>
                                            <p><strong>Nombre:</strong><?php echo $pedido["nombre_usuario"] . " " . $pedido["apellido"] ?></p>
                                            <p><strong>Email:</strong> <?php echo $pedido["email"] ?></p>
                                            <p><strong>Número de telefono</strong> <?php echo $pedido["telefono"] ?></p>

                                        </div>
                                        <div class="col-md-6">
                                            <h4>Detalles del Pedido</h4>
                                            <p><strong>Fecha:</strong> <?php echo $pedido["fecha"] ?></p>
                                            <?php if ($pedido["estado_seña"] == 1) { ?>
                                                <p><strong>Seña:</strong> PAGADA</p>
                                            <?php } else { ?>
                                                <p><strong>Seña:</strong> NO PAGADA</p>
                                            <?php } ?>
                                            <p><strong>Estado:</strong> <span id="estado-actual"><?php echo htmlspecialchars($pedido["estado_pedido"]); ?></span></p>
                                            <?php if ($pedido["nombre_local"] === null) { ?>
                                                <p><strong>Dirección de envío:</strong>
                                                    <?php echo  $pedido["provincia"] . ", " .
                                                        $pedido["localidad"] . ", " .
                                                        $pedido["barrio"] . ", " .
                                                        $pedido["calle"] . " " .
                                                        $pedido["numero"]
                                                    ?>
                                                </p>
                                            <?php } else { ?>
                                                <p><strong>Retiro en sucursal:</strong> <?php echo $pedido["nombre_local"]; ?></p>
                                            <?php } ?>

                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>fragancia</th>
                                                <th>cantidad</th>
                                                <th>precio</th>
                                                <th>subtotal</th>
                                                <th>acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productTableBody">
                                            <?php foreach ($detalles as $detalle) {
                                                $isUnavailable = $detalle["estado"] == 0; // Comprobar si está no disponible
                                            ?>
                                                <tr
                                                    data-id-pedido="<?php echo $id_pedido; ?>"
                                                    data-sku="<?php echo $detalle["sku"]; ?>"
                                                    class="<?php echo $isUnavailable ? 'text-muted line-through' : ''; ?>">
                                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                                        <?php echo $detalle["producto_nombre"]; ?>
                                                    </td>
                                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                                        <?php echo $detalle["aroma"]; ?>
                                                    </td>
                                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                                        <?php echo $detalle["cantidad"]; ?>
                                                    </td>
                                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                                        $<?php echo $detalle["precio"]; ?>
                                                    </td>
                                                    <?php $subtotal = $detalle["cantidad"] * $detalle["precio"]; ?>
                                                    <td style="<?php echo $isUnavailable ? 'text-decoration: line-through;' : ''; ?>">
                                                        $<?php echo $subtotal; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($isUnavailable) { ?>
                                                            <button type="button" class="btn btn-secondary btn-restore-product" onclick="calcularTotal()">Restaurar</button>
                                                        <?php } else { ?>
                                                            <button type="button" class="btn btn-danger btn-delete-product" onclick="calcularTotal()">Eliminar</button>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <!-- calcular el envio -->
                                            <tr>
                                                <td>Costo de Envío</td>
                                                <td>
                                                    <?php if ($pedido["nombre_local"] === null) { ?>
                                                        <input
                                                            type="number"
                                                            id="costoEnvio"
                                                            class="form-control"
                                                            value="<?php echo $pedido["envio"] ?>"
                                                            oninput="calcularTotal()"> <!-- Llamar a la función cuando el valor cambie -->
                                                    <?php } else { ?>
                                                        <input
                                                            type="number"
                                                            id="costoEnvio"
                                                            class="form-control"
                                                            value="0"
                                                            readonly> <!-- No permitir cambios si se retira en sucursal -->
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="text-right">
                                        <h4>Total: $<span id="orderTotal"></span></h4>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a href="./pedidos.php" class="btn btn-warning ">Volver</a>
                                    <button type="button" class="btn btn-danger" id="cancelChangesBtn">Cancelar Cambios</button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCambiarEstado">
                                        Cambiar Estado </button>
                                    <button type="button" class="btn btn-primary float-right ml-2" id="confirmOrderBtn">Confirmar Pedido</button>
                                    <button type="button" class="btn btn-success float-right" id="confirmChangesBtn">Confirmar Cambios</button>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <div class="col-md-4">
                            <!-- Contenedor de botones -->
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Acciones Rápidas</h3>
                                </div>
                                <div class="card-body">
                                    <div class="botones-container">
                                        <button class="btn btn-success" onclick="cambiarEstadoSeña(<?php echo $id_pedido ?>,1)">estado seña pagado</button>
                                        <button class="btn btn-danger" onclick="cambiarEstadoSeña(<?php echo $id_pedido ?>,0)">estado seña no pagado</button>
                                        <button onclick="printOrder(<?php echo $pedido['id_pedido'] ?>)" type="button" class="btn bg-green btn-flat margin"><i class="fas fa-print"></i></button>
                                        <button class="btn btn-primary">Botón 4</button>
                                        <button class="btn btn-primary">Botón 5</button>
                                        <button class="btn btn-primary">Botón 6</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php foreach ($pagos as $pago) { ?>
                            <?php if ($pago['id_metodo_pago'] == 4 || $pago['id_metodo_pago'] == 1) { ?>
                                <div class="col-md-4">
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Información de Pago</h3>
                                        </div>
                                        <div class="card-body">
                                            <strong><i class="fas fa-money-bill mr-1"></i> Método de Pago:</strong>
                                            <p class="text-muted"><?php echo $pago['nombre_metodo_pago']; ?></p>
                                            <hr>
                                            <strong><i class="fas fa-calendar-alt mr-1"></i> Fecha de Pago:</strong>
                                            <p class="text-muted" id="fechaPago"><?php echo $pago['fecha']; ?></p>
                                            <hr>
                                            <strong><i class="fas fa-calendar-alt mr-1"></i> Monto pagado:</strong>
                                            <p class="text-muted" id="monto"><?php echo $pago['monto']; ?></p>
                                            <hr>
                                            <strong><i class="fas fa-file-invoice mr-1"></i> Comprobante de Pago:</strong>
                                            <div class="mt-2">
                                                <img src="../assets/comprobantes/<?php echo $pago['id_pedido']; ?>/<?php echo $pago['comprobante']; ?>" alt="Comprobante de Transferencia" class="img-fluid img-thumbnail" style="max-width: 100%;" id="imagenComprobante">
                                            </div>
                                            <button class="btn btn-sm btn-primary mt-2" onclick="mostrarComprobante()">
                                                <i class="fas fa-eye"></i> Ver Comprobante
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-labelledby="modalCambiarEstadoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCambiarEstadoLabel">Cambiar Estado del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Estado Actual -->
                    <p><strong>Estado Actual:</strong> <span id="estado-actual"><?= htmlspecialchars($pedido['estado_pedido']); ?></span></p>

                    <!-- Formulario -->
                    <form id="formCambiarEstado">
                        <div class="mb-3">
                            <label for="nuevoEstado" class="form-label">Seleccionar Nuevo Estado</label>
                            <select class="form-select" id="nuevoEstado" name="nuevo_estado" required>
                                <option value="" selected disabled>Selecciona un estado</option>
                                <?php foreach ($estados_pedidos as $estado): ?>
                                    <option value="<?= htmlspecialchars($estado['id_estado_pedido']); ?>">
                                        <?= htmlspecialchars($estado['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="id_pedido" value="<?= htmlspecialchars($pedido['id_pedido']); ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="guardarCambioEstado" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
    <script src="imprimir.js"></script>
    <script>
        document.getElementById('guardarCambioEstado').addEventListener('click', function() {
            const form = document.getElementById('formCambiarEstado');
            const formData = new FormData(form);

            fetch('actualizar_estado.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Estado actualizado correctamente.');

                        // Actualizar el contenido del estado en la página
                        document.getElementById('estado-actual').textContent = data.nuevo_estado;

                        // Cerrar el modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCambiarEstado'));
                        modal.hide();
                    } else {
                        alert('Error al actualizar el estado: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al intentar actualizar el estado.');
                });
        });


        document.addEventListener('DOMContentLoaded', function() {
            const productTableBody = document.getElementById('productTableBody');

            // Escucha los clics en los botones de eliminar/restaurar
            productTableBody.addEventListener('click', function(event) {
                const button = event.target;
                if (
                    button.classList.contains('btn-delete-product') ||
                    button.classList.contains('btn-restore-product')
                ) {
                    const row = button.closest('tr');
                    const isRestore = button.classList.contains('btn-restore-product');

                    // Cambiar estado visual del producto
                    if (isRestore) {
                        button.textContent = 'Eliminar';
                        button.classList.remove('btn-restore-product', 'btn-secondary');
                        button.classList.add('btn-delete-product', 'btn-danger');
                        row.classList.remove('text-muted', 'line-through');
                        row.querySelectorAll('td').forEach((td) => {
                            td.style.textDecoration = 'none';
                        });
                        calcularTotal();

                    } else {
                        button.textContent = 'Restaurar';
                        button.classList.remove('btn-delete-product', 'btn-danger');
                        button.classList.add('btn-restore-product', 'btn-secondary');
                        row.classList.add('text-muted', 'line-through');
                        row.querySelectorAll('td').forEach((td) => {
                            td.style.textDecoration = 'line-through';
                        });
                        calcularTotal();
                    }
                }
            });

            // Botón de "Cancelar Cambios"
            document.getElementById('cancelChangesBtn').addEventListener('click', function() {
                const idPedido = <?php echo json_encode($id_pedido); ?>;

                // Obtener datos actuales de la base de datos mediante AJAX
                fetch(`./obtener_estado_pedido.php?id_pedido=${idPedido}`)
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            // Actualizar tabla con datos desde la base de datos
                            data.detalles.forEach((detalle) => {
                                const row = productTableBody.querySelector(`tr[data-sku="${detalle.sku}"]`);
                                if (row) {
                                    const button = row.querySelector('button');
                                    if (detalle.estado === 1) {
                                        // Restaurar visualmente como disponible
                                        button.textContent = 'Eliminar';
                                        button.classList.remove('btn-restore-product', 'btn-secondary');
                                        button.classList.add('btn-delete-product', 'btn-danger');
                                        row.classList.remove('text-muted', 'line-through');
                                        row.querySelectorAll('td').forEach((td) => {
                                            td.style.textDecoration = 'none';
                                        });
                                    } else {
                                        // Restaurar visualmente como no disponible
                                        button.textContent = 'Restaurar';
                                        button.classList.remove('btn-delete-product', 'btn-danger');
                                        button.classList.add('btn-restore-product', 'btn-secondary');
                                        row.classList.add('text-muted', 'line-through');
                                        row.querySelectorAll('td').forEach((td) => {
                                            td.style.textDecoration = 'line-through';
                                        });
                                    }
                                }
                            });
                        } else {
                            alert('Error al obtener datos del pedido.');
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        alert('No se pudo cargar los datos del pedido.');
                    });
            });

            // Botón de "Confirmar Cambios"
            document.getElementById('confirmChangesBtn').addEventListener('click', function() {
                const idPedido = <?php echo json_encode($id_pedido); ?>;
                const cambios = [];


                // Recolectar el estado actual de los productos visibles en la tabla
                productTableBody.querySelectorAll('tr').forEach((row) => {
                    const sku = row.dataset.sku;
                    const estado = row.classList.contains('line-through') ? 0 : 1; // Si está tachado, estado = 0
                    cambios.push({
                        sku,
                        estado
                    });
                });
                const envio = document.getElementById('costoEnvio').value;

                // Enviar datos al servidor para actualizar en la base de datos
                fetch('./confirmar_cambios.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id_pedido: idPedido,
                            cambios,
                            envio: envio

                        }),
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert('Cambios confirmados con éxito.');
                        } else {
                            alert(data.error || 'Error al confirmar los cambios.');
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        alert('No se pudo confirmar los cambios.');
                    });
            });
        });

        document.getElementById('confirmOrderBtn').addEventListener('click', function() {
            const idPedido = <?php echo json_encode($id_pedido); ?>;
            const productos = [];

            // Recolectar el estado actual de los productos visibles en la tabla
            productTableBody.querySelectorAll('tr').forEach((row) => {
            const sku = row.dataset.sku;
            const estado = row.classList.contains('line-through') ? 0 : 1; // Si está tachado, estado = 0
            productos.push({
                sku,
                estado
            });
            });

            // Determinar el estado del pedido
            const hayCambiados = productos.some((producto) => producto.estado === 0);
            const nuevoEstado = hayCambiados ? 'Cambiado' : 'Procesado'; // Decidir el estado
            const envio = parseFloat(document.getElementById('costoEnvio').value);

            // Comprobar si es envío a domicilio y el costo de envío es distinto de 0
            const esEnvioDomicilio = <?php echo json_encode($pedido["nombre_local"] === null); ?>;
            if (esEnvioDomicilio && envio === 0) {
            alert('El costo de envío no puede ser 0 para envíos a domicilio.');
            return;
            }

            // Enviar datos al servidor
            fetch('./confirmar_pedido.php', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                id_pedido: idPedido,
                productos,
                nuevo_estado: nuevoEstado,
                envio: envio
                }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                alert(`El pedido ha sido confirmado con estado: ${nuevoEstado}`);
                // Opcional: Redirigir o actualizar la página
                location.reload();
                location.href = './pedidos.php';
                } else {
                alert(data.error || 'Hubo un error al confirmar el pedido.');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('No se pudo confirmar el pedido.');
            });
        });

        function mostrarComprobante() {
            Swal.fire({
                title: 'Comprobante de Transferencia',
                imageUrl: document.getElementById('imagenComprobante').src,
                imageWidth: 600,
                imageHeight: 500,
                imageAlt: 'Comprobante de Transferencia',
                confirmButtonText: 'Cerrar'
            })
        }

        function calcularTotal() {
            let total = 0;

            // Obtener todas las filas del cuerpo de la tabla
            const filas = document.querySelectorAll("#productTableBody tr");

            filas.forEach(fila => {
                // Comprobar si la fila está tachada (producto eliminado)
                const isUnavailable = fila.classList.contains("text-muted");

                // Solo sumar subtotales de filas que no estén eliminadas
                if (!isUnavailable) {
                    // Seleccionar la columna de subtotal
                    const subtotalElement = fila.querySelector("td:nth-child(5)");

                    if (subtotalElement) {
                        // Extraer el texto, eliminar el símbolo de $ y convertir a número
                        const subtotalText = subtotalElement.innerText.trim();
                        const subtotal = parseFloat(subtotalText.replace("$", "").replace(",", ""));

                        // Validar que el subtotal sea un número antes de sumarlo
                        if (!isNaN(subtotal)) {
                            total += subtotal;
                        }
                    }
                }
            });

            const costoEnvioInput = document.getElementById("costoEnvio");
            if (costoEnvioInput) {
                const costoEnvio = parseFloat(costoEnvioInput.value);
                if (!isNaN(costoEnvio)) {
                    total += costoEnvio; // Agregar el costo de envío al total
                }
            }
            // Actualizar el total en el elemento con id="orderTotal"
            const totalElement = document.getElementById("orderTotal");
            if (totalElement) {
                totalElement.innerText = total.toFixed(2);
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            calcularTotal(); // Llamar a la función para calcular el total al cargar la página
        });

        function cambiarEstadoSeña(pedidoId, estado) {
            // Crear un objeto FormData para enviar los datos
            var formData = new FormData();
            formData.append('pedido_id', pedidoId);
            formData.append('estado_sena', estado);

            // Enviar la solicitud AJAX
            fetch('cambiar_estado_sena.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Si la respuesta es exitosa, actualizar la página
                        location.reload();
                    } else {
                        // Si hay un error, mostrar un mensaje
                        alert('Error al cambiar el estado de la seña');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    
    </script>

</body>

</html>