<?php
// Conexión a la base de datos usando PDO
include './admin/config/sbd.php'; // Asegúrate de tener tu archivo de conexión que defina $con


try {
    $id_pedido = $_POST['id_pedido'];
    $id_usuario = $_POST['id_usuario'];
    $metodo_pago = $_POST['metodo_pago'];
    $nombre_comprobante = $_POST['nombre_comprobante'] ?? null;


    // Iniciar una transacción
    $con->beginTransaction();

    // Insertar el pago en la tabla de pagos
    $sql_actualizar_pedido = $con->prepare("UPDATE pedidos
    SET id_estado_pedido = 7, id_metodo_pago = :metodo_pago
    WHERE id_pedido = :id_pedido;");
    $sql_actualizar_pedido->bindParam(':id_pedido', $id_pedido);
    $sql_actualizar_pedido->bindParam(':metodo_pago', $metodo_pago);


    if (!$sql_actualizar_pedido->execute()) {
        throw new Exception('Error al actualizar el pago.');
    }

    // Actualizar el estado del pedido
    $sql_registrar_pago_trasferencia = $con->prepare("INSERT INTO pagos(id_pedido, comprobante) VALUES(:id_pedido, :comprobante);");
    $sql_registrar_pago_trasferencia->bindParam(':id_pedido', $id_pedido);
    $sql_registrar_pago_trasferencia->bindParam(':comprobante', $nombre_comprobante);



    if (!$sql_registrar_pago_trasferencia->execute()) {
        throw new Exception('Error al registrar el pago.');
    }

    // Confirmar la transacción
    $con->commit();

    $response['success'] = true;
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    $response['error'] = $e->getMessage();
}

// Retornar respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
