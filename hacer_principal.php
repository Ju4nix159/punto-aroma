<?php
include './admin/config/sbd.php';

// Forzar salida JSON
header('Content-Type: application/json');

try {
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id_domicilio_actual'], $input['id_domicilio_nuevo'])) {
        echo json_encode(['success' => false, 'message' => 'IDs de domicilios no proporcionados.']);
        exit;
    }

    $id_domicilio_actual = $input['id_domicilio_actual'];
    $id_domicilio_nuevo = $input['id_domicilio_nuevo'];

    $con->beginTransaction();

    // Cambiar el domicilio actual de principal a no principal
    $stmtDesactivarActual = $con->prepare("UPDATE usuario_domicilios SET principal = 0 WHERE id_domicilio = :id_domicilio_actual");
    $stmtDesactivarActual->bindParam(':id_domicilio_actual', $id_domicilio_actual);
    $stmtDesactivarActual->execute();

    // Cambiar el nuevo domicilio a principal
    $stmtActivarNuevo = $con->prepare("UPDATE usuario_domicilios SET principal = 1 WHERE id_domicilio = :id_domicilio_nuevo");
    $stmtActivarNuevo->bindParam(':id_domicilio_nuevo', $id_domicilio_nuevo);
    $stmtActivarNuevo->execute();

    $con->commit();

    echo json_encode(['success' => true, 'message' => 'Domicilio principal actualizado correctamente.']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el domicilio principal: ' . $e->getMessage()]);
}
?>
