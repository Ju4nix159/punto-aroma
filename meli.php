<?php

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

require 'vendor/autoload.php'; // Composer autoload

MercadoPagoConfig::setAccessToken("APP_USR-1176752133760157-112121-553bbd48403e93c37212c4d1cc804fda-2110071685");
$client = new PreferenceClient();

$preference = $client->create([
    "items" => [
        [
            "id" => "1234",
            "title" => "Test",
            "quantity" => 2,
            "currency_id" => "ARS",
            "unit_price" => 10.0
        ],
        [
            "id" => "1235",
            "title" => "Test2",
            "quantity" => 1,
            "currency_id" => "ARS",
            "unit_price" => 20.0
        ],
        [
            "id" => "123123",
            "title" => "tes5",
            "quantity" => 1,
            "currency_id" => "ARS",
            "unit_price" => 20.0

        ],

    ],
    "statement_descriptor" => "punto aroma",
    "external_reference" => "1234",
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <title>Document</title>
</head>

<body>
    <div id="wallet_container"></div>

    <script>
        const mp = new MercadoPago('APP_USR-e20668d5-1dc8-4f75-abf8-c9f750898255', {
            locale: 'es-AR'
        });
        mp.bricks().create("wallet", "wallet_container", {
            initialization: {
                preferenceId: "<?php echo $preference->id; ?>",
                redirectMode: "modal",
            },
            customization: {
                paymentMethods: {
                    facility: "CREDIT_CARD",
                },
                render: {
                    container: ".wallet-container",
                    label: "Pagar",
                },
                callbacks: {
                    onReady: () => {
                        // callback llamado cuando el brick está listo
                    },
                    onSubmit: (paymentData) => {
                        // callback llamado al enviar el formulario
                        return new Promise((resolve) => {
                            // Procesar el pago con la información proporcionada por paymentData
                            // ...
                            resolve();
                        });
                    },
                },
                style: {
                    theme: "default", // "default" | "bootstrap" | "flat"
                },
                texts: {
                    action: "buy",
                    valueProp: 'security_details',
                    visual: {
                        style: 'responsive'
                    }
                }
            }
        }).render();
    </script>
</body>

</html>