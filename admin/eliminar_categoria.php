<?php
// eliminar_categoria.php
header('Content-Type: application/json');

// Verificar el método de la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_categoria'])) {
        $id_categoria = intval($_POST['id_categoria']);
        
        // Conexión a la base de datos (ajusta los valores según tu configuración)
        
        
        // Preparar la consulta SQL para eliminar la categoría
        $sql_eliminar_categoria = $con->prepare('UPDATE TABLE categorias SET estado = 0 WHERE id_categoria = :id_categoria');
        $sql_eliminar_categoria->bind_param(':id_categoria', $id_categoria);
        
        
        if ($sql_eliminar_categoria->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al eliminar la categoría.']);
        }
        
    } else {
        echo json_encode(['success' => false, 'error' => 'ID de categoría no especificado.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
}
?>
