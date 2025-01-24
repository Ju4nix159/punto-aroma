<?php
include 'procesar_pedido.php'; // Archivo donde están las funciones

var_dump($_POST["nombre"]);
var_dump($_POST["apellido"]);
var_dump($_POST["dni"]);
var_dump($_POST["phone"]);
var_dump($_POST["email"]);
var_dump($_POST["delivery_method"]);
var_dump($_POST["id_local"]);
var_dump($_POST["payment_method"]);


// Activar reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Datos simulados
$id_usuario = 1;
$carrito = [
    [
        'id_producto' => 101,
        'sku' => 'SKU015',
        'precio' => 100.50,
        'fragancias' => [
            ['cantidad' => 2],
            ['cantidad' => 1]
        ]
    ],
    [
        'id_producto' => 102,
        'sku' => 'SKU016',
        'precio' => 200.75,
        'fragancias' => [
            ['cantidad' => 3]
        ]
    ]
];
$_POST = [
    'delivery_method' => 'delivery',
    'provincia' => 'Buenos Aires',
    'localidad' => 'La Plata',
    'calle' => 'Calle Falsa',
    'numero' => '123',
    'codigo_postal' => '1900',
    'payment_method' => 'pagoenlocal'
];

try {
    // Probar calcularTotalCarrito
    echo "Probando calcularTotalCarrito...\n";
    $total = calcularTotalCarrito($carrito);
    echo "Total del carrito: $total\n";

    // Probar procesarDomicilio
    echo "Probando procesarDomicilio...\n";
    $id_domicilio = procesarDomicilio($con, $_POST, $id_usuario);
    echo "ID Domicilio: $id_domicilio\n";

    // Probar crearPedido
    echo "Probando crearPedido...\n";
    $id_pedido = crearPedido($con, $id_usuario, $total, $id_domicilio, $_POST, 'delivery');
    echo "ID Pedido: $id_pedido\n";

    // Probar insertarProductosPedido
    echo "Probando insertarProductosPedido...\n";
    insertarProductosPedido($con, $id_pedido, $carrito);
    echo "Productos insertados correctamente.\n";

    // Probar procesarPago
    echo "Probando procesarPago...\n";
    procesarPago($con, $id_pedido, $_POST, $total);
    echo "Pago procesado correctamente.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
