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
// Procesar la actualización de los datos de precio y cantidad
if(isset($_POST['submit'])) {
    if(isset($_POST['precio']) && isset($_POST['cantidad']) && isset($_POST['fig_num'])) {
        $precios = $_POST['precio'];
        $cantidades = $_POST['cantidad'];
        $fig_nums = $_POST['fig_num'];
        foreach($fig_nums as $key => $fig_num) {
            $precio = $conexion->real_escape_string($precios[$key]);
            $cantidad = $conexion->real_escape_string($cantidades[$key]);
            // Verificar si ya existe una fila para este fig_num y usuario
            $sql = "SELECT * FROM usuariominifigs WHERE username = '$username' AND fig_num = '$fig_num'";
            $result = $conexion->query($sql);
            if ($result->num_rows > 0) {
                // Si existe, actualizar la fila
                $sql_update = "UPDATE usuariominifigs SET precio = '$precio', cantidad = '$cantidad' WHERE username = '$username' AND fig_num = '$fig_num'";
                $conexion->query($sql_update);
            }
        }
        echo "Datos actualizados correctamente.";
        // Después de actualizar, redirige a la misma página
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}
// Procesar la eliminación de minifiguras
if(isset($_POST['eliminar'])) {
    $fig_num_eliminar = $_POST['eliminar'];
    deleteMinifigs($fig_num_eliminar, $username);
}
// Función para eliminar minifiguras
function deleteMinifigs($fig_num_eliminar, $username) {
    $conexion = conectar();
    $query = "DELETE FROM usuariominifigs WHERE fig_num = '$fig_num_eliminar' AND username = '$username'";
    $result = $conexion->query($query);
    if ($result) {
        echo "Minifigura eliminada correctamente.";
    } else {
        echo "Error al eliminar la minifigura: " . $conexion->error;
    }
    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Minifiguras</title>
</head>
<body>
    <nav>
        <a href="paginaPerfil.php">Home</a>
        <a href="productos.php">Productos</a>
        <a href="minifigs.php">Minifigs</a>
        <a href="mybrick.php">MyBrick</a>
        <a href="mybrickMinifigs.php">MyMinifigs</a>
        <a href="sesionCerrada.php">Cerrar Sesión</a>
    </nav>
    <?php
    // Consulta para seleccionar los campos de la tabla relacion para el usuario actual
    $sql = "SELECT usuariominifigs.*, relacion.img_url, relacion.name, relacion.year, relacion.tema 
            FROM usuariominifigs 
            INNER JOIN relacion ON usuariominifigs.fig_num = relacion.fig_num 
            WHERE usuariominifigs.username = '$username'
            GROUP BY usuariominifigs.fig_num
            ORDER BY usuariominifigs.id DESC";
    $resultado = $conexion->query($sql);
    // Comprobar si hay resultados
    if ($resultado->num_rows > 0) {
        // Mostrar los resultados en una tabla
        echo '<form method="post" action="">';
        echo '<table border="1">';
        echo '<tr><th>Fig Num</th><th>Name</th><th>Year</th><th>Tema</th><th>Imagen</th><th>Precio</th><th>Cantidad</th><th>Eliminar</th></tr>';
        
        $total_precio = 0;
        while ($fila = $resultado->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $fila['fig_num'] . '</td>';
            echo '<td>' . $fila['name'] . '</td>';
            echo '<td>' . $fila['year'] . '</td>';
            echo '<td>' . $fila['tema'] . '</td>';
            echo '<td><a href="' . $fila['img_url'] . '" target="_blank"><img src="' . $fila['img_url'] . '" width="100" height="100"></a></td>';
            echo '<td><input type="text" name="precio[]" value="' . $fila['precio'] . '"></td>';
            echo '<td><input type="text" name="cantidad[]" value="' . $fila['cantidad'] . '"></td>';
            echo '<input type="hidden" name="fig_num[]" value="' . $fila['fig_num'] . '">';
            echo '<td><button type="submit" name="eliminar" value="' . $fila['fig_num'] . '">Eliminar</button></td>';
            echo '</tr>';
            // Calcular el precio total
            $total_precio += $fila['precio'] * $fila['cantidad'];
        }
        // Agregar fila para mostrar el total
        echo '<tr>';
        echo '<td>Total:</td>';
        echo '<td>' . $total_precio . '€</td>';
        echo '</tr>';
        echo '</table>';
        echo '<button type="submit" name="submit">Actualizar</button>';
        echo '</form>';
        // Crear un enlace para descargar como CSV
        echo '<a href="descargar_minifigs_csv.php">Descargar MyMinifigs.csv</a>';
    } else {
        echo "No se encontraron resultados.";
    }
    ?>
</body>
</html>
