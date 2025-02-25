document.addEventListener("DOMContentLoaded", function () {
  cargarCarrito();
});

function cargarCarrito() {
  // Guardar el estado de los elementos colapsados
  const openCollapseId = document.querySelector(
    `.collapse.show[id^="fragancias-"]`
  )?.id;

  // Hacer la solicitud al backend para obtener el carrito
  fetch("obtener_carrito.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarCarrito(data.cart, data.total);

        // Restaurar el estado del colapso si estaba abierto
        if (openCollapseId) {
          const collapseElement = document.getElementById(openCollapseId);
          if (collapseElement) {
            const bsCollapse = new bootstrap.Collapse(collapseElement, {
              show: true,
            });
          }
        }
      } else {
        document.getElementById("cart-items").innerHTML = "";
        document.getElementById("empty-cart-message").style.display = "block";
        document.getElementById("cart-summary").style.display = "none";
      }
    })
    .catch((error) => console.error("Error al cargar el carrito:", error));
}

// Function to save the current state of collapsed elements
function saveCollapseState() {
  const openCollapses = [];
  document.querySelectorAll(".collapse.show").forEach((el) => {
    openCollapses.push(el.id);
  });
  return openCollapses;
}

// Function to restore collapse state
function restoreCollapseState() {
  // Get the collapse state from sessionStorage or from a variable
  const openCollapses = window.cartOpenCollapses || [];

  // Open all the previously opened collapses
  openCollapses.forEach((id) => {
    const collapseEl = document.getElementById(id);
    if (collapseEl) {
      new bootstrap.Collapse(collapseEl, {
        show: true,
      });
    }
  });
}

