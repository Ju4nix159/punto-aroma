<!-- carrito.php -->
<div class="position-relative">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="carritoOffcanvas" aria-labelledby="carritoOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="carritoOffcanvasLabel">Tu Carrito</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="cart-items">

            </div>
            <div id="empty-cart-message" class="text-center py-4">
                <i class="bi bi-cart-x" style="font-size: 3rem; color: var(--secondary-color);"></i>
                <p class="mt-3">Tu carrito está vacío</p>
                <a href="./catalogo.php" class="btn btn-primary-custom">explorar catalogo</a>
            </div>
            <div id="cart-summary">
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
    document.addEventListener("DOMContentLoaded", function() {
        cargarCarrito();
    });
    let openCollapses = new Set();

    function guardarEstadoColapsables() {
        openCollapses = new Set();
        document.querySelectorAll(".collapse.show").forEach(collapse => {
            openCollapses.add(collapse.id);
        });
    }

    function restaurarEstadoColapsables() {
        openCollapses.forEach(id => {
            const collapseElement = document.getElementById(id);
            if (collapseElement) {
                new bootstrap.Collapse(collapseElement, {
                    show: true
                });
            }
        });
    }

    function cargarCarrito() {
        // Hacer la solicitud al backend para obtener el carrito
        fetch("obtener_carrito.php")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    guardarEstadoColapsables(); // Guarda el estado antes de actualizar el carrito
                    mostrarCarrito(data.cart, data.total);
                    restaurarEstadoColapsables();
                } else {
                    document.getElementById("cart-items").innerHTML = "";
                    document.getElementById("empty-cart-message").style.display = "block";
                    document.getElementById("cart-summary").style.display = "none";
                }
            })
            .catch(error => console.error("Error al cargar el carrito:", error));
    }

    function mostrarCarrito(items, total) {
        const cartItemsContainer = document.getElementById("cart-items");
        cartItemsContainer.innerHTML = "";

        items.forEach((item, index) => {
            const totalCantidadFragancias = item.fragancias.reduce((sum, fragancia) => sum + fragancia.cantidad, 0);

            const itemDiv = document.createElement("div");
            itemDiv.classList.add("cart-item", "mb-3");

            itemDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">${item.nombre}</h6>
                    <small>Cantidad total de fragancias: ${totalCantidadFragancias}</small>
                </div>
                <div>
                    <span>$${item.precio.toFixed(2)}</span>
                    <button onclick="eliminarProducto('${item.id}')" class="btn btn-danger btn-sm ms-2">Eliminar producto</button>
                </div>
            </div>
            <div class="mt-2">
                <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#fragancias-${index}" aria-expanded="false" aria-controls="fragancias-${index}">
                    Listar fragancias
                </button>
                <div class="collapse" id="fragancias-${index}">
                    <ul class="list-group mt-2">
                        ${item.fragancias.map(fragancia => `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${fragancia.aroma}</span>
                                <div>
                                    <button onclick="modificarCantidadFragancia('${item.id}', '${fragancia.aroma}', -1)" class="btn btn-outline-secondary btn-sm">-</button>
                                    <span class="mx-2">${fragancia.cantidad}</span>
                                    <button onclick="modificarCantidadFragancia('${item.id}', '${fragancia.aroma}', 1)" class="btn btn-outline-secondary btn-sm">+</button>
                                    <button onclick="eliminarFragancia('${item.id}', '${fragancia.aroma}')" class="btn btn-danger btn-sm ms-2">Eliminar</button>
                                </div>
                            </li>
                        `).join("")}
                    </ul>
                </div>
            </div>
        `;
            cartItemsContainer.appendChild(itemDiv);
        });

        document.getElementById("empty-cart-message").style.display = "none";
        document.getElementById("cart-summary").style.display = "block";
        document.getElementById("cart-total").innerText = `$${total}`;
    }

    function eliminarProducto(productId) {
        fetch("actualizar_carrito.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    action: "eliminar_producto",
                    productId: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarCarrito();
                } else {
                    alert(data.message);
                }
            });
    }

    function eliminarFragancia(productId, fraganciaAroma) {
        fetch("actualizar_carrito.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    action: "eliminar_fragancia",
                    productId: productId,
                    fraganciaAroma: fraganciaAroma
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarCarrito();
                } else {
                    alert(data.message);
                }
            });
    }

    function modificarCantidadFragancia(productId, fraganciaAroma, cantidadModificacion) {
        fetch("actualizar_carrito.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    action: "modificar_cantidad",
                    productId: productId,
                    fraganciaAroma: fraganciaAroma,
                    cantidadModificacion: cantidadModificacion
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarCarrito();
                } else {
                    alert(data.message);
                }
            });
    }
</script>