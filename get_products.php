<?php
include '../admin/config/sbd.php';

// Recibe los parámetros de consulta
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category = isset($_GET['category']) ? (int)$_GET['category'] : null;
$brand = isset($_GET['brand']) ? (int)$_GET['brand'] : null;

// Define la cantidad de productos por página
$productsPerPage = 12;
$offset = ($page - 1) * $productsPerPage;

// Construye la consulta
$query = "
    SELECT p.id_producto, p.nombre, p.descripcion, c.nombre AS categoria, vtp.precio AS precio_minorista, i.ruta AS imagen_principal
    FROM productos p
    JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
    JOIN categorias c ON p.id_categoria = c.id_categoria
    LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
    WHERE vtp.id_tipo_precio = 1
";

$conditions = [];
$params = [];

// Agrega filtros dinámicos
if ($category) {
    $conditions[] = "p.id_categoria = :category";
    $params[':category'] = $category;
}

if ($brand) {
    $conditions[] = "p.id_marca = :brand";
    $params[':brand'] = $brand;
}

if (count($conditions) > 0) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " LIMIT :offset, :limit";

$stmt = $con->prepare($query);

// Vincula parámetros
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_INT);
}

$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);

$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devuelve los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($productos);
