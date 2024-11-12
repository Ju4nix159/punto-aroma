<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['sku'], $data['cantidadCambio']) && isset($_SESSION['carrito'][$data['sku']])) {
    $sku = $data['sku'];
    $cantidadCambio = (int) $data['cantidadCambio'];

    $_SESSION['carrito'][$sku]['cantidad'] += $cantidadCambio;

    if ($_SESSION['carrito'][$sku]['cantidad'] <= 0) {
        unset($_SESSION['carrito'][$sku]); // Eliminar el producto si la cantidad es 0 o menos
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
