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

$sql_informacion_pedido = $con->prepare("SELECT p.id_pedido, p.total, p.fecha, u.email, iu.nombre AS nombre_usuario, iu.apellido, iu.dni, iu.telefono, ep.nombre AS estado_pedido, ep.descripcion AS estado_pedido_descripcion, d.codigo_postal, d.provincia, d.localidad, d.calle, d.numero,d.barrio FROM pedidos p 
LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
LEFT JOIN info_usuarios iu ON u.id_usuario = iu.id_usuario 
LEFT JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido 
LEFT JOIN domicilios d ON p.id_domicilio = d.id_domicilio 
WHERE p.id_pedido = :id_pedido;");
$sql_informacion_pedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_informacion_pedido->execute();
$pedido = $sql_informacion_pedido->fetch(PDO::FETCH_ASSOC);



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>resuemn del pedido</title>
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
                                            <p><strong>Dirección de envío:</strong>
                                                <?php echo  $pedido["provincia"] . ", " .
                                                    $pedido["localidad"] . ", " .
                                                    $pedido["barrio"] . ", " .
                                                    $pedido["calle"] . " " .
                                                    $pedido["numero"]
                                                ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Detalles del Pedido</h4>
                                            <p><strong>Número de Pedido:</strong> <?php echo $id_pedido ?></p>
                                            <p><strong>Fecha:</strong> <?php echo $pedido["fecha"] ?></p>
                                            <p><strong>Estado:</strong> <?php echo $pedido["estado_pedido"] ?></p>
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
                                                            <button type="button" class="btn btn-secondary btn-restore-product">Restaurar</button>
                                                        <?php } else { ?>
                                                            <button type="button" class="btn btn-danger btn-delete-product">Eliminar</button>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>


                                    </table>
                                    <div class="text-right">
                                        <?php
                                        $total = 0;
                                        foreach ($detalles as $detalle) {
                                            if ($detalle["estado"] == 1){
                                                $total += $detalle["cantidad"] * $detalle["precio"];
                                            } 
                                        }
                                        ?>
                                        <h4>Total: $<span id="orderTotal"><?php echo $total ?></span></h4>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a href="./pedidos.php" class="btn btn-warning ">Volver</a>
                                    <button type="button" class="btn btn-danger" id="cancelChangesBtn">Cancelar Cambios</button>
                                    <button type="button" class="btn btn-primary float-right ml-2" id="confirmOrderBtn">Confirmar Pedido</button>
                                    <button type="button" class="btn btn-success float-right" id="confirmChangesBtn">Confirmar Cambios</button>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <?php if (true) { ?>
                            <div class="col-md-4">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Información de Pago</h3>
                                    </div>
                                    <div class="card-body">
                                        <strong><i class="fas fa-money-bill mr-1"></i> Método de Pago:</strong>
                                        <p class="text-muted"><?php  ?></p>
                                        <hr>
                                        <strong><i class="fas fa-calendar-alt mr-1"></i> Fecha de Pago:</strong>
                                        <p class="text-muted" id="fechaPago"><?php  ?></p>
                                        <hr>
                                        <strong><i class="fas fa-file-invoice mr-1"></i> Comprobante de Pago:</strong>
                                        <div class="mt-2">
                                            <img src="../assets/comprobantes/<?php  ?>/<?php  ?>" alt="Comprobante de Transferencia" class="img-fluid img-thumbnail" style="max-width: 100%;" id="imagenComprobante">
                                        </div>
                                        <button class="btn btn-sm btn-primary mt-2" onclick="mostrarComprobante()">
                                            <i class="fas fa-eye"></i> Ver Comprobante
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (true) { ?>
                            <div class="col-md-4">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Información de Pago</h3>
                                    </div>
                                    <div class="card-body">
                                        <strong><i class="fas fa-money-bill mr-1"></i> Método de Pago:</strong>
                                        <p class="text-muted"><?php  ?></p>
                                        <hr>
                                        <strong><i class="fas fa-calendar-alt mr-1"></i> Fecha de Pago:</strong>
                                        <p class="text-muted" id="fechaPago"><?php  ?></p>
                                        <hr>
                                        <strong><i class="fas fa-file-invoice mr-1"></i> Comprobante de Pago:</strong>
                                        <div class="mt-2">
                                            <img src="../assets/comprobantes/<?php  ?>/<?php  ?>" alt="Comprobante de Transferencia" class="img-fluid img-thumbnail" style="max-width: 100%;" id="imagenComprobante">
                                        </div>
                                        <button class="btn btn-sm btn-primary mt-2" onclick="mostrarComprobante()">
                                            <i class="fas fa-eye"></i> Ver Comprobante
                                        </button>
                                    </div>
                                </div>
                            </div>
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
    <script>
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
                        
                    } else {
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

                // Enviar datos al servidor para actualizar en la base de datos
                fetch('./confirmar_cambios.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id_pedido: idPedido,
                            cambios
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

            // Enviar datos al servidor
            fetch('./confirmar_pedido.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_pedido: idPedido,
                        productos,
                        nuevo_estado: nuevoEstado
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
    </script>

</body>

</html>