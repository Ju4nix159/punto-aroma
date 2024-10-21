<?php
include 'header.php';
include 'admin/config/sbd.php';


if (isset($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']);
    $sql_producto = $con->prepare('SELECT p.id_producto, p.nombre, p.descripcion, vtp.precio AS precio
FROM productos p
JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
WHERE p.id_producto = :id_producto AND vtp.id_tipo_precio = 1;');
    $sql_producto->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $sql_producto->execute();
    $info_producto = $sql_producto->fetch(PDO::FETCH_ASSOC);


    $sql_variantes = $con->prepare("SELECT DISTINCT a.nombre AS aroma
        FROM productos p
        JOIN categorias c ON p.id_categoria = c.id_categoria
        JOIN variantes v ON p.id_producto = v.id_producto
        JOIN aromas a ON v.id_aroma = a.id_aroma
        WHERE c.nombre = 'Perfumes' AND p.id_producto = 1;");
    $sql_variantes->execute();
    $variantes = $sql_variantes->fetchAll(PDO::FETCH_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Resumen del Producto - Punto Aroma</title>

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
                    <div class="product-gall">
                        <img src="1.webp" alt="<?php echo $info_producto["nombre"] ?>" class="gall-main-image" id="main-image">
                        <button class="gall-nav prev" onclick="changeImage(-1)">&lt;</button>
                        <button class="gall-nav next" onclick="changeImage(1)">&gt;</button>
                        <div class="gall-thumbnails">
                            <img src="1.webp" alt="Thumbnail 1" class="gall-thumbnail active" onclick="setMainImage(this.src)">
                            <img src="i1.jpg" alt="Thumbnail 2" class="gall-thumbnail" onclick="setMainImage(this.src)">
                            <img src="i2.jpg" alt="Thumbnail 3" class="gall-thumbnail" onclick="setMainImage(this.src)">
                            <img src="i3.jpg" alt="Thumbnail 4" class="gall-thumbnail" onclick="setMainImage(this.src)">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2 class="mb-3 text-primary-custom"><?php echo $info_producto["nombre"] ?></h2>
                    <p class="lead">Disfruta de la calidez y el aroma relajante de nuestras velas aromáticas de alta calidad.</p>
                    <p><strong>Precio:</strong> <?php echo $info_producto["precio"] ?></p>
                    <p>Elige entre nuestras diferentes fragancias y personaliza tu experiencia aromática.</p>

                    <form id="product-form">
                        <div id="fragrances-list">
                            <?php foreach ($variantes as $variante) { ?>
                                <div class="fragrance-item">
                                    <h5><?php echo $variante["aroma"] ?></h5>
                                    <div class="product-count">
                                        <div class="d-flex">
                                            <button type="button" class="btn-primary-custom qtyminus" onclick="decrementQuantity('<?php echo $variante['aroma']?>')">-</button>
                                            <input type="number" id="quantity-<?php echo $variante['aroma']?>" class="cantidad" value="0" min="0" readonly>
                                            <button type="button" class="btn-primary-custom qtyplus" onclick="incrementQuantity('<?php echo $variante['aroma']?>')">+</button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="mt-4">
                            <h4>Total: $<span id="total-price">0.00</span></h4>
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
                    <ul>
                        <li>Duración aproximada de 30 horas</li>
                        <li>Cera de soja natural y ecológica</li>
                        <li>Mecha de algodón sin plomo</li>
                        <li>Fragancias 100% naturales</li>
                        <li>Envase de vidrio reutilizable</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <footer class="">
        <?php include 'footer.php'; ?>
    </footer>

    <script>
        function incrementQuantity(fragrance) {
            const input = document.getElementById(`quantity-${fragrance}`);
            input.value = parseInt(input.value) + 1;
            updateTotalPrice();
        }

        function decrementQuantity(fragrance) {
            const input = document.getElementById(`quantity-${fragrance}`);
            if (parseInt(input.value) > 0) {
                input.value = parseInt(input.value) - 1;
                updateTotalPrice();
            }
        }

        function updateTotalPrice() {
            let total = 0;
            fragrances.forEach(fragrance => {
                const quantity = parseInt(document.getElementById(`quantity-${fragrance}`).value);
                total += quantity * pricePerUnit;
            });
            document.getElementById('total-price').textContent = total.toFixed(2);
        }

        function addToCart() {
            let cartItems = [];
            fragrances.forEach(fragrance => {
                const quantity = parseInt(document.getElementById(`quantity-${fragrance}`).value);
                if (quantity > 0) {
                    cartItems.push({
                        fragrance,
                        quantity
                    });
                }
            });

            if (cartItems.length > 0) {
                console.log('Productos agregados al carrito:', cartItems);
                alert('Productos agregados al carrito con éxito!');
            } else {
                alert('Por favor, selecciona al menos un producto para agregar al carrito.');
            }
        }

        function setMainImage(src) {
            document.getElementById('main-image').src = src;
            document.querySelectorAll('.gall-thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        function changeImage(direction) {
            currentImageIndex += direction;
            if (currentImageIndex < 0) currentImageIndex = images.length - 1;
            if (currentImageIndex >= images.length) currentImageIndex = 0;
            setMainImage(images[currentImageIndex].src);
        }
    </script>
</body>

</html>