<?php

include './config/sbd.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];

        $con->beginTransaction();

        // Verificar si el nombre ya existe con estado 0
        $sql_verificar_marca = $con->prepare(
            "SELECT * FROM marcas WHERE nombre = :nombre AND estado = 0"
        );
        $sql_verificar_marca->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $sql_verificar_marca->execute();
        $resultado = $sql_verificar_marca->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            // Si existe, actualizar descripción y estado
            $sql_actualizar_marca = $con->prepare(
                "UPDATE marcas SET estado = 1 WHERE nombre = :nombre AND estado = 0"
            );
            $sql_actualizar_marca->bindParam(':nombre', $nombre, PDO::PARAM_STR);

            if (!$sql_actualizar_marca->execute()) {
                throw new Exception('Error al actualizar la marca.');
            }
        } else {
            // Si no existe, insertar nueva categoría
            $sql_insertar_marca = $con->prepare(
                "INSERT INTO marcas (nombre) VALUES (:nombre)"
            );
            $sql_insertar_marca->bindParam(':nombre', $nombre, PDO::PARAM_STR);

            if (!$sql_insertar_marca->execute()) {
                throw new Exception('Error al insertar la marca.');
            }
        }

        // Confirmar la transacción
        $con->commit();
        echo json_encode(['status' => 'success']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
        exit;
    }
} catch (Exception $e) {
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
