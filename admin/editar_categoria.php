<?php

include './config/sbd.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_categoria = intval($_POST['id_categoria']);
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];

        $con->beginTransaction();

        // Verificar si el nombre ya existe con estado 0

        // Si no existe, insertar nueva categoría
        $sql_editar_categoria = $con->prepare(
            "UPDATE categorias SET nombre = :nombre, descripcion = :descripcion WHERE id_categoria = :id_categoria"
        );
        $sql_editar_categoria->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $sql_editar_categoria->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

        if (!$sql_editar_categoria->execute()) {
            throw new Exception('Error al actualizar la categoría.');
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
