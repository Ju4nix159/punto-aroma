<?php
include './config/sbd.php'; // Asegúrate de que aquí está definida la conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'], $_POST['id_producto'])) {
    // Recibir datos
    $id_producto = intval($_POST['id_producto']);
    $uploadDir = "../assets/productos/imagen/";  // Directorio para guardar las imágenes
    $ruta = "/imagen/$id_producto.png";  // Ruta para guardar en la base de datos

    // Crear el directorio si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Definir el nuevo nombre del archivo basado en el ID del producto
    $uploadFile = $uploadDir . "$id_producto.png";  // Ruta completa para almacenar en el servidor

    // Conexión a la base de datos
    try {
        // Iniciar transacción
        $con->beginTransaction();

        // Eliminar la imagen actual del producto (si existe)
        $sql_eliminar_imagen = $con->prepare("DELETE FROM imagenes WHERE id_producto = :id_producto AND principal = 1");
        $sql_eliminar_imagen->bindparam(":id_producto", $id_producto, PDO::PARAM_INT);
        $sql_eliminar_imagen->execute();

        // Mover la nueva imagen al servidor y renombrarla
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            // Insertar nueva imagen como principal en la base de datos
            $sql_imagen_nueva = $con->prepare("
                INSERT INTO imagenes (id_producto, ruta, principal)
                VALUES (:id_producto, :ruta, 1)
            ");
            $sql_imagen_nueva->bindparam(":id_producto", $id_producto, PDO::PARAM_INT);
            $sql_imagen_nueva->bindparam(":ruta", $ruta, PDO::PARAM_STR);
            $sql_imagen_nueva->execute();

            // Confirmar transacción
            $con->commit();

            echo json_encode(['success' => true]);
        } else {
            // Revertir transacción en caso de error al mover el archivo
            $con->rollBack();
            echo json_encode(['success' => false, 'error' => 'Error al mover la imagen.']);
        }
    } catch (Exception $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
}
