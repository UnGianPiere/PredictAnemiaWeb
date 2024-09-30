<?php
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

// Obtener datos del formulario
$email = $_POST['email'];
$password = $_POST['password'];

// Consulta a la base de datos para verificar el usuario
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // El usuario existe, ahora verificamos la contraseña
    $user = $result->fetch_assoc();
    
    // Comprobamos la contraseña (asumiendo que está encriptada)
    if (password_verify($password, $user['password'])) {
        // Inicio de sesión exitoso, establece la sesión
        session_start(); // Iniciar sesión
        $_SESSION['user_id'] = $user['id']; // Guardar el ID del usuario en la sesión

        // Redirigimos a la página de contenido
        header("Location: ../views/inicio.html");
        exit(); // Asegúrate de salir después de redirigir
    } else {
        // Contraseña incorrecta, redirigir con mensaje de error
        header("Location: ../views/login.html?error=Contraseña incorrecta.");
        exit();
    }
} else {
    // Usuario no encontrado, redirigir con mensaje de error
    header("Location: ../views/login.html?error=Usuario no encontrado.");
    exit();
}

// Cerrar conexión
$conn->close();
?>
