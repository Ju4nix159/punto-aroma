<?php
include 'header.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Catálogo</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    #filtros-container {
      position: sticky;
      top: 120px;
      /* Adjusted to ensure it sticks near the top */
      max-height: calc(100vh - 40px);
      /* Limit height to viewport */
      overflow-y: auto;
      /* Enable vertical scrolling */
      z-index: 1000;
    }

    /* Optional: Add smooth scrolling and scrollbar styling */
    #filtros-container {
      scrollbar-width: thin;
      /* For Firefox */
      scrollbar-color: #888 #f1f1f1;
      /* Thumb and track color */
    }

    #filtros-container::-webkit-scrollbar {
      width: 8px;
      /* Thin scrollbar for WebKit browsers */
    }

    #filtros-container::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    #filtros-container::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 4px;
    }

    #filtrosOffcanvas {
      top: 120px;
      /* Ajusta este valor según la altura de tu header */
    }

    .quick-view-btn {
      background-color: var(--primary-color);
      /* Fondo verde */
      border: none;
      /* Sin bordes */
      color: white;
      /* Color del ícono en blanco (puedes cambiarlo si quieres otro color) */
      padding: 10px;
      /* Espaciado alrededor del ícono, puedes ajustarlo */
      border-radius: 5px;
      /* Si quieres bordes redondeados, esto es opcional */
    }

    .quick-view-btn i {
      font-size: 20px;
      /* Tamaño del ícono, ajusta como lo necesites */
    }
  </style>
</head>

<body>
  <div class="container-fluid mt-4">
    <div class="row">
      <!-- Filtros (1 columna a la izquierda) -->
      <aside class="col-lg-3 col-md-4 col-sm-12" id="filtros-container">
        <!-- Offcanvas for mobile devices -->
        <div class="d-lg-block d-md-block d-sm-none d-none"> <!-- Always visible on larger screens -->
          <div class="card shadow-sm p-3">
            <!-- Existing filter content -->
            <h2 class="mb-3">Filtros <i class="fa-solid fa-filter"></i></h2>
            <label for="categoria" class="form-label">Categoría:</label>
            <select id="categoria" class="form-select mb-3">
              <option value="">Todas</option>
            </select>

            <label for="marca" class="form-label">Marca:</label>
            <select id="marca" class="form-select mb-3">
              <option value="">Todas</option>
            </select>

            <label for="nombre" class="form-label">Buscar por nombre:</label>
            <input type="text" id="nombre" class="form-control mb-3" placeholder="Escribe el nombre..." />

            <label for="precioMin" class="form-label">Precio mínimo: <span id="valorPrecioMin">0</span></label>
            <input type="range" id="precioMin" class="form-range mb-3" />

            <label for="precioMax" class="form-label">Precio máximo: <span id="valorPrecioMax">10000</span></label>
            <input type="range" id="precioMax" class="form-range mb-3" />

            <button id="aplicarFiltro" class="btn btn-secondary w-100">Aplicar Filtro</button>
          </div>
        </div>

        <!-- Offcanvas for small screens -->
        <div class="d-lg-none d-md-none d-sm-block">
          <button class="btn btn-secondary w-100 mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosOffcanvas" aria-controls="filtrosOffcanvas">
            Abrir Filtros <i class="fa-solid fa-filter"></i>
          </button>

          <div class="offcanvas offcanvas-start" tabindex="-1" id="filtrosOffcanvas" aria-labelledby="filtrosOffcanvasLabel">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title" id="filtrosOffcanvasLabel">Filtros</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
              <div class="card shadow-sm p-3">
                <!-- Duplicate of filter content above -->
                <label for="categoria-mobile" class="form-label">Categoría:</label>
                <select id="categoria-mobile" class="form-select mb-3">
                  <option value="">Todas</option>
                </select>

                <label for="marca-mobile" class="form-label">Marca:</label>
                <select id="marca-mobile" class="form-select mb-3">
                  <option value="">Todas</option>
                </select>

                <label for="nombre-mobile" class="form-label">Buscar por nombre:</label>
                <input type="text" id="nombre-mobile" class="form-control mb-3" placeholder="Escribe el nombre..." />

                <label for="precioMin-mobile" class="form-label">Precio mínimo: <span id="valorPrecioMin-mobile">0</span></label>
                <input type="range" id="precioMin-mobile" class="form-range mb-3" />

                <label for="precioMax-mobile" class="form-label">Precio máximo: <span id="valorPrecioMax-mobile">10000</span></label>
                <input type="range" id="precioMax-mobile" class="form-range mb-3" />

                <button id="aplicarFiltro-mobile" class="btn btn-secondary w-100">Aplicar Filtro</button>
              </div>
            </div>
          </div>
        </div>
      </aside>


      <!-- Productos (4 columnas) -->
      <section class="col-lg-9 col-md-8 col-sm-12">
        <div id="opciones" class="mb-3 d-flex justify-content-between">
          <button id="ordenarPrecio" class="btn btn-secondary">Ordenar por precio</button>
          <div class="d-flex align-items-center">
            <label for="productosPorPagina" class="me-2">Mostrar:</label>
            <select id="productosPorPagina" class="form-select form-select-sm w-auto">
              <option value="12">12</option>
              <option value="24">24</option>
              <option value="48">48</option>
            </select>
          </div>
        </div>

        <div id="mensajeSinProductos" class="alert alert-warning d-none">
          No se encontraron productos con las características seleccionadas.
        </div>

        <!-- Contenedor de productos con 4 columnas -->
        <div id="catalogo" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-3"></div>

        <div class="d-flex justify-content-center">
          <button id="mostrarMas" class="btn btn-outline-custom" style="display: none">Mostrar más</button>
        </div>
      </section>
    </div>
  </div>

  <!-- Modal para Vista Rápida -->
  <div id="modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle del Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Contenido dinámico del modal -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondariy" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <?php
  include 'footer.php';
  ?>
  <script src="catalogo.js"></script>
</body>

</html>