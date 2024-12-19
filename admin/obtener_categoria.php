<?php
include './config/sbd.php'; // Incluye la configuración de la base de datos

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id_categoria'])) {
        $id_categoria = $_GET['id_categoria'];

        try {
            // Preparar y ejecutar la consulta para obtener la información de la categoría
            $sql = "SELECT id_categoria, nombre, descripcion FROM categorias WHERE id_categoria = :id_categoria AND estado = 1";
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $stmt->execute();

            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($categoria) {
                echo json_encode(['success' => true, 'categoria' => $categoria]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Categoría no encontrada.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error al obtener los datos: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'ID de categoría no especificado.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
}
?>
