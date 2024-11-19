<?php
// Conectar a la base de datos
include './admin/config/sbd.php';

$id_pedido = $_GET['id_pedido'];

// Consultar detalles del pedido

$sql_pedido = $con->prepare( "SELECT * FROM pedidos WHERE id_pedido = :id_pedido");
$sql_pedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_pedido->execute();
$pedido = $sql_pedido->fetch(PDO::FETCH_ASSOC);



if ($pedido) {
    echo "<h1>Resumen de la Compra</h1>";
    echo "<p>Pedido: #{$pedido['id_pedido']}</p>";
    echo "<p>Fecha: {$pedido['fecha']}</p>";
    echo "<p>Total: {$pedido['total']}</p>";
    echo "<p>Domicilio: {$pedido['id_domicilio']}</p>";
    
    // Formas de pago
    echo "<h2>Seleccione una Forma de Pago:</h2>";
    echo "<form action='procesar_pago.php' method='post'>";
    echo "<input type='hidden' name='id_pedido' value='{$id_pedido}'>";
    echo "<label><input type='radio' name='forma_pago' value='tarjeta' required> Tarjeta</label><br>";
    echo "<label><input type='radio' name='forma_pago' value='efectivo'> Efectivo</label><br>";
    echo "<button type='submit' class='btn btn-primary'>Confirmar Pago</button>";
    echo "</form>";
} else {
    echo "<p>Error: Pedido no encontrado.</p>";
}
?>
