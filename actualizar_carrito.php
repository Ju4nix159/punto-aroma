<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'];
$productId = $data['productId'];
$fraganciaAroma = isset($data['fraganciaAroma']) ? $data['fraganciaAroma'] : null;
$recalcularPrecio = isset($data['recalcularPrecio']) ? $data['recalcularPrecio'] : true;

function actualizarSesionCarrito($productoIndex)
{
    // Si no quedan fragancias, elimina el producto
    if (empty($_SESSION['cart'][$productoIndex]['fragancias'])) {
        unset($_SESSION['cart'][$productoIndex]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexa el arreglo
    } else {
        // Recalcular cantidad total de fragancias para este producto
        $totalFragancias = 0;
        foreach ($_SESSION['cart'][$productoIndex]['fragancias'] as $fragancia) {
            $totalFragancias += $fragancia['cantidad'];
        }

        $_SESSION['cart'][$productoIndex]['totalQuantity'] = $totalFragancias;

        // Determinar si aplica precio por mayor basado en la cantidad
        $precioBase = $_SESSION['cart'][$productoIndex]['precioBase'];
        $precio = $precioBase; // Por defecto
        $esPrecioMayor = false;

        // Verificar precios por mayoreo (asumiendo que estos están disponibles en la sesión)
        if ($totalFragancias >= 120 && isset($_SESSION['cart'][$productoIndex]['precio120']) && $_SESSION['cart'][$productoIndex]['precio120'] > 0) {
            $precio = $_SESSION['cart'][$productoIndex]['precio120'];
            $esPrecioMayor = true;
        } elseif ($totalFragancias >= 48 && isset($_SESSION['cart'][$productoIndex]['precio48']) && $_SESSION['cart'][$productoIndex]['precio48'] > 0) {
            $precio = $_SESSION['cart'][$productoIndex]['precio48'];
            $esPrecioMayor = true;
        } elseif ($totalFragancias >= 6 && isset($_SESSION['cart'][$productoIndex]['precio6']) && $_SESSION['cart'][$productoIndex]['precio6'] > 0) {
            $precio = $_SESSION['cart'][$productoIndex]['precio6'];
            $esPrecioMayor = true;
        }

        $_SESSION['cart'][$productoIndex]['precio'] = $precio;
        $_SESSION['cart'][$productoIndex]['esPrecioMayor'] = $esPrecioMayor;
    }
}

// Función para calcular el total del carrito
function calcularTotal()
{
    $total = 0;
    foreach ($_SESSION['cart'] as $producto) {
        $totalFragancias = 0;
        foreach ($producto['fragancias'] as $fragancia) {
            $totalFragancias += $fragancia['cantidad'];
        }
        $total += $totalFragancias * $producto['precio'];
    }
    return $total;
}

$response = ["success" => false, "message" => "Operación no completada"];

switch ($action) {
    case "eliminar_producto":
        foreach ($_SESSION['cart'] as $index => $producto) {
            if ($producto['id'] === $productId) {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexa el arreglo
                $response = [
                    "success" => true,
                    "message" => "Producto eliminado correctamente",
                    "cart" => $_SESSION['cart'],
                    "total" => calcularTotal()
                ];
                break;
            }
        }
        if (!$response["success"]) {
            $response["message"] = "Producto no encontrado";
        }
        break;

    case "eliminar_fragancia":
        foreach ($_SESSION['cart'] as $index => &$producto) {
            if ($producto['id'] === $productId) {
                $producto['fragancias'] = array_filter($producto['fragancias'], function ($fragancia) use ($fraganciaAroma) {
                    return $fragancia['aroma'] !== $fraganciaAroma;
                });
                $producto['fragancias'] = array_values($producto['fragancias']); // Reindexa fragancias
                actualizarSesionCarrito($index);
                $response = [
                    "success" => true,
                    "message" => "Fragancia eliminada correctamente",
                    "cart" => $_SESSION['cart'],
                    "total" => calcularTotal()
                ];
                break;
            }
        }
        if (!$response["success"]) {
            $response["message"] = "Fragancia no encontrada";
        }
        break;

    case "modificar_cantidad":
        $cantidadModificacion = $data['cantidadModificacion'];
        foreach ($_SESSION['cart'] as $index => &$producto) {
            if ($producto['id'] === $productId) {
                foreach ($producto['fragancias'] as &$fragancia) {
                    if ($fragancia['aroma'] === $fraganciaAroma) {
                        $fragancia['cantidad'] += $cantidadModificacion;
                        if ($fragancia['cantidad'] <= 0) {
                            $producto['fragancias'] = array_filter($producto['fragancias'], function ($f) use ($fraganciaAroma) {
                                return $f['aroma'] !== $fraganciaAroma;
                            });
                            $producto['fragancias'] = array_values($producto['fragancias']); // Reindexa fragancias
                        }
                        actualizarSesionCarrito($index);
                        $response = [
                            "success" => true,
                            "message" => "Cantidad actualizada correctamente",
                            "cart" => $_SESSION['cart'],
                            "total" => calcularTotal()
                        ];
                        break 2; // Salir de ambos bucles
                    }
                }
            }
        }
        if (!$response["success"]) {
            $response["message"] = "Fragancia o producto no encontrado";
        }
        break;

    default:
        $response["message"] = "Acción no válida";
        break;
}

// Asegurarse de devolver el carrito en la respuesta siempre
if (!isset($response["cart"])) {
    $response["cart"] = $_SESSION['cart'];
}

// Devolver la respuesta JSON
echo json_encode($response);
