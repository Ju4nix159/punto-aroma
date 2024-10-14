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
                <form id="registerForm">
                    <div class="mb-3">
                        <label for="registerName" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="registerName" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="registerEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="registerPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerConfirmPassword" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="registerConfirmPassword" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">Acepto los términos y condiciones</label>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100">Registrarse</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="py-3 bg-primary-light mt-5">
        <div class="container">
            <p class="text-center text-muted mb-0">&copy; 2024 Punto Aroma. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica de inicio de sesión
            console.log('Intento de inicio de sesión');
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica de registro
            console.log('Intento de registro');
        });
    </script>
</body>

</html>