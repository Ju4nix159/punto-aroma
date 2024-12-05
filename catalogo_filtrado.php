<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'admin/config/sbd.php';

header('Content-Type: application/json');

try {
    // Lee el JSON enviado desde el frontend
    $filters = json_decode(file_get_contents('php://input'), true);

    if (!$filters) {
        throw new Exception('No se recibieron filtros válidos.');
    }

    $query = "SELECT p.id_producto, p.nombre, p.descripcion, c.nombre AS categoria, 
                     vtp.precio AS precio_minorista, i.ruta AS imagen_principal
              FROM productos p
              JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
              JOIN categorias c ON p.id_categoria = c.id_categoria
              LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
              WHERE vtp.id_tipo_precio = 1";

    $params = [];
    if (!empty($filters['search'])) {
        $query .= " AND p.nombre LIKE :search";
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    if (!empty($filters['categories'])) {
        $categories = implode(',', array_map('intval', $filters['categories']));
        $query .= " AND p.id_categoria IN ($categories)";
    }

    if (!empty($filters['minPrice'])) {
        $query .= " AND vtp.precio >= :minPrice";
        $params[':minPrice'] = $filters['minPrice'];
    }

    if (!empty($filters['maxPrice'])) {
        $query .= " AND vtp.precio <= :maxPrice";
        $params[':maxPrice'] = $filters['maxPrice'];
    }

    if (!empty($filters['order'])) {
        $order = $filters['order'] === 'desc' ? 'DESC' : 'ASC';
        $query .= " ORDER BY vtp.precio $order";
    }

    $stmt = $con->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($productos);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
