<?php
session_start();

// Lee el contenido del cuerpo de la solicitud
$inputJSON = file_get_contents("php://input");
$input = json_decode($inputJSON, true);

// Verifica que se recibió el producto correctamente
if (isset($input['producto'])) {
    $producto = $input['producto'];
    $productoId = $producto['id']; // ID del producto para verificar existencia en el carrito
    $productoFragancias = $producto['fragancias']; // Fragancias del producto para la validación

    // Inicializa el carrito en la sesión si no existe
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Variable para verificar si el producto ya existe en el carrito
    $productoExiste = false;

    // Recorre el carrito para verificar si ya existe el producto con el mismo ID
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $productoId) {
            // El producto ya está en el carrito, validamos las fragancias
            $fraganciasExistentes = $item['fragancias'];

            // Verificar si las fragancias del carrito son las mismas que las nuevas (comparando por sku)
            $fraganciasIguales = true;

            // Compara cada fragancia del carrito con las fragancias del producto
            $fraganciasDelCarritoSku = array_map(function($fragancia) {
                return $fragancia['sku'];
            }, $fraganciasExistentes);

            $fraganciasProductoSku = array_map(function($fragancia) {
                return $fragancia['sku'];
            }, $productoFragancias);

            // Si las fragancias no coinciden, se marcan como diferentes
            if (array_diff($fraganciasDelCarritoSku, $fraganciasProductoSku) || array_diff($fraganciasProductoSku, $fraganciasDelCarritoSku)) {
                $fraganciasIguales = false;
            }

            // Si las fragancias son diferentes, las actualizamos
            if (!$fraganciasIguales) {
                // Eliminamos las fragancias existentes que no están en el nuevo producto
                foreach ($productoFragancias as $fraganciaNueva) {
                    $encontrada = false;
                    foreach ($fraganciasExistentes as &$fraganciaExistente) {
                        if ($fraganciaExistente['sku'] === $fraganciaNueva['sku']) {
                            // Si el SKU ya existe, sumamos la cantidad
                            $fraganciaExistente['cantidad'] += $fraganciaNueva['cantidad'];
                            $encontrada = true;
                            break;
                        }
                    }
                    // Si no se encontró la fragancia, la agregamos
                    if (!$encontrada) {
                        $fraganciasExistentes[] = $fraganciaNueva;
                    }
                }

                // Ahora actualizamos el carrito con las nuevas fragancias
                $item['fragancias'] = $fraganciasExistentes;

                echo json_encode(["success" => true, "message" => "Producto actualizado en el carrito con las nuevas fragancias"]);
                return;
            } else {
                // Si las fragancias son las mismas, no hacemos nada
                echo json_encode(["success" => false, "message" => "El producto ya está en el carrito con las mismas fragancias"]);
                return;
            }
        }
    }

    // Si el producto no existe en el carrito, lo agregamos
    $_SESSION['cart'][] = $producto;

    // Responde con un mensaje de éxito
    echo json_encode(["success" => true, "message" => "Producto agregado al carrito"]);

} else {
    // Responde con un mensaje de error si no se recibe el producto
    echo json_encode(["success" => false, "message" => "Error: no se recibió el producto"]);
}
?>
