<?php
session_start(); // Iniciar sesión
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
    $user_id = $_SESSION['user_id']; // Obtener el ID del usuario de la sesión

    // Consulta para obtener los pacientes y sus resultados de anemia
    $sql = "SELECT p.nombre, p.sexo, pred.resultado_anemia 
            FROM patients p
            LEFT JOIN medical_data md ON p.id = md.patient_id
            LEFT JOIN predictions pred ON md.id = pred.medical_data_id
            WHERE p.user_id = '$user_id'"; // Filtrar por el ID del usuario

    $result = $conn->query($sql);

    $pacientes = array();

    if ($result->num_rows > 0) {
        // Recuperar los datos de los pacientes
        while ($row = $result->fetch_assoc()) {
            $pacientes[] = $row; // Agregar cada paciente al array
        }
    }

    // Devolver los datos en formato JSON
    echo json_encode($pacientes);
} else {
    echo json_encode(array("error" => "Usuario no autenticado.")); // Manejo de error si no hay sesión activa
}

$conn->close();
?>
