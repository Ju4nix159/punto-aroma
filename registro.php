<?php
include 'header.php';

?>

<!DOCTYPE html>

<head>
    <title>Registro</title>
</head>

<body>

    <main class="py-5 bg-primary-light">
        <div class="container">
            <div class="form-container bg-white p-4 rounded shadow">
                <h2 class="text-center mb-4 text-primary-custom">Registrarse</h2>
                <form id="registerForm" action="admin/sesion.php" method="POST">
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="registerEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="registerPassword" name="clave" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerConfirmPassword" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="registerConfirmPassword" name="confirmarClave" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">Acepto los términos y condiciones</label>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100" name="registrar_usuario" >Registrarse</button>
                    <p class="mt-3 text-center">¿Ya tienes una cuenta? <a href="iniciarSesion.php" class="text-primary-custom">Inicia sesión aquí</a></p>
                </form>
            </div>
        </div>
        <div class="modal fade" id="modalSuccess" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-primary-custom">Registro de usuarios exitoso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Bienvenido a Punto Aroma. Iniciar sesion con sus usuario registrado para poder empezar a comprar</p>
                    </div>
                    <div class="modal-footer border-0">
                        <a href="./iniciarSesion.php"><button type="button" class="btn btn-primary-custom" data-bs-dismiss="modal">Iniciar Sesion</button></a>
                    </div>
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
                        <h2 class="mb-3 text-secondary-custom">Error de inicio de sesión(usuario ya registrado)</h2>
                        <p class="text-muted mb-4">Usuario ya registado en la base de datos , por favor inicie sesion</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary-custom w-100" data-bs-dismiss="modal">Iniciar Sesion</button>
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