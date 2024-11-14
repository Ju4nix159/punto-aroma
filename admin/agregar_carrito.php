<?php
session_start();

// Verificar si el producto y cantidad están definidos
if (isset($_POST['producto_id']) && isset($_POST['cantidad'])) {
    $producto_id = $_POST['producto_id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];

    // Inicializar el carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Verificar si el producto ya está en el carrito
    if (isset($_SESSION['carrito'][$producto_id])) {
        // Si existe, solo actualizar la cantidad
        $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
    } else {
        // Si no existe, agregar el producto
        $_SESSION['carrito'][$producto_id] = [
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad
        ];
    }

    // Retornar el total de productos en el carrito
    $totalProductos = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
    echo json_encode(['totalProductos' => $totalProductos]);
} else {
    echo json_encode(['error' => 'Datos incompletos']);
}
?>
