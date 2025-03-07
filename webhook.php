<?php

$input = file_get_contents("php://input");
$event = json_decode($input, true);

if ($event && isset($event['type']) && $event['type'] == 'payment') {
    $paymentId = $event['data']['id'];

    // Obtener información del pago desde Mercado Pago
    $accessToken = "APP_USR-2137766256478019-030608-453575c61c4dc8e621871049f174e621-671010115";
    $url = "https://api.mercadopago.com/v1/payments/$paymentId?access_token=$accessToken";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $paymentData = json_decode($response, true);

    // Verificar si el pago fue aprobado
    if ($paymentData['status'] == "approved") {
        $externalReference = $paymentData['external_reference']; // ID de la orden en tu sistema
        $montoPagado = $paymentData['transaction_amount'];

        // Aquí puedes actualizar la base de datos con el estado del pago
        file_put_contents("pagos.log", "Pago aprobado: Orden $externalReference - Monto: $montoPagado\n", FILE_APPEND);
    }
}

http_response_code(200); // Confirmar recepción del Webhook a Mercado Pago
?>
