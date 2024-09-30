<?php
// Incluir archivo de conexión a la base de datos
include 'conexion.php'; // Asegúrate de que la ruta es correcta

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el ID del paciente a eliminar
    $paciente_id = $_POST['paciente_id'];

    // Verificar que se haya proporcionado un ID válido
    if (!empty($paciente_id)) {
        // Preparar la consulta para eliminar el paciente
        $sql = "DELETE FROM pacientes WHERE id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Vincular parámetros
            $stmt->bind_param("i", $paciente_id);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Paciente eliminado con éxito."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al eliminar el paciente."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta."]);
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "ID de paciente no válido."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}

// Cerrar la conexión
$conn->close();
?>
