<?php
include 'header.php';
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
                        <!-- Producto 1 -->
                        <div class="col">
                            <div class="card h-100 product-card">
                                <a href="producto.php" class="text-decoration-none text-dark">
                                    <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="Sahumerio de Lavanda">
                                    <div class="card-body">
                                        <h5 class="card-title">Sahumerio de Lavanda</h5>
                                        <p class="card-text"><small class="text-muted">Categoría: Sahumerios</small></p>
                                        <p class="card-text"><strong>$9.99</strong></p>
                                    </div>
                                </a>
                                <div class="card-footer">
                                    <button class="btn btn-primary-custom w-100 add-to-cart-btn" data-product-id="1">Añadir</button>
                                </div>
                                <button class="btn btn-sm btn-secondary-custom quick-view-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Producto 2 -->
                        <div class="col">
                            <div class="card h-100 product-card">
                                <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="Vela Aromática de Vainilla">
                                <div class="card-body">
                                    <h5 class="card-title">Vela Aromática de Vainilla</h5>
                                    <p class="card-text"><small class="text-muted">Categoría: Velas Aromáticas</small></p>
                                    <p class="card-text"><strong>$14.99</strong></p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary-custom w-100">Añadir</button>
                                </div>
                                <button class="btn btn-sm btn-secondary-custom quick-view-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="2">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Producto 3 -->
                        <div class="col">
                            <div class="card h-100 product-card">
                                <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="Perfume Cítrico">
                                <div class="card-body">
                                    <h5 class="card-title">Perfume Cítrico</h5>
                                    <p class="card-text"><small class="text-muted">Categoría: Perfumes</small></p>
                                    <p class="card-text"><strong>$24.99</strong></p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary-custom w-100">Añadir</button>
                                </div>
                                <button class="btn btn-sm btn-secondary-custom quick-view-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="3">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Producto 4 -->
                        <div class="col">
                            <div class="card h-100 product-card">
                                <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="Sahumerio de Sándalo">
                                <div class="card-body">
                                    <h5 class="card-title">Sahumerio de Sándalo</h5>
                                    <p class="card-text"><small class="text-muted">Categoría: Sahumerios</small></p>
                                    <p class="card-text"><strong>$11.99</strong></p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary-custom w-100">Añadir</button>
                                </div>
                                <button class="btn btn-sm btn-secondary-custom quick-view-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="4">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Producto 5 -->
                        <div class="col">
                            <div class="card h-100 product-card">
                                <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="Vela Aromática de Canela">
                                <div class="card-body">
                                    <h5 class="card-title">Vela Aromática de Canela</h5>
                                    <p class="card-text"><small class="text-muted">Categoría: Velas Aromáticas</small></p>
                                    <p class="card-text"><strong>$16.99</strong></p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary-custom w-100">Añadir</button>
                                </div>
                                <button class="btn btn-sm btn-secondary-custom quick-view-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="5">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Producto 6 -->
                        <div class="col">
                            <div class="card h-100 product-card">
                                <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="Perfume Floral">
                                <div class="card-body">
                                    <h5 class="card-title">Perfume Floral</h5>
                                    <p class="card-text"><small class="text-muted">Categoría: Perfumes</small></p>
                                    <p class="card-text"><strong>$29.99</strong></p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary-custom w-100">Añadir</button>
                                </div>
                                <button class="btn btn-sm btn-secondary-custom quick-view-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="6">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Producto 7 -->
                        <div class="col">
                            <div class="card h-100 product-card">
                                <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="Sahumerio de Palo Santo">
                                <div class="card-body">
                                    <h5 class="card-title">Sahumerio de Palo Santo</h5>
                                    <p class="card-text"><small class="text-muted">Categoría: Sahumerios</small></p>
                                    <p class="card-text"><strong>$13.99</strong></p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary-custom w-100">Añadir</button>
                                </div>
                                <button class="btn btn-sm btn-secondary-custom quick-view-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="7">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Producto 8 -->
                        <div class="col">
                            <div class="card h-100 product-card">
                                <img src="/placeholder.svg?height=200&width=300" class="card-img-top" alt="Vela Aromática de Jazmín">
                                <div class="card-body">
                                    <h5 class="card-title">Vela Aromática de Jazmín</h5>
                                    <p class="card-text"><small class="text-muted">Categoría: Velas Aromáticas</small></p>
                                    <p class="card-text"><strong>$15.99</strong></p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary-custom w-100">Añadir</button>
                                </div>
                                <button class="btn btn-sm btn-secondary-custom quick-view-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="8">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
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
                            <img src="/placeholder.svg?height=300&width=300" class="img-fluid" alt="Producto" id="quickViewImage">
                        </div>
                        <div class="col-md-6">
                            <h2 id="quickViewTitle"></h2>
                            <p id="quickViewDescription"></p>
                            <h4>Fragancias disponibles:</h4>
                            <ul id="quickViewFragrances"></ul>
                            <p><strong>Precio: </strong><span id="quickViewPrice"></span></p>
                            <button class="btn btn-primary-custom">Mas informacion</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-3 bg-primary-light mt-5">
        <div class="container">
            <p class="text-center text-muted mb-0">&copy; 2024 Punto Aroma. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script>
        // Datos de ejemplo para los productos
        const products = [{
                id: 1,
                name: "Sahumerio de Lavanda",
                description: "Relájate con el aroma suave y calmante de nuestro sahumerio de lavanda.",
                price: "$9.99",
                fragrances: ["Lavanda", "Lavanda y Vainilla", "Lavanda y Eucalipto"]
            },
            {
                id: 2,
                name: "Vela Aromática de Vainilla",
                description: "Disfruta del aroma dulce y acogedor de nuestra vela de vainilla.",
                price: "$14.99",
                fragrances: ["Vainilla", "Vainilla y Canela", "Vainilla y Coco"]
            },
            {
                id: 3,
                name: "Perfume Cítrico",
                description: "Refréscate con nuestro perfume de notas cítricas y energizantes.",
                price: "$24.99",
                fragrances: ["Limón", "Naranja", "Pomelo"]
            },
            {
                id: 4,
                name: "Sahumerio de Sándalo",
                description: "Experimenta la calidez y el misticismo del aroma a sándalo.",
                price: "$11.99",
                fragrances: ["Sándalo", "Sándalo y Pachuli", "Sándalo y Cedro"]
            },
            {
                id: 5,
                name: "Vela Aromática de Canela",
                description: "Crea un ambiente cálido y acogedor con nuestra vela de canela.",
                price: "$16.99",
                fragrances: ["Canela", "Canela y Manzana", "Canela y Naranja"]
            },
            {
                id: 6,
                name: "Perfume Floral",
                description: "Envuélvete en un bouquet de aromas florales frescos y delicados.",
                price: "$29.99",
                fragrances: ["Jazmín", "Rosa", "Gardenia"]
            },
            {
                id: 7,
                name: "Sahumerio de Palo Santo",
                description: "Purifica tu espacio con el aroma sagrado del palo santo.",
                price: "$13.99",
                fragrances: ["Palo Santo", "Palo Santo y Salvia", "Palo Santo y Romero"]
            },
            {
                id: 8,
                name: "Vela Aromática de Jazmín",
                description: "Llena tu hogar con el aroma exótico y relajante del jazmín.",
                price: "$15.99",
                fragrances: ["Jazmín", "Jazmín y Vainilla", "Jazmín y Sándalo"]
            }
        ];
        // Función para actualizar el modal de vista rápida
        function updateQuickViewModal(productId) {
            const product = products.find(p => p.id === productId);
            if (product) {
                document.getElementById('quickViewTitle').textContent = product.name;
                document.getElementById('quickViewDescription').textContent = product.description;
                document.getElementById('quickViewPrice').textContent = product.price;
                const fragrancesList = document.getElementById('quickViewFragrances');
                fragrancesList.innerHTML = '';
                product.fragrances.forEach(fragrance => {
                    const li = document.createElement('li');
                    li.textContent = fragrance;
                    fragrancesList.appendChild(li);
                });
            }
        }
        // Event listener para los botones de vista rápida
        document.querySelectorAll('.quick-view-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-product-id'));
                updateQuickViewModal(productId);
            });
        });
        // Detener la propagación del evento de clic en los botones
        document.querySelectorAll('.add-to-cart-btn, .quick-view-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation(); // Evita que el clic se propague al enlace
            });
        });
    </script>
</body>
</html>