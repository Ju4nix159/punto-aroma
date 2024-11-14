<?php
session_start();

/* // Limpia la variable de sesión 'cart'
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}
 */
// Verifica si el carrito existe en la sesión
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Muestra el contenido del carrito en formato JSON para fácil visualización
    echo json_encode(["success" => true, "cart" => $_SESSION['cart']]);
} else {
    // Carrito vacío
    echo json_encode(["success" => false, "message" => "El carrito está vacío"]);
}
?>
