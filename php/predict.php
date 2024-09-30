<?php
// Habilitar la visualización de errores en PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Recibir los datos del formulario
$edad = $_POST['edad'];
$peso = $_POST['peso'];
$talla = $_POST['talla'];
$hemoglobina = $_POST['hemoglobina'];

// Crear un array con los datos
$data = array(
    "edad" => (int)$edad,  // Convertir a entero
    "peso" => (int)$peso,
    "talla" => (int)$talla,
    "hemoglobina" => (int)$hemoglobina
);

// Convertir los datos a JSON
$data_json = json_encode($data);

// Mostrar los datos para verificar que se estén recibiendo correctamente
echo "<pre>";
print_r($data);
echo "</pre>";

// Mostrar el JSON para verificar el formato
echo "<pre>JSON generado: $data_json</pre>";

// Ejecutar el script Python y pasarle los datos correctamente
$command = 'python ' . escapeshellarg(realpath('../ML/predict_anemia.py')) . ' ' . escapeshellarg($data_json);

// Mostrar el comando para verificar
echo "<pre>Comando ejecutado: $command</pre>";

// Ejecutar el comando y capturar errores
$output = shell_exec($command . ' 2>&1');

// Mostrar el resultado de la ejecución, incluyendo errores si los hubiera
echo "<pre>Salida de shell: $output</pre>";

// Mostrar el resultado de la predicción
echo "El nivel de anemia es: " . $output;
?>
