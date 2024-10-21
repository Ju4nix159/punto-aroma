<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto Aroma - Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #83AF37;
            --secondary-color: #6B2D5C;
        }
        .card {
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-img-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .card:hover .card-img-overlay {
            opacity: 1;
        }
        .card-title, .card-text, .btn-primary-custom {
            transform: translateY(20px);
            transition: transform 0.3s ease, opacity 0.3s ease;
            opacity: 0;
        }
        .card:hover .card-title,
        .card:hover .card-text,
        .card:hover .btn-primary-custom {
            transform: translateY(0);
            opacity: 1;
        }
        .btn-primary-custom {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .btn-primary-custom:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div class="col">
                <div class="card text-white">
                    <img src="i1.jpg" class="card-img" alt="Sahumerios Vishnu Masala">
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-title">Sahumerios Vishnu Masala</h5>
                        <p class="card-text">Perfumes, flores y fibras vegetales de alta calidad. Aromas: Antiestrés, Energía, Relajación, Sensual, Meditación, Frescura El aroma perdura por más tiempo en el ambiente.</p>
                        <a href="#" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white">
                    <img src="i2.jpg" class="card-img" alt="Sahumerios Holi India">
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-title">Sahumerios Holi India</h5>
                        <p class="card-text">Renovamos la línea de sahumerios Holi India Pack con más color y los excelentes aromas premium de siempre. Presentación aromas surtido x 100 unidades.</p>
                        <a href="#" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white">
                    <img src="i3.jpg" class="card-img" alt="Aromatizantes textiles">
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-title">Aromatizantes textiles</h5>
                        <p class="card-text">Perfume para aromatizar ropa y ambientes. Sentirás bien fresco un aroma especial.</p>
                        <a href="#" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white">
                    <img src="i4.png" class="card-img" alt="Aceites para hornito">
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-title">Aceites para hornito</h5>
                        <p class="card-text">Nuevos aromas y nueva presentación x 5 unidades. Más variedad por el mismo precio.</p>
                        <a href="#" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>