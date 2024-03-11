<?php
include 'connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    header("Location: ../index.php?error=noAutenticado");
    exit;
}

$conexion = conectar();

// Consulta para seleccionar los campos de la tabla sets para el usuario actual
$sql = "SELECT usuario_coleccion.*, sets.img_url, sets.name, sets.year, sets.tema, sets.num_parts FROM usuario_coleccion 
        INNER JOIN sets ON usuario_coleccion.set_num = sets.set_num 
        WHERE usuario_coleccion.username = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();

// Calcular los totales
$total_precio = 0;
$total_piezas = 0;
while ($fila = $resultado->fetch_assoc()) {
    $total_precio += $fila['precio'] * $fila['cantidad'];
    $total_piezas += $fila['num_parts'] * $fila['cantidad'];
}

// Volver a ejecutar la consulta para obtener los resultados
$stmt->execute();
$resultado = $stmt->get_result();

// Nombre del archivo CSV a descargar
$filename = "MyBrick.csv";

// Cabeceras para descargar el archivo CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Crear un archivo CSV y escribir los datos
$output = fopen('php://output', 'w');
fputcsv($output, array('Set Num', 'Name', 'Year', 'Tema', 'Num Parts', 'Imagen', 'Precio', 'Cantidad'));

while ($fila = $resultado->fetch_assoc()) {
    fputcsv($output, array($fila['set_num'], $fila['name'], $fila['year'], $fila['tema'], $fila['num_parts'], $fila['img_url'], $fila['precio'], $fila['cantidad']));
}

// Añadir fila vacía
fputcsv($output, array('', '', '', '', '', '', '', ''));

// Escribir fila con totales
fputcsv($output, array('Precio Total:', $total_precio . '€'));
fputcsv($output, array('Piezas Totales:', $total_piezas . ' Pzs'));

fclose($output);
exit;
?>