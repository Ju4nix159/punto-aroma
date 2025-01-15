<?php
include 'header.php';
include 'admin/config/sbd.php';

$sql_catalogo = $con->prepare("SELECT p.id_producto, p.nombre, p.descripcion, c.nombre AS categoria, vtp.precio AS precio_minorista, i.ruta AS imagen_principal
FROM productos p
JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
JOIN categorias c ON p.id_categoria = c.id_categoria
LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
WHERE vtp.id_tipo_precio = 1;");
$sql_catalogo->execute();
$productos = $sql_catalogo->fetchAll(PDO::FETCH_ASSOC);

$sql_categorias = $con->prepare("SELECT * FROM categorias WHERE estado = 1 ;");
$sql_categorias->execute();
$categorias = $sql_categorias->fetchAll(PDO::FETCH_ASSOC);

$sql_marcas = $con->prepare("SELECT * FROM marcas WHERE estado = 1;");
$sql_marcas->execute();
$marcas = $sql_marcas->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>

<head>
    <title>Catálogo - Punto Aroma</title>
</head>

<body>
    <main class="py-5">
        <div class="container">
            <h1 class="mb-4 text-primary-custom">Catálogo de Productos</h1>
            <div class="row">
                <!-- Sidebar con filtros -->
                <div class="col-md-3 mb-4">
                    <div class="sticky-sidebar">
                        <h4 class="mb-3">Filtros <i class="fas fa-filter"></i></h4>
                        <div class="accordion" id="accordionFilters">
                            <!-- Búsqueda por texto -->
                            <div class="mb-3">
                                <label for="searchText" class="form-label">Buscar por nombre</label>
                                <input type="text" id="searchText" class="form-control" placeholder="Ingrese nombre del producto">
                            </div>
                            <!-- Categorías -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingCategories">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories" aria-expanded="true" aria-controls="collapseCategories">
                                        Categorías
                                    </button>
                                </h2>
                                <div id="collapseCategories" class="accordion-collapse collapse show" aria-labelledby="headingCategories">
                                    <div class="accordion-body">
                                        <?php foreach ($categorias as $categoria): ?>
                                            <div class="form-check">
                                                <input class="form-check-input category-filter" type="checkbox" value="<?php echo $categoria['id_categoria']; ?>" id="cat<?php echo $categoria['id_categoria']; ?>">
                                                <label class="form-check-label" for="cat<?php echo $categoria['id_categoria']; ?>">
                                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingMarca">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMarca" aria-expanded="false" aria-controls="collapseMarca">
                                        Marcas
                                    </button>
                                </h2>
                                <div id="collapseMarca" class="accordion-collapse collapse" aria-labelledby="headingMarca">
                                    <div class="accordion-body">
                                        <?php foreach ($marcas as $marca): ?>
                                            <div class="form-check">
                                                <input class="form-check-input brand-filter" type="checkbox" value="<?php echo $marca['id_marca']; ?>" id="brand<?php echo $marca['id_marca']; ?>">
                                                <label class="form-check-label" for="brand<?php echo $marca['id_marca']; ?>">
                                                    <?php echo htmlspecialchars($marca['nombre']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Precio -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPrecio">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrecio" aria-expanded="false" aria-controls="collapsePrecio">
                                        Precio
                                    </button>
                                </h2>
                                <div id="collapsePrecio" class="accordion-collapse collapse" aria-labelledby="headingPrecio">
                                    <div class="accordion-body">
                                        <label for="precioMin" class="form-label">Mínimo</label>
                                        <input type="number" id="precioMin" class="form-control" placeholder="Min">
                                        <label for="precioMax" class="form-label">Máximo</label>
                                        <input type="number" id="precioMax" class="form-control" placeholder="Max">
                                    </div>
                                </div>
                            </div>
                            <!-- Orden -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOrder">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrder" aria-expanded="false" aria-controls="collapseOrder">
                                        Ordenar
                                    </button>
                                </h2>
                                <div id="collapseOrder" class="accordion-collapse collapse" aria-labelledby="headingOrder">
                                    <div class="accordion-body">
                                        <select id="orderBy" class="form-select">
                                            <option value="asc">Menor Precio</option>
                                            <option value="desc">Mayor Precio</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Catálogo de productos -->
                <div class="col-md-9">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                        <?php foreach ($productos as $producto) {
                            // Consulta para obtener todas las variantes del producto con estado 1
                            $query_variantes = "SELECT DISTINCT v.nombre_variante
                            FROM variantes v
                            WHERE v.id_producto = :id_producto 
                            AND v.id_estado_producto = 1
                            AND v.color IS NOT NULL";

                            // Preparar y ejecutar la consulta
                            $variantes = $con->prepare($query_variantes);
                            $variantes->bindParam(':id_producto', $producto['id_producto'], PDO::PARAM_INT);
                            $variantes->execute();
                            $variantes_result = $variantes->fetchAll(PDO::FETCH_ASSOC);

                            // Crear un array de variantes para pasarlo como JSON al modal
                            $variantes_array = array_map(function ($variante) {
                                return $variante['nombre_variante']; // Asegúrate de que este sea el campo correcto
                            }, $variantes_result);
                            $variantes_json = json_encode($variantes_array);
                        ?>
                            <div class="col">
                                <div class="card h-100 product-card">
                                    <a href="producto.php?id_producto=<?php echo $producto["id_producto"] ?>" class="text-decoration-none text-dark">
                                        <div class="card-catalogo h-100 d-flex flex-column">
                                            <div class="img-container">
                                                <img src="./assets/productos/<?php echo $producto["imagen_principal"]; ?>" class="card-img-top w-100 h-100 object-fit-cover" alt="<?php echo $producto["id_producto"] ?>">
                                            </div>
                                            <div class="card-body flex-grow-1">
                                                <h5 class="card-title"><?php echo $producto["nombre"] ?></h5>
                                                <p class="card-text"><small class="text-muted">Categoría: <?php echo $producto["categoria"] ?></small></p>
                                                <p class="card-text"><strong><?php echo $producto["precio_minorista"] ?></strong></p>
                                            </div>
                                        </div>
                                    </a>

                                    <div class="card-footer">
                                        <a href="producto.php?id_producto=<?php echo $producto["id_producto"] ?>" class="btn btn-primary-custom w-100">Ver producto</a>
                                    </div>

                                    <button class="btn btn-sm btn-secondary-custom quick-view-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#quickViewModal"
                                        data-product-id="<?php echo $producto['id_producto']; ?>"
                                        data-product-name="<?php echo $producto['nombre']; ?>"
                                        data-product-description="<?php echo $producto['descripcion']; ?>"
                                        data-product-price="<?php echo $producto['precio_minorista']; ?>"
                                        data-product-imagen="<?php echo $producto['imagen_principal']; ?>"
                                        data-product-variants='<?php echo $variantes_json; ?>'
                                        data-product-info="<?php echo 'producto.php?id_producto=' . $producto['id_producto'] ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Modal de Vista Rápida -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickViewModalLabel">Vista Rápida del Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="" class="img-fluid" alt="Producto" id="quickViewImage">
                        </div>
                        <div class="col-md-6">
                            <h2 id="quickViewTitle"></h2>
                            <p id="quickViewDescription"></p>
                            <h4>Fragancias disponibles:</h4>
                            <ul id="quickViewFragrances"></ul>
                            <p><strong>Precio: </strong><span id="quickViewPrice"></span></p>
                            <a id="quickViewMoreInfo" href="">
                                <button class="btn btn-primary-custom">Más información</button>
                            </a>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer class="">
        <?php include 'footer.php'; ?>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchText = document.getElementById('searchText');
            const categoryFilters = document.querySelectorAll('.category-filter');
            const brandFilters = document.querySelectorAll('.brand-filter');
            const precioMin = document.getElementById('precioMin');
            const precioMax = document.getElementById('precioMax');
            const orderBy = document.getElementById('orderBy');

            const filters = {
                search: '',
                categories: [],
                brands: [],
                minPrice: null,
                maxPrice: null,
                order: 'asc'
            };

            function applyFilters() {
                fetch('catalogo_filtrado.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(filters)
                    })
                    .then(response => response.json())
                    .then(data => {
                        const catalogo = document.querySelector('.row-cols-1');
                        catalogo.innerHTML = ''; // Limpiar el catálogo
                        data.forEach(producto => {
                            catalogo.innerHTML += `
                    <div class="col">
                        <div class="card h-100 product-card">
                            <a href="producto.php?id_producto=${producto.id_producto}" class="text-decoration-none text-dark">
                                <div class="card-catalogo h-100 d-flex flex-column">
                                    <div class="img-container">
                                        <img src="./assets/productos/${producto.imagen_principal}" class="card-img-top w-100 h-100 object-fit-cover" alt="${producto.id_producto}">
                                    </div>
                                    <div class="card-body flex-grow-1">
                                        <h5 class="card-title">${producto.nombre}</h5>
                                        <p class="card-text"><small class="text-muted">Categoría: ${producto.categoria}</small></p>
                                        <p class="card-text"><strong>${producto.precio_minorista}</strong></p>
                                    </div>
                                </div>
                            </a>
                            <button class="btn btn-sm btn-secondary-custom quick-view-btn"
                                data-product-id="${producto.id_producto}"
                                data-product-name="${producto.nombre}"
                                data-product-description="${producto.descripcion}"
                                data-product-price="${producto.precio_minorista}"
                                data-product-imagen="${producto.imagen_principal}"
                                data-product-variants='${JSON.stringify(producto.variantes || [])}'
                                data-product-info="producto.php?id_producto=${producto.id_producto}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>`;
                        });

                        // Reasignar los event listeners a los nuevos botones "quick view"
                        initializeQuickViewButtons();
                    });
            }

            searchText.addEventListener('input', () => {
                filters.search = searchText.value;
                applyFilters();
            });

            categoryFilters.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    filters.categories = Array.from(categoryFilters)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);
                    applyFilters();
                });
            });

            brandFilters.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    filters.brands = Array.from(brandFilters)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);
                    applyFilters();
                });
            });

            precioMin.addEventListener('input', () => {
                filters.minPrice = precioMin.value ? parseFloat(precioMin.value) : null;
                applyFilters();
            });

            precioMax.addEventListener('input', () => {
                filters.maxPrice = precioMax.value ? parseFloat(precioMax.value) : null;
                applyFilters();
            });

            orderBy.addEventListener('change', () => {
                filters.order = orderBy.value;
                applyFilters();
            });
        });


        function initializeQuickViewButtons() {
            const quickViewButtons = document.querySelectorAll('.quick-view-btn');

            quickViewButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    const modal = document.getElementById('quickViewModal');
                    const quickViewTitle = document.getElementById('quickViewTitle');
                    const quickViewDescription = document.getElementById('quickViewDescription');
                    const quickViewPrice = document.getElementById('quickViewPrice');
                    const quickViewImage = document.getElementById('quickViewImage');
                    const quickViewFragrances = document.getElementById('quickViewFragrances');
                    const quickViewMoreInfo = document.getElementById('quickViewMoreInfo');

                    const productId = button.getAttribute('data-product-id');
                    const productName = button.getAttribute('data-product-name');
                    const productDescription = button.getAttribute('data-product-description');
                    const productPrice = button.getAttribute('data-product-price');
                    const productImage = button.getAttribute('data-product-imagen');
                    const productVariants = JSON.parse(button.getAttribute('data-product-variants') || '[]');
                    const productInfo = button.getAttribute('data-product-info');

                    quickViewTitle.textContent = productName;
                    quickViewDescription.textContent = productDescription;
                    quickViewPrice.textContent = productPrice;
                    quickViewImage.src = `./assets/productos/${productImage}`;
                    quickViewMoreInfo.href = productInfo;

                    // Limpiar y añadir variantes
                    quickViewFragrances.innerHTML = '';
                    productVariants.forEach(variant => {
                        const li = document.createElement('li');
                        li.textContent = variant;
                        quickViewFragrances.appendChild(li);
                    });

                    // Mostrar el modal
                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();
                });
            });
        }
    </script>
</body>

</html>