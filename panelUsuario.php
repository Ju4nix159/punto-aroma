<?php
include 'header.php';

include 'admin/config/sbd.php';

// Iniciamos la verificación del usuario en sesión
if (isset($_SESSION["usuario"])) {
    $id_usuario = $_SESSION["usuario"];
    $sql_usuario = $con->prepare("  SELECT iu.nombre AS nombre_usuario, iu.apellido, iu.dni, iu.fecha_nacimiento, iu.telefono
                                    FROM info_usuarios iu
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
    }

    //sexos 
    $sql_sexos = $con->prepare("SELECT id_sexo, nombre FROM sexos;");
    $sql_sexos->execute();
    $sexos = $sql_sexos->fetchAll(PDO::FETCH_ASSOC);


    $sql_pedido = $con->prepare("   SELECT p.id_pedido, p.total, p.fecha, ep.nombre AS estado_pedido, ep.id_estado_pedido
                                    FROM pedidos p
                                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                                    JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido
                                    WHERE u.id_usuario = :id_usuario;");
    $sql_pedido->bindParam(":id_usuario", $id_usuario);
    $sql_pedido->execute();
    $pedidos = $sql_pedido->fetchAll(PDO::FETCH_ASSOC);

    $sql_estados = $con->prepare("SELECT id_estado_pedido, nombre FROM estados_pedidos;");
    $sql_estados->execute();
    $estados = $sql_estados->fetchAll(PDO::FETCH_ASSOC);


    $sql_domicilios = $con->prepare("SELECT d.*, ud.tipo_domicilio, ud.principal
FROM domicilios d
    JOIN usuario_domicilios ud ON d.id_domicilio = ud.id_domicilio
    JOIN usuarios i ON ud.id_usuario = i.id_usuario
WHERE i.id_usuario = :id_usuario AND ud.estado = 1 ");
    $sql_domicilios->bindParam(":id_usuario", $id_usuario);
    $sql_domicilios->execute();
    $domicilios = $sql_domicilios->fetchAll(PDO::FETCH_ASSOC);
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

        .status-procesado {
            background-color: #17a2b8;
        }

        .status-pendiente {
            background-color: #ffc107;
        }

        .status-cambiado {
            background-color: #f57c00;
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

        .status-pagado {
            background-color: #28a745;
        }

        .status-señado{
            background-color: #ffc107;
        }
        .status-noSeaño{
            background-color: #f57c00;
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

        .text-primary-custom {
            color: var(--primary-color);
        }

        .text-secondary-custom {
            color: var(--secondary-color);
        }

        .modal-header {
            background-color: var(--secondary-color);
            color: white;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(131, 175, 55, 0.25);
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
                                        <input type="date" class="form-control" id="fecha-nacimiento" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input placeholder="Ingrese su telefono" type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo $telefono ?>" required>
                                    </div>
                                    
                                </div>
                                <div class="text-center mt-4">
                                    <button type="button" class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#confirmUpdateInfoModal">Guardar cambios</button>
                                    <button type="button" id="btn-cancelar" class="btn btn-secondary ms-2" onclick="btnCancelar()">Cancelar</button>
                                </div>
                                <div id="confirmUpdateInfoModal" class="modal fade">
                                    <div class="modal-dialog modal-confirm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="icon-box">
                                                    <i class="bi bi-person-check"></i>
                                                </div>
                                                <h4 class="modal-title">Actualizar Información</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-center">¿Estás seguro de que deseas actualizar tu información personal?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" name="actualizarInfo" class="btn btn-confirm" id="updateInfoConfirm">Sí, actualizar información</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="pedidos">
                            <h2 class="mb-4">Historial de Pedidos</h2>
                            <div class="mb-3">
                                <label for="ordenar-pedidos" class="form-label">Ordenar por estado:</label>
                                <select id="ordenar-pedidos" class="form-select">
                                    <option value="todos">Todos</option>
                                    <?php foreach ($estados as $estado) { ?>
                                        <option value="<?php echo $estado["id_estado_pedido"] ?>"><?php echo $estado["nombre"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div id="pedidos-container">
                                <?php foreach ($pedidos as $pedido) { ?>
                                    <div class="order-card">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Pedido: #<?php echo $pedido["id_pedido"] ?> </h5>
                                            <span class="order-status status-<?php echo $pedido["estado_pedido"] ?>"
                                                data-estado-id="<?php echo $pedido["id_estado_pedido"] ?>">
                                                <?php echo $pedido["estado_pedido"] ?>
                                            </span>
                                        </div>
                                        <p>Fecha: <?php echo $pedido["fecha"] ?></p>
                                        <p>Total: <?php echo $pedido["total"] ?></p>
                                        <button class="btn btn-primary-custom btn-sm me-2 btn-ver-detalle"
                                            data-id-pedido="<?php echo $pedido["id_pedido"] ?>"
                                            onclick="verDetallePedido(<?php echo $pedido['id_pedido']; ?>)">
                                            Ver Detalle
                                        </button>

                                        <?php if (in_array($pedido["estado_pedido"], ["pendiente", "procesado", "cambiado"])) { ?>
                                            <button class="btn btn-danger btn-sm btn-cancelar-pedido"
                                                data-id="<?php echo $pedido['id_pedido']; ?>"
                                                onclick="cancelarPedido(<?php echo $pedido['id_pedido']; ?>)">
                                                Cancelar Pedido
                                            </button>
                                        <?php } ?>

                                        <!-- Botón Pagar -->
                                        <?php if (in_array($pedido["estado_pedido"], ["procesado", "cambiado"])) { ?>
                                            <a href="pago_total.php?id_pedido=<?php echo $pedido['id_pedido']; ?>"
                                                class="btn btn-success btn-sm btn-pagar">
                                                Pagar
                                            </a>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="domicilios">
                            <h2 class="mb-4">Domicilios de Entrega</h2>
                            <?php if (empty($domicilios)) { ?>
                                <div id="incomplete-profile-alert" class="incomplete-profile-alert">
                                    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Sin domicilios</h4>
                                    <p>Parece que aún no has ingresado un domicilio. Agrega un domicilio valido y empieza a comprar.</p>
                                    <hr>
                                    <p class="mb-0">
                                        <button id="btn-agregar-domicilio" class="btn btn-primary-custom mt-3">Agregar Nuevo Domicilio</button>
                                    </p>
                                </div>
                            <?php } ?>
                            <?php foreach ($domicilios as $domicilio) {
                                $id_domicilio = $domicilio["id_domicilio"] ?>
                                <div id="domicilios-container">
                                    <div class="address-card address-main">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="address-type"><?php echo $domicilio["tipo_domicilio"] ?></span> <!-- Aquí se usa el valor de domicilio.tipo -->
                                            <?php if ($domicilio["principal"] == 1) {
                                                $id_domicilio_principal =  $id_domicilio ?>
                                                <span class="badge bg-primary">Principal</span>
                                            <?php } ?>
                                        </div>
                                        <p><?php echo $domicilio["calle"] . " " . $domicilio["numero"]; ?></p> <!-- Aquí se usa domicilio.calle -->
                                        <p><?php echo $domicilio["localidad"] . ", " . $domicilio["codigo_postal"]; ?></p> <!-- Aquí se usan domicilio.ciudad y domicilio.codigo_postal -->
                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-outline-primary me-2 btn-editar-domicilio" onclick="editarDomicilio(<?php echo $id_domicilio ?>)">Editar</button>
                                            <?php if ($domicilio["principal"] != 1) { ?>
                                                <button class="btn btn-sm btn-outline-success me-2 btn-principal-domicilio" onclick="hacerPrincipal(<?php echo $id_domicilio_principal ?>,<?php echo $id_domicilio ?>)">Hacer Principal</button>
                                            <?php } ?>
                                            <button class="btn btn-sm btn-outline-danger btn-eliminar-domicilio" onclick="eliminarDomicilio(<?php echo $id_domicilio ?>)">Eliminar</button> <!-- Solo se muestra si domicilio.principal es false -->
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($domicilios)) { ?>
                                <button id="btn-agregar-domicilio" class="btn btn-primary-custom mt-3">Agregar Nuevo Domicilio</button>
                            <?php } ?>
                            <!-- Modal para agregar/editar domicilio -->
                            <div class="modal fade" id="domicilioModal" tabindex="-1" aria-labelledby="domicilioModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="domicilioModalLabel">Agregar Nuevo Domicilio</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="domicilioForm">
                                                <input type="hidden" name="id_usuario" value="<?php echo $id_usuario ?>" id="id_usuario">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="codigo_postal" class="form-label">Código Postal</label>
                                                        <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" placeholder="Ingrese el código postal" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="provincia" class="form-label">Provincia</label>
                                                        <input type="text" class="form-control" id="provincia" name="provincia" placeholder="Ingrese la provincia" required>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="localidad" class="form-label">Localidad</label>
                                                        <input type="text" class="form-control" id="localidad" name="localidad" placeholder="Ingrese la localidad" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="barrio" class="form-label">Barrio</label>
                                                        <input type="text" class="form-control" id="barrio" name="barrio" placeholder="Ingrese el barrio" required>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-8">
                                                        <label for="calle" class="form-label">Calle</label>
                                                        <input type="text" class="form-control" id="calle" name="calle" placeholder="Ingrese la calle" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="numero" class="form-label">Número</label>
                                                        <input type="text" class="form-control" id="numero" name="numero" placeholder="Ingrese el número" required>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="tipo_domicilio" class="form-label">Tipo de Domicilio</label>
                                                        <input type="text" class="form-control" id="tipo_domicilio" name="tipo_domicilio" placeholder="Ingrese el tipo de domicilio" required>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="button" class="btn btn-primary-custom" onclick="guardarDomicilio()">Guardar Domicilio</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="modalEditarDomicilio" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel">Editar Domicilio</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="formEditarDomicilio">
                                                <input type="hidden" id="domicilioId" name="domicilioId">
                                                <div class="mb-3">
                                                    <label for="direccion" class="form-label">Dirección</label>
                                                    <input type="text" class="form-control" id="direccion" name="direccion">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="ciudad" class="form-label">Ciudad</label>
                                                    <input type="text" class="form-control" id="ciudad" name="ciudad">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="codigoPostal" class="form-label">Código Postal</label>
                                                    <input type="text" class="form-control" id="codigoPostal" name="codigoPostal">
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="button" class="btn btn-primary" onclick="guardarDomicilios()">Guardar Cambios</button>
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

    <div class="modal fade" id="pedidoDetalleModal" tabindex="-1" aria-labelledby="pedidoDetalleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pedidoDetalleModalLabel">Detalle del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detallePedidoBody">
                    <!-- Aquí se cargará el contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


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

        function verDetallePedido(idPedido) {
            // Crear la solicitud AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "ver_detalle.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            // Manejador para la respuesta
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Insertar la respuesta en el modal
                    document.getElementById("detallePedidoBody").innerHTML = xhr.responseText;

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById("pedidoDetalleModal"));
                    modal.show();
                } else {
                    alert("Error al obtener los detalles del pedido.");
                }
            };

            // Enviar los datos al servidor
            xhr.send(`id_pedido=${idPedido}`);
        }
        document.addEventListener('DOMContentLoaded', function() {
            const btnAgregarDomicilio = document.getElementById('btn-agregar-domicilio');
            const domicilioModal = new bootstrap.Modal(document.getElementById('domicilioModal')); // Bootstrap Modal API

            btnAgregarDomicilio.addEventListener('click', function() {
                domicilioModal.show(); // Muestra el modal
            });
        });

        // Función para guardar domicilio
        function guardarDomicilio() {
            const idUsuario = document.getElementById('id_usuario').value.trim();
            const codigoPostal = document.getElementById('codigo_postal').value.trim();
            const provincia = document.getElementById('provincia').value.trim();
            const localidad = document.getElementById('localidad').value.trim();
            const barrio = document.getElementById('barrio').value.trim();
            const calle = document.getElementById('calle').value.trim();
            const numero = document.getElementById('numero').value.trim();
            const tipoDomicilio = document.getElementById('tipo_domicilio').value;

            // Validación de campos
            if (!idUsuario || !codigoPostal || !provincia || !localidad || !barrio || !calle || !numero) {
                alert('Por favor, complete todos los campos antes de enviar el formulario.');
                return;
            }

            if (tipoDomicilio === '') {
                alert('Por favor, seleccione un tipo de domicilio.');
                return;
            }

            // Crear un objeto con los datos
            const datos = {
                id_usuario: idUsuario,
                codigo_postal: codigoPostal,
                provincia: provincia,
                localidad: localidad,
                barrio: barrio,
                calle: calle,
                numero: numero,
                tipo_domicilio: tipoDomicilio
            };

            // Enviar datos con AJAX
            fetch('agregar_domicilio.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datos) // Convertir objeto a JSON y enviarlo en el cuerpo
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text(); // Intentar leer la respuesta como texto
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text); // Intentar convertir el texto en JSON
                        if (data.success) {
                            alert('Domicilio agregado correctamente.');
                            location.reload(); // Recarga la página para reflejar cambios
                        } else {
                            alert('Error al agregar el domicilio: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Respuesta no válida:', text);
                        alert('Error: La respuesta del servidor no es válida.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al procesar la solicitud. Verifique su conexión o contacte al administrador.');
                });
        }

        function editarDomicilio(idDomicilio) {
            // Realiza una solicitud AJAX para obtener los datos del domicilio
            fetch(`getDomicilio.php?id=${idDomicilio}`)
                .then(response => response.json())
                .then(data => {
                    // Rellena el formulario con los datos obtenidos
                    document.getElementById('domicilioId').value = data.id;
                    document.getElementById('direccion').value = data.direccion;
                    document.getElementById('ciudad').value = data.ciudad;
                    document.getElementById('codigoPostal').value = data.codigoPostal;

                    // Muestra el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalEditarDomicilio'));
                    modal.show();
                })
                .catch(error => console.error('Error al obtener los datos del domicilio:', error));
        }

        function guardarDomicilios() {
            // Obtén los datos del formulario
            const formData = new FormData(document.getElementById('formEditarDomicilio'));

            // Realiza la solicitud AJAX para guardar los datos
            fetch('/guardarDomicilio.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Domicilio guardado exitosamente.');
                        // Oculta el modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarDomicilio'));
                        modal.hide();
                        // Actualiza la interfaz según sea necesario
                    } else {
                        alert('Error al guardar el domicilio.');
                    }
                })
                .catch(error => console.error('Error al guardar el domicilio:', error));
        }


        function hacerPrincipal(idDomicilioActual, idDomicilioNuevo) {
            if (!confirm('¿Está seguro de que desea establecer este domicilio como principal?')) {
                return; // Salir si el usuario cancela la confirmación
            }

            // Enviar solicitud para cambiar el domicilio principal
            fetch('hacer_principal.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_domicilio_actual: idDomicilioActual,
                        id_domicilio_nuevo: idDomicilioNuevo
                    }) // Enviar los IDs de los domicilios
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Domicilio principal actualizado correctamente.');
                        location.reload(); // Recargar la página para reflejar cambios
                    } else {
                        alert('Error al actualizar el domicilio principal: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al procesar la solicitud. Verifique su conexión o contacte al administrador.');
                });
        }


        function eliminarDomicilio(idDomicilio) {
            if (!confirm('¿Está seguro de que desea eliminar este domicilio? Esta acción no se puede deshacer.')) {
                return; // Salir si el usuario cancela la confirmación
            }

            // Enviar solicitud para eliminar el domicilio
            fetch('eliminar_domicilio.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_domicilio: idDomicilio
                    }) // Enviar el ID del domicilio a eliminar
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Domicilio eliminado correctamente.');
                        location.reload(); // Recargar la página para reflejar cambios
                    } else {
                        alert('Error al eliminar el domicilio: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al procesar la solicitud. Verifique su conexión o contacte al administrador.');
                });
        }
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.list-group-item[data-bs-toggle="list"]'); // Selecciona los tabs del menú

            // Restaurar el tab activo al cargar la página
            const activeTabId = localStorage.getItem('activeTabId');
            if (activeTabId) {
                const activeTabElement = document.querySelector(`a[href="${activeTabId}"]`);
                if (activeTabElement) {
                    const tabInstance = new bootstrap.Tab(activeTabElement);
                    tabInstance.show();
                }
            }

            // Guardar el tab activo en localStorage al cambiar de tab
            menuItems.forEach(item => {
                item.addEventListener('shown.bs.tab', function(event) {
                    const targetId = event.target.getAttribute('href');
                    if (targetId) {
                        localStorage.setItem('activeTabId', targetId);
                    }
                });
            });
        });

        document.addEventListener("DOMContentLoaded", () => {
            const selectOrdenarPedidos = document.getElementById("ordenar-pedidos");
            const pedidosContainer = document.getElementById("pedidos-container");

            selectOrdenarPedidos.addEventListener("change", () => {
                const estadoSeleccionado = selectOrdenarPedidos.value;
                const idUsuario = document.getElementById("id_usuario").value;


                // Solicitud AJAX
                fetch("filtro.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            estado: estadoSeleccionado,
                            id_usuario: idUsuario
                        }),
                    })
                    .then(response => response.text()) // Obtén la respuesta como texto inicialmente
                    .then(data => {
                        console.log("Respuesta recibida:", data);
                        return JSON.parse(data); // Intenta convertirla a JSON
                    })
                    .then(json => {
                        if (json.success) {
                            actualizarPedidos(json.pedidos);
                        } else {
                            console.error("Error al obtener los pedidos:", json.message);
                        }
                    })
                    .catch(error => console.error("Error en la solicitud:", error));

            });

            function actualizarPedidos(pedidos) {
                // Limpia el contenedor
                pedidosContainer.innerHTML = "";

                // Genera dinámicamente las tarjetas de pedidos
                pedidos.forEach(pedido => {
                    const pedidoHTML = `
                <div class="order-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Pedido: #${pedido.id_pedido}</h5>
                        <span class="order-status status-${pedido.estado_pedido}">${pedido.estado_pedido}</span>
                    </div>
                    <p>Fecha: ${pedido.fecha}</p>
                    <p>Total: ${pedido.total}</p>
                    <button class="btn btn-primary-custom btn-sm me-2 btn-ver-detalle" 
                        data-id-pedido="${pedido.id_pedido}" 
                        onclick="verDetallePedido(${pedido.id_pedido})">
                        Ver Detalle
                    </button>
                    ${
                        ["pendiente", "procesado", "cambiado"].includes(pedido.estado_pedido)
                            ? `<button class="btn btn-danger btn-sm btn-cancelar-pedido" 
                                data-id="${pedido.id_pedido}" 
                                onclick="cancelarPedido(${pedido.id_pedido})">
                                Cancelar Pedido
                            </button>`
                            : ""
                    }
                    ${
                        ["procesado", "cambiado"].includes(pedido.estado_pedido)
                            ? `<a href="pagar_pedido.php?id_pedido=${pedido.id_pedido}" 
                                class="btn btn-success btn-sm btn-pagar">
                                Pagar
                            </a>`
                            : ""
                    }
                </div>
            `;
                    pedidosContainer.insertAdjacentHTML("beforeend", pedidoHTML);
                });
            }
        });
    </script>
</body>

</html>