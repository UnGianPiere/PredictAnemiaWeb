<?php
session_start(); // Iniciar sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]); // Si no está autenticado, devolver un array vacío
    exit();
}

// Conexión a la base de datos
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

// Obtener el ID del usuario autenticado
$user_id = $_SESSION['user_id'];

// Consultar los pacientes del usuario autenticado
$sql = "SELECT * FROM patients WHERE user_id = '$user_id'";
$result = $conn->query($sql);

$pacientes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pacientes[] = $row; // Agregar cada paciente al array
    }
}

echo json_encode($pacientes); // Devolver los pacientes como JSON
$conn->close();
?>
