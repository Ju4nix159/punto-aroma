<?php
// Habilitar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include './admin/config/sbd.php'; // Archivo de conexión

try {
    $id_pedido = $_POST['id_pedido'];
    $id_usuario = $_SESSION['usuario'];
    $metodo_pago = $_POST['payment_method'];
    $nombre_comprobante = null;
    
    $monto = str_replace(',', '', $_POST['monto']); // Elimina separadores de miles
    $monto = floatval($monto); // Convierte a número correctamente
    
    file_put_contents('debug_variables.txt', "Monto recibido después de conversión: $monto\n", FILE_APPEND);
    

    // Manejo del archivo subido
    $comprobanteField = 'comprobante';

    if (isset($_FILES[$comprobanteField]) && $_FILES[$comprobanteField]['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "./assets/comprobantes/" . $id_pedido . "/";
        $nombre_comprobante = basename($_FILES[$comprobanteField]['name']);

        // Crear directorio si no existe
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            throw new Exception("No se pudo crear el directorio: $uploadDir");
        }

        // Asegurar nombre único
        $uploadFile = $uploadDir . $nombre_comprobante;
        $fileInfo = pathinfo($uploadFile);
        $baseName = $fileInfo['filename'];
        $extension = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';
        $counter = 1;

        while (file_exists($uploadFile)) {
            $uploadFile = $uploadDir . $baseName . '_' . $counter . $extension;
            $counter++;
        }

        // Mover archivo
        if (!move_uploaded_file($_FILES[$comprobanteField]['tmp_name'], $uploadFile)) {
            throw new Exception('Error al mover el archivo.');
        }

        // Guardar el nombre del archivo
        $nombre_comprobante = basename($uploadFile);
    }

    // Verificar si se subió el archivo correctamente
    if ($nombre_comprobante === null) {
        throw new Exception("Error: No se asignó un nombre de comprobante.");
    }

    // Iniciar transacción
    $con->beginTransaction();

    // Actualizar estado del pedido
    $sql_actualizar_pedido = $con->prepare("UPDATE pedidos SET id_estado_pedido = 7 WHERE id_pedido = :id_pedido;");
    $sql_actualizar_pedido->bindParam(':id_pedido', $id_pedido);
    if (!$sql_actualizar_pedido->execute()) {
        throw new Exception('Error al actualizar el pedido.');
    }

    // Determinar método de pago
    $metodo = ($metodo_pago === 'mercadopago') ? 4 : 1;
    $pago_total = "pago total";

    // Registrar el pago
    $sql_registrar_pago_trasferencia = $con->prepare("INSERT INTO pagos(id_pedido, comprobante, id_metodo_pago, monto, fecha, descropcion) 
        VALUES(:id_pedido, :comprobante, :metodo, :monto, now()), :descripcion;");
    $sql_registrar_pago_trasferencia->bindParam(':id_pedido', $id_pedido);
    $sql_registrar_pago_trasferencia->bindParam(':comprobante', $nombre_comprobante, PDO::PARAM_STR);
    $sql_registrar_pago_trasferencia->bindParam(':metodo', $metodo, PDO::PARAM_INT);
    $sql_registrar_pago_trasferencia->bindParam(':monto', $monto, PDO::PARAM_STR);
    $sql_registrar_pago_trasferencia->bindParam(':monto', $pago_total, PDO::PARAM_STR);
    

    if (!$sql_registrar_pago_trasferencia->execute()) {
        throw new Exception('Error al registrar el pago.');
    }

    // Confirmar transacción
    $con->commit();

    // Respuesta JSON
    echo json_encode(['success' => true, 'id_pedido' => $id_pedido]);

} catch (Exception $e) {
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
