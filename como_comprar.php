<?php

    include ("header.php");

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Cómo Comprar?</title>
    <style>
        body {
            background: linear-gradient(to bottom, #ffffff, #f8f9fa);
        }
        .section-title {
            color: var(--primary-color);
        }
        .step-title {
            color: var(--primary-color);
        }
        .icon-primary {
            color: var(--secondary-color);
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        .card {
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .order-example {
            border: 1px solid var(--primary-color);
        }
        .order-status {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 section-title">¿Cómo Comprar?</h2>
            
            <div class="row g-4">
                <!-- Paso 1 -->
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-search fa-2x me-3 icon-primary"></i>
                                <h3 class="card-title step-title">Paso 1: Busca tu producto</h3>
                            </div>
                            <p class="card-text">Usa nuestro buscador o navega por nuestras categorías para encontrar lo que necesitas.</p>
                        </div>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-cart-plus fa-2x me-3 icon-primary"></i>
                                <h3 class="card-title step-title">Paso 2: Añade al carrito</h3>
                            </div>
                            <p class="card-text">Haz clic en 'Agregar al carrito' en los productos que desees comprar.</p>
                            <button class="btn btn-primary mt-3">Agregar al carrito</button>
                        </div>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-shopping-cart fa-2x me-3 icon-primary"></i>
                                <h3 class="card-title step-title">Paso 3: Revisa tu carrito</h3>
                            </div>
                            <p class="card-text">Haz clic en el ícono del carrito para revisar los productos seleccionados, sus cantidades y precios.</p>
                            <div class="bg-light p-3 mt-3 rounded">
                                <p class="mb-1"><small>Resumen del carrito:</small></p>
                                <p class="mb-1"><small>2 x Producto A - $50.00</small></p>
                                <p class="mb-1"><small>1 x Producto B - $30.00</small></p>
                                <p class="fw-bold mt-2 mb-0"><small>Total: $80.00</small></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 4 -->
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-credit-card fa-2x me-3 icon-primary"></i>
                                <h3 class="card-title step-title">Paso 4: Realiza tu pago</h3>
                            </div>
                            <p class="card-text">Elige tu método de pago, ingresa los datos requeridos y confirma tu compra.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de Compras -->
            <div class="card mt-5 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-user fa-2x me-3 icon-primary"></i>
                        <h3 class="card-title step-title">Resumen de Compras en el Panel del Usuario</h3>
                    </div>
                    <p class="card-text mb-3">Para ver el resumen de tus compras, inicia sesión y accede a tu panel de usuario.</p>
                    <p class="fw-bold text-primary mb-4">En la sección 'Mis Compras', encontrarás un historial detallado de tus pedidos.</p>
                    <div class="bg-light p-4 rounded order-example">
                        <h4 class="step-title mb-3">Ejemplo de Pedido</h4>
                        <p class="mb-1"><small>Pedido #1234</small></p>
                        <p class="mb-1"><small>Fecha: 01/01/2024</small></p>
                        <p class="mb-1"><small>Total: $150.00</small></p>
                        <p class="mb-0 fw-bold order-status"><small>Estado: Entregado</small></p>
                    </div>
                </div>
            </div>

            <!-- Botón Final -->
            <div class="text-center mt-5">
                <button class="btn btn-primary btn-lg">Comenzar Compra Ahora</button>
            </div>
        </div>
    </section>

</body>
</html>