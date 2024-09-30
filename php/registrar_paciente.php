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

    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $sexo = $_POST['sexo'];
    $peso = $_POST['peso'];
    $talla = $_POST['talla'];
    $hemoglobina = $_POST['hemoglobina'];

    // Calcular edad a partir de la fecha de nacimiento
    $fecha_actual = new DateTime();
    $fecha_nac = new DateTime($fecha_nacimiento);
    $edad = $fecha_actual->diff($fecha_nac)->y;

    // Inserción en la tabla patients
    $sql = "INSERT INTO patients (user_id, nombre, fecha_nacimiento, sexo, created_at, updated_at) 
            VALUES ('$user_id', '$nombre', '$fecha_nacimiento', '$sexo', NOW(), NOW())";

    if ($conn->query($sql) === TRUE) {
        $paciente_id = $conn->insert_id; // Obtener el ID del paciente creado

        // Inserción en la tabla medical_data
        $sql_medical = "INSERT INTO medical_data (patient_id, peso, talla, hemoglobina, edad, fecha_registro) 
                        VALUES ('$paciente_id', '$peso', '$talla', '$hemoglobina', '$edad', CURDATE())";

        if ($conn->query($sql_medical) === TRUE) {
            // Redirigir a perfil.html con un mensaje de éxito
            header("Location: ../views/perfil.html?mensaje=Paciente creado con éxito.");
            exit();
        } else {
            // Manejar el error al insertar datos médicos
            header("Location: ../views/perfil.html?error=Error al insertar datos médicos.");
            exit();
        }
    } else {
        // Manejar el error al insertar paciente
        header("Location: ../views/perfil.html?error=Error al insertar paciente.");
        exit();
    }
} else {
    echo "Error: Usuario no autenticado.";
}

$conn->close();
?>
