<?php
require_once 'conexion.php'; // Conexión a la base de datos

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM pacientes WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $paciente = $result->fetch_assoc();
        echo json_encode($paciente);
    } else {
        echo json_encode([]);
    }

    $stmt->close();
}
$conn->close();
?>
