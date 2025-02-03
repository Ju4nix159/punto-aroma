document.addEventListener("DOMContentLoaded", () => {
  let currentStep = 1;
  const totalSteps = 3;

  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const pickupCards = document.querySelectorAll(".pickup-card");
  pickupCards[0].setAttribute("data-local-id", "1"); // Centro
  pickupCards[1].setAttribute("data-local-id", "2"); // Norte
  pickupCards[2].setAttribute("data-local-id", "3"); // Sur

  // Validation rules for each field
  const validationRules = {
    nombre: {
      regex: /^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]{2,}$/,
      errorMsg:
        "El nombre debe contener al menos 2 letras sin números ni caracteres especiales",
    },
    apellido: {
      regex: /^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]{2,}$/,
      errorMsg:
        "El apellido debe contener al menos 2 letras sin números ni caracteres especiales",
    },
    dni: {
      regex: /^\d{6,9}$/,
      errorMsg: "El DNI debe contener entre 6 y 9 dígitos",
    },
    phone: {
      regex: /^\d{7,11}$/,
      errorMsg: "El teléfono debe contener 10 dígitos",
    },
    email: {
      regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      errorMsg: "Ingrese un correo electrónico válido",
    },
    province: {
      regex: /^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]{2,}$/,
      errorMsg: "Ingrese una provincia válida",
    },
    locality: {
      regex: /^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]{2,}$/,
      errorMsg: "Ingrese una localidad válida",
    },
    street: {
      regex: /^.{3,}$/,
      errorMsg: "La calle debe tener al menos 3 caracteres",
    },
    number: {
      regex: /^\d+$/,
      errorMsg: "El número debe contener solo dígitos",
    },
    postalCode: {
      regex: /^\d{4,8}$/,
      errorMsg: "Ingrese un código postal válido",
    },
  };

  function calcularEdad(fechaNacimiento) {
    const hoy = new Date();
    const fechaNac = new Date(fechaNacimiento);
    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mes = hoy.getMonth() - fechaNac.getMonth();

    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
      edad--;
    }

    return edad;
  }
  function validarFechaNacimiento(fieldId) {
    const field = document.getElementById(fieldId);
    const fechaNacimiento = field.value.trim();

    if (!fechaNacimiento) {
      return showError(fieldId, "Este campo es requerido");
    }

    const edad = calcularEdad(fechaNacimiento);

    if (edad < 18) {
      return showError(fieldId, "Debes ser mayor de 18 años para continuar");
    }

    clearError(field);
    return true;
  }

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

  // Delivery method selection
  function selectDeliveryMethod(method, event) {
    // Prevenir el comportamiento por defecto
    if (event) {
      event.preventDefault();
    }

    const btnPickup = document.getElementById("btn-pickup");
    const btnDelivery = document.getElementById("btn-delivery");
    const pickupOptions = document.getElementById("pickupOptions");
    const deliveryForm = document.getElementById("deliveryForm");
    const pagoEnLocalOption = document
      .getElementById("pagoEnLocal")
      .closest(".form-check"); // Contenedor del input de pago en local
    // Clear previous errors
    document
      .querySelectorAll(".is-invalid")
      .forEach((field) => clearError(field));

    if (method === "pickup") {
      btnPickup.classList.add("btn-secondary-custom");
      btnPickup.classList.remove("btn-outline-custom");
      btnDelivery.classList.add("btn-outline-custom");
      btnDelivery.classList.remove("btn-secondary-custom");

      pickupOptions.classList.remove("d-none");
      deliveryForm.classList.add("d-none");

      // Mostrar la opción de "Pago en local"
      if (pagoEnLocalOption) {
        pagoEnLocalOption.classList.remove("d-none");
      }

      // Remover temporalmente el required de los campos de envío
      deliveryForm.querySelectorAll("input[required]").forEach((input) => {
        input.removeAttribute("required");
      });

      // Clear delivery form fields
      deliveryForm
        .querySelectorAll("input, select, textarea")
        .forEach((field) => {
          field.value = "";
          clearError(field);
        });
    } else {
      btnDelivery.classList.add("btn-secondary-custom");
      btnDelivery.classList.remove("btn-outline-custom");
      btnPickup.classList.add("btn-outline-custom");
      btnPickup.classList.remove("btn-secondary-custom");

      deliveryForm.classList.remove("d-none");
      pickupOptions.classList.add("d-none");

      if (pagoEnLocalOption) {
        pagoEnLocalOption.classList.add("d-none");
      }
      // Restaurar el required a los campos de envío
      deliveryForm.querySelectorAll("input").forEach((input) => {
        if (
          input.id !== "floor" &&
          input.id !== "department" &&
          input.id !== "additionalInfo"
        ) {
          input.setAttribute("required", "required");
        }
      });

      // Clear selected pickup point
      document.querySelectorAll(".pickup-card.selected").forEach((card) => {
        card.classList.remove("selected");
      });
    }
  }

  // Pickup point selection
  function selectPickupPoint(selectedCard) {
    document.querySelectorAll(".pickup-card").forEach((card) => {
      card.classList.remove("selected");
    });
    selectedCard.classList.add("selected");
  }

  // Error handling functions
  function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    field.classList.add("is-invalid");

    let errorDiv = field.nextElementSibling;
    if (!errorDiv || !errorDiv.classList.contains("invalid-feedback")) {
      errorDiv = document.createElement("div");
      errorDiv.className = "invalid-feedback";
      field.parentNode.insertBefore(errorDiv, field.nextSibling);
    }
    errorDiv.textContent = message;

    field.focus();
    return false;
  }

  function clearError(field) {
    field.classList.remove("is-invalid");
    const errorDiv = field.nextElementSibling;
    if (errorDiv && errorDiv.classList.contains("invalid-feedback")) {
      errorDiv.remove();
    }
  }

  // Field validation
  function validateField(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return true; // Skip if field doesn't exist

    // Skip validation if the field is in a hidden container
    if (field.closest(".d-none")) return true;

    const value = field.value.trim();
    const rule = validationRules[fieldId];

    if (!rule) return true;

    clearError(field);

    if (!value) {
      return showError(fieldId, `Este campo es requerido`);
    }

    if (!rule.regex.test(value)) {
      return showError(fieldId, rule.errorMsg);
    }

    return true;
  }

  // Delivery method validation
  function validateDeliveryMethod() {
    const pickupBtn = document.getElementById("btn-pickup");
    const deliveryBtn = document.getElementById("btn-delivery");
    const deliveryForm = document.getElementById("deliveryForm");

    // Verificar si se seleccionó algún método de entrega
    if (
      !pickupBtn.classList.contains("btn-secondary-custom") &&
      !deliveryBtn.classList.contains("btn-secondary-custom")
    ) {
      alert(
        "Debe seleccionar un método de entrega (Retiro en sucursal o Envío a domicilio)"
      );
      return false;
    }

    if (pickupBtn.classList.contains("btn-secondary-custom")) {
      // Validar selección de sucursal
      const pickupSelected = document.querySelector(".pickup-card.selected");
      if (!pickupSelected) {
        alert("Debe seleccionar una sucursal para retiro");
        return false;
      }
    } else if (!deliveryForm.classList.contains("d-none")) {
      // Solo validar los campos de envío si el formulario está visible
      const deliveryFields = [
        "province",
        "locality",
        "street",
        "number",
        "postalCode",
      ];
      const form = document.getElementById("billingForm");

      // Temporalmente remover el required de los campos ocultos
      const hiddenInputs = deliveryForm.querySelectorAll("input[required]");
      hiddenInputs.forEach((input) => {
        if (deliveryFields.includes(input.id)) {
          input.removeAttribute("required");
        }
      });

      // Validar los campos visibles
      for (const fieldId of deliveryFields) {
        const field = document.getElementById(fieldId);
        if (field && !field.value.trim()) {
          field.setAttribute("required", "required");
          showError(fieldId, "Este campo es requerido");
          return false;
        }
      }

      // Restaurar el required a los campos
      hiddenInputs.forEach((input) => {
        if (deliveryFields.includes(input.id)) {
          input.setAttribute("required", "required");
        }
      });
    }

    return true;
  }

  // Step validation
  function validateStep(step) {
    switch (step) {
      case 1:
        const cart = document.querySelectorAll(".list-group-item");
        if (cart.length === 0) {
          alert("El carrito está vacío");
          return false;
        }
        return true;

      case 2:
        // Personal information validation
        const personalFields = [
          "nombre",
          "apellido",
          "dni",
          "phone",
          "email",
          "fechaNacimiento",
        ];
        for (const fieldId of personalFields) {
          if (fieldId === "fechaNacimiento") {
            if (!validarFechaNacimiento(fieldId)) {
              return false;
            }
          } else {
            if (!validateField(fieldId)) {
              return false;
            }
          }
        }

        return validateDeliveryMethod();

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

  // Add input event listeners to clear errors
  document.querySelectorAll("input, select, textarea").forEach((field) => {
    field.addEventListener("input", () => clearError(field));
  });

  // Make these functions globally available
  window.toggleFragrances = toggleFragrances;
  window.selectDeliveryMethod = selectDeliveryMethod;
  window.selectPickupPoint = selectPickupPoint;
});

