function loadCart() {
  const cartItemsContainer = document.getElementById("cart-items");
  const emptyCartMessage = document.getElementById("empty-cart-message");
  const cartSummary = document.getElementById("cart-summary");
  const cartTotal = document.getElementById("cart-total");

  // Limpiar el contenido previo
  cartItemsContainer.innerHTML = "";

  const carrito = JSON.parse(sessionStorage.getItem("carritoTemporal")) || {};

  if (Object.keys(carrito).length > 0) {
    emptyCartMessage.style.display = "none";
    cartSummary.style.display = "block";

    let total = 0;

    // Renderizar cada producto en el carrito
    for (const id_producto in carrito) {
      const producto = carrito[id_producto];
      const fragancias = producto.fragancias;

      // Crear un elemento HTML para el producto
      const cartItem = document.createElement("div");
      cartItem.className = "card mb-3";
      cartItem.innerHTML = `
                <div class="card-body">
                    <i class="bi bi-x-circle delete-product" onclick="deleteProduct(${id_producto})"></i>
                    <div class="product-header d-flex justify-content-between align-items-start">
                        <div class="product-info">
                            <h5 class="card-title">${producto.nombre}</h5>
                            <p class="card-text mb-0">Cantidad total: <span id="total-quantity-${id_producto}">${Object.values(
        fragancias
      ).reduce((sum, f) => sum + f, 0)}</span></p>
                        </div>
                        <div class="product-price">
                            <span class="badge bg-primary-custom mb-1">$${producto.valor.toFixed(
                              2
                            )}/u</span>
                            <span class="fw-bold">Total: $<span id="total-price-${id_producto}">${(
        producto.valor *
        Object.values(fragancias).reduce((sum, f) => sum + f, 0)
      ).toFixed(2)}</span></span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#fragranceDetails${id_producto}">
                        Ver fragancias <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="collapse mt-3" id="fragranceDetails${id_producto}">
                        <div class="fragrance-list">
                            ${Object.entries(fragancias)
                              .map(
                                ([sku, quantity]) => `
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
                            `
                              )
                              .join("")}
                        </div>
                    </div>
                </div>
            `;
      cartItemsContainer.appendChild(cartItem);
    }

    // Actualizar el total del carrito
    total = Object.values(carrito).reduce(
      (acc, prod) =>
        acc +
        prod.valor *
          Object.values(prod.fragancias).reduce((sum, f) => sum + f, 0),
      0
    );
    cartTotal.innerText = `$${total.toFixed(2)}`;
  } else {
    emptyCartMessage.style.display = "block";
    cartSummary.style.display = "none";
  }
}
function deleteProduct(id_producto) {
  const carrito = JSON.parse(sessionStorage.getItem("carritoTemporal")) || {};

  if (carrito[id_producto]) {
    delete carrito[id_producto];
    sessionStorage.setItem("carritoTemporal", JSON.stringify(carrito));
    loadCart();
  }
}

function deleteFragrance(id_producto, sku) {
  const carrito = JSON.parse(sessionStorage.getItem("carritoTemporal")) || {};

  if (carrito[id_producto] && carrito[id_producto].fragancias[sku]) {
    delete carrito[id_producto].fragancias[sku];

    // Si no quedan fragancias, eliminamos el producto del carrito
    if (Object.keys(carrito[id_producto].fragancias).length === 0) {
      delete carrito[id_producto];
    }

    sessionStorage.setItem("carritoTemporal", JSON.stringify(carrito));
    loadCart(); // Refresca la vista del carrito
  }
}

function updateQuantity(id_producto, sku, increment) {
  const carrito = JSON.parse(sessionStorage.getItem("carritoTemporal")) || {};

  if (carrito[id_producto] && carrito[id_producto].fragancias[sku]) {
    carrito[id_producto].fragancias[sku] = Math.max(
      carrito[id_producto].fragancias[sku] + increment,
      0
    );

    // Si la cantidad llega a 0, eliminamos la fragancia
    if (carrito[id_producto].fragancias[sku] === 0) {
      delete carrito[id_producto].fragancias[sku];
    }

    // Si no quedan fragancias, eliminamos el producto
    if (Object.keys(carrito[id_producto].fragancias).length === 0) {
      delete carrito[id_producto];
    }

    // Guardar el carrito actualizado en sessionStorage
    sessionStorage.setItem("carritoTemporal", JSON.stringify(carrito));
    loadCart(); // Actualizar el carrito
  }
}

document.addEventListener("DOMContentLoaded", function () {
  loadCart();
});
