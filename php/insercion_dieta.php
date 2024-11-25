<?php
// Incluye la conexión a la base de datos
include 'conexion.php';

// Obtiene los datos del JSON enviado
$data = json_decode(file_get_contents('php://input'), true);

// Agrega un error log para depurar los datos recibidos
error_log("Datos recibidos: " . print_r($data, true)); // Esto imprimirá los datos recibidos en el log de errores

// Verifica que se hayan enviado los datos necesarios
if (isset($data['paciente_id']) && isset($data['dieta'])) {
    $pacienteId = $data['paciente_id']; // Asegúrate de que el ID del paciente se envíe correctamente
    $dieta = $data['dieta'];

    // Asegúrate de que la dieta tenga datos
    if (empty($dieta)) {
        echo json_encode(['status' => 'error', 'message' => 'No hay dieta para insertar']);
        exit;
    }

    // Arreglo para almacenar las respuestas
    $resultados = [];

    // Aquí puedes descomponer la dieta y crear una consulta SQL adecuada
    foreach ($dieta as $dia) {
        $desayuno = $conn->real_escape_string($dia['desayuno']);
        $almuerzo = $conn->real_escape_string($dia['almuerzo']);
        $cena = $conn->real_escape_string($dia['cena']);
        
        // Convertir "Día 1", "Día 2", etc. en un número
        preg_match('/Día (\d+)/', $dia['dia'], $matches);
        $numeroDia = isset($matches[1]) ? (int)$matches[1] : 0; // Asigna 0 si no se encuentra un día válido

        // Modificar la consulta para incluir el campo dia
        $sql = "INSERT INTO diets (patient_id, desayuno, almuerzo, cena, dia) VALUES ('$pacienteId', '$desayuno', '$almuerzo', '$cena', '$numeroDia')";

        if ($conn->query($sql) === TRUE) {
            $resultados[] = ['status' => 'success', 'message' => 'Dieta insertada correctamente para ' . $numeroDia];
        } else {
            $resultados[] = ['status' => 'error', 'message' => 'Error al insertar dieta: ' . $conn->error];
        }
    }

    // Retornar todos los resultados de las inserciones
    echo json_encode($resultados);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos insuficientes']);
}
?>
