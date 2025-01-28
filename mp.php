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

$preference = $client->create([
    "items" => [
        [
            "id" => "1234",
            "title" => "Dummy Item",
            "description" => "Multicolor Item",
            "quantity" => 1,
            "unit_price" => 10.0
        ],
        [
            "id" => "12345",
            "title" => "Dummy Item2",
            "quantity" => 1,
            "unit_price" => 10.0
        ],
        [
            "id" => "123456",
            "title" => "Dummy Item3",
            "quantity" => 2,
            "unit_price" => 10.0
        ]
    ],

    "back_urls" => $backUrls,
    "statement_descriptor" => "mi tienda",
    "external_reference" => "cdp123",
    /* "shipments" => [
        "cost" => 100,
        "mode" => "not_specified",
        "local_pickup" => true,
        "dimensions" => "30x30x30,500",
        "default_shipping_method" => 73904
    ],  este campo entraria cuando se hace el envio*/ 

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
                    action : 'Pagar',
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