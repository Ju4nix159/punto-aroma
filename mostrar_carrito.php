<?php
session_start();
$total = 0;

// Verificar la estructura del carrito
if (!isset($_SESSION['carrito'])) {
    echo "<p class='text-center'>Tu carrito está vacío</p>";
    return;
}

if (!is_array($_SESSION['carrito'])) {
    echo "<p>Error: La estructura del carrito es incorrecta.</p>";
    var_dump($_SESSION['carrito']); // Mostrar contenido para depuración
    return;
}

// Estructura correcta, continuar
$productosAgrupados = [];

foreach ($_SESSION['carrito'] as $fragancia) {
    if (!is_array($fragancia) || !isset($fragancia['nombre_producto'], $fragancia['precio_producto'], $fragancia['nombre_fragancia'], $fragancia['cantidad'])) {
        echo "<p>Error: La estructura de un elemento del carrito es incorrecta.</p>";
        var_dump($fragancia); // Mostrar contenido para depuración
        continue;
    }

    $nombreProducto = htmlspecialchars($fragancia['nombre_producto']);
    $precioProducto = (float)$fragancia['precio_producto'];
    $nombreFragancia = htmlspecialchars($fragancia['nombre_fragancia']);
    $cantidad = (int)$fragancia['cantidad'];
    $subtotal = $cantidad * $precioProducto;
    $total += $subtotal;

    if (!isset($productosAgrupados[$nombreProducto])) {
        $productosAgrupados[$nombreProducto] = [
            'precio_producto' => $precioProducto,
            'fragancias' => []
        ];
    }

    $productosAgrupados[$nombreProducto]['fragancias'][] = [
        'nombre_fragancia' => $nombreFragancia,
        'cantidad' => $cantidad,
        'sku' => $fragancia['sku'],
        'subtotal' => $subtotal
    ];
}

// Mostrar el contenido agrupado del carrito
if (!empty($productosAgrupados)) {
    foreach ($productosAgrupados as $nombreProducto => $datosProducto) {
        $precioProducto = number_format($datosProducto['precio_producto'], 2);
        ?>
        <div class="cart-item">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5><?php echo $nombreProducto; ?></h5>
                <p>Cantidad total: <?php echo array_sum(array_column($datosProducto['fragancias'], 'cantidad')); ?></p>
                <p>Precio unitario: $<?php echo $precioProducto; ?></p>
            </div>
            <div class="mt-2">
                <button class="btn btn-secondary" data-toggle="collapse" data-target="#fragancias-<?php echo md5($nombreProducto); ?>">
                    Fragancias
                </button>
                <div id="fragancias-<?php echo md5($nombreProducto); ?>" class="collapse">
                    <?php foreach ($datosProducto['fragancias'] as $fragancia) { ?>
                        <div class="fragancia-item mt-2">
                            <span><?php echo $fragancia['nombre_fragancia']; ?></span>
                            <p>Cantidad: <?php echo $fragancia['cantidad']; ?></p>
                            <p>Subtotal: $<?php echo number_format($fragancia['subtotal'], 2); ?></p>
                            <button class="btn btn-sm btn-danger" onclick="eliminarFragancia('<?php echo $fragancia['sku']; ?>')">
                                Eliminar
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p class='text-center'>Tu carrito está vacío o tiene datos incorrectos.</p>";
}
?>
<script>
    document.getElementById("cart-total").innerText = "$<?php echo number_format($total, 2); ?>";
</script>
