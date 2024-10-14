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
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="loginEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="loginPassword" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Recordarme</label>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100">Iniciar Sesión</button>
                </form>
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none text-primary-custom">¿Olvidaste tu contraseña?</a>
                </div>
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