<?php
session_start();

// Verificar si el carrito existe y tiene elementos
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode([
        'success' => false,
        'message' => 'El carrito está vacío'
    ]);
    exit;
}

// Calcular el total del carrito
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalCantidadFragancias = 0;
    foreach ($item['fragancias'] as $fragancia) {
        $totalCantidadFragancias += $fragancia['cantidad'];
    }
    $total += $totalCantidadFragancias * $item['precio'];
}

// Devolver el carrito y el total
echo json_encode([
    'success' => true,
    'cart' => $_SESSION['cart'],
    'total' => $total
]);
?>