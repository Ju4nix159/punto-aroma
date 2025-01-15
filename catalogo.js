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
          catalogo.style.display = "grid";
          data.productos.forEach((producto) => {
            const div = document.createElement("div");
            div.classList.add("producto");
            div.innerHTML = `
                            <img src="./assets/productos/${producto.imagen_principal}" alt="${producto.nombre}">
                            <h3>${producto.nombre}</h3>
                            <p>Marca: ${producto.marca}</p>
                            <p>${producto.descripcion}</p>
                            <p>Precio: $${producto.precio_minorista}</p>
                            <button class="vista-rapida" data-id="${producto.id_producto}">Vista Rápida</button>`;
            catalogo.appendChild(div);
          });
          mostrarMas.style.display =
            data.total > pagina * productosPorPagina ? "block" : "none";

          // Asignar evento a botones de vista rápida
          document.querySelectorAll(".vista-rapida").forEach((boton) => {
            boton.addEventListener("click", (e) => {
              const idProducto = e.target.dataset.id;
              abrirModal(idProducto);
            });
          });
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
        modal.querySelector(".modal-content").innerHTML = `
          <button id="cerrar-modal" class="cerrar-modal">×</button>
          <img src="./assets/productos/${data.imagen_principal}" alt="${data.nombre}" style="max-width: 100%; height: auto;">
          <h3>${data.nombre}</h3>
          <p>${data.descripcion}</p>
          <h4>Fragancias disponibles:</h4>
          <ul>
            ${data.fragancias.map((fragancia) => `<li>${fragancia}</li>`).join("")}
          </ul>
          <button class="comprar" onclick="location.href='producto.php?id_producto=${idProducto}'">Comprar</button>`;
        modal.style.display = "flex";
  
        // Vincula el evento de cierre al botón dinámico
        document.getElementById("cerrar-modal").addEventListener("click", () => {
          modal.style.display = "none";
        });
      });
  };
  
  // Cerrar modal al hacer clic fuera del contenido
  document.getElementById("modal").addEventListener("click", (event) => {
    if (event.target.id === "modal") {
      document.getElementById("modal").style.display = "none";
    }
  });
  

  // Cerrar modal
  document.getElementById("cerrar-modal").addEventListener("click", () => {
    document.getElementById("modal").style.display = "none";
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
