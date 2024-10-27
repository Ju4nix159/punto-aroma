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
                        <h4 class="mb-3">Filtros</h4>
                        <div class="accordion" id="accordionFilters">
                            <!-- Categorías -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingCategories">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories" aria-expanded="true" aria-controls="collapseCategories">
                                        Categorías
                                    </button>
                                </h2>
                                <div id="collapseCategories" class="accordion-collapse collapse show" aria-labelledby="headingCategories">
                                    <div class="accordion-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="catSahumerios">
                                            <label class="form-check-label" for="catSahumerios">Sahumerios</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="catVelas">
                                            <label class="form-check-label" for="catVelas">Velas Aromáticas</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="catPerfumes">
                                            <label class="form-check-label" for="catPerfumes">Perfumes</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fragancias -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFragancias">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFragancias" aria-expanded="false" aria-controls="collapseFragancias">
                                        Fragancias
                                    </button>
                                </h2>
                                <div id="collapseFragancias" class="accordion-collapse collapse" aria-labelledby="headingFragancias">
                                    <div class="accordion-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="fragLavanda">
                                            <label class="form-check-label" for="fragLavanda">Lavanda</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="fragVainilla">
                                            <label class="form-check-label" for="fragVainilla">Vainilla</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="fragCitricos">
                                            <label class="form-check-label" for="fragCitricos">Cítricos</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Marcas -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingMarcas">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMarcas" aria-expanded="false" aria-controls="collapseMarcas">
                                        Marcas
                                    </button>
                                </h2>
                                <div id="collapseMarcas" class="accordion-collapse collapse" aria-labelledby="headingMarcas">
                                    <div class="accordion-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="marcaPuntoAroma">
                                            <label class="form-check-label" for="marcaPuntoAroma">Punto Aroma</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="marcaNaturalEssence">
                                            <label class="form-check-label" for="marcaNaturalEssence">Natural Essence</label>
                                        </div>
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
                                        <input type="range" class="form-range" min="0" max="100" id="precioMin">
                                        <label for="precioMax" class="form-label">Máximo</label>
                                        <input type="range" class="form-range" min="0" max="100" id="precioMax">
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
                            // Si la categoría es "Perfumes", selecciona fragancias; si es "Aromas para el hogar", selecciona colores
                            if ($producto['categoria'] == 'Perfumes') {
                                $query_variantes = "SELECT DISTINCT a.nombre AS aroma_nombre
                        FROM variantes v
                        JOIN aromas a ON v.id_aroma = a.id_aroma
                        WHERE v.id_producto = :id_producto";
                            } elseif ($producto['categoria'] == 'Aromas para el hogar') {
                                $query_variantes = "SELECT DISTINCT c.nombre AS color_nombre
                        FROM variantes v
                        JOIN colores c ON v.id_color = c.id_color
                        WHERE v.id_producto = :id_producto";
                            } else {
                                $query_variantes = null;
                            }

                            if ($query_variantes) {
                                $variantes = $con->prepare($query_variantes);
                                $variantes->bindParam(':id_producto', $producto['id_producto'], PDO::PARAM_INT);
                                $variantes->execute();
                                $variantes_result = $variantes->fetchAll(PDO::FETCH_ASSOC);
                            }

                            // Crear un array de variantes para pasarlo como JSON al modal
                            $variantes_array = array_map(function ($variante) {
                                return isset($variante['aroma_nombre']) ? $variante['aroma_nombre'] : $variante['color_nombre'];
                            }, $variantes_result);
                            $variantes_json = json_encode($variantes_array);

                        ?>
                            <div class="col">
                                <div class="card h-100 product-card">
                                    <a href="producto.php?id_producto=<?php echo $producto["id_producto"] ?>" class="text-decoration-none text-dark">
                                        <div class="card-catalogo h-100 d-flex flex-column">
                                            <div class="img-container">
                                                <img src="../pa/assets/productos/<?php echo $producto["imagen_principal"]; ?>" class="card-img-top w-100 h-100 object-fit-cover" alt="<?php echo $producto["id_producto"] ?>">
                                            </div>

                                            <div class="card-body flex-grow-1">
                                                <h5 class="card-title"><?php echo $producto["nombre"] ?></h5>
                                                <p class="card-text"><small class="text-muted">Categoría: <?php echo $producto["categoria"] ?></small></p>
                                                <p class="card-text"><strong><?php echo $producto["precio_minorista"] ?></strong></p>
                                            </div>
                                        </div>
                                    </a>

                                    <div class="card-footer">
                                        <button class="btn btn-primary-custom w-100 add-to-cart-btn" onclick="añadirCarrito(<?php echo $producto['id_producto'] ?>, '<?php echo addslashes($producto['nombre']) ?>', <?php echo $producto['precio_minorista'] ?>)">Añadir</button>
                                    </div>


                                    <button class="btn btn-sm btn-secondary-custom quick-view-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#quickViewModal"
                                        data-product-id="<?php echo $producto['id_producto']; ?>"
                                        data-product-name="<?php echo $producto['nombre']; ?>"
                                        data-product-description="<?php echo $producto['descripcion']; ?>"
                                        data-product-price="<?php echo $producto['precio_minorista']; ?>"
                                        data-product-imagen="<?php echo $producto['imagen_principal']; ?>"
                                        data-product-variants='<?php echo $variantes_json; ?>'>
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- Paginación mejorada -->
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
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
                            <a href=""><button class="btn btn-primary-custom">Más información</button></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer class="">
        <?php include 'footer.php'; ?>
    </footer>
</body>

</html>