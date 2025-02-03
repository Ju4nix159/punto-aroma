<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge.badge-disponible {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge.badge-pendiente {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge.badge-cambiado {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }

        .info-label {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .table th {
            font-weight: 500;
            color: #6b7280;
        }

        .total-amount {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .payment-info {
            background-color: #f3f4f6;
            border-radius: 0.375rem;
            padding: 1rem;
        }

        /* Estilo para centrar el botón */
        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body>

<!-- Botón para iniciar el modal -->
<div class="button-container">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderDetailModal">
        Iniciar Modal
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold">Detalle del Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Order Summary -->
                <div class="row g-4 mb-4">
                    <!-- Order Information -->
                    <div class="col-md-6">
                        <h6 class="info-label mb-3">Información del Pedido</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <span class="fw-medium">ID Pedido:</span>
                            </div>
                            <div class="col-6">
                                47
                            </div>
                            <div class="col-6">
                                <span class="fw-medium">Fecha:</span>
                            </div>
                            <div class="col-6">
                                2025-01-24
                            </div>
                            <div class="col-6">
                                <span class="fw-medium">Total:</span>
                            </div>
                            <div class="col-6">
                                $280.00
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="col-md-6">
                        <h6 class="info-label mb-3">Información del Cliente</h6>
                        <div class="mb-2">
                            <span class="fw-medium">Cliente: </span>
                            Juani Melillo
                        </div>
                        <div class="mb-2">
                            <span class="fw-medium">Email: </span>
                            juanimelillo@gmail.com
                        </div>
                        <div class="mb-2">
                            <span class="fw-medium">Dirección: </span>
                            <span class="text-muted">No especificada</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="payment-info mb-4">
                    <h6 class="info-label mb-3">Información de Pago</h6>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <span class="fw-medium">Total:</span>
                        </div>
                        <div class="col-6 col-md-3">
                            $280.00
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="fw-medium">Seña pagada:</span>
                        </div>
                        <div class="col-6 col-md-3">
                            $100.00
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="fw-medium">Saldo pendiente:</span>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-danger fw-bold">$180.00</span>
                        </div>
                    </div>
                </div>

                <!-- Order Status -->
                <div class="mb-4">
                    <h6 class="info-label mb-3">Estado del Pedido</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-pendiente">Pendiente</span>
                        <small class="text-muted">Pedido pendiente, para ser procesado y aprobado por la empresa</small>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="mb-4">
                    <h6 class="info-label mb-3">Detalles de los Productos</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>9</td>
                                    <td>1</td>
                                    <td>$140.00</td>
                                    <td>
                                        <span class="badge badge-disponible">Disponible</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>1</td>
                                    <td>$140.00</td>
                                    <td>
                                        <span class="badge badge-disponible">Disponible</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total -->
                <div class="d-flex justify-content-end border-top pt-3">
                    <div class="text-end">
                        <span class="fw-medium me-3">Total:</span>
                        <span class="total-amount">$280.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>