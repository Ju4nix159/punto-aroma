<?php
include './admin/config/sbd.php';

// Forzar salida JSON
header('Content-Type: application/json');

try {
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id_domicilio'])) {
        echo json_encode(['success' => false, 'message' => 'ID de domicilio no proporcionado.']);
        exit;
    }

    $id_domicilio = $input['id_domicilio'];

    $con->beginTransaction();

    // Eliminar de usuario_domicilio
    $stmtUsuarioDomicilio = $con->prepare("DELETE FROM usuario_domicilios WHERE id_domicilio = :id_domicilio");
    $stmtUsuarioDomicilio->bindParam(':id_domicilio', $id_domicilio);
    $stmtUsuarioDomicilio->execute();

    // Eliminar de domicilios
    $stmtDomicilio = $con->prepare("DELETE FROM domicilios WHERE id_domicilio = :id_domicilio");
    $stmtDomicilio->bindParam(':id_domicilio', $id_domicilio);
    $stmtDomicilio->execute();

    $con->commit();

    echo json_encode(['success' => true, 'message' => 'Domicilio eliminado correctamente.']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el domicilio: ' . $e->getMessage()]);
}
?>
