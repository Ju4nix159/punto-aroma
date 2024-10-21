<?php
include 'header.php';

?>

<!DOCTYPE html>

<head>
    <title>Iniciar Sesión </title>
</head>

<body>

    <main class="py-5 bg-primary-light">
        <div class="container">
            <div class="form-container bg-white p-4 rounded shadow">
                <h2 class="text-center mb-4 text-primary-custom">Iniciar Sesión</h2>
                <form id="loginForm" action="admin/sesion.php" method="POST">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="loginPassword" name="clave" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Recordarme</label>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100" name="inicar_sesion">Iniciar Sesión</button>
                </form>
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none text-primary-custom">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="">
        <?php include 'footer.php'; ?>
    </footer>
</body>

</html>