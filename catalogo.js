document.addEventListener("DOMContentLoaded", () => {
  const catalogo = document.getElementById("catalogo");
  const mostrarMas = document.getElementById("mostrarMas");
  const ordenarPrecio = document.getElementById("ordenarPrecio");
  const productosPorPaginaSelect =
    document.getElementById("productosPorPagina");
  const categoriaFiltro = document.getElementById("categoria");
  const marcaFiltro = document.getElementById("marca");
  const nombreFiltro = document.getElementById("nombre");
  const precioMinFiltro = document.getElementById("precioMin");
  const precioMaxFiltro = document.getElementById("precioMax");
  const valorPrecioMin = document.getElementById("valorPrecioMin");
  const valorPrecioMax = document.getElementById("valorPrecioMax");
  const aplicarFiltro = document.getElementById("aplicarFiltro");
  const mensajeSinProductos = document.getElementById("mensajeSinProductos");

  let paginaActual = 1;
  let categoriaSeleccionada = "";
  let marcaSeleccionada = "";
  let nombreSeleccionado = "";
  let precioMinSeleccionado = 0;
  let precioMaxSeleccionado = 10000;
  let ordenarDescendente = false;
  let productosPorPagina = 12;

  const cargarFiltros = () => {
    fetch("get_catalogo.php")
      .then((response) => response.json())
      .then((data) => {
        categoriaFiltro.innerHTML = '<option value="">Todas</option>';
        data.categorias.forEach((categoria) => {
          const option = document.createElement("option");
          option.value = categoria.nombre;
          option.textContent = categoria.nombre;
          categoriaFiltro.appendChild(option);
        });

        marcaFiltro.innerHTML = '<option value="">Todas</option>';
        data.marcas.forEach((marca) => {
          const option = document.createElement("option");
          option.value = marca.nombre;
          option.textContent = marca.nombre;
          marcaFiltro.appendChild(option);
        });

        precioMinFiltro.min = data.precioMin;
        precioMinFiltro.max = data.precioMax;
        precioMaxFiltro.min = data.precioMin;
        precioMaxFiltro.max = data.precioMax;

        precioMinFiltro.value = data.precioMin;
        precioMaxFiltro.value = data.precioMax;

        valorPrecioMin.textContent = data.precioMin;
        valorPrecioMax.textContent = data.precioMax;
      });
  };

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
            div.classList.add("col-12", "col-sm-6", "col-md-4", "col-lg-3"); // Aseguramos que cada tarjeta ocupe un tamaño proporcional
            div.innerHTML = `
              <div class="card h-100 product-card">
                <div class="img-container position-relative">
                  <img src="./assets/productos/${producto.imagen_principal}" class="card-img-top" alt="${producto.nombre}">
                  <button class="quick-view-btn " data-id="${producto.id_producto}">Vista rápida</button>
                </div>
                <div class="card-body">
                  <h5 class="card-title">${producto.nombre}</h5>
                  <p class="card-text"><small class="text-muted">Categoría: ${producto.categoria}</small></p>
                  <p class="card-text"><small class="text-muted">Categoría: ${producto.marca}</small></p>
                  <p class="card-text"><strong>$${producto.precio_minorista}</strong></p>
                </div>
                <div class="card-footer">
                  <a href="producto.php?id_producto=${producto.id_producto}" class="btn btn-primary-custom w-100">Ver producto</a>
                </div>
              </div>
            `;
            catalogo.appendChild(div);
          });

          // Agregar evento a los botones de "Vista rápida"
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

  // Abrir el modal
  const abrirModal = (idProducto) => {
    const url = `get_frangancias.php?id_producto=${idProducto}`;
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        const modal = document.getElementById("modal");
        modal.querySelector(".modal-body").innerHTML = `
        <div class="row g-4">
          <div class="col-12 col-md-4">
            <img src="./assets/productos/${data.imagen_principal}" alt="${
          data.nombre
        }" class="img-fluid rounded">
          </div>
          <div class="col-12 col-md-8">
            <h3>${data.nombre}</h3>
            <p>${data.descripcion}</p>
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
        // Usar Bootstrap para mostrar el modal
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
      });
  };

  // Cerrar modal
  document.getElementById("modal").addEventListener("click", (event) => {
    if (event.target.id === "modal") {
      const bootstrapModal = bootstrap.Modal.getInstance(event.currentTarget);
      bootstrapModal.hide();
    }
  });

  // Asociar eventos a los botones de cierre
  document
    .querySelectorAll("#cerrar-modal, #cerrar-modal-footer")
    .forEach((btn) => {
      btn.addEventListener("click", () => {
        const modal = document.getElementById("modal");
        const bootstrapModal = bootstrap.Modal.getInstance(modal);
        bootstrapModal.hide();
      });
    });

  mostrarMas.addEventListener("click", () => {
    paginaActual++;
    cargarProductos(paginaActual);
  });

  ordenarPrecio.addEventListener("click", () => {
    ordenarDescendente = !ordenarDescendente;
    paginaActual = 1;
    cargarProductos(paginaActual);
  });

  productosPorPaginaSelect.addEventListener("change", () => {
    productosPorPagina = parseInt(productosPorPaginaSelect.value);
    paginaActual = 1;
    cargarProductos(paginaActual);
  });

  aplicarFiltro.addEventListener("click", () => {
    categoriaSeleccionada = categoriaFiltro.value;
    marcaSeleccionada = marcaFiltro.value;
    precioMinSeleccionado = parseInt(precioMinFiltro.value);
    precioMaxSeleccionado = parseInt(precioMaxFiltro.value);
    paginaActual = 1;
    cargarProductos(paginaActual);
  });

  nombreFiltro.addEventListener("input", () => {
    const valor = nombreFiltro.value.trim();
    if (valor.length >= 3 || valor.length === 0) {
      nombreSeleccionado = valor;
      paginaActual = 1;
      cargarProductos(paginaActual);
    }
  });

  precioMinFiltro.addEventListener("input", () => {
    valorPrecioMin.textContent = precioMinFiltro.value;
  });

  precioMaxFiltro.addEventListener("input", () => {
    valorPrecioMax.textContent = precioMaxFiltro.value;
  });

  cargarFiltros();
  cargarProductos(paginaActual);
});
