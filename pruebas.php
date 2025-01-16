<?php
include("header.php");
include 'admin/config/sbd.php';

$sql_categorias = $con->prepare("SELECT * FROM categorias WHERE estado = 1;");
$sql_categorias->execute();
$categorias = $sql_categorias->fetchAll(PDO::FETCH_ASSOC);

$sql_marcas = $con->prepare("SELECT * FROM marcas WHERE estado = 1;");
$sql_marcas->execute();
$marcas = $sql_marcas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo</title>
    <style>
        .product {
            border: 1px solid #ccc;
            padding: 16px;
            margin: 16px;
            text-align: center;
        }

        .filters {
            display: flex;
            justify-content: space-around;
            margin-bottom: 16px;
        }
    </style>
</head>

<body>
    <div class="filters">
        <select id="categoryFilter">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id_categoria'] ?>">
                    <?= $categoria['nombre'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="brandFilter">
            <option value="">Todas las marcas</option>
            <?php foreach ($marcas as $marca): ?>
                <option value="<?= $marca['id_marca'] ?>">
                    <?= $marca['nombre'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button onclick="applyFilters()">Aplicar filtros</button>
    </div>

    <div id="catalog"></div>
    <button id="loadMore" style="display: none;" onclick="loadMore()">Mostrar más</button>

    <script>
        let currentPage = 1;
        let filters = {
            category: '',
            brand: ''
        };

        async function fetchProducts(page = 1) {
            const params = new URLSearchParams({
                page,
                category: filters.category,
                brand: filters.brand
            });

            const response = await fetch(`get_products.php?${params.toString()}`);
            const data = await response.json();

            return data;
        }

        async function loadCatalog(reset = false) {
            const catalog = document.getElementById('catalog');
            const loadMoreButton = document.getElementById('loadMore');

            if (reset) {
                catalog.innerHTML = '';
                currentPage = 1;
            }

            const products = await fetchProducts(currentPage);

            products.forEach(product => {
                const productDiv = document.createElement('div');
                productDiv.className = 'product';
                productDiv.innerHTML = `
                    <img src="${product.imagen_principal}" alt="${product.nombre}" style="width: 100px; height: 100px;">
                    <h3>${product.nombre}</h3>
                    <p>${product.descripcion}</p>
                    <p>Categoría: ${product.categoria}</p>
                    <p>Precio: $${product.precio_minorista}</p>
                `;
                catalog.appendChild(productDiv);
            });

            if (products.length < 12) {
                loadMoreButton.style.display = 'none';
            } else {
                loadMoreButton.style.display = 'block';
            }

            currentPage++;
        }

        function applyFilters() {
            const categoryFilter = document.getElementById('categoryFilter').value;
            const brandFilter = document.getElementById('brandFilter').value;

            filters.category = categoryFilter;
            filters.brand = brandFilter;

            loadCatalog(true);
        }

        function loadMore() {
            loadCatalog();
        }

        // Load initial catalog
        loadCatalog();
    </script>
</body>

</html>