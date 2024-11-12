<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Extraer los datos del producto
$nombreProducto = $data['producto']['nombre'];
$precioProducto = $data['producto']['precio'];
$fragancias = $data['producto']['fragancias'];

foreach ($fragancias as $fragancia) {
    $sku = $fragancia['sku'];

    // Verificar si el producto ya está en el carrito
    if (isset($_SESSION['carrito'][$sku])) {
        // Si ya existe, simplemente sumamos la cantidad
        $_SESSION['carrito'][$sku]['cantidad'] += $fragancia['cantidad'];
    } else {
        // Si no existe, lo agregamos normalmente
        $_SESSION['carrito'][$sku] = [
            'nombre_producto' => $nombreProducto, 
            'precio_producto' => $precioProducto,
            'nombre_fragancia' => $fragancia['nombre'],
            'cantidad' => $fragancia['cantidad'],
            'sku' => $sku
        ];
    }
}


echo json_encode(['success' => true]);
?>
