<?php
include 'header.php';
include 'admin/config/sbd.php';

if (isset($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']);
    $sql_producto = $con->prepare(' SELECT p.id_producto, p.nombre, p.descripcion, vtp.precio AS precio, i.nombre AS imagen_principal, LOWER(m.nombre) as marca
                                FROM productos p
                                JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
                                LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
                                LEFT JOIN marcas m on p.id_marca = m.id_marca 
                                WHERE p.id_producto = :id_producto AND vtp.id_tipo_precio = 2');
    $sql_producto->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $sql_producto->execute();
    $info_producto = $sql_producto->fetch(PDO::FETCH_ASSOC);

    $sql_precios = $con->prepare("SELECT vtp.precio, LOWER(vtp.cantidad_minima) AS cantidad_minima, LOWER(tp.nombre) AS tipo_precio 
    FROM variantes_tipo_precio vtp 
    JOIN tipos_precios tp ON vtp.id_tipo_precio = tp.id_tipo_precio 
    WHERE vtp.id_producto = :id_producto");
    $sql_precios->bindParam(":id_producto", $id_producto, PDO::PARAM_INT);
    $sql_precios->execute();
    $precios = $sql_precios->fetchAll(PDO::FETCH_ASSOC);

    $categorias = [
        "6 productos" => null,
        "48 productos" => null,
        "120 productos" => null
    ];
    foreach ($precios as $precio) {
        if (strtolower($precio["cantidad_minima"]) == 6) {
            $categorias["6 productos"] = $precio["precio"];
        } elseif (strtolower($precio["cantidad_minima"]) == 48) {
            $categorias["48 productos"] = $precio["precio"];
        } elseif (strtolower($precio["cantidad_minima"]) == 120) {
            $categorias["120 productos"] = $precio["precio"];
        }
    }
    $sql_variantes = $con->prepare("SELECT 
    COALESCE(v.aroma, v.titulo, v.color) AS nombre,
    v.sku, 
    v.titulo, 
    v.aroma, 
    v.color, 
    v.id_estado_producto
FROM productos p
JOIN variantes v ON p.id_producto = v.id_producto
WHERE p.id_producto = :id_producto;");
    $sql_variantes->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $sql_variantes->execute();
    $variantes = $sql_variantes->fetchAll(PDO::FETCH_ASSOC);

    $sql_imagenes = $con->prepare(" SELECT i.*
                                    FROM imagenes i
                                    JOIN productos p ON i.id_producto = p.id_producto
                                    WHERE p.id_producto = :id_producto;");
    $sql_imagenes->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $sql_imagenes->execute();
    $imagenes = $sql_imagenes->fetchAll(PDO::FETCH_ASSOC);
};


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Resumen del Producto - Punto Aroma</title>
    <style>
        .sticky-container {
            position: sticky;
            top: 20px;
            /* Adjust this value to control when sticking starts */
        }

        .back-button {
            margin-bottom: 1rem;
        }

        /* Preserve existing styles */
        .icon-container {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .icon-container .hover-table {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .icon-container .hover-table table {
            border-collapse: collapse;
            width: 200px;
        }

        .icon-container .hover-table th,
        .icon-container .hover-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .icon-container .hover-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .icon-container:hover .hover-table {
            display: block;
        }
    </style>
</head>

<body>
    <main class="py-5">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-primary-custom">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="catalogo.php" class="text-decoration-none text-primary-custom">Catálogo</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $info_producto["nombre"] ?></li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-6">
                    <div class="sticky-container">
                        <a href="catalogo.php" class="btn btn-outline-secondary back-button">Volver al Catálogo</a>
                        <div class="product-gall">
                            <?php
                            $image_path = './assets/productos/imagen/' . $id_producto . "/" . ($info_producto["imagen_principal"] ? $info_producto["imagen_principal"] : '/otrasimagenes/noimagen.jpeg');
                            if (!file_exists($image_path)) {
                                $image_path = './assets/productos/otrasimagenes/noimagen.jpeg';
                            }
                            ?>
                            <img src="<?php echo $image_path; ?>" alt="<?php echo $info_producto["nombre"] ?>" class="gall-main-image" id="main-image">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2 class="mb-3 text-primary-custom"><?php echo $info_producto["nombre"] ?></h2>
                    <p class="lead">Disfruta de la calidez y el aroma relajante de nuestras velas aromáticas de alta calidad.</p>
                    <p>
                        <strong>Precio:</strong>
                        <?php if (strtolower($info_producto["marca"]) == 'saphirus') { ?>
                    <div class="icon-container" style="display: inline-flex; align-items: center;">
                        <i class="fas fa-info-circle" style="font-size: 24px; cursor: pointer; margin-right: 10px;"></i>
                        <span><?php echo $info_producto["precio"]; ?></span>
                        <div class="hover-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>6 productos</th>
                                        <th>48 productos</th>
                                        <th>>120 productos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $categorias["6 productos"] ?? 'N/A'; ?></td>
                                        <td><?php echo $categorias["48 productos"] ?? 'N/A'; ?></td>
                                        <td><?php echo $categorias["120 productos"] ?? 'N/A'; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } else { ?>
                    <span><?php foreach ($precios as $precio) {
                                if (strtolower($precio["tipo_precio"]) == "minorista") {
                                    echo $precio["precio"];
                                }
                            }; ?></span>
                <?php } ?>
                </p>

                <p>Elige entre nuestras diferentes fragancias y personaliza tu experiencia aromática.</p>

                <form id="product-form">
                    <input type="hidden" id="id-producto" value="<?php echo $id_producto ?>">
                    <input type="hidden" id="nombre-producto" value="<?php echo $info_producto['nombre'] ?>">
                    <input type="hidden" id="precio-producto" value="<?php echo $info_producto["precio"]; ?>">
                    <input type="hidden" id="precio-6-productos" value="<?php echo isset($categorias["6 productos"]) ? $categorias["6 productos"] : ''; ?>">
                    <input type="hidden" id="precio-48-productos" value="<?php echo isset($categorias["48 productos"]) ? $categorias["48 productos"] : ''; ?>">
                    <input type="hidden" id="precio-120-productos" value="<?php echo isset($categorias["120 productos"]) ? $categorias["120 productos"] : ''; ?>">

                    <div class="card mb-4">
                        <div class="card-body">
                            <div id="variants">
                                <?php foreach ($variantes as $variante) { ?>
                                    <?php if ($variante['id_estado_producto'] == 1) { ?>
                                        <div class="fragrance-item" data-sku="<?php echo $variante['sku']; ?>" data-aroma="<?php echo $variante['aroma']; ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?php echo $variante["nombre"] ?></strong>
                                                </div>

                                                <div class="product-count">
                                                    <div class="d-flex align-items-center">
                                                        <button type="button" class="btn-primary-custom qtyminus" onclick="decrementQuantity('<?php echo $variante['sku'] ?>')">-</button>

                                                        <input type="number" id="quantity-<?php echo $variante['sku'] ?>" class="cantidad" value="0" min="0">
                                                        <button type="button" class="btn-primary-custom qtyplus" onclick="incrementQuantity('<?php echo $variante['sku'] ?>')">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>



                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h4>Total: $<span id="total-price">0.00</span></h4>
                        <p class="text-muted" id="leyendaPrecio">Agregue 6 productos para tener el precio por mayor </p>
                    </div>

                    <button type="button" class="btn btn-primary-custom btn-lg mt-3" onclick="addToCart()">
                        Agregar al Carrito
                    </button>

                </form>

                <a href="catalogo.php" class="btn btn-outline-secondary mt-3">Volver al Catálogo</a>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-primary-custom">Descripción del Producto</h3>
                    <p><?php echo $info_producto["descripcion"] ?></p>
                    <p>Características:</p>
                    <!-- <ul>
                        <li>Duración aproximada de 30 horas</li>
                        <li>Cera de soja natural y ecológica</li>
                        <li>Mecha de algodón sin plomo</li>
                        <li>Fragancias 100% naturales</li>
                        <li>Envase de vidrio reutilizable</li>
                    </ul> -->
                </div>
            </div>
        </div>
    </main>

    <footer class="">
        <?php include 'footer.php'; ?>
    </footer>
</body>

</html>