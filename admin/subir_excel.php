<?php
include './header.php';
include './aside.php';
include './footer.php';
include "./config/sbd.php";
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;


// Almacena carpetas creadas temporalmente para eliminarlas en caso de error
$tempCreatedDirs = [];

function createTempDirectory($path)
{
    global $tempCreatedDirs;

    if (!is_dir($path)) {
        if (mkdir($path, 0777, true)) {
            $tempCreatedDirs[] = $path;
            return true;
        }
        return false;
    }
    return true;
}

function cleanupTempDirectories()
{
    global $tempCreatedDirs;

    foreach (array_reverse($tempCreatedDirs) as $dir) {
        // Intenta eliminar archivos dentro del directorio
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    @unlink("$dir/$file");
                }
            }
            @rmdir($dir);
        }
    }
    // Vaciar el array después de limpiar
    $tempCreatedDirs = [];
}

function processImages($con)
{
    global $importSuccess;
    if (!$importSuccess) {
        echo "<p style='color:orange;'>Omitiendo procesamiento de imágenes debido a errores previos.</p>";
        return false;
    }

    /* 
    
    saphirus
    ambar
    milano
    shiny

     */
    $sourceDir = '../productos/shiny/';
    $processedCount = 0;
    $errorCount = 0;

    if (!is_dir($sourceDir)) {
        echo "<p style='color:red;'>Error: La carpeta de origen $sourceDir no existe.</p>";
        $importSuccess = false;
        return false;
    }

    echo "<h3>Procesando imágenes...</h3>";

    echo "<table border='1' cellpadding='5' cellspacing='0'>
            <tr>
                <th>ID Producto</th>
                <th>Nombre Imagen en BD</th>
                <th>Archivo Encontrado</th>
                <th>Estado</th>
            </tr>";

    try {
        // Verificar si ya hay una transacción activa
        $activeTransaction = $con->inTransaction();

        // Solo iniciar una nueva transacción si no hay una activa
        if (!$activeTransaction) {
            $con->beginTransaction();
        }

        // Obtener todos los productos con sus imágenes registradas
        $stmt = $con->prepare("SELECT p.id_producto, i.nombre 
                              FROM productos p 
                              LEFT JOIN imagenes i ON p.id_producto = i.id_producto
                              WHERE i.nombre IS NOT NULL");
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productos as $producto) {
            $id_producto = $producto['id_producto'];
            $rutaImagen = $producto['nombre'];

            // Extraer solo el nombre del archivo de la ruta
            $nombreArchivo = $rutaImagen;

            // Buscar el archivo en el directorio fuente (ignorando mayúsculas/minúsculas)
            $encontrado = false;
            $archivosEncontrados = scandir($sourceDir);

            foreach ($archivosEncontrados as $archivo) {
                if ($archivo === '.' || $archivo === '..') continue;

                // Comparar nombres en minúsculas
                if (strtolower($archivo) === strtolower($nombreArchivo)) {
                    // Crear directorio destino si no existe
                    $targetDir = "../assets/productos/imagen/$id_producto/";
                    if (!createTempDirectory($targetDir)) {
                        throw new Exception("No se pudo crear el directorio $targetDir");
                    }

                    $sourcePath = $sourceDir . $archivo;
                    $targetPath = $targetDir . $archivo;

                    // Mover el archivo
                    if (@copy($sourcePath, $targetPath)) {
                        $processedCount++;
                        $status = "<span style='color:green;'>Imagen copiada exitosamente</span>";
                        $encontrado = true;
                    } else {
                        $errorCount++;
                        $status = "<span style='color:red;'>Error al copiar el archivo</span>";
                        throw new Exception("Error al copiar $sourcePath a $targetPath");
                    }

                    echo "<tr>
                            <td>$id_producto</td>
                            <td>$nombreArchivo</td>
                            <td>$archivo</td>
                            <td>$status</td>
                          </tr>";
                    break;
                }
            }

            if (!$encontrado) {
                echo "<tr>
                        <td>$id_producto</td>
                        <td>$nombreArchivo</td>
                        <td>No encontrado</td>
                        <td><span style='color:orange;'>Imagen no encontrada en el directorio fuente</span></td>
                      </tr>";
            }
        }

        // Solo hacer commit si nosotros iniciamos la transacción
        if (!$activeTransaction) {
            $con->commit();
        }

        echo "</table>";
        echo "<h4>Resumen de procesamiento de imágenes:</h4>";
        echo "<p style='color:green;'>Imágenes procesadas exitosamente: $processedCount</p>";
        if ($errorCount > 0) {
            echo "<p style='color:orange;'>Imágenes no encontradas: $errorCount</p>";
        }
        return true;
    } catch (Exception $e) {
        // Solo hacer rollback si nosotros iniciamos la transacción
        if (!$activeTransaction && $con->inTransaction()) {
            $con->rollBack();
        }

        cleanupTempDirectories();

        echo "</table>";
        echo "<h4>Error en el procesamiento de imágenes:</h4>";
        echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        echo "<p style='color:red;'>No se realizaron cambios debido a errores.</p>";
        $importSuccess = false;
        return false;
    }
}

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
        array_shift($rows);
        $groupedProducts = [];

        if (!$con) {
            throw new Exception("No se pudo conectar a la base de datos.");
        }

        // INICIAR UNA ÚNICA TRANSACCIÓN PARA TODO EL PROCESO
        $con->beginTransaction();
        echo "<p style='color:green;'>Conexión a la base de datos exitosa. Iniciando transacción...</p>";

        $tipos_precios = [
            ['Minorista', 'Precio para ventas al por menor'],
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

        $id_minorista = $con->query("SELECT id_tipo_precio FROM tipos_precios WHERE LOWER(nombre) = 'minorista'")->fetchColumn();
        $id_mayorista_6 = $con->query("SELECT id_tipo_precio FROM tipos_precios WHERE LOWER(nombre) = 'mayorista 6'")->fetchColumn();
        $id_mayorista_48 = $con->query("SELECT id_tipo_precio FROM tipos_precios WHERE LOWER(nombre) = 'mayorista 48'")->fetchColumn();
        $id_mayorista_120 = $con->query("SELECT id_tipo_precio FROM tipos_precios WHERE LOWER(nombre) = 'mayorista 120'")->fetchColumn();


        if (!$id_minorista || !$id_mayorista_6 || !$id_mayorista_48 || !$id_mayorista_120) {
            throw new Exception("No se pudieron obtener los IDs de tipos de precios");
        }

        echo "<table border='1' cellpadding='5' cellspacing='0'>
                <tr>
                    <th>ID Precio Minorista</th>
                    <th>ID Precio Mayorista 6</th>
                    <th>ID Precio Mayorista 48</th>
                    <th>ID Precio Mayorista 120</th>
                </tr>
                <tr>
                    <td>$id_minorista</td>
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
                    <th>Precio Minorista</th>
                    <th>Precio Mayorista 6</th>
                    <th>Precio Mayorista 48</th>
                    <th>Precio Mayorista 120</th>
                    <th>Imagen</th>
                </tr>";

        foreach ($rows as $row) {
            if (count($row) < 12) {
                throw new Exception("Fila con datos insuficientes. Se requieren al menos 9 columnas.");
            }

            $codigo = $row[0];
            $nombre = $row[1];
            $atributo = $row[2];
            $valor = $row[3];
            $precio_minorista = floatval($row[4]);
            $precio_mayorista_6 = floatval($row[5]);
            $precio_mayorista_48 = floatval($row[6]);
            $precio_mayorista_120 = floatval($row[7]);
            $marca = $row[8];
            $submarca = $row[9];
            $imagen = $row[10];
            $categoria = $row[11];


            // Validaciones de datos
            if (empty($codigo) || empty($nombre)) {
                throw new Exception("Código y nombre de producto son obligatorios");
            }

            if (
                $precio_minorista <= 0 || $precio_mayorista_6 <= 0 ||
                $precio_mayorista_48 <= 0 || $precio_mayorista_120 <= 0
            ) {
                throw new Exception("Los precios deben ser valores positivos mayores a cero");
            }

            // **Buscar y/o insertar marca**
            $stmt = $con->prepare("SELECT id_marca FROM marcas WHERE LOWER(nombre) = LOWER(?)");
            $stmt->execute([$marca]);
            $id_marca = $stmt->fetchColumn();

            if (!$id_marca) {
                $stmt = $con->prepare("INSERT INTO marcas (nombre) VALUES (?)");
                if (!$stmt->execute([$marca])) {
                    throw new Exception("Error al insertar la marca '$marca'.");
                }
                $id_marca = $con->lastInsertId();
            }

            // **Buscar y/o insertar submarca**
            $id_submarca = null; // Valor por defecto si no hay submarca
            if (!empty($submarca)) {
                // Verificar si la submarca ya existe en la tabla marcas
                $stmt = $con->prepare("SELECT id_marca FROM marcas WHERE LOWER(nombre) = LOWER(?)");
                $stmt->execute([$submarca]);
                $id_submarca = $stmt->fetchColumn();

                if (!$id_submarca) {
                    // Si no existe, la insertamos como una nueva marca
                    $stmt = $con->prepare("INSERT INTO marcas (nombre) VALUES (?)");
                    if (!$stmt->execute([$submarca])) {
                        throw new Exception("Error al insertar la submarca '$submarca'.");
                    }
                    $id_submarca = $con->lastInsertId();
                }
            }

            // **Buscar y/o insertar categoría**
            $stmt = $con->prepare("SELECT id_categoria FROM categorias WHERE LOWER(nombre) = LOWER(?)");
            $stmt->execute([$categoria]);
            $id_categoria = $stmt->fetchColumn();

            if (!$id_categoria) {
                $stmt = $con->prepare("INSERT INTO categorias (nombre) VALUES (?)");
                if (!$stmt->execute([$categoria])) {
                    throw new Exception("Error al insertar la categoría '$categoria'.");
                }
                $id_categoria = $con->lastInsertId();
            }

            if (!isset($groupedProducts[$codigo])) {
                $groupedProducts[$codigo] = [
                    'nombre' => $nombre,
                    'precio_minorista' => $precio_minorista,
                    'precio_mayorista_6' => $precio_mayorista_6,
                    'precio_mayorista_48' => $precio_mayorista_48,
                    'precio_mayorista_120' => $precio_mayorista_120,
                    'imagen' => $imagen,
                    'id_marca' => $id_marca,
                    'id_submarca' => $id_submarca,
                    'id_categoria' => $id_categoria,
                    'variantes' => []
                ];
            }

            $groupedProducts[$codigo]['variantes'][] = [
                'atributo' => $atributo,
                'valor' => $valor
            ];

            echo "<tr>
                    <td>$codigo</td>
                    <td>$nombre</td>
                    <td>$marca</td>
                    <td>$submarca</td>
                    <td>$categoria</td>
                    <td>$precio_minorista</td>
                    <td>$precio_mayorista_6</td>
                    <td>$precio_mayorista_48</td>
                    <td>$precio_mayorista_120</td>
                    <td>$imagen</td>
                  </tr>";
        }

        echo "</table>";

        // Procesar productos en lotes de 16 variantes
        echo "<h3>Procesando variantes en lotes de 16...</h3>";
        $successCount = 0;
        $errorCount = 0;

        foreach ($groupedProducts as $codigo => $producto) {
            try {
                // Verificar si el producto ya existe
                $stmt = $con->prepare("SELECT id_producto FROM productos WHERE LOWER(nombre) = LOWER(?)");
                $stmt->execute([$producto['nombre']]);
                $id_producto = $stmt->fetchColumn();

                if (!$id_producto) {
                    $stmt = $con->prepare("INSERT INTO productos (nombre, id_marca, id_submarca, id_categoria) VALUES (?, ?, ?, ?)");
                    if (!$stmt->execute([$producto['nombre'], $id_marca, $id_submarca, $producto['id_categoria']])) {
                        throw new Exception("Error al insertar el producto '{$producto['nombre']}'");
                    }
                    $id_producto = $con->lastInsertId();
                }

                // Insertar precios
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
                    throw new Exception("Error al insertar los precios para el producto '{$producto['nombre']}'");
                }

                // Insertar imagen si existe
                if (!empty($producto['imagen'])) {
                    $stmt = $con->prepare("INSERT INTO imagenes (id_producto, nombre) 
                                          SELECT ?, ? WHERE NOT EXISTS 
                                          (SELECT 1 FROM imagenes WHERE id_producto = ? AND LOWER(nombre) = LOWER(?))");
                    if (!$stmt->execute([$id_producto, $producto['imagen'], $id_producto, $producto['imagen']])) {
                        throw new Exception("Error al registrar la imagen para el producto '{$producto['nombre']}'");
                    }
                }

                // Sistema avanzado de generación de SKU para más de 26 variantes
                $totalVariantes = count($producto['variantes']);
                echo "<p>Producto $codigo tiene $totalVariantes variantes en total</p>";

                // Sistema de etiquetado avanzado para manejar más de 26 variantes
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
                        $variantesSkuMap[$i] = $prefix . $suffix;
                    }
                }

                // Procesar variantes en lotes de 16
                $variantChunks = array_chunk($producto['variantes'], 16);
                foreach ($variantChunks as $chunkIndex => $variantChunk) {
                    echo "<p>Procesando lote " . ($chunkIndex + 1) . " de variantes para producto $codigo (" . count($variantChunk) . " variantes)</p>";

                    foreach ($variantChunk as $indexInChunk => $variante) {
                        // Calcular el índice global de esta variante
                        $globalIndex = $chunkIndex * 16 + $indexInChunk;
                        $variantSuffix = $variantesSkuMap[$globalIndex];
                        $sku = $codigo . $variantSuffix;

                        echo "<p>Variante $globalIndex: usando sufijo '$variantSuffix' para crear SKU '$sku'</p>";

                        // Verificar si la variante ya existe
                        $stmt_check = $con->prepare("SELECT COUNT(*) FROM variantes WHERE LOWER(sku) = LOWER(?)");
                        $stmt_check->execute([$sku]);
                        $existe = $stmt_check->fetchColumn();

                        if ($existe > 0) {
                            echo "<p style='color:orange;'>Variante con SKU '$sku' ya existe, omitiendo.</p>";
                            continue;
                        }

                        $nombre_variante = $producto['nombre'] . ' - ' . $variante['valor'];
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
                    }

                    echo "<p style='color:green;'>Lote " . ($chunkIndex + 1) . " procesado con éxito.</p>";
                    $successCount++;
                }
            } catch (Exception $e) {
                throw new Exception("Error procesando producto $codigo: " . $e->getMessage());
            }
        }

        // Si llegamos hasta aquí sin errores, seguimos adelante con las imágenes
        echo "<h3>Resumen de procesamiento de productos:</h3>";
        echo "<p>Lotes exitosos: $successCount</p>";

        // Primero, procesamos las imágenes (sin hacer commit todavía)
        if (!processImages($con)) {
            throw new Exception("Error en el procesamiento de imágenes.");
        }

        // Si todo ha ido bien hasta aquí, hacemos commit
        $con->commit();

        echo "<h3 style='color:green;'>IMPORTACIÓN COMPLETADA CON ÉXITO</h3>";
        echo "<p>Todos los productos, variantes e imágenes han sido importados correctamente.</p>";

        return true;
    } catch (Exception $e) {
        if ($con && $con->inTransaction()) {
            $con->rollBack();
        }

        // Limpieza de directorios temporales
        cleanupTempDirectories();

        echo "<div style='border: 2px solid red; padding: 15px; margin: 20px 0; background-color: #fff0f0;'>";
        echo "<h3 style='color:red;'>ERROR CRÍTICO - IMPORTACIÓN CANCELADA</h3>";
        echo "<p style='color:red;'><strong>Detalle del error:</strong> " . $e->getMessage() . "</p>";
        echo "<p style='color:red;'><strong>No se han realizado cambios en la base de datos.</strong></p>";
        echo "<p style='color:red;'><strong>No se han creado carpetas permanentes en el sistema.</strong></p>";
        echo "</div>";

        $importSuccess = false;
        return false;
    }
}

// Variable global para rastrear el estado del proceso
$importSuccess = true;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Productos</title>
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

    <div class="wrapper">
        <div class="content-wrapper">
            <h1>Importar Productos desde Excel</h1>

            <form method="post" enctype="multipart/form-data">
                <h2>Seleccionar archivo Excel</h2>
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
        </div>
    </div>
</body>

</html>