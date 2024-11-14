<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito Desplegable Corregido - Punto Aroma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #83AF37;
            --secondary-color: #6B2D5C;
        }
        body {
            background-color: #f8f9fa;
        }
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary-custom:hover {
            background-color: #6f9430;
            border-color: #6f9430;
        }
        .btn-secondary-custom {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        .btn-secondary-custom:hover {
            background-color: #5a2650;
            border-color: #5a2650;
        }
        .text-primary-custom {
            color: var(--primary-color);
        }
        .text-secondary-custom {
            color: var(--secondary-color);
        }
        .fragrance-list {
            max-height: 200px;
            overflow-y: auto;
        }
        .offcanvas {
            width: 400px;
            max-width: 100%;
        }
        .product-info {
            display: flex;
            flex-direction: column;
        }
        .product-price {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        @media (max-width: 576px) {
            .product-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .product-price {
                align-items: flex-start;
                margin-top: 0.5rem;
            }
        }
        .delete-product {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            font-size: 1.2rem;
            color: #dc3545;
            cursor: pointer;
        }
        .delete-fragrance {
            color: #dc3545;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Punto Aroma</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Sobre nosotros</a>
                    </li>
                </ul>
                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#carritoOffcanvas">
                    <i class="bi bi-cart"></i> Carrito <span class="badge bg-secondary" id="cart-count">2</span>
                </button>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="carritoOffcanvas" aria-labelledby="carritoOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="carritoOffcanvasLabel">Tu Carrito</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="cart-items">
                <!-- Los productos del carrito se generarán dinámicamente aquí -->
            </div>

            <div id="empty-cart-message" class="text-center py-4" style="display: none;">
                <i class="bi bi-cart-x" style="font-size: 3rem; color: var(--secondary-color);"></i>
                <p class="mt-3">Tu carrito está vacío</p>
            </div>

            <div id="cart-summary" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <h4 class="text-secondary-custom">Total:</h4>
                    <h4 class="text-primary-custom" id="cart-total">$0.00</h4>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button class="btn btn-primary-custom" id="checkout-button">Proceder al pago</button>
                    <button class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Seguir comprando</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let products = [
            {
                id: 1,
                name: "Vela aromática",
                unitPrice: 15.99,
                fragrances: [
                    { id: 1, name: "Lavanda", quantity: 1 },
                    { id: 2, name: "Vainilla", quantity: 2 }
                ]
            },
            {
                id: 2,
                name: "Difusor de aceites",
                unitPrice: 29.99,
                fragrances: [
                    { id: 1, name: "Eucalipto", quantity: 1 },
                    { id: 2, name: "Limón", quantity: 1 }
                ]
            }
        ];

        function renderCart() {
            const cartItemsContainer = document.getElementById('cart-items');
            const emptyCartMessage = document.getElementById('empty-cart-message');
            const cartSummary = document.getElementById('cart-summary');

            cartItemsContainer.innerHTML = '';

            if (products.length === 0) {
                emptyCartMessage.style.display = 'block';
                cartSummary.style.display = 'none';
            } else {
                emptyCartMessage.style.display = 'none';
                cartSummary.style.display = 'block';

                products.forEach(product => {
                    const productElement = document.createElement('div');
                    productElement.className = 'card mb-3 position-relative';
                    productElement.innerHTML = `
                        <div class="card-body">
                            <i class="bi bi-x-circle delete-product" onclick="deleteProduct(${product.id})"></i>
                            <div class="product-header d-flex justify-content-between align-items-start">
                                <div class="product-info">
                                    <h5 class="card-title">${product.name}</h5>
                                    <p class="card-text mb-0">Cantidad total: <span id="total-quantity-${product.id}">${product.fragrances.reduce((sum, f) => sum + f.quantity, 0)}</span></p>
                                </div>
                                <div class="product-price">
                                    <span class="badge bg-primary-custom mb-1">$${product.unitPrice.toFixed(2)}/u</span>
                                    <span class="fw-bold">Total: $<span id="total-price-${product.id}">${(product.unitPrice * product.fragrances.reduce((sum, f) => sum + f.quantity, 0)).toFixed(2)}</span></span>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#fragranceDetails${product.id}">
                                Ver fragancias <i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="collapse mt-3" id="fragranceDetails${product.id}">
                                <div class="fragrance-list">
                                    ${product.fragrances.map(fragrance => `
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>${fragrance.name}</span>
                                            <div class="d-flex align-items-center">
                                                <div class="input-group input-group-sm me-2" style="width: 100px;">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${product.id}, ${fragrance.id}, -1)">-</button>
                                                    <input type="text" class="form-control text-center" id="quantity-${product.id}-${fragrance.id}" value="${fragrance.quantity}" readonly>
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${product.id}, ${fragrance.id}, 1)">+</button>
                                                </div>
                                                <i class="bi bi-trash delete-fragrance" onclick="deleteFragrance(${product.id}, ${fragrance.id})"></i>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                    cartItemsContainer.appendChild(productElement);
                });
            }

            updateCartTotal();
        }

        function updateQuantity(productId, fragranceId, change) {
            const product = products.find(p => p.id === productId);
            if (!product) return;
            
            const fragrance = product.fragrances.find(f => f.id === fragranceId);
            if (!fragrance) return;
            
            fragrance.quantity = Math.max(0, fragrance.quantity + change);

            if (fragrance.quantity === 0) {
                deleteFragrance(productId, fragranceId);
            } else {
                document.getElementById(`quantity-${productId}-${fragranceId}`).value = fragrance.quantity;
                updateProductTotal(product);
            }
        }

        function deleteProduct(productId) {
            products = products.filter(p => p.id !== productId);
            renderCart();
        }

        function deleteFragrance(productId, fragranceId) {
            const productIndex = products.findIndex(p => p.id === productId);
            if (productIndex === -1) return;

            products[productIndex].fragrances = products[productIndex].fragrances.filter(f => f.id !== fragranceId);
            
            if (products[productIndex].fragrances.length === 0) {
                products.splice(productIndex, 1);
            }
            
            renderCart();
        }

        function updateProductTotal(product) {
            const totalQuantity = product.fragrances.reduce((sum, f) => sum + f.quantity, 0);
            const totalPrice = product.unitPrice * totalQuantity;
            const totalQuantityElement = document.getElementById(`total-quantity-${product.id}`);
            const totalPriceElement = document.getElementById(`total-price-${product.id}`);
            
            if (totalQuantityElement) totalQuantityElement.textContent = totalQuantity;
            if (totalPriceElement) totalPriceElement.textContent = totalPrice.toFixed(2);
            
            updateCartTotal();
        }

        function updateCartTotal() {
            const total = products.reduce((sum, product) => {
                return sum + product.unitPrice * product.fragrances.reduce((fSum, f) => fSum + f.quantity, 0);
            }, 0);
            document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`;
            document.getElementById('cart-count').textContent = products.length;
        }

        // Inicializar el carrito
        renderCart();
    </script>
</body>
</html>


<!-- <script>
        let products = [{
                id: 1,
                name: "Vela aromática",
                unitPrice: 15.99,
                fragrances: [{
                        id: 1,
                        name: "Lavanda",
                        quantity: 1
                    },
                    {
                        id: 2,
                        name: "Vainilla",
                        quantity: 2
                    }
                ]
            },
            {
                id: 2,
                name: "Difusor de aceites",
                unitPrice: 29.99,
                fragrances: [{
                        id: 1,
                        name: "Eucalipto",
                        quantity: 1
                    },
                    {
                        id: 2,
                        name: "Limón",
                        quantity: 1
                    }
                ]
            }
        ];

        function renderCart() {
            const cartItemsContainer = document.getElementById('cart-items');
            const emptyCartMessage = document.getElementById('empty-cart-message');
            const cartSummary = document.getElementById('cart-summary');

            cartItemsContainer.innerHTML = '';

            if (products.length === 0) {
                emptyCartMessage.style.display = 'block';
                cartSummary.style.display = 'none';
            } else {
                emptyCartMessage.style.display = 'none';
                cartSummary.style.display = 'block';

                products.forEach(product => {
                    const productElement = document.createElement('div');
                    productElement.className = 'card mb-3 position-relative';
                    productElement.innerHTML = `
                        <div class="card-body">
                            <i class="bi bi-x-circle delete-product" onclick="deleteProduct(${product.id})"></i>
                            <div class="product-header d-flex justify-content-between align-items-start">
                                <div class="product-info">
                                    <h5 class="card-title">${product.name}</h5>
                                    <p class="card-text mb-0">Cantidad total: <span id="total-quantity-${product.id}">${product.fragrances.reduce((sum, f) => sum + f.quantity, 0)}</span></p>
                                </div>
                                <div class="product-price">
                                    <span class="badge bg-primary-custom mb-1">$${product.unitPrice.toFixed(2)}/u</span>
                                    <span class="fw-bold">Total: $<span id="total-price-${product.id}">${(product.unitPrice * product.fragrances.reduce((sum, f) => sum + f.quantity, 0)).toFixed(2)}</span></span>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#fragranceDetails${product.id}">
                                Ver fragancias <i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="collapse mt-3" id="fragranceDetails${product.id}">
                                <div class="fragrance-list">
                                    ${product.fragrances.map(fragrance => `
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>${fragrance.name}</span>
                                            <div class="d-flex align-items-center">
                                                <div class="input-group input-group-sm me-2" style="width: 100px;">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${product.id}, ${fragrance.id}, -1)">-</button>
                                                    <input type="text" class="form-control text-center" id="quantity-${product.id}-${fragrance.id}" value="${fragrance.quantity}" readonly>
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${product.id}, ${fragrance.id}, 1)">+</button>
                                                </div>
                                                <i class="bi bi-trash delete-fragrance" onclick="deleteFragrance(${product.id}, ${fragrance.id})"></i>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                    cartItemsContainer.appendChild(productElement);
                });
            }

            updateCartTotal();
        }

        function updateQuantity(productId, fragranceId, change) {
            const product = products.find(p => p.id === productId);
            if (!product) return;

            const fragrance = product.fragrances.find(f => f.id === fragranceId);
            if (!fragrance) return;

            fragrance.quantity = Math.max(0, fragrance.quantity + change);

            if (fragrance.quantity === 0) {
                deleteFragrance(productId, fragranceId);
            } else {
                document.getElementById(`quantity-${productId}-${fragranceId}`).value = fragrance.quantity;
                updateProductTotal(product);
            }
        }

        function deleteProduct(productId) {
            products = products.filter(p => p.id !== productId);
            renderCart();
        }

        function deleteFragrance(productId, fragranceId) {
            const productIndex = products.findIndex(p => p.id === productId);
            if (productIndex === -1) return;

            products[productIndex].fragrances = products[productIndex].fragrances.filter(f => f.id !== fragranceId);

            if (products[productIndex].fragrances.length === 0) {
                products.splice(productIndex, 1);
            }

            renderCart();
        }

        function updateProductTotal(product) {
            const totalQuantity = product.fragrances.reduce((sum, f) => sum + f.quantity, 0);
            const totalPrice = product.unitPrice * totalQuantity;
            const totalQuantityElement = document.getElementById(`total-quantity-${product.id}`);
            const totalPriceElement = document.getElementById(`total-price-${product.id}`);

            if (totalQuantityElement) totalQuantityElement.textContent = totalQuantity;
            if (totalPriceElement) totalPriceElement.textContent = totalPrice.toFixed(2);

            updateCartTotal();
        }

        function updateCartTotal() {
            const total = products.reduce((sum, product) => {
                return sum + product.unitPrice * product.fragrances.reduce((fSum, f) => fSum + f.quantity, 0);
            }, 0);
            document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`;
            document.getElementById('cart-count').textContent = products.length;
        }

        // Inicializar el carrito
        renderCart();
    </script>
    
    CODIGO PARA MOSTRAR EL CARRITO -->