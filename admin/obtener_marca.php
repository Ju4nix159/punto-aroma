<?php
include './config/sbd.php'; // Incluye la configuración de la base de datos

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id_marca'])) {
        $id_marca = $_GET['id_marca'];

        try {
            // Preparar y ejecutar la consulta para obtener la información de la marca
            $sql = "SELECT id_marca, nombre FROM marcas WHERE id_marca = :id_marca AND estado = 1";
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':id_marca', $id_marca, PDO::PARAM_INT);
            $stmt->execute();

            $marca = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($marca) {
                echo json_encode(['success' => true, 'marca' => $marca]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Marca no encontrada.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error al obtener los datos: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'ID de marca no especificado.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
}
?>
