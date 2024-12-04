<?php

include './config/sbd.php'; // Archivo que contiene la conexión PDO

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos enviados por POST
        $nombre = $_POST['nombre'];
        $categoria = $_POST['categoria'];
        $precio_minorista = $_POST['precio_minorista'];
        $precio_mayorista = $_POST['precio_mayorista'];
        $cantidad_minima = $_POST['cantidad_minima'];
        $destacado = $_POST['destacado'];
        $descripcion = $_POST['descripcion'];

        // Preparar consulta SQL para insertar en la tabla productos
        $sql_insertar_producto = $con->prepare("INSERT INTO productos (nombre, descripcion, id_categoria, destacado)
                                                VALUES (:nombre_producto, :descripcion_producto, :id_categoria, :destacado);");
        $sql_insertar_producto->bindparam(':nombre_producto', $nombre, PDO::PARAM_STR);
        $sql_insertar_producto->bindparam(':descripcion_producto', $descripcion, PDO::PARAM_STR);
        $sql_insertar_producto->bindparam(':id_categoria', $categoria, PDO::PARAM_INT);
        $sql_insertar_producto->bindparam(':destacado', $destacado, PDO::PARAM_INT);

        // Ejecutar consulta
        $sql_insertar_producto->execute();

        // Confirmar éxito y obtener el último ID insertado
        if ($sql_insertar_producto->rowCount() > 0) {
            $id_producto = $con->lastInsertId(); // Obtener el ID del producto recién insertado

            // Preparar consulta SQL para insertar precios en variantes_tipo_precio
            $sql_insertar_precios = $con->prepare("INSERT INTO variantes_tipo_precio (id_producto, id_tipo_precio, precio, cantidad_minima) 
                                                    VALUES (:id_producto, :id_tipo_precio, :precio, :cantidad_minima);");

            // Inserción para precio minorista (id_tipo_precio = 1)
            if (!empty($precio_minorista) && $precio_minorista > 0) {
                $sql_insertar_precios->execute([
                    ':id_producto' => $id_producto,
                    ':id_tipo_precio' => 1,
                    ':precio' => $precio_minorista,
                    ':cantidad_minima' => null // No aplica para precio minorista
                ]);
            }

            // Inserción para precio mayorista (id_tipo_precio = 2)
            if (!empty($precio_mayorista) && $precio_mayorista > 0) {
                $sql_insertar_precios->execute([
                    ':id_producto' => $id_producto,
                    ':id_tipo_precio' => 2,
                    ':precio' => $precio_mayorista,
                    ':cantidad_minima' => $cantidad_minima // Aplica solo para mayorista
                ]);
            }

            // Confirmar éxito
            echo json_encode(['success' => true, 'id_producto' => $id_producto]);
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
