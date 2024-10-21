<?php
include 'header.php';

?>
<!DOCTYPE html>

<head>
    <title>Panel de Usuario - Punto Aroma</title>

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
                            <form>
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" value="Juan Pérez">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" value="juan.perez@ejemplo.com">
                                </div>
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" value="+34 123 456 789">
                                </div>
                                <button type="submit" class="btn btn-primary-custom">Guardar Cambios</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="pedidos">
                            <h2 class="mb-4">Historial de Pedidos</h2>
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action" aria-current="true" data-bs-toggle="modal" data-bs-target="#pedidoModal">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Pedido #1234</h5>
                                        <small>3 días atrás</small>
                                    </div>
                                    <p class="mb-1">3 productos - Total: $44.97</p>
                                    <small class="text-success">Entregado</small>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#pedidoModal">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Pedido #1235</h5>
                                        <small>1 semana atrás</small>
                                    </div>
                                    <p class="mb-1">2 productos - Total: $29.98</p>
                                    <small class="text-warning">En tránsito</small>
                                </a>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="domicilios">
                            <h2 class="mb-4">Domicilios de Entrega</h2>
                            <div class="list-group mb-3">
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Casa</h5>
                                        <small>
                                            <a href="#" class="text-primary-custom" data-bs-toggle="modal" data-bs-target="#domicilioModal">Editar</a>
                                        </small>
                                    </div>
                                    <p class="mb-1">Calle Principal 123, Ciudad, CP 12345</p>
                                </div>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Trabajo</h5>
                                        <small>
                                            <a href="#" class="text-primary-custom" data-bs-toggle="modal" data-bs-target="#domicilioModal">Editar</a>
                                        </small>
                                    </div>
                                    <p class="mb-1">Avenida Comercial 456, Ciudad, CP 54321</p>
                                </div>
                            </div>
                            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#domicilioModal">Agregar Nuevo Domicilio</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Detalles del Pedido -->
    <div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pedidoModalLabel">Detalles del Pedido #1234</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Estado: <span class="text-success">Entregado</span></h6>
                    <h6>Fecha de Pedido: 15/04/2024</h6>
                    <h6>Dirección de Entrega:</h6>
                    <p>Calle Principal 123, Ciudad, CP 12345</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Vela Aromática - Lavanda</td>
                                <td>2</td>
                                <td>$14.99</td>
                                <td>$29.98</td>
                            </tr>
                            <tr>
                                <td>Sahumerio - Sándalo</td>
                                <td>1</td>
                                <td>$14.99</td>
                                <td>$14.99</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th>$44.97</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar/Agregar Domicilio -->
    <div class="modal fade" id="domicilioModal" tabindex="-1" aria-labelledby="domicilioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="domicilioModalLabel">Editar Domicilio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="nombreDomicilio" class="form-label">Nombre del Domicilio</label>
                            <input type="text" class="form-control" id="nombreDomicilio" value="Casa">
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" value="Calle Principal 123">
                        </div>
                        <div class="mb-3">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" id="ciudad" value="Ciudad">
                        </div>
                        <div class="mb-3">
                            <label for="codigoPostal" class="form-label">Código Postal</label>
                            <input type="text" class="form-control" id="codigoPostal" value="12345">
                        </div>
                        <button type="button" class="btn btn-secondary-custom">Guardar Cambios</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary-custom">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="">
        <?php include 'footer.php'; ?>
    </footer>

</body>

</html>