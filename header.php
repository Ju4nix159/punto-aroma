<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        #carrito-desplegable {
            position: fixed;
            top: 60px;
            right: 20px;
            width: 400px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
        }

        .carrito-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .carrito-item {
            border-bottom: 1px solid #eee;
            padding: 15px;
            transition: background-color 0.3s ease;
        }

        .carrito-item:hover {
            background-color: #f8f9fa;
        }

        .carrito-item:last-child {
            border-bottom: none;
        }

        .cantidad-control {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cantidad-control button {
            background: none;
            border: 1px solid #ddd;
            padding: 0 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .cantidad-control span {
            margin: 0 8px;
        }

        .precio-unitario {
            font-size: 0.8em;
            color: #666;
        }

        .btn-eliminar {
            color: #dc3545;
            background: none;
            border: none;
            padding: 0;
            font: inherit;
            cursor: pointer;
            outline: inherit;
            transition: color 0.3s ease;
        }

        .btn-eliminar:hover {
            color: #a71d2a;
        }

        #carrito-btn {
            position: relative;
        }

        #carrito-btn .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--secondary-color);
            color: white;
            border-radius: 50%;
            padding: 0.25em 0.6em;
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <header id="header" class="py-3 bg-white border-bottom sticky-top">
        <div class="container d-flex flex-wrap justify-content-center">
            <a href="index.php#hero" class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto text-dark text-decoration-none">
                <span class="fs-4 fw-bold text-primary-custom">Punto Aroma</span>
            </a>

            <nav class="navbar navbar-expand-lg navbar-light">
                <form class="d-flex mx-auto">
                    <input class="form-control me-2" type="search" placeholder="Buscar productos..." aria-label="Search">
                </form>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link text-primary-custom" href="catalogo.php">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-primary-custom" href="index.php#destacados">destacados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-primary-custom" href="index.php#testimonios">Testimonios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-primary-custom" href="index.php#contacto">Contacto</a>
                        </li>

                        <!-- Dropdown "Mi cuenta" -->
                        <li id="miCuenta" class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user nav-icon"></i> Mi cuenta
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if (isset($_SESSION["usuario"])): ?>
                                    <?php if ($_SESSION["permiso"] == 2): ?>
                                        <li><a class="dropdown-item" href="panelUsuario.php"><i class="fas fa-user-circle nav-icon"></i> Panel de Usuario</a></li>
                                        <li><a class="dropdown-item" href="/pa/admin/cerrar_sesion.php"><i class="fas fa-sign-out-alt nav-icon"></i> Cerrar Sesión</a></li>
                                    <?php elseif ($_SESSION["permiso"] == 1): ?>
                                        <li><a class="dropdown-item" href="/pa/admin/admin.php"><i class="fas fa-user-circle nav-icon"></i> Panel de Admin</a></li>
                                        <li><a class="dropdown-item" href="panelUsuario.php"><i class="fas fa-user-circle nav-icon"></i> Panel de usuario</a></li>
                                        <li><a class="dropdown-item" href="/pa/admin/cerrar_sesion.php"><i class="fas fa-sign-out-alt nav-icon"></i> Cerrar Sesión</a></li>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="iniciarSesion.php"><i class="fas fa-sign-in-alt nav-icon"></i> Iniciar Sesión</a></li>
                                    <li><a class="dropdown-item" href="registro.php"><i class="fas fa-user-plus nav-icon"></i> Registrarse</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <div class="position-relative">
                            <button id="carrito-btn" class="btn btn-outline-secondary" aria-label="Ver carrito">
                                <i class="bi bi-cart"></i>
                                <span class="badge">3</span>
                            </button>
                            <div id="carrito-desplegable">
                                <div class="carrito-header">
                                    <h5 class="mb-0">Carrito de Compras</h5>
                                </div>
                                <div class="p-3">
                                    <div class="d-flex justify-content-between mb-2 fw-bold">
                                        <div style="width: 40%;">Nombre</div>
                                        <div style="width: 30%;" class="text-center">Cantidad</div>
                                        <div style="width: 30%;" class="text-end">Total</div>
                                    </div>
                                    <div class="carrito-items">
                                        <div class="carrito-item d-flex justify-content-between align-items-center">
                                            <div class="producto-info" style="width: 40%;">
                                                <h6 class="mb-0">Vela Aromática Lavanda</h6>
                                                <span class="precio-unitario">$14.99 c/u</span>
                                            </div>
                                            <div class="cantidad-control" style="width: 30%;">
                                                <button class="restar">-</button>
                                                <span class="cantidad">2</span>
                                                <button class="sumar">+</button>
                                            </div>
                                            <div class="total text-end" style="width: 30%;">
                                                $29.98
                                            </div>
                                            <button class="btn-eliminar ms-2" aria-label="Eliminar producto">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="carrito-item d-flex justify-content-between align-items-center">
                                            <div class="producto-info" style="width: 40%;">
                                                <h6 class="mb-0">Sahumerio Sándalo</h6>
                                                <span class="precio-unitario">$9.99 c/u</span>
                                            </div>
                                            <div class="cantidad-control" style="width: 30%;">
                                                <button class="restar">-</button>
                                                <span class="cantidad">1</span>
                                                <button class="sumar">+</button>
                                            </div>
                                            <div class="total text-end" style="width: 30%;">
                                                $9.99
                                            </div>
                                            <button class="btn-eliminar ms-2" aria-label="Eliminar producto">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="carrito-item d-flex justify-content-between align-items-center">
                                            <div class="producto-info" style="width: 40%;">
                                                <h6 class="mb-0">Aceite Esencial Eucalipto</h6>
                                                <span class="precio-unitario">$12.99 c/u</span>
                                            </div>
                                            <div class="cantidad-control" style="width: 30%;">
                                                <button class="restar">-</button>
                                                <span class="cantidad">1</span>
                                                <button class="sumar">+</button>
                                            </div>
                                            <div class="total text-end" style="width: 30%;">
                                                $12.99
                                            </div>
                                            <button class="btn-eliminar ms-2" aria-label="Eliminar producto">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Total</h6>
                                        <span class="fw-bold" id="total-carrito">$52.96</span>
                                    </div>
                                    <button class="btn btn-primary-custom w-100 mt-3">Ir al Checkout</button>
                                </div>
                            </div>
                        </div>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Carrito -->
        <div id="cart" class="hidden">
            <h2 class="cart-title">Tu Carrito</h2>
            <div id="cart-content"></div>
        </div>
    </header>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carritoBtn = document.getElementById('carrito-btn');
            const carritoDesplegable = document.getElementById('carrito-desplegable');
            const botonesEliminar = document.querySelectorAll('.btn-eliminar');
            const botonesSumar = document.querySelectorAll('.sumar');
            const botonesRestar = document.querySelectorAll('.restar');
            const badge = carritoBtn.querySelector('.badge');

            carritoBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                carritoDesplegable.style.display = carritoDesplegable.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function(e) {
                if (!carritoDesplegable.contains(e.target) && e.target !== carritoBtn) {
                    carritoDesplegable.style.display = 'none';
                }
            });

            botonesEliminar.forEach(boton => {
                boton.addEventListener('click', function(e) {
                    const item = this.closest('.carrito-item');
                    item.remove();
                    actualizarTotal();
                    actualizarContadorCarrito();
                });
            });

            botonesSumar.forEach(boton => {
                boton.addEventListener('click', function(e) {
                    const item = this.closest('.carrito-item');
                    const cantidadElement = item.querySelector('.cantidad');
                    const cantidad = parseInt(cantidadElement.textContent) + 1;
                    cantidadElement.textContent = cantidad;
                    actualizarTotalItem(item);
                    actualizarTotal();
                });
            });

            botonesRestar.forEach(boton => {
                boton.addEventListener('click', function(e) {
                    const item = this.closest('.carrito-item');
                    const cantidadElement = item.querySelector('.cantidad');
                    const cantidad = Math.max(parseInt(cantidadElement.textContent) - 1, 1);
                    cantidadElement.textContent = cantidad;
                    actualizarTotalItem(item);
                    actualizarTotal();
                });
            });

            function actualizarTotalItem(item) {
                const cantidad = parseInt(item.querySelector('.cantidad').textContent);
                const precioUnitario = parseFloat(item.querySelector('.precio-unitario').textContent.replace('$', ''));
                const totalElement = item.querySelector('.total');
                const total = (cantidad * precioUnitario).toFixed(2);
                totalElement.textContent = '$' + total;
            }

            function actualizarTotal() {
                const totales = document.querySelectorAll('.carrito-item .total');
                let total = 0;
                totales.forEach(t => {
                    total += parseFloat(t.textContent.replace('$', ''));
                });
                document.getElementById('total-carrito').textContent = '$' + total.toFixed(2);
            }

            function actualizarContadorCarrito() {
                const items = document.querySelectorAll('.carrito-item');
                badge.textContent = items.length;
            }
        });
    </script>
</body>

</html>