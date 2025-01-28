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

$productos = $_SESSION['cart'];

$totalPedido = 0;

// Calcular el total del pedido
foreach ($productos as $item) {
    foreach ($item['fragancias'] as $fragancia) {
        $totalPedido += $item['precio'] * $fragancia['cantidad'];
    }
}

// Calcular el 30% del total
$montoReserva = $totalPedido * 0.3;

// Crear la preferencia con un único ítem que representa el 30% del total
$preference = $client->create([
    "items" => [
        [
            "id" => "reserva-30", // ID único para la reserva
            "title" => "Reserva del 30% del pedido",
            "description" => "Pago del 30% como reserva del pedido total.",
            "quantity" => 1,
            "unit_price" => $montoReserva
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