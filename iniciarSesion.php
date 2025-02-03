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
                <form id="loginForm" action="./admin/sesion.php" method="POST">
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
                    <span class="mx-2">|</span>
                    <a href="./registro.php" class="text-decoration-none text-primary-custom">Registrarte</a>
                </div>
            </div>
        </div>
        
        <!-- Modal de error -->
        <div class="modal fade" id="modalError" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content auth-container text-center">
                    <div class="modal-body">
                        <div class="icon-circle bg-secondary-custom">
                            <i class="bi bi-x-lg"></i>
                        </div>
                        <h2 class="mb-3 text-secondary-custom">Error de inicio de sesión</h2>
                        <p class="text-muted mb-4">Usuario o contraseña incorrectos. Por favor, inténtalo de nuevo.</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary-custom w-100" data-bs-dismiss="modal">Volver a intentar</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <footer class="">
        <?php include 'footer.php'; ?>
    </footer>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            const successModal = new bootstrap.Modal(document.getElementById('modalSuccess'));
            successModal.show();
        } else if (status === 'error') {
            const errorModal = new bootstrap.Modal(document.getElementById('modalError'));
            errorModal.show();
        }
    </script>
</body>

</html>