<?php

include './config/sbd.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];

        $con->beginTransaction();

        // Verificar si el nombre ya existe con estado 0
        $sql_verificar_categoria = $con->prepare(
            "SELECT * FROM categorias WHERE nombre = :nombre AND estado = 0"
        );
        $sql_verificar_categoria->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $sql_verificar_categoria->execute();
        $resultado = $sql_verificar_categoria->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            // Si existe, actualizar descripción y estado
            $sql_actualizar_categoria = $con->prepare(
                "UPDATE categorias SET descripcion = :descripcion, estado = 1 WHERE nombre = :nombre AND estado = 0"
            );
            $sql_actualizar_categoria->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $sql_actualizar_categoria->bindParam(':nombre', $nombre, PDO::PARAM_STR);

            if (!$sql_actualizar_categoria->execute()) {
                throw new Exception('Error al actualizar la categoría.');
            }
        } else {
            // Si no existe, insertar nueva categoría
            $sql_insertar_categoria = $con->prepare(
                "INSERT INTO categorias (nombre, descripcion, estado) VALUES (:nombre, :descripcion, 1)"
            );
            $sql_insertar_categoria->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $sql_insertar_categoria->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

            if (!$sql_insertar_categoria->execute()) {
                throw new Exception('Error al insertar la categoría.');
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
