<?php 
    include 'header.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión / Registrarse - Punto Aroma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <header class="py-3 bg-white border-bottom">
        <div class="container d-flex flex-wrap justify-content-center">
            <a href="/" class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto text-dark text-decoration-none">
                <span class="fs-4 fw-bold text-primary-custom">Punto Aroma</span>
            </a>
        </div>
    </header>

    <main class="py-5 bg-primary-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
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
                <div class="col-md-6">
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
            </div>
        </div>
    </main>

    <footer class="py-3 bg-primary-light mt-5">
        <div class="container">
            <p class="text-center text-muted mb-0">&copy; 2024 Punto Aroma. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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