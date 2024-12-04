<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfume Floral - Punto Aroma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            background-color: #8E4B9E;
            border-color: #8E4B9E;
            color: white;
        }

        .btn-custom:hover {
            background-color: #7A3F87;
            border-color: #7A3F87;
            color: white;
        }

        .text-custom {
            color: #8E4B9E;
        }

        .btn-quantity {
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="text-custom">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-custom">Catálogo</a></li>
                <li class="breadcrumb-item active" aria-current="page">Perfume Floral</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Image -->
            <div class="col-md-6 mb-4">
                <img src="https://via.placeholder.com/600" alt="Perfume Floral" class="img-fluid rounded">
            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <h1 class="mb-3">Perfume Floral</h1>
                <p class="text-muted">Disfruta de la calidez y el aroma relajante de nuestras velas aromáticas de alta calidad.</p>
                <h2 class="mb-3">$600.25</h2>
                <p class="mb-4">Elige entre nuestras diferentes fragancias y personaliza tu experiencia aromática.</p>

                <div class="card mb-4">
                    <div class="card-body">
                        <div id="variants">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Variante Floral</strong>
                                        <p class="text-muted mb-0 small">Aroma: Rosa | Color: Rojo</p>
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-custom btn-quantity me-2">-</button>
                                        <span>0</span>
                                        <button class="btn btn-custom btn-quantity ms-2">+</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="h4" id="total">Total: $0.00</span>
                    <span class="text-muted" id="discount-message">Te faltan 10 producto(s) para obtener descuento por mayor</span>
                </div>

                <button class="btn btn-custom btn-lg w-100">Agregar al carrito</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const variants = {
            'Tierra Húmeda': {
                name: 'Tierra Húmeda',
                aroma: 'Menta',
                color: 'Marrón'
            },
            'Brisa Marina': {
                name: 'Brisa Marina',
                aroma: 'Océano',
                color: 'Azul'
            },
            'Bosque Encantado': {
                name: 'Bosque Encantado',
                aroma: 'Pino',
                color: 'Verde'
            },
            'Atardecer Cálido': {
                name: 'Atardecer Cálido',
                aroma: 'Vainilla',
                color: 'Naranja'
            },
            'Luna Llena': {
                name: 'Luna Llena',
                aroma: 'Lavanda',
                color: 'Púrpura'
            }
        };

        const quantities = {};
        let total = 0;
        let remainingForDiscount = 10;

        function updateQuantity(variant, increment) {
            quantities[variant] = (quantities[variant] || 0) + (increment ? 1 : -1);
            if (quantities[variant] < 0) quantities[variant] = 0;
            updateDisplay();
        }

        function updateDisplay() {
            total = 0;
            let totalQuantity = 0;
            for (const [variant, quantity] of Object.entries(quantities)) {
                document.getElementById(`quantity-${variant}`).textContent = quantity;
                total += quantity * 600.25;
                totalQuantity += quantity;
            }
            document.getElementById('total').textContent = `Total: $${total.toFixed(2)}`;
            remainingForDiscount = Math.max(0, 10 - totalQuantity);
            document.getElementById('discount-message').textContent =
                remainingForDiscount > 0 ?
                `Te faltan ${remainingForDiscount} producto(s) para obtener descuento por mayor` :
                "¡Has alcanzado el descuento por mayor!";
        }

        function createVariantHTML(key, variant) {
            return `
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${variant.name}</strong>
                            <p class="text-muted mb-0 small">Aroma: ${variant.aroma} | Color: ${variant.color}</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-custom btn-quantity me-2" onclick="updateQuantity('${key}', false)">-</button>
                            <span id="quantity-${key}">0</span>
                            <button class="btn btn-custom btn-quantity ms-2" onclick="updateQuantity('${key}', true)">+</button>
                        </div>
                    </div>
                </div>
            `;
        }

        window.onload = function() {
            const variantsContainer = document.getElementById('variants');
            for (const [key, variant] of Object.entries(variants)) {
                variantsContainer.innerHTML += createVariantHTML(key, variant);
                quantities[key] = 0;
            }
            updateDisplay();
        };
    </script>
</body>

</html>