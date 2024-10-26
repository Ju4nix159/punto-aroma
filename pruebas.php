<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas Especiales - Punto Aroma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .text-secondary-custom {
            color: var(--secondary-color);
        }
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        .btn-primary-custom:hover {
            background-color: #6f9430;
            border-color: #6f9430;
            color: white;
        }
        .carousel-item {
            position: relative;
        }
        .carousel-caption {
            background: rgba(0, 0, 0, 0.5);
            left: 0;
            right: 0;
            bottom: 0;
            padding: 20px;
        }
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
        }
        #ofertasCarousel {
            max-width: 1200px;
            margin: 0 auto;
        }
        .carousel-inner img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <section class="py-5 bg-primary-light">
        <div class="container">
            <h2 class="text-center mb-4 text-secondary-custom">Ofertas Especiales</h2>
            <div id="ofertasCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="./assets/banner1.jpg" class="d-block w-100" alt="Vela aromática de lavanda">
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Vela aromática de lavanda</h3>
                            <p>Relájate con el suave aroma de la lavanda. 20% de descuento por tiempo limitado.</p>
                            <a href="#" class="btn btn-primary-custom">Ver oferta</a>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="./assets/banner2.jpg" class="d-block w-100" alt="Difusor de aceites esenciales">
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Difusor de aceites esenciales</h3>
                            <p>Transforma tu hogar con fragancias naturales. Ahorra 15% en nuestros difusores premium.</p>
                            <a href="#" class="btn btn-primary-custom">Ver oferta</a>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="./assets/banner3.jpg" class="d-block w-100" alt="Set de jabones artesanales">
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Set de jabones artesanales</h3>
                            <p>Cuida tu piel con nuestros jabones naturales. Llévate un 25% de descuento en sets seleccionados.</p>
                            <a href="#" class="btn btn-primary-custom">Ver oferta</a>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="./assets/banner4.jpg" class="d-block w-100" alt="Spray ambiental de vainilla">
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Spray ambiental de vainilla</h3>
                            <p>Crea un ambiente acogedor con nuestro spray de vainilla. 10% de descuento en la segunda unidad.</p>
                            <a href="#" class="btn btn-primary-custom">Ver oferta</a>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#ofertasCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#ofertasCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var carousel = new bootstrap.Carousel(document.getElementById('ofertasCarousel'), {
                interval: 5000,
                wrap: true
            });
        });
    </script>
</body>
</html>