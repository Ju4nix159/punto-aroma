<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

// Comprobar si se recibió un carrito desde el cliente
if (isset($data['carrito'])) {
    $_SESSION['carrito'] = $data['carrito'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos del carrito']);
}
?>
