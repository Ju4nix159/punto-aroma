<?php
include './admin/config/sbd.php';

header('Content-Type: application/json');
// Leer datos del cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);

// Validar que los datos necesarios estén presentes
if (
    !isset(
        $input['id_usuario'], 
        $input['codigo_postal'], 
        $input['provincia'], 
        $input['localidad'], 
        $input['barrio'], 
        $input['calle'], 
        $input['numero'], 
        $input['tipo_domicilio']
    )
) {
    echo "<script>alert('Datos incompletos: " . json_encode($input) . "');</script>";
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

// Asignar valores desde el input
$id_usuario = $input['id_usuario'];
$codigo_postal = $input['codigo_postal'];
$provincia = $input['provincia'];
$localidad = $input['localidad'];
$barrio = $input['barrio'];
$calle = $input['calle'];
$numero = $input['numero'];
$tipo_domicilio = $input['tipo_domicilio'];

try {
    // Iniciar una transacción
    $con->beginTransaction();

    // Paso 1: Insertar datos en la tabla 'domicilios'
    $sql_domicilio = $con->prepare(
        "INSERT INTO domicilios (codigo_postal, provincia, localidad, barrio, calle, numero) 
        VALUES (:codigo_postal, :provincia, :localidad, :barrio, :calle, :numero)"
    );
    $sql_domicilio->bindParam(':codigo_postal', $codigo_postal);
    $sql_domicilio->bindParam(':provincia', $provincia);
    $sql_domicilio->bindParam(':localidad', $localidad);
    $sql_domicilio->bindParam(':barrio', $barrio);
    $sql_domicilio->bindParam(':calle', $calle);
    $sql_domicilio->bindParam(':numero', $numero);
    $sql_domicilio->execute();

    // Recuperar el ID del domicilio recién insertado
    $id_domicilio = $con->lastInsertId();

    // Paso 2: Insertar datos en la tabla 'usuario_domicilio'
    $sql_usuario_domicilio = $con->prepare(
        "INSERT INTO usuario_domicilios (id_domicilio, id_usuario, tipo_domicilio) 
        VALUES (:id_domicilio, :id_usuario, :tipo_domicilio)"
    );
    $sql_usuario_domicilio->bindParam(':id_domicilio', $id_domicilio);
    $sql_usuario_domicilio->bindParam(':id_usuario', $id_usuario);
    $sql_usuario_domicilio->bindParam(':tipo_domicilio', $tipo_domicilio);
    $sql_usuario_domicilio->execute();

    // Confirmar la transacción
    $con->commit();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    $con->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al insertar en la base de datos: ' . $e->getMessage()]);
}
?>
