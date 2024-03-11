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

// Consulta para seleccionar los campos de la tabla usuariominifigs para el usuario actual
$sql = "SELECT usuariominifigs.*, relacion.img_url, relacion.name, relacion.year, relacion.tema 
        FROM usuariominifigs 
        INNER JOIN relacion ON usuariominifigs.fig_num = relacion.fig_num 
        WHERE usuariominifigs.username = ?
        GROUP BY usuariominifigs.fig_num
        ORDER BY usuariominifigs.id DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();

// Calcular los totales
$total_precio = 0;
while ($fila = $resultado->fetch_assoc()) {
    $total_precio += $fila['precio'] * $fila['cantidad'];
}

// Volver a ejecutar la consulta para obtener los resultados
$stmt->execute();
$resultado = $stmt->get_result();

// Nombre del archivo CSV a descargar
$filename = "MyMinifigs.csv";

// Cabeceras para descargar el archivo CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Crear un archivo CSV y escribir los datos
$output = fopen('php://output', 'w');
fputcsv($output, array('Fig Num', 'Name', 'Year', 'Tema', 'Imagen', 'Precio', 'Cantidad'));

while ($fila = $resultado->fetch_assoc()) {
    fputcsv($output, array($fila['fig_num'], $fila['name'], $fila['year'], $fila['tema'], $fila['img_url'], $fila['precio'], $fila['cantidad']));
}

// Añadir fila vacía
fputcsv($output, array('', '', '', '', '', '', '', ''));

// Escribir fila con totales
fputcsv($output, array('Precio Total:', $total_precio . '€'));

fclose($output);
exit;
?>