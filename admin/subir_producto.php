<?php
include './config/sbd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get basic product data
        $nombre = $_POST['nombre'];
        $categoria = $_POST['categoria'];
        $marca = $_POST['marca'];
        $precio_minorista = $_POST['precio_minorista'];
        $destacado = $_POST['destacado'];
        $descripcion = $_POST['descripcion'];
        $precios_mayoristas = json_decode($_POST['precios_mayoristas'], true);

        // Begin transaction
        $con->beginTransaction();

        // Insert product
        $sql_insertar_producto = $con->prepare("
            INSERT INTO productos (nombre, descripcion, id_categoria, id_marca, destacado)
            VALUES (:nombre_producto, :descripcion_producto, :id_categoria, :id_marca, :destacado)
        ");

        $sql_insertar_producto->execute([
            ':nombre_producto' => $nombre,
            ':descripcion_producto' => $descripcion,
            ':id_categoria' => $categoria,
            ':id_marca' => $marca,
            ':destacado' => $destacado
        ]);

        $id_producto = $con->lastInsertId();

        // Insert retail price if exists
        if (!empty($precio_minorista) && $precio_minorista > 0) {
            $sql_insertar_precio = $con->prepare("
                INSERT INTO variantes_tipo_precio (id_producto, id_tipo_precio, precio, cantidad_minima)
                VALUES (:id_producto, 1, :precio, 1)
            ");

            $sql_insertar_precio->execute([
                ':id_producto' => $id_producto,
                ':precio' => $precio_minorista
            ]);
        }

        // Insert wholesale prices
        if (!empty($precios_mayoristas)) {
            $sql_insertar_precio = $con->prepare("
                INSERT INTO variantes_tipo_precio (id_producto, id_tipo_precio, precio, cantidad_minima)
                VALUES (:id_producto, 2, :precio, :cantidad_minima)
            ");

            foreach ($precios_mayoristas as $precio_mayorista) {
                $sql_insertar_precio->execute([
                    ':id_producto' => $id_producto,
                    ':precio' => $precio_mayorista['precio'],
                    ':cantidad_minima' => $precio_mayorista['cantidad_minima']
                ]);
            }
        }

        // Commit transaction
        $con->commit();

        echo json_encode(['success' => true, 'id_producto' => $id_producto]);
    } catch (PDOException $e) {
        // Rollback on error
        $con->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
