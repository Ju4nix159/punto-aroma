<?php
include './config/sbd.php'; // Incluye la conexión a la base de datos

// Configurar manejo de errores para excepciones
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los datos enviados por POST
        $id_producto = $_POST['id_producto'];
        $sku = $_POST['sku'];
        $aroma = $_POST['aroma'];
        $color = $_POST['color'];
        $nombre = $_POST['nombre_variante'];

        // Inicia una transacción
        $con->beginTransaction();

        // Insertar la variante en la tabla de variantes
        $sqlInsertVariante = "INSERT INTO variantes (id_producto, aroma, sku, color, nombre_variante) 
                              VALUES (:id_producto, :aroma, :sku, :color, :nombre);";
        $stmtVariante = $con->prepare($sqlInsertVariante);
        $stmtVariante->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmtVariante->bindParam(':aroma', $aroma, PDO::PARAM_STR);
        $stmtVariante->bindParam(':sku', $sku, PDO::PARAM_STR);
        $stmtVariante->bindParam(':color', $color, PDO::PARAM_STR);
        $stmtVariante->bindParam(':nombre', $nombre, PDO::PARAM_STR);

        if ($stmtVariante->execute()) {
            // Confirmar la transacción
            $con->commit();
            echo json_encode(['status' => 'success']);
        } else {
            // Si falla, revertir la transacción
            $con->rollBack();
            throw new Exception('Error al guardar la variante.');
        }
    } else {
        // Respuesta para métodos no permitidos
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    // Registrar el error en el log (opcional)
    error_log($e->getMessage());
    // Enviar respuesta de error en JSON
    echo json_encode(['status' => 'error', 'message' => 'Ocurrió un problema al procesar la solicitud.']);
}
