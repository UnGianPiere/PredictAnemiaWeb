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
            // Obtener el ID de los datos médicos insertados
            $medical_data_id = $conn->insert_id;

            // Crear el array con los datos para la predicción
            $data = array(
                "edad" => $edad,
                "hemoglobina" => $hemoglobina,
                "peso" => $peso,
                "talla" => $talla
            );

            // Generar el JSON correctamente
            $jsonData = json_encode($data);

            // Comando para ejecutar el script de Python
            $command = 'python "C:\xampp\htdocs\ANEMIA_WEB\ML\predict_anemia.py" "' . addslashes($jsonData) . '"';
            $output = shell_exec($command);
            $return_var = 0; // Variable para el código de retorno

            // Verificar si el script Python se ejecutó correctamente
            if ($return_var === 0) {
                // Decodificar el JSON de la predicción
                $prediccion = json_decode($output, true);

                // Verificar si se recibió un array y si contiene el campo esperado
                if (is_array($prediccion) && isset($prediccion['resultado_anemia'])) {
                    $resultado_anemia = $prediccion['resultado_anemia'];

                    // Guardar el resultado en la tabla predictions con medical_data_id
                    $sql_prediction = "INSERT INTO predictions (medical_data_id, resultado_anemia, fecha_prediccion) 
                                    VALUES ('$medical_data_id', '$resultado_anemia', CURDATE())";

                    if ($conn->query($sql_prediction) === TRUE) {
                        // Redirigir a perfil.html con el mensaje de éxito
                        header("Location: ../views/perfil.html?mensaje=Paciente registrado con éxito&resultado_anemia=$resultado_anemia");
                        exit();
                    } else {
                        // Manejar el error al insertar la predicción
                        echo "<p>Error al guardar predicción.</p>";
                        exit();
                    }
                } else {
                    // Manejar el error de formato en la predicción
                    echo "<p>Error: No se recibió una predicción válida. Datos obtenidos: $output</p>";
                    exit();
                }
            } else {
                // Manejar el error al ejecutar el script Python
                echo "<h2>Error al ejecutar el modelo de predicción. Código de retorno: $return_var</h2>";
                echo "<pre>" . htmlspecialchars($output) . "</pre>";
            }
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
