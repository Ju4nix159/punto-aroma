<?php
session_start();

// Recibimos el carrito completo desde el cliente
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $_SESSION['carrito'] = $data;
    echo json_encode($_SESSION['carrito']);  // Enviar el carrito actualizado al frontend
} else {
    echo json_encode(['error' => 'No se pudo actualizar el carrito']);
}
?>
