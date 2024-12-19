<?php
include './config/sbd.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idCategoria']) && isset($_POST['nombreCategoria'])) {
        $id_categoria = $_POST['idCategoria'];
        $nombre = $_POST['nombreCategoria'];
        $descripcion = $_POST['descripcionCategoria'];

        try {
            $sql = "UPDATE categorias SET nombre = :nombre, descripcion = :descripcion WHERE id_categoria = :id_categoria";
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al actualizar la categoría.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Excepción capturada: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
}
?>
