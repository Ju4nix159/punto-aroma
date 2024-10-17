// Función para actualizar el modal de vista rápida
function updateQuickViewModal(button) {
  const productName = button.getAttribute("data-product-name");
  const productDescription = button.getAttribute("data-product-description");
  const productPrice = button.getAttribute("data-product-price");

  // Actualizar el contenido del modal
  document.getElementById("quickViewTitle").textContent = productName;
  document.getElementById("quickViewDescription").textContent =
    productDescription;
  document.getElementById("quickViewPrice").textContent = productPrice;

  /* document.getElementById("quickViewImage").src = productImage; */

  /* const fragrancesList = document.getElementById("quickViewFragrances");
  fragrancesList.innerHTML = ""; // Limpiar la lista anterior
  productFragrances.forEach((fragrance) => {
    const li = document.createElement("li");
    li.textContent = fragrance;
    fragrancesList.appendChild(li);
  }); */
}

// Event listener para los botones de vista rápida
document.querySelectorAll(".quick-view-btn").forEach((button) => {
  button.addEventListener("click", function () {
    updateQuickViewModal(this);
  });
});

// Detener la propagación del evento de clic en los botones
document
  .querySelectorAll(".add-to-cart-btn, .quick-view-btn")
  .forEach((button) => {
    button.addEventListener("click", function (event) {
      event.stopPropagation(); // Evita que el clic se propague al enlace
    });
  });


function toggleCart(event) {
  event.stopPropagation();
  const cart = document.getElementById("cart");
  const dropdownMenu = document.querySelector(".dropdown-menu.show");
  if (dropdownMenu) {
    dropdownMenu.classList.remove("show");
    const dropdownToggle = document.querySelector(".dropdown-toggle");
    dropdownToggle.setAttribute("aria-expanded", "false");
  }
  cart.classList.toggle("hidden");
  updateCart();
}

// Cierra el carrito si se hace clic fuera
document.addEventListener("click", function (event) {
  const cart = document.getElementById("cart");
  const cartIcon = document.getElementById("cart-icon");
  if (!cart.contains(event.target) && !cartIcon.contains(event.target)) {
    cart.classList.add("hidden");
  }
});

document.getElementById("cart").addEventListener("click", function (event) {
  event.stopPropagation();
});

function updateCart() {
    const cartContent = document.getElementById("cart-content");

    if (cartItems.length === 0) {
        cartContent.innerHTML = `
            <div class="empty-cart">
                <p>Tu carrito está vacío.</p>
            </div>`;
    }
}

function añadirCarrito(producto, nombre, precio) {
    let cartItems = JSON.parse(sessionStorage.getItem('cartItems')) || [];
    let itemIndex = cartItems.findIndex(item => item.producto === producto);

    if (itemIndex !== -1) {
        cartItems[itemIndex].cantidad += 1;
    } else {
        cartItems.push({ producto, nombre, precio, cantidad: 1 });
    }

    sessionStorage.setItem('cartItems', JSON.stringify(cartItems));
    updateCart();
}

