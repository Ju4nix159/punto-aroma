<?php 
    include 'header.php';

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <title>Iniciar Sesión Requerido - Punto Aroma</title>
    <style>
        .login-required-card {
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="login-required-card bg-white">
                        <h1 class="text-center mb-4 text-primary-custom">Iniciar Sesión Requerido</h1>
                        <div class="text-center mb-4">
                            <i class="bi bi-lock-fill text-secondary" style="font-size: 3rem;"></i>
                        </div>
                        <p class="text-center mb-4">
                            Para acceder a esta sección, es necesario iniciar sesión en tu cuenta de Punto Aroma.
                        </p>
                        <div class="d-flex justify-content-center">
                            <a href="/pa/iniciarSesion.php" class="btn btn-primary-custom">
                                Iniciar Sesión
                            </a>
                        </div>
                        <hr class="my-4">
                        <p class="text-center mb-0">
                            ¿No tienes una cuenta? <a href="/pa/registro.php" class="text-primary-custom">Regístrate aquí</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="">
        <?php include 'footer.php'; ?>
    </footer>

</body>
</html>