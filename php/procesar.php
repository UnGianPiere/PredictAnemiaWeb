<?php
// Configuración de cabeceras para asegurar que se retorne JSON
header('Content-Type: application/json');

// Obtener el mensaje desde el formulario
$message = isset($_POST['message']) ? $_POST['message'] : '';

// Asegúrate de que el mensaje no esté vacío
if (!empty($message)) {
    // Ejecutar el script Python y pasar el mensaje como argumento
    $command = escapeshellcmd("python ../ML/procesar.py \"$message\"");
    
    // Ejecutar el comando y obtener la salida
    $output = shell_exec($command);

    // Asegúrate de que la salida del script Python sea un JSON válido
    if ($output !== null) {
        // Intentar decodificar la respuesta JSON
        $response = json_decode($output, true);
        
        // Verificar si la respuesta es válida
        if ($response && isset($response['response'])) {
            echo json_encode($response); // Responder con la respuesta del asistente
        } else {
            // Si no es válido, enviar un error
            echo json_encode(['response' => 'Error en la respuesta de Python 1.']);
        }
    } else {
        echo json_encode(['response' => 'No se recibió respuesta de Python 2.']);
    }
} else {
    echo json_encode(['response' => 'No se recibió ningún mensaje jaja.']);
}
?>
