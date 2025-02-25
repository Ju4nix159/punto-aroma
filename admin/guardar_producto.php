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
        $precio_minorista = isset($_POST['precio_minorista']) ? $_POST['precio_minorista'] : null;
        $precio_mayorista = isset($_POST['precio_mayorista']) ? $_POST['precio_mayorista'] : null;
        $precio_mayorista_6 = isset($_POST['precio_mayorista_6']) ? $_POST['precio_mayorista_6'] : null;
        $precio_mayorista_48 = isset($_POST['precio_mayorista_48']) ? $_POST['precio_mayorista_48'] : null;
        $precio_mayorista_120 = isset($_POST['precio_mayorista_120']) ? $_POST['precio_mayorista_120'] : null;

        // Verificar marca para determinar qué precios actualizar
        $sql_marca = $con->prepare("SELECT nombre FROM marcas WHERE id_marca = :id_marca");
        $sql_marca->bindParam(':id_marca', $marca, PDO::PARAM_INT);
        $sql_marca->execute();
        $marca_nombre = $sql_marca->fetchColumn();
        $es_saphirus = (strtolower($marca_nombre) === 'saphirus');

        // Preparar consulta SQL para actualizar productos
        $sql_actualizar_productos = $con->prepare("UPDATE productos
                                                SET nombre = :nombre_producto,
                                                    descripcion = :descripcion_producto,
                                                    id_categoria = :id_categoria,
                                                    id_marca = :id_marca,
                                                    destacado = :destacado,
                                                    estado = :estado
                                                WHERE id_producto = :id_producto;");
        $sql_actualizar_productos->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $sql_actualizar_productos->bindParam(':nombre_producto', $nombre, PDO::PARAM_STR);
        $sql_actualizar_productos->bindParam(':descripcion_producto', $descripcion, PDO::PARAM_STR);
        $sql_actualizar_productos->bindParam(':id_categoria', $categoria, PDO::PARAM_INT);
        $sql_actualizar_productos->bindParam(':id_marca', $marca, PDO::PARAM_INT);
        $sql_actualizar_productos->bindParam(':destacado', $destacado, PDO::PARAM_INT);
        $sql_actualizar_productos->bindParam(':estado', $estado, PDO::PARAM_INT);

        // Ejecutar consulta
        $sql_actualizar_productos->execute();

        // Verificar si hay registros existentes para este producto en la tabla variantes_tipo_precio
        $sql_check = $con->prepare("SELECT COUNT(*) FROM variantes_tipo_precio WHERE id_producto = :id_producto");
        $sql_check->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $sql_check->execute();
        $record_exists = $sql_check->fetchColumn() > 0;

        // Actualizar precios según la marca
        if ($es_saphirus) {
            // Para productos Saphirus
            if ($record_exists) {
                // Actualizar registros existentes
                $sql = "UPDATE variantes_tipo_precio 
                        SET precio = CASE 
                            WHEN id_tipo_precio = 2 THEN :p_minorista
                            WHEN id_tipo_precio = 3 THEN :p_mayorista_6
                            WHEN id_tipo_precio = 4 THEN :p_mayorista_48
                            WHEN id_tipo_precio = 5 THEN :p_mayorista_120
                        END
                        WHERE id_producto = :id_producto AND id_tipo_precio IN (2, 3, 4, 5)";
                
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $stmt->bindParam(':p_minorista', $precio_minorista);
                $stmt->bindParam(':p_mayorista_6', $precio_mayorista_6);
                $stmt->bindParam(':p_mayorista_48', $precio_mayorista_48);
                $stmt->bindParam(':p_mayorista_120', $precio_mayorista_120);
                $stmt->execute();
            } else {
                // Insertar nuevos registros
                $tipos_precios = [
                    ['id' => 2, 'precio' => $precio_minorista],
                    ['id' => 3, 'precio' => $precio_mayorista_6],
                    ['id' => 4, 'precio' => $precio_mayorista_48],
                    ['id' => 5, 'precio' => $precio_mayorista_120]
                ];
                
                foreach ($tipos_precios as $tipo) {
                    if ($tipo['precio'] !== null) {
                        $sql = "INSERT INTO variantes_tipo_precio (id_producto, id_tipo_precio, precio) 
                                VALUES (:id_producto, :id_tipo_precio, :precio)";
                        $stmt = $con->prepare($sql);
                        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                        $stmt->bindParam(':id_tipo_precio', $tipo['id'], PDO::PARAM_INT);
                        $stmt->bindParam(':precio', $tipo['precio']);
                        $stmt->execute();
                    }
                }
            }
        } else {
            // Para productos no Saphirus
            if ($record_exists) {
                // Actualizar registros existentes
                $sql = "UPDATE variantes_tipo_precio 
                        SET precio = CASE 
                            WHEN id_tipo_precio = 1 THEN :p_mayorista
                            WHEN id_tipo_precio = 2 THEN :p_minorista
                        END
                        WHERE id_producto = :id_producto AND id_tipo_precio IN (1, 2)";
                
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $stmt->bindParam(':p_mayorista', $precio_mayorista);
                $stmt->bindParam(':p_minorista', $precio_minorista);
                $stmt->execute();
            } else {
                // Insertar nuevos registros
                $tipos_precios = [
                    ['id' => 1, 'precio' => $precio_mayorista],
                    ['id' => 2, 'precio' => $precio_minorista]
                ];
                
                foreach ($tipos_precios as $tipo) {
                    if ($tipo['precio'] !== null) {
                        $sql = "INSERT INTO variantes_tipo_precio (id_producto, id_tipo_precio, precio) 
                                VALUES (:id_producto, :id_tipo_precio, :precio)";
                        $stmt = $con->prepare($sql);
                        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                        $stmt->bindParam(':id_tipo_precio', $tipo['id'], PDO::PARAM_INT);
                        $stmt->bindParam(':precio', $tipo['precio']);
                        $stmt->execute();
                    }
                }
            }
        }

        // Confirmar éxito
        echo json_encode(['success' => true]);
        
    } catch (PDOException $e) {
        // Manejar errores
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    // Respuesta para métodos no permitidos
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}