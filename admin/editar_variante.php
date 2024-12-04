<?php
include './config/sbd.php'; // Incluye la conexión a la base de datos

header('Content-Type: application/json'); // Establece el tipo de respuesta como JSON

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica si el parámetro SKU está presente
        if (isset($_POST['sku']) && !empty($_POST['sku'])) {
            $sku = $_POST['sku'];
            $aroma = $_POST['aroma'];
            $color = $_POST['color'];
            $nombre = $_POST['nombre_variante'];

            // Inicia una transacción
            $con->beginTransaction();

            // Prepara la consulta para actualizar el estado de la variante por SKU
            $editar_variante = $con->prepare("UPDATE variantes SET aroma = :aroma , color = :color , nombre_variante = :nombre_variante WHERE sku = :sku");
            $editar_variante->bindParam(':sku', $sku, PDO::PARAM_STR);
            $editar_variante->bindParam(':aroma', $aroma, PDO::PARAM_STR);
            $editar_variante->bindParam(':color', $color, PDO::PARAM_STR);
            $editar_variante->bindParam(':nombre_variante', $nombre, PDO::PARAM_STR);


            if ($editar_variante->execute()) {
                // Verificar si se actualizó alguna fila
                if ($editar_variante->rowCount() > 0) {
                    $con->commit(); // Confirma la transacción
                    echo json_encode(['status' => 'success']);
                } else {
                    $con->rollBack(); // Revertir en caso de que no se actualice ninguna fila
                    echo json_encode(['status' => 'error', 'message' => 'No se encontró una variante con ese SKU.']);
                }
            } else {
                $con->rollBack(); // Revertir en caso de error
                throw new Exception('Error al actualizar la variante.');
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'El SKU es requerido.']);
        }
    } else {
        // Respuesta para métodos no permitidos
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    }
} catch (Exception $e) {
    if ($con->inTransaction()) {
        $con->rollBack(); // Revertir la transacción si está activa
    }
    // Registrar el error en el log del servidor
    error_log($e->getMessage());
    // Enviar respuesta de error en JSON
    echo json_encode(['status' => 'error', 'message' => 'Ocurrió un problema al procesar la solicitud.']);
}
?>
