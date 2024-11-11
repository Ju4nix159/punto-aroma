<!-- carrito.php -->
<div class="position-relative">
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
</div>
<script src="carrito.js"></script>


<script>
    // Llamamos a la función loadCart cuando el documento se haya cargado
    document.addEventListener('DOMContentLoaded', function() {
        loadCart(); // Cargar el carrito
    });

    function loadCart() {
        const cartItemsContainer = document.getElementById('cart-items');
        const emptyCartMessage = document.getElementById('empty-cart-message');
        const cartSummary = document.getElementById('cart-summary');
        const cartTotal = document.getElementById('cart-total');

        // Limpiar el contenido previo
        cartItemsContainer.innerHTML = '';

        const carrito = JSON.parse(sessionStorage.getItem('carritoTemporal')) || {};
        console.log('Carrito temporal:', carrito); // Revisa el contenido del carrito temporal

        if (Object.keys(carrito).length > 0) {
            emptyCartMessage.style.display = 'none';
            cartSummary.style.display = 'block';

            let total = 0;

            // Renderizar cada producto en el carrito
            for (const id_producto in carrito) {
                const producto = carrito[id_producto];
                const fragancias = producto.fragancias;

                // Crear un elemento HTML para el producto
                const cartItem = document.createElement('div');
                cartItem.className = 'card mb-3';
                cartItem.innerHTML = `
                <div class="card-body">
                    <i class="bi bi-x-circle delete-product" onclick="deleteProduct(${id_producto})"></i>
                    <div class="product-header d-flex justify-content-between align-items-start">
                        <div class="product-info">
                            <h5 class="card-title">${producto.nombre}</h5>
                            <p class="card-text mb-0">Cantidad total: <span id="total-quantity-${id_producto}">${Object.values(fragancias).reduce((sum, f) => sum + f, 0)}</span></p>
                        </div>
                        <div class="product-price">
                            <span class="badge bg-primary-custom mb-1">$${producto.valor.toFixed(2)}/u</span>
                            <span class="fw-bold">Total: $<span id="total-price-${id_producto}">${(producto.valor * Object.values(fragancias).reduce((sum, f) => sum + f, 0)).toFixed(2)}</span></span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#fragranceDetails${id_producto}">
                        Ver fragancias <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="collapse mt-3" id="fragranceDetails${id_producto}">
                        <div class="fragrance-list">
                            ${Object.entries(fragancias).map(([sku, quantity]) => `
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Fragancia ${sku}</span>
                                    <div class="d-flex align-items-center">
                                        <div class="input-group input-group-sm me-2" style="width: 100px;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${id_producto}, ${sku}, -1)">-</button>
                                            <input type="text" class="form-control text-center" id="quantity-${id_producto}-${sku}" value="${quantity}" readonly>
                                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${id_producto}, ${sku}, 1)">+</button>
                                        </div>
                                        <i class="bi bi-trash delete-fragrance" onclick="deleteFragrance(${id_producto}, ${sku})"></i>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
                cartItemsContainer.appendChild(cartItem);
            }

            // Actualizar el total del carrito
            total = Object.values(carrito).reduce((acc, prod) => acc + (prod.valor * Object.values(prod.fragancias).reduce((sum, f) => sum + f, 0)), 0);
            cartTotal.innerText = `$${total.toFixed(2)}`;
        } else {
            emptyCartMessage.style.display = 'block';
            cartSummary.style.display = 'none';
        }
    }



    // Agregar el evento para cargar el carrito cada vez que se abre el offcanvas
    document.getElementById('carritoOffcanvas').addEventListener('show.bs.offcanvas', loadCart);
</script>