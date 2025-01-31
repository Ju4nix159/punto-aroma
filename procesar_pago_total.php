<?php
// Conexión a la base de datos usando PDO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include './admin/config/sbd.php'; // Asegúrate de tener tu archivo de conexión que defina $con

// Depurar datos
file_put_contents('debug_formdata.txt', "POST Data:\n" . print_r($_POST, true), FILE_APPEND);
file_put_contents('debug_formdata.txt', "\nFILES Data:\n" . print_r($_FILES, true), FILE_APPEND);

try {
    $id_pedido = $_POST['id_pedido'];
    $id_usuario = $_SESSION['usuario'];
    $metodo_pago = $_POST['payment_method'];
    $monto = $_POST['monto'];
    $nombre_comprobante = null;

    // Imprimir las variables para depuración
    file_put_contents('debug_variables.txt', "ID Pedido: $id_pedido\n", FILE_APPEND);
    file_put_contents('debug_variables.txt', "ID Usuario: $id_usuario\n", FILE_APPEND);
    file_put_contents('debug_variables.txt', "Método de Pago: $metodo_pago\n", FILE_APPEND);
    file_put_contents('debug_variables.txt', "Nombre Comprobante: $nombre_comprobante\n", FILE_APPEND);

    // Manejo del archivo subido
    $comprobanteField = 'comprobante' . ($metodo_pago === 'transferencia' ? 'Transferencia' : 'MercadoPago');
    if (isset($_FILES[$comprobanteField]) && $_FILES[$comprobanteField]['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "./assets/comprobantes/" . $id_pedido . "/"; // Ruta relativa
        $nombre_comprobante = basename($_FILES[$comprobanteField]['name']);

        // Crear directorio si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Crear directorio con permisos
        }

        // Asegurarse de que el archivo tenga un nombre único
        $uploadFile = $uploadDir . $nombre_comprobante;
        $fileInfo = pathinfo($uploadFile);
        $baseName = $fileInfo['filename'];
        $extension = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';
        $counter = 1;

        while (file_exists($uploadFile)) {
            $uploadFile = $uploadDir . $baseName . '_' . $counter . $extension;
            $counter++;
        }

        // Mover archivo a su ubicación final
        if (!move_uploaded_file($_FILES[$comprobanteField]['tmp_name'], $uploadFile)) {
            throw new Exception('Error al mover el archivo subido.');
        }

        // Actualizar el nombre del comprobante con el nombre único
        $nombre_comprobante = basename($uploadFile);
    }

    // Iniciar una transacción
    $con->beginTransaction();

    // Actualizar el estado del pedido
    $sql_actualizar_pedido = $con->prepare("UPDATE pedidos SET id_estado_pedido = 7 WHERE id_pedido = :id_pedido;");
    $sql_actualizar_pedido->bindParam(':id_pedido', $id_pedido);

    if (!$sql_actualizar_pedido->execute()) {
        throw new Exception('Error al actualizar el pedido.');
    }

    $metodo = NULL;
    // Registrar el pago en la base de datos
    if ($metodo_pago === 'mercadopago'){
        $metodo = 4;
    }
    else{
        $metodo = 1;
    }
    $sql_registrar_pago_trasferencia = $con->prepare("INSERT INTO pagos(id_pedido, comprobante, id_metodo_pago, monto, fecha) VALUES(:id_pedido, :comprobante, :metodo, :monto, now());");
    $sql_registrar_pago_trasferencia->bindParam(':id_pedido', $id_pedido);
    $sql_registrar_pago_trasferencia->bindParam(':comprobante', $nombre_comprobante);
    $sql_registrar_pago_trasferencia->bindParam(':metodo', $metodo);
    $sql_registrar_pago_trasferencia->bindParam(':monto', $monto); 


    if (!$sql_registrar_pago_trasferencia->execute()) {
        throw new Exception('Error al registrar el pago.');
    }

    // Confirmar la transacción
    $con->commit();

    $response['success'] = true;
    $response['id_pedido'] = $id_pedido;
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    $response['success'] = false;
    $response['error'] = $e->getMessage();
}

// Retornar respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);