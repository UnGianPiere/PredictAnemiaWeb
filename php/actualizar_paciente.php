<?php
session_start(); // Iniciar sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo "Error: Usuario no autenticado.";
    exit(); // Termina la ejecución si el usuario no está autenticado
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

// Obtener el ID del paciente y los nuevos datos del formulario
$paciente_id = $_POST['paciente_id'];
$nombre = $_POST['nombre'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$sexo = $_POST['sexo'];
$peso = $_POST['peso'];
$talla = $_POST['talla'];
$hemoglobina = $_POST['hemoglobina'];

// Actualizar la información del paciente en la tabla patients
$sql = "UPDATE patients SET nombre = '$nombre', fecha_nacimiento = '$fecha_nacimiento', sexo = '$sexo', updated_at = NOW() WHERE id = '$paciente_id'";

if ($conn->query($sql) === TRUE) {
    // Actualizar los datos médicos en la tabla medical_data
    $sql_medical = "UPDATE medical_data SET peso = '$peso', talla = '$talla', hemoglobina = '$hemoglobina' WHERE patient_id = '$paciente_id'";

    if ($conn->query($sql_medical) === TRUE) {
        // Redirigir a perfil.html con un mensaje de éxito
        header("Location: ../views/perfil.html?mensaje=Paciente actualizado con éxito.");
        exit();
    } else {
        // Manejar el error al actualizar datos médicos
        header("Location: ../views/perfil.html?error=Error al actualizar datos médicos.");
        exit();
    }
} else {
    // Manejar el error al actualizar paciente
    header("Location: ../views/perfil.html?error=Error al actualizar paciente.");
    exit();
}

$conn->close();
?>
