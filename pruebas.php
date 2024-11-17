<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="address-card address-main">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="address-type">Casa</span> <!-- Aquí se usa el valor de domicilio.tipo -->
            <span class="badge bg-primary">Principal</span> <!-- Solo se muestra si domicilio.principal es true -->
        </div>
        <p>Calle Falsa 123</p> <!-- Aquí se usa domicilio.calle -->
        <p>Ciudad Ficticia, 12345</p> <!-- Aquí se usan domicilio.ciudad y domicilio.codigoPostal -->
        <div class="mt-3">
            <button class="btn btn-sm btn-outline-primary me-2 btn-editar-domicilio" data-id="1">Editar</button>
            <!-- No se genera este botón si domicilio.principal es true -->
            <button class="btn btn-sm btn-outline-success me-2 btn-principal-domicilio" data-id="1">Hacer Principal</button> <!-- Solo se muestra si domicilio.principal es false -->
            <button class="btn btn-sm btn-outline-danger btn-eliminar-domicilio" data-id="1">Eliminar</button> <!-- Solo se muestra si domicilio.principal es false -->
        </div>
    </div>

    <div class="address-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="address-type">Apartamento</span> <!-- Aquí se usa el valor de domicilio.tipo -->
        </div>
        <p>Avenida Siempre Viva 742</p> <!-- Aquí se usa domicilio.calle -->
        <p>Springfield, 98765</p> <!-- Aquí se usan domicilio.ciudad y domicilio.codigoPostal -->
        <div class="mt-3">
            <button class="btn btn-sm btn-outline-primary me-2 btn-editar-domicilio" data-id="2">Editar</button>
            <!-- No se genera este botón si domicilio.principal es true -->
            <button class="btn btn-sm btn-outline-success me-2 btn-principal-domicilio" data-id="2">Hacer Principal</button> <!-- Solo se muestra si domicilio.principal es false -->
            <button class="btn btn-sm btn-outline-danger btn-eliminar-domicilio" data-id="2">Eliminar</button> <!-- Solo se muestra si domicilio.principal es false -->
        </div>
    </div>
</body>

</html>





<!-- Más tarjetas de dirección generadas dinámicamente -->