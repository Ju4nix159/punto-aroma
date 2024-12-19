<?php
include './config/sbd.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idMarca']) && isset($_POST['nombreMarca'])) {
        $id_marca = $_POST['idMarca'];
        $nombre = $_POST['nombreMarca'];

        try {
            $sql = "UPDATE marcas SET nombre = :nombre WHERE id_marca = :id_marca";
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':id_marca', $id_marca, PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al actualizar la marca.']);
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