// Modify the mostrarCarrito function to add data attributes for easier updates
function mostrarCarrito(items) {
  const cartItemsContainer = document.getElementById("cart-items");
  cartItemsContainer.innerHTML = "";

  let total = 0;

  items.forEach((item) => {
    const totalCantidadFragancias = item.fragancias.reduce(
      (sum, fragancia) => sum + fragancia.cantidad,
      0
    );

    total += totalCantidadFragancias * item.precio;
    const precioPorMayor = item.esPrecioMayor ? " (Precio por mayor)" : "";
    
    const itemDiv = document.createElement("div");
    itemDiv.classList.add("cart-item", "mb-3");
    itemDiv.setAttribute("data-product-id", item.id); // Add data attribute for easier targeting

    itemDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 product-title">${item.nombre}${precioPorMayor}</h6>
                <small>Cantidad total de fragancias: <span class="cantidad-total">${totalCantidadFragancias}</span></small>
                <small class="d-block">Precio unitario: <span class="product-price">$${parseFloat(
                  item.precio
                ).toFixed(2)}</span></small>
            </div>
            <div>
                <span class="product-subtotal">$${(totalCantidadFragancias * item.precio).toFixed(2)}</span>
                <button onclick="eliminarProducto('${item.id}')" class="btn btn-danger btn-sm ms-2"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="mt-2">
            <button class="btn btn-secondary-custom p-1" type="button" data-bs-toggle="collapse" data-bs-target="#fragancias-${item.id}" aria-expanded="false" aria-controls="fragancias-${item.id}">
                Listar fragancias
            </button>
            <div class="collapse" id="fragancias-${item.id}">
                <ul class="list-group mt-2">
                    ${item.fragancias.map(
                      (fragancia) => `
                        <li class="list-group-item d-flex justify-content-between align-items-center fragancia-item" data-fragancia="${fragancia.aroma}">
                            <span>${fragancia.aroma}</span>
                            <div>
                                <button onclick="modificarCantidadFragancia('${item.id}', '${fragancia.aroma}', -1)" class="btn btn-outline-secondary btn-sm">-</button>
                                <span class="mx-2 fragancia-cantidad">${fragancia.cantidad}</span>
                                <button onclick="modificarCantidadFragancia('${item.id}', '${fragancia.aroma}', 1)" class="btn btn-outline-secondary btn-sm">+</button>
                                <button onclick="eliminarFragancia('${item.id}', '${fragancia.aroma}')" class="btn btn-danger btn-sm ms-2"><i class="fas fa-trash"></i></button>
                            </div>
                        </li>
                    `
                    ).join("")}
                </ul>
            </div>
        </div>
    `;
    cartItemsContainer.appendChild(itemDiv);
  });

  document.getElementById("empty-cart-message").style.display = items.length > 0 ? "none" : "block";
  document.getElementById("cart-summary").style.display = items.length > 0 ? "block" : "none";
  document.getElementById("cart-total").innerText = `$${total.toFixed(2)}`;
}

function eliminarProducto(productId) {
  fetch("actualizar_carrito.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      action: "eliminar_producto",
      productId: productId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        if (data.cart) {
          mostrarCarrito(data.cart);
        } else {
          cargarCarrito();
        }
      } else {
        alert(data.message);
      }
    })
    .catch((error) => {
      console.error("Error al procesar la respuesta:", error);
      cargarCarrito();
    });
}

// Apply the same pattern to eliminarFragancia and eliminarProducto functions
// Actualizar las otras funciones también
function eliminarFragancia(productId, fraganciaAroma) {
  // Guardar el estado de los elementos colapsados
  const openCollapseId = document.querySelector(
    `.collapse.show[id^="fragancias-"]`
  )?.id;

  fetch("actualizar_carrito.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      action: "eliminar_fragancia",
      productId: productId,
      fraganciaAroma: fraganciaAroma,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        if (data.cart) {
          mostrarCarrito(data.cart);

          // Restaurar el estado del colapso si estaba abierto
          if (openCollapseId) {
            const collapseElement = document.getElementById(openCollapseId);
            if (collapseElement) {
              const bsCollapse = new bootstrap.Collapse(collapseElement, {
                show: true,
              });
            }
          }
        } else {
          cargarCarrito();
        }
      } else {
        alert(data.message);
      }
    })
    .catch((error) => {
      console.error("Error al procesar la respuesta:", error);
      cargarCarrito();
    });
}

function actualizarValoresCarrito(items) {
  let total = 0;

  items.forEach((item) => {
    const productContainer = document.querySelector(
      `.cart-item[data-product-id="${item.id}"]`
    );
    if (!productContainer) return; // Skip if product container not found

    const totalCantidadFragancias = item.fragancias.reduce(
      (sum, fragancia) => sum + fragancia.cantidad,
      0
    );

    // Update total quantity display
    const cantidadTotalElement =
      productContainer.querySelector(".cantidad-total");
    if (cantidadTotalElement) {
      cantidadTotalElement.textContent = `${totalCantidadFragancias}`;
    }

    // Update price display
    const precioPorMayor = item.esPrecioMayor ? " (Precio por mayor)" : "";
    const productTitleElement =
      productContainer.querySelector(".product-title");
    if (productTitleElement) {
      productTitleElement.textContent = `${item.nombre}${precioPorMayor}`;
    }

    // Update product subtotal
    const subtotalElement = productContainer.querySelector(".product-subtotal");
    if (subtotalElement) {
      subtotalElement.textContent = `$${(
        totalCantidadFragancias * item.precio
      ).toFixed(2)}`;
    }

    // Update price info
    const priceElement = productContainer.querySelector(".product-price");
    if (priceElement) {
      priceElement.textContent = `$${parseFloat(item.precio).toFixed(2)}`;
    }

    // Update fragrance quantities
    item.fragancias.forEach((fragancia) => {
      const fraganciasItems =
        productContainer.querySelectorAll(`.fragancia-item`);

      // Find the li corresponding to this fragrance
      fraganciasItems.forEach((li) => {
        const fraganciaNombre = li.getAttribute("data-fragancia");
        if (fraganciaNombre === fragancia.aroma) {
          // Update quantity
          const cantidadEl = li.querySelector(".fragancia-cantidad");
          if (cantidadEl) {
            cantidadEl.textContent = fragancia.cantidad;
          }
        }
      });
    });

    total += totalCantidadFragancias * item.precio;
  });

  // Update cart total
  const totalElement = document.getElementById("cart-total");
  if (totalElement) {
    totalElement.innerText = `$${total.toFixed(2)}`;
  }

  // Hide/show empty cart message and summary
  document.getElementById("empty-cart-message").style.display =
    items.length > 0 ? "none" : "block";
  document.getElementById("cart-summary").style.display =
    items.length > 0 ? "block" : "none";
}

function modificarCantidadFragancia(
  productId,
  fraganciaAroma,
  cantidadModificacion
) {
  // Guardar el estado de los elementos colapsados
  const openCollapseId = document.querySelector(
    `.collapse.show[id^="fragancias-"]`
  )?.id;

  fetch("actualizar_carrito.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      action: "modificar_cantidad",
      productId: productId,
      fraganciaAroma: fraganciaAroma,
      cantidadModificacion: cantidadModificacion,
      recalcularPrecio: true,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Usar el carrito devuelto por el servidor para actualizar la UI
        if (data.cart) {
          actualizarValoresCarrito(data.cart);
        } else {
          console.error(
            "Error: el servidor no devolvió el carrito actualizado",
            data);
          cargarCarrito();
        }
      } else {
        alert(data.message || "Error al modificar la cantidad");
      }
    })
    .catch((error) => {
      console.error("Error al procesar la respuesta:", error);
      cargarCarrito();
    });
}

function incrementQuantity(sku) {
  var quantityInput = document.getElementById("quantity-" + sku);
  var currentQuantity = parseInt(quantityInput.value);
  quantityInput.value = currentQuantity + 1;
  cantidad = currentQuantity + 1;
  updateTotalPrice();
}

function decrementQuantity(sku) {
  var quantityInput = document.getElementById("quantity-" + sku);
  var currentQuantity = parseInt(quantityInput.value);
  if (currentQuantity > 0) {
    quantityInput.value = currentQuantity - 1;
    cantidad = currentQuantity - 1;
    updateTotalPrice();
  }
}

function updateTotalPrice() {
  var total = 0;
  var quantities = document.querySelectorAll(".cantidad");
  var totalQuantity = 0;

  // Obtener el precio base con verificación de existencia
  var precioBase = 0;
  var precioProductoElement = document.getElementById("precio-producto");
  if (precioProductoElement) {
    precioBase = parseFloat(precioProductoElement.value) || 0;
  }

  // Obtener los precios por cantidad con verificación de existencia
  var precio6 = 0;
  var precio48 = 0;
  var precio120 = 0;

  var precio6Element = document.getElementById("precio-6-productos");
  var precio48Element = document.getElementById("precio-48-productos");
  var precio120Element = document.getElementById("precio-120-productos");

  if (precio6Element && precio6Element.value) {
    precio6 = parseFloat(precio6Element.value) || 0;
  }

  if (precio48Element && precio48Element.value) {
    precio48 = parseFloat(precio48Element.value) || 0;
  }

  if (precio120Element && precio120Element.value) {
    precio120 = parseFloat(precio120Element.value) || 0;
  }

  // Calcular la cantidad total
  quantities.forEach(function (input) {
    totalQuantity += parseInt(input.value) || 0;
  });

  // Determinar qué precio aplicar según la cantidad total
  var precioAplicado = precioBase; // Por defecto, usar el precio base

  if (totalQuantity >= 120 && precio120 > 0) {
    precioAplicado = precio120;
    if (document.getElementById("leyendaPrecio")) {
      document.getElementById("leyendaPrecio").innerText =
        "Precio por mayor (120+ productos)";
    }
  } else if (totalQuantity >= 48 && precio48 > 0) {
    precioAplicado = precio48;
    if (document.getElementById("leyendaPrecio")) {
      document.getElementById("leyendaPrecio").innerText =
        "Precio por mayor (48+ productos)";
    }
  } else if (totalQuantity >= 6 && precio6 > 0) {
    precioAplicado = precio6;
    if (document.getElementById("leyendaPrecio")) {
      document.getElementById("leyendaPrecio").innerText =
        "Precio por mayor (6+ productos)";
    }
  } else {
    if (document.getElementById("leyendaPrecio")) {
      document.getElementById("leyendaPrecio").innerText =
        "Agregue 6 productos para tener el precio por mayor";
    }
  }

  // Calcular el total
  total = totalQuantity * precioAplicado;

  // Actualizar el precio en la interfaz con verificación
  var totalPriceElement = document.getElementById("total-price");
  if (totalPriceElement) {
    totalPriceElement.innerText = total.toFixed(2);
  }

  // Para debugging
  console.log({
    totalQuantity: totalQuantity,
    precioBase: precioBase,
    precio6: precio6,
    precio48: precio48,
    precio120: precio120,
    precioAplicado: precioAplicado,
    total: total,
  });

  return {
    totalQuantity: totalQuantity,
    precioAplicado: precioAplicado,
    total: total,
  };
}

function listarFragancias() {
  const fragancias = [];
  // Obtener todos los contenedores de fragancias
  const fragranceItems = document.querySelectorAll(".fragrance-item");

  fragranceItems.forEach((item) => {
    // Extraer datos de cada fragancia
    const sku = item.getAttribute("data-sku");
    const aroma = item.getAttribute("data-aroma");
    const cantidad = parseInt(item.querySelector(".cantidad").value) || 0; // Evita NaN si el valor es vacío

    // Crear un objeto con la información y agregarlo a la lista
    if (cantidad > 0) {
      fragancias.push({
        aroma: aroma,
        sku: sku,
        cantidad: cantidad,
      });
    }
  });
  // Imprimir la lista de fragancias en consola
  console.log(fragancias);
  // Si quieres que devuelva la lista en lugar de solo imprimirla:
  return fragancias;
}

function addToCart() {
  var nombreProducto = document.getElementById("nombre-producto").value;
  var idProducto = document.getElementById("id-producto").value;
  var fragancias = listarFragancias();
  if (fragancias.length === 0) {
    alert("Por favor, seleccione al menos una fragancia.");
    return;
  }

  var priceInfo = updateTotalPrice();
  var precioAplicado = priceInfo.precioAplicado;
  var totalQuantity = priceInfo.totalQuantity;

  var precio6 = document.getElementById("precio-6-productos")
    ? document.getElementById("precio-6-productos").value
    : null;
  var precio48 = document.getElementById("precio-48-productos")
    ? document.getElementById("precio-48-productos").value
    : null;
  var precio120 = document.getElementById("precio-120-productos")
    ? document.getElementById("precio-120-productos").value
    : null;

  var producto = {
    nombre: nombreProducto,
    precio: precioAplicado,
    precioBase: document.getElementById("precio-producto").value,
    id: idProducto, // Asegúrate de que esto sea único para cada producto
    fragancias: fragancias,
    totalQuantity: totalQuantity,
    esPrecioMayor: totalQuantity >= 6,
    precio6: precio6,
    precio48: precio48,
    precio120: precio120,
  };

  console.log("Producto a agregar:", producto);

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "anadir_carrito.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var response = JSON.parse(xhr.responseText);
      if (response.success) {
        alert(response.message);
        cargarCarrito();
      } else {
        alert(response.message);
      }
    }
  };

  xhr.send(
    JSON.stringify({
      producto: producto,
      precio6: precio6,
      precio48: precio48,
      precio120: precio120,
    })
  );
  document.querySelectorAll(".cantidad").forEach((input) => (input.value = 0));
  document.getElementById("total-price").innerText = "0.00";
  if (document.getElementById("leyendaPrecio")) {
    document.getElementById("leyendaPrecio").innerText =
      "Agregue 6 productos para tener el precio por mayor";
  }
}
