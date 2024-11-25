<?php
session_start();
$servername = "localhost";
$username = "root"; // Usuario de MySQL
$password = "";     // Contraseña de MySQL
$dbname = "DBAnemia"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Comprobar si la sesión del usuario está activa
if (isset($_SESSION['user_id'])) {
    $pacienteId = $_GET['id']; // Obtener ID del paciente desde la consulta

    // Consulta para obtener la dieta
    $sql = "SELECT * FROM diets WHERE patient_id = '$pacienteId'"; // Cambia 'diets' por el nombre de tu tabla de dietas

    $result = $conn->query($sql);
    $dieta = [];

    if ($result->num_rows > 0) {
        // Recuperar la dieta existente
        while ($row = $result->fetch_assoc()) {
            $dieta[] = $row; // Agregar la dieta al array
        }

        // Devolver los datos de la dieta en formato JSON
        echo json_encode(['dieta' => $dieta, 'tieneDieta' => true]);
    } else {
        // No hay dieta existente para el paciente
        echo json_encode(['dieta' => [], 'tieneDieta' => false]);
    }
} else {
    echo json_encode(array("error" => "Usuario no autenticado.")); // Manejo de error si no hay sesión activa
}

$conn->close();
?>
