document.addEventListener("DOMContentLoaded", function () {
  // Selecciona todos los botones de vista rápida
  const quickViewButtons = document.querySelectorAll(".quick-view-btn");

  quickViewButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Obtener los datos del producto desde los atributos data-*
      const productName = this.getAttribute("data-product-name");
      const productDescription = this.getAttribute("data-product-description");
      const productPrice = this.getAttribute("data-product-price");
      const productImage = this.getAttribute("data-product-imagen");
      const productVariants = JSON.parse(
        this.getAttribute("data-product-variants")
      );

      // Insertar los datos en el modal
      document.getElementById("quickViewTitle").innerText = productName;
      document.getElementById("quickViewDescription").innerText =
        productDescription;
      document.getElementById("quickViewPrice").innerText = `$${productPrice}`;
      document.getElementById(
        "quickViewImage"
      ).src = `../pa/assets/productos${productImage}`;

      // Limpiar el contenido previo de las fragancias/colores
      const variantsList = document.getElementById("quickViewFragrances");
      variantsList.innerHTML = ""; // Limpiar la lista antes de agregar nuevos elementos

      // Añadir las variantes (fragancias o colores) a la lista
      productVariants.forEach((variant) => {
        const listItem = document.createElement("li");
        listItem.innerText = variant;
        variantsList.appendChild(listItem);
      });
    });
  });
});

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

