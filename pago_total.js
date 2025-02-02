document.addEventListener("DOMContentLoaded", () => {
  let currentStep = 1;
  const totalSteps = 3;
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const pickupCards = document.querySelectorAll(".pickup-card");
  // Product summary functionality
  function toggleFragrances(productId) {
    const fragranceList = document.getElementById(
      `checkout-fragancias-${productId}`
    );
    const toggleButton = document.querySelector(
      `[onclick="toggleFragrances(${productId})"]`
    );
    const toggleIcon = toggleButton.querySelector("i");

    if (fragranceList) {
      if (fragranceList.style.display === "none") {
        fragranceList.style.display = "block";
        toggleIcon.classList.replace("bi-chevron-down", "bi-chevron-up");
      } else {
        fragranceList.style.display = "none";
        toggleIcon.classList.replace("bi-chevron-up", "bi-chevron-down");
      }
    }
  }
  // Step validation
  function validateStep(step) {
    switch (step) {
      case 3:
        const paymentMethod = document.querySelector(
          'input[name="paymentMethod"]:checked'
        );
        if (!paymentMethod) {
          alert("Debe seleccionar un método de pago");
          return false;
        }

        if (["transferencia", "mercadopago"].includes(paymentMethod.value)) {
          const comprobanteId = `comprobante${
            paymentMethod.value === "transferencia"
              ? "Transferencia"
              : "MercadoPago"
          }`;
          const comprobante = document.getElementById(comprobanteId);

          if (!comprobante || comprobante.files.length === 0) {
            alert("Debe subir el comprobante de pago");
            comprobante.focus();
            return false;
          }
        }
        return true;
    }
    return true;
  }
  // Payment method handling
  function handlePaymentMethodChange() {
    const paymentMethods = document.querySelectorAll(
      'input[name="paymentMethod"]'
    );
    const paymentFields = {
      transferencia: document.getElementById("transferenciaFields"),
      mercadopago: document.getElementById("mercadoPagoFields"),
      pagoenlocal: document.getElementById("pagoEnLocalFields"),
    };
    paymentMethods.forEach((radio) => {
      radio.addEventListener("change", (event) => {
        Object.values(paymentFields).forEach((field) =>
          field.classList.add("d-none")
        );
        const selectedField = paymentFields[event.target.value];
        if (selectedField) {
          selectedField.classList.remove("d-none");
        }
      });
    });
  }
  // Update step visibility and buttons
  function updateSteps() {
    document.querySelectorAll(".checkout_step").forEach((step, index) => {
      step.classList.toggle("active", index + 1 === currentStep);
    });
    document
      .querySelectorAll(".checkout_step-content")
      .forEach((content, index) => {
        content.classList.toggle("d-none", index + 1 !== currentStep);
      });
    prevBtn.disabled = currentStep === 1;
    nextBtn.textContent =
      currentStep === totalSteps ? "Finalizar compra" : "Siguiente";
  }
  // Event listeners
  prevBtn.addEventListener("click", () => {
    if (currentStep > 1) {
      currentStep--;
      updateSteps();
    }
  });
  nextBtn.addEventListener("click", () => {
    if (validateStep(currentStep)) {
      if (currentStep < totalSteps) {
        currentStep++;
        updateSteps();
      } else {
        // Final submission
        submitCheckoutForm();
      }
    }
  });
  // Initialize payment method handling
  handlePaymentMethodChange();
  // Make these functions globally available
  window.toggleFragrances = toggleFragrances;
});
function submitCheckoutForm() {
  const formData = new FormData();
  formData.append("id_pedido", document.getElementById("id_pedido").value);
  formData.append("monto", document.getElementById("monto").value);
  // Add payment information
  const paymentMethod = document.querySelector(
    'input[name="paymentMethod"]:checked'
  );
  formData.append("payment_method", paymentMethod.value);
  // Add payment proof if applicable
  if (["transferencia", "mercadopago"].includes(paymentMethod.value)) {
    const comprobanteId = `comprobante${
      paymentMethod.value === "transferencia" ? "Transferencia" : "MercadoPago"
    }`;
    const comprobante = document.getElementById(comprobanteId).files[0];
    if (comprobante) {
      formData.append("comprobante", comprobante);
    }
  }
  // Show loading state
  const nextBtn = document.getElementById("nextBtn");
  const originalText = nextBtn.textContent;
  nextBtn.disabled = true;
  nextBtn.textContent = "Procesando...";
  // Send AJAX request
  fetch("procesar_pago_total.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("¡pago total realizado!");
        /* window.location.href = "index.php"; */
        window.location.href = `gracias.php?id_pedido=${data.id_pedido}`;
      } else {
        // Show error message
        alert("Error: " + data.message);
        nextBtn.disabled = false;
        nextBtn.textContent = originalText;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert(
        "Ha ocurrido un error al procesar el pago. Por favor, inténtelo nuevamente."
      );
      nextBtn.disabled = false;
      nextBtn.textContent = originalText;
    });
}
