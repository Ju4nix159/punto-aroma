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
                </form>
            </div>
        </div>
    </main>

    <footer class="">
        <?php include 'footer.php'; ?>
    </footer>
</body>

</html>