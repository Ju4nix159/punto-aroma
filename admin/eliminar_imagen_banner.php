<?php

include './config/sbd.php';

try {
    // Obtén el ID de la imagen desde la solicitud AJAX
    $image_id = $_POST['id'];

    // Consulta para obtener la ruta completa del archivo de la imagen desde la columna `ruta`
    $sql_ruta = $con->prepare("SELECT ruta FROM banner WHERE id_banner = :id_banner");
    $sql_ruta->bindParam(":id_banner", $image_id, PDO::PARAM_INT);
    $sql_ruta->execute();
    $ruta = $sql_ruta->fetch(PDO::FETCH_ASSOC);

    if ($ruta) {  // Verifica si se encontró una fila
        $image_path ="../".$ruta['ruta'];

        // Verifica si el archivo existe y elimina el archivo físico de la imagen
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // Elimina la fila correspondiente en la base de datos
        $sql_eliminar = $con->prepare("DELETE FROM banner WHERE id_banner = :id_banner");
        $sql_eliminar->bindParam(":id_banner", $image_id, PDO::PARAM_INT);
        $sql_eliminar->execute();

        echo json_encode(["success" => true, "message" => "Imagen eliminada correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Imagen no encontrada en la base de datos"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error en el servidor: " . $e->getMessage()]);
}
