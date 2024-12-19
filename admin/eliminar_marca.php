<?php
include './config/sbd.php'; // Asegúrate de que esta línea cargue correctamente la conexión

header('Content-Type: application/json');

try {
    // Verificar el método de la solicitud
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['id_marca'])) {
            $id_marca = $_POST['id_marca'];

            // Preparar la consulta SQL para "eliminar" la marca (cambiar estado a 0)
            $sql = "UPDATE marcas SET estado = 0 WHERE id_marca = :id_marca";

            // Verifica que la conexión `$con` sea un PDO
            if ($con instanceof PDO) {
                $stmt = $con->prepare($sql);
                $stmt->bindValue(':id_marca', $id_marca, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta.']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Error en la conexión con la base de datos.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'ID de marca no especificado.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Excepción capturada: ' . $e->getMessage()]);
}
?>
