<?php


include './config/sbd.php'; // Archivo que contiene la conexión PDO


// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos enviados por POST
        $id_producto = $_POST['id_producto'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $categoria = $_POST['categoria'];
        $marca = $_POST['marca'];
        $destacado = $_POST['destacado'];
        $estado = $_POST['estado'];
        $precio_minorista = $_POST['precio_minorista'];
        $precio_mayorista = $_POST['precio_mayorista'];

        // Preparar consulta SQL
        $sql_actualizar_productos = $con->prepare("UPDATE productos
                                                    SET nombre = :nombre_producto,
                                                        descripcion = :descripcion_producto,
                                                        id_categoria = :id_categoria,
                                                        id_marca = :id_marca,
                                                        destacado = :destacado,
                                                        estado = :estado
                                                    WHERE id_producto = :id_producto;");
        $sql_actualizar_productos->bindparam(':id_producto', $id_producto, PDO::PARAM_INT);
        $sql_actualizar_productos->bindparam(':nombre_producto', $nombre, PDO::PARAM_STR);
        $sql_actualizar_productos->bindparam(':descripcion_producto', $descripcion, PDO::PARAM_STR);
        $sql_actualizar_productos->bindparam(':id_categoria', $categoria, PDO::PARAM_INT);
        $sql_actualizar_productos->bindparam(':id_marca', $marca, PDO::PARAM_INT);
        $sql_actualizar_productos->bindparam(':destacado', $destacado, PDO::PARAM_INT);
        $sql_actualizar_productos->bindparam(':estado', $estado, PDO::PARAM_INT);

        // Ejecutar consulta
        $sql_actualizar_productos->execute();

        // Actualizar precios
        $sql_actualizar_precios = $con->prepare("   UPDATE variantes_tipo_precio
                                                    SET precio = CASE 
                                                                    WHEN id_tipo_precio = 1 THEN :p_minorista  -- New retail price
                                                                    WHEN id_tipo_precio = 2 THEN :p_mayorista  -- New wholesale price
                                                                END
                                                    WHERE id_producto = :id_producto;");

        $sql_actualizar_precios->bindparam(':id_producto', $id_producto);
        $sql_actualizar_precios->bindparam(':p_minorista', $precio_minorista);
        $sql_actualizar_precios->bindparam(':p_mayorista', $precio_mayorista);

        $sql_actualizar_precios->execute();


        // Confirmar éxito
        if ($sql_actualizar_productos->rowCount() > 0 || $sql_actualizar_precios->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se realizaron cambios o ID no encontrado.']);
        }
    } catch (PDOException $e) {
        // Manejar errores
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    // Respuesta para métodos no permitidos
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
