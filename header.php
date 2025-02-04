<?php
session_start();
include 'carrito.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aroma y bienestar</title>
    <link rel="icon" type="image/svg" href="./assets/logoos/ayb-logo-sinfondo-chiquito.svg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .header-container {
            position: sticky;
            top: 0;
            background: white;
            z-index: 1050;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .logo-container {
            display: flex;
            justify-content: center;
            padding: 0.5rem;
        }

        .img-header {
            width: 130px;
            height: 70px;
        }

        .nav-cart-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 1rem;
        }

        .navbar {
            flex-grow: 1;
            margin-right: 1rem;
        }

        @media (min-width: 992px) {
            .header-container {
                padding: 1rem;
            }

            .header-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
                max-width: 1200px;
                margin: 0 auto;
            }

            .logo-container {
                padding: 0;
            }

            .nav-cart-container {
                padding: 0;
            }
        }

        @media (max-width: 991.98px) {
            .header-content {
                flex-direction: column;
            }

            .navbar-collapse {
                margin-top: 1rem;
            }

            .navbar-nav {
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <header class="header-container">
        <div class="header-content">
            <div class="logo-container">
                <a href="index.php#hero">
                    <img class="img-header" src="./assets/logoos/ayb-cto.jpg" alt="logo empresa">
                </a>
            </div>

            <div class="nav-cart-container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link text-primary-custom" href="catalogo.php">Catálogo</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-primary-custom" href="index.php#destacados">Destacados</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-primary-custom" href="index.php#testimonios">Testimonios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-primary-custom" href="como_comprar.php">¿Como Comprar?</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-primary-custom" href="index.php#contacto">Contacto</a>
                            </li>
                            <li id="miCuenta" class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" data-bs-toggle="dropdown">
                                    <i class="fas fa-user nav-icon"></i> Mi cuenta
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if (isset($_SESSION["usuario"])): ?>
                                        <?php if ($_SESSION["permiso"] == 2): ?>
                                            <li><a class="dropdown-item" href="panelUsuario.php"><i class="fas fa-user-circle nav-icon"></i> Panel de Usuario</a></li>
                                            <li><a class="dropdown-item" href="./admin/cerrar_sesion.php"><i class="fas fa-sign-out-alt nav-icon"></i> Cerrar Sesión</a></li>
                                        <?php elseif ($_SESSION["permiso"] == 1): ?>
                                            <li><a class="dropdown-item" href="./admin/admin.php"><i class="fas fa-user-circle nav-icon"></i> Panel de Admin</a></li>
                                            <li><a class="dropdown-item" href="panelUsuario.php"><i class="fas fa-user-circle nav-icon"></i> Panel de usuario</a></li>
                                            <li><a class="dropdown-item" href="./admin/cerrar_sesion.php"><i class="fas fa-sign-out-alt nav-icon"></i> Cerrar Sesión</a></li>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <li><a class="dropdown-item" href="iniciarSesion.php"><i class="fas fa-sign-in-alt nav-icon"></i> Iniciar Sesión</a></li>
                                        <li><a class="dropdown-item" href="registro.php"><i class="fas fa-user-plus nav-icon"></i> Registrarse</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#carritoOffcanvas">
                    <i class="bi bi-cart"></i> Carrito <span class="badge bg-secondary" id="cart-count"></span>
                </button>
            </div>
        </div>
    </header>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="app.js"></script>
    <script src="carrito.js"></script>
</body>

</html>