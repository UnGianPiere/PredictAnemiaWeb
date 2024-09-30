<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario y convertirlos a números
    $edad = (int)$_POST['edad'];
    $peso = (float)$_POST['peso'];
    $talla = (float)$_POST['talla'];
    $hemoglobina = (float)$_POST['hemoglobina'];

    // Crear un array con los datos
    $data = array(
        "edad" => $edad,
        "peso" => $peso,
        "talla" => $talla,
        "hemoglobina" => $hemoglobina
    );

    // Generar el JSON correctamente
    $jsonData = json_encode($data);

    // Escapar las comillas para el comando
    $command = 'python "C:\xampp\htdocs\ANEMIA_WEB\ML\predict_anemia.py" "' . addslashes($jsonData) . '"';

    // Ejecutar el comando
    $output = shell_exec($command);

    // Mostrar la salida
    echo "<h2>Comando ejecutado:</h2>";
    echo "<p>" . htmlspecialchars($command) . "</p>";
    echo "<h2>Salida de shell:</h2>";
    echo "<p>" . htmlspecialchars($output) . "</p>";
} else {
    echo "<p>No se recibió ningún dato.</p>";
}
?>
