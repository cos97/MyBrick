<?php
include 'connect.php';

session_start();

// Verifica si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y limpiar los datos del formulario (Quita espacios en blanco)
    $username = trim($_POST["username"]); 
    $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING); // Sanitizar la contraseña
    $passwordRepeat = filter_var($_POST["passwordRepeat"], FILTER_SANITIZE_STRING);

    // Log de los datos recibidos
    error_log("Datos recibidos del formulario: username = $username, password = $password, passwordRepeat = $passwordRepeat");

    // Llama a la función validar
    validar($username, $password, $passwordRepeat);
} else {
    // Redirigir si los datos del formulario no se han enviado correctamente
    header("Location: ../index.php?error=invalid");
    exit;
}

function validar($username, $password, $passwordRepeat) {
    if (preg_match('/^[a-zA-Z0-9]+$/', $username) && !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $username) && preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*()\-_=+{};:,<.>]{6,15}$/', $password) && $password === $passwordRepeat && $username !== $password) {
        $conexion = conectar();

        // Comprobar si el nombre de usuario ya existe (usando sentencias preparadas)
        $query = "SELECT * FROM usuarios WHERE username = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt->close(); // Cerrar la sentencia antes de redirigir
            header("Location: ../index.php?error=user_exists");
            exit;
        }

        // Encriptar la contraseña antes de almacenarla en la base de datos
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Si no existe y es correcto, se inserta en la BD
        $query = "INSERT INTO usuarios (username, password) VALUES (?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION["username"] = $username;
            $_SESSION["timeout"] = time();
            $conexion->close(); // Cerrar la conexión antes de redirigir
            header("Location: paginaPerfil.php");
            exit;
        } else {
            echo "Error al registrar el usuario.";
        }
    } else {
        header("Location: ../index.php?error=invalid");
        exit;
    }
}
?>