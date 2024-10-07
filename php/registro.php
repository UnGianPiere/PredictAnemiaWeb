<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Según tu configuración
$password = ""; // Sin contraseña en tu caso
$dbname = "dbanemia";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si los datos del formulario están presentes
if (isset($_POST['nombre'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar si las contraseñas coinciden
    if ($password !== $confirm_password) {
        header("Location: ../views/registro.html?error=Las contraseñas no coinciden");
        exit();
    }

    // Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: ../views/registro.html?error=El email ya está registrado");
        exit();
    }

    $stmt->close();

    // Hashear la contraseña antes de guardarla
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO users (nombre, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $hashed_password);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Redirigir al formulario de login con un mensaje de éxito
        header("Location: ../views/login.html?message=Registro exitoso, por favor inicie sesión");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        // Redirigir al formulario de registro con un mensaje de error
        header("Location: ../views/registro.html?error=Error al registrar, intente nuevamente");
        exit();
    }
} else {
    header("Location: ../views/registro.html?error=Complete todos los campos");
    exit();
}
?>
