<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'];
$productId = $data['productId'];
$fraganciaAroma = isset($data['fraganciaAroma']) ? $data['fraganciaAroma'] : null;

function actualizarSesionCarrito($productoIndex) {
    // Si no quedan fragancias, elimina el producto
    if (empty($_SESSION['cart'][$productoIndex]['fragancias'])) {
        unset($_SESSION['cart'][$productoIndex]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexa el arreglo
    }
}

switch ($action) {
    case "eliminar_producto":
        foreach ($_SESSION['cart'] as $index => $producto) {
            if ($producto['id'] === $productId) {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexa el arreglo
                echo json_encode(["success" => true]);
                exit;
            }
        }
        echo json_encode(["success" => false, "message" => "Producto no encontrado"]);
        break;

    case "eliminar_fragancia":
        foreach ($_SESSION['cart'] as $index => &$producto) {
            if ($producto['id'] === $productId) {
                $producto['fragancias'] = array_filter($producto['fragancias'], function($fragancia) use ($fraganciaAroma) {
                    return $fragancia['aroma'] !== $fraganciaAroma;
                });
                $producto['fragancias'] = array_values($producto['fragancias']); // Reindexa fragancias
                actualizarSesionCarrito($index);
                echo json_encode(["success" => true]);
                exit;
            }
        }
        echo json_encode(["success" => false, "message" => "Fragancia no encontrada"]);
        break;

    case "modificar_cantidad":
        $cantidadModificacion = $data['cantidadModificacion'];
        foreach ($_SESSION['cart'] as $index => &$producto) {
            if ($producto['id'] === $productId) {
                foreach ($producto['fragancias'] as &$fragancia) {
                    if ($fragancia['aroma'] === $fraganciaAroma) {
                        $fragancia['cantidad'] += $cantidadModificacion;
                        if ($fragancia['cantidad'] <= 0) {
                            $producto['fragancias'] = array_filter($producto['fragancias'], function($f) use ($fraganciaAroma) {
                                return $f['aroma'] !== $fraganciaAroma;
                            });
                            $producto['fragancias'] = array_values($producto['fragancias']); // Reindexa fragancias
                        }
                        actualizarSesionCarrito($index);
                        echo json_encode(["success" => true]);
                        exit;
                    }
                }
            }
        }
        echo json_encode(["success" => false, "message" => "Fragancia o producto no encontrado"]);
        break;
    
    default:
        echo json_encode(["success" => false, "message" => "Acción no válida"]);
        break;
}
?>
