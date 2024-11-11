<?php
include 'header.php';
include 'admin/config/sbd.php';
if (isset($_SESSION["usuario"])) {
    $_SESSION['cart_temp'] = [];
}
if (isset($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']);
    $sql_producto = $con->prepare(' SELECT p.id_producto, p.nombre, p.descripcion, vtp.precio AS precio, i.ruta AS imagen_principal
                                    FROM productos p
                                    JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
                                    LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
                                    WHERE p.id_producto = :id_producto AND vtp.id_tipo_precio = 1;');
    $sql_producto->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $sql_producto->execute();
    $info_producto = $sql_producto->fetch(PDO::FETCH_ASSOC);
    $sql_variantes = $con->prepare("SELECT DISTINCT a.nombre AS aroma, v.sku
FROM productos p
    JOIN categorias c ON p.id_categoria = c.id_categoria
    JOIN variantes v ON p.id_producto = v.id_producto
    JOIN aromas a ON v.id_aroma = a.id_aroma
WHERE c.nombre = 'Perfumes' AND p.id_producto = :id_producto;");
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
                        <img src="../pa/assets/productos<?php echo $info_producto["imagen_principal"] ?>" alt="<?php echo $info_producto["nombre"] ?>" class="gall-main-image" id="main-image">
                        <button class="gall-nav prev" onclick="changeImage(-1)">&lt;</button>
                        <button class="gall-nav next" onclick="changeImage(1)">&gt;</button>
                        <div class="gall-thumbnails">
                            <?php foreach ($imagenes as $imagen) { ?>
                                <img src="../pa/assets/productos<?php echo $imagen['ruta']; ?>" alt="Thumbnail <?php echo $imagen['id_imagen']; ?>" class="gall-thumbnail" onclick="setMainImage(this.src)">
                            <?php } ?>
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
                                            <button type="button" class="btn-primary-custom qtyminus" onclick="decrementQuantity('<?php echo $variante['sku'] ?>', '<?php echo $variante['aroma'] ?>')">-</button>
                                            <input type="number" id="quantity-<?php echo $variante['sku'] ?>" class="cantidad" value="0" min="0" readonly>
                                            <button type="button" class="btn-primary-custom qtyplus" onclick="incrementQuantity('<?php echo $variante['sku'] ?>', '<?php echo $variante['aroma'] ?>')">+</button>
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
</body>
<script>
    // Función para actualizar el carrito temporal en sessionStorage y mostrar en la consola
    function updateTemporaryCart(id_producto, precio, sku, cantidad, nombre_producto) {
        // Obtener el carrito temporal desde sessionStorage
        let carritoTemporal = JSON.parse(sessionStorage.getItem('carritoTemporal')) || {};

        // Si el producto aún no existe en el carrito temporal, inicializamos su estructura
        if (!carritoTemporal[id_producto]) {
            carritoTemporal[id_producto] = {
                nombre: nombre_producto,
                precio: precio,
                fragancias: {}
            };
        }

        // Si la cantidad es mayor a 0, actualizamos la fragancia
        if (cantidad > 0) {
            carritoTemporal[id_producto]['fragancias'][sku] = cantidad;
        } else {
            // Si la cantidad es 0, eliminamos la fragancia específica
            delete carritoTemporal[id_producto]['fragancias'][sku];

            // Si no quedan fragancias, eliminamos el producto completo del carrito temporal
            if (Object.keys(carritoTemporal[id_producto]['fragancias']).length === 0) {
                delete carritoTemporal[id_producto];
            }
        }

        // Guardamos el carrito temporal actualizado en sessionStorage y lo imprimimos en la consola
        sessionStorage.setItem('carritoTemporal', JSON.stringify(carritoTemporal));
        console.log('Carrito temporal:', carritoTemporal);
    }

    // Funciones para incrementar y decrementar la cantidad de una fragancia específica
    function incrementQuantity(sku, aroma) {
        let input = document.getElementById(`quantity-${sku}`);
        let cantidad = parseInt(input.value) + 1;
        input.value = cantidad;

        const id_producto = <?php echo $info_producto["id_producto"]; ?>;
        const nombre_producto = '<?php echo $info_producto["nombre"]; ?>';
        const precio = <?php echo $info_producto["precio"]; ?>;
        updateTemporaryCart(id_producto, precio, sku, cantidad, nombre_producto);
    }

    function decrementQuantity(sku, aroma) {
        let input = document.getElementById(`quantity-${sku}`);
        let cantidad = Math.max(parseInt(input.value) - 1, 0);
        input.value = cantidad;

        const id_producto = <?php echo $info_producto["id_producto"]; ?>;
        const nombre_producto = '<?php echo $info_producto["nombre"]; ?>';
        const precio = <?php echo $info_producto["precio"]; ?>;

        updateTemporaryCart(id_producto, precio, sku, cantidad, nombre_producto);
    }

    // Función para enviar el carrito temporal al servidor cuando se hace clic en "Agregar al Carrito"
    function addToCart() {
        const carritoTemporal = JSON.parse(sessionStorage.getItem('carritoTemporal'));

        if (!carritoTemporal || Object.keys(carritoTemporal).length === 0) {
            alert('Tu carrito está vacío.');
            return;
        }

        // Enviamos el carrito temporal al servidor para almacenarlo en la sesión
        fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(carritoTemporal)
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error del servidor:', data.error);
                } else {
                    console.log('Carrito guardado en la sesión:', data);
                    sessionStorage.removeItem('carritoTemporal'); // Limpia el carrito temporal después de agregarlo a la sesión
                    loadCart(); // Actualiza el carrito en el offcanvas
                }
            })
            .catch(error => console.error('Error al agregar al carrito:', error));
    }
</script>

</html>