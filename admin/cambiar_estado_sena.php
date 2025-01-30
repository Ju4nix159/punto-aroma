<?php
// cambiar_estado_sena.php

include "./config/sbd.php"; // Asegúrate de que este archivo contenga la conexión a la base de datos

// Verificar si los datos POST están presentes
if (!isset($_POST['pedido_id'], $_POST['estado_sena'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Obtener los datos del POST
$pedido_id = $_POST['pedido_id'];
$estado_sena = $_POST['estado_sena'];

// Validar que el estado sea 0 o 1
if (!in_array($estado_sena, [0, 1])) {
    echo json_encode(['success' => false, 'message' => 'Estado de seña no válido']);
    exit;
}

try {
    // Preparar la consulta SQL para actualizar el estado de la seña
    $sql_cambiar_estado = $con->prepare("UPDATE pedidos SET estado_seña = :estado_sena WHERE id_pedido = :pedido_id");
    $sql_cambiar_estado->bindParam(':estado_sena', $estado_sena, PDO::PARAM_INT);
    $sql_cambiar_estado->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);

    // Ejecutar la consulta
    $success = $sql_cambiar_estado->execute();

    // Verificar si la consulta se ejecutó correctamente
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Estado de la seña actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la seña']);
    }
} catch (PDOException $e) {
    // Capturar errores de la base de datos
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>