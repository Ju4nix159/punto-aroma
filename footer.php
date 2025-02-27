<!DOCTYPE html>
<html lang="es">
<head>
    <style>
        :root {
            --primary-color: #83AF37;
            --secondary-color: #6B2D5C;
        }
        .bg-primary-light {
            background-color: rgba(131, 175, 55, 0.1);
        }
        .text-primary-custom {
            color: var(--primary-color);
        }
        .btn-primary-custom {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
            transition: transform 0.3s ease;
        }
        .btn-primary-custom:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
            transform: scale(1.05);
        }
        footer {
            background-color: #f8f9fa;
            padding: 3rem 0;
            font-size: 0.9rem;
        }
        footer h5 {
            color: var(--secondary-color);
            font-weight: bold;
            margin-bottom: 1rem;
        }
        footer ul {
            list-style-type: none;
            padding-left: 0;
        }
        footer ul li {
            margin-bottom: 0.5rem;
        }
        footer a {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        footer a:hover {
            color: var(--primary-color);
        }
        .social-icons a {
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        .newsletter-form .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .newsletter-form .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
    </style>
</head>
<body>
    <footer class="bg-primary-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5 class="text-primary-custom">Punto Aroma</h5>
                    <p>Descubre la magia de los aromas y transforma tu espacio con nuestros productos de alta calidad.</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5>Enlaces Rápidos</h5>
                    <ul>
                        <li><a href="index.php#hero">Inicio</a></li>
                        <li><a href="catalogo.php">Catálogo</a></li>
                        <li><a href="nosotros.php">Sobre Nosotros</a></li>
                        <li><a href="index.php#contacto">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5>Contacto</h5>
                    <ul>
                        <li><i class="bi bi-geo-alt-fill me-2"></i>Esperanza 1572 , Rio Tercero</li>
                        <li><i class="bi bi-telephone-fill me-2"></i>351 755-0374</li>
                        <!-- <li><i class="bi bi-envelope-fill me-2"></i>info@puntoaroma.com</li> -->
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5>Síguenos</h5>
                    <div class="social-icons mb-3">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                    </div>
                    <h5>Boletín</h5>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Tu email" aria-label="Tu email" required>
                            <button class="btn btn-primary-custom" type="submit">Suscribir</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; 2024 Punto Aroma. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#">Términos y Condiciones</a></li>
                        <li class="list-inline-item"><a href="#">Política de Privacidad</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>