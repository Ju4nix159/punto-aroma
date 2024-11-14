<?php
session_start();

// Lee el contenido del cuerpo de la solicitud
$inputJSON = file_get_contents("php://input");
$input = json_decode($inputJSON, true);

// Verifica que se recibió el producto correctamente
if (isset($input['producto'])) {
    $producto = $input['producto'];

    // Inicializa el carrito en la sesión si no existe
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Agrega el producto al carrito
    $_SESSION['cart'][] = $producto;

    // Responde con un mensaje de éxito
    echo json_encode(["success" => true, "message" => "Producto agregado al carrito"]);
} else {
    // Responde con un mensaje de error si no se recibe el producto
    echo json_encode(["success" => false, "message" => "Error: no se recibió el producto"]);
}
?>

