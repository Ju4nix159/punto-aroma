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
  let categoriaSeleccionada = "";
  let marcaSeleccionada = "";
  let nombreSeleccionado = "";
  let precioMinSeleccionado = 0;
  let precioMaxSeleccionado = 10000;
  let ordenarDescendente = false;
  let productosPorPagina = 12;

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
          data.productos.forEach((producto) => {
            const div = document.createElement("div");
            div.classList.add("col-12", "col-sm-6", "col-md-4", "col-lg-3");
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
      });
  };

  const cargarFiltros = () => {
    fetch("get_catalogo.php")
      .then((response) => response.json())
      .then((data) => {
        // Desktop filters
        categoriaFiltro.innerHTML = '<option value="">Todas</option>';
        marcaFiltro.innerHTML = '<option value="">Todas</option>';

        data.categorias.forEach((categoria) => {
          const option = document.createElement("option");
          option.value = categoria.nombre;
          option.textContent = categoria.nombre;
          categoriaFiltro.appendChild(option);

          const optionMobile = option.cloneNode(true);
          categoriaFiltroMobile?.appendChild(optionMobile);
        });

        data.marcas.forEach((marca) => {
          const option = document.createElement("option");
          option.value = marca.nombre;
          option.textContent = marca.nombre;
          marcaFiltro.appendChild(option);

          const optionMobile = option.cloneNode(true);
          marcaFiltroMobile?.appendChild(optionMobile);
        });

        const setInitialPriceRange = (min, max) => {
          [precioMinFiltro, precioMinFiltroMobile].forEach((element) => {
            if (element) {
              element.min = min;
              element.max = max;
              element.value = min;
            }
          });

          [precioMaxFiltro, precioMaxFiltroMobile].forEach((element) => {
            if (element) {
              element.min = min;
              element.max = max;
              element.value = max;
            }
          });

          [valorPrecioMin, valorPrecioMinMobile].forEach((element) => {
            if (element) element.textContent = min;
          });

          [valorPrecioMax, valorPrecioMaxMobile].forEach((element) => {
            if (element) element.textContent = max;
          });
        };

        setInitialPriceRange(data.precioMin, data.precioMax);
        precioMinSeleccionado = data.precioMin;
        precioMaxSeleccionado = data.precioMax;
      });
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
            <img src="./assets/productos/imagen/${idProducto}/${data.imagen_principal}" alt="${
          data.nombre
        }" class="img-fluid rounded">
          </div>
          <div class="col-12 col-md-8">
            <h3>${data.nombre}</h3>
            <h4>Fragancias disponibles:</h4>
            <div class="fragancias-scroll">
              <ul class="list-inline">
                ${data.fragancias
                  .map(
                    (fragancia) =>
                      `<li class="list-inline-item badge bg-primary">${fragancia}</li>`
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
      });
  };

  // Event Listeners
  const setupEventListeners = () => {
    // Desktop filter events
    if (aplicarFiltro) {
      aplicarFiltro.addEventListener("click", () => {
        categoriaSeleccionada = categoriaFiltro.value;
        marcaSeleccionada = marcaFiltro.value;
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
        precioMinSeleccionado = parseInt(precioMinFiltroMobile.value);
        precioMaxSeleccionado = parseInt(precioMaxFiltroMobile.value);
        paginaActual = 1;

        const offcanvas = document.getElementById("filtrosOffcanvas");
        if (offcanvas) {
          const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
          bsOffcanvas?.hide();
        }

        cargarProductos(paginaActual);
      });
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
          bootstrapModal?.hide();
        }
      });
    }

    document
      .querySelectorAll("#cerrar-modal, #cerrar-modal-footer")
      .forEach((btn) => {
        btn?.addEventListener("click", () => {
          const modal = document.getElementById("modal");
          const bootstrapModal = bootstrap.Modal.getInstance(modal);
          bootstrapModal?.hide();
        });
      });

    // Other events
    mostrarMas?.addEventListener("click", () => {
      paginaActual++;
      cargarProductos(paginaActual);
    });

    ordenarPrecio?.addEventListener("click", () => {
      ordenarDescendente = !ordenarDescendente;
      paginaActual = 1;
      cargarProductos(paginaActual);
    });

    productosPorPaginaSelect?.addEventListener("change", () => {
      productosPorPagina = parseInt(productosPorPaginaSelect.value);
      paginaActual = 1;
      cargarProductos(paginaActual);
    });
  };

  // Initialize
  cargarFiltros();
  setupEventListeners();
  cargarProductos(paginaActual);
});
