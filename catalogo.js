document.addEventListener("DOMContentLoaded", () => {
  // Main elements
  const catalogo = document.getElementById("catalogo");
  const mostrarMas = document.getElementById("mostrarMas");
  const ordenarPrecio = document.getElementById("ordenarPrecio");
  const productosPorPaginaSelect =
    document.getElementById("productosPorPagina");
  const mensajeSinProductos = document.getElementById("mensajeSinProductos");

  // Desktop filter elements
  const categoriaFiltro = document.getElementById("categoria");
  const marcaFiltro = document.getElementById("marca");
  const nombreFiltro = document.getElementById("nombre");
  const precioMinFiltro = document.getElementById("precioMin");
  const precioMaxFiltro = document.getElementById("precioMax");
  const valorPrecioMin = document.getElementById("valorPrecioMin");
  const valorPrecioMax = document.getElementById("valorPrecioMax");
  const aplicarFiltro = document.getElementById("aplicarFiltro");

  // Mobile filter elements
  const categoriaFiltroMobile = document.getElementById("categoria-mobile");
  const marcaFiltroMobile = document.getElementById("marca-mobile");
  const nombreFiltroMobile = document.getElementById("nombre-mobile");
  const precioMinFiltroMobile = document.getElementById("precioMin-mobile");
  const precioMaxFiltroMobile = document.getElementById("precioMax-mobile");
  const valorPrecioMinMobile = document.getElementById("valorPrecioMin-mobile");
  const valorPrecioMaxMobile = document.getElementById("valorPrecioMax-mobile");
  const aplicarFiltroMobile = document.getElementById("aplicarFiltro-mobile");

  // State variables
  let paginaActual = 1;
  let categoriaSeleccionada = getSavedFilter("categoria") || "";
  let marcaSeleccionada = getSavedFilter("marca") || "";
  let nombreSeleccionado = getSavedFilter("nombre") || "";
  let precioMinSeleccionado = parseInt(getSavedFilter("precioMin") || 0);
  let precioMaxSeleccionado = parseInt(getSavedFilter("precioMax") || 10000);
  let ordenarDescendente = getSavedFilter("ordenarDescendente") === "true";
  let productosPorPagina = parseInt(getSavedFilter("productosPorPagina") || 12);

  // Función para guardar filtro en localStorage
  function saveFilter(key, value) {
    localStorage.setItem(`catalogo_filtro_${key}`, value);
  }

  // Función para obtener filtro guardado
  function getSavedFilter(key) {
    return localStorage.getItem(`catalogo_filtro_${key}`);
  }

  // Función para guardar todos los filtros actuales
  function saveAllFilters() {
    saveFilter("categoria", categoriaSeleccionada);
    saveFilter("marca", marcaSeleccionada);
    saveFilter("nombre", nombreSeleccionado);
    saveFilter("precioMin", precioMinSeleccionado);
    saveFilter("precioMax", precioMaxSeleccionado);
    saveFilter("ordenarDescendente", ordenarDescendente);
    saveFilter("productosPorPagina", productosPorPagina);
  }

  const cargarProductos = (pagina) => {
    const url = `get_catalogo.php?pagina=${pagina}&categoria=${encodeURIComponent(
      categoriaSeleccionada
    )}&marca=${encodeURIComponent(
      marcaSeleccionada
    )}&nombre=${encodeURIComponent(
      nombreSeleccionado
    )}&precioMin=${precioMinSeleccionado}&precioMax=${precioMaxSeleccionado}&productosPorPagina=${productosPorPagina}&ordenar=${
      ordenarDescendente ? "desc" : "asc"
    }`;

    // Guardar filtros antes de cargar para persistencia
    saveAllFilters();

    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        if (pagina === 1) catalogo.innerHTML = "";

        if (data.productos.length === 0 && pagina === 1) {
          mensajeSinProductos.style.display = "block";
          catalogo.style.display = "none";
          mostrarMas.style.display = "none";
        } else {
          mensajeSinProductos.style.display = "none";
          catalogo.style.display = "flex"; // Aseguramos que el catálogo sea visible cuando hay productos
          catalogo.style.flexWrap = "wrap"; // Aseguramos que el flex tenga wrap

          data.productos.forEach((producto) => {
            const div = document.createElement("div");
            div.classList.add("col");
            div.innerHTML = `
              <div class="card h-100 product-card">
                <div class="img-container position-relative">
                  <img src="./assets/productos/imagen/${producto.id_producto}/${producto.imagen_principal}" class="card-img-top" alt="${producto.nombre}">
                  <button class="quick-view-btn" data-id="${producto.id_producto}">Vista rápida</button>
                </div>
                <div class="card-body">
                  <h5 class="card-title">${producto.nombre}</h5>
                  <p class="card-text"><small class="text-muted">Categoría: ${producto.categoria}</small></p>
                  <p class="card-text"><small class="text-muted">Marca: ${producto.marca}</small></p>
                  <p class="card-text"><strong>$${producto.precio_minorista}</strong></p>
                </div>
                <div class="card-footer">
                  <a href="producto.php?id_producto=${producto.id_producto}" class="btn btn-primary-custom w-100">Ver producto</a>
                </div>
              </div>
            `;
            catalogo.appendChild(div);
          });

          document.querySelectorAll(".quick-view-btn").forEach((boton) => {
            boton.addEventListener("click", (e) => {
              const idProducto = e.target.dataset.id;
              abrirModal(idProducto);
            });
          });

          mostrarMas.style.display =
            data.total > pagina * productosPorPagina ? "block" : "none";
        }
      })
      .catch((error) => {
        console.error("Error al cargar productos:", error);
        mensajeSinProductos.textContent =
          "Error al cargar los productos. Por favor, inténtelo de nuevo.";
        mensajeSinProductos.style.display = "block";
      });
  };

  const cargarFiltros = () => {
    fetch("get_catalogo.php")
      .then((response) => response.json())
      .then((data) => {
        // Desktop filters
        categoriaFiltro.innerHTML = '<option value="">Todas</option>';
        marcaFiltro.innerHTML = '<option value="">Todas</option>';

        // También limpiar los filtros móviles
        if (categoriaFiltroMobile)
          categoriaFiltroMobile.innerHTML = '<option value="">Todas</option>';
        if (marcaFiltroMobile)
          marcaFiltroMobile.innerHTML = '<option value="">Todas</option>';

        data.categorias.forEach((categoria) => {
          const option = document.createElement("option");
          option.value = categoria.nombre;
          option.textContent = categoria.nombre;
          categoriaFiltro.appendChild(option);

          if (categoriaFiltroMobile) {
            const optionMobile = option.cloneNode(true);
            categoriaFiltroMobile.appendChild(optionMobile);
          }
        });

        data.marcas.forEach((marca) => {
          const option = document.createElement("option");
          option.value = marca.nombre;
          option.textContent = marca.nombre;
          marcaFiltro.appendChild(option);

          if (marcaFiltroMobile) {
            const optionMobile = option.cloneNode(true);
            marcaFiltroMobile.appendChild(optionMobile);
          }
        });

        const setInitialPriceRange = (min, max) => {
          [precioMinFiltro, precioMinFiltroMobile].forEach((element) => {
            if (element) {
              element.min = min;
              element.max = max;
              element.value = precioMinSeleccionado || min;
            }
          });

          [precioMaxFiltro, precioMaxFiltroMobile].forEach((element) => {
            if (element) {
              element.min = min;
              element.max = max;
              element.value = precioMaxSeleccionado || max;
            }
          });

          [valorPrecioMin, valorPrecioMinMobile].forEach((element) => {
            if (element) element.textContent = precioMinSeleccionado || min;
          });

          [valorPrecioMax, valorPrecioMaxMobile].forEach((element) => {
            if (element) element.textContent = precioMaxSeleccionado || max;
          });
        };

        setInitialPriceRange(data.precioMin, data.precioMax);

        // Solo inicializar estos valores si no hay filtros guardados
        if (!precioMinSeleccionado) precioMinSeleccionado = data.precioMin;
        if (!precioMaxSeleccionado) precioMaxSeleccionado = data.precioMax;

        // Restaurar valores de los filtros desde la persistencia
        restaurarValoresFiltros();
      })
      .catch((error) => {
        console.error("Error al cargar filtros:", error);
      });
  };

  // Función para restaurar valores de filtros guardados
  const restaurarValoresFiltros = () => {
    // Restaurar valores en selectores
    if (categoriaFiltro && categoriaSeleccionada) {
      categoriaFiltro.value = categoriaSeleccionada;
    }
    if (categoriaFiltroMobile && categoriaSeleccionada) {
      categoriaFiltroMobile.value = categoriaSeleccionada;
    }

    if (marcaFiltro && marcaSeleccionada) {
      marcaFiltro.value = marcaSeleccionada;
    }
    if (marcaFiltroMobile && marcaSeleccionada) {
      marcaFiltroMobile.value = marcaSeleccionada;
    }

    if (nombreFiltro && nombreSeleccionado) {
      nombreFiltro.value = nombreSeleccionado;
    }
    if (nombreFiltroMobile && nombreSeleccionado) {
      nombreFiltroMobile.value = nombreSeleccionado;
    }

    // Restaurar valores para productos por página
    if (productosPorPaginaSelect) {
      productosPorPaginaSelect.value = productosPorPagina;
    }
  };

  const abrirModal = (idProducto) => {
    const url = `get_frangancias.php?id_producto=${idProducto}`;
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        const modal = document.getElementById("modal");
        modal.querySelector(".modal-body").innerHTML = `
        <div class="row g-4">
          <div class="col-12 col-md-4">
            <img src="./assets/productos/imagen/${idProducto}/${
          data.imagen_principal
        }" alt="${data.nombre}" class="img-fluid rounded">
          </div>
          <div class="col-12 col-md-8">
            <h3>${data.nombre}</h3>
            <h4>Fragancias disponibles:</h4>
            <div class="fragancias-scroll">
              <ul class="list-inline">
                ${data.fragancias
                  .map(
                    (fragancia) =>
                      `<li class="list-inline-item badge bg-primary mb-1">${fragancia}</li>`
                  )
                  .join("")}
              </ul>
            </div>
            <button class="btn btn-primary-custom mt-3" onclick="location.href='producto.php?id_producto=${idProducto}'">Comprar</button>
          </div>
        </div>
      `;
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
      })
      .catch((error) => {
        console.error("Error al cargar datos del modal:", error);
      });
  };

  // Función para limpiar los filtros
  const limpiarFiltros = () => {
    categoriaSeleccionada = "";
    marcaSeleccionada = "";
    nombreSeleccionado = "";

    // No reiniciar los precios a 0 y 10000 aquí, ya que deberíamos usar los valores originales del catálogo
    // Los recuperaremos en la próxima llamada a cargarFiltros

    paginaActual = 1;

    // Actualizar los campos visibles
    if (categoriaFiltro) categoriaFiltro.value = "";
    if (categoriaFiltroMobile) categoriaFiltroMobile.value = "";
    if (marcaFiltro) marcaFiltro.value = "";
    if (marcaFiltroMobile) marcaFiltroMobile.value = "";
    if (nombreFiltro) nombreFiltro.value = "";
    if (nombreFiltroMobile) nombreFiltroMobile.value = "";

    // Guardar los filtros limpios
    saveAllFilters();

    // Recargar productos
    cargarProductos(paginaActual);
  };

  // Event Listeners
  const setupEventListeners = () => {
    // Desktop filter events
    if (aplicarFiltro) {
      aplicarFiltro.addEventListener("click", () => {
        categoriaSeleccionada = categoriaFiltro.value;
        marcaSeleccionada = marcaFiltro.value;
        nombreSeleccionado = nombreFiltro.value.trim();
        precioMinSeleccionado = parseInt(precioMinFiltro.value);
        precioMaxSeleccionado = parseInt(precioMaxFiltro.value);
        paginaActual = 1;
        cargarProductos(paginaActual);
      });
    }

    // Mobile filter events
    if (aplicarFiltroMobile) {
      aplicarFiltroMobile.addEventListener("click", () => {
        categoriaSeleccionada = categoriaFiltroMobile.value;
        marcaSeleccionada = marcaFiltroMobile.value;
        nombreSeleccionado = nombreFiltroMobile.value.trim();
        precioMinSeleccionado = parseInt(precioMinFiltroMobile.value);
        precioMaxSeleccionado = parseInt(precioMaxFiltroMobile.value);
        paginaActual = 1;

        const offcanvas = document.getElementById("filtrosOffcanvas");
        if (offcanvas) {
          const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
          if (bsOffcanvas) bsOffcanvas.hide();
        }

        cargarProductos(paginaActual);
      });
    }

    // Agregar botón para limpiar filtros (puedes añadir este HTML a tu página)
    const limpiarFiltroBtn = document.createElement("button");
    limpiarFiltroBtn.textContent = "Limpiar Filtros";
    limpiarFiltroBtn.classList.add(
      "btn",
      "btn-outline-secondary",
      "w-100",
      "mt-2"
    );
    limpiarFiltroBtn.addEventListener("click", limpiarFiltros);

    if (aplicarFiltro && aplicarFiltro.parentNode) {
      aplicarFiltro.parentNode.appendChild(limpiarFiltroBtn);
    }

    // Agregar el mismo botón para móvil
    const limpiarFiltroBtnMobile = limpiarFiltroBtn.cloneNode(true);
    limpiarFiltroBtnMobile.addEventListener("click", limpiarFiltros);

    if (aplicarFiltroMobile && aplicarFiltroMobile.parentNode) {
      aplicarFiltroMobile.parentNode.appendChild(limpiarFiltroBtnMobile);
    }

    // Price range events
    [
      { input: precioMinFiltro, display: valorPrecioMin },
      { input: precioMaxFiltro, display: valorPrecioMax },
      { input: precioMinFiltroMobile, display: valorPrecioMinMobile },
      { input: precioMaxFiltroMobile, display: valorPrecioMaxMobile },
    ].forEach(({ input, display }) => {
      if (input && display) {
        input.addEventListener("input", () => {
          display.textContent = input.value;
        });
      }
    });

    // Name filter events
    [nombreFiltro, nombreFiltroMobile].forEach((input) => {
      if (input) {
        input.addEventListener("input", () => {
          const valor = input.value.trim();
          if (valor.length >= 3 || valor.length === 0) {
            nombreSeleccionado = valor;
            paginaActual = 1;
            cargarProductos(paginaActual);
          }
        });
      }
    });

    // Modal events
    const modal = document.getElementById("modal");
    if (modal) {
      modal.addEventListener("click", (event) => {
        if (event.target === modal) {
          const bootstrapModal = bootstrap.Modal.getInstance(modal);
          if (bootstrapModal) bootstrapModal.hide();
        }
      });
    }

    document
      .querySelectorAll("#cerrar-modal, #cerrar-modal-footer")
      .forEach((btn) => {
        if (btn) {
          btn.addEventListener("click", () => {
            const modal = document.getElementById("modal");
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            if (bootstrapModal) bootstrapModal.hide();
          });
        }
      });

    // Other events
    if (mostrarMas) {
      mostrarMas.addEventListener("click", () => {
        paginaActual++;
        cargarProductos(paginaActual);
      });
    }

    if (ordenarPrecio) {
      ordenarPrecio.addEventListener("click", () => {
        ordenarDescendente = !ordenarDescendente;

        // Actualizar el texto del botón para reflejar el orden
        ordenarPrecio.textContent = ordenarDescendente
          ? "Ordenar por precio (Mayor a Menor)"
          : "Ordenar por precio (Menor a Mayor)";

        paginaActual = 1;
        cargarProductos(paginaActual);
      });

      // Inicializar el texto del botón según el estado actual
      ordenarPrecio.textContent = ordenarDescendente
        ? "Ordenar por precio (Mayor a Menor)"
        : "Ordenar por precio (Menor a Mayor)";
    }

    if (productosPorPaginaSelect) {
      productosPorPaginaSelect.addEventListener("change", () => {
        productosPorPagina = parseInt(productosPorPaginaSelect.value);
        paginaActual = 1;
        cargarProductos(paginaActual);
      });
    }
  };

  // Initialize
  cargarFiltros();
  setupEventListeners();
  cargarProductos(paginaActual);
});
