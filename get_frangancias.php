<?php
header('Content-Type: application/json');
include './admin/config/sbd.php'; // Asegúrate de que esta conexión use PDO

$idProducto = $_GET['id_producto'] ?? null;

// Validar que se proporcione un ID de producto
if (!$idProducto) {
    echo json_encode(["error" => "ID del producto no proporcionado."]);
    exit;
}

try {
    // Consulta de producto
    $sqlProducto = "SELECT p.nombre, p.descripcion, i.nombre AS imagen_principal
                    FROM productos p
                    JOIN imagenes i ON p.id_producto = i.id_producto
                    WHERE p.id_producto = :id_producto AND i.principal = 1";
    $stmtProducto = $con->prepare($sqlProducto);

    // Asignar parámetro
    $stmtProducto->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
    $stmtProducto->execute();
    $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

    // Validar si el producto existe
    if (!$producto) {
        echo json_encode(["error" => "Producto no encontrado."]);
        exit;
    }

    // Consulta de fragancias
    $sqlFragancias = "SELECT
      COALESCE(titulo, aroma, color) AS atributo
  FROM variantes
  WHERE id_producto = :id_producto;";
    $stmtFragancias = $con->prepare($sqlFragancias);
    $stmtFragancias->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
    $stmtFragancias->execute();

    $fragancias = $stmtFragancias->fetchAll(PDO::FETCH_COLUMN);

    // Añadir las fragancias al resultado del producto
    $producto['fragancias'] = $fragancias;

    // Devolver la respuesta en formato JSON
    echo json_encode($producto);
} catch (PDOException $e) {
    // Manejo de errores en la base de datos
    echo json_encode(["error" => "Error de base de datos: " . $e->getMessage()]);
    exit;
}
