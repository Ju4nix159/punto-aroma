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

    // Verificar si el domicilio a eliminar es principal
    $stmtVerificarPrincipal = $con->prepare(
        "SELECT principal, id_usuario FROM usuario_domicilios WHERE id_domicilio = :id_domicilio"
    );
    $stmtVerificarPrincipal->bindParam(':id_domicilio', $id_domicilio);
    $stmtVerificarPrincipal->execute();
    $domicilio = $stmtVerificarPrincipal->fetch(PDO::FETCH_ASSOC);

    if (!$domicilio) {
        echo json_encode(['success' => false, 'message' => 'Domicilio no encontrado.']);
        $con->rollBack();
        exit;
    }

    $es_principal = $domicilio['principal'];
    $id_usuario = $domicilio['id_usuario'];

    if ($es_principal) {
        // Verificar si el usuario tiene otros domicilios para asignar como principal
        $stmtBuscarOtro = $con->prepare(
            "SELECT id_domicilio FROM usuario_domicilios WHERE id_usuario = :id_usuario AND id_domicilio != :id_domicilio LIMIT 1"
        );
        $stmtBuscarOtro->bindParam(':id_usuario', $id_usuario);
        $stmtBuscarOtro->bindParam(':id_domicilio', $id_domicilio);
        $stmtBuscarOtro->execute();
        $otroDomicilio = $stmtBuscarOtro->fetch(PDO::FETCH_ASSOC);

        if (!$otroDomicilio) {
            // Si no hay otros domicilios, no se puede eliminar el principal
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar el domicilio principal si no hay otros domicilios asignados.']);
            $con->rollBack();
            exit;
        }

        // Si hay otro domicilio, actualizarlo como principal
        $nuevoPrincipal = $otroDomicilio['id_domicilio'];
        $stmtActualizarPrincipal = $con->prepare(
            "UPDATE usuario_domicilios SET principal = 1 WHERE id_domicilio = :nuevo_principal"
        );
        $stmtActualizarPrincipal->bindParam(':nuevo_principal', $nuevoPrincipal);
        $stmtActualizarPrincipal->execute();
    }

    // Eliminar de usuario_domicilios
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
