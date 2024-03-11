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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['set_num']) && isset($_POST['user_nick'])) {
        $set_num = $_POST['set_num'];
        $user_nick = $_POST['user_nick'];

        // Sanitizar los datos antes de usarlos en la consulta SQL
        $set_num = filter_var($set_num, FILTER_SANITIZE_STRING);
        $user_nick = filter_var($user_nick, FILTER_SANITIZE_STRING);

        // Preparar la consulta SQL con sentencias preparadas
        $stmt = $conexion->prepare("INSERT INTO usuario_coleccion (username, set_num) VALUES (?, ?)");
        $stmt->bind_param("ss", $user_nick, $set_num);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Producto añadido a la colección correctamente.";
        } else {
            echo "Error al añadir el producto a la colección: " . $stmt->error;
        }

        // Cerrar la sentencia
        $stmt->close();
    }
}

// Redirigir al final del script
header("Location: productos.php");
exit;
?>