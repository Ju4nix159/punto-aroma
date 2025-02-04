document.addEventListener("DOMContentLoaded", () => {
  // Elementos de filtro móvil
  const categoriaFiltroMobile = document.getElementById("categoria-mobile");
  const marcaFiltroMobile = document.getElementById("marca-mobile");
  const nombreFiltroMobile = document.getElementById("nombre-mobile");
  const precioMinFiltroMobile = document.getElementById("precioMin-mobile");
  const precioMaxFiltroMobile = document.getElementById("precioMax-mobile");
  const valorPrecioMinMobile = document.getElementById("valorPrecioMin-mobile");
  const valorPrecioMaxMobile = document.getElementById("valorPrecioMax-mobile");
  const aplicarFiltroMobile = document.getElementById("aplicarFiltro-mobile");

  // Función para cargar los filtros en la versión móvil
  const cargarFiltrosMobile = () => {
    fetch("get_catalogo.php")
      .then((response) => response.json())
      .then((data) => {
        // Cargar categorías
        categoriaFiltroMobile.innerHTML = '<option value="">Todas</option>';
        data.categorias.forEach((categoria) => {
          const option = document.createElement("option");
          option.value = categoria.nombre;
          option.textContent = categoria.nombre;
          categoriaFiltroMobile.appendChild(option);
        });

        // Cargar marcas
        marcaFiltroMobile.innerHTML = '<option value="">Todas</option>';
        data.marcas.forEach((marca) => {
          const option = document.createElement("option");
          option.value = marca.nombre;
          option.textContent = marca.nombre;
          marcaFiltroMobile.appendChild(option);
        });

        // Configurar rangos de precio
        precioMinFiltroMobile.min = data.precioMin;
        precioMinFiltroMobile.max = data.precioMax;
        precioMaxFiltroMobile.min = data.precioMin;
        precioMaxFiltroMobile.max = data.precioMax;

        precioMinFiltroMobile.value = data.precioMin;
        precioMaxFiltroMobile.value = data.precioMax;

        valorPrecioMinMobile.textContent = data.precioMin;
        valorPrecioMaxMobile.textContent = data.precioMax;
      });
  };

  // Event listeners para filtros móviles
  aplicarFiltroMobile.addEventListener("click", () => {
    categoriaSeleccionada = categoriaFiltroMobile.value;
    marcaSeleccionada = marcaFiltroMobile.value;
    nombreSeleccionado = nombreFiltroMobile.value;
    precioMinSeleccionado = parseInt(precioMinFiltroMobile.value);
    precioMaxSeleccionado = parseInt(precioMaxFiltroMobile.value);
    paginaActual = 1;
    
    // Cerrar el offcanvas después de aplicar filtros
    const offcanvas = document.getElementById('filtrosOffcanvas');
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
    if (bsOffcanvas) {
      bsOffcanvas.hide();
    }
    
    cargarProductos(paginaActual);
  });

  nombreFiltroMobile.addEventListener("input", () => {
    const valor = nombreFiltroMobile.value.trim();
    if (valor.length >= 3 || valor.length === 0) {
      nombreSeleccionado = valor;
      paginaActual = 1;
      cargarProductos(paginaActual);
    }
  });

  precioMinFiltroMobile.addEventListener("input", () => {
    valorPrecioMinMobile.textContent = precioMinFiltroMobile.value;
  });

  precioMaxFiltroMobile.addEventListener("input", () => {
    valorPrecioMaxMobile.textContent = precioMaxFiltroMobile.value;
  });

  // Llamar a cargarFiltrosMobile cuando se carga la página
  cargarFiltrosMobile();
});