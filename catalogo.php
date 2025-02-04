<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Catálogo</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    #filtros-container {
      position: static;
      max-height: none;
      scrollbar-width: thin;
      scrollbar-color: #888 #f1f1f1;
    }

    #filtros-container::-webkit-scrollbar {
      width: 8px;
    }

    #filtros-container::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    #filtros-container::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 4px;
    }

    .mobile-filters {
      position: fixed;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      width: 90%;
      max-width: 300px;
      z-index: 1049;
    }

    .mobile-filters button {
      width: 100%;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .offcanvas {
      z-index: 1052;
    }

    /* Aseguramos que el offcanvas ocupe toda la altura y comience desde arriba */
    .offcanvas-start {
      top: 0 !important;
      height: 100vh !important;
      transform: translateX(-100%);
    }

    /* Ajustamos el contenido del offcanvas */
    .offcanvas-body {
      padding: 1rem;
      overflow-y: auto;
    }

    /* Aseguramos que los controles del filtro sean usables */
    .offcanvas-body .card {
      margin-bottom: 1rem;
    }

    .offcanvas-body .form-select,
    .offcanvas-body .form-control,
    .offcanvas-body .form-range {
      margin-bottom: 1rem;
    }

    .quick-view-btn {
      background-color: var(--primary-color);
      border: none;
      color: white;
      padding: 10px;
      border-radius: 5px;
    }

    .quick-view-btn i {
      font-size: 20px;
    }

    /* Catalog grid styles */
    .row-cols-1>* {
      flex: 0 0 auto;
      width: 100%;
    }

    @media (min-width: 576px) {
      .row-cols-sm-2>* {
        flex: 0 0 auto;
        width: 50%;
      }
    }

    @media (min-width: 768px) {
      .row-cols-md-3>* {
        flex: 0 0 auto;
        width: 33.333333%;
      }
    }

    @media (min-width: 992px) {
      .row-cols-lg-4>* {
        flex: 0 0 auto;
        width: 25%;
      }
    }

    /* Modal styles */
    .modal-dialog-centered {
      display: flex;
      align-items: center;
      min-height: calc(100% - 1rem);
    }

    /* Filter button styles */
    .btn-secondary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-secondary:hover {
      background-color: var(--primary-color-dark);
      border-color: var(--primary-color-dark);
    }

    /* Filter inputs */
    .form-select,
    .form-control {
      border-radius: 0.375rem;
    }

    .form-range {
      width: 100%;
    }
  </style>
</head>

<body>
  <div class="container-fluid mt-4">
    <div class="row">
      <!-- Filtros -->
      <aside class="col-lg-3 col-md-4 col-sm-12" id="filtros-container">
        <!-- Desktop filters -->
        <div class="d-lg-block d-md-block d-sm-none d-none">
          <div class="card shadow-sm p-3">
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

        <!-- Mobile filters button -->
        <div class="d-lg-none d-md-none d-sm-block mobile-filters">
          <button class="btn btn-secondary w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosOffcanvas">
            Abrir Filtros <i class="fa-solid fa-filter"></i>
          </button>
        </div>
      </aside>

      <!-- Mobile filters offcanvas -->
      <div class="offcanvas offcanvas-start" tabindex="-1" id="filtrosOffcanvas">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title">Filtros</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <div class="card shadow-sm p-3">
            <label for="categoria-mobile" class="form-label">Categoría:</label>
            <select id="categoria-mobile" class="form-select mb-3">
              <option value="">Todas</option>
              <?php
              // Suponiendo que tienes un array $categorias con las categorías disponibles
              foreach ($categorias as $categoria) {
          echo "<option value=\"{$categoria['id']}\">{$categoria['nombre']}</option>";
              }
              ?>
            </select>

            <label for="marca-mobile" class="form-label">Marca:</label>
            <select id="marca-mobile" class="form-select mb-3">
              <option value="">Todas</option>
              <?php
              // Suponiendo que tienes un array $marcas con las marcas disponibles
              foreach ($marcas as $marca) {
          echo "<option value=\"{$marca['id']}\">{$marca['nombre']}</option>";
              }
              ?>
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

      <!-- Productos -->
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

        <div id="catalogo" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-3"></div>

        <div class="d-flex justify-content-center">
          <button id="mostrarMas" class="btn btn-outline-custom" style="display: none">Mostrar más</button>
        </div>
      </section>
    </div>
  </div>

  <!-- Modal Vista Rápida -->
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
  <script src="catalogo.js"></script>
  <script src="filtrosMobile.js"></script>
</body>

</html>