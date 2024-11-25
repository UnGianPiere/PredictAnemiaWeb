<?php
// Obtener el ID del paciente desde la URL
$paciente_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$paciente_id) {
    echo json_encode(null);
    exit();
}

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbanemia";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del paciente y su dieta
$sql = "
    SELECT 
        p.nombre, 
        TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS edad, 
        pr.resultado_anemia AS diagnostico,
        d.dia, d.desayuno, d.almuerzo, d.cena
    FROM 
        patients p
    LEFT JOIN 
        medical_data m ON p.id = m.patient_id
    LEFT JOIN 
        predictions pr ON m.id = pr.medical_data_id
    LEFT JOIN 
        diets d ON p.id = d.patient_id
    WHERE 
        p.id = $paciente_id
";

// Ejecutar la consulta
$result = $conn->query($sql);

$datos = array();

if ($result->num_rows > 0) {
    // Inicializar la información del paciente
    $paciente = null;
    $dieta = array();

    while ($row = $result->fetch_assoc()) {
        if (!$paciente) {
            $paciente = array(
                'nombre' => $row['nombre'],
                'edad' => $row['edad'],
                'diagnostico' => $row['diagnostico']
            );
        }

        // Agregar las comidas del paciente
        $dieta[] = array(
            'dia' => $row['dia'],
            'desayuno' => $row['desayuno'],
            'almuerzo' => $row['almuerzo'],
            'cena' => $row['cena']
        );
    }

    $datos = array(
        'nombre' => $paciente['nombre'],
        'edad' => $paciente['edad'],
        'diagnostico' => $paciente['diagnostico'],
        'dieta' => $dieta
    );
}

// Cerrar la conexión
$conn->close();

// Retornar los datos en formato JSON
echo json_encode($datos);
?>
