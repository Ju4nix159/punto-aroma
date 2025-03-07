<?php

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

require "vendor/autoload.php";

MercadoPagoConfig::setAccessToken("APP_USR-2137766256478019-030608-453575c61c4dc8e621871049f174e621-671010115");

$client = new PreferenceClient();
$backUrls = [
    "success" => "http://puntoaroma.com/checkout.php",
    "failure" => "http://puntoaroma.com/checkout.php",
    "pending" => "http://puntoaroma.com/checkout.php"
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

// Generar un ID único para la orden
$orderId = uniqid("orden_", true);

// Información del comprador (debe estar en la sesión o venir del formulario)
$nombreComprador = $_SESSION['usuario']['nombre'] ?? "Nombre";
$apellidoComprador = $_SESSION['usuario']['apellido'] ?? "Apellido";
$emailComprador = $_SESSION['usuario']['email'] ?? "email@ejemplo.com";

// Crear la preferencia con Webhook, external_reference, statement_descriptor, payer y category_id
$preference = $client->create([
    "items" => [
        [
            "id" => "reserva-30",
            "title" => "Reserva del 30% del pedido",
            "description" => "Pago del 30% como reserva del pedido total.",
            "quantity" => 1,
            "unit_price" => $montoReserva,
            "currency_id" => "ARS",
            "category_id" => "others"
        ]
    ],
    "external_reference" => $orderId,
    "back_urls" => $backUrls,
    "auto_return" => "approved",
    "notification_url" => "http://aromaybienestar.com/webhook.php",
    "statement_descriptor" => "PagoReserva",
    "payer" => [
        "name" => $nombreComprador,
        "surname" => $apellidoComprador,
        "email" => $emailComprador
    ],
    // Esta configuración es crítica para habilitar pagos con dinero en cuenta
    "payment_methods" => [
        "excluded_payment_types" => [],  // Deja esto vacío para permitir todos los tipos de pago
        "excluded_payment_methods" => [] // Deja esto vacío para permitir todos los métodos
    ],
    // Permite que los pagos queden en estado pendiente en vez de rechazarlos directamente
    "binary_mode" => false
]);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>

<script>
    const mp = new MercadoPago('APP_USR-479e75a3-dd9c-46a5-9e9f-c3757a1457a0', {
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