<?php
include 'admin/config/sbd.php';

header('Content-Type: application/json');

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$marca = isset($_GET['marca']) ? trim($_GET['marca']) : '';
$nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$precioMin = isset($_GET['precioMin']) ? (int)$_GET['precioMin'] : 0;
$precioMax = isset($_GET['precioMax']) ? (int)$_GET['precioMax'] : 10000;
$productosPorPagina = isset($_GET['productosPorPagina']) ? (int)$_GET['productosPorPagina'] : 12;
$ordenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : '';

$offset = ($pagina - 1) * $productosPorPagina;

// Construir consulta dinámica
$query = "SELECT p.id_producto, p.nombre, p.descripcion, c.nombre AS categoria, 
                 vtp.precio AS precio_minorista, i.ruta AS imagen_principal, m.nombre AS marca
          FROM productos p
          JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
          JOIN categorias c ON p.id_categoria = c.id_categoria
          JOIN marcas m ON p.id_marca = m.id_marca
          LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
          WHERE vtp.id_tipo_precio = 1";

// Aplicar filtros
$params = [];
if ($categoria !== '') {
    $query .= " AND c.nombre = :categoria";
    $params[':categoria'] = $categoria;
}

if ($marca !== '') {
    $query .= " AND m.nombre = :marca";
    $params[':marca'] = $marca;
}

if ($nombre !== '') {
    $query .= " AND p.nombre LIKE :nombre";
    $params[':nombre'] = '%' . $nombre . '%';
}

if ($precioMin >= 0 && $precioMax > $precioMin) {
    $query .= " AND vtp.precio BETWEEN :precioMin AND :precioMax";
    $params[':precioMin'] = $precioMin;
    $params[':precioMax'] = $precioMax;
}

// Ordenar
if ($ordenar === 'asc') {
    $query .= " ORDER BY vtp.precio ASC";
} elseif ($ordenar === 'desc') {
    $query .= " ORDER BY vtp.precio DESC";
}

// Paginación
$query .= " LIMIT :limit OFFSET :offset";
$params[':limit'] = $productosPorPagina;
$params[':offset'] = $offset;

$sql = $con->prepare($query);
foreach ($params as $key => $value) {
    if (is_int($value)) {
        $sql->bindValue($key, $value, PDO::PARAM_INT);
    } else {
        $sql->bindValue($key, $value, PDO::PARAM_STR);
    }
}
$sql->execute();
$productos = $sql->fetchAll(PDO::FETCH_ASSOC);

// Total de productos (sin límite ni offset)
$totalQuery = "SELECT COUNT(*) AS total
               FROM productos p
               JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
               JOIN categorias c ON p.id_categoria = c.id_categoria
               JOIN marcas m ON p.id_marca = m.id_marca
               WHERE vtp.id_tipo_precio = 1";

$totalParams = [];
if ($categoria !== '') {
    $totalQuery .= " AND c.nombre = :categoria";
    $totalParams[':categoria'] = $categoria;
}

if ($marca !== '') {
    $totalQuery .= " AND m.nombre = :marca";
    $totalParams[':marca'] = $marca;
}

if ($nombre !== '') {
    $totalQuery .= " AND p.nombre LIKE :nombre";
    $totalParams[':nombre'] = '%' . $nombre . '%';
}

if ($precioMin >= 0 && $precioMax > $precioMin) {
    $totalQuery .= " AND vtp.precio BETWEEN :precioMin AND :precioMax";
    $totalParams[':precioMin'] = $precioMin;
    $totalParams[':precioMax'] = $precioMax;
}

$totalSql = $con->prepare($totalQuery);
foreach ($totalParams as $key => $value) {
    if (is_int($value)) {
        $totalSql->bindValue($key, $value, PDO::PARAM_INT);
    } else {
        $totalSql->bindValue($key, $value, PDO::PARAM_STR);
    }
}
$totalSql->execute();
$total = $totalSql->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener categorías y marcas
$categoriasQuery = $con->prepare("SELECT nombre FROM categorias WHERE estado = 1");
$categoriasQuery->execute();
$categorias = $categoriasQuery->fetchAll(PDO::FETCH_ASSOC);

$marcasQuery = $con->prepare("SELECT nombre FROM marcas WHERE estado = 1");
$marcasQuery->execute();
$marcas = $marcasQuery->fetchAll(PDO::FETCH_ASSOC);

// Obtener rango de precios
$precioQuery = $con->prepare("SELECT MIN(vtp.precio) AS precioMin, MAX(vtp.precio) AS precioMax
                              FROM variantes_tipo_precio vtp
                              JOIN productos p ON vtp.id_producto = p.id_producto
                              WHERE vtp.id_tipo_precio = 1");
$precioQuery->execute();
$precios = $precioQuery->fetch(PDO::FETCH_ASSOC);

// Respuesta en JSON
echo json_encode([
    'productos' => $productos,
    'total' => (int) $total,
    'categorias' => $categorias,
    'marcas' => $marcas,
    'precioMin' => (int) $precios['precioMin'],
    'precioMax' => (int) $precios['precioMax'],
]);
