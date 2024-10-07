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
    $paciente_id = $_POST['paciente_id']; // Obtener el ID del paciente a actualizar

    // Calcular edad a partir de la fecha de nacimiento
    $fecha_actual = new DateTime();
    $fecha_nac = new DateTime($fecha_nacimiento);
    $edad = $fecha_actual->diff($fecha_nac)->m + ($fecha_actual->diff($fecha_nac)->y * 12); // Guardar edad en meses

    // Actualizar en la tabla patients
    $sql = "UPDATE patients 
            SET nombre = '$nombre', fecha_nacimiento = '$fecha_nacimiento', sexo = '$sexo', updated_at = NOW() 
            WHERE id = '$paciente_id'";

    if ($conn->query($sql) === TRUE) {
        // Actualizar en la tabla medical_data
        $sql_medical = "UPDATE medical_data 
                         SET peso = '$peso', talla = '$talla', hemoglobina = '$hemoglobina', edad = '$edad', fecha_registro = CURDATE() 
                         WHERE patient_id = '$paciente_id'";

        if ($conn->query($sql_medical) === TRUE) {
            // Obtener el ID de los datos médicos del paciente
            $sql_last = "SELECT id FROM medical_data WHERE patient_id = '$paciente_id' ORDER BY id DESC LIMIT 1";
            $result = $conn->query($sql_last);
            $row = $result->fetch_assoc();
            $medical_data_id = $row['id'];

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

                    // Verificar si ya existe un registro de predicción para los datos médicos
                    $sql_check = "SELECT * FROM predictions WHERE medical_data_id = '$medical_data_id'";
                    $result_check = $conn->query($sql_check);

                    if ($result_check->num_rows > 0) {
                        // Actualizar la predicción existente
                        $sql_prediction = "UPDATE predictions SET resultado_anemia = '$resultado_anemia', fecha_prediccion = CURDATE() 
                                           WHERE medical_data_id = '$medical_data_id'";
                    } else {
                        // Guardar el resultado en la tabla predictions
                        $sql_prediction = "INSERT INTO predictions (medical_data_id, resultado_anemia, fecha_prediccion) 
                                           VALUES ('$medical_data_id', '$resultado_anemia', CURDATE())";
                    }

                    if ($conn->query($sql_prediction) === TRUE) {
                        // Redirigir a perfil.html con el mensaje de éxito
                        header("Location: ../views/perfil.html?mensaje=Paciente actualizado con éxito&resultado_anemia=$resultado_anemia");
                        exit();
                    } else {
                        // Manejar el error al insertar o actualizar la predicción
                        echo "<p>Error al guardar o actualizar la predicción.</p>";
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
            // Manejar el error al actualizar datos médicos
            header("Location: ../views/perfil.html?error=Error al actualizar datos médicos.");
            exit();
        }
    } else {
        // Manejar el error al actualizar paciente
        header("Location: ../views/perfil.html?error=Error al actualizar paciente.");
        exit();
    }
} else {
    echo "Error: Usuario no autenticado.";
}

$conn->close();
?>
