<?php
include './admin/config/sbd.php'; // Asegúrate de que aquí está definida la conexión PDO
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos usando PDO
try {
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos: " . $e->getMessage()]);
    exit;
}

// Obtener datos enviados por la solicitud AJAX
$data = json_decode(file_get_contents("php://input"), true);
$estado = $data["estado"] ?? "todos";

// Construir y ejecutar la consulta
try {
    if ($estado === "todos") {
        $sql = "SELECT * FROM pedidos";
        $stmt = $con->prepare($sql);
        $stmt->execute();
    } else {
        $sql = "SELECT p.id_pedido, p.total, p.fecha, ep.nombre AS estado_pedido
FROM pedidos p
JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido
WHERE p.id_estado_pedido = :estado;";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Obtener resultados
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "pedidos" => $pedidos]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error al ejecutar la consulta: " . $e->getMessage()]);
    exit;
}