function submitCheckoutForm() {
  const formData = new FormData();
  // Add user information from session
  const userFields = [
    "nombre",
    "apellido",
    "dni",
    "phone",
    "email",
    "fechaNacimiento",
  ];
  userFields.forEach((field) => {
    formData.append(field, document.getElementById(field).value);
  });

  // Get delivery method and related information
  const isPickup = document
    .getElementById("btn-pickup")
    .classList.contains("btn-secondary-custom");
  formData.append("delivery_method", isPickup ? "pickup" : "delivery");

  if (isPickup) {
    const selectedPickup = document.querySelector(".pickup-card.selected");
    const localId = selectedPickup.getAttribute("data-local-id");
    formData.append("id_local", localId);
  } else {
    // Add delivery address information
    const deliveryFields = {
      province: "provincia",
      locality: "localidad",
      street: "calle",
      number: "numero",
      floor: "piso",
      department: "departamento",
      postalCode: "codigo_postal",
      additionalInfo: "informacion_adicional",
    };

    Object.entries(deliveryFields).forEach(([fieldId, dbField]) => {
      const value = document.getElementById(fieldId).value;
      formData.append(dbField, value || ""); // Send empty string if value is null
    });
  }

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
  fetch("procesar_pedido.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Clear cart and show success message
        alert("¡Pedido realizado con éxito!");
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
        "Ha ocurrido un error al procesar el pedido. Por favor, inténtelo nuevamente."
      );
      nextBtn.disabled = false;
      nextBtn.textContent = originalText;
    });
}
