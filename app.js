// Función para actualizar el modal de vista rápida
function updateQuickViewModal(button) {
  const productName = button.getAttribute("data-product-name");
  const productDescription = button.getAttribute("data-product-description");
  const productPrice = button.getAttribute("data-product-price");

  // Actualizar el contenido del modal
  document.getElementById("quickViewTitle").textContent = productName;
  document.getElementById("quickViewDescription").textContent = productDescription;
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


  function añadirCarrito(producto) {
    const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
    carrito.push(producto);
    localStorage.setItem("carrito", JSON.stringify(carrito));
  }