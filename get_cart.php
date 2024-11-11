<?php
session_start();
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    echo json_encode([
        'status' => 'success',
        'cart' => $_SESSION['carrito']
    ]);
} else {
    echo json_encode([
        'status' => 'empty'
    ]);
}
