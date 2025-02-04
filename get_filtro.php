<?php
// Conexión a la base de datos
require_once './admin/config/db.php';

try {

    // Obtener categorías
    $stmt = $conn->prepare("SELECT DISTINCT nombre FROM categorias ORDER BY nombre");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener marcas
    $stmt = $conn->prepare("SELECT DISTINCT nombre FROM marcas ORDER BY nombre");
    $stmt->execute();
    $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener rango de precios
    $stmt = $conn->prepare("SELECT MIN(precio_minorista) as precioMin, MAX(precio_minorista) as precioMax FROM productos");
    $stmt->execute();
    $precios = $stmt->fetch(PDO::FETCH_ASSOC);

    $response = [
        'categorias' => $categorias,
        'marcas' => $marcas,
        'precioMin' => floatval($precios['precioMin']),
        'precioMax' => floatval($precios['precioMax'])
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error en la base de datos']);
}