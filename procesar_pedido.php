<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include './admin/config/sbd.php';

// Depurar datos
file_put_contents('debug_formdata.txt', "POST Data:\n" . print_r($_POST, true), FILE_APPEND);
file_put_contents('debug_formdata.txt', "\nFILES Data:\n" . print_r($_FILES, true), FILE_APPEND);

try {
    $id_usuario = $_SESSION['usuario'] ?? null;
    $carrito = $_SESSION['cart'] ?? null;
    $delivery_method = "pickup";

    if (!$id_usuario || !$carrito || !$delivery_method) {
        if (!$id_usuario) {
            throw new Exception("ID de usuario no encontrado en la sesión.");
        }
        if (!$carrito) {
            throw new Exception("Carrito vacío o no encontrado en la sesión.");
        }
        if (!$delivery_method) {
            throw new Exception("Método de entrega no especificado.");
        }
    }

    // Inicia la transacción
    $con->beginTransaction();

    // Procesar total del carrito
    $total = calcularTotalCarrito($carrito);

    // Manejar domicilio
    $id_domicilio = null;
    if ($delivery_method === 'delivery') {
        $id_domicilio = procesarDomicilio($con, $_POST, $id_usuario);
    }

    // Crear pedido
    $id_pedido = crearPedido($con, $id_usuario, $total, $id_domicilio, $_POST, $delivery_method);

    // Insertar productos del pedido
    insertarProductosPedido($con, $id_pedido, $carrito);

    // Procesar pago
    procesarPago($con, $id_pedido, $_POST, $total);

    // Finalizar la transacción
    $con->commit();

    // Vaciar el carrito
    unset($_SESSION['cart']);

    echo json_encode(['success' => true, 'id_pedido' => $id_pedido]);
} catch (Exception $e) {
    if (isset($con) && $con->inTransaction()) {
        $con->rollBack();
    }
    // Registrar error en archivo de log
    file_put_contents('error_log.txt', "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n", FILE_APPEND);

    // Responder al cliente con el mensaje de error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Funciones auxiliares

function calcularTotalCarrito($carrito)
{
    try {
        $total = 0;
        foreach ($carrito as $item) {
            if (!isset($item['precio']) || !isset($item['fragancias'])) {
                throw new Exception("Datos del producto en el carrito inválidos.");
            }
            foreach ($item['fragancias'] as $fragancia) {
                if (!isset($fragancia['cantidad']) || $fragancia['cantidad'] <= 0) {
                    throw new Exception("Cantidad inválida para una fragancia.");
                }
                $total += $item['precio'] * $fragancia['cantidad'];
            }
        }
        return $total;
    } catch (Exception $e) {
        throw new Exception("Error al calcular el total del carrito: " . $e->getMessage());
    }
}

function procesarDomicilio($con, $data, $id_usuario)
{
    try {
        if (!isset($data['provincia'], $data['localidad'], $data['calle'], $data['numero'], $data['codigo_postal'])) {
            throw new Exception("Faltan datos obligatorios del domicilio.");
        }

        $stmt = $con->prepare("SELECT id_domicilio FROM domicilios WHERE provincia = ? AND localidad = ? AND calle = ? AND numero = ? AND codigo_postal = ? AND piso = ? AND departamento = ?");
        $stmt->execute([
            $data['provincia'],
            $data['localidad'],
            $data['calle'],
            $data['numero'],
            $data['codigo_postal'],
            $data['piso'] ?? '',
            $data['departamento'] ?? ''
        ]);
        $domicilio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$domicilio) {
            $stmt = $con->prepare("INSERT INTO domicilios (provincia, localidad, calle, numero, codigo_postal, piso, departamento, informacion_adicional) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['provincia'],
                $data['localidad'],
                $data['calle'],
                $data['numero'],
                $data['codigo_postal'],
                $data['piso'] ?? '',
                $data['departamento'] ?? '',
                $data['informacion_adicional'] ?? ''
            ]);
            $id_domicilio = $con->lastInsertId();

            $stmt = $con->prepare("INSERT INTO usuario_domicilios (id_usuario, id_domicilio, tipo_domicilio) VALUES (?, ?, 'envio')");
            $stmt->execute([$id_usuario, $id_domicilio]);
        } else {
            $id_domicilio = $domicilio['id_domicilio'];
        }

        return $id_domicilio;
    } catch (Exception $e) {
        throw new Exception("Error al procesar el domicilio: " . $e->getMessage());
    }
}

function crearPedido($con, $id_usuario, $total, $id_domicilio, $data, $delivery_method)
{
    try {
        $stmt = $con->prepare("INSERT INTO pedidos (id_usuario, total, fecha, id_domicilio, id_local) VALUES (?, ?, NOW(), ?, ?)");
        $stmt->execute([
            $id_usuario,
            $total,
            $id_domicilio,
            $delivery_method === 'pickup' ? $data['id_local'] ?? null : null
        ]);
        return $con->lastInsertId();
    } catch (Exception $e) {
        throw new Exception("Error al crear el pedido: " . $e->getMessage());
    }
}

function insertarProductosPedido($con, $id_pedido, $carrito)
{
    try {
        foreach ($carrito as $item) {
            foreach ($item['fragancias'] as $fragancia) {
                $stmt = $con->prepare("INSERT INTO productos_pedido (id_pedido, id_producto, sku, cantidad, precio) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $id_pedido,
                    $item['id'],
                    $fragancia['sku'],
                    $fragancia['cantidad'],
                    $item['precio']
                ]);
            }
        }
    } catch (Exception $e) {
        throw new Exception("Error al insertar productos del pedido: " . $e->getMessage());
    }
}

function procesarPago($con, $id_pedido, $data, $total)
{
    try {
        $payment_methods = [
            'transferencia' => 1,
            'mercadopago' => 4,
            'pagoenlocal' => 5
        ];

        if (!isset($payment_methods[$data['payment_method']])) {
            throw new Exception("Método de pago no válido.");
        }

        $comprobante_path = null;
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "./assets/comprobantes/";
            $file_extension = pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;

            if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $upload_dir . $file_name)) {
                throw new Exception("Error al subir el comprobante.");
            }
            $comprobante_path = $upload_dir . $file_name;
        }

        $stmt = $con->prepare("INSERT INTO pagos (id_pedido, id_metodo_pago, comprobante, monto, fecha) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $id_pedido,
            $payment_methods[$data['payment_method']],
            $comprobante_path,
            $total
        ]);
    } catch (Exception $e) {
        throw new Exception("Error al procesar el pago: " . $e->getMessage());
    }
}
