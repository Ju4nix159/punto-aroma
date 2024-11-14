<?php
session_start();

// Verifica si el carrito existe y tiene productos
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Calcula el total del carrito
    $total = array_reduce($_SESSION['cart'], function ($sum, $item) {
        return $sum + $item['precio'];
    }, 0);

    // Devuelve el contenido del carrito y el total
    echo json_encode([
        "success" => true,
        "cart" => $_SESSION['cart'],
        "total" => number_format($total, 2)
    ]);
} else {
    // Respuesta si el carrito está vacío
    echo json_encode(["success" => false, "message" => "El carrito está vacío"]);
}
?>
