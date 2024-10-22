<?php
include 'header.php';

include 'admin/config/sbd.php';

if (isset($_SESSION["usuario"])) {
    $id_usuario = $_SESSION["usuario"];
    $sql_usuario = $con->prepare("  SELECT iu.nombre AS nombre_usuario, iu.apellido, iu.dni, iu.fecha_nacimiento, iu.telefono, s.nombre AS sexo
                                    FROM info_usuarios iu
                                    JOIN sexos s ON iu.id_sexo = s.id_sexo
                                    WHERE iu.id_usuario = :id_usuario;");
    $sql_usuario->bindParam(":id_usuario", $id_usuario);
    $sql_usuario->execute();
    $usuario = $sql_usuario->fetch(PDO::FETCH_ASSOC);
} else {
    header("Location: /pa/iniciarSesion.php");
    exit;
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Punto Aroma</title>
    <style>
        .user-info-card, .order-card, .address-card {
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
                            
                            <!-- Alerta para perfil incompleto -->
                            <div id="incomplete-profile-alert" class="incomplete-profile-alert">
                                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Perfil Incompleto</h4>
                                <p>Parece que aún no has completado toda tu información personal. Completa tu perfil para aprovechar al máximo tu experiencia en Punto Aroma.</p>
                                <hr>
                                <p class="mb-0">
                                    <button id="btn-completar-perfil" class="btn btn-primary-custom">Completar Perfil</button>
                                </p>
                            </div>

                            <div id="info-usuario" class="user-info-card">
                                <div class="row">
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Nombre:</div>
                                        <div class="user-info-value" id="nombre-display">-</div>
                                    </div>
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Apellido:</div>
                                        <div class="user-info-value" id="apellido-display">-</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">DNI:</div>
                                        <div class="user-info-value" id="dni-display">-</div>
                                    </div>
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Edad:</div>
                                        <div class="user-info-value"><span id="edad-display">-</span></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Teléfono:</div>
                                        <div class="user-info-value" id="telefono-display">-</div>
                                    </div>
                                    <div class="col-md-6 user-info-item">
                                        <div class="user-info-label">Sexo:</div>
                                        <div class="user-info-value" id="sexo-display">-</div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button id="btn-editar" class="btn btn-primary-custom">Editar Información</button>
                                </div>
                            </div>
                            <form id="form-editar" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input type="text" class="form-control" id="apellido" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="dni" class="form-label">DNI</label>
                                        <input type="text" class="form-control" id="dni" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha-nacimiento" class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fecha-nacimiento" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="telefono" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sexo" class="form-label">Sexo</label>
                                        <select class="form-select" id="sexo" required>
                                            <option value="">Seleccionar</option>
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary-custom">Guardar Cambios</button>
                                    <button type="button" id="btn-cancelar" class="btn btn-secondary ms-2">Cancelar</button>
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
                                <!-- Los pedidos se cargarán aquí dinámicamente -->
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
    <div class="modal fade" id="pedidoDetalleModal" tabindex="-1" aria-labelledby="pedidoDetalleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pedidoDetalleModalLabel">Detalle del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="pedidoDetalleModalBody">
                    <!-- El detalle del pedido se cargará aquí dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnEditar = document.getElementById('btn-editar');
            const btnCancelar = document.getElementById('btn-cancelar');
            const btnCompletarPerfil = document.getElementById('btn-completar-perfil');
            const infoUsuario = document.getElementById('info-usuario');
            const formEditar = document.getElementById('form-editar');
            const incompleteProfileAlert = document.getElementById('incomplete-profile-alert');

            function mostrarFormulario() {
                infoUsuario.style.display = 'none';
                formEditar.style.display = 'block';
                incompleteProfileAlert.style.display = 'none';
            }

            function mostrarInformacion() {
                infoUsuario.style.display = 'block';
                formEditar.style.display = 'none';
                verificarPerfilCompleto();
            }

            function verificarPerfilCompleto() {
                const camposInfo = document.querySelectorAll('.user-info-value');
                const perfilIncompleto = Array.from(camposInfo).some(campo => 
                    campo.textContent.trim() === '-' || campo.textContent.trim() === ''
                );
                incompleteProfileAlert.style.display = perfilIncompleto ? 'block' : 'none';
            }

            btnEditar.addEventListener('click', mostrarFormulario);
            btnCompletarPerfil.addEventListener('click', mostrarFormulario);
            btnCancelar.addEventListener('click', mostrarInformacion);

            formEditar.addEventListener('submit', function(e) {
                e.preventDefault();
                // Actualizar la información mostrada
                document.getElementById('nombre-display').textContent = document.getElementById('nombre').value || '-';
                document.getElementById('apellido-display').textContent = document.getElementById('apellido').value || '-';
                document.getElementById('dni-display').textContent = document.getElementById('dni').value || '-';
                document.getElementById('telefono-display').textContent = document.getElementById('telefono').value || '-';
                document.getElementById('sexo-display').textContent = document.getElementById('sexo').value || '-';

                // Calcular y actualizar la edad
                const fechaNacimiento = new Date(document.getElementById('fecha-nacimiento').value);
                if (!isNaN(fechaNacimiento.getTime())) {
                    const hoy = new Date();
                    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
                    const m = hoy.getMonth() - fechaNacimiento.getMonth();
                    if (m < 0 || (m === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                        edad--;
                    }
                    document.getElementById('edad-display').textContent = edad + ' años';
                } else {
                    document.getElementById('edad-display').textContent = '-';
                }

                mostrarInformacion();
            });

            // Verificar perfil completo al cargar la página
            verificarPerfilCompleto();

            // Funcionalidad para manejar domicilios
            const domiciliosContainer = document.getElementById('domicilios-container');
            const btnAgregarDomicilio = document.getElementById('btn-agregar-domicilio');
            const domicilioModal = new bootstrap.Modal(document.getElementById('domicilioModal'));
            const formDomicilio = document.getElementById('form-domicilio');
            const btnGuardarDomicilio = document.getElementById('btn-guardar-domicilio');

            let domicilios = [
                { id: 1, tipo: 'Casa', calle: 'Calle Principal 123', ciudad: 'Ciudad A', codigoPostal: '12345', principal: true },
                { id: 2, tipo: 'Trabajo', calle: 'Avenida Comercial 456', ciudad: 'Ciudad B', codigoPostal: '67890', principal: false }
            ];

            function renderizarDomicilios() {
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
            }

            btnAgregarDomicilio.addEventListener('click', () => {
                formDomicilio.reset();
                document.getElementById('domicilio-id').value = '';
                document.getElementById('domicilioModalLabel').textContent = 'Agregar Nuevo Domicilio';
                domicilioModal.show();
            });

            btnGuardarDomicilio.addEventListener('click', () => {
                const id = document.getElementById('domicilio-id').value;
                const tipo = document.getElementById('domicilio-tipo').value;
                const calle = document.getElementById('domicilio-calle').value;
                const ciudad = document.getElementById('domicilio-ciudad').value;
                const codigoPostal = document.getElementById('domicilio-codigo-postal').value;

                if (id) {
                    // Editar domicilio existente
                    const index = domicilios.findIndex(d => d.id == id);
                    if (index !== -1) {
                        domicilios[index] = { ...domicilios[index], tipo, calle, ciudad, codigoPostal };
                    }
                } else {
                    // Agregar nuevo domicilio
                    const newId = Math.max(...domicilios.map(d => d.id)) + 1;
                    domicilios.push({ id: newId, tipo, calle, ciudad, codigoPostal, principal: false });
                }

                renderizarDomicilios();
                domicilioModal.hide();
            });

            domiciliosContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-editar-domicilio')) {
                    const id = e.target.dataset.id;
                    const domicilio = domicilios.find(d => d.id == id);
                    if (domicilio) {
                        document.getElementById('domicilio-id').value = domicilio.id;
                        document.getElementById('domicilio-tipo').value = domicilio.tipo;
                        document.getElementById('domicilio-calle').value = domicilio.calle;
                        document.getElementById('domicilio-ciudad').value = domicilio.ciudad;
                        document.getElementById('domicilio-codigo-postal').value = domicilio.codigoPostal;
                        document.getElementById('domicilioModalLabel').textContent = 'Editar Domicilio';
                        domicilioModal.show();
                    }
                } else if (e.target.classList.contains('btn-principal-domicilio')) {
                    const id = e.target.dataset.id;
                    domicilios.forEach(d => d.principal = d.id == id);
                    renderizarDomicilios();
                } else if (e.target.classList.contains('btn-eliminar-domicilio')) {
                    const id = e.target.dataset.id;
                    if (confirm('¿Estás seguro de que deseas eliminar este domicilio?')) {
                        domicilios = domicilios.filter(d => d.id != id);
                        renderizarDomicilios();
                    }
                }
            });

            // Renderizar domicilios iniciales
            renderizarDomicilios();

            // Funcionalidad para manejar pedidos
            const pedidosContainer = document.getElementById('pedidos-container');
            const pedidoDetalleModal = new bootstrap.Modal(document.getElementById('pedidoDetalleModal'));
            const pedidoDetalleModalBody = document.getElementById('pedidoDetalleModalBody');
            const ordenarPedidosSelect = document.getElementById('ordenar-pedidos');

            let pedidos = [
                { 
                    id: 1234, 
                    fecha: '15/05/2024', 
                    total: 150.00, 
                    estado: 'Procesando',
                    productos: [
                        { nombre: 'Perfume Floral', cantidad: 1, precio: 80.00 },
                        { nombre: 'Vela Aromática', cantidad: 2, precio: 35.00 }
                    ],
                    direccionEnvio: 'Calle Principal 123, Ciudad A, 12345'
                },
                { 
                    id: 1235, 
                    fecha: '10/05/2024', 
                    total: 85.50, 
                    estado: 'En Camino',
                    productos: [
                        { nombre: 'Difusor de Aceites', cantidad: 1, precio: 55.50 },
                        { nombre: 'Aceite Esencial de Lavanda', cantidad: 1, precio: 30.00 }
                    ],
                    direccionEnvio: 'Avenida Comercial 456, Ciudad B, 67890'
                },
                { 
                    id: 1236, 
                    fecha: '05/05/2024', 
                    total: 200.00, 
                    estado: 'Entregado',
                    productos: [
                        { nombre: 'Set de Aromaterapia', cantidad: 1, precio: 150.00 },
                        { nombre: 'Spray Ambiental', cantidad: 2, precio: 25.00 }
                    ],
                    direccionEnvio: 'Calle Principal 123, Ciudad A, 12345'
                },
                { 
                    id: 1237, 
                    fecha: '01/05/2024', 
                    total: 75.00, 
                    estado: 'Cancelado',
                    productos: [
                        { nombre: 'Jabón Artesanal', cantidad: 3, precio: 25.00 }
                    ],
                    direccionEnvio: 'Avenida Comercial 456, Ciudad B, 67890'
                }
            ];

            function renderizarPedidos(filtro = 'todos') {
                pedidosContainer.innerHTML = '';
                const pedidosFiltrados = filtro === 'todos' ? pedidos : pedidos.filter(p => p.estado.toLowerCase() === filtro);
                pedidosFiltrados.forEach(pedido => {
                    const pedidoCard = document.createElement('div');
                    pedidoCard.className = 'order-card';
                    pedidoCard.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Pedido #${pedido.id}</h5>
                            <span class="order-status status-${pedido.estado.toLowerCase().replace(' ', '-')}">${pedido.estado}</span>
                        </div>
                        <p>Fecha: ${pedido.fecha}</p>
                        <p>Total: $${pedido.total.toFixed(2)}</p>
                        <button class="btn btn-primary-custom btn-sm me-2 btn-ver-detalle" data-id="${pedido.id}">Ver Detalle</button>
                        ${pedido.estado === 'Procesando' ? `<button class="btn btn-danger btn-sm btn-cancelar-pedido" data-id="${pedido.id}">Cancelar Pedido</button>` : ''}
                    `;
                    pedidosContainer.appendChild(pedidoCard);
                });
            }

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
                    pedidoDetalleModal.show();
                }
            }

            pedidosContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-ver-detalle')) {
                    const id = e.target.dataset.id;
                    mostrarDetallePedido(id);
                } else if (e.target.classList.contains('btn-cancelar-pedido')) {
                    const id = e.target.dataset.id;
                    if (confirm('¿Estás seguro de que deseas cancelar este pedido?')) {
                        const index = pedidos.findIndex(p => p.id == id);
                        if (index !== -1) {
                            pedidos[index].estado = 'Cancelado';
                            renderizarPedidos(ordenarPedidosSelect.value);
                        }
                    }
                }
            });

            ordenarPedidosSelect.addEventListener('change', (e) => {
                renderizarPedidos(e.target.value);
            });

            // Renderizar pedidos iniciales
            renderizarPedidos();
        });
    </script>
</body>
</html>