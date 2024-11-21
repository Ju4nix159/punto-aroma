<?php

include './config/sbd.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar el archivo
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo.');
        }

        // Configuración de subida
        $uploadsbd = 'assets/banners/';
        $uploadDir = '../assets/banners/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['image']['name']);
        $filePath = $uploadDir  . '_' . $fileName;
        $filePathsbd = $uploadsbd . '_' . $fileName;

        // Mover el archivo subido
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            throw new Exception('No se pudo guardar el archivo.');
        }

        // Obtener título y descripción
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';

        // Insertar en la base de datos
        $sql_subir_banner = $con->prepare("INSERT INTO banner (nombre, ruta,descripcion) VALUES (:titulo, :image_path,:descripcion)");
        $sql_subir_banner->bindParam(':titulo', $title);
        $sql_subir_banner->bindParam(':image_path', $filePathsbd);
        $sql_subir_banner->bindParam(':descripcion', $description);
        $sql_subir_banner->execute();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
