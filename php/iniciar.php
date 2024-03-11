<?php
include 'connect.php';

$username = $_POST["username"];
$password = $_POST["password"];

function validarInicio($username, $password)
{
    $conexion = conectar();

    // Consulta la base de datos para obtener el hash de la contraseña almacenada
    $queryUser = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conexion->prepare($queryUser);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultUser = $stmt->get_result();

    if ($resultUser->num_rows === 1) {
        $row = $resultUser->fetch_assoc();
        $storedPassword = $row["password"];

        // Utiliza password_verify para verificar la contraseña proporcionada
        if (password_verify($password, $storedPassword)) {

            session_start();
            $_SESSION["username"] = $username; // Almacena el nombre de usuario en una sesión
            header("Location: paginaPerfil.php");
            exit;
        } else {
            header("Location: ../index.php?error=PassNoEncontrado");
            exit;
        }
    } else {
        header("Location: ../index.php?error=noEncontrado");
        exit;
    }


}

validarInicio($username, $password);
?>
