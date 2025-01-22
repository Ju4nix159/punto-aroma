document.addEventListener("DOMContentLoaded", () => {
  let currentStep = 1;
  const totalSteps = 3;

  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");

  function updateSteps() {
    document.querySelectorAll(".checkout_step").forEach((step, index) => {
      if (index + 1 === currentStep) {
        step.classList.add("active");
      } else {
        step.classList.remove("active");
      }
    });

    document
      .querySelectorAll(".checkout_step-content")
      .forEach((content, index) => {
        if (index + 1 === currentStep) {
          content.classList.remove("d-none");
        } else {
          content.classList.add("d-none");
        }
      });

    prevBtn.disabled = currentStep === 1;
    nextBtn.textContent =
      currentStep === totalSteps ? "Finalizar compra" : "Siguiente";
  }

  prevBtn.addEventListener("click", () => {
    if (currentStep > 1) {
      currentStep--;
      updateSteps();
    }
  });

  nextBtn.addEventListener("click", () => {
    if (currentStep < totalSteps) {
      currentStep++;
      updateSteps();
    } else {
      // Here you would typically submit the form or process the order
      alert("¡Compra finalizada!");
    }
  });

  // Toggle credit card fields visibility
  const paymentMethodRadios = document.querySelectorAll(
    'input[name="paymentMethod"]'
  );
  const transferenciaFields = document.getElementById("transferenciaFields");
  const mercadoPagoFields = document.getElementById("mercadoPagoFields");
  const pagoEnLocalFields = document.getElementById("pagoEnLocalFields");

  paymentMethodRadios.forEach((radio) => {
    radio.addEventListener("change", (event) => {
      const value = event.target.value;

      // Mostrar y ocultar secciones según el método de pago seleccionado
      if (value === "transferencia") {
        transferenciaFields.classList.remove("d-none");
        mercadoPagoFields.classList.add("d-none");
        pagoEnLocalFields.classList.add("d-none");
      } else if (value === "mercadopago") {
        transferenciaFields.classList.add("d-none");
        mercadoPagoFields.classList.remove("d-none");
        pagoEnLocalFields.classList.add("d-none");
      } else if (value === "pagoenlocal") {
        transferenciaFields.classList.add("d-none");
        mercadoPagoFields.classList.add("d-none");
        pagoEnLocalFields.classList.remove("d-none");
      }
    });
  });
});
function toggleFragrances(productId) {
  // Usamos el ID único generado con "checkout-"
  const fragranceList = document.getElementById(
    `checkout-fragancias-${productId}`
  );

  // Verificar si el elemento existe
  if (!fragranceList) {
    console.error(
      `No se encontró el elemento con ID checkout-fragancias-${productId}`
    );
    return;
  }

  const toggleIcon = fragranceList.previousElementSibling.querySelector("i");

  if (toggleIcon) {
    if (fragranceList.style.display === "none") {
      fragranceList.style.display = "block";
      toggleIcon.classList.remove("bi-chevron-down");
      toggleIcon.classList.add("bi-chevron-up");
    } else {
      fragranceList.style.display = "none";
      toggleIcon.classList.remove("bi-chevron-up");
      toggleIcon.classList.add("bi-chevron-down");
    }
  } else {
    console.warn("No se encontró el icono <i> asociado.");
  }
}
