document.addEventListener("DOMContentLoaded", function () {
  cargarCarrito();
});
let openCollapses = new Set();

function guardarEstadoColapsables() {
  openCollapses = new Set();
  document.querySelectorAll(".collapse.show").forEach((collapse) => {
    openCollapses.add(collapse.id);
  });
}

function restaurarEstadoColapsables() {
  openCollapses.forEach((id) => {
    const collapseElement = document.getElementById(id);
    if (collapseElement) {
      new bootstrap.Collapse(collapseElement, {
        show: true,
      });
    }
  });
}

function cargarCarrito() {
  // Hacer la solicitud al backend para obtener el carrito
  fetch("obtener_carrito.php")
    .then((response) => response.json())
    .then((data) => {
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
    .catch((error) => console.error("Error al cargar el carrito:", error));
}

function mostrarCarrito(items) {
  const cartItemsContainer = document.getElementById("cart-items");
  cartItemsContainer.innerHTML = "";

  let total = 0; // Inicializamos el total en 0

  items.forEach((item, index) => {
    const totalCantidadFragancias = item.fragancias.reduce(
      (sum, fragancia) => sum + fragancia.cantidad,
      0
    );

    // Actualizar el total sumando la cantidad total de fragancias * precio del producto
    total += totalCantidadFragancias * item.precio;

    const itemDiv = document.createElement("div");
    itemDiv.classList.add("cart-item", "mb-3");

    itemDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">${item.nombre}</h6>
                <small>Cantidad total de fragancias: ${totalCantidadFragancias}</small>
            </div>
            <div>
                <span>$${(totalCantidadFragancias * item.precio).toFixed(
                  2
                )}</span>
                <button onclick="eliminarProducto('${
                  item.id
                }')" class="btn btn-danger btn-sm ms-2"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="mt-2">
            <button class="btn btn-secondary-custom p-1" type="button" data-bs-toggle="collapse" data-bs-target="#fragancias-${index}" aria-expanded="false" aria-controls="fragancias-${index}">
                Listar fragancias
            </button>
            <div class="collapse" id="fragancias-${index}">
                <ul class="list-group mt-2">
                    ${item.fragancias
                      .map(
                        (fragancia) => `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${fragancia.aroma}</span>
                            <div>
                                <button onclick="modificarCantidadFragancia('${item.id}', '${fragancia.aroma}', -1)" class="btn btn-outline-secondary btn-sm">-</button>
                                <span class="mx-2">${fragancia.cantidad}</span>
                                <button onclick="modificarCantidadFragancia('${item.id}', '${fragancia.aroma}', 1)" class="btn btn-outline-secondary btn-sm">+</button>
                                <button onclick="eliminarFragancia('${item.id}', '${fragancia.aroma}')" class="btn btn-danger btn-sm ms-2"><i class="fas fa-trash"></i></button>
                            </div>
                        </li>
                    `
                      )
                      .join("")}
                </ul>
            </div>
        </div>
    `;
    cartItemsContainer.appendChild(itemDiv);
  });

  document.getElementById("empty-cart-message").style.display = "none";
  document.getElementById("cart-summary").style.display = "block";
  document.getElementById("cart-total").innerText = `$${total.toFixed(2)}`; // Mostrar el total actualizado
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
        cargarCarrito();
      } else {
        alert(data.message);
      }
    });
}

function modificarCantidadFragancia(
  productId,
  fraganciaAroma,
  cantidadModificacion
) {
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
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        cargarCarrito();
      } else {
        alert(data.message);
      }
    });
}

function incrementQuantity(sku) {
  var quantityInput = document.getElementById("quantity-" + sku);
  var currentQuantity = parseInt(quantityInput.value);
  quantityInput.value = currentQuantity + 1;
  updateTotalPrice();
}

function decrementQuantity(sku) {
  var quantityInput = document.getElementById("quantity-" + sku);
  var currentQuantity = parseInt(quantityInput.value);
  if (currentQuantity > 0) {
    quantityInput.value = currentQuantity - 1;
    updateTotalPrice();
  }
}

function updateTotalPrice() {
  var total = 0;
  var quantities = document.querySelectorAll(".cantidad");
  var precio = document.getElementById("precio-producto").value;
  quantities.forEach(function (input) {
    var quantity = parseInt(input.value);
    total += quantity * precio;
  });
  document.getElementById("total-price").innerText = total.toFixed(2);
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
  var precioProducto = parseFloat(
    document.getElementById("precio-producto").value
  );
  var idProducto = document.getElementById("id-producto").value;
  var fragancias = listarFragancias();
  if (fragancias.length === 0) {
    alert("Por favor, seleccione al menos una fragancia.");
    return;
  }

  var producto = {
    nombre: nombreProducto,
    precio: precioProducto,
    id: idProducto,
    fragancias: fragancias,
  };

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "añadir_carrito.php", true);
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
    })
  );
  document.querySelectorAll(".cantidad").forEach((input) => (input.value = 0));
}
