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
    #modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      align-items: center;
      justify-content: center;
    }

    #modal .modal-dialog {
      max-width: 600px;
    }

    .product-card .card-img-top {
      object-fit: cover;
      /* Esto asegura que las imágenes mantengan la proporción, sin deformarse */
      height: 200px;
      /* Establecer una altura fija para las imágenes */
    }

    .product-card .card-body {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 150px;
      /* Esto asegura que el contenido se ajuste dentro de un alto fijo */
    }

    .product-card .card-footer {
      margin-top: auto;
      /* Esto asegura que el pie de la tarjeta siempre esté al final */
    }

    .card-body .card-text {
      font-size: 14px;
    }

    .quick-view-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 3px;
      cursor: pointer;
    }

    .quick-view-btn:hover {
      background-color: rgba(0, 0, 0, 0.7);
    }

    /* Modal con imagen a la izquierda y texto a la derecha */
    .modal-content .row {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-content .col-md-4 {
      max-width: 250px;
      /* Imagen no tan grande */
    }

    .modal-content .col-md-8 {
      max-width: 500px;
      /* Espacio suficiente para el texto */
    }

    /* Para las fragancias, hacerlas deslizar si son muchas */
    .fragancias-scroll {
      max-width: 100%;
      overflow-x: auto;
      /* Desplazamiento horizontal si hay muchas fragancias */
      white-space: nowrap;
      /* Evita que las fragancias se ajusten a varias líneas */
      margin-top: 10px;
    }

    .fragancias-scroll .list-inline-item {
      margin-right: 15px;
      /* Espacio entre cada fragancia */
      font-size: 14px;
    }

    /* Mejorar la apariencia de la imagen */
    .modal-content img {
      object-fit: cover;
      /* Asegura que la imagen no se deforme */
      height: auto;
      width: 100%;
      /* Ajuste proporcional */
    }

    /* Asegura que el modal sea desplazable si el contenido es demasiado largo */
    .modal-body {
      max-height: 400px;
      overflow-y: auto;
    }

    .fragancias-scroll ul {
      padding: 0;
      margin: 0;
      list-style: none;
    }

    .fragancias-scroll ul li {
      display: inline-block;
      margin-right: 10px;
    }

    .img-fluid {
      max-width: 100%;
      height: auto;
      border-radius: 10px;
    }

    #filtros-container {
      position: sticky;
      top: 120px;
      /* Distancia desde la parte superior de la pantalla */
      height: fit-content;
      /* Ajusta la altura al contenido */
      background-color: #f8f9fa;
      /* Fondo claro para distinguirlo */
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      /* Sombra para darle un aspecto flotante */
      z-index: 1000;
      /* Asegura que el filtro esté sobre otros elementos */
    }
  </style>
</head>

<body>
  <div class="container-fluid mt-4">
    <div class="row">
      <!-- Filtros (1 columna a la izquierda) -->
      <aside class="col-lg-3 col-md-4 col-sm-12" id="filtros-container">
        <h2 class="mb-3">Filtros <i class="fa-solid fa-filter"></i></h2>
        <label for="categoria">Categoría:</label>
        <select id="categoria" class="form-select mb-3">
          <option value="">Todas</option>
        </select>

        <label for="marca">Marca:</label>
        <select id="marca" class="form-select mb-3">
          <option value="">Todas</option>
        </select>

        <label for="nombre">Buscar por nombre:</label>
        <input type="text" id="nombre" class="form-control mb-3" placeholder="Escribe el nombre..." />

        <label for="precioMin">Precio mínimo: <span id="valorPrecioMin">0</span></label>
        <input type="range" id="precioMin" class="form-range mb-3" />

        <label for="precioMax">Precio máximo: <span id="valorPrecioMax">10000</span></label>
        <input type="range" id="precioMax" class="form-range mb-3" />

        <button id="aplicarFiltro" class="btn btn-secondary-custom w-100 mb-3">Aplicar Filtro</button>
      </aside>

      <!-- Productos (4 columnas) -->
      <section class="col-lg-9 col-md-8 col-sm-12">
        <div id="opciones" class="mb-3 d-flex justify-content-between">
          <button id="ordenarPrecio" class="btn btn-secondary">Ordenar por precio</button>
          <div>
            <label for="productosPorPagina">Mostrar:</label>
            <select id="productosPorPagina" class="form-select d-inline-block w-auto">
              <option value="12">12</option>
              <option value="24">24</option>
              <option value="48">48</option>
            </select>
          </div>
        </div>

        <div id="mensajeSinProductos" class="alert alert-warning" style="display: none">
          No se encontraron productos con las características seleccionadas.
        </div>
        <!-- Contenedor de productos con 4 columnas -->
        <div id="catalogo" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-3"></div>
        <div class="d-flex justify-content-center">
          <button id="mostrarMas" class="btn btn-primary-custom btn-outline-primary w-10 m-3" style="display: none">Mostrar más</button>
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
          <button type="button" id="cerrar-modal" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Contenido dinámico del modal -->
        </div>
        <div class="modal-footer">
          <button type="button" id="cerrar-modal-footer" class="btn btn-secondary">Cerrar</button>
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