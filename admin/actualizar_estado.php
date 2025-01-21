<?php
require './config/sbd.php'; // Incluye tu archivo de conexión

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = $_POST['id_pedido'] ?? null;
    $nuevo_estado = $_POST['nuevo_estado'] ?? null;

    if ($id_pedido && $nuevo_estado) {
        try {
            // Actualizar el estado del pedido en la base de datos
            $sql_actualizar = $con->prepare("UPDATE pedidos SET id_estado_pedido = :nuevo_estado WHERE id_pedido = :id_pedido");
            $sql_actualizar->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
            $sql_actualizar->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $sql_actualizar->execute();

            // Obtener el nombre del nuevo estado
            $sql_estado = $con->prepare("SELECT nombre FROM estados_pedidos WHERE id_estado_pedido = :id_estado");
            $sql_estado->bindParam(':id_estado', $nuevo_estado, PDO::PARAM_INT);
            $sql_estado->execute();
            $nuevo_estado_nombre = $sql_estado->fetchColumn();

            $response['success'] = true;
            $response['nuevo_estado'] = $nuevo_estado_nombre;
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Datos incompletos.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
