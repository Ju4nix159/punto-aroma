<?php
session_start();
// Conexión a la base de datos
include '/admin/config/sbd.php';
$id_domicilio = $_GET['id'];
$id_usuario = $_SESSION['usuario'];
$query = "SELECT 
        d.*,
        ud.tipo_domicilio,
        ud.principal,
        ud.estado
    FROM 
        domicilios d
    JOIN 
        usuario_domicilios ud ON d.id_domicilio = ud.id_domicilio
    WHERE 
        ud.id_domicilio = :id_domicilio AND ud.id_usuario = :id_usuario;
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id_domicilio', $id_domicilio);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
    echo '<script>console.log(' . json_encode($result->fetch_assoc()) . ');</script>';
} else {
    echo json_encode(['success' => false, 'message' => 'Domicilio no encontrado']);
}
