<?php
session_start();
include("../admin/config/sbd.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizarInfo'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $fechaNacimiento = $_POST['fecha_nacimiento'];
    $telefono = $_POST['telefono'];
    var_dump($id_usuario, $nombre, $apellido, $dni, $fechaNacimiento, $telefono);
    

    $query = "SELECT COUNT(*) as count FROM info_usuarios WHERE id_usuario = :id_usuario";
    $stmt = $con->prepare($query);
    $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row['count'] > 0) {
        $updateQuery = "UPDATE info_usuarios SET nombre = :nombre, apellido = :apellido, dni = :dni, fecha_nacimiento = :fecha_nacimiento, telefono = :telefono  WHERE id_usuario = :id_usuario";
        $updateStmt = $con->prepare($updateQuery);
        $updateStmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
        $updateStmt->bindParam(":apellido", $apellido, PDO::PARAM_STR);
        $updateStmt->bindParam(":dni", $dni, PDO::PARAM_STR);
        $updateStmt->bindParam(":fecha_nacimiento", $fechaNacimiento, PDO::PARAM_STR);
        $updateStmt->bindParam(":telefono", $telefono, PDO::PARAM_STR);
        $updateStmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $updateStmt->execute();
    } else {
        $insertQuery = "INSERT INTO info_usuarios (id_usuario, nombre, apellido, dni, fecha_nacimiento, telefono) VALUES (:id_usuario, :nombre, :apellido, :dni, :fecha_nacimiento, :telefono)";
        $insertStmt = $con->prepare($insertQuery);
        $insertStmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $insertStmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
        $insertStmt->bindParam(":apellido", $apellido, PDO::PARAM_STR);
        $insertStmt->bindParam(":dni", $dni, PDO::PARAM_STR);
        $insertStmt->bindParam(":fecha_nacimiento", $fechaNacimiento, PDO::PARAM_STR);
        $insertStmt->bindParam(":telefono", $telefono, PDO::PARAM_STR);
        $insertStmt->execute();
    }

    header("Location: ../panelUsuario.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelarPedido'])) {
    $id_pedido = $_POST['id_pedido'];

    // Asumiendo que $con es tu conexión a la base de datos PDO
    $updateQuery = "UPDATE pedidos SET id_estado_pedido = 6 WHERE id_pedido = :id_pedido";
    $updateStmt = $con->prepare($updateQuery);
    $updateStmt->bindParam(":id_pedido", $id_pedido, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        // Enviar respuesta en formato JSON
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida.']);
}
