<?php

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

require "vendor/autoload.php";

MercadoPagoConfig::setAccessToken("APP_USR-8591228501780042-012715-2b2f8f8922d7ae2e755916da6e518e43-2110071685");
$client = new PreferenceClient();
$backUrls = [
    "success" => "http://localhost:8080/feedback",
    "failure" => "http://localhost:8080/feedback",
    "pending" => "http://localhost:8080/feedback"
];

$id_pedido = $_GET['id_pedido'];

$sql_producto_pedido = $con->prepare("SELECT v.aroma, pp.id_pedido, pp.sku, p.nombre AS producto_nombre, pp.cantidad, pp.precio, pp.estado
FROM productos_pedido pp
    JOIN productos p ON pp.id_producto = p.id_producto
    JOIN variantes v ON pp.sku = v.sku
WHERE pp.id_pedido = :id_pedido;");
$sql_producto_pedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_producto_pedido->execute();
$productos = $sql_producto_pedido->fetchAll(PDO::FETCH_ASSOC);

$sql_pago_seña = $con->prepare("SELECT p.id_pago, p.id_pedido, p.id_metodo_pago, mp.nombre_metodo_pago, p.comprobante, p.monto, p.fecha, p.descripcion
FROM pagos p
JOIN metodos_pago mp ON p.id_metodo_pago = mp.id_metodo_pago
WHERE p.id_pedido = :id_pedido AND p.descripcion LIKE '%seña%';");
$sql_pago_seña->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_pago_seña->execute();
$pago_seña = $sql_pago_seña->fetch(PDO::FETCH_ASSOC);
$monto_seña = $pago_seña['monto'];
// Inicializar totalProductos antes del foreach
$totalProductos = 0;

foreach ($productos as $producto) {
    if ($producto['estado'] == 1) {
        $totalProductos += $producto['cantidad'] * $producto['precio']; // Acumulando en vez de sobrescribir
    }
}

$sql_envio = $con->prepare("SELECT envio FROM pedidos where id_pedido = :id_pedido;");
$sql_envio->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$sql_envio->execute();
$envio = $sql_envio->fetch(PDO::FETCH_ASSOC);

// Evitar error si 'envio' no existe en la consulta
$envioCosto = isset($envio['envio']) ? $envio['envio'] : 0;

$total = $totalProductos + $envioCosto - $monto_seña;
// Crear la preferencia con un único ítem que representa el 30% del total
$preference = $client->create([
    "items" => [
        [
            "id" => "pago-total", // ID único para la reserva
            "title" => "Pago total del pedido",
            "description" => "Pago completo del pedido realizado",
            "quantity" => 1,
            "unit_price" => $total,
        ]
    ]
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <script src="https://sdk.mercadopago.com/js/v2"></script>

</head>

<script>
    const mp = new MercadoPago('APP_USR-52d256c0-1c6e-4fcb-90fc-d88d5ee231f6', {
        locale: 'es-AR'
    });
    const bricksBuilder = mp.bricks();

    mp.bricks().create("wallet", "wallet_container", {
        initialization: {
            preferenceId: "<?php echo $preference->id; ?>",
            redirectMode: "modal",

        },
        customization: {
            texts: {
                action: 'Pagar',
                valueProp: 'smart_option',
            },
            visual: {
                buttonBackground: 'lightblue',
                borderRadius: '5px',
            }
        },
    });
</script>
</body>

</html>