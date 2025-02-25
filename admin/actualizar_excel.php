<?php
include "./config/sbd.php";
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Variable global para rastrear el estado del proceso
$importSuccess = true;

function processExcelData($file, $con)
{
    global $importSuccess;
    $importSuccess = true;

    try {
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new Exception("El archivo no se subió correctamente.");
        }

        $filePath = $file['tmp_name'];
        echo "<h2>Procesando archivo: " . htmlspecialchars($file['name']) . "</h2>";

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (!$rows) {
            throw new Exception("El archivo está vacío o no se pudo leer.");
        }

        echo "<p style='color:green;'>Archivo cargado con éxito.</p>";
        array_shift($rows); // Eliminar cabecera
        $groupedProducts = [];

        if (!$con) {
            throw new Exception("No se pudo conectar a la base de datos.");
        }

        // INICIAR UNA ÚNICA TRANSACCIÓN PARA TODO EL PROCESO
        $con->beginTransaction();
        echo "<p style='color:green;'>Conexión a la base de datos exitosa. Iniciando transacción...</p>";

        // Asegurar que existan los tipos de precios
        $tipos_precios = [
            ['Minorista', 'Precio para ventas al por menor'],
            ['Mayorista', 'Precio para ventas al por mayor general'],
            ['Mayorista 6', 'Precio para ventas al por mayor 6 unidades'],
            ['Mayorista 48', 'Precio para ventas al por mayor 48 unidades'],
            ['Mayorista 120', 'Precio para ventas al por mayor 120 unidades']
        ];

        foreach ($tipos_precios as $tipo) {
            $stmt = $con->prepare("INSERT INTO tipos_precios (nombre, descripcion) 
            SELECT ?, ? WHERE NOT EXISTS 
            (SELECT 1 FROM tipos_precios WHERE LOWER(nombre) = LOWER(?))");
            $stmt->execute([$tipo[0], $tipo[1], $tipo[0]]);
        }

        echo "<h3>Tipos de precios verificados.</h3>";

        // Obtener IDs de los tipos de precios - MODIFICADO PARA USAR LOWER()
        $id_minorista = $con->query("SELECT id_tipo_precio FROM tipos_precios WHERE LOWER(nombre) = LOWER('minorista')")->fetchColumn();
        $id_mayorista = $con->query("SELECT id_tipo_precio FROM tipos_precios WHERE LOWER(nombre) = LOWER('mayorista')")->fetchColumn();
        $id_mayorista_6 = $con->query("SELECT id_tipo_precio FROM tipos_precios WHERE LOWER(nombre) = LOWER('mayorista 6')")->fetchColumn();
        $id_mayorista_48 = $con->query("SELECT id_tipo_precio FROM tipos_precios WHERE LOWER(nombre) = LOWER('mayorista 48')")->fetchColumn();
        $id_mayorista_120 = $con->query("SELECT id_tipo_precio FROM tipos_precios WHERE LOWER(nombre) = LOWER('mayorista 120')")->fetchColumn();

        if (!$id_minorista || (!$id_mayorista && !$id_mayorista_6)) {
            throw new Exception("No se pudieron obtener los IDs de tipos de precios esenciales");
        }

        echo "<table border='1' cellpadding='5' cellspacing='0'>
                <tr>
                    <th>ID Precio Minorista</th>
                    <th>ID Precio Mayorista General</th>
                    <th>ID Precio Mayorista 6</th>
                    <th>ID Precio Mayorista 48</th>
                    <th>ID Precio Mayorista 120</th>
                </tr>
                <tr>
                    <td>$id_minorista</td>
                    <td>$id_mayorista</td>
                    <td>$id_mayorista_6</td>
                    <td>$id_mayorista_48</td>
                    <td>$id_mayorista_120</td>
                </tr>
              </table>";

        echo "<h3>Procesando productos...</h3>";

        echo "<table border='1' cellpadding='5' cellspacing='0'>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Submarca</th>
                    <th>Categoría</th>
                    <th>Tipo Producto</th>
                    <th>Precios</th>
                    <th>Estado</th>
                </tr>";

        // Contadores para el resumen
        $nuevosProductos = 0;
        $productosActualizados = 0;
        $nuevasVariantes = 0;
        $variantesActualizadas = 0;

        foreach ($rows as $row) {
            if (count($row) < 10) {
                throw new Exception("Fila con datos insuficientes. Se requieren al menos 10 columnas.");
            }

            $codigo = $row[0];
            $nombre = $row[1];
            $atributo = $row[2];
            $valor = $row[3];
            $precio_minorista = floatval($row[4]);

            // Detectar tipo de producto basado en los precios disponibles
            $tipo_producto = "";
            $precios_info = "";

            // Verificar si es un producto con 4 precios o 2 precios
            if (
                isset($row[5]) && isset($row[6]) && isset($row[7]) &&
                is_numeric($row[5]) && is_numeric($row[6]) && is_numeric($row[7])
            ) {

                // Producto con 4 precios
                $precio_mayorista_6 = floatval($row[5]);
                $precio_mayorista_48 = floatval($row[6]);
                $precio_mayorista_120 = floatval($row[7]);
                $marca = $row[8];
                $submarca = $row[9]; // Tiene submarca
                $categoria = isset($row[11]) ? $row[11] : '';

                $tipo_producto = "4 precios con submarca";
                $precios_info = "Min: $precio_minorista, May6: $precio_mayorista_6, May48: $precio_mayorista_48, May120: $precio_mayorista_120";

                // Validar los 4 precios
                if (
                    $precio_minorista <= 0 || $precio_mayorista_6 <= 0 ||
                    $precio_mayorista_48 <= 0 || $precio_mayorista_120 <= 0
                ) {
                    throw new Exception("Los precios deben ser valores positivos mayores a cero");
                }
            } else if (isset($row[5]) && is_numeric($row[5])) {
                // Producto con solo 2 precios
                $precio_mayorista = floatval($row[5]);
                $marca = $row[6]; // Ajustar índices para este tipo
                $submarca = null; // No tiene submarca
                $categoria = isset($row[8]) ? $row[8] : '';

                $tipo_producto = "2 precios sin submarca";
                $precios_info = "Min: $precio_minorista, May: $precio_mayorista";

                // Validar los 2 precios
                if ($precio_minorista <= 0 || $precio_mayorista <= 0) {
                    throw new Exception("Los precios deben ser valores positivos mayores a cero");
                }
            } else {
                throw new Exception("Formato de precios no reconocido para el producto $codigo");
            }

            // Validaciones de datos comunes
            if (empty($codigo) || empty($nombre)) {
                throw new Exception("Código y nombre de producto son obligatorios");
            }

            // Agrupar por código para procesar juntas todas las variantes de un producto
            if (!isset($groupedProducts[$codigo])) {
                $groupedProducts[$codigo] = [
                    'nombre' => $nombre,
                    'precio_minorista' => $precio_minorista,
                    'tipo_producto' => $tipo_producto,
                    'marca' => $marca,
                    'submarca' => $submarca,
                    'categoria' => $categoria,
                    'variantes' => []
                ];

                // Guardar precios según el tipo
                if ($tipo_producto == "4 precios con submarca") {
                    $groupedProducts[$codigo]['precio_mayorista_6'] = $precio_mayorista_6;
                    $groupedProducts[$codigo]['precio_mayorista_48'] = $precio_mayorista_48;
                    $groupedProducts[$codigo]['precio_mayorista_120'] = $precio_mayorista_120;
                } else {
                    $groupedProducts[$codigo]['precio_mayorista'] = $precio_mayorista;
                }
            }

            $groupedProducts[$codigo]['variantes'][] = [
                'atributo' => $atributo,
                'valor' => $valor
            ];

            $estado = "<span style='color:blue;'>Pendiente</span>";

            echo "<tr>
                    <td>$codigo</td>
                    <td>$nombre</td>
                    <td>$marca</td>
                    <td>" . ($submarca ?: "N/A") . "</td>
                    <td>$categoria</td>
                    <td>$tipo_producto</td>
                    <td>$precios_info</td>
                    <td>$estado</td>
                  </tr>";
        }

        echo "</table>";

        echo "<h3>Procesando productos y variantes...</h3>";

        echo "<table border='1' cellpadding='5' cellspacing='0'>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Acción Producto</th>
                    <th>Variantes</th>
                    <th>Resultado</th>
                </tr>";

        foreach ($groupedProducts as $codigo => $producto) {
            try {
                // PASO 1: Verificar/Insertar Marca
                $stmt = $con->prepare("SELECT id_marca FROM marcas WHERE LOWER(nombre) = LOWER(?)");
                $stmt->execute([$producto['marca']]);
                $id_marca = $stmt->fetchColumn();

                if (!$id_marca) {
                    $stmt = $con->prepare("INSERT INTO marcas (nombre) VALUES (?)");
                    if (!$stmt->execute([$producto['marca']])) {
                        throw new Exception("Error al insertar la marca '{$producto['marca']}'.");
                    }
                    $id_marca = $con->lastInsertId();
                }

                // PASO 2: Verificar/Insertar Submarca (solo para productos con 4 precios)
                $id_submarca = null;
                if ($producto['tipo_producto'] == "4 precios con submarca" && !empty($producto['submarca'])) {
                    $stmt = $con->prepare("SELECT id_marca FROM marcas WHERE LOWER(nombre) = LOWER(?)");
                    $stmt->execute([$producto['submarca']]);
                    $id_submarca = $stmt->fetchColumn();

                    if (!$id_submarca) {
                        $stmt = $con->prepare("INSERT INTO marcas (nombre) VALUES (?)");
                        if (!$stmt->execute([$producto['submarca']])) {
                            throw new Exception("Error al insertar la submarca '{$producto['submarca']}'.");
                        }
                        $id_submarca = $con->lastInsertId();
                    }
                }

                // PASO 3: Verificar/Insertar Categoría
                $stmt = $con->prepare("SELECT id_categoria FROM categorias WHERE LOWER(nombre) = LOWER(?)");
                $stmt->execute([$producto['categoria']]);
                $id_categoria = $stmt->fetchColumn();

                if (!$id_categoria) {
                    $stmt = $con->prepare("INSERT INTO categorias (nombre) VALUES (?)");
                    if (!$stmt->execute([$producto['categoria']])) {
                        throw new Exception("Error al insertar la categoría '{$producto['categoria']}'.");
                    }
                    $id_categoria = $con->lastInsertId();
                }

                // PASO 4: Verificar si el producto existe por nombre
                $stmt = $con->prepare("SELECT id_producto FROM productos WHERE LOWER(nombre) = LOWER(?)");
                $stmt->execute([$producto['nombre']]);
                $id_producto = $stmt->fetchColumn();

                $productoAccion = "";

                // Si el producto no existe, lo creamos
                if (!$id_producto) {
                    $stmt = $con->prepare("INSERT INTO productos (nombre, id_marca, id_submarca, id_categoria) VALUES (?, ?, ?, ?)");
                    if (!$stmt->execute([$producto['nombre'], $id_marca, $id_submarca, $id_categoria])) {
                        throw new Exception("Error al insertar el producto '{$producto['nombre']}'");
                    }
                    $id_producto = $con->lastInsertId();
                    $productoAccion = "<span style='color:green;'>Nuevo producto creado</span>";
                    $nuevosProductos++;
                } else {
                    // Actualizar información del producto si ya existe
                    $stmt = $con->prepare("UPDATE productos SET id_marca = ?, id_submarca = ?, id_categoria = ? WHERE id_producto = ?");
                    if (!$stmt->execute([$id_marca, $id_submarca, $id_categoria, $id_producto])) {
                        throw new Exception("Error al actualizar el producto '{$producto['nombre']}'");
                    }
                    $productoAccion = "<span style='color:blue;'>Producto actualizado</span>";
                    $productosActualizados++;
                }

                // PASO 5: Actualizar/Insertar precios según tipo de producto
                if ($producto['tipo_producto'] == "4 precios con submarca") {
                    // Producto con 4 precios
                    $stmt = $con->prepare("INSERT INTO variantes_tipo_precio (id_producto, id_tipo_precio, precio, cantidad_minima) 
                          VALUES 
                          (?, ?, ?, 1),
                          (?, ?, ?, 6),
                          (?, ?, ?, 48),
                          (?, ?, ?, 120)
                          ON DUPLICATE KEY UPDATE precio = VALUES(precio)");
                    if (!$stmt->execute([
                        $id_producto,
                        $id_minorista,
                        $producto['precio_minorista'],
                        $id_producto,
                        $id_mayorista_6,
                        $producto['precio_mayorista_6'],
                        $id_producto,
                        $id_mayorista_48,
                        $producto['precio_mayorista_48'],
                        $id_producto,
                        $id_mayorista_120,
                        $producto['precio_mayorista_120']
                    ])) {
                        throw new Exception("Error al actualizar los precios para el producto '{$producto['nombre']}'");
                    }
                } else {
                    // Producto con 2 precios
                    $stmt = $con->prepare("INSERT INTO variantes_tipo_precio (id_producto, id_tipo_precio, precio, cantidad_minima) 
                          VALUES 
                          (?, ?, ?, 1),
                          (?, ?, ?, 1)
                          ON DUPLICATE KEY UPDATE precio = VALUES(precio)");
                    if (!$stmt->execute([
                        $id_producto,
                        $id_minorista,
                        $producto['precio_minorista'],
                        $id_producto,
                        $id_mayorista,
                        $producto['precio_mayorista']
                    ])) {
                        throw new Exception("Error al actualizar los precios para el producto '{$producto['nombre']}'");
                    }
                }

                // PASO 6: Procesar variantes
                $totalVariantes = count($producto['variantes']);
                $variantesInfo = [];

                // Sistema de sufijos para SKU
                $variantesSkuMap = [];
                for ($i = 0; $i < $totalVariantes; $i++) {
                    if ($i < 26) {
                        // A-Z para las primeras 26
                        $variantesSkuMap[$i] = "." . chr(65 + $i);
                    } else if ($i < 52) {
                        // AA-AZ para las siguientes 26
                        $variantesSkuMap[$i] = ".A" . chr(65 + ($i - 26));
                    } else if ($i < 78) {
                        // BA-BZ para las siguientes 26
                        $variantesSkuMap[$i] = ".B" . chr(65 + ($i - 52));
                    } else {
                        // CA-CZ para las siguientes, y así sucesivamente
                        $prefix = chr(67 + floor(($i - 78) / 26));
                        $suffix = chr(65 + (($i - 78) % 26));
                        $variantesSkuMap[$i] = "." . $prefix . $suffix;
                    }
                }

                // Procesar cada variante
                foreach ($producto['variantes'] as $varianteIndex => $variante) {
                    $variantSuffix = $variantesSkuMap[$varianteIndex];
                    $sku = $codigo . $variantSuffix;
                    $nombre_variante = $producto['nombre'] . ' - ' . $variante['valor'];

                    // Verificar si la variante ya existe - MODIFICADO PARA USAR LOWER()
                    $stmt_check = $con->prepare("SELECT sku FROM variantes WHERE LOWER(sku) = LOWER(?)");
                    $stmt_check->execute([$sku]);
                    $id_variante = $stmt_check->fetchColumn();

                    if ($id_variante) {
                        // Actualizar variante existente
                        $stmt = $con->prepare("UPDATE variantes SET 
                                            nombre_variante = ?,
                                            aroma = ?,
                                            titulo = ?,
                                            color = ?
                                            WHERE sku = ?");
                        if (!$stmt->execute([
                            $nombre_variante,
                            $variante['atributo'] == 'aroma' ? $variante['valor'] : null,
                            $variante['atributo'] == 'titulo' ? $variante['valor'] : null,
                            $variante['atributo'] == 'color' ? $variante['valor'] : null,
                            $id_variante
                        ])) {
                            throw new Exception("Error al actualizar la variante '$nombre_variante'");
                        }
                        $variantesInfo[] = "$sku: <span style='color:blue;'>Actualizada</span>";
                        $variantesActualizadas++;
                    } else {
                        // Crear nueva variante
                        $stmt = $con->prepare("INSERT INTO variantes (sku, nombre_variante, id_producto, aroma, titulo, color) 
                                             VALUES (?, ?, ?, ?, ?, ?)");
                        if (!$stmt->execute([
                            $sku,
                            $nombre_variante,
                            $id_producto,
                            $variante['atributo'] == 'aroma' ? $variante['valor'] : null,
                            $variante['atributo'] == 'titulo' ? $variante['valor'] : null,
                            $variante['atributo'] == 'color' ? $variante['valor'] : null
                        ])) {
                            throw new Exception("Error al insertar la variante '$nombre_variante'");
                        }
                        $variantesInfo[] = "$sku: <span style='color:green;'>Nueva</span>";
                        $nuevasVariantes++;
                    }
                }

                $variantesResumen = implode("<br>", $variantesInfo);
                $resultado = "<span style='color:green;'>Éxito</span>";

                echo "<tr>
                        <td>$codigo</td>
                        <td>{$producto['nombre']}</td>
                        <td>{$producto['tipo_producto']}</td>
                        <td>$productoAccion</td>
                        <td>$variantesResumen</td>
                        <td>$resultado</td>
                      </tr>";
            } catch (Exception $e) {
                echo "<tr>
                        <td>$codigo</td>
                        <td>{$producto['nombre']}</td>
                        <td>{$producto['tipo_producto']}</td>
                        <td colspan='2'><span style='color:red;'>Error: {$e->getMessage()}</span></td>
                        <td><span style='color:red;'>Fallido</span></td>
                      </tr>";
                throw new Exception("Error procesando producto $codigo: " . $e->getMessage());
            }
        }

        echo "</table>";

        // Si todo ha ido bien hasta aquí, hacemos commit
        $con->commit();

        echo "<h3 style='color:green;'>ACTUALIZACIÓN COMPLETADA CON ÉXITO</h3>";
        echo "<div style='border: 2px solid green; padding: 15px; margin: 20px 0; background-color: #f0fff0;'>";
        echo "<h4>Resumen de la operación:</h4>";
        echo "<ul>";
        echo "<li><strong>Productos nuevos:</strong> $nuevosProductos</li>";
        echo "<li><strong>Productos actualizados:</strong> $productosActualizados</li>";
        echo "<li><strong>Variantes nuevas:</strong> $nuevasVariantes</li>";
        echo "<li><strong>Variantes actualizadas:</strong> $variantesActualizadas</li>";
        echo "</ul>";
        echo "</div>";

        return true;
    } catch (Exception $e) {
        if ($con && $con->inTransaction()) {
            $con->rollBack();
        }

        echo "<div style='border: 2px solid red; padding: 15px; margin: 20px 0; background-color: #fff0f0;'>";
        echo "<h3 style='color:red;'>ERROR CRÍTICO - ACTUALIZACIÓN CANCELADA</h3>";
        echo "<p style='color:red;'><strong>Detalle del error:</strong> " . $e->getMessage() . "</p>";
        echo "<p style='color:red;'><strong>No se han realizado cambios en la base de datos.</strong></p>";
        echo "</div>";

        $importSuccess = false;
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Productos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1,
        h2,
        h3 {
            color: #333;
        }

        form {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        input[type="file"] {
            margin-bottom: 15px;
            display: block;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .warning {
            color: orange;
        }
    </style>
</head>

<body>
    <h1>Actualizar Productos desde Excel</h1>

    <form method="post" enctype="multipart/form-data">
        <h2>Seleccionar archivo Excel</h2>
        <p>El archivo debe contener las siguientes columnas en este orden:</p>
        <p><strong>Para productos con 4 precios:</strong> Código, Nombre, Atributo, Valor, Precio Minorista,
            Precio Mayorista 6, Precio Mayorista 48, Precio Mayorista 120, Marca, Submarca, Categoría.</p>
        <p><strong>Para productos con 2 precios:</strong> Código, Nombre, Atributo, Valor, Precio Minorista,
            Precio Mayorista, Marca, Categoría (No incluir submarca).</p>
        <input type="file" name="excel_file" accept=".xlsx,.xls">
        <input type="submit" name="submit" value="Procesar Archivo">
    </form>

    <?php
    if (isset($_POST['submit'])) {
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            echo "<div style='border: 2px solid red; padding: 15px; margin: 20px 0; background-color: #fff0f0;'>";
            echo "<h3 style='color:red;'>ERROR AL SUBIR EL ARCHIVO</h3>";
            echo "<p>Por favor, verifique el archivo e inténtelo de nuevo. Código de error: " .
                (isset($_FILES['excel_file']) ? $_FILES['excel_file']['error'] : 'Archivo no encontrado') . "</p>";
            echo "</div>";
        } else {
            processExcelData($_FILES['excel_file'], $con);
        }
    }
    ?>
</body>

</html>