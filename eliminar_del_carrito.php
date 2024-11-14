<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['sku']) && isset($_SESSION['carrito'][$data['sku']])) {
    unset($_SESSION['carrito'][$data['sku']]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
