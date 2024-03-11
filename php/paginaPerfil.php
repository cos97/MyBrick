<?php
include 'connect.php';
session_start(); // Llama a session_start() solo una vez al principio del script
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    header("Location: ../index.php?error=noAutenticado");
    exit;
}
$conexion = conectar();
verificarInactividadSesion(600);
function verificarInactividadSesion($tiempoInactivo)
{
    if (isset($_SESSION["timeout"])) {
        $sessionTTL = time() - $_SESSION["timeout"];
        if ($sessionTTL > $tiempoInactivo) {
            session_destroy();
            header("Location: ../index.php?error=sesionCerrada");
            exit;
        }
    }
    $_SESSION["timeout"] = time();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagina Perfil</title>
    
</head>
<body>
    <nav>
        <a href="paginaPerfil.php" ><i ></i> Home</a>
        <a href="productos.php"><i ></i> Productos</a>
        <a href="minifigs.php"><i ></i> Minifigs</a>
        <a href="mybrick.php"><i ></i> MyBrick</a>
        <a href="mybrickMinifigs.php"><i ></i> MyMinifigs</a>
        <a href="sesionCerrada.php"><i ></i> Cerrar Sesión</a>
       
        <?php
        // Verifica si el rol es "admin" en la sesión
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
            echo '<a href="tareasAdmin.php"><i ></i> Funciones Admin</a>';
        }
        ?>
    </nav>
    <br>
    <div >
        <h1>Bienvenido <?php echo $username; ?></h1>
    </div>
</body>
</html>