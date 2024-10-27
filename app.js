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

document.getElementById("cart").addEventListener("click", function (event) {
  event.stopPropagation();
});

function InformacionPersonal() {
  const infoUsuario = document.getElementById("info-usuario");
  const formUsuario = document.getElementById("form-editar");

  infoUsuario.classList.add("hidden");
  formUsuario.classList.remove("hidden");
}

function btnCancelar() {
  const infoUsuario = document.getElementById("info-usuario");
  const formUsuario = document.getElementById("form-editar");

  // Reiniciar los formularios a los datos que tenía antes
  formUsuario.reset();

  infoUsuario.classList.remove("hidden");
  formUsuario.classList.add("hidden");
}

document.addEventListener("DOMContentLoaded", function () {
  // Función para manejar la confirmación de eliminar
  document
    .getElementById("deleteConfirm")
    .addEventListener("click", function () {
      // Aquí iría la lógica para eliminar el elemento
      alert("Elemento eliminado");
      bootstrap.Modal.getInstance(
        document.getElementById("confirmDeleteModal")
      ).hide();
    });

  // Función para manejar la confirmación de cerrar sesión
  document
    .getElementById("logoutConfirm")
    .addEventListener("click", function () {
      // Aquí iría la lógica para cerrar sesión
      alert("Sesión cerrada");
      bootstrap.Modal.getInstance(
        document.getElementById("confirmLogoutModal")
      ).hide();
    });

  // Función para manejar la confirmación de compra
  document
    .getElementById("purchaseConfirm")
    .addEventListener("click", function () {
      // Aquí iría la lógica para procesar la compra
      alert("Compra realizada");
      bootstrap.Modal.getInstance(
        document.getElementById("confirmPurchaseModal")
      ).hide();
    });

  // Función para manejar la confirmación de cancelar pedido
  document
    .getElementById("cancelOrderConfirm")
    .addEventListener("click", function () {
      // Aquí iría la lógica para cancelar el pedido
      alert("Pedido cancelado");
      bootstrap.Modal.getInstance(
        document.getElementById("confirmCancelOrderModal")
      ).hide();
    });

  // Función para manejar la confirmación de actualizar información personal
  document
    .getElementById("updateInfoConfirm")
    .addEventListener("click", function () {
      // Aquí iría la lógica para actualizar la información personal
      alert("Información personal actualizada");
      bootstrap.Modal.getInstance(
        document.getElementById("confirmUpdateInfoModal")
      ).hide();
    });
});
