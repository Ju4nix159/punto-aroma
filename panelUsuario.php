<?php
include 'header.php';

include 'admin/config/sbd.php';

// Iniciamos la verificación del usuario en sesión
if (isset($_SESSION["usuario"])) {
    $id_usuario = $_SESSION["usuario"];
    $sql_usuario = $con->prepare("  SELECT iu.nombre AS nombre_usuario, iu.apellido, iu.dni, iu.fecha_nacimiento, iu.telefono, s.nombre AS sexo
                                    FROM info_usuarios iu
                                    JOIN sexos s ON iu.id_sexo = s.id_sexo
                                    WHERE iu.id_usuario = :id_usuario;");
    $sql_usuario->bindParam(":id_usuario", $id_usuario);
    $sql_usuario->execute();
    $usuario = $sql_usuario->fetch(PDO::FETCH_ASSOC);

    // Bandera para mostrar el div de perfil incompleto
    $mostrar_alerta_perfil_incompleto = false;

    // Verificamos si no se encontraron datos del usuario
    if ($usuario === false) {
        // Si no hay resultados, activamos la bandera de alerta y asignamos "-" a los campos
        $mostrar_alerta_perfil_incompleto = true;
        $nombre_usuario = "";
        $apellido = "";
        $dni = "";
        $fecha_nacimiento = "";
        $telefono = "";
        $sexo = "";
    } else {
        // Si hay resultados, asignamos los valores reales
        $nombre_usuario = $usuario["nombre_usuario"];
        $apellido = $usuario["apellido"];
        $dni = $usuario["dni"];
        $fecha_nacimiento = $usuario["fecha_nacimiento"];
        $telefono = $usuario["telefono"];
        $sexo = $usuario["sexo"];
    }

    //sexos 
    $sql_sexos = $con->prepare("SELECT id_sexo, nombre FROM sexos;");
    $sql_sexos->execute();
    $sexos = $sql_sexos->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Punto Aroma</title>
    <style>
        .user-info-card,
        .order-card,
        .address-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .user-info-item {
            margin-bottom: 15px;
        }

        .user-info-label {
            font-weight: bold;
            color: var(--secondary-color);
        }

        .user-info-value {
            color: #333;
        }

        .incomplete-profile-alert {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .order-status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 15px;
            color: white;
        }

        .status-procesando {
            background-color: #ffc107;
        }

        .status-en-camino {
            background-color: #17a2b8;
        }

        .status-entregado {
            background-color: #28a745;
        }

        .status-cancelado {
            background-color: #dc3545;
        }

        .address-type {
            font-weight: bold;
            color: var(--primary-color);
        }

        .address-main {
            border: 2px solid var(--primary-color);
        }

        #menu-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <main class="py-5">
        <div class="container">
            <h1 class="mb-4 text-primary-custom">Panel de Usuario</h1>
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="list-group">
                        <a href="#perfil" class="list-group-item list-group-item-action active" data-bs-toggle="list">Perfil</a>
                        <a href="#pedidos" class="list-group-item list-group-item-action" data-bs-toggle="list">Historial de Pedidos</a>
                        <a href="#domicilios" class="list-group-item list-group-item-action" data-bs-toggle="list">Domicilios de Entrega</a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="perfil">
                            <h2 class="mb-4">Mi Perfil</h2>

                            <?php if ($mostrar_alerta_perfil_incompleto) { ?>
                                <div id="incomplete-profile-alert" class="incomplete-profile-alert">
                                    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Perfil Incompleto</h4>
                                    <p>Parece que aún no has completado toda tu información personal. Completa tu perfil para aprovechar al máximo tu experiencia en Punto Aroma.</p>
                                    <hr>
                                    <p class="mb-0">
                                        <button id="btn-completar-perfil" class="btn btn-primary-custom" onclick="InformacionPersonal()">Completar Perfil</button>
                                    </p>
                                </div>
                            <?php } ?>

                            <div id="info-usuario" class="user-info-card">
                                <div class="row">
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Nombre:</div>
                                        <div class="user-info-value" id="nombre-display"><?php echo $nombre_usuario ?></div>
                                    </div>
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Apellido:</div>
                                        <div class="user-info-value" id="apellido-display"><?php echo $apellido ?></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">DNI:</div>
                                        <div class="user-info-value" id="dni-display"><?php echo $dni ?></div>
                                    </div>
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Edad:</div>
                                        <div class="user-info-value"><span id="edad-display"><?php echo $fecha_nacimiento ?></span></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Teléfono:</div>
                                        <div class="user-info-value" id="telefono-display"><?php echo $telefono ?></div>
                                    </div>
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Sexo:</div>
                                        <div class="user-info-value" id="sexo-display"><?php echo $sexo ?></div>
                                    </div>
                                </div>
                                <?php if (!$mostrar_alerta_perfil_incompleto) { ?>
                                    <div class="text-center mt-4">
                                        <button id="btn-editar" class="btn btn-primary-custom" onclick="InformacionPersonal()">Editar Información</button>
                                    </div>
                                <?php } ?>
                            </div>
                            <form class="hidden" action="./admin/procesarsbd.php" method="POST" id="form-editar">
                                <input class="hidden" value="<?php echo $id_usuario ?>" type="text" name="id_usuario">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input placeholder="Ingrese su nombre" type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre_usuario ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input placeholder="Ingrese su apellido" type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $apellido ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="dni" class="form-label">DNI</label>
                                        <input placeholder="Ingrese su dni" type="text" class="form-control" id="dni" name="dni" value="<?php echo $dni ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha-nacimiento" class="form-label">Fecha de Nacimiento</label>
                                        <input  type="date" class="form-control" id="fecha-nacimiento" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input placeholder="Ingrese su telefono" type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo $telefono ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sexo" class="form-label">Sexo</label>
                                        <select class="form-select" id="sexo" name="sexo" required>
                                            <option value="" disabled selected>Seleccionar</option>
                                            <?php foreach ($sexos as $sexo_option) { ?>
                                                <option value="<?php echo $sexo_option['id_sexo']; ?>" <?php echo ($sexo_option['nombre'] == $sexo) ? 'selected' : ''; ?>>
                                                    <?php echo $sexo_option['nombre']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary-custom" name="actualizarInfo">Guardar Cambios</button>
                                    <button type="button" id="btn-cancelar" class="btn btn-secondary ms-2" onclick="btnCancelar()">Cancelar</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="pedidos">
                            <h2 class="mb-4">Historial de Pedidos</h2>
                            <div class="mb-3">
                                <label for="ordenar-pedidos" class="form-label">Ordenar por estado:</label>
                                <select id="ordenar-pedidos" class="form-select">
                                    <option value="todos">Todos</option>
                                    <option value="procesando">Procesando</option>
                                    <option value="en-camino">En Camino</option>
                                    <option value="entregado">Entregado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                            <div id="pedidos-container">
                                <?php foreach ($pedidos as $pedido) { ?>
                                    <div class="order-card">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Pedido: #<?php echo $pedido["id_pedido"] ?> </h5>
                                            <span class="order-status status-<?php echo $pedido["estado_pedido"] ?>"><?php echo $pedido["estado_pedido"] ?></span>
                                        </div>
                                        <p>Fecha: <?php echo $pedido["fecha"] ?></p>
                                        <p>Total: <?php echo $pedido["total"] ?></p>
                                        <button class="btn btn-primary-custom btn-sm me-2 btn-ver-detalle"
                                            data-id="<?php echo $pedido['id_pedido']; ?>">
                                            Ver Detalle
                                        </button>
                                        <?php if (in_array($pedido["estado_pedido"], ["pendiente", "procesado", "cambiado"])) { ?>
                                            <button class="btn btn-danger btn-sm btn-cancelar-pedido"
                                                data-id="<?php echo $pedido['id_pedido']; ?>"
                                                onclick="cancelarPedido(<?php echo $pedido['id_pedido']; ?>)">
                                                Cancelar Pedido
                                            </button>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="domicilios">
                            <h2 class="mb-4">Domicilios de Entrega</h2>
                            <div id="domicilios-container">
                                <!-- Los domicilios se cargarán aquí dinámicamente -->
                            </div>
                            <button id="btn-agregar-domicilio" class="btn btn-primary-custom mt-3">Agregar Nuevo Domicilio</button>

                            <!-- Modal para agregar/editar domicilio -->
                            <div class="modal fade" id="domicilioModal" tabindex="-1" aria-labelledby="domicilioModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="domicilioModalLabel">Agregar Nuevo Domicilio</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="form-domicilio">
                                                <input type="hidden" id="domicilio-id">
                                                <div class="mb-3">
                                                    <label for="domicilio-tipo" class="form-label">Tipo de Domicilio</label>
                                                    <select class="form-select" id="domicilio-tipo" required>
                                                        <option value="">Seleccionar</option>
                                                        <option value="Casa">Casa</option>
                                                        <option value="Trabajo">Trabajo</option>
                                                        <option value="Otro">Otro</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="domicilio-calle"
                                                        class="form-label">Calle y Número</label>
                                                    <input type="text" class="form-control" id="domicilio-calle" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="domicilio-ciudad" class="form-label">Ciudad</label>
                                                    <input type="text" class="form-control" id="domicilio-ciudad" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="domicilio-codigo-postal" class="form-label">Código Postal</label>
                                                    <input type="text" class="form-control" id="domicilio-codigo-postal" required>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="button" class="btn btn-primary-custom" id="btn-guardar-domicilio">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para detalles del pedido -->

    <script src="app.js"></script>
    <script>
        function cancelarPedido(id_pedido) {
            fetch('./admin/procesarsbd.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    // Cambiamos el body para enviar correctamente los datos
                    body: new URLSearchParams({
                        cancelarPedido: 'true',
                        id_pedido: id_pedido
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pedido cancelado exitosamente.');
                        // Actualizar el estado en la interfaz
                        const card = document.querySelector(`.btn-cancelar-pedido[data-id="${id_pedido}"]`).closest('.order-card');
                        const statusElement = card.querySelector('.order-status');
                        statusElement.classList.replace('status-pendiente', 'status-cancelado');
                        statusElement.textContent = 'Cancelado';
                        card.querySelector(`.btn-cancelar-pedido[data-id="${id_pedido}"]`).remove();
                    } else {
                        alert('Hubo un error al cancelar el pedido. Inténtalo nuevamente. 1');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al cancelar el pedido. Inténtalo nuevamente.2');
                });
        }
        document.querySelectorAll('.btn-ver-detalle').forEach(button => {
            button.addEventListener('click', function() {
                const id_pedido = this.getAttribute('data-id');
                obtenerDetallePedido(id_pedido);
            });
        });

        function obtenerDetallePedido(id_pedido) {
            fetch('./admin/procesarsbd.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'obtenerDetalle',
                        id_pedido: id_pedido
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Llenar el modal con los datos del pedido
                        document.getElementById('pedidoDetalleModalLabel').textContent = `Detalle del Pedido #${data.pedido.id_pedido}`;
                        document.getElementById('pedidoDetalleModalBody').innerHTML = `
                <p><strong>Fecha:</strong> ${data.pedido.fecha}</p>
                <p><strong>Estado:</strong> ${data.pedido.estado}</p>
                <p><strong>Dirección de Envío:</strong> ${data.pedido.direccionEnvio}</p>
                <h6 class="mt-4">Productos:</h6>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.pedido.productos.map(producto => `
                            <tr>
                                <td>${producto.nombre}</td>
                                <td>${producto.cantidad}</td>
                                <td>$${producto.precio_unitario}</td>
                                <td>$${producto.subtotal}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th>$${data.pedido.total}</th>
                        </tr>
                    </tfoot>
                </table>
            `;
                        // Mostrar el modal
                        const pedidoDetalleModal = new bootstrap.Modal(document.getElementById('pedidoDetalleModal'));
                        pedidoDetalleModal.show();
                    } else {
                        alert('No se pudo obtener el detalle del pedido.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al obtener el detalle del pedido.');
                });
        }




        /* function renderizarDomicilios() {
            domiciliosContainer.innerHTML = '';
            domicilios.forEach(domicilio => {
                const domicilioCard = document.createElement('div');
                domicilioCard.className = `address-card ${domicilio.principal ? 'address-main' : ''}`;
                domicilioCard.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="address-type">${domicilio.tipo}</span>
                            ${domicilio.principal ? '<span class="badge bg-primary">Principal</span>' : ''}
                        </div>
                        <p>${domicilio.calle}</p>
                        <p>${domicilio.ciudad}, ${domicilio.codigoPostal}</p>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary me-2 btn-editar-domicilio" data-id="${domicilio.id}">Editar</button>
                            ${!domicilio.principal ? `<button class="btn btn-sm btn-outline-success me-2 btn-principal-domicilio" data-id="${domicilio.id}">Hacer Principal</button>` : ''}
                            ${!domicilio.principal ? `<button class="btn btn-sm btn-outline-danger btn-eliminar-domicilio" data-id="${domicilio.id}">Eliminar</button>` : ''}
                        </div>
                    `;
                domiciliosContainer.appendChild(domicilioCard);
            });



            function mostrarDetallePedido(id) {
                const pedido = pedidos.find(p => p.id == id);
                if (pedido) {
                    pedidoDetalleModalBody.innerHTML = `
                        <h6>Pedido #${pedido.id}</h6>
                        <p><strong>Fecha:</strong> ${pedido.fecha}</p>
                        <p><strong>Estado:</strong> ${pedido.estado}</p>
                        <p><strong>Dirección de Envío:</strong> ${pedido.direccionEnvio}</p>
                        <h6 class="mt-4">Productos:</h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${pedido.productos.map(producto => `
                                    <tr>
                                        <td>${producto.nombre}</td>
                                        <td>${producto.cantidad}</td>
                                        <td>$${producto.precio.toFixed(2)}</td>
                                        <td>$${(producto.cantidad * producto.precio).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>$${pedido.total.toFixed(2)}</th>
                                </tr>
                            </tfoot>
                        </table>
                    `;
                }
            } */
    </script>
</body>

</html>