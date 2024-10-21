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
</head>

<body>
    <header class="py-3 bg-white border-bottom sticky-top">
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
                            <a class="nav-link text-primary-custom" href="index.php#ofertas">Ofertas</a>
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

                        <li class="nav-item">
                            <a class="nav-link" href="#" id="cart-icon" onclick="toggleCart(event)">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                        </li>
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
</body>

</html>
